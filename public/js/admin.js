// Admin panel interactivity — jQuery + DataTables, both self-hosted (no CDN).
$(function () {
    $('table[id$="-table"]').each(function () {
        var $table = $(this);

        $table.DataTable({
            pageLength: 10,
            lengthChange: false,
            columnDefs: [{ targets: 'no-sort', orderable: false }],
        });

        // Move the auto-generated search box out of the table's own
        // toolbar and into the slot next to the "Tambah" button, if present.
        // The search box is the only thing in its toolbar row (length menu
        // is disabled above), so once it's moved the row itself is dropped.
        var $slot = $('#' + $table.attr('id') + '-search-slot');
        var $search = $table.closest('.dt-container').find('.dt-search');

        if ($slot.length && $search.length) {
            var $row = $search.closest('.dt-layout-row');
            $slot.append($search);
            $row.remove();
        }
    });

    // Sidebar: off-canvas drawer on mobile, collapsible-to-icons on desktop.
    // State is applied by toggling real Tailwind utility classes directly
    // (not a custom override class) so there's no cascade-layer fight with
    // the base utility classes already on these elements.
    var $sidebar = $('#admin-sidebar');
    var $backdrop = $('#sidebar-backdrop');
    var $labels = $('.sidebar-label');
    var $collapseIcon = $('.sidebar-collapse-icon');
    var $items = $('.sidebar-item');

    function openMobileSidebar() {
        $sidebar.removeClass('-translate-x-full').addClass('translate-x-0');
        $backdrop.removeClass('hidden');
    }

    function closeMobileSidebar() {
        $sidebar.removeClass('translate-x-0').addClass('-translate-x-full');
        $backdrop.addClass('hidden');
    }

    $('#sidebar-open-toggle').on('click', openMobileSidebar);
    $('#sidebar-close-toggle').on('click', closeMobileSidebar);
    $backdrop.on('click', closeMobileSidebar);

    function setCollapsed(collapsed) {
        if (collapsed) {
            $sidebar.addClass('md:w-16');
            $labels.addClass('md:hidden');
            $collapseIcon.addClass('md:rotate-180');
            $items.addClass('md:justify-center md:px-0 md:gap-0');
        } else {
            $sidebar.removeClass('md:w-16');
            $labels.removeClass('md:hidden');
            $collapseIcon.removeClass('md:rotate-180');
            $items.removeClass('md:justify-center md:px-0 md:gap-0');
        }
        localStorage.setItem('admin-sidebar-collapsed', collapsed ? '1' : '0');
    }

    // Desktop collapse-to-icons state persists across page loads.
    setCollapsed(localStorage.getItem('admin-sidebar-collapsed') === '1');

    $('#sidebar-collapse-toggle').on('click', function () {
        setCollapsed(!$sidebar.hasClass('md:w-16'));
    });
});
