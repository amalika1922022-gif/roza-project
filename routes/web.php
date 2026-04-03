<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\AdminMiddleware;

use App\Http\Controllers\front\HomeController;
use App\Http\Controllers\front\ProductController;
use App\Http\Controllers\front\CartController;
use App\Http\Controllers\front\CheckoutController;
use App\Http\Controllers\front\AccountController;

use App\Http\Controllers\StripePaymentController;

use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\CategoryAdminController;
use App\Http\Controllers\admin\ProductAdminController;
use App\Http\Controllers\admin\OrderAdminController;
use App\Http\Controllers\admin\UserController;

use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\auth\RegisterController;
use App\Http\Controllers\auth\ForgetPasswordController;
use App\Http\Controllers\auth\ResetPasswordController;

/* Front Routes */

Route::name('front.')->group(function () {

    // Home
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Products
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/{slug}', [ProductController::class, 'show'])->name('show');
    });

    // Cart
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::post('/remove/{id}', [CartController::class, 'remove'])->name('remove');
    });

    // Checkout (محمي بـ auth)
    Route::prefix('checkout')->name('checkout.')->middleware('auth')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    });

    // Account (محمي بـ auth)
    Route::prefix('account')->name('account.')->middleware('auth')->group(function () {
        Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
        Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
        Route::get('/address', [AccountController::class, 'address'])->name('address');
        Route::post('/address', [AccountController::class, 'updateAddress'])->name('address.update');
    });
});

/* Stripe Payment Routes (محمي بـ auth) */
Route::middleware('auth')->group(function () {
    Route::get('/pay', [StripePaymentController::class, 'checkoutPage'])->name('pay.form');
    Route::post('/pay/intent', [StripePaymentController::class, 'createIntent'])->name('pay.intent');
    Route::get('/pay/success', [StripePaymentController::class, 'success'])->name('pay.success');
    Route::get('/pay/cancel', [StripePaymentController::class, 'cancel'])->name('pay.cancel');
    Route::post('/pay/fail', [StripePaymentController::class, 'fail'])->name('pay.fail');
});

/* Admin Routes */
Route::prefix('admin')->name('admin.')->middleware(['auth', AdminMiddleware::class])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', CategoryAdminController::class);

    Route::resource('products', ProductAdminController::class);
    Route::get('products/{product}/primary-image/{image}', [ProductAdminController::class, 'setPrimaryImage'])
        ->name('products.primary-image');
    Route::delete('products/{product}/image/{image}', [ProductAdminController::class, 'deleteImage'])
        ->name('products.image.delete');

    Route::post('homepage-carousel', [DashboardController::class, 'storeHomepageCarouselItem'])
        ->name('homepage-carousel.store');
    Route::put('homepage-carousel/{item}', [DashboardController::class, 'updateHomepageCarouselItem'])
        ->name('homepage-carousel.update');

    Route::delete('homepage-carousel/{item}', [DashboardController::class, 'destroyHomepageCarouselItem'])
        ->name('homepage-carousel.destroy');

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderAdminController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderAdminController::class, 'show'])->name('show');
        Route::put('/{order}/status', [OrderAdminController::class, 'updateStatus'])->name('updateStatus');
    });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/blocked', [UserController::class, 'blocked'])->name('blocked');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');

        Route::put('/{user}/role', [UserController::class, 'updateRole'])->name('updateRole');
        Route::put('/{user}/block', [UserController::class, 'block'])->name('block');
        Route::put('/{user}/unblock', [UserController::class, 'unblock'])->name('unblock');
    });
});

/* login Routes */
Route::prefix('auth')->name('auth.')->group(function () {

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

    Route::get('/forgot-password', [ForgetPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgetPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('/reset-password', [ResetPasswordController::class, 'show'])->name('password.reset.demo');
    Route::post('/reset-password', [ResetPasswordController::class, 'submit'])->name('password.reset.demo.submit');

    Route::middleware('auth')->post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Laravel auth middleware لازم يلاقي route اسمها "login"
Route::get('/login', function () {
    return redirect()->route('auth.login'); // يودّي على /auth/login
})->name('login');
