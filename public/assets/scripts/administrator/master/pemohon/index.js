let table = $("table").DataTable({
    ajax: {
        url: baseUrl("/master/pemohon-fetch"),
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
        {
            data:"nik"
        },
        { data: "nama" },
        {
            data: "jenis_kelamin_nama",
        },
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

            $('input[name="id"]').val(data.id);
            $('input[name="nama"]').val(data.nama);
            $('input[name="nik"]').val(data.nik);
            $('input[name="no_telp"]').val(data.no_telp);
            $('textarea[name="alamat"]').val(data.alamat);

            $('select[target="jenis_kelamin"]').select2AjaxNew({
                url: baseUrl('/master/pemohon/select2/getjeniskelamin'),
                method: "POST",
                data:{
                    selected:data.jenis_kelamin
                }
            });

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
                        url: baseUrl(`/master/pemohon/${data.id}`),
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

// datepicker
$('.datepicker').datepicker();

// Add Button
$("#btn-form-add").click(function () {
    $("#form-pemohon").formReset();
    $('select[target="jenis_kelamin"]').select2AjaxNew({
        url: baseUrl('/master/pemohon/select2/getjeniskelamin'),
        method: "POST",
    });
    $(".message-error").empty();
    $("#modal-form").modal("show");
});

// End Add Button

// select2
$('select[target="jenis_kelamin"]').select2AjaxNew({
    url: baseUrl('/master/pemohon/select2/getjeniskelamin'),
    method: "POST",
});

// Form Submit
$("#form-pemohon").formSubmit((response) => {
    if (response.statusCode == 200) {
        $("#form-pemohon").formReset();
        $("#modal-form").modal("hide");

        swal("Sukses !", response.message, "success");
        table.ajax.reload();
    }
});
// End Form Submit

$("#export-to-excel").on("click", function () {
    // Mengambil data dari DataTable
    var data = table.rows({ filter: 'applied' }).data().toArray();
    var formattedData = data.map((row, index) => ({
        No: index + 1,
        NIK: row.nik,
        Nama: row.nama,
        JenisKelamin: row.jenis_kelamin_nama,
        NoTelp: row.no_telp,
        CreatedAt: new Date(row.created_at).toLocaleString()
    }));

    // Membuat workbook dan worksheet
    var wb = XLSX.utils.book_new();
    var ws = XLSX.utils.json_to_sheet(formattedData);

    // Menambahkan worksheet ke workbook
    XLSX.utils.book_append_sheet(wb, ws, "Data Pemohon");

    // Mengunduh file Excel
    XLSX.writeFile(wb, "data_pemohon.xlsx");
});
