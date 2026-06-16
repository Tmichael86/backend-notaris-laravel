@extends('layout.administrator')

@section('css')
    <style>

    </style>
@endsection

@section('content')
    <div class="d-flex justify-content-between">
        <div class="breadcrumb">
            <h1 class="mr-2">Dashboard</h1>
            <ul>
                <li>Dashboard</li>
                <li>Page</li>
            </ul>
        </div>
        <div class="col-auto">
            <div class="form-group" style="width: 130px">
                <select class="form-control select2" id="filter-tahun">
                    @foreach ($tahun as $value)
                        <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-4">
                    <i class="i-Big-Data display-4 text-primary mb-3"></i>
                    <h5 class="card-title text-muted mb-1">Pemohon</h5>
                    <p class="card-text text-primary display-4 mb-0" id="pemohon-count">0</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-4">
                    <i class="i-Big-Data display-4 text-primary mb-3"></i>
                    <h5 class="card-title text-muted mb-1">Total Transaksi</h5>
                    <p class="card-text text-primary display-4 mb-0" id="transaksi-total">0</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-4">
                    <i class="i-Big-Data display-4 text-primary mb-3"></i>
                    <h5 class="card-title text-muted mb-1">Transaksi Belum Selesai</h5>
                    <p class="card-text text-primary display-4 mb-0" id="transaksi-pending">0</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-4">
                    <i class="i-Big-Data display-4 text-primary mb-3"></i>
                    <h5 class="card-title text-muted mb-1">Transaksi Selesai</h5>
                    <p class="card-text text-primary display-4 mb-0" id="transaksi-selesai">0</p>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-6 col-md-12">
            <div class="card card-chart-bottom o-hidden mb-4">
                <div class="card-title p-3">Grafik Transaksi</div>
                <div id="echart1" style="height: 120px;"></div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <div class="card card-chart-bottom o-hidden mb-4">
                <div class="card-title p-3">Grafik Pemohon</div>
                <div id="echart2" style="height: 120px;"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="card-title">Grafik Perbandingan Transaksi</div>
                    <div id="echartBar" style="height: 300px;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-sm-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="card-title">Perbandingan Pemohon Dan Transaksi</div>
                    <div id="echartPie" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript" src="{{ asset('assets/js/plugins/echarts.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/scripts/echart.options.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/scripts/administrator/dashboard/index.js') }}"></script>
@endsection
