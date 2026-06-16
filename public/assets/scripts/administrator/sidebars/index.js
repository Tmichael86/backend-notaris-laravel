// Add Triangle in Sidebar
$('#system').addClass('active');

let table = $("table").DataTable({
    ajax: {
        url: baseUrl("/sidebars-fetch"),
        headers: { 'X-XSRF-TOKEN': getCookie('XSRF-TOKEN') },
        dataSrc: "data",
        type: "POST",
    },
    processing: true,
    serverSide: true,
    paging: true,
    lengthChange: true,
    searching: true,
    ordering: true,
    info: true,
    autoWidth: false,
    columns: [
        {
            name: "",
            render: function (data, i, row, meta) {
                return meta.row + 1;
            },
            width: "20px",
        },
        { data: "sidebar_nama" },
        {
            data: "sidebar_parent_id",
            render: function (data, i, row) {
                return `<b class="text-secondary">${data}</b>`;
            }
        },
        { data: "sidebar_route" },
        { data: "sidebar_kode" },
        { data: "sidebar_index" },
        { data: "sidebar_icon" },
        {
            data: "created_at",
            render: function (data, i, row) {
                return new Date(data).toLocaleString();
            }
        },
    ]
});
