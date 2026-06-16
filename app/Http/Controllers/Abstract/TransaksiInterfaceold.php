<?php

namespace App\Http\Controllers\Abstract;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface TransaksiInterface
{
    /**
     * Menampilkan halaman index.
     *
     * @return void
     */
    public function index();

    /**
     * Mengambil data untuk ditampilkan.
     *
     * @return void
     */
    public function fetch();

    /**
     * Mengambil detail data Notaris berdasarkan ID.
     *
     * @param  int  $id  ID dari notaris yang ingin diambil datanya.
     * @return void
     */
    public function fetchNotaris($id);

    /**
     * Mengambil data PPAT berdasarkan nomor akta.
     *
     * @param  Request  $request  Objek request yang diterima.
     * @param  string  $no_akta  Nomor akta yang digunakan untuk pencarian data PPAT.
     * @return void
     */
    public function fetchPPAT($no_akta);

    /**
     * Mengambil jenis pajak yang tersedia.
     *
     * @return void
     */
    public function fetchJenisPajak();

    /**
     * Mengambil riwayat pembayaran.
     *
     * @param  Request  $request  Objek request yang berisi data filter riwayat pembayaran.
     * @return void
     */
    public function getRiwayatPPAT(Request $request);

    public function riwayatPembayaranFetch(Request $request);

    /**
     * Menyimpan data transaksi ke dalam database.
     *
     * @param  Request  $request  Data transaksi yang ingin disimpan.
     * @return void
     */
    public function store(Request $request);

    /**
     * Memperbarui data transaksi berdasarkan ID.
     *
     * @param  Request  $request  Data yang ingin diperbarui.
     * @param  int  $id  ID transaksi yang akan diperbarui.
     * @return void
     */
    public function update(Request $request, $id);

    /**
     * Mengambil detail pembayaran berdasarkan request.
     *
     * @param  Request  $request  Data yang diperlukan untuk mengambil detail pembayaran.
     * @return void
     */
    public function detailPembayaran(Request $request);

    /**
     * Mengambil kategori pekerjaan yang tersedia.
     *
     * @param  Request  $request  Objek request yang berisi data filter kategori.
     * @return void
     */
    public function getKategori(Request $request);

    /**
     * Mengambil data jenis pajak.
     *
     * @param  Request  $request  Objek request yang diterima.
     * @return JsonResponse Data jenis pajak dalam format JSON.
     */
    public function getJenisPajak(Request $request);

    /**
     * Mengambil data pekerjaan yang tersedia.
     *
     * @param  Request  $request  Objek request yang berisi data filter pekerjaan.
     * @return void
     */
    public function getPekerjaan(Request $request);

    /**
     * Mengambil data proses berdasarkan request.
     *
     * @param  Request  $request  Objek request yang berisi data proses yang ingin diambil.
     * @return void
     */
    public function getProses(Request $request);

    /**
     * Mengambil data proses PPAT.
     *
     * @return void
     */
    public function prosesFetchPPAT();

    /**
     * Mengambil detail kategori berdasarkan request.
     *
     * @param  Request  $request  Data request yang diperlukan untuk mengambil detail kategori.
     * @return void
     */
    public function getDetailKategori(Request $request);

    /**
     * Mengambil detail proses pekerjaan berdasarkan request.
     *
     * @param  Request  $request  Objek request yang berisi data detail proses.
     * @return void
     */
    public function setProsesNotaris(Request $request);

    public function getProsesNotaris(Request $request);

    public function setProsesPPAT(Request $request);

    public function getProsesPPAT(Request $request);

    public function setPajak(Request $request);

    public function getPajak();

    /**
     * Mengambil kode nomor akta berdasarkan jenis pekerjaan.
     *
     * @param  int  $jenisPekerjaan  ID jenis pekerjaan untuk mendapatkan nomor akta.
     * @return void
     */
    public function getNoTransaksiCodes($jenisPekerjaan);

    /**
     * Menambahkan catatan proses berdasarkan request.
     *
     * @param  Request  $request  Data catatan proses yang ingin ditambahkan.
     * @return void
     */

    /**
     * Menghapus cookie yang terkait dengan request.
     *
     * @param  Request  $request  Objek request yang berisi informasi cookie yang ingin dihapus.
     * @return void
     */
    public function deleteCookie(Request $request);

    /**
     * Mengambil data cetak pemohon berdasarkan nomor akta.
     *
     * @param  string  $no_akta  Nomor akta yang digunakan untuk pencarian data pemohon.
     * @return void
     */
    public function cetakPemohonFetch($no_akta);

    /**
     * Memperbarui data cetak pemohon berdasarkan ID.
     *
     * @param  Request  $request  Data yang akan diperbarui.
     * @param  int  $id  ID pemohon yang akan diperbarui.
     * @return void
     */
    public function cetakPemohonUpdate(Request $request, $id);

    /**
     * Mengambil data cetak pemohon berdasarkan ID.
     *
     * @param  int  $id  ID pemohon yang akan diambil datanya.
     * @return void
     */
    public function cetakPemohon($id);

    /**
     * Mencetak dokumen PPAT berdasarkan nomor akta.
     *
     * @param  string  $no_akta  Nomor akta yang digunakan untuk pencetakan dokumen PPAT.
     * @return void
     */
    public function cetakInvoices($no_akta);

    /**
     * Mengambil daftar materai yang tersedia.
     *
     * @param  Request  $request  Objek request yang berisi filter daftar materai.
     * @return void
     */
    public function getListMaterai(Request $request);

    /**
     * Menyimpan sesi jenis pekerjaan.
     *
     * @param  array  $dataSession  Data yang akan disimpan dalam sesi jenis pekerjaan.
     * @return void
     */
    public function storeJenisPekerjaanSession($dataSession);
}
