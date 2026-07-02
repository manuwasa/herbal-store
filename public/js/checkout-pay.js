// Payment step only: invoke the Midtrans Snap widget. Callbacks route the buyer
// to the confirmation page (success/pending) or leave them here to retry (error).
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var button = document.getElementById('pay-button');
        if (!button || typeof window.snap === 'undefined') {
            return;
        }

        function go(url) {
            window.location.href = url;
        }

        button.addEventListener('click', function () {
            window.snap.pay(window.__snapToken, {
                onSuccess: function () { go(window.__confirmationUrl); },
                onPending: function () { go(window.__confirmationUrl); },
                onError: function () {
                    alert('Pembayaran gagal atau dibatalkan. Silakan coba lagi.');
                },
                onClose: function () {
                    // Buyer dismissed the popup without finishing — they can retry.
                },
            });
        });
    });
})();
