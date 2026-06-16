// Memisahkan tanggal dan waktu
$('#backButton').click(function () {
    // Tutup modal yang sedang aktif
    $('#modal-detail-catatan').modal('hide');

    // Tunggu sampai modal tertutup, lalu buka modal sebelumnya
    $('#modal-detail-catatan').on('hidden.bs.modal', function () {
        $('#modal-detail-proses').modal('show');
    });
});

// Saat Load Kadang Warning
// $.fn.dataTable.ext.errMode = "none";

let transaksi_id;
let no_transaksi;
let jenis_pekerjaan;

let table = $(".table-penghasilan").DataTable({
    ajax: {
        url: baseUrl("/penghasilan-fetch"),
        headers: {
            'X-XSRF-TOKEN': getCookie('XSRF-TOKEN')
        },
        dataSrc: "data",
        type: "POST",
        data: function (form) {
            form.tanggal_awal = $("#tanggal_awal").val();
            form.tanggal_akhir = $("#tanggal_akhir").val();
            form.jenis_pekerjaan_id = $('#filter_jenis_pekerjaan_id').val();
            form.pekerjaan_id = $('#filter_pekerjaan_id').val();
            form.kategori_id = $('#filter_kategori_pekerjaan_id').val();
            form.status_id = $('#filter_status_id').val();
            form.petugas_id = $('#filter_petugas_id').val();
        }
    },
    autoWidth: true,
    responsive: false,
    ordering: false,
    searching: true,
    info: true,
    sorting: false,
    paging: true,
    columns: [
        {
            name: "",
            render: function (data, i, row, meta) {
                return meta.row + 1;
            },
            width: "20px",
        },
        {
            data: "tanggal_pembayaran",
            render: function (data, i, row) {
                return localDateTime(data);
            }
        },
        {
            data: "no_akta"
        },
        {
            data: "pemohon_nik"
        },
        {
            data: "pemohon_nama",
            render: function (data, i, row) {
                return `<div style="width:120px">${data}</div>`;
            }
        },
        {
            data: "jenis_pekerjaan_nama"
        },
        {
            data: "pekerjaan_nama",
            render: function (data, i, row) {
                return `<div style="width:150px">${data}</div>`;
            }
        },
        {
            data: "kategori_nama"
        },
        {
            data: "total"
        },
        {
            data: "jumlah_dibayar"
        },
        {
            data: "total_piutang"
        },
        {
            data: "petugas_nama"
        }
    ],
    createdRow: function (row, data) {
        $('td', row).eq(0).addClass('text-center');
    }
});

function localDate(tanggal) {
    // Memisahkan tanggal dan waktu
    let parts = tanggal.split(' ');
    let datePart = parts[0];

    let dateComponents = datePart.split('-');
    let year = dateComponents[0];
    let month = dateComponents[1];
    let day = dateComponents[2];

    let formattedDate = day + '/' + month + '/' + year;
    return formattedDate;
}

function localDateTime(tanggal) {
    // Memisahkan tanggal dan waktu
    let parts = tanggal.split(' ');
    let datePart = parts[0];

    let dateComponents = datePart.split('-');
    let year = dateComponents[0];
    let month = dateComponents[1];
    let day = dateComponents[2];

    let formattedDate = day + '/' + month + '/' + year + ' ' + parts[1];
    return formattedDate;
}

function textChanges(jenisPekerjaan) {
    if (jenisPekerjaan == 1) {
        $("#jenis-pekerjaan").text("Pekerjaan Notaris");
    }
}

function formatRupiah(angka) {
    var isNegative = angka < 0;
    var strAngka = Math.abs(angka).toString().split("").reverse().join("");

    let ribuan = strAngka.match(/\d{1,3}/g);
    if (!ribuan) return "0";
    let formatted = ribuan.join(".").split("").reverse().join("");
    if (isNegative) {
        formatted = "-" + formatted;
    }

    return formatted;
}

$('select[target="filter_jenis_pekerjaan_id"]').select2AjaxNew({
    url: baseUrl('/penghasilan/select2/getjenispekerjaan'),
    method: "POST",
});

$('select[target="filter_kategori_pekerjaan_id"]').select2AjaxNew({
    url: baseUrl('/penghasilan/select2/getkategoripekerjaan'),
    method: "POST",
});

$('select[target="filter_status_id"]').select2AjaxNew({
    url: baseUrl('/penghasilan/select2/getstatus'),
    method: "POST",
});

$('select[target="filter_petugas_id"]').select2AjaxNew({
    url: baseUrl('/penghasilan/select2/getpetugas'),
    method: "POST",
});

$('#filter_jenis_pekerjaan_id').on('change', function () {
    $.LoadingOverlay("show");

    table.ajax.reload();

    $('select[target="filter_pekerjaan_id"]').select2AjaxNew({
        url: baseUrl('/penghasilan/select2/getpekerjaan'),
        method: "POST",
        data: {
            jenis_pekerjaan: $("#filter_jenis_pekerjaan_id").val()
        }
    });

    $.LoadingOverlay("hide");
})

$('#filter_pekerjaan_id,#filter_jenis_pekerjaan_id,#filter_kategori_pekerjaan_id,#filter_status_id,#filter_petugas_id,#tanggal_awal,#tanggal_akhir')
    .on('change', function () {
        $.LoadingOverlay("show");
        table.ajax.reload();
        $.LoadingOverlay("hide");
    })


table.on('draw search.dt', function () {
    let totalJumlahDibayar = 0;
    let data = table.rows({ search: 'applied' }).data();

    // Iterasi melalui data dan jumlahkan jumlah_dibayar
    for (let i = 0; i < data.length; i++) {
        let nominal = data[i].jumlah_dibayar
            .replace('Rp', '')
            .replaceAll('.', '');

        totalJumlahDibayar += parseInt(nominal) || 0;
    }

    $('#total_pembayaran').text(`Rp. ${formatRupiah(totalJumlahDibayar)}`);
});

function formatRupiah(angka) {
    var strAngka = Math.abs(angka).toString().split("").reverse().join("");

    let ribuan = strAngka.match(/\d{1,3}/g);
    if (!ribuan) return "0";
    let formatted = ribuan.join(".").split("").reverse().join("");

    return formatted;
}
