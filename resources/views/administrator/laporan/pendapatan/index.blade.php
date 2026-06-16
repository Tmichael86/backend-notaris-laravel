@extends('layout.administrator')

@section('css')
    {{-- Tambahan CSS jika diperlukan --}}
@endsection

@section('content')
    <div class="d-flex justify-content-between">
        <div class="breadcrumb">
            <h1 class="mr-2">Laporan Pendapatan</h1>
            <ul>
                <li>Page</li>
                <li>Laporan Pendapatan</li>
            </ul>
        </div>
        <div class="d-inline-block">
            <button id="export-to-excel" class="btn btn-success">
                <i class="fa fa-file-excel mr-2"></i> Excel
            </button>
        </div>
    </div>

    <div class="separator-breadcrumb border-top"></div>

    {{-- Filter Tanggal --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <!-- Filter Tanggal -->
                <div class="col-md-6 col-lg-4 mb-2">
                    <div class="input-group">
                        <input type="date" id="tanggal_awal" class="form-control bg-white" value="{{ date('Y-m-01') }}"
                            autocomplete="off">
                        <span class="input-group-text">s.d.</span>
                        <input type="date" id="tanggal_akhir" class="form-control bg-white" value="{{ date('Y-m-t') }}"
                            autocomplete="off">
                    </div>
                </div>

                <!-- Filter Jenis Pembayaran -->
                <div class="col-md-3 col-lg-2 mb-2">
                    <select id="jenis_pembayaran_filter" class="form-control">
                        <option value="">--Semua Jenis Pembayaran--</option>
                        <!-- Opsi akan diisi dari server -->
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Pendapatan --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover pendapatan-table">
                    <thead>
                        <tr class="text-center">
                            <th width="50px">#</th>
                            <th>Tanggal</th>
                            <th>Penghasilan</th>
                            <th>Pengeluaran</th>
                            <th>Pendapatan</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="2" class="text-center">Total</td>
                            <td id="total-penghasilan"></td>
                            <td id="total-pengeluaran"></td>
                            <td colspan="2" class="text-center" id="total-pendapatan"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="{{ asset('assets/plugins/xlsx/xlsx.full.min.js') }}"></script>
    <script src="{{ asset('assets/scripts/administrator/laporan/pendapatan/index.js') }}"></script>
@endsection
