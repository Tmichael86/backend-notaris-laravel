<?php

// => Helper Change Number To Word (Rupiah)
if (! function_exists('SetNilaiTerbilang')) {
    function SetAmountInWord($angka)
    {
        $angka = abs($angka);
        $huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $temp = '';

        if ($angka < 12) {
            $temp = ' '.$huruf[$angka];
        } elseif ($angka < 20) {
            $temp = SetAmountInWord($angka - 10).' Belas ';
        } elseif ($angka < 100) {
            $temp = SetAmountInWord($angka / 10).' Puluh '.SetAmountInWord($angka % 10);
        } elseif ($angka < 200) {
            $temp = ' Seratus '.SetAmountInWord($angka - 100);
        } elseif ($angka < 1000) {
            $temp = SetAmountInWord($angka / 100).' Ratus '.SetAmountInWord($angka % 100);
        } elseif ($angka < 2000) {
            $temp = ' Seribu '.SetAmountInWord($angka - 1000);
        } elseif ($angka < 1000000) {
            $temp = SetAmountInWord($angka / 1000).' Ribu '.SetAmountInWord($angka % 1000);
        } elseif ($angka < 1000000000) {
            $temp = SetAmountInWord($angka / 1000000).' Juta '.SetAmountInWord($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            $temp = SetAmountInWord($angka / 1000000000).' Miliar '.SetAmountInWord(fmod($angka, 1000000000));
        } elseif ($angka < 1000000000000000) {
            $temp = SetAmountInWord($angka / 1000000000000).' Triliun '.SetAmountInWord(fmod($angka, 1000000000000));
        }

        return trim($temp);
    }
}
