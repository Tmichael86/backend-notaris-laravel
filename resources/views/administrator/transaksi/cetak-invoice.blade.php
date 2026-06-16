<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            -webkit-print-color-adjust: exact !important;
        }

        .container {
            width: 210mm;
            margin: 0 auto;
            padding: 10px;
        }

        .header {
            text-align: center;
            font-size: 16px;
        }

        .sub-header-1 {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            margin-top: 5px;
            /* Reduced margin for better spacing */
        }

        .sub-header {
            text-align: left;
            font-size: 12px;
            margin-bottom: 10px;
        }

        hr {
            border: 1px solid black;
            margin: 10px 0;
        }

        .content {
            font-size: 16px;
            margin-bottom: 18px;
        }

        .content p {
            margin: 6px 0px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        table thead,
        .total {
            background-color: #c0dfa8;
        }

        .total {
            text-align: left;
        }

        .note {
            font-size: 16px;
            background-color: #fb7d7d;
            font-style: italic;
            font-weight: bold;
        }

        .sub-header table {
            margin-bottom: 0px;
        }

        .sub-header table td {
            padding: 4px 0px;
            font-weight: bold;
            font-size: 13px;
            text-transform: uppercase;
        }

        .signature-container {
            display: flex;
            justify-content: end;
            margin-top: 40px;
        }

        .signature {
            width: 180px;
            text-align: center;
            font-size: 16px;
        }

        .signature .head {
            margin-bottom: 90px;
        }

        .signature .name {
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .signature .devisi {
            font-size: 14px;
            margin-top: 10px;
        }

        .header-main {
            text-align: center;
        }

        h1,
        h2,
        h3 {
            margin: 5px 0px;
        }

        .list-biaya-pajak {
            margin: 0px 0px;
            padding-left: 20px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header-main">
            <h1>NOTARIS/PPAT</h1>
            <h2>YUDHANA EKO PRASETYO, SH.,M.Kn</h2>
        </div>
        <div class="header">
            <div>
                SK. MENTERI HUKUM DAN HAK ASASI MANUSIA REPUBLIK INDONESIA
            </div>
            <div>
                Nomor : AHU-01155.AH.02.01 Tahun 2017, Tanggal 15 Desember 2017
            </div>
            <div>
                SK. MENTERI AGRARIA DAN TATA RUANG / KEPALA BADAN PERTAHANAN NASIONAL
            </div>
            <div>
                Nomor : 726/SK - 400.HR.03.01/XII/2019, Tanggal 31 Desember 2019
            </div>
            <div>
                {{ $konfigurasi->alamat ?? 'Jalan Raya Tumpang RT 003, RW 005, Kec Talun' }}
            </div>
            <div>
                Telp. {{ $konfigurasi->telepon_rumah ?? '(0342) 441306' }},
                {{ $konfigurasi->telepon_pertama ?? '082126787808' }},
                {{ $konfigurasi->telepon_kedua ?? '085815711117' }}
                Email : {{ $konfigurasi->email ?? 'yudhana.notaris@gmail.com' }}
            </div>
        </div>
        <div class="sub-header-1">
            KABUPATEN BLITAR
        </div>
        <hr> <!-- Added horizontal line -->
        <div class="sub-header">
            <table>
                <tr>
                    <td style="width: 100px; border: none;">PERIHAL</td>
                    <td style="border: none;">: Tagihan</td>
                </tr>
                <tr>
                    <td style="width: 100px; border: none;">KEPADA</td>
                    <td style="border: none;">: {{ $namaPemohon }}</td>
                </tr>
                <tr>
                    <td style="width: 100px; border: none;">DARI</td>
                    <td style="border: none;">:
                        {{ $konfigurasi->notaris_bersangkutan ?? 'NOTARIS/PPAT YUDHANA EKO PRASETYO, SH., M.Kn' }}</td>
                </tr>
            </table>
        </div>

        <div class="content">
            <p>Dengan Hormat,</p>
            <p>Dengan ini kami sampaikan rincian biaya yang harus dibayarkan : </p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Keterangan</th>
                    <th>Alamat</th>
                    <th>Rincian Biaya</th>
                    <th style="width: 100px">Biaya</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pekerjaanBiaya as $index => $pekerjaan)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $pekerjaan['nama'] }}</td>
                        <td>{{ $alamatPemohon }}</td>
                        <td></td>
                        <td>Rp. {{ number_format($pekerjaan['total'], 0, ',', '.') }},- </td>
                    </tr>

                    @if (!empty($pekerjaan['jenisPajak']))
                        <tr>
                            <td></td>
                            <td colspan="1">{{ $pekerjaan['jenisPajak'] }}</td>
                            <td></td>
                            <td>
                                @if (!empty($pekerjaan['pihak_pertama']) || !empty($pekerjaan['pihak_kedua']))
                                    <ul class="list-biaya-pajak">
                                        @if (!empty($pekerjaan['pihak_pertama']))
                                            <li>
                                                BPHTB/{{ $pekerjaan['label_pihak_pertama'] }} :
                                                Rp. {{ number_format($pekerjaan['pihak_pertama'], 0, ',', '.') }},-
                                            </li>
                                        @endif
                                        @if (!empty($pekerjaan['pihak_kedua']))
                                            <li>
                                                PPH/{{ $pekerjaan['label_pihak_kedua'] }} :
                                                Rp. {{ number_format($pekerjaan['pihak_kedua'], 0, ',', '.') }},-
                                            </li>
                                        @endif
                                    </ul>
                                @endif
                            </td>
                            <td>
                                Rp. {{ number_format($pekerjaan['total_pihak'], 0, ',', '.') }},-
                            </td>
                        </tr>
                    @endif

                @endforeach
                <tr>
                    <td colspan="4" class="total">TOTAL BIAYA</td>
                    <td class="total">
                        <b>
                            Rp. {{ number_format($totalKeseluruhan, 0, ',', '.') }},-
                        </b>
                    </td>
                </tr>
                <tr>
                    <td colspan="5" class="note">Terbilang : {{ SetAmountInWord($totalKeseluruhan) }} Rupiah.</td>
                </tr>
            </tbody>
        </table>

        <div class="content">
            <p>
                Pembayaran melalui Rekening Bank BRI : <b>6147-01-01-4990-53-6</b> Atas nama YUDHANA EKO PRASETYO
            </p>
            <p>
                Demikian surat ini kami sampaikan, atas perhatianya kami ucapkan terimakasih.
            </p>
        </div>
        <div class="signature-container">
            <div class="signature">
                <div class="head">
                    Hormat Kami,
                </div>
                <div class="name">
                    {{ $namaPenyerah }}
                </div>
                <div class="devisi">
                    Staff Notaris/PPAT
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
            window.onafterprint = function() {
                // window.close();
            };
        };
    </script>

</body>

</html>
