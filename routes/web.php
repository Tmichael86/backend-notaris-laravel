<?php

use App\Http\Controllers\Administrator\AuthController;
use App\Http\Controllers\Administrator\DashboardController;
use App\Http\Controllers\Administrator\GroupController;
use App\Http\Controllers\Administrator\KonfigurasiUmumController;
use App\Http\Controllers\Administrator\Laporan\LaporanMateraiController;
use App\Http\Controllers\Administrator\Laporan\LaporanPendapatanController;
use App\Http\Controllers\Administrator\Master\JenisPengeluaranController;
use App\Http\Controllers\Administrator\Master\KategoriPekerjaanController;
use App\Http\Controllers\Administrator\Master\PekerjaanNotarisController;
use App\Http\Controllers\Administrator\Master\PekerjaanPPATController;
use App\Http\Controllers\Administrator\Master\PemohonController;
use App\Http\Controllers\Administrator\Master\PetugasController;
use App\Http\Controllers\Administrator\MonitoringController;
use App\Http\Controllers\Administrator\PenghasilanController;
use App\Http\Controllers\Administrator\PengeluaranController;
use App\Http\Controllers\Administrator\PiutangController;
use App\Http\Controllers\Administrator\ProfileController;
use App\Http\Controllers\Administrator\SidebarController;
use App\Http\Controllers\Administrator\TransaksiController;
use App\Http\Controllers\Administrator\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/coba', function () {
    return view('clone');
});

// ==> Route Login
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/auth', [AuthController::class, 'authentication'])->name('auth');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// ==> Route Admin
Route::prefix('/administrator')->middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/data', [DashboardController::class, 'dataChart'])->name('dashboard-data');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'save'])->name('profile-save');

    Route::middleware('authorized')->group(function () {
        // Transaksi
        Route::get('transaksi', [TransaksiController::class, 'index'])->name('transaksi');
        Route::post('/transaksi-fetch', [TransaksiController::class, 'fetch'])->name('transaksi-fetch');
        Route::post('/transaksi/fetchPPAT/{no_akta}', [TransaksiController::class, 'fetchPPAT']);
        Route::post('/transaksi/fetchNotaris/{id}', [TransaksiController::class, 'fetchNotaris']);
        Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi-store');
        Route::post('/transaksi/update/{id}', [TransaksiController::class, 'update'])->name('transaksi-update');
        Route::post('/transaksi/getpekerjaan', [TransaksiController::class, 'getPekerjaan'])->name('transaksi-getpekerjaan');
        Route::post('/transaksi/getkategori', [TransaksiController::class, 'getKategori'])->name('transaksi-getKategori');
        Route::post('/transaksi/getdetailkategori', [TransaksiController::class, 'getDetailKategori'])->name('transaksi-getDetailKategori');

        Route::post('/transaksi/proses-fetch', [TransaksiController::class, 'getProses'])->name('transaksi-proses-fetch');
        Route::post('/transaksi/proses-notaris', [TransaksiController::class, 'getProsesNotaris'])->name('proses.notaris');
        Route::post('/transaksi/proses-ppat', [TransaksiController::class, 'getProsesPPAT'])->name('proses.ppat');

        Route::post('/transaksi/riwayat-ppat', [TransaksiController::class, 'getRiwayatPPAT'])->name('riwayat.ppat');
        Route::post('/transaksi/riwayat-pembayaran-fetch', [TransaksiController::class, 'riwayatpembayaranFetch'])->name('transaksi-riwayat-pembayaran-fetch');
        Route::post('/transaksi/detail-pembayaran', [TransaksiController::class, 'detailPembayaran'])->name('transaksi-detail-pembayaran');
        Route::get('/transaksi/noakta-code/{id}', [TransaksiController::class, 'getNoTransaksiCodes'])->name('getNoTransaksiCode');

        Route::delete('/transaksi/cookie-flush', [TransaksiController::class, 'cookieFlusher']);
        Route::delete('/transaksi/ppat/cookie-remove/{rowIndex}/{pekerjaanId}/{kategoriId}', [TransaksiController::class, 'cookieRemover']);

        Route::post('/transaksi/riwayat-pembayaran/edit/{id}', [TransaksiController::class, 'riwayatBayarEdit']);
        Route::delete('/transaksi/riwayat-pembayaran/remove/{id}', [TransaksiController::class, 'riwayatBayarDelete']);

        // => Validasi Proses
        Route::post('/transaksi/validasi-notaris', [TransaksiController::class, 'setProsesNotaris'])->name('validasi.notaris');
        Route::post('/transaksi/validasi-ppat', [TransaksiController::class, 'setProsesPPAT'])->name('validasi.PPAT');
        Route::post('/transaksi/catatan-proses', [TransaksiController::class, 'catatanProses'])->name('validasiProses');
        Route::post('/transaksi/list-validasi', [TransaksiController::class, 'setListValidation'])->name('setListValidasi');

        Route::get('/transaksi/cetakDokumenNotaris/{transaksi_id}', [TransaksiController::class, 'cetakDokumenNotaris'])->name('cetak-dokumen');
        Route::get('/transaksi/cetakInvoices/{no_akta}', [TransaksiController::class, 'cetakInvoices'])->name('cetak-invoices');

        Route::post('/transaksi/delete_cookie', [TransaksiController::class, 'deleteCookie'])->name('deleteCookies');
        Route::post('/transaksi/jenisPekerjaan/session/{dataSession}', [TransaksiController::class, 'storeJenisPekerjaanSession'])->name('jenis-pekerjaan');

        Route::get('/transaksi/cetakPemohon/{id}', [TransaksiController::class, 'cetakPemohon'])->name('cetak-pemohon');
        Route::get('/transaksi/cetakPemohonFetch/{no_akta}', [TransaksiController::class, 'cetakPemohonFetch'])->name('cetak-fetch');
        Route::post('/transaksi/cetakPemohon/{id}', [TransaksiController::class, 'cetakPemohonUpdate'])->name('cetak-pemohon-update');
        Route::post('/transaksi/create/cetak-serah-terima', [TransaksiController::class, 'setCetakSerahTerima'])->name('create.serah-terima');

        Route::post('/transaksi/getjenispajak', [TransaksiController::class, 'getJenisPajak'])->name('transaksi.jenis-pajak');
        Route::post('/transaksi/simpan-pajak', [TransaksiController::class, 'setPajak'])->name('transaksi.simpan-pajak');
        Route::post('/transaksi/getPajak', [TransaksiController::class, 'getPajak'])->name('transaksi.get-pajak');

        // Monitoring
        Route::get('monitoring', [MonitoringController::class, 'index'])->name('monitoring');
        Route::post('/monitoring-fetch', [MonitoringController::class, 'fetch'])->name('monitoring-fetch');
        Route::post('/monitoring/select2/getjenispekerjaan', [MonitoringController::class, 'getJenisPekerjaan'])->name('monitoring-jenispekerjaan');
        Route::post('/monitoring/select2/getpekerjaan', [MonitoringController::class, 'getPekerjaan'])->name('monitoring-pekerjaan');
        Route::post('/monitoring/select2/getkategoripekerjaan', [MonitoringController::class, 'getKategoriPekerjaan'])->name('monitoring-getkategoripekerjaan');
        Route::post('/monitoring/select2/getstatus', [MonitoringController::class, 'getStatus'])->name('monitoring-getstatus');
        Route::post('/monitoring/select2/getpetugas', [MonitoringController::class, 'getPetugas'])->name('monitoring-getpetugas');
        Route::post('/monitoring/table-notaris', [MonitoringController::class, 'getTableProsesNotaris'])->name('monitoring.table-notaris');
        Route::post('/monitoring/table-ppat', [MonitoringController::class, 'getTableProsesPPAT'])->name('monitoring.table-notaris');
        Route::post('/monitoring/getdetailcatatan', [MonitoringController::class, 'getDetailCatatan'])->name('monitoring-getdetailcatatan');
        Route::post('/monitoring/getdetailcatatanppat', [MonitoringController::class, 'getDetailCatatanPPAT'])->name('monitoring-getdetailcatatanppat');
        Route::post('/monitoring/list-ppat', [MonitoringController::class, 'getTableListPPAT'])->name('monitoring.table-ppat');
        Route::post('/monitoring/getpajak/{code}', [MonitoringController::class, 'getPajak'])->name('monitoring.getpajak');

        // penghasilan
        Route::get('penghasilan', [PenghasilanController::class, 'index'])->name('penghasilan');
        Route::post('/penghasilan-fetch', [PenghasilanController::class, 'fetch'])->name('penghasilan-fetch');
        Route::post('/penghasilan/select2/getjenispekerjaan', [PenghasilanController::class, 'getJenisPekerjaan'])->name('penghasilan-jenispekerjaan');
        Route::post('/penghasilan/select2/getpekerjaan', [PenghasilanController::class, 'getPekerjaan'])->name('penghasilan-pekerjaan');
        Route::post('/penghasilan/select2/getkategoripekerjaan', [PenghasilanController::class, 'getKategoriPekerjaan'])->name('penghasilan-getkategoripekerjaan');
        Route::post('/penghasilan/select2/getstatus', [PenghasilanController::class, 'getStatus'])->name('penghasilan-getstatus');
        Route::post('/penghasilan/select2/getpetugas', [PenghasilanController::class, 'getPetugas'])->name('penghasilan-getpetugas');
        Route::post('/penghasilan/getpajak/{code}', [PenghasilanController::class, 'getPajak'])->name('penghasilan.getpajak');
        
        // Piutang
        Route::get('piutang', [PiutangController::class, 'index'])->name('piutang');
        Route::post('/piutang-fetch', [PiutangController::class, 'fetch'])->name('piutang-fetch');
        Route::post('/piutang/getTotalPiutang', [PiutangController::class, 'getTotalPiutang'])->name('piutang-total-piutang');
        Route::post('/piutang/select2/getjenispekerjaan', [PiutangController::class, 'jenispekerjaan'])->name('piutang-jenispekerjaan');

        // pengeluaran
        Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran');
        Route::post('/pengeluaran-fetch', [PengeluaranController::class, 'fetch'])->name('pengeluaran-fetch');
        Route::post('/pengeluaran', [PengeluaranController::class, 'store'])->name('pengeluaran-store');
        Route::post('/pengeluaran/{id}', [PengeluaranController::class, 'update'])->name('pengeluaran-update');
        Route::post('/pengeluaran/select2/getjenispengeluaran', [PengeluaranController::class, 'getJenisPengeluaran'])->name('pengeluaran-jenisPengeluaran');
        Route::post('/pengeluaran/get/nofaktur', [PengeluaranController::class, 'getNoFaktur'])->name('pengeluaran-getnofaktur');
        Route::post('/pengeluaran/get/totalpengeluaran', [PengeluaranController::class, 'getTotalPengeluaran'])->name('pengeluaran-gettotalpengeluaran');
        Route::delete('/pengeluaran/{id}', [PengeluaranController::class, 'destroy'])->name('pengeluaran-destroy');

        // pekerjaan_notaris
        Route::get('/master/pekerjaan_notaris', [PekerjaanNotarisController::class, 'index'])->name('pekerjaan_notaris');
        Route::get('/master/pekerjaan_notaris/{id}', [PekerjaanNotarisController::class, 'formEdit'])->name('pekerjaan_notaris-formedit');
        Route::post('/master/pekerjaan_notaris-fetch', [PekerjaanNotarisController::class, 'fetch'])->name('pekerjaan_notaris-fetch');
        Route::delete('/master/pekerjaan_notaris/{id}', [PekerjaanNotarisController::class, 'destroy'])->name('pekerjaan_notaris-destroy');
        Route::post('/master/pekerjaan_notaris/getkategori', [PekerjaanNotarisController::class, 'getKategori'])->name('pekerjaan_notaris-getkategori');
        Route::post('/master/pekerjaan_notaris', [PekerjaanNotarisController::class, 'store'])->name('pekerjaan_notaris-store');
        Route::post('/master/pekerjaan_notaris/{id}', [PekerjaanNotarisController::class, 'update'])->name('pekerjaan_notaris-store');

        // pekerjaan_ppat
        Route::get('/master/pekerjaan_ppat', [PekerjaanPPATController::class, 'index'])->name('pekerjaan_ppat');
        Route::get('/master/pekerjaan_ppat/{id}', [PekerjaanPPATController::class, 'formEdit'])->name('pekerjaan_ppat-formedit');
        Route::post('/master/pekerjaan_ppat-fetch', [PekerjaanPPATController::class, 'fetch'])->name('pekerjaan_ppat-fetch');
        Route::delete('/master/pekerjaan_ppat/{id}', [PekerjaanPPATController::class, 'destroy'])->name('pekerjaan_ppat-destroy');
        Route::post('/master/pekerjaan_ppat/getkategori', [PekerjaanPPATController::class, 'getKategori'])->name('pekerjaan_ppat-getkategori');
        Route::post('/master/pekerjaan_ppat', [PekerjaanPPATController::class, 'store'])->name('pekerjaan_ppat-store');
        Route::post('/master/pekerjaan_ppat/{id}', [PekerjaanPPATController::class, 'update'])->name('pekerjaan_ppat-store');

        // kategori pekerjaan
        Route::get('/master/kategori_pekerjaan', [KategoriPekerjaanController::class, 'index'])->name('kategori_pekerjaan');
        Route::post('/master/kategori_pekerjaan-fetch', [KategoriPekerjaanController::class, 'fetch'])->name('kategori_pekerjaan-fetch');
        Route::post('/master/kategori_pekerjaan', [KategoriPekerjaanController::class, 'store'])->name('kategori_pekerjaan-store');
        Route::post('/master/kategori_pekerjaan/{id}', [KategoriPekerjaanController::class, 'update'])->name('kategori_pekerjaan-update');
        Route::delete('/master/kategori_pekerjaan/{id}', [KategoriPekerjaanController::class, 'destroy'])->name('kategori_pekerjaan-destroy');

        // jenis pengeluaran
        Route::get('/master/jenis_pengeluaran', [JenisPengeluaranController::class, 'index'])->name('jenis_pengeluaran');
        Route::post('/master/jenis_pengeluaran-fetch', [JenisPengeluaranController::class, 'fetch'])->name('jenis_pengeluaran-fetch');
        Route::post('/master/jenis_pengeluaran', [JenisPengeluaranController::class, 'store'])->name('jenis_pengeluaran-store');
        Route::post('/master/jenis_pengeluaran/{id}', [JenisPengeluaranController::class, 'update'])->name('jenis_pengeluaran-update');
        Route::delete('/master/jenis_pengeluaran/{id}', [JenisPengeluaranController::class, 'destroy'])->name('jenis_pengeluaran-destroy');

        // Petugas
        Route::get('/master/petugas', [PetugasController::class, 'index'])->name('petugas');
        Route::post('/master/petugas-fetch', [PetugasController::class, 'fetch'])->name('petugas-fetch');
        Route::post('/master/petugas', [PetugasController::class, 'store'])->name('petugas-store');
        Route::post('/master/petugas/{id}', [PetugasController::class, 'update'])->name('petugas-update');
        Route::delete('/master/petugas/{id}', [PetugasController::class, 'destroy'])->name('petugas-destroy');
        Route::post('/master/petugas/select2/getjeniskelamin', [PetugasController::class, 'getJenisKelamin'])->name('petugas-getjeniskelamin');
        Route::post('/master/petugas/select2/getuser', [PetugasController::class, 'getUser'])->name('petugas-getuser');

        // Pemohon
        Route::get('/master/pemohon', [PemohonController::class, 'index'])->name('pemohon');
        Route::post('/master/pemohon-fetch', [PemohonController::class, 'fetch'])->name('pemohon-fetch');
        Route::post('/master/pemohon', [PemohonController::class, 'store'])->name('pemohon-store');
        Route::post('/master/pemohon/{id}', [PemohonController::class, 'update'])->name('pemohon-update');
        Route::post('/master/pemohon/select2/getjeniskelamin', [PemohonController::class, 'getJenisKelamin'])->name('pemohon-getjeniskelamin');
        Route::delete('/master/pemohon/{id}', [PemohonController::class, 'destroy'])->name('pemohon-destroy');

        // Laporan Materai
        Route::get('/laporan/materai', [LaporanMateraiController::class, 'index'])->name('materai.index');
        Route::post('/laporan/materai-fetch', [LaporanMateraiController::class, 'fetch'])->name('materai.index');
        Route::post('/laporan/materai/store', [LaporanMateraiController::class, 'store'])->name('materai.store');
        Route::post('/laporan/materai/getpetugas', [LaporanMateraiController::class, 'getPetugas'])->name('materai.getpetugas');

        Route::post('/laporan/materai/select2/getpetugas', [LaporanMateraiController::class, 'getFilterPetugas'])->name('filter.petugas');

        // Laporan Pendapatan
        Route::get('/laporan/pendapatan', [LaporanPendapatanController::class, 'index'])->name('laporan.pendapatan');
        Route::post('/laporan/pendapatan/fetch', [LaporanPendapatanController::class, 'fetch']);
        Route::get('/laporan/pendapatan/jenis-pembayaran', [LaporanPendapatanController::class, 'jenisPembayaran']);

        // Users
        Route::get('/users', [UsersController::class, 'index'])->name('users');
        Route::post('/users-fetch', [UsersController::class, 'fetch'])->name('users-fetch');
        Route::post('/users', [UsersController::class, 'store'])->name('users-store');
        Route::post('/users/{id}', [UsersController::class, 'update'])->name('users-update');
        Route::delete('/users/{id}', [UsersController::class, 'destroy'])->name('users-destroy');

        // Groups
        Route::get('/groups', [GroupController::class, 'index'])->name('groups');
        Route::post('/groups-fetch', [GroupController::class, 'fetch'])->name('groups-fetch');
        Route::get('/groups/form', [GroupController::class, 'form'])->name('groups-form');
        Route::post('/groups/form', [GroupController::class, 'store'])->name('groups-store');
        Route::get('/groups/form/{id}', [GroupController::class, 'form'])->name('groups-form-update');
        Route::post('/groups/form/{id}', [GroupController::class, 'update'])->name('groups-update');
        Route::delete('/groups/{id}', [GroupController::class, 'destroy'])->name('groups-destroy');

        // Sidebars
        Route::get('/sidebars', [SidebarController::class, 'index'])->name('sidebars');
        Route::post('/sidebars-fetch', [SidebarController::class, 'fetch'])->name('sidebars-fetch');

        // => Konfigurasi Umum
        Route::get('/konfigurasi', [KonfigurasiUmumController::class, 'index']);
        Route::put('/konfigurasi/update', [KonfigurasiUmumController::class, 'update'])->name('konfigurasi.update');
    });
});

Route::get('/', function () {
    return redirect()->route('login');
});
