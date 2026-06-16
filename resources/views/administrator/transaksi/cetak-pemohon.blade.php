<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanda Terima</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 210mm;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #000;
        }
        .header {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .sub-header {
            text-align: center;
            font-size: 12px;
            margin-bottom: 20px;
        }
        .sub-header-2 {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .content {
            font-size: 14px;
            line-height: 1.6;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .underline {
            border-bottom: 1px solid #000;
            flex: 1;
            margin-left: 5px;
        }
        .table-container {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
        /* Kolom yang akan digunakan untuk mengisi data manual */
        .large-column {
            height: 300px; /* Mengatur tinggi agar lebih besar untuk diisi manual */
            vertical-align: top;
            padding: 10px;
            colspan: 2; /* Membuat kolom melebar dua kolom */
        }
        .signature-container {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        /* Bagian Keperluan */
        .purpose {
            width: 30%;
        }
        .purpose .underline {
            margin-bottom: 10px;
        }
        .purpose .underline-space {
            height: 20px; /* Atur tinggi underline untuk membuat ruang manual */
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
        }
        /* Bagian Tanda Tangan */
        .signature {
            text-align: right;
            width: 50%;
            font-size: 14px;
        }
        .signature .date {
            margin-bottom: 60px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        YUDHANA EKO PRASETYO, SH., M.Kn.<br>
        Notaris - PPAT Kabupaten Blitar
    </div>
    <div class="sub-header">
        SK. Menteri Hukum dan HAM RI Nomor: AHU-01155. AH. 02. 01 Tahun 2017<br>
        Jalan Raya Tumpang RT. 01 RW. 05 Talun - Blitar<br>
        Telp. (0342) 441306 / 082126787808 / 085746002444
    </div>
    <div class="sub-header-2">
        TANDA TERIMA
    </div>

    <div class="content">
        <div class="row">
            <span>Telah diterima:</span>
            <span class="underline">{{ $pemohon_nama }}</span>
        </div>
        <div class="row">
            <span>Nomor:</span>
            <span class="underline">{{ $no_akta }}</span>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        {{-- <th style="width: 10%;">NO</th> --}}
                        <th>URAIAN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        {{-- <td style="text-align: center">1</td> --}}
                        <td colspan="2" class="large-column">{!! $uraian !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="signature-container">
            <!-- Bagian Keperluan -->
            <div class="purpose">
                <span>Keperluan:</span>
                <div class="underline-space">{!! $keperluan !!}</div>
            </div>
            <!-- Bagian Tanda Tangan -->
            <div class="signature">
                <div class="date">
                    Blitar, {{ $tanggalSekarang }}
                </div>
                <div>
                    <span>(</span><span class="underline">{{ $petugas_nama }}</span><span>)</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        window.print();
        window.onafterprint = function() {
            window.close();
        };
    };
</script>

</body>
</html>
