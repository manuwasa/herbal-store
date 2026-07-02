// Checkout page only: on area selection, fetch shipping rates and render courier
// options; keep the grand total and hidden fields in sync. Degrades cleanly to
// the "arranged via WhatsApp" fallback on any failure.
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var areaRoot = document.querySelector('[data-area-search]');
        var list = document.getElementById('shipping-options');
        var grandTotalEl = document.getElementById('grand-total');
        var subtotalEl = document.querySelector('[data-subtotal]');
        if (!areaRoot || !list) {
            return;
        }

        var subtotal = subtotalEl ? parseFloat(subtotalEl.getAttribute('data-subtotal')) : 0;

        function rupiah(n) {
            return 'Rp' + Number(n).toLocaleString('id-ID');
        }

        function setTotals(shipping) {
            document.getElementById('shipping_cost').value = shipping;
            grandTotalEl.textContent = rupiah(subtotal + Number(shipping));
        }

        function csrf() {
            var el = document.querySelector('input[name="_token"]');
            return el ? el.value : '';
        }

        function selectOption(opt, radio) {
            document.getElementById('shipping_courier').value = opt.courier_code;
            document.getElementById('shipping_service').value = opt.service_code;
            document.getElementById('shipping_service_label').value = opt.courier_name + ' - ' + opt.service_name;
            setTotals(opt.price);
            if (radio) radio.checked = true;
        }

        function showFallback() {
            list.innerHTML = '<p class="text-amber-700">Ongkir otomatis sedang tidak tersedia. ' +
                'Ongkos kirim akan disepakati via WhatsApp setelah order.</p>';
            document.getElementById('shipping_courier').value = '';
            document.getElementById('shipping_service').value = '';
            document.getElementById('shipping_service_label').value = '';
            setTotals(0);
        }

        function renderOptions(options) {
            list.innerHTML = '';
            options.forEach(function (opt, i) {
                var id = 'ship-opt-' + i;
                var label = document.createElement('label');
                label.className = 'flex items-center justify-between gap-2 py-1.5 cursor-pointer';
                label.htmlFor = id;

                var left = document.createElement('span');
                left.className = 'flex items-center gap-2';
                var radio = document.createElement('input');
                radio.type = 'radio';
                radio.name = 'shipping_option_radio';
                radio.id = id;
                left.appendChild(radio);
                var text = document.createElement('span');
                text.textContent = opt.courier_name + ' - ' + opt.service_name +
                    (opt.duration ? ' (' + opt.duration + ')' : '');
                left.appendChild(text);

                var price = document.createElement('span');
                price.className = 'font-medium text-stone-900';
                price.textContent = rupiah(opt.price);

                label.appendChild(left);
                label.appendChild(price);
                radio.addEventListener('change', function () { selectOption(opt, radio); });
                list.appendChild(label);

                if (i === 0) { selectOption(opt, radio); }
            });
        }

        areaRoot.addEventListener('area-selected', function (e) {
            var area = e.detail;
            list.innerHTML = '<p class="text-stone-500">Menghitung ongkir…</p>';

            fetch('/checkout/cek-ongkir', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf(),
                },
                body: JSON.stringify({
                    destination_area_id: area.id,
                    destination_province: area.province,
                    destination_city: area.city,
                    destination_district: area.district,
                }),
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.fallback || !data.options || data.options.length === 0) {
                        showFallback();
                    } else {
                        renderOptions(data.options);
                    }
                })
                .catch(function () { showFallback(); });
        });
    });
})();
