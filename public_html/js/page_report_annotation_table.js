$(function () {
    $.fn.DataTable.ext.pager.numbers_length = 5;

    $('#public').DataTable({
        ordering: false,
        pageLength: 15,
        lengthMenu: [[15, 30, 50, -1], [15, 30, 50, 'All']]
    });
});
