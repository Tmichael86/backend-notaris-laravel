<?php

namespace App\Http\Controllers\Administrator\Master;

use App\Http\Controllers\Controller;
use App\Http\Libraries\StrHelper;
use App\Http\Libraries\System;
use App\Models\AtributPekerjaanNotaris;
use App\Models\HargaPekerjaanNotaris;
use App\Models\KategoriPekerjaan;
use App\Models\PekerjaanNotaris;
use App\Models\ProsesPekerjaanNotaris;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PekerjaanNotarisController extends Controller
{
    public function index()
    {
        $this->unlockData();
        $data['groups'] = System::getAccess();

        return view('administrator.master.pekerjaan_notaris.index', $data);
    }

    public function fetch(Request $request)
    {
        if (! System::getAccess('read')) {
            return $this->responseServer(403, [
                'statusCode' => 403,
                'message' => 'You are not authorized to access this page',
            ]);
        }
        $DB = PekerjaanNotaris::where('pekerjaan_notaris.status', 1)
            ->select(['pekerjaan_notaris.*']);

        return DataTables::of($DB)
            ->editColumn('id', function ($val) {
                return System::strEncode($val->id);
            })
            ->addColumn('harga', function ($val) {
                $data = HargaPekerjaanNotaris::select('harga')
                    ->where('pekerjaan_notaris_id', $val->id)
                    ->get()
                    ->toArray();
                $result = array_map(function ($item) {
                    return StrHelper::format_rupiah($item['harga']);
                }, $data);
                $finalResult = implode(' , ', $result);

                return $finalResult;
            })
            ->addColumn('estimasi_waktu', function ($val) {
                $datas = HargaPekerjaanNotaris::select('estimasi_waktu')
                    ->where('pekerjaan_notaris_id', $val->id)
                    ->get()
                    ->toArray();
                $results = array_map(function ($item) {
                    return $item['estimasi_waktu'];
                }, $datas);
                $finalResult = implode(' , ', $results);

                return $finalResult;
            })
            ->toJson();
    }

    public function getKategori(Request $request)
    {
        $selected_id = $request->input('selected');
        $data = KategoriPekerjaan::select(['id', 'nama as text'])->where('status', 1)->get()->toArray();
        $data[] = ['id' => 0, 'text' => '--pilih--'];
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

    public function store(Request $request)
    {
        if (!System::getAccess('create')) {
            return $this->responseServer(403, [
                'statusCode' => 403,
                'message' => 'You are not authorized to access this page',
            ]);
        }

        $validated = Validator::make($request->all(), [
            'nama' => 'required',
        ]);

        if ($validated->fails()) {
            return $this->badRequest($validated);
        }

        $data = System::crudIdentity('create', [
            'nama' => $request->input('nama'),
        ]);

        try {
            DB::beginTransaction();

            $pekerjaan = PekerjaanNotaris::create($data);

            // Menyimpan data harga jika ada
            $hargaPost = $request->input('harga');

            // Kategori unique
            $kategoriPekerjaan = $hargaPost['kategori_pekerjaan_id'];
            $kategoriIsDuplicate = System::checkArrayDuplicate($kategoriPekerjaan);
            if ($kategoriIsDuplicate) {
                throw new Exception("Kategori pekerjaan tidak boleh sama !");
            }

            if ($hargaPost) {
                $dataHargaPekerjaan = StrHelper::array_build($hargaPost);

                foreach ($dataHargaPekerjaan as $key => $value) {
                    unset($value['pekerjaan_harga_id']);

                    $dataHarga = System::crudIdentity(
                        'create',
                        array_merge($value, [
                            'pekerjaan_notaris_id' => $pekerjaan->id,
                            'harga' => StrHelper::rupiah_to_number($value['pekerjaan_harga']),
                            'kategori_pekerjaan_id' => $value['kategori_pekerjaan_id'],
                        ])
                    );

                    HargaPekerjaanNotaris::create($dataHarga);
                }
            }

            // Menyimpan data proses dan atributnya
            $prosesPost = $request->input('proses');
            $detailProses = $request->input('proses_detail');
            $atribut = $request->input('atribut');
            $atribut = array_values($atribut ?: []);

            foreach ($prosesPost as $key => $value) {
                $dataProses = System::crudIdentity('create', [
                    'pekerjaan_notaris_id' => $pekerjaan->id,
                    'nama' => $value,
                    'detail' => $detailProses[$key],
                ]);

                $proses = ProsesPekerjaanNotaris::create($dataProses);

                if (!@$atribut[$key]) {
                    continue;
                }

                foreach ($atribut[$key]['proses_pekerjaan_atribut'] as $data) {
                    if ($data == null) {
                        continue;
                    }

                    $dataAtribut = System::crudIdentity('create', [
                        'pekerjaan_notaris_id' => $pekerjaan->id,
                        'proses_pekerjaan_notaris_id' => $proses->id,
                        'atribut' => $data,
                    ]);

                    AtributPekerjaanNotaris::create($dataAtribut);
                }
            }

            // commit
            DB::commit();

            return $this->responseServer(200, [
                'statusCode' => 200,
                'message' => 'Data telah berhasil disimpan',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->responseServer(500, [
                'statusCode' => 500,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function update(Request $request, $ids)
    {
        if (!System::getAccess('update')) {
            return $this->responseServer(403, [
                'statusCode' => 403,
                'message' => 'You are not authorized to access this page',
            ]);
        }

        $id = System::strDecode($ids);
        if ($id === false) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'Data tidak ditemukan',
            ]);
        }

        $validated = Validator::make($request->all(), [
            'nama' => 'required',
        ]);

        if ($validated->fails()) {
            return $this->badRequest($validated);
        }

        $pekerjaanNotaris = PekerjaanNotaris::where('id', $id)
            ->first();

        if (!$pekerjaanNotaris) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'Data tidak ditemukan',
            ]);
        }

        $data = System::crudIdentity('update', [
            'nama' => $request->input('nama'),
        ]);

        try {
            DB::beginTransaction();

            // Update Data
            $pekerjaanNotaris->update($data);

            // -- save pekerjaan harga block
            $hargaPost = $request->input('harga');
            $dataHargaPekerjaan = StrHelper::array_build($hargaPost);
            $pekerjaanHargaIds = collect($hargaPost['pekerjaan_harga_id'])
                ->filter(function ($item) {
                    return $item != null;
                })
                ->toArray();

            // Kategori unique
            $kategoriPekerjaan = $hargaPost['kategori_pekerjaan_id'];
            $kategoriIsDuplicate = System::checkArrayDuplicate($kategoriPekerjaan);
            if ($kategoriIsDuplicate) {
                throw new Exception("Kategori pekerjaan tidak boleh sama !");
            }

            HargaPekerjaanNotaris::where('pekerjaan_notaris_id', $id)
                ->whereNotIn('id', $pekerjaanHargaIds)
                ->delete();

            foreach ($dataHargaPekerjaan as $hargaItem) {
                $hargaId = $hargaItem['pekerjaan_harga_id'];

                // -- update
                if ($hargaId) {
                    HargaPekerjaanNotaris::where('id', $hargaId)
                        ->update(
                            System::crudIdentity('update', [
                                'harga' => StrHelper::rupiah_to_number($hargaItem['pekerjaan_harga']),
                                'kategori_pekerjaan_id' => $hargaItem['kategori_pekerjaan_id'],
                                'estimasi_waktu' => $hargaItem['estimasi_waktu'],
                            ])
                        );

                    continue;
                }

                // -- insert
                HargaPekerjaanNotaris::create(
                    System::crudIdentity('create', [
                        'pekerjaan_notaris_id' => $id,
                        'harga' => StrHelper::rupiah_to_number($hargaItem['pekerjaan_harga']),
                        'kategori_pekerjaan_id' => $hargaItem['kategori_pekerjaan_id'],
                        'estimasi_waktu' => $hargaItem['estimasi_waktu'],
                    ])
                );
            }

            // -- save proses block
            $prosesPostId = $request->input('proses_id');
            $prosesPost = $request->input('proses');
            $detailProses = $request->input('proses_detail');
            $attributeProses = $request->input('atribut');
            $attributeProses = array_values($attributeProses ?: []);

            $dataProsesPekerjaan = StrHelper::array_build([
                'proses_id' => $prosesPostId,
                'proses_nama' => $prosesPost,
                'proses_detail' => $detailProses,
            ]);

            $prosesIds = collect($prosesPostId)
                ->filter(function ($item) {
                    return $item != null;
                })
                ->toArray();

            ProsesPekerjaanNotaris::where('pekerjaan_notaris_id', $id)
                ->whereNotIn('id', $prosesIds)
                ->delete();

            AtributPekerjaanNotaris::where('pekerjaan_notaris_id', $id)
                ->whereNotIn('proses_pekerjaan_notaris_id', $prosesIds)
                ->delete();

            foreach ($dataProsesPekerjaan as $key => $prosesItem) {
                $prosesId = $prosesItem['proses_id'];
                $attributeBuild = StrHelper::array_build(@$attributeProses[$key] ?: []);

                // -- update
                if ($prosesId) {
                    ProsesPekerjaanNotaris::where('id', $prosesId)
                        ->update(
                            System::crudIdentity('update', [
                                'nama' => $prosesItem['proses_nama'],
                                'detail' => $prosesItem['proses_detail'],
                            ])
                        );

                    $attributeBuild = collect($attributeBuild)
                        ->map(function ($item) use ($id, $prosesId) {
                            $item['proses_pekerjaan_id'] = $prosesId;
                            $item['pekerjaan_id'] = $id;

                            return $item;
                        })
                        ->toArray();

                    // -- save attribute
                    self::saveAttributePekerjaan($id, $prosesId, $attributeBuild);

                    continue;
                }

                // -- insert
                $prosesSave = ProsesPekerjaanNotaris::create(
                    System::crudIdentity('create', [
                        'pekerjaan_notaris_id' => $id,
                        'nama' => $prosesItem['proses_nama'],
                        'detail' => $prosesItem['proses_detail'],
                    ])
                );

                $attributeBuild = collect($attributeBuild)
                    ->map(function ($item) use ($id, $prosesSave) {
                        $item['proses_pekerjaan_id'] = $prosesSave->id;
                        $item['pekerjaan_id'] = $id;

                        return $item;
                    })
                    ->toArray();

                // -- save attribute
                self::saveAttributePekerjaan($id, $prosesSave->id, $attributeBuild);
            }

            DB::commit();

            return $this->responseServer(200, [
                'statusCode' => 200,
                'message' => 'Data telah berhasil diupdate',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->responseServer(500, [
                'statusCode' => 500,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function formEdit($ids)
    {
        if (! System::getAccess('update')) {
            return $this->responseServer(403, [
                'statusCode' => 403,
                'message' => 'You are not authorized to access this page',
            ]);
        }

        $id = System::strDecode($ids);
        if ($id === false) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'Data tidak ditemukan',
            ]);
        }

        $data = PekerjaanNotaris::where('id', $id)->first();
        $data->harga = HargaPekerjaanNotaris::where('pekerjaan_notaris_id', $id)->get();
        $data->proses = ProsesPekerjaanNotaris::where('pekerjaan_notaris_id', $id)->get();
        $atributs = AtributPekerjaanNotaris::where('pekerjaan_notaris_id', $id)->get();

        $prosesAtribut = [];

        if (! empty($data->proses)) {
            foreach ($data->proses as $proses) {
                $atributSesuai = [];

                foreach ($atributs as $atribut) {
                    if ($atribut->proses_pekerjaan_notaris_id == $proses->id) {
                        $atributSesuai[] = [
                            'id' => $atribut->id,
                            'pekerjaan_notaris_id' => $atribut->pekerjaan_notaris_id,
                            'atribut' => $atribut->atribut,
                            'created_by' => $atribut->created_by,
                            'updated_by' => $atribut->updated_by,
                            'created_at' => $atribut->created_at,
                            'updated_at' => $atribut->updated_at,
                            'status' => $atribut->status,
                        ];
                    }
                }

                $prosesAtribut[] = [
                    'id' => $proses->id,
                    'pekerjaan_notaris_id' => $proses->pekerjaan_notaris_id,
                    'nama' => $proses->nama,
                    'detail' => $proses->detail,
                    'created_by' => $proses->created_by,
                    'updated_by' => $proses->updated_by,
                    'created_at' => $proses->created_at,
                    'updated_at' => $proses->updated_at,
                    'status' => $proses->status,
                    'atribut' => $atributSesuai,
                ];
            }
        }

        $data->proses = $prosesAtribut;

        return $this->responseServer(200, $data);
    }

    public function destroy($ids)
    {
        if (! System::getAccess('delete')) {
            return $this->responseServer(403, [
                'statusCode' => 403,
                'message' => 'You are not authorized to access this page',
            ]);
        }

        $id = System::strDecode($ids);

        if ($id === false) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'Data tidak ditemukan',
            ]);
        }

        $i = PekerjaanNotaris::where('id', $id)->update(System::crudIdentity('delete'));
        ProsesPekerjaanNotaris::where('pekerjaan_notaris_id', $id)->update(System::crudIdentity('delete'));
        HargaPekerjaanNotaris::where('pekerjaan_notaris_id', $id)->update(System::crudIdentity('delete'));
        AtributPekerjaanNotaris::where('pekerjaan_notaris_id', $id)->update(System::crudIdentity('delete'));
        if (! $i) {
            return $this->responseServer(500, [
                'statusCode' => 500,
                'message' => 'Terjadi kesalahan saat hapus data',
            ]);
        }

        return $this->responseServer(200, [
            'statusCode' => 200,
            'message' => 'Data telah berhasil dihapus',
        ]);
    }

    private static function saveAttributePekerjaan($pekerjaanId, $prosesId, $arrBuild = [])
    {
        /**
         * [
         *   proses_pekerjaan_atribut_id,
         *   proses_pekerjaan_atribut,
         *   proses_pekerjaan_id,
         *   pekerjaan_id
         * ]
         */

        $attributeIds = collect($arrBuild)
            ->map(function ($e) {
                return $e['proses_pekerjaan_atribut_id'];
            })
            ->filter(function ($e) {
                return $e != null;
            })
            ->toArray();

        AtributPekerjaanNotaris::where('pekerjaan_notaris_id', $pekerjaanId)
            ->where('proses_pekerjaan_notaris_id', $prosesId)
            ->whereNotIn('id', $attributeIds)
            ->delete();

        foreach ($arrBuild as $item) {
            $attrId = $item['proses_pekerjaan_atribut_id'];

            // -- update
            if ($attrId) {
                AtributPekerjaanNotaris::where('id', $attrId)
                    ->update(
                        System::crudIdentity('update', [
                            'atribut' => $item['proses_pekerjaan_atribut'],
                        ])
                    );

                continue;
            }

            // -- insert
            AtributPekerjaanNotaris::create(
                System::crudIdentity('create', [
                    'pekerjaan_notaris_id' => $item['pekerjaan_id'],
                    'proses_pekerjaan_notaris_id' => $item['proses_pekerjaan_id'],
                    'atribut' => $item['proses_pekerjaan_atribut'],
                ])
            );
        }
    }
}
