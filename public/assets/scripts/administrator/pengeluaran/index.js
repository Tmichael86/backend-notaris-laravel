totalPengeluaran();

let table = $("table").DataTable({
    ajax: {
        url: baseUrl("/pengeluaran-fetch"),
        headers: { 'X-XSRF-TOKEN': getCookie('XSRF-TOKEN') },
        dataSrc: "data",
        type: "POST",
        'data': function(form) {
            form.jenis_pengeluaran_id = $('#filter_pengeluaran_id').val();
            form.tanggal_awal = $('#tanggal_awal').val();
            form.tanggal_akhir = $('#tanggal_akhir').val();
        },
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
        { data: "no_faktur" },
        { data: "keterangan"},
        { data: "jenis_pengeluaran_nama"},
        { data: "jumlah"},
        {
            data: "tanggal_pengeluaran",
            render: function (data, i, row) {
                return localDateTime(data);
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
            $('input[name="no_faktur"]').val(data.no_faktur);
            $('input[name="tanggal_pengeluaran"]').val(localDateTime(data.tanggal_pengeluaran));
            $('input[name="jumlah"]').val(formatRupiah(data.jumlah));

            $('select[target="jenis_pengeluaran_id"]').select2AjaxNew({
                url: baseUrl('/pengeluaran/select2/getjenispengeluaran'),
                method: "POST",
                data:{
                    selected:data.jenis_pengeluaran_id
                }
            });

            $('textarea[name="keterangan"]').val(data.keterangan);
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
                        url: baseUrl(`/pengeluaran/${data.id}`),
                        method: "DELETE",
                        response: (res) => {
                            $.LoadingOverlay("hide");
                            if (res.statusCode == 200) {
                                swal("Sukses !", res.message, "success");
                                totalPengeluaran();
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

$('#filter_pengeluaran_id').on('change',function(){
    $.LoadingOverlay("show");
    totalPengeluaran();
    table.ajax.reload();
    $.LoadingOverlay("hide");
})

$('#tanggal_awal').on('change',function(){
    $.LoadingOverlay("show");
    totalPengeluaran();
    table.ajax.reload();
    $.LoadingOverlay("hide");
})

$('#tanggal_akhir').on('change',function(){
    $.LoadingOverlay("show");
    totalPengeluaran();
    table.ajax.reload();
    $.LoadingOverlay("hide");
})
// End User Image

// Add Button
$("#btn-form-add").click(function () {
    $("#form-pengeluaran").formReset();
    $(".message-error").empty();
    $.httpRequest({
        url: baseUrl(`/pengeluaran/get/nofaktur`),
        method: "POST",
        response: (res) => {
            if (res.statusCode == 200) {
                $('#no_faktur').val(res.no_faktur_code)
            }
        },
    });
    $('select[target="jenis_pengeluaran_id"]').select2AjaxNew({
        url: baseUrl('/pengeluaran/select2/getjenispengeluaran'),
        method: "POST",
    });
    $("#modal-form").modal("show");
});
// End Add Button

$('[name="jumlah"]').on("input",function(){
    const value = $('[name="jumlah"]').val()
    var numericVal = value.replace(/[^0-9]/g, '');
    var formattedVal = formatRupiah(numericVal);
    $('[name="jumlah"]').val(formattedVal);
})

function totalPengeluaran(){
    $.httpRequest({
        url: baseUrl(`/pengeluaran/get/totalpengeluaran`),
        method: "POST",
        data:JSON.stringify({
            "jenis_pengeluaran_id": $('#filter_pengeluaran_id').val(),
            "tanggal_awal": $('#tanggal_awal').val(),
            "tanggal_akhir": $('#tanggal_akhir').val(),
        }),
        headers: { 'X-XSRF-TOKEN': getCookie('XSRF-TOKEN') },
        contentType: "application/json",
        response: (res) => {
            if (res.statusCode == 200) {
                $('#total_pengeluaran').text('')
                $('#total_pengeluaran').text('Rp. '+formatRupiah(res.data))
            }
        },
    });
}

// formater rupiah
function formatRupiah(angka) {
    var reverse = angka.toString().split('').reverse().join('');
    var ribuan = reverse.match(/\d{1,3}/g);
    var formatted = ribuan.join('.').split('').reverse().join('');
    return formatted;
}


// Memisahkan tanggal dan waktu
function localDateTime(tanggal) {
    let parts = tanggal.split(' ');
    let datePart = parts[0];

    let dateComponents = datePart.split('-');
    let year = dateComponents[0];
    let month = dateComponents[1];
    let day = dateComponents[2];

    let formattedDate = day + '/' + month + '/' + year +' '+parts[1];
    return formattedDate;
}

// datepicker
$('.datetimepicker').datetimepicker({
    format:'DD/MM/YYYY HH:mm:ss',
  });

// select2
$('select[target="jenis_pengeluaran_id"]').select2AjaxNew({
    url: baseUrl('/pengeluaran/select2/getjenispengeluaran'),
    method: "POST",
});

//filter select 2
$('select[target="filter_pengeluaran_id"]').select2AjaxNew({
    url: baseUrl('/pengeluaran/select2/getjenispengeluaran'),
    method: "POST",
});

// Form Submit
$("#form-pengeluaran").formSubmit((response) => {
    if (response.statusCode == 200) {
        $("#form-pengeluaran").formReset();
        $("#modal-form").modal("hide");

        swal("Sukses !", response.message, "success");
        totalPengeluaran();
        table.ajax.reload();
    }
});
// End Form Submit

$("#export-to-excel").click(function() {
    // Ambil data dari DataTable
    let tableData = table.rows().data().toArray();

    // Buat array untuk menyimpan data yang akan diekspor
    let exportData = [];

    // Tambahkan header
    exportData.push([
        "No",
        "No Faktur",
        "Keterangan",
        "Jenis Pengeluaran",
        "Jumlah",
        "Tanggal Pengeluaran"
    ]);

    // Tambahkan data ke array
    tableData.forEach((row, index) => {
        exportData.push([
            index + 1,
            row.no_faktur,
            row.keterangan,
            row.jenis_pengeluaran_nama,
            row.jumlah,
            localDateTime(row.tanggal_pengeluaran)
        ]);
    });

    // Buat worksheet dari data
    let ws = XLSX.utils.aoa_to_sheet(exportData);

    // Buat workbook dan tambahkan worksheet
    let wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Pengeluaran Data");

    // Simpan file Excel
    XLSX.writeFile(wb, "Pengeluaran_Data.xlsx");
});