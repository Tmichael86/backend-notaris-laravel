<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Http\Libraries\StrHelper;
use App\Http\Libraries\System;
use App\Models\Group;
use App\Models\JenisPengeluaran;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PengeluaranController extends Controller
{
    public function index()
    {
        $this->unlockData();
        $data['groupData'] = Group::where('status', 1)->get();
        $data['groups'] = System::getAccess();

        return view('administrator.pengeluaran.index', $data);
    }

    public function fetch(Request $request)
    {

        $jenis_pengeluaran_id = $request->post('jenis_pengeluaran_id');

        if (! System::getAccess('read')) {
            return $this->responseServer(403, [
                'statusCode' => 403,
                'message' => 'You are not authorized to access this page',
            ]);
        }

        $DB = Pengeluaran::query()
            ->where('status', 1)
            // ->orderBy('tanggal_pengeluaran', 'DESC');
            ->orderBy('id', 'DESC');
            
        // if ($request->tanggal_awal && $request->tanggal_akhir) {
        //     $DB->whereBetween('tanggal_pengeluaran', [$request->tanggal_awal, $request->tanggal_akhir]);
        // }
        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $tanggal_awal = $request->tanggal_awal . ' 00:00:00';
            $tanggal_akhir = $request->tanggal_akhir . ' 23:59:59';
            
            $DB->whereBetween('tanggal_pengeluaran', [$tanggal_awal, $tanggal_akhir]);
        }
        
        if ($jenis_pengeluaran_id != 0) {
            $DB->where('jenis_pengeluaran_id', $jenis_pengeluaran_id);
        }

        $DB->get();

        return DataTables::of($DB)
            ->editColumn('id', function (Pengeluaran $pengeluaran) {
                return System::strEncode($pengeluaran->id);
            })
            ->editColumn('jumlah', function (Pengeluaran $pengeluaran) {
                return strHelper::format_rupiah($pengeluaran->jumlah);
            })
            ->addColumn('jenis_pengeluaran_nama', function (Pengeluaran $pengeluaran) {
                return $pengeluaran->jenis_nama ?? '-';
            })
            ->toJson();
    }

    public function store(Request $request)
    {
        if (! System::getAccess('create')) {
            return $this->responseServer(403, [
                'statusCode' => 403,
                'message' => 'You are not authorized to access this page',
            ]);
        }

        $validated = Validator::make($request->all(), [
            'no_faktur' => 'required',
            'jenis_pengeluaran_id' => 'required',
            'jumlah' => 'required',
            'tanggal_pengeluaran' => 'required|date_format:d/m/Y H:i:s', // Validasi format tanggal
            'keterangan' => 'required',
        ]);

        if ($validated->fails()) {
            return $this->badRequest($validated);
        }

        $pengeluaranNominal = StrHelper::rupiah_to_number($request->input('jumlah'));
        $tanggalPengeluaran = Carbon::createFromFormat('d/m/Y H:i:s', $request->input('tanggal_pengeluaran'))
            ->format('Y-m-d H:i:s');

        $tanggalSync = Carbon::parse($tanggalPengeluaran)
            ->format('Y-m-d');

        $data = System::crudIdentity('create', [
            'no_faktur' => $request->input('no_faktur'),
            'jenis_pengeluaran_id' => $request->input('jenis_pengeluaran_id'),
            'jumlah' => $pengeluaranNominal,
            'tanggal_pengeluaran' => $tanggalPengeluaran, // Gunakan format yang benar
            'keterangan' => $request->input('keterangan'),
        ]);

        // ==> Save Data
        try {
            DB::beginTransaction();
            Pengeluaran::create($data);

            // -- sync pendapatan
            System::syncLaporanPendapatan($tanggalSync, [
                'pengeluaran' => $pengeluaranNominal
            ]);

            DB::commit();

            return $this->responseServer(200, [
                'statusCode' => 200,
                'message' => 'Data telah berhasil disimpan',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->responseServer(500, [
                'statusCode' => 500,
                'message' => 'Terjadi kesalahan saat menyimpan data',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $id = System::strDecode($id);
        if ($id === false) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'Data tidak ditemukan',
            ]);
        }

        if (! System::getAccess('update')) {
            return $this->responseServer(403, [
                'statusCode' => 403,
                'message' => 'You are not authorized to access this page',
            ]);
        }

        $pengeluaran = Pengeluaran::where("id", $id)->first();

        if (!$pengeluaran) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'Data tidak ditemukan',
            ]);
        }

        $validated = Validator::make($request->all(), [
            'no_faktur' => 'required',
            'jenis_pengeluaran_id' => 'required',
            'jumlah' => 'required',
            'tanggal_pengeluaran' => 'required|date_format:d/m/Y H:i:s', // Validasi format tanggal
            'keterangan' => 'required',
        ]);

        if ($validated->fails()) {
            return $this->badRequest($validated);
        }

        $pengeluaranBefore = $pengeluaran->jumlah;
        $pengeluaranNominal = StrHelper::rupiah_to_number($request->input('jumlah'));
        $tanggalPengeluaran = Carbon::createFromFormat('d/m/Y H:i:s', $request->input('tanggal_pengeluaran'))
            ->format('Y-m-d H:i:s');

        $pengeluaranSync = $pengeluaranNominal - $pengeluaranBefore;
        $tanggalSync = Carbon::parse($tanggalPengeluaran)
            ->format('Y-m-d');
        $tanggalData = Carbon::parse($pengeluaran->tanggal_pengeluaran)
            ->format('Y-m-d');

        // ==> Save Data
        try {
            $syncSame = $tanggalSync == $tanggalData;
            DB::beginTransaction();

            $pengeluaran->update(
                System::crudIdentity('update', [
                    'jenis_pengeluaran_id' => $request->input('jenis_pengeluaran_id'),
                    'jumlah' => $pengeluaranNominal,
                    'tanggal_pengeluaran' => $tanggalPengeluaran, // Gunakan format yang benar
                    'keterangan' => $request->input('keterangan'),
                ])
            );

            // -- sync tanggal != tanggal update
            if (!$syncSame) {
                System::syncLaporanPendapatan($tanggalData, [
                    'pengeluaran' => -1 * $pengeluaranBefore
                ]);
            }

            // -- sync pendapatan
            System::syncLaporanPendapatan($tanggalSync, [
                'pengeluaran' => $syncSame ? $pengeluaranSync : $pengeluaranNominal
            ]);

            DB::commit();

            return $this->responseServer(200, [
                'statusCode' => 200,
                'message' => 'Data telah berhasil update',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->responseServer(500, [
                'statusCode' => 500,
                'message' => 'Terjadi kesalahan saat menyimpan data',
            ]);
        }
    }

    public function getJenisPengeluaran(Request $request)
    {
        $selected_id = $request->input('selected');
        $jenis_pekerjaan = $request->input('jenis_pekerjaan');

        $data = JenisPengeluaran::select(['id', 'nama as text'])->where('status', 1)->get()->toArray();
        $data[] = ['id' => 0, 'text' => '--Pilih Jenis Pengeluaran--'];
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

    public function getTotalPengeluaran(Request $request)
    {
        $tanggal_awal = $request->input('tanggal_awal');
        $tanggal_akhir = $request->input('tanggal_akhir');
        $jenis_pengeluaran_id = $request->input('jenis_pengeluaran_id');

        if (! System::getAccess('read')) {
            return $this->responseServer(403, [
                'statusCode' => 403,
                'message' => 'You are not authorized to access this page',
            ]);
        }

        $DB = Pengeluaran::where('status', 1);
        $tanggal_awal != '' ? $DB->whereDate('tanggal_pengeluaran', '>=', $tanggal_awal) : '';
        $tanggal_akhir != '' ? $DB->whereDate('tanggal_pengeluaran', '<=', $tanggal_akhir) : '';
        $jenis_pengeluaran_id != 0 ? $DB->where('jenis_pengeluaran_id', $jenis_pengeluaran_id) : '';
        $totalPengeluaran = $DB->selectRaw('SUM(CAST(jumlah AS DECIMAL)) as total_jumlah')->value('total_jumlah');

        return $this->responseServer(200, [
            'statusCode' => 200,
            'data' => $totalPengeluaran,
        ]);
    }

    public function getNoFaktur()
    {
        return $this->responseServer(200, [
            'statusCode' => 200,
            'no_faktur_code' => $this->generateNoFaktur(),
        ]);
    }

    private function generateNoFaktur()
    {
        $uId = Session::get('uid');
        $count = DB::table('pengeluaran')
            ->where(DB::raw('extract(year from created_at)'), date('Y'))
            ->where(DB::raw('extract(month from created_at)'), date('m'))
            ->count() ?: '0';
        $count++;
        $date = date('dmy');
        $userId = sprintf('%02s', $uId);

        return sprintf("{$userId}{$date}%04d", $count);
    }

    public function destroy($id)
    {
        if (!System::getAccess('delete')) {
            return $this->responseServer(403, [
                'statusCode' => 403,
                'message' => 'You are not authorized to access this page',
            ]);
        }

        $id = $this->decodeId($id);
        if ($id === false) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'Data tidak ditemukan',
            ]);
        }

        $pengeluaran = Pengeluaran::where('id', $id)
            ->first();

        if (!$pengeluaran) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'Data tidak ditemukan',
            ]);
        }

        $pengeluaranBefore = $pengeluaran->jumlah;
        $tanggalData = Carbon::parse($pengeluaran->tanggal_pengeluaran)
            ->format('Y-m-d');

        try {
            DB::beginTransaction();
            $pengeluaran->update(System::crudIdentity('delete'));

            // -- sync pendapatan
            System::syncLaporanPendapatan($tanggalData, [
                'pengeluaran' => -1 * $pengeluaranBefore,
            ]);

            DB::commit();

            return $this->responseServer(200, [
                'statusCode' => 200,
                'message' => 'Data telah berhasil dihapus',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->responseServer(500, [
                'statusCode' => 500,
                'message' => 'Terjadi kesalahan saat hapus data',
            ]);
        }
    }
}
