document.addEventListener('DOMContentLoaded', () => {
    const cfg = window.__stripePayment || {};
    if (!cfg.stripeKey) return;

    const stripe = Stripe(cfg.stripeKey);
    const elements = stripe.elements();

    const card = elements.create('card', { hidePostalCode: true });
    card.mount('#card-element');

    const payBtn = document.getElementById('payBtn');
    const cardErrors = document.getElementById('card-errors');

    // Roza Modal
    function showMsg(type, text) {
        const modal = document.getElementById('rozaModal');
        const icon = document.getElementById('rozaModalIcon');
        const title = document.getElementById('rozaModalTitle');
        const body = document.getElementById('rozaModalText');
        const ok = document.getElementById('rozaModalOk');
        const x = document.getElementById('rozaModalX');

        if (!modal || !icon || !title || !body) return;

        const isSuccess = type === 'success';
        icon.textContent = isSuccess ? '✔️' : '⚠️';
        title.textContent = isSuccess ? 'Payment successful' : 'Payment failed';
        body.textContent = text || '';

        modal.style.display = 'block';

        const close = () => (modal.style.display = 'none');
        if (ok) ok.onclick = close;
        if (x) x.onclick = close;

        modal.onclick = (e) => {
            if (e.target === modal) close();
        };
    }

    function setLoading(isLoading) {
        if (!payBtn) return;
        payBtn.disabled = isLoading;

        if (isLoading) {
            payBtn.dataset.original = payBtn.innerHTML;
            payBtn.innerHTML = `<span class="roza-spinner"></span>Processing...`;
        } else {
            payBtn.innerHTML = payBtn.dataset.original || payBtn.innerHTML;
        }
    }

    card.on('change', ({ error }) => {
        if (cardErrors) cardErrors.textContent = error ? error.message : '';
    });

    if (!payBtn) return;

    payBtn.addEventListener('click', async () => {
        if (cardErrors) cardErrors.textContent = '';
        setLoading(true);

        try {
            // 1) Create intent
            const res = await fetch(cfg.intentUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': cfg.csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({}),
            });

            const raw = await res.text();
            let data = {};
            try { data = JSON.parse(raw); } catch (e) {}

            if (!res.ok) {
                throw new Error(data.error || data.message || 'Failed to create payment intent.');
            }
            if (!data.client_secret) {
                throw new Error('Missing client_secret from server.');
            }

            // 2) Confirm payment
            const confirmPromise = stripe.confirmCardPayment(data.client_secret, {
                payment_method: { card },
            });

            const timeoutPromise = new Promise((_, reject) =>
                setTimeout(() => reject(new Error('Stripe timed out. Please try again.')), 20000)
            );

            const result = await Promise.race([confirmPromise, timeoutPromise]);

            if (result.error) {
                // restore stock
                await fetch(cfg.failUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': cfg.csrf,
                        'Accept': 'application/json',
                    },
                });

                showMsg('error', result.error.message || 'Payment failed.');
                setLoading(false);
                return;
            }

            const status = result.paymentIntent?.status;

            if (status === 'succeeded') {
                window.location.href =
                    cfg.successUrl + '?pid=' + encodeURIComponent(result.paymentIntent.id);
                return;
            }

            if (status === 'requires_payment_method') {
                await fetch(cfg.failUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': cfg.csrf,
                        'Accept': 'application/json',
                    },
                });

                showMsg('error', 'Your card was declined. Please try another card.');
                setLoading(false);
                return;
            }

            showMsg('error', 'Payment not completed. Status: ' + (status || 'unknown'));
            setLoading(false);

        } catch (e) {
            showMsg('error', (e && e.message) ? e.message : 'Something went wrong.');
            setLoading(false);
        }
    });
});
