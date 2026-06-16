@extends('layout.administrator')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/users/index.css') }}">
    <style>
        table.dataTable.table-proses tbody tr:hover {
            background-color: #c3d0d3 !important;
            /* cursor: pointer; */
        }

        .row-action-detail-proses {
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <div class="d-flex justify-content-between">
        <div class="breadcrumb">
            <h1 class="mr-2">Monitoring</h1>
            <ul>
                <li>Page</li>
                <li>Monitoring</li>
            </ul>
        </div>
        <div class="d-inline-block">
        </div>
    </div>
    <div class="separator-breadcrumb border-top"></div>
    <div class="card mb-2">
        <div class="card-body">
            <div class="row pt-4 pb-3">
                <div class="col-md-3">
                    <div class="input-group flex-nowrap">
                        <input autocomplete="off" type="date" id="tanggal_awal" class="form-control bg-white">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="addon-wrapping">Sd</span>
                        </div>
                        <input autocomplete="off" type="date" id="tanggal_akhir" class="form-control bg-white"
                            value="{{ date('Y-m-t') }}">
                    </div>
                </div>
                <div class="col-md-1">
                    <select name="filter_jenis_pekerjaan_id" id="filter_jenis_pekerjaan_id" class="form-control select2"
                        target="filter_jenis_pekerjaan_id">
                        <option value="0">--Pilih Jenis Pekerjaan--</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="filter_pekerjaan_id" id="filter_pekerjaan_id" class="form-control select2"
                        target="filter_pekerjaan_id">
                        <option value="0">--Pilih Pekerjaan--</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="filter_kategori_pekerjaan_id" id="filter_kategori_pekerjaan_id"
                        class="form-control select2" target="filter_kategori_pekerjaan_id">
                        <option value="0">--Pilih Kategori Pekerjaan--</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="filter_status_id" id="filter_status_id" class="form-control select2"
                        target="filter_status_id">
                        <option value="0">--Pilih Status--</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="filter_petugas_id" id="filter_petugas_id" class="form-control select2"
                        target="filter_petugas_id">
                        <option value="0">--Pilih Petugas--</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-monitoring table-bordered table-hovered">
                    <thead>
                        <tr class="text-center">
                            <th rowspan="2">#</th>
                            <th rowspan="2">Daftar</th>
                            <th rowspan="2">Selesai</th>
                            <th rowspan="2">No Akta</th>
                            <th colspan="2">Pemohon</th>
                            <th rowspan="2">Jenis Pekerjaan</th>
                            <th rowspan="2">Pekerjaan</th>
                            <th rowspan="2">Kategori</th>
                            <th rowspan="2">Estimasi Waktu</th>
                            {{-- <th rowspan="2">Proses</th> --}}
                            <th rowspan="2">Jumlah Tagihan</th>
                            <th rowspan="2">Sisa Tagihan</th>
                            <th rowspan="2">Petugas</th>
                            <th rowspan="2">Status</th>
                        </tr>
                        <tr>
                            <th>NIK</th>
                            <th>Nama</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="modal-detail-proses">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Monitoring</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <input type="hidden" id="id_proses" name="id_proses">
                                <label for="detail_proses_pekerjaan">Pekerjaan</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-white" target="detail_proses_pekerjaan"
                                        name="detail_proses_pekerjaan" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="detail_proses_kategori">Kategori</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-white" target="detail_proses_kategori"
                                        name="detail_proses_kategori" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-proses table-bordered hover">
                                    <thead>
                                        <tr>
                                            <td class="text-center">
                                                #
                                            </td>
                                            <td class="text-center text-nowrap">Daftar Proses</td>
                                            <td class="text-center text-nowrap">Proses</td>
                                            <td class="text-center text-nowrap">Waktu Pengerjaan</td>
                                            <td class="text-center text-nowrap">Petugas</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal detail Proses -->
    <div class="modal fade" id="modal-detail-catatan">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Proses</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <input type="hidden" id="id_proses" name="id_proses">
                                <label for="detail_proses_pekerjaan">Pekerjaan</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-white" target="detail_proses_pekerjaan"
                                        name="detail_proses_pekerjaan" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="detail_proses_kategori">Kategori</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-white" target="detail_proses_kategori"
                                        name="detail_proses_kategori" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="detail_proses_nama">Proses</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-white" target="detail_proses_nama"
                                        name="detail_proses_nama" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="detail_proses_waktu_pengerjaan">Waktu Pengerjaan</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-white"
                                        target="detail_proses_waktu_pengerjaan" name="detail_proses_waktu_pengerjaan"
                                        readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="card card-dashed">
                                <div class="card-body">
                                    <p id="detail_proses">-</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="form-group">
                                <label for="detail_proses_catataan">Catatan</label>
                                <div class="input-group">
                                    <textarea class="form-control bg-white" readonly name="detail_proses_catataan" id="detail_proses_catataan"
                                        row="10"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" id="backButton">Kembali</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        const access = {
            update: "{{ $groups->update }}",
            delete: "{{ $groups->delete }}"
        };
    </script>
    <script src="{{ asset('assets/scripts/administrator/monitoring/index.js') }}"></script>
@endsection
