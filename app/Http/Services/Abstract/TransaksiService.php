<?php

/**
 * nk hartod nyusu² kn garap dewe tod !!
 */

namespace App\Http\Services\Abstract;

use App\Models\Materai;
use Illuminate\Http\Request;

interface TransaksiService
{
    public function getAllTransaction();
    public function getNotarisTransaction($id);
    public function getPPATTransaction($no_akta);
    public function findRiwayatPPAT($no_transaksi);
    public function findRiwayatPembayaran($transaksi_id);
    public function storeNotaris(Request $request, array $date, Materai $materai);
    public function storePPAT(Request $request, array $date, Materai $materai);
    public function updateNotaris(Request $request, $date, $materai, $id);
    public function updatePPAT(Request $request, $date, $materai, $id);
    public function getDetailPembayaran($transaksi_id);
    public function getPekerjaan($selected_id, $jenis_pekerjaan);
    public function getKategori($selected_id, $jenis_pekerjaan, $pekerjaan_id);
    public function fetchNotarsiProses($pekerjaan_id, $detailProses);
    public function fetchPPATProses($pekerjaan_id, $kategoriId, $detailProses);
    public function getDetailKategori($jenis_pekerjaan, $pekerjaan_id, $kategori_id);
    public function getDetailProses(array $setDataToService);
}
