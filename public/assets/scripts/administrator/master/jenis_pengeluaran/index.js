let table = $("table").DataTable({
    ajax: {
        url: baseUrl("/master/jenis_pengeluaran-fetch"),
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
        { data: "nama" },
        {
            data: "created_at",
            render: function (data, i, row) {
                return new Date(data).toLocaleString();
            }
        },
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
                if (access.update == 1) div.append(btn);

                // ==> Button Delete
                var btn = document.createElement("button");
                btn.className = "btn btn-danger btn-action action-hapus";
                btn.innerHTML = '<i class="fas fa-eraser mr-1"></i> Hapus';
                if (access.delete == 1) div.append(btn);

                return div.outerHTML;
            },
            width: "180px",
        },
    ],
    createdRow: function (row, data) {
        // ==> Edit Button
        $(".action-edit", row).click(function (e) {
            e.preventDefault();

            $('input[name="id"]').val(data.id);
            $('input[name="nama"]').val(data.nama);

            $(".message-error").empty();
            $("#modal-form").modal("show");
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
                        url: baseUrl(`/master/jenis_pengeluaran/${data.id}`),
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

// Select2
$('.select2').select2();
// End User Image

// Add Button
$("#btn-form-add").click(function () {
    $("#form-jenis-pengeluaran").formReset();
    $(".message-error").empty();
    $("#modal-form").modal("show");
});
// End Add Button

// Form Submit
$("#form-jenis-pengeluaran").formSubmit((response) => {
    if (response.statusCode == 200) {
        $("#form-jenis-pengeluaran").formReset();
        $("#modal-form").modal("hide");

        swal("Sukses !", response.message, "success");
        table.ajax.reload();
    }
});
// End Form Submit
