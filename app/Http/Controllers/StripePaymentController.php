<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripePaymentController extends Controller
{
    public function checkoutPage(Request $request)
    {
        $order = $this->getCheckoutOrderOrFail($request);

        return view('Front.Pay.pay', [
            'stripeKey' => config('services.stripe.key'),
            'order'     => $order,
            'total'     => (float) $order->total,
            'currency'  => strtoupper($this->currency()),
        ]);
    }

    /**
     * AJAX: Create PaymentIntent
     * لا نستقبل amount ولا email من الواجهة
     */
    public function createIntent(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $order = $this->getCheckoutOrderOrFail($request);

        // لا تسمحي بدفع طلب مدفوع
        if (($order->payment_status ?? null) === 'paid') {
            return response()->json(['error' => 'Order already paid.'], 422);
        }

        $total = (float) $order->total;
        if ($total <= 0) {
            return response()->json(['error' => 'Order total is invalid.'], 422);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $receiptEmail = auth()->user()->email ?? null;

        $intent = PaymentIntent::create([
            'amount'   => (int) round($total * 100),
            'currency' => strtolower($this->currency()),
            'payment_method_types' => ['card'],
            'receipt_email' => $receiptEmail,
            'metadata' => [
                'order_id' => (string) $order->id,
                'uuid'     => (string) ($order->uuid ?? ''),
                'user_id'  => (string) (auth()->id() ?? ''),
                'source'   => 'roza-web',
            ],
        ]);

        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'provider'            => 'stripe',
                'provider_payment_id' => $intent->id,
                'provider_status'     => $intent->status,
                'amount'              => $total,
                'currency'            => strtoupper($this->currency()),
                'status'              => 'pending',
                'metadata'            => [
                    'receipt_email' => $receiptEmail,
                ],
            ]
        );

        return response()->json([
            'client_secret' => $intent->client_secret,
        ]);
    }

    /**
     * Success page: verify intent from Stripe then update DB
     * /pay/success?pid=pi_...
     */
    public function success(Request $request)
    {
        $request->validate([
            'pid' => ['required', 'string'],
        ]);

        $order = $this->getCheckoutOrderOrFail($request);

        Stripe::setApiKey(config('services.stripe.secret'));
        $intent = PaymentIntent::retrieve($request->query('pid'));

        // لازم الـ intent يكون لنفس الطلب (تحقق عبر metadata)
        $intentOrderId = (string) ($intent->metadata->order_id ?? '');
        if ($intentOrderId !== (string) $order->id) {
            abort(403);
        }

        $payment = Payment::where('order_id', $order->id)
            ->where('provider', 'stripe')
            ->first();

        if (!$payment) {
            abort(404, 'Payment record not found.');
        }

        DB::transaction(function () use ($intent, $payment, $order) {
            $payment->provider_status = $intent->status;

            $meta = $payment->metadata ?? [];
            $meta['stripe'] = [
                'id'       => $intent->id,
                'status'   => $intent->status,
                'amount'   => $intent->amount,
                'currency' => $intent->currency,
            ];
            $payment->metadata = $meta;

            if ($intent->status === 'succeeded') {

                // ✅ commit reservation (ينقص stock + ينقص reserved_stock)
                // ✅ idempotent + safe with transaction nesting
                $this->commitReservationForOrder($order);

                $payment->status = 'success';

                // حسب نظامك: خلي الطلب processing بعد الدفع
                if (($order->status ?? null) === 'pending') {
                    $order->status = 'processing';
                }

                $order->payment_status = 'paid';
                $order->save();

                // ✅ تفريغ السلة بعد نجاح الدفع فقط
                $cart = Cart::where('user_id', auth()->id())->first();
                if ($cart) {
                    $cart->items()->delete();
                }

            } elseif ($intent->status === 'canceled') {

                // ✅ release reservation عند الإلغاء النهائي فقط
                $this->releaseReservationForOrder($order);

                $payment->status = 'cancelled';
                $order->payment_status = 'cancelled';
                $order->save();

            } else {
                // pending وغيره
                $payment->status = 'pending';
            }

            $payment->save();
        });

        // ✅ شلنا بلوك التكرار يلي كان يعمل release مرة ثانية (كان redundant)

        // تنظيف السيشن بعد ما خلصنا
        $request->session()->forget('checkout_order_id');

        return view('Front.Pay.success', [
            'order' => $order,
            'payment' => $payment,
            'stripeStatus' => $intent->status,
        ]);
    }

    /**
     * Cancel redirect (لو عندك Route بيرجع المستخدم من Stripe/زر إلغاء)
     */
    public function cancel(Request $request)
    {
        $orderId = session('checkout_order_id');
        if (!$orderId) {
            return redirect()->route('front.checkout.index')->with('error', 'Payment was cancelled.');
        }

        $order = Order::with('items.product')->findOrFail($orderId);

        // ✅ release reservation عند الإلغاء النهائي
        $this->releaseReservationForOrder($order);

        // ✅ حدّثي حالات الطلب/الدفع
        $order->update([
            'payment_status' => 'cancelled',
            // 'status' => 'cancelled', // إذا بدك فعلياً
        ]);

        Payment::where('order_id', $order->id)->update([
            'status' => 'cancelled',
            'provider_status' => 'cancelled',
        ]);

        return redirect()->route('front.checkout.index')
            ->with('error', 'Payment was cancelled. No charges were made.');
    }

    /**
     * AJAX fail endpoint (من الواجهة لما يفشل confirmCardPayment)
     *
     * ✅ ملاحظة مهمة:
     * هون ما بدنا release لأن المستخدم ممكن يجرب بطاقة ثانية بنفس الصفحة.
     * ✅ كمان ما بدنا نختم order = failed نهائي.
     */
    public function fail(Request $request)
    {
        $orderId = session('checkout_order_id');
        if (!$orderId) {
            return response()->json(['ok' => false, 'message' => 'Missing order session.'], 400);
        }

        $order = Order::findOrFail($orderId);

        // ✅ خليه unpaid لحتى يضل في retry
        $order->update([
            'payment_status' => 'unpaid',
        ]);

        // ✅ خلي الدفع pending (مو failed نهائي)
        Payment::where('order_id', $order->id)->update([
            'status' => 'pending',
            'provider_status' => 'requires_payment_method', // أو 'failed_attempt'
        ]);

        return response()->json(['ok' => true]);
    }

    // ---------------- Helpers ----------------

    private function currency(): string
    {
        return env('STRIPE_CURRENCY', 'USD');
    }

    private function getCheckoutOrderOrFail(Request $request): Order
    {
        if (!auth()->check()) {
            abort(403);
        }

        $orderId = session('checkout_order_id');

        if (!$orderId) {
            abort(404, 'Checkout order not found. Create order first.');
        }

        $order = Order::findOrFail($orderId);

        // لازم الطلب يكون لنفس المستخدم
        if ((int) $order->user_id !== (int) auth()->id()) {
            abort(403);
        }

        return $order;
    }

    /**
     * ✅ (قديم) Restock items safely (مرة واحدة فقط)
     * requires: orders.stock_reverted boolean default 0
     *
     * ❗ مع reservation system: هاد لازم يضل موجود (مثل ما بدك)
     * بس ما عاد نستخدمه للفشل/الإلغاء الطبيعي، لأنو هاد بيرجع stock (غلط معنا).
     */
    private function restockOrderItems(Order $order): void
    {
        // ✅ لا تعيدي المخزون إذا سبق رجعناه
        if (!empty($order->stock_reverted) && (int)$order->stock_reverted === 1) {
            return;
        }

        DB::transaction(function () use ($order) {
            $order->loadMissing('items.product');

            foreach ($order->items as $item) {
                if (!$item->product) continue;

                // lock product row
                $product = $item->product->newQuery()->lockForUpdate()->find($item->product->id);
                if (!$product) continue;

                if ($product->stock !== null) {
                    $product->stock = (int)$product->stock + (int)$item->quantity;
                    $product->save();
                }
            }

            $order->stock_reverted = 1;
            $order->save();
        });
    }

    /**
     * ✅ NEW: commit reservation
     * - ينقص stock
     * - ينقص reserved_stock
     * - يحدد reservation.status=committed
     * - idempotent
     *
     * ✅ FIX: يمنع مشاكل nested transactions
     */
    private function commitReservationForOrder(\App\Models\Order $order): void
    {
        $reservations = \App\Models\StockReservation::where('order_id', $order->id)->get();
        if ($reservations->isEmpty()) return;

        $run = function () use ($reservations) {
            foreach ($reservations as $r) {

                if ($r->status === 'committed') {
                    continue; // idempotent
                }
                if ($r->status === 'released') {
                    continue;
                }

                $product = \App\Models\Product::where('id', $r->product_id)->lockForUpdate()->first();
                if (!$product) continue;

                $qty = (int) $r->qty;

                // ✅ خصم نهائي من stock (مرة واحدة)
                $product->stock = max(0, (int)$product->stock - $qty);

                // ✅ نقص من reserved_stock
                $product->reserved_stock = max(0, (int)$product->reserved_stock - $qty);

                $product->save();

                $r->status = 'committed';
                $r->save();
            }
        };

        if (DB::transactionLevel() > 0) {
            $run();
        } else {
            DB::transaction($run);
        }
    }

    /**
     * ✅ NEW: release reservation
     * - لا يغير stock
     * - ينقص reserved_stock فقط
     * - يحدد reservation.status=released
     * - idempotent
     *
     * ✅ FIX: يمنع مشاكل nested transactions
     */
    private function releaseReservationForOrder(\App\Models\Order $order): void
    {
        $reservations = \App\Models\StockReservation::where('order_id', $order->id)->get();
        if ($reservations->isEmpty()) return;

        $run = function () use ($reservations) {
            foreach ($reservations as $r) {

                if ($r->status === 'released') {
                    continue; // idempotent
                }
                if ($r->status === 'committed') {
                    // تم بيعها، ما لازم نرجّع
                    continue;
                }

                $product = \App\Models\Product::where('id', $r->product_id)->lockForUpdate()->first();
                if (!$product) continue;

                $qty = (int) $r->qty;

                $product->reserved_stock = max(0, (int)$product->reserved_stock - $qty);
                $product->save();

                $r->status = 'released';
                $r->save();
            }
        };

        if (DB::transactionLevel() > 0) {
            $run();
        } else {
            DB::transaction($run);
        }
    }
}
