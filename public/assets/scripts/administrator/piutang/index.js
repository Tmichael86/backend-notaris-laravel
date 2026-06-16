$(document).ready(function() {
    let table = $(".table").DataTable({
        ajax: {
            url: baseUrl("/piutang-fetch"),
            headers: {
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN')
            },
            dataSrc: "data",
            type: "POST",
            data: function (form) {
                form.tanggal_awal = $("#tanggal_awal").val();
                form.jenis_pekerjaan_id = $('#filter_jenis_pekerjaan_id').val();
                form.tanggal_akhir = $("#tanggal_akhir").val();
            },
            complete: function() {
                // Update total piutang setelah DataTables selesai memuat data
                $.ajax({
                    url: baseUrl("/piutang/getTotalPiutang"),
                    headers: {
                        'X-XSRF-TOKEN': getCookie('XSRF-TOKEN')
                    },
                    type: "POST",
                    data: {
                        tanggal_awal: $("#tanggal_awal").val(),
                        jenis_pekerjaan_id: $('#filter_jenis_pekerjaan_id').val(),
                        tanggal_akhir: $("#tanggal_akhir").val()
                    },
                    success: function(response) {
                        $('#total_piutang_footer').html(response.totalPiutang);
                    }
                });
            }
        },
        autoWidth: false,
        responsive: true,
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
            { data: "no_akta" },
            { data: "pemohon_nik" },
            { data: "pemohon_nama" },
            { data: "jenis_pekerjaan_nama" },
            { data: "pekerjaan_nama" },
            { data: "kategori_nama" },
            { data: "total" },
            { data: "total_piutang" },
            { data: "petugas_nama" }
        ]
    });

    $('select[target="filter_jenis_pekerjaan_id"]').select2AjaxNew({
        url: baseUrl('/piutang/select2/getjenispekerjaan'),
        method: "POST",
    });

    $('#filter_jenis_pekerjaan_id').on('change', function() {
        $.LoadingOverlay("show");
        table.ajax.reload();
        $.LoadingOverlay("hide");
    });

    $('#tanggal_awal').on('change', function() {
        $.LoadingOverlay("show");
        table.ajax.reload();
        $.LoadingOverlay("hide");
    });

    $('#tanggal_akhir').on('change', function() {
        $.LoadingOverlay("show");
        table.ajax.reload();
        $.LoadingOverlay("hide");
    });

    $("#export-to-excel").click(function() {
        // Ambil data dari DataTable
        let tableData = table.rows().data().toArray();

        // Buat array untuk menyimpan data yang akan diekspor
        let exportData = [];

        // Tambahkan header
        exportData.push([
            "No",
            "No Akta",
            "NIK Pemohon",
            "Nama Pemohon",
            "Jenis Pekerjaan",
            "Nama Pekerjaan",
            "Kategori",
            "Total",
            "Total Piutang",
            "Nama Petugas"
        ]);

        // Tambahkan data ke array
        tableData.forEach((row, index) => {
            exportData.push([
                index + 1,
                row.no_akta,
                row.pemohon_nik,
                row.pemohon_nama,
                row.jenis_pekerjaan_nama,
                row.pekerjaan_nama,
                row.kategori_nama,
                row.total,
                row.total_piutang,
                row.petugas_nama
            ]);
        });

        // Buat worksheet dari data
        let ws = XLSX.utils.aoa_to_sheet(exportData);

        // Buat workbook dan tambahkan worksheet
        let wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Piutang Data");

        // Simpan file Excel
        XLSX.writeFile(wb, "Piutang_Data.xlsx");
    });
});
