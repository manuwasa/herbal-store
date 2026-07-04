// Cart interactions: add-to-cart stays on the current page and shows a toast
// (marketplace-app style, no forced navigation to /keranjang), and the cart
// page's quantity stepper auto-submits (debounced) instead of needing a
// separate "Perbarui" click. Every action falls back to a normal full-page
// form submission if fetch fails — nothing here is required for the site to
// work, only for it to feel instant.
(function () {
    'use strict';

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function rupiah(n) {
        return 'Rp' + Number(n).toLocaleString('id-ID');
    }

    function jsonHeaders() {
        return {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
        };
    }

    function showToast(message, variant) {
        var container = document.getElementById('toast-container');
        if (!container) return;

        var toast = document.createElement('div');
        var isError = variant === 'error';
        toast.className = 'flex items-center gap-2 px-4 py-3 rounded-lg shadow-lg text-sm font-medium text-white max-w-xs ' +
            (isError ? 'bg-red-600' : 'bg-stone-900') +
            ' transition-opacity duration-300 opacity-0';
        toast.textContent = message;
        container.appendChild(toast);

        requestAnimationFrame(function () { toast.classList.remove('opacity-0'); });
        window.setTimeout(function () {
            toast.classList.add('opacity-0');
            window.setTimeout(function () { toast.remove(); }, 300);
        }, 2600);
    }

    function updateCartBadge(count) {
        var badge = document.querySelector('[data-cart-badge]');
        if (!badge) return;
        if (count > 0) {
            badge.textContent = count;
            badge.hidden = false;
        } else {
            badge.hidden = true;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // --- Add to cart: stay on page, toast instead of redirecting ---------
        document.querySelectorAll('[data-add-to-cart-form]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var button = form.querySelector('button[type="submit"]');
                var productName = form.getAttribute('data-product-name') || 'Produk';
                var productId = form.querySelector('input[name="product_id"]').value;
                var quantity = form.querySelector('input[name="quantity"]').value;

                if (button) button.disabled = true;

                fetch(form.action, {
                    method: 'POST',
                    headers: jsonHeaders(),
                    body: JSON.stringify({ product_id: productId, quantity: quantity }),
                })
                    .then(function (r) { return r.json().then(function (data) { return { ok: r.ok, data: data }; }); })
                    .then(function (result) {
                        if (result.ok && result.data.success) {
                            showToast(productName + ' ditambahkan ke keranjang.');
                            updateCartBadge(result.data.cartCount);
                        } else {
                            showToast((result.data && result.data.message) || 'Gagal menambahkan ke keranjang.', 'error');
                        }
                    })
                    .catch(function () { form.submit(); })
                    .finally(function () { if (button) button.disabled = false; });
            });
        });

        // --- Cart page: quantity auto-update (debounced), no "Perbarui" click ---
        document.querySelectorAll('[data-cart-update-form]').forEach(function (form) {
            var input = form.querySelector('[data-quantity-input]');
            if (!input) return;
            var row = form.closest('[data-cart-row]');
            var debounceTimer = null;

            function submitUpdate() {
                fetch(form.action, {
                    method: 'PATCH',
                    headers: jsonHeaders(),
                    body: JSON.stringify({ quantity: input.value }),
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (!data.success) throw new Error('update failed');
                        if (data.isEmpty) { window.location.reload(); return; }

                        if (row) {
                            var lineTotalEl = row.querySelector('[data-line-total]');
                            if (lineTotalEl) lineTotalEl.textContent = rupiah(data.lineTotal);
                        }
                        var subtotalEl = document.querySelector('[data-cart-subtotal]');
                        if (subtotalEl) subtotalEl.textContent = rupiah(data.subtotal);
                        updateCartBadge(data.cartCount);
                    })
                    .catch(function () { form.submit(); });
            }

            // Debounced so rapid +/- clicks send one request for the final
            // quantity, not one request per click.
            form.querySelectorAll('[data-step]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    window.clearTimeout(debounceTimer);
                    debounceTimer = window.setTimeout(submitUpdate, 500);
                });
            });
        });

        // --- Cart page: remove item without a full reload ---------------------
        document.querySelectorAll('[data-cart-remove-form]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var row = form.closest('[data-cart-row]');

                fetch(form.action, {
                    method: 'DELETE',
                    headers: jsonHeaders(),
                    body: JSON.stringify({}),
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (!data.success) throw new Error('remove failed');
                        if (data.isEmpty) { window.location.reload(); return; }

                        if (row) row.remove();
                        var subtotalEl = document.querySelector('[data-cart-subtotal]');
                        if (subtotalEl) subtotalEl.textContent = rupiah(data.subtotal);
                        updateCartBadge(data.cartCount);
                        showToast('Produk dihapus dari keranjang.');
                    })
                    .catch(function () { form.submit(); });
            });
        });
    });
})();
