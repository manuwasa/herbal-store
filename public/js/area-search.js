// Debounced area autocomplete against the server-side Biteship proxy.
// Reusable: mounts on every [data-area-search] container (checkout destination,
// admin branch origin). Emits an 'area-selected' CustomEvent on pick.
(function () {
    'use strict';

    function initAreaSearch(root) {
        var input = root.querySelector('[data-area-input]');
        var hiddenId = root.querySelector('[data-area-id]');
        var hiddenLabel = root.querySelector('[data-area-label]');
        var hiddenProvince = root.querySelector('[data-area-province]');
        var hiddenCity = root.querySelector('[data-area-city]');
        var hiddenDistrict = root.querySelector('[data-area-district]');
        var dropdown = root.querySelector('[data-area-dropdown]');
        var timer = null;

        if (!input || !dropdown) {
            return;
        }

        input.addEventListener('input', function () {
            // Any edit invalidates a previously-picked area, so a stale id can't
            // ride through to submission.
            hiddenId.value = '';
            window.clearTimeout(timer);

            var q = input.value.trim();
            if (q.length < 3) {
                dropdown.replaceChildren();
                dropdown.hidden = true;
                return;
            }

            timer = window.setTimeout(function () { runSearch(q); }, 400);
        });

        function runSearch(q) {
            fetch('/checkout/cari-area?q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json' },
            })
                .then(function (r) { return r.json(); })
                .then(function (data) { render(data.areas || []); })
                .catch(function () { render([]); });
        }

        function render(areas) {
            dropdown.replaceChildren();
            dropdown.hidden = areas.length === 0;

            areas.forEach(function (area) {
                var item = document.createElement('button');
                item.type = 'button';
                item.className = 'block w-full text-left px-3 py-2 hover:bg-stone-100';
                item.textContent = area.label;
                item.addEventListener('click', function () {
                    hiddenId.value = area.id;
                    hiddenLabel.value = area.label;
                    if (hiddenProvince) hiddenProvince.value = area.province || '';
                    if (hiddenCity) hiddenCity.value = area.city || '';
                    if (hiddenDistrict) hiddenDistrict.value = area.district || '';
                    input.value = area.label;
                    dropdown.hidden = true;
                    root.dispatchEvent(new CustomEvent('area-selected', { bubbles: true, detail: area }));
                });
                dropdown.appendChild(item);
            });
        }

        // Dismiss the dropdown on outside click.
        document.addEventListener('click', function (e) {
            if (!root.contains(e.target)) {
                dropdown.hidden = true;
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-area-search]').forEach(initAreaSearch);
    });
})();
