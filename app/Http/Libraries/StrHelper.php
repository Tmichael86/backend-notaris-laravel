<?php

namespace App\Http\Libraries;

class StrHelper
{
    public static function array_build($array)
    {
        $data = [];
        foreach ($array as $k => $list) {
            $i = 0;
            foreach ($list as $key => $value) {
                $data[$i][$k] = $value;
                $i++;
            }
        }

        return $data;
    }

    public static function date_id($dateString)
    {
        $months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];

        $dateComponents = explode('-', $dateString);
        $day = $dateComponents[2];
        $month = $months[(int) $dateComponents[1] - 1];
        $year = $dateComponents[0];

        return $day.' '.$month.' '.$year;
    }

    public static function format_rupiah($amount)
    {
        return 'Rp. '.number_format($amount, 0, ',', '.');
    }

    public static function format_surat_hari_ini()
    {
        $nama_hari = [
            'Minggu', 'Senin', 'Selasa', 'Rabu',
            'Kamis', 'Jumat', 'Sabtu',
        ];
        $indeks_hari = date('w');
        $hari_ini = $nama_hari[$indeks_hari];
        $months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];
        $month = $months[(int) date('m') - 1];
        $day = date('d');
        $year = date('Y');

        return "Hari ini $hari_ini tanggal $day bulan $month Tahun $year";
    }

    public static function rupiah_to_number($rupiahString)
    {
        $cleanedString = preg_replace('/[^0-9,]/', '', $rupiahString);
        $cleanedString = str_replace(',', '.', $cleanedString);
        $number = floatval($cleanedString);

        return $number;
    }
}
