<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Abstract\TransaksiInterface;
use App\Http\Controllers\Controller;
use App\Http\Libraries\System;
use App\Http\Services\Abstract\TransaksiService;
use App\Models\AtributPekerjaanNotaris;
use App\Models\AtributPekerjaanPPAT;
use App\Models\Group;
use App\Models\HargaPekerjaanNotaris;
use App\Models\HargaPekerjaanPPAT;
use App\Models\JenisPajak;
use App\Models\JenisPekerjaan;
use App\Models\JenisPembayaran;
use App\Models\KategoriPekerjaan;
use App\Models\KonfigurasiUmum;
use App\Models\Materai;
use App\Models\PekerjaanNotaris;
use App\Models\PekerjaanPPAT;
use App\Models\Petugas;
use App\Models\ProsesPekerjaanNotaris;
use App\Models\ProsesPekerjaanPPAT;
use App\Models\Transaksi;
use App\Models\TransaksiMaterai;
use App\Models\TransaksiCetakSerahTerima;
use App\Models\TransaksiDetailProses;
use App\Models\TransaksiDetailProsesNotaris;
use App\Models\TransaksiRiwayatPembayaran;
use App\Models\TransaksiStatus;
use App\Models\User;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TransaksiController extends Controller
{
    // => Private Function || Don't Repeat Your Code
    private function generateNoTransaksi($jenisPekerjaan)
    {
        $uId = Session::get('uid');
        $count = DB::table('transaksi')
            ->distinct('no_akta')
            ->where(DB::raw('extract(year from created_at)'), date('Y'))
            ->where(DB::raw('extract(month from created_at)'), date('m'))
            ->count() ?: '0';
        $count++;
        $date = date('dmy');
        $userId = sprintf('%02s', $uId);
        $jPekerjaan = sprintf($jenisPekerjaan);

        return sprintf("{$jPekerjaan}{$userId}{$date}%04d", $count);
    }

    private function addCookies($name, $data, $minutes = 60)
    {
        Cookie::queue(Cookie::make($name, $data, $minutes));
    }

    private function deleteCookies($names)
    {
        foreach ($names as $value) {
            Cookie::queue(Cookie::forget($value));
        }
    }

    private function isValidateFail()
    {
        return response()->json([
            'failed' => true,
            'message' => 'Mohon isi form dengan benar',
        ]);
    }

    private function isMateraiAvailable()
    {
        return response()->json([
            'failed' => true,
            'message' => 'Data materai tidak ditemukan atau stok tidak mencukupi',
        ]);
    }

    // => Set Validation Request if Notaris
    private function isNotarisRequest($request)
    {
        return Validator::make($request->all(), [
            'no_akta' => 'required',
            'tanggal_daftar' => 'required',
            'tanggal_selesai' => 'required',
            'pemohon_id' => 'required',
            'jenis_pekerjaan_id' => 'required',
            'pekerjaan_id' => 'required',
            'kategori_pekerjaan_id' => 'required',
            'biaya_layanan' => 'required',
            'biaya_lainnya' => 'nullable|numeric',
            'jumlah_materai' => 'required|integer|min:0',
            'status_id' => 'required',
            'petugas_id' => 'required',
            'potongan_harga' => 'nullable|numeric',
            'jatuh_tempo' => 'required',
            'pembayaran_sekarang' => 'required|numeric',
        ]);
    }

    // => Set Validation Request if PPAT
    private function isPPATRequest($request)
    {
        return Validator::make($request->all(), [
            'no_akta' => 'required',
            'tanggal_daftar' => 'required|date_format:d/m/Y',
            'tanggal_selesai' => 'required|date_format:d/m/Y',
            'pemohon_id' => 'required',
            'jenis_pekerjaan_id' => 'required',
            'pekerjaan_id.pekerjaan_ppat_id.*' => 'required',
            'kategori_pekerjaan_id.kategori_pekerjaan_ppat_id.*' => 'required',
            'biaya_layanan.biaya_layanan_ppat.*' => 'required',
            'biaya_lainnya' => 'nullable|numeric',
            'jumlah_materai' => 'required|integer|min:0',
            'petugas_id' => 'required',
            'potongan_harga' => 'nullable|numeric',
            'jatuh_tempo' => 'required|date_format:d/m/Y',
            'pembayaran_sekarang' => 'required|numeric',
            'transaksi_status.status_id.*' => 'required|numeric',
        ]);
    }
    // => End Private Function

    // ------------------------------------------------------------------------------------------------------------------------ //
    // => List Property On This Page
    private TransaksiService $transaksiService;
    // => End List Property
    // ------------------------------------------------------------------------------------------------------------------------ //

    // => Constructor Service For Depedency Injection
    public function __construct(TransaksiService $transaksiService)
    {
        $this->transaksiService = $transaksiService;
    }
    // => End Constructor Service For Depedency Injection

    // ------------------------------------------------------------------------------------------------------------------------ //

    // => Main Function For Transaksi At Here !
    public function index()
    {
        Cache::flush();

        $this->deleteCookies(['X-PROSES-NOTARIS']);
        $this->deleteCookies(['X-PROSES-PPAT']);
        $this->deleteCookies(['X-HITUNG-PAJAK']);

        $data['groupData'] = User::join('groups as g', 'users.group_id', '=', 'g.id')
            ->where('users.id', Auth::id())
            ->select([
                'g.*'
            ])
            ->first();

        $noAkta = self::generateNoTransaksi(1);
        $data['noAkta'] = $noAkta;
        $data['groups'] = System::getAccess();
        $data['transaksiStatus'] = TransaksiStatus::where('status', 1)->get();
        $data['jeniPembayaran'] = JenisPembayaran::where('status', 1)->get();
        $data['pekerjaanJenisSaatIni'] = Session::get('X-SESSION-JENIS');
        $data['pemohonCreate'] = System::getAccess('create', 'pemohon');
        $data['jenisPekerjaan'] = JenisPekerjaan::where('status', 1)
            ->orderBy('id', 'ASC')
            ->get();

        return view('administrator.transaksi.index', $data);
    }

    public function fetch(Request $request)
    {
        setAccessibilityPermission('read');

        $jenisId = $request->input('jenis_pekerjaan');
        return $this->transaksiService->getAllTransaction($jenisId);
    }

    public function fetchNotaris($id)
    {
        setAccessibilityPermission('read');

        $this->deleteCookies(['X-PROSES-NOTARIS']);

        $id = System::strDecode($id);

        $result = $this->transaksiService->getNotarisTransaction($id);

        return $this->responseServer(200, [
            'statusCode' => 200,
            'data' => $result,
        ]);
    }

    public function fetchPPAT($no_akta)
    {
        setAccessibilityPermission('read');

        $this->deleteCookies(['X-HITUNG-PAJAK']);

        $DB = $this->transaksiService->getPPATTransaction($no_akta);

        $hitungPajakArray = [];
        foreach ($DB as $key => $value) {
            $hitungPajak = [
                'id' => $key,
                'jenis_pajak' => $value->jenis_pajak_id,
                'acuanNilaiPajak' => $value->acuan_hitung_pajak,
                'nilaiPengurang' => $value->nilai_pengurang,
                'pihak_pertama' => $value->besaran_pajak_pihak_pertama,
                'pihak_kedua' => $value->besaran_pajak_pihak_kedua,
                'besaran_tidak_kena_pajak' => $value->besaran_tidak_kena_pajak ?? 0,
            ];

            $hitungPajakArray[] = $hitungPajak;
        }

        $this->addCookies('X-HITUNG-PAJAK', json_encode($hitungPajakArray, true));

        return $this->responseServer(200, [
            'statusCode' => 200,
            'data' => $DB,
            'cookieData' => json_decode(Cookie::get('X-HITUNG-PAJAK'), true),
        ]);
    }

    public function store(Request $request)
    {
        setAccessibilityPermission('create');

        $jenisPekerjaanId = $request->jenis_pekerjaan_id;
        $validated = $jenisPekerjaanId == 1 ? $this->isNotarisRequest($request) : $this->isPPATRequest($request);

        if ($validated->fails()) {
            return $this->isValidateFail();
        }

        $date['tanggal_daftar'] = Carbon::createFromFormat('d/m/Y', $request->input('tanggal_daftar'))->format('Y-m-d');
        $date['tanggal_selesai'] = Carbon::createFromFormat('d/m/Y', $request->input('tanggal_selesai'))->format('Y-m-d');
        $date['jatuh_tempo'] = Carbon::createFromFormat('d/m/Y', $request->input('jatuh_tempo'))->format('Y-m-d');

        $materai = Materai::query()
            ->select([
                DB::raw('SUM(materai_masuk) as materai_masuk'),
                DB::raw('SUM(materai_keluar) as materai_keluar'),
                DB::raw('SUM(materai_masuk - materai_keluar) as stok_materai'),
            ])
            ->first();

        if (!$materai || $request->input('jumlah_materai') > $materai->stok_materai) {
            return $this->isMateraiAvailable();
        }

        try {
            if ($jenisPekerjaanId == 1) {
                $this->transaksiService->storeNotaris($request, $date, $materai);
            } else {
                $this->transaksiService->storePPAT($request, $date, $materai);
            }

            return $this->responseServer(200, [
                'statusCode' => 200,
                'message' => 'Data berhasil disimpan',
            ]);
        } catch (\Throwable $th) {
            return $this->responseServer(500, [
                'statusCode' => 500,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        setAccessibilityPermission('update');

        $date = [];

        $date['tanggal_daftar'] = Carbon::createFromFormat('d/m/Y', $request->input('tanggal_daftar'))->format('Y-m-d');
        $date['tanggal_selesai'] = Carbon::createFromFormat('d/m/Y', $request->input('tanggal_selesai'))->format('Y-m-d');
        $date['jatuh_tempo'] = Carbon::createFromFormat('d/m/Y', $request->input('jatuh_tempo'))->format('Y-m-d');

        $materai = Materai::query()
            ->select([
                DB::raw('SUM(materai_masuk) as materai_masuk'),
                DB::raw('SUM(materai_keluar) as materai_keluar'),
                DB::raw('SUM(materai_masuk - materai_keluar) as stok_materai'),
            ])
            ->first();

            if ($request->jenis_pekerjaan_id == 1) {
                $relasi_materai_id = TransaksiMaterai::where('transaksi_id',$id)->pluck('materai_id')->toArray();
                $materai_masuk = Materai::whereIn('id',$relasi_materai_id)->sum('materai_masuk');
                $materai_keluar = Materai::whereIn('id',$relasi_materai_id)->sum('materai_keluar');
                $selisih_input = $request->input('jumlah_materai') - ($materai_keluar - $materai_masuk);
                if (!$materai || $selisih_input > $materai->stok_materai) {
                    return $this->isMateraiAvailable();
                }
            }else{
                $transaksi_ppat_id = Transaksi::where('no_akta',$request->no_akta)->first();
                $relasi_materai_id = TransaksiMaterai::where('transaksi_id',$transaksi_ppat_id->id)->pluck('materai_id')->toArray();
                $materai_masuk = Materai::whereIn('id',$relasi_materai_id)->sum('materai_masuk');
                $materai_keluar = Materai::whereIn('id',$relasi_materai_id)->sum('materai_keluar');
                $selisih_input = $request->input('jumlah_materai') - ($materai_keluar - $materai_masuk);
                if (!$materai || $selisih_input > $materai->stok_materai) {
                    return $this->isMateraiAvailable();
                }
            }

        try {
            if ($request->jenis_pekerjaan_id == 1) {
                $validated = $this->isNotarisRequest($request);
                if ($validated->fails()) {
                    return $this->isValidateFail();
                }
                $this->transaksiService->updateNotaris($request, $date, $materai, $id);
            } else {
                $validated = $this->isPPATRequest($request);
                $no_transaksi = $request->no_akta;
                if ($validated->fails()) {
                    return $this->isValidateFail();
                }
                $this->transaksiService->updatePPAT($request, $date, $materai, $no_transaksi);
            }

            return $this->responseServer(200, [
                'statusCode' => 200,
                'message' => 'Data transaksi berhasil diupdate',
            ]);
        } catch (\Throwable $th) {
            return $this->responseServer(500, [
                'statusCode' => 500,
                'message' => $th->getMessage(),
            ]);
        }
    }


    // => End Main Function
    // -------------------------------------------------------------------------------------------------------------------------------
    // => Additional Function At Here !
    public function fetchJenisPajak()
    {
        setAccessibilityPermission('read');

        $result = JenisPajak::query()->where('status', 1)->get();

        return $this->responseServer(200, [
            'statusCode' => 200,
            'data' => $result,
        ]);
    }

    public function getRiwayatPPAT(Request $request)
    {
        setAccessibilityPermission('read');

        $no_transaksi = $request->no_transaksi;

        if (! $no_transaksi) {
            return $this->responseServer(200, [
                'statusCode' => 200,
                'message' => 'No Transaksi Tidak Di Temukan',
            ]);
        }

        return $this->transaksiService->findRiwayatPPAT($no_transaksi);
    }

    public function riwayatpembayaranFetch(Request $request)
    {
        setAccessibilityPermission('read');

        $transaksi_id = $request->input('transaksi_id');

        if ($transaksi_id == false) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'data tidak ditemukan',
            ]);
        }

        return $this->transaksiService->findRiwayatPembayaran($transaksi_id);
    }

    public function detailPembayaran(Request $request)
    {
        $transaksi_id = $request->input('transaksi_id');

        if ($transaksi_id == false) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'Data tidak ditemukan',
            ]);
        }

        if (! System::getAccess('read')) {
            return $this->responseServer(403, [
                'statusCode' => 403,
                'message' => 'You are not authorized to access this page',
            ]);
        }

        $noAkta = Transaksi::where('id', $transaksi_id)->value('no_akta');
        $jumlahBayar = TransaksiRiwayatPembayaran::where('no_transaksi', $noAkta)
            ->where('status', 1)
            ->pluck('jumlah_dibayar')
            ->toArray();

        $totalBayar = array_sum($jumlahBayar);
        $totalTagihan =  System::getTotalTransaksiByCode($noAkta);

        $sisaHutang = $totalTagihan - $totalBayar;

        $data['no_akta'] = $noAkta;
        $data['total_bayar'] = $totalBayar;
        $data['sisa_hutang'] = $sisaHutang;

        return $this->responseServer(200, [
            'statusCode' => 200,
            'data' => $data,
        ]);
    }

    public function getJenisPajak(Request $request)
    {
        $selected_id = $request->selected;

        $data = JenisPajak::query()->select(columns: ['id', 'nama_pajak as text'])->where('status', 1)->get()->toArray();
        $data[] = ['id' => 0, 'text' => '--Pilih Pajak--'];
        if ($selected_id != '') {
            foreach ($data as $key => $value) {
                if ($value['id'] == $selected_id) {
                    $data[$key]['selected'] = true;
                }
            }
        }
        $data = collect($data)->sortBy('id')->values()->all();

        return $this->responseServer(200, [
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function getPekerjaan(Request $request)
    {
        $selected_id = $request->selected;
        $jenis_pekerjaan = $request->jenis_pekerjaan;

        $data = $this->transaksiService->getPekerjaan($selected_id, $jenis_pekerjaan);

        return $this->responseServer(200, [
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function getKategori(Request $request)
    {
        $selected_id = $request->input('selected');
        $jenis_pekerjaan = $request->input('jenis_pekerjaan');
        $pekerjaan_id = $request->input('pekerjaan_id');
        if ($jenis_pekerjaan == '1') {
            $data = KategoriPekerjaan::join('pekerjaan_notaris_harga as pnh', 'pnh.kategori_pekerjaan_id', '=', 'pekerjaan_kategori.id')
                ->select(['pekerjaan_kategori.id as id', 'pekerjaan_kategori.nama as text'])
                ->where('pnh.pekerjaan_notaris_id', $pekerjaan_id)
                ->where('pekerjaan_kategori.status', 1)->get()->toArray();
            $data[] = ['id' => 0, 'text' => '--Pilih Kategori--'];
            if ($selected_id != '') {
                foreach ($data as $key => $value) {
                    if ($value['id'] == $selected_id) {
                        $data[$key]['selected'] = true;
                    }
                }
            }
            $data = collect($data)->sortBy('id')->values()->all();

            return response()->json([
                'status' => 'success',
                'data' => $data,
            ]);
        } else {
            $data = KategoriPekerjaan::join('pekerjaan_ppat_harga as pph', 'pph.kategori_pekerjaan_id', '=', 'pekerjaan_kategori.id')
                ->select(['pekerjaan_kategori.id as id', 'pekerjaan_kategori.nama as text'])
                ->where('pph.pekerjaan_ppat_id', $pekerjaan_id)
                ->where('pekerjaan_kategori.status', 1)->get()->toArray();
            $data[] = ['id' => 0, 'text' => '--Pilih Kategori--'];
            if ($selected_id != '') {
                foreach ($data as $key => $value) {
                    if ($value['id'] == $selected_id) {
                        $data[$key]['selected'] = true;
                    }
                }
            }
            $data = collect($data)->sortBy('id')->values()->all();

            return response()->json([
                'status' => 'success',
                'data' => $data,
            ]);
        }
    }

    public function getProses(Request $request)
    {
        setAccessibilityPermission('read');

        // => Getting Current Session
        // $sesiSaatIni = Session::get('X-SESSION-JENIS');

        $sesiSaatIni = $request->input('jenis_pekerjaan');
        $pekerjaan_id = $request->input('pekerjaan_id');
        $kategori_id = $request->input('kategori_pekerjaan_id');
        $transaksi_id = $request->input('transaksi_id');

        // => Session 1 == Notaris
        if ($sesiSaatIni == 1) {
            // => Getting Current Cookie
            $cookie_data = stripslashes(Cookie::get('X-PROSES-NOTARIS'));
            $detailProsesCache = json_decode($cookie_data, true);

            $detailProsesDB = TransaksiDetailProses::query()
                ->leftJoin('pekerjaan_notaris_proses as pnp', 'pnp.id', '=', 'transaksi_detail_proses.prosesId')
                ->where('transaksi_id', $transaksi_id)
                ->get()
                ->toArray();

            $detailProses = $detailProsesCache ? $detailProsesCache : $detailProsesDB;

            if ($detailProses) {
                foreach ($detailProses as $key => $process) {
                    $detailProses[$key] = [
                        'proses_id' => @$process['prosesId'] ?: @$process['proses_id'],
                        'jenis_pekerjaan_id' => 1, // notaris
                        'kategoriNama' => $process['kategoriNama'],
                        'pekerjaanNama' => $process['pekerjaanNama'],
                        'prosesNama' => $process['prosesNama'],
                        'atribut' => is_string($process['atribut']) ? json_decode($process['atribut'], true) : $process['atribut'],
                        'catatan' => $process['catatan'],
                        'isValidate' => $process['isValidate'],
                    ];
                }
            }

            // ==> Cookie Setter
            if (!$detailProsesCache) {
                $this->addCookies('X-PROSES-NOTARIS', json_encode($detailProses ?: []));
            }

            $datatable = $this->transaksiService->fetchNotarsiProses($pekerjaan_id, $detailProses);
            return $datatable->toJson();
        } else {
            //  => Session 2 == PPAT
            $noAkta = Transaksi::where('id', $transaksi_id)
                ->where('status', 1)
                ->value('no_akta');

            $inTransaksiId = Transaksi::where('no_akta', $noAkta)
                ->where('status', 1)
                ->pluck('id')
                ->toArray();

            $cookie_data = stripslashes(Cookie::get('X-PROSES-PPAT'));
            $detailProsesCache = json_decode($cookie_data, true);

            $detailProsesDB = TransaksiDetailProses::query()
                ->join('transaksi', 'transaksi_detail_proses.transaksi_id', '=', 'transaksi.id')
                ->leftJoin('pekerjaan_ppat_proses as pnp', 'pnp.id', '=', 'transaksi_detail_proses.prosesId')
                ->whereIn('transaksi_id', $inTransaksiId)
                ->where('transaksi_detail_proses.status', 1)
                ->select([
                    'transaksi_detail_proses.*',
                    'transaksi.pekerjaan_id as pekerjaanId',
                    'transaksi.kategori_pekerjaan_id as kategoriPekerjaanId',
                ])
                ->get()
                ->toArray();

            $detailProses = $detailProsesCache ? $detailProsesCache : $detailProsesDB;

            if ($detailProses) {
                foreach ($detailProses as $key => $process) {
                    $detailProses[$key] = [
                        'proses_id' => @$process['prosesId'] ?: @$process['proses_id'],
                        'jenis_pekerjaan_id' => 2, // ppat
                        'pekerjaanId' => $process['pekerjaanId'],
                        'kategoriPekerjaanId' => $process['kategoriPekerjaanId'],
                        'pekerjaanNama' => $process['pekerjaanNama'],
                        'kategoriNama' => $process['kategoriNama'],
                        'prosesNama' => $process['prosesNama'],
                        'atribut' => is_string($process['atribut']) ? json_decode($process['atribut'], true) : $process['atribut'],
                        'catatan' => $process['catatan'],
                        'isValidate' => $process['isValidate'],
                    ];
                }
            }

            // ==> Cookie Setter
            if (!$detailProsesCache) {
                $this->addCookies('X-PROSES-PPAT', json_encode($detailProses ?: []));
            }

            $datatable = $this->transaksiService->fetchPPATProses($pekerjaan_id, $kategori_id, $detailProses);
            return $datatable->toJson();
        }
    }

    public function prosesFetchPPAT() {}

    public function getDetailKategori(Request $request)
    {
        $jenis_pekerjaan = $request->input('jenis_pekerjaan');
        $pekerjaan_id = $request->input('pekerjaan_id');
        $kategori_id = $request->input('kategori_pekerjaan_id');

        $data = null;
        if ($jenis_pekerjaan == '1') {
            $data = HargaPekerjaanNotaris::query()->where('pekerjaan_notaris_id', $pekerjaan_id)->where('kategori_pekerjaan_id', $kategori_id)->first();
        } else {
            $data = HargaPekerjaanPPAT::query()->where('pekerjaan_ppat_id', $pekerjaan_id)->where('kategori_pekerjaan_id', $kategori_id)->first();
        }

        return $this->responseServer(200, [
            'statusCode' => 200,
            'data' => $data,
        ]);
    }

    public function setProsesNotaris(Request $request)
    {
        $newProcess = $request->all();
        $cookieData = Cookie::get('X-PROSES-NOTARIS');
        $processList = json_decode($cookieData, true) ?? [];

        $newProcessId = $newProcess['prosesId'] ?? null;
        $found = false;

        foreach ($processList as $key => $process) {
            $getId = @$process['prosesId'] ?: @$process['proses_id'];
            if ($getId == $newProcessId) {
                $processList[$key] = $newProcess;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $processList[] = $newProcess;
        }

        $this->addCookies('X-PROSES-NOTARIS', json_encode($processList));

        return $this->responseServer(200, [
            'statusCode' => 200,
            'message' => 'Data proses sukses tersimpan',
        ]);
    }

    public function getProsesNotaris(Request $request)
    {
        setAccessibilityPermission('read');

        $id = System::strDecode($request->input('id'));
        $id = $id ?: $request->id;

        if (!$id) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'Data tidak ditemukan',
            ]);
        }

        $pekerjaan_id = $request->input('pekerjaan_id');
        $kategori_id = $request->input('kategori_pekerjaan_id');
        $transaksi_id = $request->input('transaksi_id');

        $cookie_data = Cookie::get('X-PROSES-NOTARIS');
        $prosesNotarisData = null;
        if ($cookie_data === null) {
            $detailProses = TransaksiDetailProses::query()
                ->where('prosesId', $id)
                ->where('transaksi_id', $transaksi_id)
                ->first();

            if ($detailProses) {
                $prosesNotarisData = [
                    'prosesId' => $detailProses->prosesId,
                    'pekerjaanNama' => $detailProses->pekerjaanNama,
                    'kategoriNama' => $detailProses->kategoriNama,
                    'prosesNama' => $detailProses->prosesNama,
                    'atribut' => json_decode($detailProses->atribut, true),
                    'catatan' => $detailProses->catatan,
                    'isValidate' => $detailProses->isValidate,
                ];
            }
        } else {
            $cleanCookieData = json_decode($cookie_data, true);
            if (is_array($cleanCookieData)) {
                $prosesNotarisFiltered = array_filter($cleanCookieData, function ($item) use ($id) {
                    $getId = @$item['prosesId'] ?: @$item['proses_id'];
                    return $getId == $id;
                });
                $prosesNotarisData = !empty($prosesNotarisFiltered) ? reset($prosesNotarisFiltered) : null;
            }
        }

        $proses = ProsesPekerjaanNotaris::query()->findOrFail($id);
        $pekerjaan = PekerjaanNotaris::query()->findOrFail($pekerjaan_id);
        $kategori = KategoriPekerjaan::query()->findOrFail($kategori_id);
        $atribut = AtributPekerjaanNotaris::query()
            ->where('proses_pekerjaan_notaris_id', $proses->id)
            ->get();

        if (!$prosesNotarisData) {
            $prosesNotarisData = [
                'prosesId' => $proses->id,
                'pekerjaanNama' => $pekerjaan->nama,
                'pekerjaan_id' => $pekerjaan->id,
                'kategoriNama' => $kategori->nama,
                'prosesNama' => $proses->nama,
                'atribut' => $atribut->toArray(),
                'catatan' => null,
                'isValidate' => 0,
            ];
        }

        $data = [
            'proses' => $proses->toArray(),
            'pekerjaan' => $pekerjaan->toArray(),
            'kategori' => $kategori->toArray(),
            'atribut' => $atribut->toArray(),
            'prosesNotaris' => $prosesNotarisData,
        ];

        return $this->responseServer(200, [
            'statusCode' => 200,
            'data' => $data,
        ]);
    }

    public function setProsesPPAT(Request $request)
    {
        // -- in here set ppat
        setAccessibilityPermission('read');

        $newProcess = $request->all();
        $cookieData = Cookie::get('X-PROSES-PPAT');
        $processList = json_decode($cookieData, true) ?? [];

        $newProcessId = $newProcess['prosesId'] ?? null;
        $found = false;

        foreach ($processList as $key => $process) {
            $getId = @$process['prosesId'] ?: @$process['proses_id'];
            $exist = $getId == $newProcessId
                && @$process['pekerjaanId'] == $newProcess['pekerjaanId']
                && @$process['kategoriPekerjaanId'] == $newProcess['kategoriPekerjaanId'];

            if ($exist) {
                $processList[$key] = $newProcess;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $processList[] = $newProcess;
        }

        $this->addCookies('X-PROSES-PPAT', json_encode($processList));

        return $this->responseServer(200, [
            'statusCode' => 200,
            'message' => 'Data proses sukses tersimpan',
        ]);
    }

    public function getProsesPPAT(Request $request)
    {
        // -- in here get proses ppat

        setAccessibilityPermission('read');

        $id = System::strDecode($request->input('id'));
        $id = $id ?: $request->id;
        if (!$id) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'Data tidak ditemukan',
            ]);
        }

        $pekerjaan_id = $request->input('pekerjaan_id');
        $kategori_id = $request->input('kategori_pekerjaan_id');
        $transaksi_id = $request->input('transaksi_id');

        $cookie_data = Cookie::get('X-PROSES-PPAT');
        $prosesNotarisData = null;

        if ($cookie_data === null) {
            $detailProses = TransaksiDetailProses::query()
                ->where('prosesId', $id)
                ->where('transaksi_id', $transaksi_id)
                ->first();

            if ($detailProses) {
                $prosesNotarisData = [
                    'prosesId' => $detailProses->prosesId,
                    'pekerjaanNama' => $detailProses->pekerjaanNama,
                    'kategoriNama' => $detailProses->kategoriNama,
                    'prosesNama' => $detailProses->prosesNama,
                    'atribut' => json_decode($detailProses->atribut, true),
                    'catatan' => $detailProses->catatan,
                    'isValidate' => $detailProses->isValidate,
                ];
            }
        } else {
            $cleanCookieData = json_decode($cookie_data, true);
            if (is_array($cleanCookieData)) {
                $prosesNotarisFiltered = array_filter($cleanCookieData, function ($item) use ($id, $pekerjaan_id, $kategori_id) {
                    $getId = @$item['prosesId'] ?: @$item['proses_id'];

                    return $getId == $id
                        && $item['pekerjaanId'] == $pekerjaan_id
                        && $item['kategoriPekerjaanId'] == $kategori_id;
                });
                $prosesNotarisData = !empty($prosesNotarisFiltered) ? reset($prosesNotarisFiltered) : null;
            }
        }

        $proses = ProsesPekerjaanPPAT::query()->findOrFail($id);
        $pekerjaan = PekerjaanPPAT::query()->findOrFail($pekerjaan_id);
        $kategori = KategoriPekerjaan::find($kategori_id);
        $atribut = AtributPekerjaanPPAT::query()
            ->where('proses_pekerjaan_ppat_id', $proses->id)
            ->get();

        if (!$prosesNotarisData) {
            $prosesNotarisData = [
                'prosesId' => $proses->id,
                'pekerjaanNama' => $pekerjaan->nama,
                'pekerjaan_id' => $pekerjaan->id,
                'kategoriNama' => $kategori->nama,
                'prosesNama' => $proses->nama,
                'atribut' => $atribut->toArray(),
                'catatan' => null,
                'isValidate' => 0,
            ];
        }

        $data = [
            'proses' => $proses->toArray(),
            'pekerjaan' => $pekerjaan->toArray(),
            'kategori' => $kategori->toArray(),
            'atribut' => $atribut->toArray(),
            'prosesPPAT' => $prosesNotarisData,
        ];

        return $this->responseServer(200, [
            'statusCode' => 200,
            'data' => $data,
        ]);
    }

    public function setPajak(Request $request)
    {
        $cookie_data = Cookie::get('X-HITUNG-PAJAK');
        $hitungPajak = json_decode($cookie_data, true) ?? [];

        $hitungPajakPPAT = [
            'id' => $request->id,
            'jenis_pajak' => $request->jenis_pajak,
            'acuanNilaiPajak' => $request->acuan_nilai_pajak,
            'nilaiPengurang' => $request->nilaiPengurang,
            'pihak_pertama' => $request->pihak_pertama,
            'pihak_kedua' => $request->pihak_kedua,
            'besaran_tidak_kena_pajak' => $request->nilai_tidak_kena_pajak
        ];

        $idExists = false;
        foreach ($hitungPajak as &$pajak) {
            if ($pajak['id'] == $request->id) {
                $pajak = $hitungPajakPPAT;
                $idExists = true;
                break;
            }
        }

        if (! $idExists) {
            $hitungPajak[] = $hitungPajakPPAT;
        }

        $this->addCookies('X-HITUNG-PAJAK', json_encode($hitungPajak));

        return $this->responseServer(200, [
            'statusCode' => 200,
            'message' => 'Data Pajak Sukses Disimpan',
        ]);
    }

    public function getPajak()
    {
        $data = Cookie::get('X-HITUNG-PAJAK');
        $data = stripslashes(string: $data);
        $data = json_decode($data, true);

        return $this->responseServer(200, [
            'statusCode' => 200,
            'data' => $data,
        ]);
    }

    public function getNoTransaksiCodes($jenisPekerjaan)
    {
        return $this->responseServer(200, [
            'statusCode' => 200,
            'transaction_code' => $this->generateNoTransaksi($jenisPekerjaan),
        ]);
    }

    public function setCetakSerahTerima(Request $request)
    {
        $request->validate([
            "daftar_list" => "string",
            "uraian" => "string",
            "keperluan" => "string"
        ]);

        TransaksiCetakSerahTerima::query()
            ->create(
                System::crudIdentity('create', [
                    "no_transaksi" => $request->no_transaksi,
                    "list_keperluan" => $request->daftar_list,
                    "uraian" => $request->uraian,
                    "keperluan" => $request->keperluan
                ])
            );

        return $this->responseServer(200, [
            "statusCode" => 200,
            "message" => "Sukses Membuat Data Cetak Serah Terima!"
        ]);
    }

    public function cetakPemohonFetch($no_akta)
    {
        $uniqueNoAkta = Transaksi::where('status', 1)
            ->where('no_akta', $no_akta)
            ->distinct()
            ->pluck('no_akta')
            ->first();

        if (!$uniqueNoAkta) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'No transaksi atau id transaksi tidak di temukan',
            ]);
        }

        $DB = TransaksiCetakSerahTerima::where('status', 1)
            ->where('no_transaksi', $uniqueNoAkta)
            ->orderBy('id', 'ASC')
            ->get();

        return DataTables::of($DB)->toJson();
    }

    public function cetakPemohonUpdate(Request $request, $id)
    {
        $request->validate([
            'daftar-list' => 'string',
            'uraian' => 'string',
            'keperluan' => 'string',
        ]);

        $transaksi = TransaksiCetakSerahTerima::query()->findOrFail($id);

        $transaksi->update(System::crudIdentity('update', [
            'list_keperluan' => $request->daftar_list,
            'uraian' => $request->uraian,
            'keperluan' => $request->keperluan,
        ]));

        return $this->responseServer(200, [
            'statusCode' => 200,
            'message' => 'Sukses mengubah data cetak serah terima',
        ]);
    }

    public function cetakPemohon($id)
    {
        $user = Auth::user();

        $DB = TransaksiCetakSerahTerima::select(
            'transaksi_cetak_serah_terima.uraian as uraian',
            'transaksi_cetak_serah_terima.keperluan',
            'transaksi.no_akta as no_akta',
            'pemohon.nama as pemohon_nama',
            'petugas.nama as petugas_nama'
        )
            ->join('transaksi', 'transaksi.no_akta', '=', 'transaksi_cetak_serah_terima.no_transaksi')
            ->join('petugas', 'petugas.id', '=', 'transaksi.petugas_id')
            ->join('pemohon', 'pemohon.id', '=', 'transaksi.pemohon_id')
            ->where('transaksi_cetak_serah_terima.status', 1)
            ->where('transaksi_cetak_serah_terima.id', $id)
            ->firstOrFail();


        $data = [
            'tanggalSekarang' => Carbon::now()->locale('id')->isoFormat('D MMMM Y'),
            'no_akta' => $DB->no_akta,
            'uraian' => $DB->uraian,
            'pemohon_nama' => $DB->pemohon_nama,
            'keperluan' => $DB->keperluan,
            'petugas_nama' => $user->nama,
        ];

        return view('administrator.transaksi.cetak-pemohon', $data);
    }

    public function cetakInvoices($no_akta)
    {
        // Ambil data transaksi termasuk petugas dan jenis pekerjaan
        $DB = Transaksi::select(
            'transaksi.*',
            'petugas.nama as petugas_nama',
            'pekerjaan_notaris.nama as pekerjaan_notaris_nama',
            'pekerjaan_ppat.nama as pekerjaan_ppat_nama',
            'pemohon.nama as pemohon_nama',
            'pemohon.alamat as pemohon_alamat',
            'transaksi.total as total_biaya',
            'transaksi.jenis_pajak_id as jenis_pajak',
            'transaksi.besaran_pajak_pihak_pertama as pihak_pertama',
            'transaksi.besaran_pajak_pihak_kedua as pihak_kedua',
        )
            ->join('petugas', 'petugas.id', '=', 'transaksi.petugas_id')
            ->join('pemohon', 'pemohon.id', '=', 'transaksi.pemohon_id')
            ->leftJoin('pekerjaan_notaris', function ($join) {
                $join->on('pekerjaan_notaris.id', '=', 'transaksi.pekerjaan_id')
                    ->where('transaksi.jenis_pekerjaan_id', '=', 1);
            })
            ->leftJoin('pekerjaan_ppat', function ($join) {
                $join->on('pekerjaan_ppat.id', '=', 'transaksi.pekerjaan_id')
                    ->where('transaksi.jenis_pekerjaan_id', '=', 2);
            })
            ->where('transaksi.status', 1)
            ->where('transaksi.no_akta', $no_akta)
            ->get();

        if ($DB->isEmpty()) {
            return $this->responseServer(401, [
                'statusCode' => 401,
                'message' => 'Nomor transaksi atau id yang anda pilih tidak ada',
            ]);
        }

        // Buat array untuk nama pekerjaan dan total biaya per pekerjaan
        $pekerjaanBiaya = [];
        $totalKeseluruhan = 0;

        foreach ($DB as $record) {
            $jenisPajak = '';
            $pihakPertamaLabel = '';
            $pihakKeduaLabel = '';

            if ($record->jenis_pajak == 1) {
                $jenisPajak = 'Biaya Pajak Waris';
                $pihakPertamaLabel = 'Penerima';
                $pihakKeduaLabel = 'Pewaris';
            } elseif ($record->jenis_pajak == 2) {
                $jenisPajak = 'Biaya Pajak Hibah';
                $pihakPertamaLabel = 'Pemberi';
                $pihakKeduaLabel = 'Penerima';
            } elseif ($record->jenis_pajak == 3) {
                $jenisPajak = 'AJB';
                $pihakPertamaLabel = 'Penjual';
                $pihakKeduaLabel = 'Pembeli';
            } elseif ($record->jenis_pajak == 4) {
                $jenisPajak = 'Tukar Guling';
                $pihakPertamaLabel = 'Penjual';
                $pihakKeduaLabel = 'Pembeli';
            } elseif ($record->jenis_pajak == 5) {
                $jenisPajak = 'Biaya Jasa APHB';
                $pihakPertamaLabel = 'Penerima hak';
                $pihakKeduaLabel = 'Pemberi hak';
            } elseif ($record->jenis_pajak == 6) {
                $jenisPajak = 'APHT';
                $pihakPertamaLabel = 'Penerima hak';
                $pihakKeduaLabel = 'Pemberi hak';
            }
            $totalPihak = $record->pihak_pertama + $record->pihak_kedua;

            $pekerjaan = [
                'total' => $record->total_biaya,
                'jenisPajak' => $jenisPajak,
                'pihak_pertama' => $record->pihak_pertama,
                'pihak_kedua' => $record->pihak_kedua,
                'total_pihak' => $totalPihak,
                'label_pihak_pertama' => $pihakPertamaLabel,
                'label_pihak_kedua' => $pihakKeduaLabel,
            ];

            if ($record->pekerjaan_notaris_nama) {
                $pekerjaan['nama'] = $record->pekerjaan_notaris_nama;
                $pekerjaanBiaya[] = $pekerjaan;
            }

            if ($record->pekerjaan_ppat_nama) {
                $pekerjaan['nama'] = $record->pekerjaan_ppat_nama;
                $pekerjaanBiaya[] = $pekerjaan;
            }

            $totalKeseluruhan += $record->total_biaya + $record->pihak_pertama + $record->pihak_kedua;
        }

        $konfigurasiUmum = KonfigurasiUmum::query()->first();

        $user = Auth::user();
        $petugas = Petugas::where('user_id', Auth::id())
            ->first();

        $data = [
            'tanggalSekarang' => Carbon::now()->locale('id')->isoFormat('D MMMM Y'),
            'namaPenyerah' => !$petugas ? $user->nama : $petugas->nama,
            'pekerjaanBiaya' => $pekerjaanBiaya,
            'alamatPemohon' => $DB[0]->pemohon_alamat,
            'namaPemohon' => $DB[0]->pemohon_nama,
            'totalKeseluruhan' => $totalKeseluruhan,
            'jenisPajak' => $jenisPajak,
            "konfigurasi" => $konfigurasiUmum,
        ];

        return view('administrator.transaksi.cetak-invoice', $data);
    }

    public function getListMaterai(Request $request)
    {
        $selected_id = $request->input('get_list_materai');

        $data = Materai::query()->select('id', 'keterangan as text')->where('status', 1)->get()->toArray();

        if ($selected_id != '') {
            foreach ($data as $key => $value) {
                if ($value['id'] == $selected_id) {
                    $data[$key]['selected'] = true;
                }
            }
        }
        $data = collect($data)->sortBy('id')->values()->all();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function storeJenisPekerjaanSession($dataSession)
    {
        Session::put('X-SESSION-JENIS', $dataSession);

        $sessionJenisPekerjaan = Session::get('X-SESSION-JENIS', 1);

        return $this->responseServer(200, [
            'statusCode' => 200,
            'data' => $sessionJenisPekerjaan,
        ]);
    }

    public function cookieFlusher()
    {
        $this->deleteCookies(['X-PROSES-NOTARIS']);
        $this->deleteCookies(['X-PROSES-PPAT']);

        return $this->responseServer(200, [
            'statusCode' => 200,
            'data' => 'Flush cookie successful',
        ]);
    }

    public function cookieRemover($rowIndex, $pekerjaanId, $kategoriId)
    {
        $pajakCookie = Cookie::get('X-HITUNG-PAJAK');
        $pajakCookie = json_decode($pajakCookie, true) ?: [];

        $listProsesCookie = Cookie::get('X-PROSES-PPAT');
        $listProsesCookie = json_decode($listProsesCookie, true) ?: [];

        // -- remove pajak
        $pajakSetter = collect($pajakCookie)
            ->filter(function ($item, $key) use ($rowIndex) {
                return $key != $rowIndex;
            })
            ->toArray();

        // -- remove list proses
        $listProsesSetter = collect($listProsesCookie)
            ->filter(function ($item, $key) use ($pekerjaanId, $kategoriId) {
                return !($item['pekerjaanId'] == $pekerjaanId && $item['kategoriPekerjaanId'] == $kategoriId);
            })
            ->toArray();

        // -- setter
        $this->addCookies('X-HITUNG-PAJAK', json_encode($pajakSetter));
        $this->addCookies('X-PROSES-PPAT', json_encode($listProsesSetter));

        return $this->responseServer(200, [
            'statusCode' => 200,
            'data' => 'Remove cookie successful',
        ]);
    }

    public function riwayatBayarEdit(Request $request, $id)
    {
        $id = $this->decodeId($id);
        if ($id === false) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'Data tidak ditemukan',
            ]);
        }

        $data = TransaksiRiwayatPembayaran::where('id', $id)
            ->where('status', 1)
            ->first();

        if (!$data) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'Data tidak ditemukan',
            ]);
        }

        $nominalBayar = str_replace('.', '', $request->input('jumlah_dibayar'));
        $tanggalSync = $data->tanggal_pembayaran;
        $nominalBayarSync = (int) $nominalBayar - $data->jumlah_dibayar;

        $save = System::crudIdentity('update', [
            'pembayaran_ke' => $request->input('pembayaran_ke'),
            'jumlah_dibayar' => $nominalBayar,
        ]);

        try {
            DB::beginTransaction();
            $data->update($save);

            // -- sync pendapatan
            System::syncLaporanPendapatan($tanggalSync, [
                'penghasilan' => $nominalBayarSync
            ]);

            DB::commit();

            return $this->responseServer(200, [
                'statusCode' => 200,
                'message' => 'Data berhasil disimpan',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->responseServer(500, [
                'statusCode' => 500,
                'message' => 'Gagal menyimpan data',
            ]);
        }
    }

    public function riwayatBayarDelete($id)
    {
        $id = $this->decodeId($id);
        if ($id === false) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'Data tidak ditemukan',
            ]);
        }

        $data = TransaksiRiwayatPembayaran::where('id', $id)
            ->where('status', 1)
            ->first();

        if (!$data) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'Data tidak ditemukan',
            ]);
        }

        $tanggalSync = $data->tanggal_pembayaran;
        $nominalBayarSync = $data->jumlah_dibayar;

        try {
            DB::beginTransaction();
            $data->delete();

            // -- sync pendapatan
            System::syncLaporanPendapatan($tanggalSync, [
                'penghasilan' => -1 * $nominalBayarSync
            ]);

            DB::commit();

            return $this->responseServer(200, [
                'statusCode' => 200,
                'message' => 'Data berhasil dihapus',
            ]);
        } catch (\Throwable $th) {
            return $this->responseServer(500, [
                'statusCode' => 500,
                'message' => 'Gagal menghapus data',
            ]);
        }
    }
}
