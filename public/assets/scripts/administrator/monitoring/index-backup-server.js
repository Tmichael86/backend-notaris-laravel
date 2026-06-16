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
let table = $(".table-monitoring").DataTable({
    ajax: {
        url: baseUrl("/monitoring-fetch"),
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
    autoWidth: false,
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
            data: "tanggal_daftar",
            render: function (data, i, row) {
                return localDate(data);
            }
        },
        {
            data: "tanggal_selesai",
            render: function (data, i, row) {
                return localDate(data);
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
            data: "estimasi_waktu",
            render: function (data, i, row) {
                return `<div style="width:150px">${data}</div>`;
            }
        },
        // {
        //     data: "id",
        //     render: function (data, i, row) {
        //         // ==> Container
        //         let div = document.createElement("div");
        //         div.className = "row-action-detail-proses";

        //         // ==> Button Edit
        //         let p = document.createElement("a");
        //         p.className = "action-detail-proses";
        //         p.innerHTML = `${row.proses_nama}`;
        //         div.append(p);


        //         return div.outerHTML;
        //     },
        // },
        {
            data: "total"
        },
        {
            data: "total_piutang"
        },
        {
            data: "petugas_nama"
        },
        {
            data: "status_nama"
        }
    ],
    createdRow: function (row, data) {
        $('td', row).eq(0).addClass('text-center');

        if (data.bg == '1') {
            row.classList.add("bg-danger");
            row.classList.add("text-white");
        }

        // ==> detail table proses
        $(".action-detail-proses", row).click(function (e) {
            e.preventDefault();

            $('[name="detail_proses_pekerjaan"]').val(data.pekerjaan_nama)
            $('[name="detail_proses_kategori"]').val(data.kategori_nama)

            transaksi_id = data.id
            table_proses.ajax.reload();

            $("#modal-detail-proses").modal("show");
        });
    },
});


// -- disabled table
let table_proses = $(".table-proses-none").DataTable({
    ajax: {
        url: baseUrl("/monitoring/gettableproses"),
        headers: {
            'X-XSRF-TOKEN': getCookie('XSRF-TOKEN')
        },
        dataSrc: "data",
        type: "POST",
        data: function (form) {
            form.transaksi_id = transaksi_id
        }
    },
    autoWidth: false,
    responsive: true,
    ordering: false,
    searching: false,
    info: false,
    sorting: false,
    paging: false,
    columns: [
        {
            name: "",
            render: function (data, i, row, meta) {
                return meta.row + 1;
            },
            width: "20px",
        },
        {
            data: "proses_nama",
        },
        {
            data: "status_proses",
            render: function (data, i, row, meta) {
                var div = document.createElement("div");
                div.className = "text-center";

                // ==> Button Edit
                var p = document.createElement("p");
                if (data == 1) {
                    p.innerHTML = '<i class="fas fa-check text-success fa-2x"></i>';
                } else {
                    p.innerHTML = '<i class="fas fa-times text-danger fa-2x"></i>';
                }
                div.append(p);

                return div.outerHTML;
            }
        },
        {
            data: "created_at",
            render: function (data, i, row) {
                return new Date(data).toLocaleString();
            }
        },
        {
            data: "petugas_nama"

        }
    ],
    createdRow: function (row, data) {
        $(row).click(function (e) {
            e.preventDefault()
            $.LoadingOverlay("show");
            $.httpRequest({
                url: baseUrl(`/monitoring/getdetailcatatan`),
                method: "POST",
                data: JSON.stringify({
                    'proses_id': data.proses_id,
                    'transaksi_id': transaksi_id
                }),
                contentType: "application/json",
                response: (res) => {
                    $.LoadingOverlay("hide");
                    if (res.statusCode == 200) {
                        let data = res.data;
                        $('#modal-detail-proses').modal('hide')
                        $('[name="detail_proses_nama"]').val(data.proses.nama)
                        $('[name="detail_proses_waktu_pengerjaan"]').val(localDateTime(data.waktu_pengerjaan))
                        $('#detail_proses').text(data.proses.detail)
                        $('[name="detail_proses_catataan"]').val(data.catatan)
                        $('#modal-detail-catatan').modal('show')
                    } else {
                        swal("Peringatan !", res.message, "error");
                    }
                },
            });

        })
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

$('select[target="filter_jenis_pekerjaan_id"]').select2AjaxNew({
    url: baseUrl('/monitoring/select2/getjenispekerjaan'),
    method: "POST",
});

$('select[target="filter_kategori_pekerjaan_id"]').select2AjaxNew({
    url: baseUrl('/monitoring/select2/getkategoripekerjaan'),
    method: "POST",
});

$('select[target="filter_status_id"]').select2AjaxNew({
    url: baseUrl('/monitoring/select2/getstatus'),
    method: "POST",
});

$('select[target="filter_petugas_id"]').select2AjaxNew({
    url: baseUrl('/monitoring/select2/getpetugas'),
    method: "POST",
});

$('#filter_jenis_pekerjaan_id').on('change', function () {
    $.LoadingOverlay("show");

    table.ajax.reload();

    $('select[target="filter_pekerjaan_id"]').select2AjaxNew({
        url: baseUrl('/monitoring/select2/getpekerjaan'),
        method: "POST",
        data: {
            jenis_pekerjaan: $("#filter_jenis_pekerjaan_id").val()
        }
    });

    $.LoadingOverlay("hide");
})

$('#filter_pekerjaan_id').on('change', function () {
    $.LoadingOverlay("show");
    table.ajax.reload();
    $.LoadingOverlay("hide");
})

$('#filter_kategori_pekerjaan_id').on('change', function () {
    $.LoadingOverlay("show");
    table.ajax.reload();
    $.LoadingOverlay("hide");
})

$('#filter_status_id').on('change', function () {
    $.LoadingOverlay("show");
    table.ajax.reload();
    $.LoadingOverlay("hide");
})

$('#filter_petugas_id').on('change', function () {
    $.LoadingOverlay("show");
    table.ajax.reload();
    $.LoadingOverlay("hide");
})
