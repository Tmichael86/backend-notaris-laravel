<?php

namespace App\Http\Repositories\Abstract;

use App\Models\Transaksi;
use Illuminate\Http\Request;

interface TransaksiRepository
{
    /**
     * Mendapatkan semua transaksi yang ada.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function findAllTransaction();

    /**
     * Mendapatkan transaksi berdasarkan ID Notaris.
     *
     * @param  int  $id  ID dari notaris.
     * @return \Illuminate\Database\Eloquent\Model|object|null
     */
    public function findNotarisTransaction($id);

    /**
     * Menyimpan informasi materai untuk transaksi notaris.
     *
     * @param  \Illuminate\Http\Request  $request  Data permintaan yang berisi informasi materai.
     * @param  \Carbon\Carbon|string  $tanggal_daftar  Tanggal daftar transaksi.
     * @param  int  $newStok  Jumlah stok baru yang akan disimpan.
     * @param  string|null  $keterangan  Keterangan tambahan terkait transaksi.
     */
    public function saveNotarisMaterai($request, $tanggal_daftar, $newStok, $keterangan);

    /**
     * Menyimpan transaksi notaris.
     *
     * @param  \Illuminate\Http\Request  $request  Data permintaan yang berisi detail transaksi.
     * @param  array  $data  Data tambahan yang akan disimpan bersama transaksi.
     * @return bool
     */
    public function saveNotarisTransaction($request, $data);

    /**
     * Menyimpan riwayat transaksi notaris.
     *
     * @param  \Illuminate\Http\Request  $request  Data permintaan terkait riwayat transaksi.
     * @param  \App\Models\Transaksi  $transaksi  Model transaksi yang akan diperbarui.
     * @return bool
     */
    public function saveNotarisHistoryTransaction(Request $request, $transaksi);

    public function savePPATMaterai($request, $tanggal_daftar, $newStok, $keterangan);

    public function savePPATTransaction(Request $request, $data);

    public function savePPATHistoryTransaction(Request $request, $transaksi);

    public function updatePPATTransaction(Request $request, $data, $id);
    
    public function saveRelasiMateraiTransaksi($dataRelasi);
}
