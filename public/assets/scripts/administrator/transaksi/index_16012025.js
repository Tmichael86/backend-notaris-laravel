let sesiSaatIni = 1;
let currentPPATPekerjaanId = null;
let currentPPATKategoriId = null;
let total_biaya_layanan = null;
let currentJenisPekerjaan = null;
let calculatedTaxValue = 0;
let pajakListData = [];
let riwayatPembayaranData = [];

// => Fungsi Penting Untuk Mengatur Notaris Dan
function setTransactionCode(jenisId) {
    // Ambil sesi pekerjaan (Notaris atau PPAT)
    $.httpRequest({
        url: baseUrl(`/transaksi/jenisPekerjaan/session/${jenisId}`),
        method: "POST",
        data: "",
        response: function (response) {
            res = response.data;
            sesiSaatIni = res;

            formTransaksiReset();
            if (res == 1) {
                // Jika sesi  (Notaris)
                $(".notaris-container").removeClass("d-none");
                $(".ppat-container").addClass("d-none");
                $("#radioBtnStatus").parent()
                    .parent()
                    .removeClass("d-none");

                $(".statusBtn").on("click", function () {
                    let dataValue = $(this).attr("data-title");

                    if (dataValue == 3 && res == 1) {
                        $("#additional-form-notaris").removeClass("d-none");
                    } else {
                        $("#additional-form-notaris").addClass("d-none");
                    }
                });
            } else if (res == 2) {
                // Jika sesi 2 (PPAT)
                tablePPATTransaksi.clear().draw(true);
                $(".ppat-container").removeClass("d-none");
                $(".notaris-container").addClass("d-none");
                $("#radioBtnStatus").parent()
                    .parent()
                    .addClass("d-none");

                let lengthTablePPAT = tablePPATTransaksi.rows().count();
                if (lengthTablePPAT < 1) {
                    $(".btn-add-transaksi-ppat").click();
                }
            }

            // Tambahkan logika pencetakan dokumen berdasarkan sesi
            $("#cetakDokumen").click(function (e) {
                e.preventDefault(); // Mencegah default action dari link

                var transaksiId = $("#transaksi_id").val();
                var no_akta = $("#no_akta_notaris").val();

                if (!transaksiId) {
                    swal(
                        "Peringatan!",
                        "Anda Harus Membuat Transaksi Atau Memilih No Akta Terlebih Dahulu",
                        "error"
                    );
                } else {
                    // Tentukan URL cetak berdasarkan sesi
                    var cetakUrl;
                    if (sesiSaatIni == 1) {
                        cetakUrl = baseUrl(
                            "/transaksi/cetakDokumenNotaris/" + transaksiId
                        );
                    } else if (sesiSaatIni == 2) {
                        cetakUrl = baseUrl(
                            "/transaksi/cetakDokumenPPAT/" + no_akta
                        );
                    } else {
                        swal("Peringatan!", "Sesi tidak valid.", "error");
                        return;
                    }

                    window.open(cetakUrl); // Buka jendela cetak dengan URL yang sesuai
                }
            });
        },
    });

    // Ambil kode no akta
    $.httpRequest({
        url: baseUrl(`/transaksi/noakta-code/${jenisId}`),
        method: "GET",
        response: (res) => {
            if (res.statusCode == 200) {
                $("#no_akta_notaris").val(res.transaction_code);
            } else {
                swal("Peringatan !", response.message, "success");
            }
        },
    });
}

function renderHTML(html) {
    let div = document.createElement('div');
    div.innerHTML = html;
    return div.textContent || div.innerText || "";
}

// => Define Pekerjaan ID Value
let pekerjaan_id = $("#pekerjaan_id").val();

// => Delete Cookie On Load Page
window.onload = function () {
    function deleteCookie(name) {
        document.cookie =
            name + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
    }

    const cookiesToDelete = [
        "transaksi_detail_proses_validasi",
        "transaksi_catatan_proses",
        "transaksi_detail_list_validasi",
    ];

    cookiesToDelete.forEach(function (cookieName) {
        deleteCookie(cookieName);
    });
};

$(".datepicker").datepicker();

$(document).ready(function () {
    $("#radioBtn a.active").click();
    $("#radioBtnStatus a.active").click();

    // => Clear Table PPAT
    tablePPATTransaksi.clear().draw(true);
});
// // => Disable Error Warning
$.fn.dataTable.ext.errMode = "none";

let tablePPATTransaksi = $(".table-ppat").DataTable({
    autoWidth: false,
    responsive: true,
    ordering: false,
    searching: false,
    info: false,
    sorting: false,
    paging: false,
});

let tableTransaksi = $("#table-data-transaksi").DataTable({
    ajax: {
        url: baseUrl("/transaksi-fetch"),
        headers: {
            "X-XSRF-TOKEN": getCookie("XSRF-TOKEN"),
        },
        type: "POST",
        data: (e) => {
            const jenis = $('#jenis_pekerjaan').val();
            e.jenis_pekerjaan = jenis == '' ? 1 : jenis;
        }
    },
    processing: true,
    serverSide: true,
    paging: true,
    lengthChange: true,
    searching: true,
    ordering: true,
    order: [],
    info: true,
    autoWidth: false,
    columns: [{
        name: "",
        render: function (data, i, row, meta) {
            return meta.row + 1;
        },
        width: "20px",
    },
    {
        data: "no_akta",
    },
    {
        data: "pekerjan_nama",
    },
    {
        data: "pemohon_nama",
    },
    {
        data: "no_telp",
    },
    {
        data: "id",
        render: function (data, i, row) {
            // ==> Container
            let div = document.createElement("div");
            div.className = "d-flex row-action";

            // ==> Button Pilih
            let btn = document.createElement("button");
            btn.className =
                "btn btn-success btn-action btn-sm action-pilih";
            btn.innerHTML = '<i class="fa fa-edit mr-1"></i> Edit';
            div.append(btn);

            return div.outerHTML;
        },
    },
    ],
    createdRow: function (row, data) {
        let jenis_pekerjaan = data.jenis_pekerjaan_id;

        $(".action-pilih", row).on("click", function (e) {
            e.preventDefault();

            // -- in here set update access
            let saveButton = $('#simpan_transaksi_baru').removeClass('d-none');
            if (access.update != 1) saveButton.addClass('d-none');

            // => Form Edit Notaris
            if (jenis_pekerjaan == 1 && sesiSaatIni == 1) {
                $.httpRequest({
                    url: baseUrl("/transaksi/fetchNotaris/" + data.id),
                    method: "POST",
                    contentType: "application/json",
                    response: function (res) {
                        $.LoadingOverlay("show");
                        $("#modal-no-akta").modal("hide");

                        pekerjaan_id = res.data.pekerjaan_id;

                        $("#transaksi_id").val(res.data.id);
                        $("#no_akta_notaris").val(res.data.no_akta);
                        $("#pemohon_id").val(res.data.pemohon_id);
                        $("#pemohon_nik").text(": " + res.data.pemohon_nik);
                        $("#pemohon_nama").text(": " + res.data.pemohon_nama);
                        $("#pemohon_jenis_kelamin").text(
                            ": " + res.data.jenis_kelamin_pemohon
                        );
                        $("#pemohon_no_telp").text(
                            ": " + res.data.pemohon_notelp
                        );
                        $("#pemohon_alamat").val(res.data.pemohon_alamat);
                        $("#jumlah_materai").val(res.data.materai_keluar);

                        $('input[name="tanggal_daftar"]').val(
                            localDate(data.tanggal_daftar)
                        );
                        $('input[name="tanggal_selesai"]').val(
                            localDate(data.tanggal_selesai)
                        );

                        let sel = res.data.jenis_pekerjaan_id;
                        $("#jenis_pekerjaan").val(sel);
                        $('a[data-toggle="jenis_pekerjaan"]')
                            .not('[data-title="' + sel + '"]')
                            .removeClass("active")
                            .addClass("notActive");

                        $('a[data-toggle="jenis_pekerjaan"][data-title="' + sel + '"]')
                            .removeClass("notActive")
                            .addClass("active");

                        $('a[data-toggle="jenis_pekerjaan"]').on("click", function () {
                            formTransaksiReset();
                        });

                        $.httpRequest({
                            url: baseUrl(`/transaksi/getdetailkategori`),
                            method: "POST",
                            data: JSON.stringify({
                                jenis_pekerjaan: res.data.jenis_pekerjaan_id,
                                pekerjaan_id: res.data.pekerjaan_id,
                                kategori_pekerjaan_id: res.data.kategori_pekerjaan_id,
                            }),
                            contentType: "application/json",
                            response: function (res) {
                                let response = res;
                                if (response.statusCode === 200) {
                                    $('[name="estimasi_waktu"]').val(
                                        response.data.estimasi_waktu
                                    );
                                }
                            },
                        });

                        $("#biaya_layanan").val(formatRupiah(res.data.biaya_layanan));
                        $("#biaya_lainnya").val(formatRupiah(res.data.biaya_lainnya));

                        $(`#radioBtnStatus a[data-title="${res.data.status_id}"]`).click();

                        $("#petugas_id").val(res.data.petugas_id);
                        $("#petugas_nama").text(res.data.petugas_nama);
                        $("#petugas_jenis_kelamin").text(
                            res.data.jenis_kelamin_petugas
                        );
                        $("#petugas_no_telp").text(res.data.petugas_notelp);

                        $('select[name="pekerjaan_id"]').select2AjaxNew({
                            url: baseUrl("/transaksi/getpekerjaan"),
                            method: "POST",
                            data: {
                                selected: res.data.pekerjaan_id,
                                jenis_pekerjaan: res.data.jenis_pekerjaan_id,
                            },
                        });

                        $('select[name="kategori_pekerjaan_id"]').select2AjaxNew({
                            url: baseUrl("/transaksi/getkategori"),
                            method: "POST",
                            data: {
                                selected: res.data.kategori_pekerjaan_id,
                                jenis_pekerjaan: res.data.jenis_pekerjaan_id,
                                pekerjaan_id: res.data.pekerjaan_id,
                            },
                        });

                        $("#jenis_pembayaran_id")
                            .val(res.data.jenis_pembayaran_id)
                            .change();
                        $("#jatuh_tempo").val(localDate(res.data.jatuh_tempo));

                        $("#potongan_harga").val(
                            formatRupiah(res.data.potongan_biaya)
                        );
                        let jumlah_dibayar = res.data.total_dibayar;
                        let total_sum_dibayar = jumlah_dibayar.reduce((a, b) => a + b, 0);
                        $("#jumlah_pembayaran").val(total_sum_dibayar);

                        $("#judul").val(res.data.judul);
                        $("#nomor_akta").val(res.data.nomor_akta);
                        $("#tanggal_akta").val(res.data.tanggal_akta);

                        $("#pembayaran_sekarang").val(0);
                        $("#keterangan").val(res.data.keterangan);
                        $('#biaya_lainya').val(formatRupiah(res.data.biaya_lainnya));

                        subTotalNotaris();

                        table.ajax.reload();
                        $.LoadingOverlay("hide");
                    },
                });
            } else if (jenis_pekerjaan == 2 && sesiSaatIni == 2) {
                // => Form Edit PPAT
                let no_akta = data.no_akta;

                $.httpRequest({
                    url: baseUrl("/transaksi/fetchPPAT/" + no_akta),
                    method: "POST",
                    data: {
                        id: data.id,
                        no_akta: data.no_akta,
                    },
                    response: async function (res) {

                        let result = res.data[0];
                        let datas = res.data;

                        $.LoadingOverlay("show");
                        $("#modal-no-akta").modal("hide");

                        $("#transaksi_id").val(result.id);
                        $("#no_akta_notaris").val(result.no_akta);
                        $("#pemohon_id").val(result.pemohon_id);
                        $("#pemohon_nik").text(": " + result.pemohon_nik);
                        $("#pemohon_nama").text(": " + result.pemohon_nama);
                        $("#pemohon_jenis_kelamin").text(
                            ": " + result.jenis_kelamin_pemohon
                        );
                        $("#pemohon_no_telp").text(
                            ": " + result.pemohon_notelp
                        );
                        $("#pemohon_alamat").val(result.pemohon_alamat);
                        $("#jumlah_materai").val(result.materai_keluar);
                        $("#keterangan").val(result.keterangan);

                        $('input[name="tanggal_daftar"]').val(
                            localDate(result.tanggal_daftar)
                        );
                        $('input[name="tanggal_selesai"]').val(
                            localDate(result.tanggal_selesai)
                        );

                        var sel = result.jenis_pekerjaan_id;
                        $("#jenis_pekerjaan").val(sel);
                        $('a[data-toggle="jenis_pekerjaan"]')
                            .not('[data-title="' + sel + '"]')
                            .removeClass("active")
                            .addClass("notActive");
                        $('a[data-toggle="jenis_pekerjaan"][data-title="' + sel + '"]')
                            .removeClass("notActive")
                            .addClass("active");

                        $('a[data-toggle="jenis_pekerjaan"]').on(
                            "click",
                            function () {
                                formTransaksiReset();
                            }
                        );

                        $("#petugas_id").val(result.petugas_id);
                        $("#petugas_nama").text(result.petugas_nama);
                        $("#petugas_jenis_kelamin").text(result.jenis_kelamin_petugas);
                        $("#petugas_no_telp").text(result.petugas_notelp);

                        tablePPATTransaksi.clear().draw(true);

                        let subTotalBiaya = 0;
                        let totalBiaya = 0;
                        let potonganBiaya = 0;

                        $.each(datas, function (index, val) {
                            if (val.sub_total) {
                                subTotalBiaya += parseFloat(val.sub_total);
                            }

                            if (val.total) {
                                totalBiaya += parseFloat(val.total);
                            }

                            if (val.potongan_biaya) {
                                potonganBiaya += parseFloat(val.potongan_biaya);
                            }

                            currentPPATPekerjaanId = val.pekerjaan_id;
                            currentPPATKategoriId = val.kategori_pekerjaan_id;

                            let rowIndex = tablePPATTransaksi.rows().count();

                            let row = tablePPATTransaksi.row
                                .add([
                                    $("#clone_aksi_transaksi").html(),
                                    $("#clone_pekerjaan_ppat_id").sl2HTML({
                                        target: "pekerjaan_ppat_id_" + rowIndex,
                                    }),
                                    $("#clone_kategori_pekerjaan_id").sl2HTML({
                                        target: "kategori_pekerjaan_ppat_id_" + rowIndex,
                                    }),
                                    $("#clone_estimasi_waktu").html(),
                                    $("#clone_biaya_layanan").html(),
                                    $("#clone_biaya_lainya").html(),
                                    $("#clone_aksi").html(),
                                    $("#clone_perhitungan_pajak").html(),
                                ])
                                .draw(false)
                                .node();

                            $(row).attr("data-row-index", rowIndex);

                            $(row).find('input[name="transaksi_ppat_id[]"]').val(val.id);

                            $(row).find('input[target="estimasi_waktu"]')
                                .attr("target", "estimasi_waktu_" + rowIndex)
                                .val(val.estimasi_waktu);

                            $(row).find('input[target="biaya_layanan"]')
                                .attr("target", "biaya_layanan_" + rowIndex)
                                .val(formatRupiah(val.biaya_layanan));

                            $(row).find('input[target="biaya_lainnya"]')
                                .attr("target", "biaya_lainya_" + rowIndex)
                                .val(formatRupiah(val.biaya_lainnya));

                            $(row).find('select[target="jenis_pajak"]')
                                .attr("target", "jenis_pajak_" + rowIndex)
                                .val(val.jenis_pajak_id);

                            $(row).find('input[name="status_ppat[]"]').val(val.status_id);
                            $(row).find('input[name="judul_ppat[]"]').val(val.judul);
                            $(row).find('input[name="no_akta_ppat[]"]').val(val.nomor_akta);
                            $(row).find('input[name="tgl_akta_ppat[]"]').val(val.tanggal_akta);

                            let pekerjaanSelect = $(row).find('select[target="pekerjaan_ppat_id_' + rowIndex + '"]');

                            pekerjaanSelect.select2AjaxNew({
                                url: baseUrl("/transaksi/getpekerjaan"),
                                method: "POST",
                                data: {
                                    selected: val.pekerjaan_id,
                                    jenis_pekerjaan: $("#jenis_pekerjaan").val(),
                                },
                                success: function () {
                                    pekerjaanSelect
                                        .val(val.pekerjaan_id)
                                        .trigger("change");
                                },
                            });

                            pekerjaanSelect.off("change").on("change", function () {
                                $.LoadingOverlay("show");

                                let currentRow = $(this).closest("tr");

                                currentRow.find('input[target="estimasi_waktu_' + rowIndex + '"]').val("");
                                currentRow.find('input[target="biaya_layanan_' + rowIndex + '"]').val("");
                                currentRow.find('input[target="biaya_lainya_' + rowIndex + '"]').val("");

                                let kategoriSelect = currentRow.find(
                                    'select[target="kategori_pekerjaan_ppat_id_' +
                                    rowIndex +
                                    '"]'
                                );
                                kategoriSelect.val(null).trigger("change");

                                let pekerjaan_id = $(this).val();

                                kategoriSelect.select2AjaxNew({
                                    url: baseUrl("/transaksi/getkategori"),
                                    method: "POST",
                                    data: {
                                        jenis_pekerjaan: $("#jenis_pekerjaan").val(),
                                        pekerjaan_id: pekerjaan_id,
                                    },
                                });

                                $.LoadingOverlay("hide");
                            });

                            let kategoriSelect = $(row).find(
                                'select[target="kategori_pekerjaan_ppat_id_' +
                                rowIndex +
                                '"]'
                            );
                            kategoriSelect.select2({
                                placeholder: "-- Pilih Kategori --",
                            });

                            kategoriSelect.select2AjaxNew({
                                url: baseUrl("/transaksi/getkategori"),
                                method: "POST",
                                data: {
                                    selected: val.kategori_pekerjaan_id,
                                    jenis_pekerjaan: $("#jenis_pekerjaan").val(),
                                    pekerjaan_id: val.pekerjaan_id,
                                },
                                success: function () {
                                    kategoriSelect
                                        .val(val.kategori_pekerjaan_id)
                                        .trigger("change");
                                },
                            });

                            kategoriSelect
                                .off("change")
                                .on("change", function () {
                                    $.LoadingOverlay("show");

                                    let currentRow = $(this).closest("tr");
                                    let pekerjaan_id = currentRow
                                        .find(
                                            'select[target="pekerjaan_ppat_id_' +
                                            rowIndex +
                                            '"]'
                                        )
                                        .val();
                                    let kategori_pekerjaan_id = $(this).val();

                                    $.httpRequest({
                                        url: baseUrl(
                                            "/transaksi/getdetailkategori"
                                        ),
                                        method: "POST",
                                        data: JSON.stringify({
                                            jenis_pekerjaan: $("#jenis_pekerjaan").val(),
                                            pekerjaan_id: pekerjaan_id,
                                            kategori_pekerjaan_id: kategori_pekerjaan_id,
                                        }),
                                        contentType: "application/json",
                                        response: (res) => {
                                            if (res.statusCode == 200) {
                                                currentRow
                                                    .find(
                                                        'input[target="estimasi_waktu_' +
                                                        rowIndex +
                                                        '"]'
                                                    )
                                                    .val(
                                                        res.data.estimasi_waktu
                                                    );
                                                currentRow
                                                    .find(
                                                        'input[target="biaya_layanan_' +
                                                        rowIndex +
                                                        '"]'
                                                    )
                                                    .val(
                                                        formatRupiah(
                                                            res.data.harga
                                                        )
                                                    );
                                                subTotalPPAT();
                                            } else {
                                                swal(
                                                    "Peringatan !",
                                                    res.message,
                                                    "error"
                                                );
                                            }
                                        },
                                    });
                                    $.LoadingOverlay("hide");
                                });
                        });

                        // -- in here calc pajak
                        await new Promise((resolve) => {
                            $.httpRequest({
                                url: baseUrl("/transaksi/getPajak"),
                                method: "POST",
                                response: function (res) {
                                    // -- in here set pajak list
                                    let data = res.data;

                                    if (data[0].pihak_kedua) {
                                        $("#checked-skb").prop("checked", true);
                                    }
                                    pajakListData = data ?? [];
                                    resolve();
                                },
                            });
                        });

                        // => Mengkalkulasikan Sisa Pembayaran
                        await new Promise((resolve) => {
                            $.httpRequest({
                                url: baseUrl("/transaksi/riwayat-ppat"),
                                method: "POST",
                                data: JSON.stringify({
                                    no_transaksi: result.no_akta,
                                }),
                                contentType: "application/json",
                                response: function (res) {
                                    riwayatPembayaranData = res;

                                    let len = riwayatPembayaranData.length;
                                    let nominal = len === 0 ?
                                        0 :
                                        riwayatPembayaranData.reduce((a, b) => a + b.jumlah_dibayar, 0);
                                    $("#jumlah_pembayaran").val(nominal);

                                    resolve();
                                },
                            });
                        });


                        $("#transaksi_sub_total").text(
                            "Rp. " + formatRupiah(subTotalBiaya)
                        );
                        $("#transaksi_total").text(
                            "Rp. " + formatRupiah(totalBiaya)
                        );
                        $("#potongan_harga").val(
                            "Rp. " + formatRupiah(potonganBiaya)
                        );

                        let jatuhTempo = result.jatuh_tempo;
                        let [year, month, day] = jatuhTempo.split('-');
                        let formattedDate = `${day}/${month}/${year}`;
                        $("#jatuh_tempo").val(formattedDate);

                        setInitialTotalsFromFetch(datas);

                        $("#potongan_harga")
                            .off("change")
                            .on("change", function () {
                                subTotalPPAT();
                            });

                        table.ajax.reload();
                        $.LoadingOverlay("hide");
                    },
                });
            } else if (jenis_pekerjaan == 2 && sesiSaatIni == 1) {
                swal("Peringatan", "Data Notaris Tidak Sesuai", "error");
                return;
            } else if (jenis_pekerjaan == 1 && sesiSaatIni == 2) {
                swal("Peringatan", "Data PPAT Tidak Sesuai", "error");
                return;
            }
        });
    },
});

let table = $(".table-proses").DataTable({
    ajax: {
        url: baseUrl("/transaksi/proses-fetch"),
        headers: {
            "X-XSRF-TOKEN": getCookie("XSRF-TOKEN"),
        },
        dataSrc: "data",
        type: "POST",
        data: function (form) {
            form.jenis_pekerjaan = $("#jenis_pekerjaan").val();
            form.pekerjaan_id = pekerjaan_id;
            form.transaksi_id = $("#transaksi_id").val();
        },
    },
    autoWidth: false,
    responsive: true,
    ordering: false,
    searching: false,
    info: false,
    sorting: false,
    paging: false,
    columns: [{
        name: "",
        render: function (data, i, row, meta) {
            return meta.row + 1;
        },
        width: "20px",
    },
    {
        data: "nama",
        width: "180px",
    },
    {
        data: "status_proses",
        render: function (data, row) {
            if (row.status_proses == "1") {
                div.querySelector('input[type="checkbox"]').setAttribute(
                    "checked",
                    true
                );
            }
            return data == "1" ?
                '<span class="badge badge-success">Sudah Valid</span>' :
                '<span class="badge badge-danger">Belum Valid</span>';
        },

        width: "70px",
    },
    ],
    createdRow: function (row, data) {
        $("td", row).eq(0).addClass("text-center");
    },
});

// => Show Modal Petugas
let tablePetugas = $("#table-petugas").DataTable({
    ajax: {
        url: baseUrl("/master/petugas-fetch"),
        headers: {
            "X-XSRF-TOKEN": getCookie("XSRF-TOKEN"),
        },
        dataSrc: "data",
        type: "POST",
    },
    processing: true,
    serverSide: true,
    paging: true,
    lengthChange: true,
    searching: true,
    ordering: true,
    order: [],
    info: true,
    autoWidth: false,
    columns: [{
        name: "",
        render: function (data, i, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
        },
        width: "20px",
    },
    {
        data: "nama",
    },

    {
        data: "jenis_kelamin_nama",
    },
    {
        data: "no_telp",
    },
    ],
    createdRow: function (row, data) {
        $(row).click(function (e) {
            e.preventDefault();

            $("#petugas_id").val(data.id);
            $("#petugas_nama").text(data.nama);
            $("#petugas_jenis_kelamin").text(data.jenis_kelamin_nama);
            $("#petugas_no_telp").text(data.no_telp);

            $("#modal-petugas").modal("hide");
        });
    },
});

$("#btn-pilih-petugas").on("click", function () {
    $("#modal-petugas").modal("show");
});
// END CARI PETUGAS
/**
 * Cari Pemohon
 */
let tablePemohon = $("#table-pemohon").DataTable({
    ajax: {
        url: baseUrl("/master/pemohon-fetch"),
        headers: {
            "X-XSRF-TOKEN": getCookie("XSRF-TOKEN"),
        },
        dataSrc: "data",
        type: "POST",
    },
    processing: true,
    serverSide: true,
    paging: true,
    lengthChange: true,
    searching: true,
    ordering: true,
    order: [],
    info: true,
    autoWidth: false,
    columns: [{
        name: "",
        render: function (data, i, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
        },
        width: "20px",
    },
    {
        data: "nik",
    },
    {
        data: "nama",
    },

    {
        data: "jenis_kelamin_nama",
    },
    {
        data: "no_telp",
    },
    ],
    createdRow: function (row, data) {
        $(row).click(function (e) {
            e.preventDefault();

            $("#pemohon_id").val(data.id);
            $("#pemohon_nama").text(": " + data.nama);
            $("#pemohon_nik").text(": " + data.nik);
            $("#pemohon_jenis_kelamin").text(": " + data.jenis_kelamin_nama);
            $("#pemohon_no_telp").text(": " + data.no_telp);
            $("#pemohon_alamat").val(data.alamat);

            $("#modal-pemohon").modal("hide");
        });
    },
});

$("#btn-pilih-pemohon").on("click", function () {
    $("#modal-pemohon").modal("show");
});

// END CARI PEMOHON
$("#btn-tambah-pemohon").on("click", function () {
    $("#form-pemohon").formReset();
    $(".message-error").empty();
    $('select[target="jenis_kelamin"]').select2AjaxNew({
        url: baseUrl("/master/pemohon/select2/getjeniskelamin"),
        method: "POST",
    });
    $("#modal-tambah-pemohon").modal("show");
});

$("#radioBtnStatus a").on("click", function () {
    var sel = $(this).data("title");
    var tog = $(this).data("toggle");
    $("#" + tog).prop("value", sel);

    $('a[data-toggle="' + tog + '"]')
        .not('[data-title="' + sel + '"]')
        .removeClass("active")
        .addClass("notActive");
    $('a[data-toggle="' + tog + '"][data-title="' + sel + '"]')
        .removeClass("notActive")
        .addClass("active");
});

$("#radioBtn a").on("click", function () {
    var sel = $(this).data("title");
    var tog = $(this).data("toggle");
    $("#" + tog).prop("value", sel);

    $('a[data-toggle="' + tog + '"]')
        .not('[data-title="' + sel + '"]')
        .removeClass("active")
        .addClass("notActive");
    $('a[data-toggle="' + tog + '"][data-title="' + sel + '"]')
        .removeClass("notActive")
        .addClass("active");
    $.LoadingOverlay("show");

    setTransactionCode($("#jenis_pekerjaan").val());
    reset();

    $('select[name="pekerjaan_id"]').select2AjaxNew({
        url: baseUrl("/transaksi/getpekerjaan"),
        method: "POST",
        data: {
            jenis_pekerjaan: $("#jenis_pekerjaan").val(),
        },
    });

    // -- transaksi reload
    tableTransaksi.ajax.reload();

    // -- in here set create access
    let saveButton = $('#simpan_transaksi_baru').removeClass('d-none');
    if (access.create != 1) saveButton.addClass('d-none');

    $.LoadingOverlay("hide");
});

$('select[name="pekerjaan_id"]').on("change", function (e) {
    $.LoadingOverlay("show");

    pekerjaan_id = $("#pekerjaan_id").val();
    table.ajax.reload();

    $('select[name="kategori_pekerjaan_id"]').select2AjaxNew({
        url: baseUrl("/transaksi/getkategori"),
        method: "POST",
        data: {
            jenis_pekerjaan: $("#jenis_pekerjaan").val(),
            pekerjaan_id: $("#pekerjaan_id").val(),
        },
    });

    $.LoadingOverlay("hide");
});

$('select[name="kategori_pekerjaan_id"]').on("change", function (e) {
    $.LoadingOverlay("show");

    $.httpRequest({
        url: baseUrl(`/transaksi/getdetailkategori`),
        method: "POST",
        data: JSON.stringify({
            jenis_pekerjaan: $("#jenis_pekerjaan").val(),
            pekerjaan_id: pekerjaan_id,
            kategori_pekerjaan_id: $("#kategori_pekerjaan_id").val(),
        }),
        contentType: "application/json",
        response: (res) => {
            $.LoadingOverlay("hide");
            if (res.statusCode == 200) {
                $('[name="estimasi_waktu"]').val(
                    res.data ? res.data.estimasi_waktu : ""
                );
                $('[name="biaya_layanan"]').val(
                    res.data ? formatRupiah(res.data.harga) : 0
                );

                // sub total kanan
                subTotalNotaris();
            } else {
                swal("Peringatan !", message, "error");
            }
        },
    });
});

$("#btn-pilih-no-akta").click(function () {
    $("#modal-no-akta").modal("show");
});

$("#btn-riwayat-pembayaran").click(function () {
    $.httpRequest({
        url: baseUrl(`/transaksi/detail-pembayaran`),
        method: "POST",
        data: JSON.stringify({
            transaksi_id: $("#transaksi_id").val(),
        }),
        contentType: "application/json",
        response: (res) => {
            if (res.statusCode == 200) {
                const sisaPembayaran = res.data.sisa_hutang;

                $(".riwayat-pembayaran-no-akta").text(": " + res.data.no_akta);
                $(".riwayat-pembayaran-total-dibayar").text(": Rp. " + formatRupiah(res.data.total_bayar));
                $(".riwayat-pembayaran-sisa-hutang").text(`: ${sisaPembayaran < 0 ? 'Kelebihan :' : ''} Rp. ${formatRupiah(sisaPembayaran)}`);

                /**
                 * riwayat pembayaran
                 */
                let tableRiwayatPembayaran = $("#table-riwayat-pembayaran").DataTable({
                    ajax: {
                        url: baseUrl("/transaksi/riwayat-pembayaran-fetch"),
                        headers: {
                            "X-XSRF-TOKEN": getCookie("XSRF-TOKEN"),
                        },
                        dataSrc: "data",
                        type: "POST",
                        data: function (form) {
                            form.transaksi_id = $("#transaksi_id").val();
                        },
                    },
                    processing: true,
                    serverSide: true,
                    paging: true,
                    lengthChange: true,
                    searching: true,
                    ordering: true,
                    order: [],
                    info: true,
                    destroy: true,
                    autoWidth: false,
                    columns: [
                        {
                            data: "pembayaran_ke",
                        },
                        {
                            data: "jumlah_dibayar",
                            render: function (data, i, row) {
                                return "Rp. " + formatRupiah(data);
                            },
                        },
                        {
                            data: "created_at",
                            render: function (data, i, row) {
                                if (row.created_at != null) {
                                    return localDateTime(data.toLocaleString());
                                } else {
                                    return localDateTime(
                                        row.updated_at.toLocaleString()
                                    );
                                }
                            },
                        },
                        {
                            data: "id",
                            render: function (data, i, row) {
                                // ==> Container
                                var div = document.createElement("div");
                                div.className = "row-action";

                                // ==> Button Edit
                                var btn = document.createElement("button");
                                btn.className = "btn btn-warning btn-action action-edit-pembayaran";
                                btn.innerHTML = '<i class="fa fa-edit"></i>';
                                div.append(btn);

                                // ==> Button Delete
                                var btn = document.createElement("button");
                                btn.className = "btn btn-danger btn-action action-hapus-pembayaran";
                                btn.innerHTML = '<i class="fas fa-eraser"></i>';
                                div.append(btn);

                                return access.role == 'superadmin' ? div.outerHTML : '-';
                            },
                        },
                    ],
                    createdRow: function (row, data) {
                        // ==> Edit Button
                        $(".action-edit-pembayaran", row).click(function (e) {
                            // -- in here set input
                            $('#modal-edit-riwayat-bayar input[name="id"]').val(data.id);
                            $('#modal-edit-riwayat-bayar .dibayar_value').val(data.jumlah_dibayar);
                            $('#modal-edit-riwayat-bayar input[name="pembayaran_ke"]').val(data.pembayaran_ke);
                            $('#modal-edit-riwayat-bayar input[name="jumlah_dibayar"]').val(formatRupiah(data.jumlah_dibayar));

                            $("#modal_riwayat_pembayaran").modal("hide");
                            $("#modal-edit-riwayat-bayar").modal("show");
                        });

                        // ==> Delete Button
                        $(".action-hapus-pembayaran", row).click(function (e) {
                            e.preventDefault();
                            swal({
                                title: "Peringatan !",
                                text: `Data akan langsung terhapus, anda yakin akan menghapusnya ??`,
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
                                        url: baseUrl(`/transaksi/riwayat-pembayaran/remove/${data.id}`),
                                        method: "DELETE",
                                        response: (res) => {
                                            $.LoadingOverlay("hide");
                                            if (res.statusCode == 200) {
                                                // -- in here set jumlah bayar
                                                let totalBayar = $('#jumlah_pembayaran').val();
                                                totalBayar = parseInt(totalBayar) - parseInt(data.jumlah_dibayar);

                                                $('#jumlah_pembayaran').val(totalBayar);

                                                // calculate
                                                if (sesiSaatIni == 1) {
                                                    subTotalNotaris();
                                                } else if (sesiSaatIni == 2) {
                                                    subTotalPPAT();
                                                }

                                                swal("Sukses", res.message, "success");
                                                $("#modal_riwayat_pembayaran").modal("hide");
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

                $("#modal_riwayat_pembayaran").modal("show");
            } else {
                swal("Maaf!", res.message, "error");
            }
        },
    });
});

$('input[name="biaya_layanan"]').on("input", function () {
    isFormatting = true;
    const value = $(this).val();
    var numericVal = value.replace(/[^0-9]/g, "");
    var formattedVal = formatRupiah(numericVal);
    $(this).val(formattedVal);

    if (sesiSaatIni == 1) {
        subTotalNotaris();
    } else if (sesiSaatIni == 2) {
        subTotalPPAT();
    }
});

$('input[name="biaya_lainnya"]').on("input", function () {
    const value = $(this).val();
    var numericVal = value.replace(/[^0-9]/g, "");
    var formattedVal = formatRupiah(numericVal);
    $(this).val(formattedVal);

    if (sesiSaatIni == 1) {
        subTotalNotaris();
    } else if (sesiSaatIni == 2) {
        subTotalPPAT();
    }
});

$(document).on("input", "input[target^='biaya_lainya']", function () {
    const value = $(this).val();
    var numericVal = value.replace(/[^0-9]/g, "");
    var formattedVal = formatRupiah(numericVal);
    $(this).val(formattedVal);

    if (sesiSaatIni == 1) {
        subTotalNotaris();
    } else if (sesiSaatIni == 2) {
        subTotalPPAT();
    }
});

$('input[name="potongan_harga"], input[name="pembayaran_sekarang"]')
    .off("input")
    .on("input", function () {
        const inputField = $(this);
        const value = inputField.val();

        var numericVal = value.replace(/[^0-9]/g, "");
        var formattedVal = formatRupiah(numericVal);

        if (formattedVal !== value) {
            inputField.val(formattedVal);
        }

        if (sesiSaatIni == 1) {
            subTotalNotaris();
        } else if (sesiSaatIni == 2) {
            subTotalPPAT();
        }
    });

$("#cetakPemohon").off("click").on("click", function () {
    var no_akta = $("#no_akta_notaris").val();

    $.httpRequest({
        url: baseUrl("/transaksi/cetakPemohonFetch/" + no_akta),
        method: "GET",
        response: (res) => {
            if (res.statusCode == 404) {
                swal("Peringatan!", res.message, "error");
                $("#modalCetakPemohon").addClass("d-none");
                $("#modalCetakPemohon").modal("hide");
                return;
            } else {
                $(".table-serah-terima").DataTable({
                    ajax: {
                        url: baseUrl("/transaksi/cetakPemohonFetch/" + no_akta),
                        type: "GET",
                        headers: {
                            "X-XSRF-TOKEN": getCookie("XSRF-TOKEN"),
                        },
                        dataSrc: function (json) {
                            return json.data;
                        },
                    },
                    processing: true,
                    serverSide: true,
                    lengthChange: false,
                    searching: false,
                    ordering: false,
                    order: [],
                    info: false,
                    paging: false,
                    autoWidth: false,
                    destroy: true,
                    columns: [{
                        name: "no",
                        render: function (data, i, row, meta) {
                            return meta.row + 1;
                        },
                        width: "20px",
                    },
                    {
                        data: "no_transaksi",
                        render: function (data, type, row) {
                            return renderHTML(data);
                        }
                    },
                    {
                        data: "uraian",
                        render: function (data, type, row) {
                            return renderHTML(data);
                        }
                    },
                    {
                        data: "keperluan",
                        render: function (data, type, row) {
                            return renderHTML(data);
                        }
                    },
                    {
                        data: "id",
                        render: function (data, i, row) {
                            let div = document.createElement("div");
                            div.className = "d-flex row-action";

                            let btnEdit = document.createElement("button");
                            btnEdit.className = "btn btn-success btn-action btn-sm action-edit";
                            btnEdit.innerHTML = '<i class="fa fa-edit mr-1"></i> Edit';
                            btnEdit.setAttribute("data-id", data);
                            if (access.update == 1) div.append(btnEdit);

                            let btnCetak = document.createElement("button");
                            btnCetak.className = "btn btn-primary btn-action action-cetak";
                            btnCetak.innerHTML = '<i class="fas fa-print mr-1"></i> Cetak';
                            div.append(btnCetak);

                            return div.outerHTML;
                        },
                    }
                    ],
                    createdRow: function (row, data, dataIndex) {
                        $(".action-edit", row).on("click", function (e) {
                            e.preventDefault();
                            $("#modalCetakPemohon").modal("hide");
                            $(".message-error").empty();
                            $("#modal-edit-cetak-pemohon").modal("show");

                            $('input[name="id"]').val(data.id);
                            // $("#daftar-list-edit").summernote("code", renderHTML(data.list_keperluan));
                            $("#uraian-edit").summernote("code", renderHTML(data.uraian));
                            $("#keperluan-edit").summernote("code", renderHTML(data.keperluan));
                        });

                        $(".action-cetak", row).click(function (e) {
                            e.preventDefault();
                            const id = data.id;
                            window.open(baseUrl(`/transaksi/cetakPemohon/${id}`), "_blank");
                        });
                    },
                });

                $("#tambah-serah-terima").off().on("click", function () {
                    $("#modal-tambah-serah-terima").modal("show");
                    $("#modalCetakPemohon").modal("hide");

                    // $("#daftar-list-tambah").summernote("code", "");
                    $("#uraian-tambah").summernote("code", "");
                    $("#keperluan-tambah").summernote("code", "");
                    $("#modal-tambah-cetak-pemohon").modal("hide");
                });

                $(".btn-simpan-cetak-pemohon").off().on("click", function (e) {
                    e.preventDefault();

                    $.httpRequest({
                        url: baseUrl(`/transaksi/create/cetak-serah-terima/`),
                        method: "POST",
                        data: JSON.stringify({
                            no_transaksi: no_akta,
                            daftar_list: $("#daftar-list-tambah").val(),
                            uraian: $("#uraian-tambah").val(),
                            keperluan: $("#keperluan-tambah").val()
                        }),
                        contentType: "application/json",
                        response: (res) => {
                            if (res.statusCode == 200) {
                                swal("Sukses", "Sukses Menambahkan Data", "success");
                                $("#modal-tambah-serah-terima").modal("hide");
                            } else {
                                swal("Terjadi Kesalahan", res.message, "error");
                            }

                        }
                    });
                });

            }
        },
    });
});;


$("#form-edit-cetak-pemohon").formSubmit((response) => {
    if (response.statusCode == 200) {
        $("#form-edit-cetak-pemohon").formReset();
        $("#modal-edit-cetak-pemohon").modal("hide");

        swal("Sukses !", response.message, "success");
    }
});

$("#cetakInvoices").on("click", function (e) {
    e.preventDefault();

    const noAkta = $("#no_akta_notaris").val();

    $.httpRequest({
        url: baseUrl(`/transaksi/cetakInvoices/${noAkta}`),
        method: "GET",
        response: function (res) {
            window.open(baseUrl(`/transaksi/cetakInvoices/${noAkta}`), "_blank");
        }
    });
});

$("#simpan_transaksi_baru").click(function () {
    let sesi = sesiSaatIni;

    let pekerjaan_id = "";
    let kategori_pekerjaan_id = "";

    if (sesi == 1) {
        pekerjaan_id = $("#pekerjaan_id").val();
        kategori_pekerjaan_id = $("#kategori_pekerjaan_id").val();
    } else if (sesi == 2) {
        pekerjaan_id = currentPPATPekerjaanId;
        kategori_pekerjaan_id = currentPPATKategoriId;
    }

    if (!pekerjaan_id) {
        swal("Warning", "Pastikan Memilih Pekerjaan", "error");
        return;
    }

    if (kategori_pekerjaan_id == 0) {
        swal("Warning!", "Pastikan Anda Memilih Kategori Pekerjaan", "error");
        return;
    }

    let kembalian = parseInt($("#hidden_kembalian").val(), 10);
    let pembayaran_sekarang = parseInt(
        formatRupiahToNumber($("#pembayaran_sekarang").val()),
        10
    );

    let true_pembayaran_sekarang = pembayaran_sekarang;

    $(".message-error").empty();

    if (sesiSaatIni == 1) {
        let url = baseUrl("/transaksi"); // URL insert
        if ($("#transaksi_id").val() !== "")
            url = baseUrl("/transaksi/update/" + $("#transaksi_id").val()); // url update
        $.ajax({
            type: "POST",
            url: url,
            headers: {
                "X-XSRF-TOKEN": getCookie("XSRF-TOKEN"),
            },
            contentType: "application/json",
            data: JSON.stringify({
                transaksi_id: $("#transaksi_id").val(),
                no_akta: $("#no_akta_notaris").val(),
                tanggal_daftar: $("#tanggal_daftar").val(),
                tanggal_selesai: $("#tanggal_selesai").val(),
                pemohon_id: $("#pemohon_id").val(),
                jenis_pekerjaan_id: $("#jenis_pekerjaan").val(),
                pekerjaan_id: $("#pekerjaan_id").val(),
                kategori_pekerjaan_id: $("#kategori_pekerjaan_id").val(),
                biaya_layanan: formatRupiahToNumber($("#biaya_layanan").val()),
                biaya_lainya: formatRupiahToNumber($("#biaya_lainya").val()),
                jumlah_materai: $("#jumlah_materai").val(),
                materai_id: $("#materai_id").val(),
                status_id: $("#status").val(),
                petugas_id: $("#petugas_id").val(),
                jenis_pembayaran_id: $("#jenis_pembayaran_id").val(),
                potongan_harga: formatRupiahToNumber(
                    $("#potongan_harga").val()
                ),
                jatuh_tempo: $("#jatuh_tempo").val(),
                pembayaran_sekarang: true_pembayaran_sekarang,
                keterangan: $("#keterangan").val(),
                judul: $("#judul").val(),
                nomor_akta: $("#nomor_akta").val(),
                tanggal_akta: $("#tanggal_akta").val(),
            }),
            success: function (res) {
                if (res.failed == true) {
                    swal("Maaf !", res.message, "error");
                    return;
                }
                $.LoadingOverlay("hide");
                swal("Sukses !", res.message, "success").then(function () {
                    location.reload();
                });
            },
            error: function (jqxhr) {
                res = jqxhr.responseJSON;
                switch (res.statusCode) {
                    case 400:
                        if (typeof res.data !== "undefined") {
                            let error = res.data.error;
                            let index = Object.keys(error);
                            index.forEach((val) => {
                                $(`small[data-target="${val}_error"]`).text(
                                    error[val]
                                );
                            });
                        }

                        if (res.reloadDraft) {
                            swal("Peringatan !", res.message, "error");
                            table_draft.ajax.reload();
                        }

                        break;
                    case 403:
                        swal("Maaf !", res.message, "error");
                        break;
                    case 404:
                        swal("Maaf !", res.message, "error");
                        break;
                    case 500:
                        swal("Maaf !", res.message, "error");
                        break;
                    default:
                        // code
                        break;
                }
            },
        });
    } else {
        let url = baseUrl("/transaksi");

        if ($("#transaksi_id").val() !== "")
            url = baseUrl("/transaksi/update/" + $("#transaksi_id").val()); // url update

        $.ajax({
            type: "POST",
            url: url,
            headers: {
                "X-XSRF-TOKEN": getCookie("XSRF-TOKEN"),
            },
            contentType: "application/json",
            data: JSON.stringify({
                transaksi_id: $("#transaksi_id").val(),
                no_akta: $("#no_akta_notaris").val(),
                tanggal_daftar: $("#tanggal_daftar").val(),
                tanggal_selesai: $("#tanggal_selesai").val(),
                pemohon_id: $("#pemohon_id").val(),
                jenis_pekerjaan_id: $("#jenis_pekerjaan").val(),
                pekerjaan_id: {
                    pekerjaan_ppat_id: $("select[name='pekerjaan[pekerjaan_ppat_id][]']")
                        .map(function () {
                            return $(this).val();
                        })
                        .get(),
                },
                kategori_pekerjaan_id: {
                    kategori_pekerjaan_ppat_id: $("select[name='kategori_pekerjaan[kategori_pekerjaan_ppat_id][]']")
                        .map(function () {
                            return $(this).val();
                        })
                        .get(),
                },
                transaksi_ppat_id: $(".ppat-container input[name='transaksi_ppat_id[]']")
                    .map(function () {
                        return $(this).val();
                    })
                    .get(),
                transaksi_status: {
                    status_id: $(".ppat-container input[name='status_ppat[]']")
                        .map((i, e) => $(e).val())
                        .get(),
                    judul: $(".ppat-container input[name='judul_ppat[]']")
                        .map((i, e) => $(e).val())
                        .get(),
                    no_akta: $(".ppat-container input[name='no_akta_ppat[]']")
                        .map((i, e) => $(e).val())
                        .get(),
                    tgl_akta: $(".ppat-container input[name='tgl_akta_ppat[]']")
                        .map((i, e) => $(e).val())
                        .get(),
                },
                biaya_layanan: {
                    biaya_layanan_ppat: $(".ppat-container input[name='biaya_layanan[biaya_layanan_ppat][]']")
                        .map(function () {
                            let value = formatRupiahToNumber($(this).val());
                            return !value ? 0 : value;
                        })
                        .get()
                },
                biaya_lainya: {
                    biaya_lainya_ppat: $(".ppat-container input[name='biaya_lainnya[biaya_lainya_ppat][]']")
                        .map(function () {
                            let value = formatRupiahToNumber($(this).val());
                            return !value ? 0 : value;
                        })
                        .get()
                },
                jumlah_materai: $("#jumlah_materai").val(),
                petugas_id: $("#petugas_id").val(),
                jenis_pembayaran_id: $("#jenis_pembayaran_id").val(),
                potongan_harga: formatRupiahToNumber(
                    $("#potongan_harga").val()
                ),
                jatuh_tempo: $("#jatuh_tempo").val(),
                pembayaran_sekarang: true_pembayaran_sekarang,
                keterangan: $("#keterangan").val(),
            }),
            success: function (res) {
                if (res.failed == true) {
                    swal("Maaf !", res.message, "error");
                    return;
                }
                swal("Sukses !", res.message, "success").then(function () {
                    location.reload();
                });
            },
            error: function (jqxhr) {
                let res = jqxhr.responseJSON;
                switch (res.statusCode) {
                    case 400:
                        if (typeof res.data !== "undefined") {
                            let error = res.data.error;
                            let index = Object.keys(error);
                            index.forEach((val) => {
                                $(`small[data-target="${val}_error"]`).text(
                                    error[val]
                                );
                            });
                        }

                        if (res.reloadDraft) {
                            swal("Peringatan !", res.message, "error");
                            table_draft.ajax.reload();
                        }

                        break;
                    case 403:
                        swal("Maaf !", res.message, "error");
                        break;
                    case 404:
                        swal("Maaf !", res.message, "error");
                        break;
                    case 500:
                        swal("Maaf !", res.message, "error");
                        break;
                    default:
                        break;
                }
            },
        });
    }
});
// -----------------------------------------------------------------------------------------------------------------------------
// => Get List Validasi Notaris
// Table click handler
$(".table-proses tbody").on("click", "td", function () {
    if (this.textContent === "" || !table.row(this).data()) return;

    const tableData = table.row(this).data();
    const kategoriPekerjaanId = $("#kategori_pekerjaan_id").val();

    if (kategoriPekerjaanId == 0) {
        swal("Peringatan!", "Pastikan Anda memilih kategori", "error");
        return;
    }

    $.LoadingOverlay("show");

    const requestData = {
        jenis_pekerjaan: $("#jenis_pekerjaan").val(),
        pekerjaan_id: pekerjaan_id,
        kategori_pekerjaan_id: kategoriPekerjaanId,
        id: tableData.id,
        transaksi_id: $("#transaksi_id").val(),
    };

    $.httpRequest({
        url: baseUrl("/transaksi/proses-notaris"),
        method: "POST",
        data: JSON.stringify(requestData),
        contentType: "application/json",
        response: handleResponse
    });
});

// Event handler untuk menyimpan proses notaris
$("#simpan-proses-notaris").on("click", function () {
    const atributData = {};
    $("input[name='atribut']").each(function () {
        const atributId = $(this).val();
        const isChecked = $(this).is(":checked") ? 1 : 0;
        atributData[atributId] = isChecked;
    });

    const requestData = {
        prosesId: $("input[name='id_proses']").val(),
        pekerjaanNama: $("input[name='detail_proses_pekerjaan']").val(),
        kategoriNama: $("input[name='detail_proses_kategori']").val(),
        prosesNama: $("input[name='detail_proses_nama']").val(),
        atribut: atributData,
        catatan: $("#proses_catatan").val(),
        isValidate: $("#validasi").is(":checked") ? "1" : "0"
    };

    $.httpRequest({
        url: baseUrl("/transaksi/validasi-notaris"),
        method: "POST",
        data: JSON.stringify(requestData),
        contentType: "application/json",
        response: function (res) {
            if (res.statusCode === 200) {
                const requestData = {
                    jenis_pekerjaan: $("#jenis_pekerjaan").val(),
                    pekerjaan_id: pekerjaan_id,
                    kategori_pekerjaan_id: $("#kategori_pekerjaan_id").val(),
                    id: $("input[name='id_proses']").val(),
                    transaksi_id: $("#transaksi_id").val(),
                };

                $.httpRequest({
                    url: baseUrl("/transaksi/proses-notaris"),
                    method: "POST",
                    data: JSON.stringify(requestData),
                    contentType: "application/json",
                    response: function (fetchRes) {
                        if (fetchRes.statusCode === 200) {
                            updateModalContent(fetchRes.data);
                            createAtributToggles(fetchRes.data);

                            table.ajax.reload();

                            swal("Sukses", fetchRes.message, "success");
                            $("#modal-detal-proses").modal("hide");
                        } else {
                            swal("Peringatan!", fetchRes.message, "error");
                        }
                    }
                });
            } else {
                swal("Gagal", "Gagal saat menyimpan data", "error");
            }
        }
    });
});

function handleResponse(res) {
    $.LoadingOverlay("hide");

    if (res.statusCode !== 200) {
        swal("Peringatan!", res.message, "error");
        return;
    }

    const result = res.data;
    updateModalContent(result);
    createAtributToggles(result);
}

function updateModalContent(result) {
    $('[name="id_proses"]').val(result.proses.id);
    $('[name="detail_proses_pekerjaan"]').val(result.pekerjaan.nama);
    $('[name="detail_proses_kategori"]').val(result.kategori.nama);
    $('[name="detail_proses_nama"]').val(result.proses.nama);
    $("#detail_proses").text(result.proses.detail);

    if (result.prosesNotaris) {
        $("#proses_catatan").val(result.prosesNotaris.catatan);
        $("#validasi").prop("checked", result.prosesNotaris.isValidate == "1" || result.prosesNotaris.isValidate == 1);
    } else {
        $("#proses_catatan").val("");
        $("#validasi").prop("checked", false);
    }

    if (!$("#modal-detal-proses").is(":visible")) {
        $("#modal-detal-proses").modal("show");
    }
}

function updateModalContentPPAT(result) {
    $('[name="id_proses_ppat"]').val(result.proses.id);
    $('[name="detail_proses_pekerjaan_ppat"]').val(result.pekerjaan.nama);
    $('[name="detail_proses_kategori_ppat"]').val(result.kategori.nama);
    $('[name="detail_proses_nama_ppat"]').val(result.proses.nama);
    $("#detail_proses_ppat").text(result.proses.detail);

    if (result.prosesPPAT) {
        $("#proses_catatan_ppat").val(result.prosesPPAT.catatan);
        $("#validasi_ppat").prop("checked", result.prosesPPAT.isValidate == "1" || result.prosesPPAT.isValidate == 1);
    } else {
        $("#proses_catatan_ppat").val("");
        $("#validasi_ppat").prop("checked", false);
    }

    if (!$("#modal-detal-proses-ppat").is(":visible")) {
        $("#modal-detal-proses-ppat").modal("show");
    }
}

function createAtributToggles(result) {
    const $container = $("#label-atribut-container");
    $container.empty();

    if (result.atribut && result.atribut.length > 0) {
        result.atribut.forEach((val) => {
            const $newToggle = createToggle(val);
            const isChecked =
                result.prosesNotaris && result.prosesNotaris.atribut ?
                    result.prosesNotaris.atribut[val.id] === 1 :
                    false;

            $newToggle
                .find('input[name="atribut"]')
                .prop("checked", isChecked)
                .val(val.id);

            $container.append($newToggle);
        });

        $container.show();
    } else {
        $container.hide();
    }
}

function createAtributTogglesPPAT(result) {
    const $container = $("#label-atribut-container-ppat");
    $container.empty();

    if (result.atribut && result.atribut.length > 0) {
        result.atribut.forEach((val) => {
            const $newToggle = createToggle(val);
            const isChecked =
                result.prosesPPAT && result.prosesPPAT.atribut ?
                    result.prosesPPAT.atribut[val.id] === 1 :
                    false;

            $newToggle
                .find('input[name="atribut"]')
                .prop("checked", isChecked)
                .val(val.id);

            $container.append($newToggle);
        });

        $container.show();
    } else {
        $container.hide();
    }
}

function createToggle(val) {
    return $(`
        <div class="col-md-4 d-flex">
            <label class="switch switch-info d-flex align-items-center">
                <p class="mb-0 me-2">${val.atribut}</p>
                <input type="checkbox" name="atribut" class="form-check-input" value="${val.id}">
                <span class="slider"></span>
            </label>
        </div>
    `);
}
// ------------------------------------------------------------------------------------------------------------------------------

// => End List Validasi Notaris

// ------------------------------------------------------------------------------------------------------------------------------

// => Start List Validasi PPAT

// => End List Validasi PPAT

//-----------------------------------------------------------------------------------------------------------------------------
// => Helper Reset Form Transaksi
function formTransaksiReset() {
    const dateNow = moment().format("DD/MM/YYYY");

    $("#pemohon_nik").text("");
    $("#pemohon_jenis_kelamin").text("");
    $("#pemohon_nama").text("");
    $("#pemohon_no_telp").text("");
    $("#pemohon_alamat").val("");
    $("#jatuh_tempo").val(dateNow);
    $("#tanggal_selesai").val(dateNow);
    $("#petugas_nama").empty();
    $("#petugas_jenis_kelamin").empty();
    $("#petugas_no_telp").empty();
    $("#transaksi_sub_total").empty();
    $("#potongan_harga").val("");
    $("#transaksi_total").empty();
    $("#sisa_pembayaran").val("");
    $("#pembayaran_sekarang").val("");
    $("#keterangan").val("");
    $("#jumlah_materai").val(0);

    // Reset status button
    $(".statusBtn").removeClass("active notActive");
    $(".statusBtn[data-title='1']").addClass("active");
    $(".statusBtn[data-title='2'], .statusBtn[data-title='3']").addClass(
        "notActive"
    );
    $("#additional-form-notaris").addClass("d-none");

    // Set nilai input tersembunyi
    $("#status").val("1");
}

function reset() {
    table.clear().draw();

    // Cookie flush
    $.httpRequest({
        url: baseUrl("/transaksi/cookie-flush"),
        method: "DELETE",
        response: (res) => null,
    });

    $('#transaksi_id').val("");
    $('select[name="pekerjaan_id"]').val("0").trigger("change");
    $('select[name="kategori_pekerjaan_id"]').val("0").trigger("change");
    $('input[name="estimasi_waktu"]').val("");
    $('input[name="biaya_layanan"]').val(0);
    $('input[name="biaya_lainnya"]').val(0);
    $('input[name="tanggal_selesai"]').val("");
    $('input[name="materai"]').val("");
    $("#petugas_id").val("");
    $("#petugas_nama").text("-");
    $("#petugas_jenis_kelamin").text("-");
    $("#petugas_no_telp").text("-");

    $("#transaksi_sub_total").text("Rp." + formatRupiah(0));
    $("#potongan_harga").val(formatRupiah(0));
    $("#transaksi_total").text("Rp." + formatRupiah(0));
    $("#kembalian").text("Rp." + formatRupiah(0));
    $("#jumlah_materai").val("");
    $("#sisa_pembayaran").val(formatRupiah(0));
    $("#jumlah_pembayaran").val(0);
    $('input[name="pembayaran_sekarang"]').val(formatRupiah(0));
}

function subTotalNotaris() {
    let biaya_layanan = formatRupiahToNumber($('[name="biaya_layanan"]').val());
    let biaya_lainnya = formatRupiahToNumber($('[name="biaya_lainnya"]').val());
    let total_sudahbayar = formatRupiahToNumber($("#jumlah_pembayaran").val());
    let sub_total = biaya_layanan + biaya_lainnya;
    $("#transaksi_sub_total").text("Rp." + formatRupiah(sub_total));
    $("#transaksi_total").text("Rp." + formatRupiah(sub_total));
    $("#sisa_pembayaran").val(formatRupiah(sub_total));

    let potongan_harga = $("#potongan_harga").val();
    let pembayaran_sekarang = $("#pembayaran_sekarang").val();

    if (potongan_harga && potongan_harga != 0) {
        potongan_harga = formatRupiahToNumber(potongan_harga);
    } else {
        potongan_harga = 0;
    }

    if (pembayaran_sekarang && pembayaran_sekarang != 0) {
        pembayaran_sekarang = formatRupiahToNumber(pembayaran_sekarang);
    } else {
        pembayaran_sekarang = 0;
    }

    if (potongan_harga > sub_total) {
        potongan_harga = sub_total;
        $("#potongan_harga").val(formatRupiah(potongan_harga));
        swal("Peringatan!", "Diskon tidak boleh melebihi Sub Total!", "error");
    }

    let total = sub_total - potongan_harga;
    $("#transaksi_total").text("Rp." + formatRupiah(total));

    if (
        pembayaran_sekarang > total - total_sudahbayar &&
        $("#jenis_pembayaran_id").val() == 1
    ) {
        let kembalian = total - total_sudahbayar - pembayaran_sekarang;
        $("#sisa_pembayaran").val(0);
        $("#kembalian").text("Rp." + formatRupiah(Math.abs(kembalian)));
        $("#hidden_kembalian").val(Math.abs(kembalian));
    } else if (
        pembayaran_sekarang > total - total_sudahbayar &&
        $("#jenis_pembayaran_id").val() != 1
    ) {
        pembayaran_sekarang = total;
        $("#pembayaran_sekarang").val(formatRupiah(pembayaran_sekarang));
        let sisa_pembayaran = total - total_sudahbayar - pembayaran_sekarang;
        $("#sisa_pembayaran").val(formatRupiah(sisa_pembayaran));
        swal("Peringatan!", "Pembayaran melebihi tagihan!", "error");
    } else {
        let sisa_pembayaran = total - total_sudahbayar - pembayaran_sekarang;
        $("#sisa_pembayaran").val(formatRupiah(sisa_pembayaran));
        $("#kembalian").text("Rp." + formatRupiah(0));
        $("#hidden_kembalian").val(0);
    }
}

function setInitialTotalsFromFetch(data) {
    let subTotalBiaya = 0;
    let totalBiaya = 0;
    let potonganBiaya = 0;

    // Iterasi data untuk menghitung subtotal, total, dan potongan
    $.each(data, function (index, val) {
        if (val.sub_total) {
            subTotalBiaya += parseFloat(val.sub_total);
        }
        if (val.total) {
            totalBiaya += parseFloat(val.total);
        }
        if (val.potongan_biaya) {
            potonganBiaya += parseFloat(val.potongan_biaya);
        }
    });

    // Update elemen DOM untuk Subtotal, Total, dan Potongan
    $("#transaksi_sub_total").text("Rp. " + formatRupiah(subTotalBiaya));
    $("#transaksi_total").text("Rp. " + formatRupiah(totalBiaya));

    // Set nilai potongan biaya dengan format Rupiah dan trigger change event
    $("#potongan_harga").val(formatRupiah(potonganBiaya));
    $("#potongan_harga").trigger("input"); // Ini memicu perubahan untuk memastikan ada sinkronisasi nilai

    // Menyimpan nilai asli di data attribute untuk referensi jika dibutuhkan
    $("#transaksi_sub_total").data("original-value", subTotalBiaya);
    $("#transaksi_total").data("original-value", totalBiaya);
    $("#potongan_harga").data("original-value", potonganBiaya);
}

function resetTaxValue() {
    calculatedTaxValue = 0;
    return calculatedTaxValue;
}

function subTotalPPAT() {
    let sub_total = 0;
    let total_sudahbayar = formatRupiahToNumber($("#jumlah_pembayaran").val());

    $('input[target^="biaya_layanan_"]').each(function () {
        let nilai = formatRupiahToNumber($(this).val());
        sub_total += nilai;
    });

    $('input[target^="biaya_lainya_"]').each(function () {
        let nilai = formatRupiahToNumber($(this).val()) || 0;
        sub_total += nilai;
    });

    // -- in here get pajak
    pajakListData.forEach((val) => {
        sub_total += val.pihak_pertama + val.pihak_kedua;
    });

    $("#transaksi_sub_total").text("Rp." + formatRupiah(sub_total));

    let potongan_harga = formatRupiahToNumber($("#potongan_harga").val()) || 0;
    if (potongan_harga > sub_total) {
        potongan_harga = sub_total;
        $("#potongan_harga").val(formatRupiah(potongan_harga));
        swal("Peringatan!", "Diskon tidak boleh melebihi Sub Total!", "error");
    }

    // Hitung total setelah potongan
    let total = sub_total - potongan_harga;
    $("#transaksi_total").text("Rp." + formatRupiah(total));

    let pembayaran_sekarang =
        formatRupiahToNumber($("#pembayaran_sekarang").val()) || 0;
    let sisa_pembayaran = total - total_sudahbayar - pembayaran_sekarang;
    $("#sisa_pembayaran").val(formatRupiah(Math.max(sisa_pembayaran, 0)));

    handlePembayaranLogic(total, total_sudahbayar, pembayaran_sekarang);
}

function handlePembayaranLogic(total, total_sudahbayar, pembayaran_sekarang) {
    let sisa_pembayaran = total - total_sudahbayar;

    if ($("#jenis_pembayaran_id").val() == 1) {
        // Pembayaran tunai
        if (pembayaran_sekarang > sisa_pembayaran) {
            let kembalian = pembayaran_sekarang - sisa_pembayaran;
            $("#sisa_pembayaran").val(formatRupiah(0));
            $("#kembalian").text("Rp." + formatRupiah(kembalian));
            $("#hidden_kembalian").val(kembalian);
        } else {
            $("#sisa_pembayaran").val(
                formatRupiah(sisa_pembayaran - pembayaran_sekarang)
            );
            $("#kembalian").text("Rp." + formatRupiah(0));
            $("#hidden_kembalian").val(0);
        }
    } else {
        // Pembayaran non-tunai
        if (pembayaran_sekarang > sisa_pembayaran) {
            $("#pembayaran_sekarang").val(formatRupiah(sisa_pembayaran));
            $("#sisa_pembayaran").val(formatRupiah(0));
            swal("Peringatan!", "Pembayaran melebihi tagihan!", "error");
        } else {
            $("#sisa_pembayaran").val(
                formatRupiah(sisa_pembayaran - pembayaran_sekarang)
            );
        }
    }
}

function formatUang(input) {
    let angka = input.value.replace(/[^,\d]/g, "").toString();
    input.setAttribute("value", angka);

    let splitAngka = angka.split(",");
    let sisa = splitAngka[0].length % 3;
    let rupiah = splitAngka[0].substr(0, sisa);
    let ribuan = splitAngka[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? "." : "";
        rupiah += separator + ribuan.join(".");
    }

    rupiah =
        splitAngka[1] !== undefined ? rupiah + "," + splitAngka[1] : rupiah;
    input.value = rupiah ? rupiah : "";
}

function formatRupiah(angka) {
    var strAngka = Math.abs(angka).toString().split("").reverse().join("");

    let ribuan = strAngka.match(/\d{1,3}/g);
    if (!ribuan) return "0";
    let formatted = ribuan.join(".").split("").reverse().join("");

    return formatted;
}


function formatRupiahToNumber(rupiah) {
    var numberString = rupiah.split(".").join("");
    var number = parseInt(numberString, 10);
    return number;
}

function localDate(tanggal) {
    // Memisahkan tanggal dan waktu
    let parts = tanggal.split(" ");
    let datePart = parts[0];

    let dateComponents = datePart.split("-");
    let year = dateComponents[0];
    let month = dateComponents[1];
    let day = dateComponents[2];

    let formattedDate = day + "/" + month + "/" + year;
    return formattedDate;
}

function localDateTime(tanggal) {
    let parts = tanggal.split(" ");
    let datePart = parts[0];

    let dateComponents = datePart.split("-");
    let year = dateComponents[0];
    let month = dateComponents[1];
    let day = dateComponents[2];

    let formattedDate = day + "/" + month + "/" + year + " " + parts[1];
    return formattedDate;
}

// select2
$('select[target="jenis_kelamin"]').select2AjaxNew({
    url: baseUrl("/master/pemohon/select2/getjeniskelamin"),
    method: "POST",
});

// Form Submit
$("#form-pemohon").formSubmit((response) => {
    if (response.statusCode == 200) {
        $("#form-pemohon").formReset();
        $("#modal-tambah-pemohon").modal("hide");

        swal("Sukses !", response.message, "success");
        tablePemohon.ajax.reload();
    }
});

// ------------------------------------------------------------------------------------------------------------------------------
// => START PPAT TRANSAKSI TABLE
$(".btn-add-transaksi-ppat").click(function () {
    let rowIndex = tablePPATTransaksi.rows().count();

    let row = tablePPATTransaksi.row.add([
        $("#clone_aksi_transaksi").html(),
        $("#clone_pekerjaan_ppat_id").sl2HTML({
            target: "pekerjaan_ppat_id_" + rowIndex,
        }),
        $("#clone_kategori_pekerjaan_id").sl2HTML({
            target: "kategori_pekerjaan_ppat_id_" + rowIndex,
        }),
        $("#clone_estimasi_waktu").html(),
        $("#clone_biaya_layanan").html(),
        $("#clone_biaya_lainya").html(),
        $("#clone_aksi").html(),
    ])
        .draw(false).node();

    $(row).attr("data-row-index", rowIndex);

    tablePPATTransaksi.rows().every(function (rowIdx) {
        $(this.node()).attr("data-row-index", rowIdx);
    });

    $(row).find('input[target="estimasi_waktu"]')
        .attr("target", "estimasi_waktu_" + rowIndex);

    $(row).find('input[target="biaya_layanan"]')
        .attr("target", "biaya_layanan_" + rowIndex);

    $(row).find(".input-biaya-lainya")
        .attr("target", "biaya_lainya_" + rowIndex);

    $(row).find("input[target='nilai_pajak']")
        .attr("target", "nilai_pajak_" + rowIndex);

    let pekerjaanSelect = $(row).find('select[target="pekerjaan_ppat_id_' + rowIndex + '"]');
    pekerjaanSelect.select2AjaxNew({
        url: baseUrl("/transaksi/getpekerjaan"),
        method: "POST",
        data: {
            jenis_pekerjaan: $("#jenis_pekerjaan").val(),
        },
    });

    pekerjaanSelect.off("change").on("change", function () {
        $.LoadingOverlay("show");
        let currentRow = $(this).closest("tr");
        let pekerjaan_id = $(this).val();

        currentPPATPekerjaanId = pekerjaan_id;

        currentRow.find('input[target="estimasi_waktu_' + rowIndex + '"]').val("");
        currentRow.find('input[target="biaya_layanan_' + rowIndex + '"]').val("");
        currentRow.find('input[target="biaya_lainya_' + rowIndex + '"]').val("");

        let kategoriSelect = currentRow.find('select[target="kategori_pekerjaan_ppat_id_' + rowIndex + '"]');
        kategoriSelect.val(null).trigger("change");

        if (pekerjaan_id) {
            kategoriSelect.select2AjaxNew({
                url: baseUrl("/transaksi/getkategori"),
                method: "POST",
                data: {
                    jenis_pekerjaan: $("#jenis_pekerjaan").val(),
                    pekerjaan_id: pekerjaan_id,
                },
            });
        }

        $.LoadingOverlay("hide");
    });

    let kategoriSelect = $(row).find('select[target="kategori_pekerjaan_ppat_id_' + rowIndex + '"]');
    kategoriSelect.select2({
        placeholder: "-- Pilih Kategori --",
    });

    kategoriSelect.off("change").on("change", function () {
        if (!$(this).val()) return;

        $.LoadingOverlay("show");
        currentPPATKategoriId = $(this).val();

        let currentRow = $(this).closest("tr");
        let pekerjaan_id = currentRow.find('select[target="pekerjaan_ppat_id_' + rowIndex + '"]').val();
        let kategori_pekerjaan_id = $(this).val();

        if (!pekerjaan_id) {
            $.LoadingOverlay("hide");
            swal("Peringatan!", "Silakan pilih pekerjaan terlebih dahulu", "error");
            $(this).val(null).trigger("change");
            return;
        }

        $.httpRequest({
            url: baseUrl("/transaksi/getdetailkategori"),
            method: "POST",
            data: JSON.stringify({
                jenis_pekerjaan: $("#jenis_pekerjaan").val(),
                pekerjaan_id: pekerjaan_id,
                kategori_pekerjaan_id: kategori_pekerjaan_id,
            }),
            contentType: "application/json",
            response: (res) => {
                $.LoadingOverlay("hide");

                if (res.statusCode == 200) {
                    currentRow.find('input[target="estimasi_waktu_' + rowIndex + '"]').val(res.data.estimasi_waktu);
                    currentRow.find('input[target="biaya_layanan_' + rowIndex + '"]').val(formatRupiah(res.data.harga));
                    currentRow.find('input[target="biaya_lainya_' + rowIndex + '"]').val("");
                    subTotalPPAT();
                } else {
                    swal("Peringatan!", res.message, "error");
                    $(this).val(null).trigger("change");
                }
            },
        });
    });
});

// => BTN TAMBAH LIST TRANSAKSI PPAT
$(document).on("click", ".btn-list-ppat", function () {
    let currentRow = $(this).closest("tr");
    let pekerjaan_id = currentRow
        .find('select[target^="pekerjaan_ppat_id_"]')
        .val();
    let kategoriPekerjaanId = currentRow
        .find('select[target^="kategori_pekerjaan_ppat_id_"]')
        .val();

    currentPPATPekerjaanId = pekerjaan_id;
    currentPPATKategoriId = kategoriPekerjaanId;
    if (!pekerjaan_id || pekerjaan_id == 0) {
        swal(
            "Peringatan !",
            "Silakan pilih pekerjaan terlebih dahulu.",
            "error"
        );
        return;
    }

    if (!kategoriPekerjaanId || kategoriPekerjaanId == 0) {
        swal(
            "Peringatan !",
            "Silahkan pilih kategori pekerjaan terlebih dahulu",
            "error"
        );
        return;
    }

    $.LoadingOverlay("show");
    $(".table-proses-ppat").DataTable().clear().destroy();

    if (pekerjaan_id && kategoriPekerjaanId) {
        $(".table-proses-ppat").DataTable({
            ajax: {
                url: baseUrl("/transaksi/proses-fetch"),
                headers: {
                    "X-XSRF-TOKEN": getCookie("XSRF-TOKEN"),
                },
                type: "POST",
                data: function (d) {
                    d.jenis_pekerjaan = $("#jenis_pekerjaan").val();
                    d.pekerjaan_id = pekerjaan_id;
                    d.transaksi_id = $("#transaksi_id").val();
                    d.kategori_pekerjaan_id = kategoriPekerjaanId;
                },
                dataSrc: function (json) {
                    if (json.data.length === 0) {
                        swal("Peringatan !", "Tidak ada data untuk ditampilkan.", "error");
                    }
                    return json.data || [];
                },
            },
            columns: [{
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                },
                width: "20px",
            },
            {
                data: "nama",
                width: "180px",
            },
            {
                data: "status_proses",
                render: function (data) {
                    return data == "1" ?
                        '<span class="badge badge-success">Sudah Valid</span>' :
                        '<span class="badge badge-danger">Belum Valid</span>';
                },
                width: "70px",
            },
            ],
            createdRow: function (row) {
                $("td", row).eq(0).addClass("text-center");
                $(".table-proses-ppat tbody").off().on("click", "tr", function () {
                    let table = $(".table-proses-ppat").DataTable();
                    let rowData = table.row(this).data();

                    if (rowData) {
                        $.LoadingOverlay("show");
                        // if (currentPPATKategoriId) {
                        //     $.LoadingOverlay("hide");
                        //     swal("Peringatan !", "Data tidak ditemukan", "error");
                        //     $("#modal-list-ppat").modal("hide");
                        //     return;
                        // }
                        $.httpRequest({
                            url: baseUrl("/transaksi/proses-ppat"),
                            method: "POST",
                            data: JSON.stringify({
                                jenis_pekerjaan: $("#jenis_pekerjaan").val(),
                                // pekerjaan_id: 1,
                                pekerjaan_id: currentPPATPekerjaanId,
                                kategori_pekerjaan_id: currentPPATKategoriId,
                                // kategori_pekerjaan_id: 1,
                                id: rowData.id,
                                transaksi_id: $("#transaksi_id").val(),
                            }),
                            contentType: "application/json",
                            response: (res) => {
                                $.LoadingOverlay("hide");

                                if (res.statusCode == 200) {
                                    let result = res.data;

                                    $('input[name="id_proses_ppat"]').val(result.proses.id);
                                    $('input[name="id_pekerjaan_ppat"]').val(result.pekerjaan.id);
                                    $('input[name="id_kategori_pekerjaan_ppat"]').val(result.kategori.id);
                                    $('input[name="detail_proses_pekerjaan_ppat"]').val(result.pekerjaan.nama);
                                    $('input[name="detail_proses_kategori_ppat"]').val(result.kategori.nama);
                                    $('input[name="detail_proses_nama_ppat"]').val(result.proses.nama);
                                    $("#detail_proses_ppat").text(result.proses.detail);
                                    if (result.prosesPPAT) {
                                        $('textarea[name="detail_proses_catatan_ppat"]').val(result.prosesPPAT.catatan);
                                        $("#validasi_ppat").prop("checked", result.prosesPPAT.isValidate == "1" || result.prosesPPAT.isValidate == 1);
                                    } else {
                                        $('textarea[name="detail_proses_catatan_ppat"]').val("");
                                        $("#validasi_ppat").prop("checked", false);
                                    }

                                    createAtributTogglesPPAT(result);
                                    $("#modal-detal-proses-ppat").modal("show");

                                } else {
                                    swal("Peringatan!", res.message, "error");
                                }
                            },
                        });
                    }

                    // -- #simpan-proses-ppat old location

                    $("#modal-list-ppat").modal("hide");
                });
            },
        });
    }

    $.LoadingOverlay("hide");
    $("#modal-list-ppat").modal("show");


});

// => BTN STATUS PPAT
$('#modal-status-ppat .select2').select2();

$('#modal-status-ppat .select2').change(function () {
    const value = $(this).val();

    if (value != 3) {
        $('.container-status-ppat-selesai').addClass('d-none');
        $('#modal-status-ppat input[name="setter_judul_ppat"]').val("");
        $('#modal-status-ppat input[name="setter_no_akta_ppat"]').val("");
        $('#modal-status-ppat input[name="setter_tgl_akta_ppat"]').val("");
        return;
    }

    $('.container-status-ppat-selesai').removeClass('d-none');
});

$(document).on("click", ".btn-status-ppat", function () {
    let row = $(this).closest("tr");
    let pekerjaanId = row.find('select[target^="pekerjaan_ppat_id_"]')
        .val();
    let kategoriPekerjaanId = row.find('select[target^="kategori_pekerjaan_ppat_id_"]')
        .val();

    const elStatus = row.find("input[name='status_ppat[]']");
    const elJudul = row.find("input[name='judul_ppat[]']");
    const elNoAkta = row.find("input[name='no_akta_ppat[]']");
    const elTglAkta = row.find("input[name='tgl_akta_ppat[]']");

    if (!pekerjaanId || pekerjaanId == 0) {
        swal(
            "Peringatan !",
            "Silakan pilih pekerjaan terlebih dahulu.",
            "error"
        );
        return;
    }

    if (!kategoriPekerjaanId || kategoriPekerjaanId == 0) {
        swal(
            "Peringatan !",
            "Silahkan pilih kategori pekerjaan terlebih dahulu",
            "error"
        );
        return;
    }

    // -- getter data
    $('#modal-status-ppat input[name="setter_judul_ppat"]').val(elJudul.val());
    $('#modal-status-ppat input[name="setter_no_akta_ppat"]').val(elNoAkta.val());
    $('#modal-status-ppat input[name="setter_tgl_akta_ppat"]').val(elTglAkta.val());
    $('#modal-status-ppat select[name="setter_status_ppat"]').val(elStatus.val())
        .trigger('change');

    // -- setter data
    $('#simpan-status-ppat').off()
        .on("click", function () {
            // -- get value
            const status = $('#modal-status-ppat select[name="setter_status_ppat"]').val();
            const judul = $('#modal-status-ppat input[name="setter_judul_ppat"]').val();
            const noAkta = $('#modal-status-ppat input[name="setter_no_akta_ppat"]').val();
            const tglAkta = $('#modal-status-ppat input[name="setter_tgl_akta_ppat"]').val();

            // -- set value
            elStatus.val(status);
            elJudul.val(judul);
            elNoAkta.val(noAkta);
            elTglAkta.val(tglAkta);

            $('#modal-status-ppat').modal('hide');
        });

    // -- show modal
    $('#modal-status-ppat').modal('show');
});

function removeRowTransaksi(el) {
    swal({
        title: "Peringatan!",
        text: "Anda akan menghapus item yang dipilih.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        cancelButtonText: "Tidak",
        confirmButtonText: "Ya",
    }).then(function () {
        const elx = $(el).closest("tr");
        const row = tablePPATTransaksi.row(elx);
        const key = row.index();

        const pekerjaanId = $(elx).find("select[name='pekerjaan[pekerjaan_ppat_id][]']")
            .val();
        const kategoriId = $(elx).find("select[name='kategori_pekerjaan[kategori_pekerjaan_ppat_id][]']")
            .val();

        if (!pekerjaanId || !kategoriId) {
            row.remove().draw();
            return;
        }

        // removing cookie
        $.LoadingOverlay("show");
        $.httpRequest({
            url: baseUrl(`/transaksi/ppat/cookie-remove/${key}/${pekerjaanId}/${kategoriId}`),
            method: "DELETE",
            contentType: "application/json",
            response: (res) => {
                $.LoadingOverlay("hide");

                // removing
                row.remove().draw();
                pajakListData.splice(key, 1);

                subTotalPPAT();
            },
        });
    });
}

// ---------------------------------------------------------------------------------------------------------------------------
// => Kalkulator Pajak Section
$(document).on("click", "#btn_kalkulator_pajak", function () {
    let row = $(this).closest("tr");
    let rowIndex = row.data("row-index");

    // set hide all on pajak feature
    $("#checked-skb").hide();
    $("#checked-skb-label").hide();
    $(".besaran_tidak_kena_pajak").addClass("d-none");

    $.httpRequest({
        url: baseUrl("/transaksi/getPajak"),
        method: "POST",
        response: function (res) {
            if (res.data != null) {
                let data = res.data;

                let matchingData = data.find((d) => d.id == rowIndex);
                if (matchingData) {
                    $("#njop").val(
                        formatRupiah(matchingData.acuanNilaiPajak.toString(), "Rp")
                    );
                    $("#n").val(
                        formatRupiah(matchingData.nilaiPengurang.toString(), "Rp")
                    );

                    // -- label pihak
                    let labelPihakPertama = '';
                    let labelPihakKedua = '';

                    if (matchingData.jenis_pajak == 1) {
                        labelPihakPertama = 'Penerima';
                        labelPihakKedua = 'Pewaris';
                    }

                    if (matchingData.jenis_pajak == 2) {
                        labelPihakPertama = 'Pemberi';
                        labelPihakKedua = 'Penerima';
                    }

                    if (matchingData.jenis_pajak == 3 || matchingData.jenis_pajak == 4) {
                        labelPihakPertama = 'Penjual';
                        labelPihakKedua = 'Pemberi';
                    }

                    if (matchingData.jenis_pajak == 5 || matchingData.jenis_pajak == 6) {
                        labelPihakPertama = 'Penerima Hak';
                        labelPihakKedua = 'Pemberi Hak';
                    }

                    $("#label-pihak-pertama").text(
                        `${labelPihakPertama}: Rp. ` +
                        formatRupiah(
                            matchingData.pihak_pertama.toString(),
                            "Rp"
                        )
                    );

                    $("#label-pihak-kedua").text(
                        `${labelPihakKedua}: Rp. ` +
                        formatRupiah(
                            matchingData.pihak_kedua.toString(),
                            "Rp"
                        )
                    );

                    if (matchingData.pihak_kedua > 0) {
                        $("#additional-table").removeClass("d-none");
                    } else {
                        $("#additional-table").addClass("d-none");
                    }

                    $('select[target="jenis_pajak"]').select2AjaxNew({
                        url: baseUrl("/transaksi/getjenispajak"),
                        method: "POST",
                        data: {
                            selected: parseInt(matchingData.jenis_pajak),
                        },
                    });

                    // -- balik nama waris, biaya hibah
                    if (matchingData.jenis_pajak == 1 || matchingData.jenis_pajak == 2) {
                        $("#checked-skb").show().prop('checked', !(matchingData.pihak_kedua > 0));
                        $("#checked-skb-label").show();
                    }

                    // -- biaya jasa aphb
                    if (matchingData.jenis_pajak == 5) {
                        $(".besaran_tidak_kena_pajak").removeClass("d-none").show()
                        $("input[id='objek_tidak_kena_pajak']").val(matchingData.besaran_tidak_kena_pajak.toString(), "Rp")
                    }

                    $("#table-result").removeClass("d-none");
                } else {
                    $("#njop").val("");
                    $("#n").val("");
                    $("#table-result").addClass("d-none");
                }

                // -- in here set pajak list
                pajakListData = res.data;

            } else {
                $("#njop").val("");
                $("#n").val("");
                $("#table-result").addClass("d-none");
            }
        },
    });

    $("#njop").val("");
    $("#n").val("");

    let pekerjaan_id = row.find('select[name="pekerjaan[pekerjaan_ppat_id][]"]').val();
    let kategori_pekerjaan_id = row.find('select[name="kategori_pekerjaan[kategori_pekerjaan_ppat_id][]"]').val();

    // Validasi jika pekerjaan atau kategori pekerjaan belum dipilih
    if (!pekerjaan_id || pekerjaan_id == 0) {
        swal(
            "Peringatan",
            "Pastikan Memilih Pekerjaan Terlebih Dahulu",
            "error"
        );
        return;
    }

    if (!kategori_pekerjaan_id || kategori_pekerjaan_id == 0) {
        swal(
            "Peringatan",
            "Pastikan Memilih Kategori Terlebih Dahulu",
            "error"
        );
        return;
    }

    // Memilih jenis pajak dengan select2
    $('select[target="jenis_pajak"]').select2AjaxNew({
        url: baseUrl("/transaksi/getjenispajak"),
        method: "POST",
    });

    $('select[target="jenis_pajak"]').off().on("change", function () {
        $.LoadingOverlay("show");

        if ($(this).val() == "5") {
            $(".besaran_tidak_kena_pajak").removeClass("d-none");
        } else {
            $(".besaran_tidak_kena_pajak").addClass("d-none");
        }

        tampilHitungPajak();

        $.LoadingOverlay("hide");
    });

    if ($('select[target="jenis_pajak"]').val() == 1) {
        $("#table-result").removeClass("d-none");
    }

    // Perbaikan event keyup untuk objek tidak kena pajak
    $("#njop, #n, input[id='objek_tidak_kena_pajak']").off().on("keyup", function () {
        tampilHitungPajak();
    });

    $("#checked-skb").off().on("click", function () {
        tampilHitungPajak();
    });

    $("#simpan-pajak").off().on("click", function () {
        let isChecked = $("#checked-skb").is(":checked");
        let selectedValue = $('select[target="jenis_pajak"]').val();
        let njop = parseFloat($("#njop").val().replace(/\./g, "").replace(",", "."));
        let nilaiPengurang = parseFloat($("#n").val().replace(/\./g, "").replace(",", "."));
        let nilaiPajakTidakKena =
            parseFloat($('input[id="objek_tidak_kena_pajak"]').val().replace(/\./g, "").replace(",", ".") || 0
            );

        let hasilHitungPihakPertama = 0;
        let hasilHitungPihakKedua = 0;



        if (selectedValue == 0) {
            swal(
                "Peringatan!",
                "Anda harus memilih jenis pajak terlebih dahulu",
                "error"
            );
            return;
        }

        // => Proses Perhitungan Rumus Pajak Untuk Disimpan
        if (selectedValue == 1) {
            hasilHitungPihakPertama = (njop - nilaiPengurang) * 0.05;

            if (!isChecked) {
                hasilHitungPihakKedua = 0.025 * njop;
            }
        } else if (selectedValue == 2) {
            hasilHitungPihakPertama = 0.025 * njop;

            if (!isChecked) {
                hasilHitungPihakKedua = (njop - nilaiPengurang) * 0.05;
            }
        } else if (selectedValue == 2 || selectedValue == 3 || selectedValue == 4) {
            hasilHitungPihakPertama = 0.025 * njop;
            hasilHitungPihakKedua = (njop - nilaiPengurang) * 0.05;
        } else if (selectedValue == 5) {
            hasilHitungPihakPertama = ((njop - nilaiPengurang) - nilaiPajakTidakKena) * 0.05;
            hasilHitungPihakPertama = hasilHitungPihakPertama < 0 ? 0 : hasilHitungPihakPertama;
            hasilHitungPihakKedua = njop * 0.025;
        }

        $.httpRequest({
            url: baseUrl("/transaksi/simpan-pajak"),
            method: "POST",
            data: JSON.stringify({
                id: rowIndex,
                jenis_pajak: selectedValue,
                acuan_nilai_pajak: njop,
                nilaiPengurang: nilaiPengurang,
                pihak_pertama: hasilHitungPihakPertama,
                pihak_kedua: hasilHitungPihakKedua,
                nilai_tidak_kena_pajak: nilaiPajakTidakKena
            }),
            contentType: "application/json",
            response: function (res) {
                if (res.statusCode == 200) {
                    setListPajak({
                        id: rowIndex,
                        jenis_pajak: selectedValue,
                        acuan_nilai_pajak: njop,
                        nilaiPengurang: nilaiPengurang,
                        pihak_pertama: hasilHitungPihakPertama,
                        pihak_kedua: hasilHitungPihakKedua,
                    }, rowIndex);

                    // -- in here set calc ppat
                    subTotalPPAT();

                    swal("Sukses!", res.message, "success");
                } else {
                    // Tampilkan pesan error
                    swal("Gagal!", res.message, "error");
                }
                // Tutup modal
                $("#modal-kalkulator-pajak").modal("hide");
            },
        });
    });

    $.LoadingOverlay("show");
    $("#modal-kalkulator-pajak").modal("show");
    $.LoadingOverlay("hide");
});

function updateDisplayValues(value) {
    $("#transaksi_sub_total").text("Rp." + formatRupiah(value));
    $("#transaksi_total").text("Rp." + formatRupiah(value));
    $("#sisa_pembayaran").val(formatRupiah(value));
}

function tampilHitungPajak() {

    // => Set Default Hide Semua
    $("#checked-skb").hide();
    $("#checked-skb-label").hide();

    let njop = parseFloat($("#njop").val().replace(/\./g, "").replace(",", ".")) || 0;
    let nilaiPengurang = parseFloat($("#n").val().replace(/\./g, "").replace(",", ".")) || 0;
    if (njop < nilaiPengurang) {
        $("#n").val(0)
        nilaiPengurang = 0;
        swal('Maaf!', 'Nilai Pengurang Tidak Boleh Lebih dari Nilai NJOP', 'error');
    }
    let nilaiTidakKenaPajak =
        parseFloat($("input[id='objek_tidak_kena_pajak']").val().replace(/\./g, "").replace(",", ".")) || 0;

    let selectedValue = $('select[target="jenis_pajak"]').val();

    $('select[target="jenis_pajak"]').on('change', function () {
        resetFormPajak();
    })

    let isChecked = $("#checked-skb").is(":checked");

    let hasilHitungPihakPertama = 0;
    let hasilHitungPihakKedua = 0;

    if (selectedValue == 1) {
        hasilHitungPihakPertama = (njop - nilaiPengurang) * 0.05;
        if (!isChecked) {
            hasilHitungPihakKedua = 0.025 * njop;
        }

        $("#checked-skb").show();
        $("#checked-skb-label").show();

        $(".besaran_tidak_kena_pajak").addClass("d-none");
        $("#label-pihak-pertama").text("Penerima: Rp. " + formatRupiah(hasilHitungPihakPertama.toString(), "Rp"));
        if (!isChecked) {
            $("#label-pihak-kedua").text("Pewaris: Rp. " + formatRupiah(hasilHitungPihakKedua.toString(), "Rp"));
            $("#additional-table").removeClass("d-none");
        } else {
            $("#label-pihak-kedua").text("");
            $("#additional-table").addClass("d-none");
        }
    } else if (selectedValue == 2) {
        hasilHitungPihakPertama = 0.025 * njop;
        if (!isChecked) {
            hasilHitungPihakKedua = (njop - nilaiPengurang) * 0.05;
        }

        $("#checked-skb").show();
        $("#checked-skb-label").show();

        $(".besaran_tidak_kena_pajak").addClass("d-none");
        $("#label-pihak-pertama").text("Pemberi: Rp. " + formatRupiah(hasilHitungPihakPertama.toString(), "Rp"));
        if (!isChecked) {
            $("#label-pihak-kedua").text("Penerima: Rp. " + formatRupiah(hasilHitungPihakKedua.toString(), "Rp"));
            $("#additional-table").removeClass("d-none");
        } else {
            $("#label-pihak-kedua").text("");
            $("#additional-table").addClass("d-none");
        }
    } else if (selectedValue == 3 || selectedValue == 4) {
        hasilHitungPihakPertama = 0.025 * njop;
        hasilHitungPihakKedua = (njop - nilaiPengurang) * 0.05;

        $(".besaran_tidak_kena_pajak").addClass("d-none");
        $("#checked-skb").hide();
        $("#checked-skb-label").hide();
        $("#label-pihak-pertama").text(
            "Penjual: Rp. " +
            formatRupiah(hasilHitungPihakPertama.toString(), "Rp")
        );
        $("#label-pihak-kedua").text(
            "Pembeli: Rp. " +
            formatRupiah(hasilHitungPihakKedua.toString(), "Rp")
        );
        $("#additional-table").removeClass("d-none");
    }
    else if (selectedValue == 5) {
        $(".besaran_tidak_kena_pajak").removeClass("d-none");

        // rumus sesuai persis seperti di kertas
        let nilaiKenaPajak = (njop - nilaiPengurang) - nilaiTidakKenaPajak;
        nilaiKenaPajak = nilaiKenaPajak < 0 ? 0 : nilaiKenaPajak;

        hasilHitungPihakPertama = nilaiKenaPajak * 0.05;
        hasilHitungPihakKedua = njop * 0.025;

        $("#checked-skb").hide();
        $("#checked-skb-label").hide();
        $("#label-pihak-pertama").text(
            "Penerima Hak : Rp. " +
            formatRupiah(hasilHitungPihakPertama, "Rp")
        );
        $("#label-pihak-kedua").text(
            "Pemberi Hak : Rp. " +
            formatRupiah(hasilHitungPihakKedua, "Rp")
        );
        $("#additional-table").removeClass("d-none");
    }


    $("#table-result").removeClass("d-none");
}

$("#njop, #n", "#objek-tidak-kena-pajak").on("input", function () {
    tampilHitungPajak();
});

$("#checked-skb").on("change", function () {
    tampilHitungPajak();
});

function resetFormPajak() {
    $("#njop").val("");
    $("#n").val("");
    $("#table-result").addClass("d-none");
}

function setListPajak(data, rowIndex) {
    let i = pajakListData.findIndex((e) => e.id == rowIndex);
    i < 0 ? pajakListData.push(data) : (pajakListData[i] = data);
}

// ----------------------------------------------------------------------------------------------------------------------------
// => End Kalkulator Pajak Section

$("#simpan-proses-ppat").on("click", function () {
    const atributData = {};
    $("input[name='atribut']").each(function () {
        const atributId = $(this).val();
        const isChecked = $(this).is(":checked") ? 1 : 0;
        atributData[atributId] = isChecked;
    });

    const requestData = {
        prosesId: $("input[name='id_proses_ppat']").val(),
        pekerjaanId: $('input[name="id_pekerjaan_ppat"]').val(),
        kategoriPekerjaanId: $('input[name="id_kategori_pekerjaan_ppat"]').val(),
        pekerjaanNama: $("input[name='detail_proses_pekerjaan_ppat']").val(),
        kategoriNama: $("input[name='detail_proses_kategori_ppat']").val(),
        prosesNama: $("input[name='detail_proses_nama_ppat']").val(),
        atribut: atributData,
        catatan: $("#proses_catatan_ppat").val(),
        isValidate: $("#validasi_ppat").is(":checked") ? "1" : "0"
    };

    $.httpRequest({
        url: baseUrl("/transaksi/validasi-ppat"),
        method: "POST",
        data: JSON.stringify(requestData),
        contentType: "application/json",
        response: function (res) {
            if (res.statusCode === 200) {
                const requestData = {
                    jenis_pekerjaan: $("#jenis_pekerjaan").val(),
                    pekerjaan_id: currentPPATPekerjaanId,
                    kategori_pekerjaan_id: currentPPATKategoriId,
                    id: $("input[name='id_proses_ppat']").val(),
                    transaksi_id: $("#transaksi_id").val(),
                };

                $.httpRequest({
                    url: baseUrl("/transaksi/proses-ppat"),
                    method: "POST",
                    data: JSON.stringify(requestData),
                    contentType: "application/json",
                    response: function (fetchRes) {
                        if (fetchRes.statusCode === 200) {
                            updateModalContentPPAT(fetchRes.data);
                            createAtributTogglesPPAT(fetchRes.data);

                            swal("Sukses", fetchRes.message, "success");
                            $("#modal-detal-proses-ppat").modal("hide");
                            $("#modal-detal-proses").modal("hide");
                        } else {
                            swal("Peringatan!", fetchRes.message, "error");
                        }
                    }
                });
            } else {
                swal("Gagal", "Gagal saat menyimpan data", "error");
            }
        }
    });
});

$('.currency-input')
    .off("input")
    .on("input", function () {
        const inputField = $(this);
        const value = inputField.val();

        var numericVal = value.replace(/[^0-9]/g, "");
        var formattedVal = formatRupiah(numericVal);

        if (formattedVal !== value) {
            inputField.val(formattedVal);
        }
    });

$('#form-edit-riwayat-bayar').formSubmit((res) => {
    if (res.statusCode == 200) {
        // -- in here set jumlah bayar
        let totalBayar = $('#jumlah_pembayaran').val();
        let jumlahBefore = $('#form-edit-riwayat-bayar .dibayar_value').val();
        let jumlahAfter = $('#form-edit-riwayat-bayar input[name="jumlah_dibayar"]').val();

        jumlahAfter = formatRupiahToNumber(jumlahAfter);
        totalBayar = parseInt(totalBayar) + parseInt(jumlahAfter) - parseInt(jumlahBefore);

        $('#jumlah_pembayaran').val(totalBayar);

        // calculate
        if (sesiSaatIni == 1) {
            subTotalNotaris();
        } else if (sesiSaatIni == 2) {
            subTotalPPAT();
        }

        swal("Sukses", res.message, "success");
        $("#modal-edit-riwayat-bayar").modal("hide");
    }
});
