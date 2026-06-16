// let table = $(".pendapatan-table").DataTable({
//     ajax: {
//         url: baseUrl("/laporan/pendapatan/fetch"),
//         headers: {
//             'X-XSRF-TOKEN': getCookie('XSRF-TOKEN')
//         },
//         dataSrc: "data",
//         type: "POST",
//         data: function (form) {
//             form.tanggal_awal = $("#tanggal_awal").val();
//             form.tanggal_akhir = $("#tanggal_akhir").val();
//         }
//     },
//     processing: false,
//     serverSide: true,
//     autoWidth: false,
//     responsive: false,
//     ordering: false,
//     searching: false,
//     info: true,
//     sorting: false,
//     paging: true,
//     columns: [
//         {
//             name: "",
//             render: function (data, i, row, meta) {
//                 return meta.row + 1;
//             },
//             width: "20px",
//             searchable: false,
//             orderable: false,
//         },
//         {
//             data: 'tanggal',
//             name: 'tanggal'
//         },
//         {
//             data: 'penghasilan',
//             name: 'penghasilan',
//             render: function(data, type, row) {
//                 return data ? 'Rp. ' + numberFormat(data) : 'Rp. 0';
//             }
//         },
//         {
//             data: 'pengeluaran',
//             name: 'pengeluaran',
//             render: function(data, type, row) {
//                 return data ? 'Rp. ' + numberFormat(data) : 'Rp. 0';
//             }
//         },
//         {
//             data: 'pendapatan',
//             name: 'pendapatan',
//             render: function(data, type, row) {
//                 return data ? 'Rp. ' + numberFormat(data) : 'Rp. 0';
//             }
//         },
//         {
//             data: 'saldo',
//             name: 'saldo',
//             render: function(data, type, row) {
//                 return data ? 'Rp. ' + numberFormat(data) : 'Rp. 0';
//             }
//         }
//     ],
//     drawCallback: (settings) => {
//         let response = settings.json;
//         if (response) {
//             $("#total-penghasilan").text(response.total_penghasilan);
//             $("#total-pengeluaran").text(response.total_pengeluaran);
//             $("#total-pendapatan").text(response.total_pendapatan);
//         }
//     }
// });

let table = $(".pendapatan-table").DataTable({
    ajax: {
        url: baseUrl("/laporan/pendapatan/fetch"),
        headers: {
            'X-XSRF-TOKEN': getCookie('XSRF-TOKEN')
        },
        dataSrc: "data",
        type: "POST",
        data: function (form) {
            form.tanggal_awal = $("#tanggal_awal").val();
            form.tanggal_akhir = $("#tanggal_akhir").val();
            form.jenis_pembayaran = $("#jenis_pembayaran_filter").val();
        }
    },
    processing: false,
    serverSide: true,
    autoWidth: false,
    responsive: false,
    ordering: false,
    searching: false,
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
            searchable: false,
            orderable: false,
        },
        {
            data: 'tanggal',
            name: 'tanggal',
            render: function (data) {
                return data ? data : '-'; // Default jika tanggal kosong
            }
        },
        {
            data: 'total_penghasilan',
            name: 'penghasilan',
            render: function (data) {
                return data ? 'Rp. ' + numberFormat(data) : 'Rp. 0'; // Total penghasilan per tanggal
            }
        },
        {
            data: 'total_pengeluaran',
            name: 'pengeluaran',
            render: function (data) {
                return data ? 'Rp. ' + numberFormat(data) : 'Rp. 0'; // Total pengeluaran per tanggal
            }
        },
        {
            data: 'total_pendapatan',
            name: 'pendapatan',
            render: function (data) {
                return data ? 'Rp. ' + numberFormat(data) : 'Rp. 0'; // Total pendapatan per tanggal
            }
        },
        {
            data: 'total_saldo',
            name: 'saldo',
            render: function (data) {
                return data ? 'Rp. ' + numberFormat(data) : 'Rp. 0'; // Total saldo per tanggal
            }
        }
    ],
    drawCallback: (settings) => {
        let response = settings.json;
        if (response) {
            // Update total keseluruhan
            $("#total-penghasilan").text(response.total_penghasilan);
            $("#total-pengeluaran").text(response.total_pengeluaran);
            $("#total-pendapatan").text(response.total_pendapatan);
        }
    }
});

function numberFormat(number) {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

$("#tanggal_awal, #tanggal_akhir").change(function () {
    table.ajax.reload();
});

// Tambahkan tombol ekspor ke Excel
$("#export-to-excel").click(function () {
    // Ambil data dari DataTable
    let tableData = table.rows().data().toArray();

    // Buat array untuk menyimpan data yang akan diekspor
    let exportData = [];

    // Tambahkan header
    exportData.push([
        "No",
        "Tanggal",
        "Penghasilan",
        "Pengeluaran",
        "Pendapatan",
        "Saldo"
    ]);

    // Tambahkan data ke array
    tableData.forEach((row, index) => {
        exportData.push([
            index + 1,
            row.tanggal,
            row.total_penghasilan ? 'Rp. ' + numberFormat(row.total_penghasilan) : 'Rp. 0',
            row.total_pengeluaran ? 'Rp. ' + numberFormat(row.total_pengeluaran) : 'Rp. 0',
            row.total_pendapatan ? 'Rp. ' + numberFormat(row.total_pendapatan) : 'Rp. 0',
            row.total_saldo ? 'Rp. ' + numberFormat(row.total_saldo) : 'Rp. 0'
        ]);
    });

    // Buat worksheet dari data
    let ws = XLSX.utils.aoa_to_sheet(exportData);

    // Buat workbook dan tambahkan worksheet
    let wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Pendapatan Data");

    // Simpan file Excel
    XLSX.writeFile(wb, "Pendapatan_Data.xlsx");
});

$(document).ready(function () {
    // Load jenis pembayaran options
    $.ajax({
        url: baseUrl("/laporan/pendapatan/jenis-pembayaran"),
        type: "GET",
        success: function (response) {
            if (response && response.data) {
                let jenisPembayaranFilter = $("#jenis_pembayaran_filter");
                response.data.forEach(item => {
                    jenisPembayaranFilter.append(
                        `<option value="${item.id}">${item.nama}</option>`
                    );
                });
            }
        }
    });

    // Reinitialize table on filter change
    $("#jenis_pembayaran_filter").change(function () {
        table.ajax.reload(); // Reload DataTable when filter changes
    });
});

