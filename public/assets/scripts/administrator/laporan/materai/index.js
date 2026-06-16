let table = $("#table-materai").DataTable({
    processing: false,
    serverSide: true,
    ajax: {
        url: baseUrl("/laporan/materai-fetch"),
        type: "POST",
        headers: {
            "X-XSRF-TOKEN": getCookie("XSRF-TOKEN"),
        },
        data: function (data) {
            data.tanggal_awal = $("#tanggal_awal").val();
            data.tanggal_akhir = $("#tanggal_akhir").val();
            data.filter_petugas_id = $('#filter_petugas_id').val();
        },
        dataSrc: "data",
    },
    columns: [
        {
            name: "",
            render: function (data, i, row, meta) {
                return meta.row + 1;
            },
            width: "20px",
            searchable: false,
            orderable: false,
        },
        {
            data: "date",
            searchable: false,
            orderable: false,
        },
        {
            data: "keterangan",
            searchable: true,
            orderable: false,
        },
        {
            data: "materai_masuk",
            searchable: false,
            orderable: false,
        },
        {
            data: "materai_keluar",
            searchable: false,
            orderable: false,
        },
        {
            data: "stok_materai",
            searchable: false,
            orderable: false,
        },
        {
            data: "petugas_name",
            searchable: false,
            orderable: false,
        },
    ],
});

// ==> Add Button
$("#btn-add-materai").click(function () {
    $("#form-materai").formReset();
    $(".message-error").empty();

    $.httpRequest({
        url: baseUrl("/laporan/materai/getpetugas"),
        method: "POST",
        response: (res) => {
            $.LoadingOverlay("hide");

            let petugasId = res.data.id;
            let petugasUsername = res.data.nama;

            $("#username-display").val(petugasUsername);
            $("#get-petugas").val(petugasId);
        },
    });
    $("#modal-form-materai").modal("show");
});

// End Add Button

// ==> Form Submit
$("#form-materai").formSubmit((response) => {
    if (response.statusCode == 200) {
        $("#form-materai").formReset();
        $("#modal-form-materai").modal("hide");

        swal("Sukses !", response.message, "success");
        table.ajax.reload();
    }
});

// Event untuk filter tanggal
$('#tanggal_awal').on('change', function() {
    table.ajax.reload();
});

$('#tanggal_akhir').on('change', function() {
    table.ajax.reload();
});

$('select[name="filter_petugas_id"]').select2AjaxNew({
    url: baseUrl('/laporan/materai/select2/getpetugas'),
    method: "POST",
    data : {
        selected : $('select[name="filter_petugas_id"]').val()
    }
});

$('select[name="filter_petugas_id"]').on('change',function(){
    $.LoadingOverlay("show");
    table.ajax.reload();
    $.LoadingOverlay("hide");
})
