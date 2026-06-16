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
        {
            data: "id",
            render: function (data, i, row) {
                let div = document.createElement("div");
                div.className = "row-action-detail-proses";

                let p = document.createElement("a");
                p.className = "action-detail-proses";
                p.innerHTML = `${row.proses_nama}`;
                div.append(p);

                return div.outerHTML;
            },
        },
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

        $(".action-detail-proses", row).click(function (e) {
            e.preventDefault();
            textChanges(data.jenis_pekerjaan_id);


            jenis_pekerjaan = data.jenis_pekerjaan_id;

            if (data.jenis_pekerjaan_id == 1) {
                $('[name="detail_proses_pekerjaan"]').val(data.pekerjaan_nama)
                $('[name="detail_proses_kategori"]').val(data.kategori_nama)
                transaksi_id = data.id

                table_proses_notaris.ajax.reload();
                $("#modal-detail-notaris").modal("show");
            } else {
                let modal = $(".modal-list-ppat");

                no_transaksi = data.no_akta;

                if (modal.data('current-transaksi') !== no_transaksi) {
                    table_list_ppat.one('draw', function () {
                        modal.data('current-transaksi', no_transaksi);
                        modal.modal("show");
                    });
                    table_list_ppat.ajax.reload();
                } else {
                    modal.modal("show");
                }
            }
        });
    }
});

let table_list_ppat = $(".table-list-ppat").DataTable({
    ajax: {
        url: baseUrl("/monitoring/list-ppat"),
        headers: {
            'X-XSRF-TOKEN': getCookie('XSRF-TOKEN')
        },
        dataSrc: "data",
        type: "POST",
        data: function (form) {
            form.transaksi_id = transaksi_id;
            form.no_transaksi = no_transaksi;
        }
    },
    autoWidth: false,
    responsive: false,
    ordering: false,
    searching: false,
    info: false,
    paging: false,
    columns: [
        {
            data: null,
            render: function (data, type, row, meta) {
                return meta.row + 1;
            },
            width: "20px"
        },
        {
            data: "pekerjaan_nama",
            render: function (data) {
                return data || '-';
            }
        },
        {
            data: "kategori",
            render: function (data) {
                return data || '-';
            }
        },
        {
            data: "estimasi_waktu",
            render: function (data) {
                return data ?? "";
            }
        },
        {
            data: "biaya_layanan",
            render: function (data) {
                return data ? 'Rp ' + parseInt(data).toLocaleString('id-ID') : 'Rp 0';
            }
        },
        {
            data: "biaya_lainnya",
            render: function (data) {
                return data ? 'Rp ' + parseInt(data).toLocaleString('id-ID') : 'Rp 0';
            }
        },
        {
            data: "id",
            render: function (data, type, row, meta) {
                var div = document.createElement("div");
                div.className = "text-center";

                var btn = document.createElement("button");
                btn.className = "btn btn-primary w-100 mb-2 kalkulator-pajak";
                btn.setAttribute("data-id", data);
                btn.innerHTML = '<i class="fas fa-calculator"></i> Kalkulator Pajak';
                div.appendChild(btn);

                var btn = document.createElement("button");
                btn.className = "btn btn-success w-100 daftar-proses-ppat";
                btn.innerHTML = '<i class="fas fa-list"></i> Daftar Proses';
                div.appendChild(btn);

                return div.outerHTML;
            }
        }
    ],
    createdRow: function (row, data) {
        $(row).find(".kalkulator-pajak").click(function (e) {
            e.preventDefault();
            let code = data.id;

            $.httpRequest({
                url: baseUrl(`/monitoring/getpajak/${code}`),
                method: "POST",
                response: function (res) {
                    if (!res.data) {
                        swal("Terjadi kesalahan!", "data pajak tidak ada", "error");
                        $("modal-detail-proses").modal("hide");
                        return;
                    }

                    $(".modal-list-ppat").modal("hide");
                    $("#modal-detail-pajak").modal("show");

                    if (res.statusCode == 200) {
                        let result = res.data;

                        if (!result.jenis_pajak_id) {
                            swal("Data pajak tidak di temukan");
                            return;
                        }

                        $("#jenis_pajak").val(result.nama_pajak);
                        $("#njop").val(formatRupiah(result.acuan_hitung_pajak));
                        $("#n").val(formatRupiah(result.nilai_pengurang));

                        if (result.jenis_pajak_id == 1) {
                            $("#label-pihak-pertama").text("Penerima");
                            $("#pihak-pertama").text("Rp. " + formatRupiah(result.besaran_pajak_pertama));

                            $("#label-pihak-kedua").text("Pewaris");
                            $("#pihak-kedua").text("Rp. " + formatRupiah(result.besaran_pajak_pihak_kedua));
                        } else if (result.jenis_pajak_id == 2) {
                            $("#label-pihak-pertama").text("Penjual");
                            $("#pihak-pertama").text("Rp. " + formatRupiah(result.besaran_pajak_pertama));

                            $("#label-pihak-kedua").text("Pembeli");
                            $("#pihak-kedua").text("Rp. " + formatRupiah(result.besaran_pajak_pihak_kedua));
                        }

                        $("#table-result").removeClass("d-none");
                    } else {
                        swal("Peringatan!", "Gagal mendapatkan data pajak", "error");
                    }
                }
            });
        });

        $(row).find(".daftar-proses-ppat").click(function (e) {
            e.preventDefault();

            transaksi_id = data.id;
            table_proses_ppat.ajax.reload();
            $(".modal-detail-ppat").modal("show");
            $(".modal-list-ppat").modal("hide");
        });
    }
});

let table_proses_notaris = $(".table-proses-notaris").DataTable({
    ajax: {
        url: baseUrl("/monitoring/table-notaris"),
        headers: {
            'X-XSRF-TOKEN': getCookie('XSRF-TOKEN')
        },
        dataSrc: "data",
        type: "POST",
        data: function (form) {
            form.jenis_pekerjaan = jenis_pekerjaan;
            form.transaksi_id = transaksi_id;
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
            data: "isvalidate",
            render: function (data, i, row, meta) {
                var div = document.createElement("div");
                div.className = "text-center";

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
            e.preventDefault();
            $.LoadingOverlay("show");

            $.httpRequest({
                url: baseUrl(`/monitoring/getdetailcatatan`),
                method: "POST",
                data: JSON.stringify({
                    'proses_id': data.proses_id,
                    'transaksi_id': transaksi_id,
                    'jenis_pekerjaan_id': jenis_pekerjaan
                }),
                contentType: "application/json",
                response: (res) => {
                    $.LoadingOverlay("hide");
                    if (res.statusCode == 200) {
                        let data = res.data;

                        // Reset container terlebih dahulu
                        $('#attribute-list-container').remove();
                        $('#modal-detail-catatan .modal-body').append('<div id="attribute-list-container" class="mt-3"></div>');

                        if (Array.isArray(data.proses.atribut) && data.proses.atribut.length > 0) {
                            let tableHTML = `
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="text-center" width="50">No</th>
                                                <th>List Data</th>
                                                <th class="text-center" width="100">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;

                            data.proses.atribut.forEach((attr, index) => {
                                tableHTML += `
                                    <tr>
                                        <td class="text-center">${index + 1}</td>
                                        <td>${attr.nama || '-'}</td>
                                        <td class="text-center">
                                            ${attr.status ?
                                        '<i class="fas fa-check text-success"></i>' :
                                        '<i class="fas fa-times text-danger"></i>'
                                    }
                                        </td>
                                    </tr>
                                `;
                            });

                            tableHTML += `
                                        </tbody>
                                    </table>
                                </div>
                            `;

                            $('#attribute-list-container').html(tableHTML);
                        } else {
                            $('#attribute-list-container').html('<div class="alert alert-info">Tidak ada atribut yang tersedia</div>');
                        }

                        $('#modal-detail-proses').modal('hide');
                        $('[name="detail_proses_nama"]').val(data.proses.nama);
                        $('[name="detail_proses_waktu_pengerjaan"]').val(data.waktu_pengerjaan);
                        $('#detail_proses').text(data.proses.pekerjaanNama);
                        $('[name="detail_proses_catataan"]').val(data.proses.catatan);
                        $('#modal-detail-catatan').modal('show');
                    } else {
                        swal("Peringatan !", res.message, "error");
                    }
                },
                error: function (xhr, status, error) {
                    $.LoadingOverlay("hide");
                    swal("Error !", "Terjadi kesalahan saat memproses data", "error");
                }
            });
        })
    }
});

let table_proses_ppat = $(".table-proses-ppat").DataTable({
    ajax: {
        url: baseUrl("/monitoring/table-ppat"),
        headers: {
            'X-XSRF-TOKEN': getCookie('XSRF-TOKEN')
        },
        dataSrc: "data",
        type: "POST",
        data: function (form) {
            form.jenis_pekerjaan = jenis_pekerjaan;
            form.transaksi_id = transaksi_id;
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
            data: "isvalidate",
            render: function (data, i, row, meta) {
                var div = document.createElement("div");
                div.className = "text-center";

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
            e.preventDefault();
            $.LoadingOverlay("show");

            $.httpRequest({
                url: baseUrl(`/monitoring/getdetailcatatanppat`),
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

                        // Reset container terlebih dahulu
                        $('#attribute-list-container').remove();
                        $('#modal-detail-catatan .modal-body').append('<div id="attribute-list-container" class="mt-3"></div>');

                        if (Array.isArray(data.proses.atribut) && data.proses.atribut.length > 0) {
                            let tableHTML = `
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="text-center" width="50">No</th>
                                                <th>List Data</th>
                                                <th class="text-center" width="100">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;

                            data.proses.atribut.forEach((attr, index) => {
                                tableHTML += `
                                    <tr>
                                        <td class="text-center">${index + 1}</td>
                                        <td>${attr.nama || '-'}</td>
                                        <td class="text-center">
                                            ${attr.status ?
                                        '<i class="fas fa-check text-success"></i>' :
                                        '<i class="fas fa-times text-danger"></i>'
                                    }
                                        </td>
                                    </tr>
                                `;
                            });

                            tableHTML += `
                                        </tbody>
                                    </table>
                                </div>
                            `;

                            $('#attribute-list-container').html(tableHTML);
                        } else {
                            $('#attribute-list-container').html('<div class="alert alert-info">Tidak ada atribut yang tersedia</div>');
                        }

                        $('#modal-detail-proses').modal('hide');
                        $('[name="detail_proses_nama"]').val(data.proses.nama);
                        $('[name="detail_proses_waktu_pengerjaan"]').val(data.waktu_pengerjaan);
                        $('#detail_proses').text(data.proses.pekerjaanNama);
                        $('[name="detail_proses_catataan"]').val(data.proses.catatan);
                        $('#modal-detail-catatan').modal('show');
                    } else {
                        swal("Peringatan !", res.message, "error");
                    }
                },
                error: function (xhr, status, error) {
                    $.LoadingOverlay("hide");
                    swal("Error !", "Terjadi kesalahan saat memproses data", "error");
                }
            });
        })
    }
});

$("#modal-list-ppat").on('hidden.bs.modal', function () {
    $(this).removeData('current-transaksi');
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

$('#filter_pekerjaan_id,#filter_kategori_pekerjaan_id,#filter_status_id,#filter_petugas_id,#tanggal_awal,#tanggal_akhir')
    .on('change', function () {
        $.LoadingOverlay("show");
        table.ajax.reload();
        $.LoadingOverlay("hide");
    });

$("#export-to-excel").click(function () {
    // Ambil data dari DataTable
    let tableData = $(".table-monitoring")
        .DataTable()
        .rows()
        .data()
        .toArray();

    // Buat array untuk menyimpan data yang akan diekspor
    let exportData = [];

    // Tambahkan header
    exportData.push([
        "No",
        "Tanggal Daftar",
        "Tanggal Selesai",
        "No Akta",
        "NIK Pemohon",
        "Nama Pemohon",
        "Jenis Pekerjaan",
        "Nama Pekerjaan",
        "Kategori",
        "Estimasi Waktu",
        "Proses",
        "Total",
        "Total Piutang",
        "Nama Petugas",
        "Status"
    ]);

    // Tambahkan data ke array
    tableData.forEach((row, index) => {
        exportData.push([
            index + 1,
            localDate(row.tanggal_daftar),
            localDate(row.tanggal_selesai),
            row.no_akta,
            row.pemohon_nik,
            row.pemohon_nama,
            row.jenis_pekerjaan_nama,
            row.pekerjaan_nama,
            row.kategori_nama,
            row.estimasi_waktu,
            row.proses_nama,
            row.total,
            row.total_piutang,
            row.petugas_nama,
            row.status_nama
        ]);
    });

    // Buat worksheet dari data
    let ws = XLSX.utils.aoa_to_sheet(exportData);

    // Buat workbook dan tambahkan worksheet
    let wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Monitoring Data");

    // Simpan file Excel
    XLSX.writeFile(wb, "Monitoring_Data.xlsx");
});
