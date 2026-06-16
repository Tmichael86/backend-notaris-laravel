let table = $("table").DataTable({
    ajax: {
        url: baseUrl("/users-fetch"),
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
            data: "group_nama",
            render: function (data, i, row) {
                return `<b class="text-secondary text-uppercase">${data}</b>`;
            }
        },
        { data: "username" },
        { data: "email" },
        { data: "no_telp" },
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
            let img = assetsUrl(
                `uploads/profile/${data.image ? data.image : "no-image.png"}`
            );

            $('input[name="id"]').val(data.id);
            $('input[name="nama"]').val(data.nama);
            $('input[name="no_telp"]').val(data.no_telp);
            $('input[name="username"]').val(data.username);
            $('input[name="email"]').val(data.email);
            $('textarea[name="alamat"]').val(data.alamat);
            $('select[name="group_id"]').val(data.group_id).trigger("change");

            $(".foto-profile").attr("src", img);

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
                        url: baseUrl(`/users/${data.id}`),
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

// User Image
$("#user-img-file").change(function () {
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $(".foto-profile").attr("src", e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    }
});

$(".foto-profile-card").click(function () {
    $("#user-img-file").trigger("click");
});
// End User Image

// Add Button
$("#btn-form-add").click(function () {
    $(".foto-profile").attr("src", assetsUrl("uploads/profile/no-image.png"));
    $("#form-pengguna").formReset();
    $('select[name="group_id"]').val("").trigger("change");
    $(".message-error").empty();
    $("#modal-form").modal("show");
});
// End Add Button

// Form Submit
$("#form-pengguna").formSubmit((response) => {
    if (response.statusCode == 200) {
        $("#form-pengguna").formReset();
        $("#modal-form").modal("hide");

        swal("Sukses !", response.message, "success");
        table.ajax.reload();
    }
});
// End Form Submit
