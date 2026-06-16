@extends('layout.administrator')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/users/index.css') }}">
    <style></style>
@endsection

@section('content')
    <div class="d-flex justify-content-between">
        <div class="breadcrumb">
            <h1 class="mr-2">Piutang</h1>
            <ul>
                <li>Page</li>
                <li>Piutang</li>
            </ul>
        </div>
        <div class="d-inline-block">
            <button id="export-to-excel" class="btn btn-success">
                <i class="fa fa-file-excel mr-2"></i> Excel
            </button>
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
                            value="<?= date('Y-m-t') ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="filter_jenis_pekerjaan_id" id="filter_jenis_pekerjaan_id" class="form-control select2"
                        target="filter_jenis_pekerjaan_id">
                        <option value="0">--pilih--</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hovered">
                    <thead>
                        <tr class="text-center">
                            <th rowspan="2">#</th>
                            <th rowspan="2">No Akta</th>
                            <th colspan="2">Pemohon</th>
                            <th rowspan="2">Jenis Pekerjaan</th>
                            <th rowspan="2">Pekerjaan</th>
                            <th rowspan="2">Kategori</th>
                            <th rowspan="2">Total Tagihan</th>
                            <th rowspan="2">Total Piutang</th>
                            <th rowspan="2">Petugas</th>
                        </tr>
                        <tr>
                            <th>NIK</th>
                            <th>Nama</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="8" class="text-right">Total Piutang:</th>
                            <th id="total_piutang_footer"></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
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
    <script src="{{ asset('assets/plugins/xlsx/xlsx.full.min.js') }}"></script>
    <script src="{{ asset('assets/scripts/administrator/piutang/index.js') }}"></script>
@endsection
