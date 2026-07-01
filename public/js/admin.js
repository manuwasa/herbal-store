// Admin panel interactivity — jQuery + DataTables, both self-hosted (no CDN).
$(function () {
    $('table[id$="-table"]').DataTable({
        pageLength: 10,
        lengthChange: false,
    });
});
