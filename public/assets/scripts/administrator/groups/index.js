// Add Triangle in Sidebar
$('#system').addClass('active');

let table = $("table").DataTable({
    ajax: {
        url: baseUrl("/groups-fetch"),
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
        { data: "group_nama" },
        {
            data: "id",
            render: function (data, i, row) {
                // ==> Container
                var div = document.createElement("div");
                div.className = "row-action";

                // ==> Button Edit
                var btn = document.createElement("button");
                btn.className = "btn btn-warning btn-action action-edit";
                btn.innerHTML = '<i class="fa fa-edit mr-1"></i> Edit';
                div.append(btn);

                // ==> Button Delete
                var btn = document.createElement("button");
                btn.className = "btn btn-danger btn-action action-hapus";
                btn.innerHTML = '<i class="fas fa-eraser mr-1"></i> Hapus';
                div.append(btn);

                return div.outerHTML;
            },
            width: "180px",
        },
    ],
    createdRow: function (row, data) {
        // ==> Edit Button
        $(".action-edit", row).click(function (e) {
            location.href = baseUrl('/groups/form/' + data.id)
        });

        // ==> Delete Button
        $(".action-hapus", row).click(function (e) {
            e.preventDefault();
            swal({
                title: "Peringatan !",
                text: `Anda yakin akan menghapus data ini ??`,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#993333",
                cancelButtonColor: "#5d5d5d",
                cancelButtonText: "Tidak",
                confirmButtonText: "Hapus",
            }).then(
                function () {
                    $.LoadingOverlay("show");
                    $.httpRequest({
                        url: baseUrl(`/groups/${data.id}`),
                        method: "DELETE",
                        response: (res) => {
                            $.LoadingOverlay("hide");
                            if (res.statusCode == 200) {
                                swal("Sukses !", res.message, "success");
                                table.ajax.reload();
                            }
                        },
                    });
                },
                function (dismiss) {
                    if (dismiss === "cancel") {
                    }
                }
            );
        });
    },
});
