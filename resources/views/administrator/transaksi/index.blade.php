@extends('layout.administrator')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/users/index.css') }}">
    <style>
        #radioBtn .notActive {
            color: #336fb9;
            background-color: #fff;
        }

        #radioBtnStatus .notActive {
            color: #336fb9;
            background-color: #fff;
        }

        .card-dashed {
            border: 1px #ced4da solid;
        }

        table.dataTable tbody tr:hover {
            background-color: #c3d0d3 !important;
            /* cursor: pointer; */
        }
    </style>
@endsection

@section('content')
    <div class="d-flex justify-content-between">
        <div class="breadcrumb">
            <h1 class="mr-2">Transaksi</h1>
            <ul>
                <li>Page</li>
                <li>Transaksi</li>
            </ul>
        </div>
    </div>
    <div class="separator-breadcrumb border-top"></div>
    <div class="row">
        <div class=" col-md-9">
            <div class="card">
                <div class="card-body bg-white">
                    <div class="row ml-2">
                        <div class="col-auto">
                            <label for="" class="mt-2">Jenis Pekerjaan</label>
                        </div>
                        <div class="col">
                            <div class="input-group">
                                <div id="radioBtn" class="btn-group">
                                    @foreach ($jenisPekerjaan as $item)
                                        <a class="btn btn-info {{ $item->id == 1 ? 'active' : 'notActive' }}"
                                            data-toggle="jenis_pekerjaan"
                                            data-title="{{ $item->id }}">{{ $item->nama }}</a>
                                    @endforeach
                                </div>
                                <input type="hidden" name="jenis_pekerjaan" id="jenis_pekerjaan">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="row ml-2">
                                <div class="col-auto">
                                    <label for="" class="mt-2">No. Transaksi</label>
                                </div>
                                <div class="col">
                                    <div class="input-group mb-2 mx-2">
                                        <input type="hidden" id="transaksi_id">
                                        <input type="text" class="form-control bg-white rounded-left ml-1"
                                            id="no_akta_notaris" value="{{ $noAkta }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-info rounded-right" type="button"
                                                id="btn-pilih-no-akta"><i class="fas fa-search mr-1"></i> Cari No.
                                                Transaksi</button>
                                        </div>
                                        <small class="message-error text-danger"
                                            data-target="no_akta_notaris_kode_error"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row mr-2">
                                <div class="col-md-8 text-right">
                                    <label for="" class=" mt-2">Tanggal Registrasi</label>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" id="tanggal_daftar" class="form-control datepicker"
                                            value="{{ date('d/m/Y') }}" name="tanggal_daftar">
                                        <div class="input-group-append">
                                            <div class="input-group-text rounded-right"><i class="fas fa-calendar"></i>
                                            </div>
                                        </div>
                                        <small class="message-error text-danger" data-target="tanggal_daftar_error"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="px-4 py-1">
                                <button type="button" class="btn btn-info mr-1" id="btn-pilih-pemohon">
                                    <i class="fas fa-search mr-1" aria-hidden="true"></i> Pilih Pemohon
                                </button>
                                @if ($pemohonCreate)
                                    <button type="button" class="btn btn-success ml-1" id="btn-tambah-pemohon">
                                        <i class="fas fa-plus mr-1" aria-hidden="true"></i> Tambah Pemohon
                                    </button>
                                @endif
                                <div class="mt-2">
                                    <small class="message-error text-danger" data-target="pemohon_id_error"></small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row mr-2">
                                <div class="col-md-8 text-right">
                                    <label for="" class=" mt-2">Tanggal Deadline</label>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" id="tanggal_selesai" class="form-control datepicker"
                                            name="tanggal_selesai">
                                        <div class="input-group-append">
                                            <div class="input-group-text rounded-right"><i class="fas fa-calendar"></i>
                                            </div>
                                        </div>
                                        <small class="message-error text-danger"
                                            data-target="tanggal_selesai_error"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row ml-3 mt-2 mb-2 mr-3">
                                <div class="col-md-12 card card-dashed">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">NIK</div>
                                                    <input type="hidden" id="pemohon_id" value="">
                                                    <div class="col-md-8">
                                                        <span class="form-item" id="pemohon_nik">: -</span>
                                                    </div>
                                                    <div class="col-md-4">Jenis Kelamin</div>
                                                    <div class="col-md-8">
                                                        <span class="form-item" id="pemohon_jenis_kelamin">: -</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">Nama</div>
                                                    <input type="hidden" id="pemohon_id" value="">
                                                    <div class="col-md-8">
                                                        <span class="form-item" id="pemohon_nama">: -</span>
                                                    </div>

                                                    <div class="col-md-4"> No. HP</div>
                                                    <div class="col-md-8">
                                                        <span class="form-item" id="pemohon_no_telp">: -</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="">Alamat</label>
                                                <input id="pemohon_alamat" class="form-control bg-white" type="text"
                                                    value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card m-3 card-dashed">
                                <div class="card-body">

                                    {{-- Start Notaris Container --}}
                                    <div class="notaris-container d-none">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="hidden" name="id">
                                                <div class="col-xl-12 col-md-12 col-12">
                                                    <div class="form-group">
                                                        <label for="pekerjaan_id">Nama Pekerjaan</label>
                                                        <div class="input-group">
                                                            <select name="pekerjaan_id" class="form-control select2"
                                                                id="pekerjaan_id" target="pekerjaan_id">
                                                                <option value="">-</option>
                                                            </select>
                                                        </div>
                                                        <small class="message-error text-danger"
                                                            data-target="pekerjaan_id_error"></small>
                                                    </div>
                                                </div>
                                                <div class="col-xl-12 col-md-12 col-12">
                                                    <div class="form-group">
                                                        <label for="kategori_pekerjaan_id">Kategori</label>
                                                        <div class="input-group">
                                                            <select target="kategori_pekerjaan_id"
                                                                name="kategori_pekerjaan_id" class="form-control select2"
                                                                target="kategori_pekerjaan_id" id="kategori_pekerjaan_id">
                                                                <option value="">-</option>
                                                            </select>
                                                        </div>
                                                        <small class="message-error text-danger"
                                                            data-target="kategori_pekerjaan_id_error"></small>
                                                    </div>
                                                </div>
                                                <div class="col-xl-12 col-md-12 col-12">
                                                    <div class="form-group">
                                                        <label for="estimasi_waktu">Estimasi Waktu</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control bg-white"
                                                                target="estimasi_waktu" name="estimasi_waktu">
                                                        </div>
                                                        <small class="message-error text-danger"
                                                            data-target="estimasi_waktu_error"></small>
                                                    </div>
                                                </div>
                                                <div class="col-xl-12 col-md-12 col-12">
                                                    <div class="form-group">
                                                        <label for="biaya_layanan">Biaya Layanan</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text">Rp</div>
                                                            </div>
                                                            <input type="text" class="form-control bg-white"
                                                                target="biaya_layanan" id="biaya_layanan" value="0"
                                                                name="biaya_layanan" readonly>
                                                        </div>
                                                        <small class="message-error text-danger"
                                                            data-target="biaya_layanan_error"></small>
                                                    </div>
                                                </div>
                                                <div class="col-xl-12 col-md-12 col-12">
                                                    <div class="form-group">
                                                        <label for="biaya_lainnya">Biaya Lainya</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text">Rp</div>
                                                            </div>
                                                            <input type="text" class="form-control bg-white"
                                                                id="biaya_lainya" name="biaya_lainnya"
                                                                onkeyup="formatUang(this)">
                                                        </div>
                                                        <small class="message-error text-danger"
                                                            data-target="biaya_lainnya_error"></small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-3">
                                                <div class="table-responsive">
                                                    <table class="table table-proses table-bordered table-hovered">
                                                        <thead>
                                                            <tr>
                                                                <td class="text-center text-nowrap">
                                                                    #
                                                                </td>
                                                                <td class="text-center text-nowrap">Daftar Proses</td>
                                                                <td class="text-center text-nowrap">Status Proses</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- End Notaris Container --}}

                                    {{-- Start Container PPAT --}}
                                    <div class="ppat-container d-none">
                                        <div class="table-responsive">
                                            <table class="table table-ppat table-bordered table-hovered">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th class="text-center">
                                                            <button
                                                                class="btn btn-icon btn-md btn-danger btn-add-transaksi-ppat"
                                                                type="button">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </th>
                                                        <th>Nama Pekerjaan</th>
                                                        <th>Kategori</th>
                                                        <th>Estimasi Waktu</th>
                                                        <th>Biaya Layanan</th>
                                                        <th>Biaya Lainnya</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-center">
                                                            <button type="button"
                                                                class="btn btn-icon btn-md btn-danger text-center"
                                                                onclick="removeRowTransaksi(this)">
                                                                <i class="fas fa-minus"></i>
                                                            </button>
                                                        </td>

                                                        <td>
                                                            <div class="input-group">
                                                                <select name="pekerjaan[pekerjaan_ppat_id][]"
                                                                    target="pekerjaan_ppat_id" id="pekerjaan_ppat_id"
                                                                    class="form-control select2">
                                                                    <option value="0">-- Pilih Pekerjaan --
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </td>

                                                        <td>
                                                            <div class="input-group">
                                                                <select
                                                                    name="kategori_pekerjaan[kategori_pekerjaan_ppat_id][]"
                                                                    target="kategori_pekerjaan_ppat_id"
                                                                    id="kategori_pekerjaan_ppat_id"
                                                                    class="form-control select2">
                                                                    <option value="0">-- Pilih Kategori --
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </td>

                                                        <td>
                                                            <input type="text" class="form-control"
                                                                name="estimasi_waktu[]" target="estimasi_waktu" readonly>
                                                        </td>

                                                        <td>
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text">Rp</div>
                                                                    </div>
                                                                    <input type="text" class="form-control bg-white"
                                                                        target="biaya_layanan" value="0"
                                                                        name="biaya_layanan[biaya_layanan_ppat][]"
                                                                        readonly>
                                                                </div>
                                                                <small class="message-error text-danger"
                                                                    data-target="biaya_layanan_error"></small>
                                                            </div>
                                                        </td>

                                                        <td>
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text">Rp</div>
                                                                    </div>
                                                                    <input type="text" class="form-control bg-white"
                                                                        target="biaya_lainnya"
                                                                        name="biaya_lainnya[biaya_lainya_ppat][]"
                                                                        onkeyup="formatUang(this)">
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">

                                                                {{-- Input Data Value --}}
                                                                <input type="hidden" target="nilai_pajak">
                                                                {{-- End Input Data Value --}}

                                                                <button target="kalkulator_pajak"
                                                                    id="btn_kalkulator_pajak"
                                                                    class="btn btn-primary w-100"><i
                                                                        class="fa fa-calculator" aria-hidden="true"></i>
                                                                    Kalkulator Pajak</button>
                                                                <button target="list_proses"
                                                                    class="btn btn-list-ppat btn-success w-100"> <i
                                                                        class="fa fa-tasks" aria-hidden="true"></i> Daftar
                                                                    Proses</button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    {{-- End PPAT Container --}}

                                    {{-- Start PPAT Form Clone --}}
                                    <div class="d-none">
                                        <div id="clone_aksi_transaksi">
                                            <div class="text-center">
                                                <input type="hidden" name="transaksi_ppat_id[]">
                                                <button type="button" class="btn btn-icon btn-md btn-danger text-center"
                                                    onclick="removeRowTransaksi(this)">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div id="clone_pekerjaan_ppat_id">
                                            <select name="pekerjaan[pekerjaan_ppat_id][]" class="form-control">
                                            </select>
                                        </div>

                                        <div id="clone_kategori_pekerjaan_id">
                                            <select name="kategori_pekerjaan[kategori_pekerjaan_ppat_id][]"
                                                class="form-control select2">
                                            </select>
                                        </div>

                                        <div id="clone_estimasi_waktu">
                                            <input type="text" class="form-control" name="estimasi_waktu[]"
                                                target="estimasi_waktu" target="estimasi_waktu" readonly>
                                        </div>

                                        <div id="clone_biaya_layanan">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">Rp</div>
                                                    </div>
                                                    <input type="text" class="form-control bg-white"
                                                        name="biaya_layanan[biaya_layanan_ppat][]" target="biaya_layanan"
                                                        readonly>
                                                </div>
                                                <small class="message-error text-danger"
                                                    data-target="biaya_layanan_error"></small>
                                            </div>
                                        </div>

                                        <div id="clone_biaya_lainya">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">Rp</div>
                                                    </div>
                                                    <input type="text" class="input-biaya-lainya form-control bg-white"
                                                        target="biaya_lainnya" name="biaya_lainnya[biaya_lainya_ppat][]"
                                                        onkeyup="formatUang(this)">
                                                </div>
                                            </div>
                                        </div>

                                        <div id="clone_aksi">
                                            <div class="form-group">

                                                {{-- Start Input Untuk Menampung Data --}}
                                                <input type="hidden" target="nilai_pajak">

                                                <input type="hidden" class="selected_pajak" id="selected_pajak"
                                                    name="selected[selected_pajak[]]">
                                                <input type="hidden" id="pihak-pertama"
                                                    name="besaran_pajak_pertama[besaran_pajak_pihak_pertama[]]">
                                                <input type="hidden" id="pihak-kedua"
                                                    name="besaran_pajak_kedua[besaran_pajak_pihak_kedua[]]">
                                                {{-- End Input Data Untuk Menampung Input --}}

                                                {{-- Status PPAT --}}
                                                <input type="hidden" name="status_ppat[]" value="1">
                                                <input type="hidden" name="judul_ppat[]">
                                                <input type="hidden" name="no_akta_ppat[]">
                                                <input type="hidden" name="tgl_akta_ppat[]">

                                                <button class="btn btn-primary w-100" id="btn_kalkulator_pajak">
                                                    <i class="fa fa-calculator mr-1" aria-hidden="true"></i> Kalkulator
                                                    Pajak
                                                </button>
                                                <button class="btn btn-list-ppat btn-success w-100">
                                                    <i class="fa fa-tasks mr-1" aria-hidden="true"></i> Daftar Proses
                                                </button>
                                                <button class="btn btn-status-ppat btn-info w-100">
                                                    <i class="fa fa-feather mr-1" aria-hidden="true"></i> Status PPAT
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- End PPAT CLONE --}}

                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row mx-2 my-2">
                                <div class="col-md-3">
                                    <label for="jumlah_materai">Jumlah Materai</label>
                                    <div>
                                        <input class="form-control" type="number" name="jumlah_materai"
                                            id="jumlah_materai">
                                        <small class="message-error text-danger"
                                            data-target="jumlah_materai_error"></small>
                                    </div>
                                </div>

                                <!-- Kolom untuk Status -->
                                <div class="col-auto">
                                    <label for="status">Status</label>
                                    <div class="input-group">
                                        <div id="radioBtnStatus" class="btn-group">
                                            @foreach ($transaksiStatus as $val)
                                                <a class="btn btn-info statusBtn {{ $val->id == 1 ? 'active' : 'notActive' }}"
                                                    data-toggle="status"
                                                    data-title="{{ $val->id }}">{{ $val->nama }}</a>
                                            @endforeach
                                        </div>
                                        <input type="hidden" name="status" id="status">
                                        <small class="message-error text-danger" data-target="status_error"></small>
                                    </div>
                                </div>

                                <!-- Kolom untuk Cetak Dokumen Serah Terima -->
                                {{-- <div class="col-md-4">
                                    <label for="status">Cetak</label>
                                    <div>
                                        <a href="#" class="btn btn-success mr-1" id="cetakInvoices">
                                            <i class="fas fa-print"></i> Cetak Invoice
                                        </a>
                                        <a href="#" class="btn btn-success ml-1" id="cetakPemohon"
                                            data-toggle="modal" data-target="#modalCetakPemohon">
                                            <i class="fas fa-print"></i> Cetak Serah Terima
                                        </a>
                                    </div>
                                </div> --}}
                                <div class="col-md-4">
                                    <label for="status">Cetak</label>
                                    <div class="d-flex flex-nowrap gap-2">
                                        <a href="#" class="btn btn-success mr-2" id="cetakInvoices">
                                            <i class="fas fa-print"></i> Cetak Invoice
                                        </a>
                                        <a href="#" class="btn btn-success" id="cetakPemohon" data-toggle="modal"
                                            data-target="#modalCetakPemohon">
                                            <i class="fas fa-print"></i> Cetak Serah Terima
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card m-3 p-4 card-dashed d-none" id="additional-form-notaris">
                    <div class="card-body">
                        <div>
                            <p class="font-weight-600"># Form Notaris Status Selesai</p>
                        </div>
                        <div class="d-flex flex-column w-50">
                            <div class="form-group">
                                <label for="judul">Judul</label>
                                <input type="text" class="form-control" id="judul">
                            </div>

                            <div class="form-group">
                                <label for="no_akta">Nomor Akta</label>
                                <input type="text" class="form-control" id="nomor_akta">
                            </div>

                            <div class="form-group">
                                <label for="tanggal_akta">Tanggal Akta</label>
                                <input type="date" class="form-control" id="tanggal_akta">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="col-md-3">
            <div class="card">
                <div class="card-body bg-white">
                    <div class="row">
                        <div class="col-12 cari_supplier">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-info" id="btn-pilih-petugas"><i
                                            class="fas fa-search" aria-hidden="true"></i> Pilih Petugas</button>
                                </div>
                            </div>
                            <small class="message-error text-danger" data-target="petugas_id_error"></small>
                        </div>
                        <div class="col-md-4 mt-2">Nama :</div>
                        <input type="hidden" id="petugas_id" value="">
                        <div class="col-md-8 mt-2 text-right">
                            <span class="form-item" id="petugas_nama">-</span>
                        </div>
                        <div class="col-md-5 mt-2">Jenis Kelamin :</div>
                        <div class="col-md-7 mt-2 text-right">
                            <span class="form-item" id="petugas_jenis_kelamin">-</span>
                        </div>
                        <div class="col-md-4">No. HP :</div>
                        <div class="col-md-8 text-right">
                            <span class="form-item" id="petugas_no_telp">-</span>
                        </div>
                    </div>
                    <div class="border-top mt-2 mb-2"></div>
                    <div class="row">
                        <div class="col-md-5">
                            Total Biaya
                        </div>
                        <div class="col-md-7 text-right">
                            <span id="transaksi_sub_total" class="tot_bay_text">Rp&nbsp;0,00</span>
                        </div>
                        <div class="col-md-6">Potongan</div>
                        <div class="col-md-6 text-right">
                            <div class="input-group">
                                <input type="text" name="potongan_harga" id="potongan_harga" class="form-control"
                                    value="0">
                            </div>
                        </div>
                    </div>
                    <div class="border-top mt-2"></div>
                    <div class="row mt-2">
                        <div class="col-md-5">
                            Total Netto(Rp)
                        </div>
                        <div class="col-md-7 text-right">
                            <span id="transaksi_total" class="tot_bay_text">Rp&nbsp;0,00</span>
                        </div>
                    </div>
                    <div class="border-top mt-2 mb-2"></div>
                    <div class="row">
                        <div class="col-md-6">
                            Jenis Pembayaran
                        </div>
                        <div class="col-md-6">
                            <select id="jenis_pembayaran_id" name="jenis_pembayaran_id" class="form-control select2">
                                jeniPembayaran
                                @foreach ($jeniPembayaran as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                            <small class="message-error text-danger" data-target="jenis_pembayaran_id_error"></small>
                        </div>
                    </div>
                    <div>
                        <div class="border-top mt-2 mb-2"></div>
                        <div class="row">
                            <div class="col-md-6">Jatuh Tempo</div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" id="jatuh_tempo" class="form-control datepicker">
                                    <div class="input-group-append">
                                        <div class="input-group-text rounded-right"><i class="fas fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <small class="message-error text-danger" data-target="jatuh_tempo_error"></small>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">Riwayat Pembayaran</div>
                            <div class="col-md-6">
                                <a class="btn btn-info btn-sm text-white" id="btn-riwayat-pembayaran"> Lihat Riwayat</a>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">Sisa Pembayaran</div>
                            <div class="col-md-6">
                                <input type="hidden" id="jumlah_pembayaran" value="0">
                                <input type="text" id="sisa_pembayaran" class="form-control" value="0" disabled>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">Pembayaran Sekarang</div>
                            <div class="col-md-6">
                                <input type="text" name="pembayaran_sekarang" id="pembayaran_sekarang" value="0"
                                    class="form-control bg-white">
                            </div>
                            <small class="message-error text-danger" data-target="pembayaran_sekarang_error"></small>
                        </div>
                        <div class="row mt-2 d-none" id="div_kembalian">
                            <div class="col-md-6">Kembalian</div>
                            <div class="col-md-6 text-right">
                                <span id="kembalian" class="tot_bay_text">Rp&nbsp;0,00</span>
                                <input type="hidden" value="0" id="hidden_kembalian">
                            </div>
                        </div>
                    </div>
                    <div class="border-top mt-2 mb-2"></div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Keterangan</label>
                            <textarea id="keterangan" class="form-control bg-white"></textarea>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-success float-right d-none"
                                id="simpan_transaksi_baru"><i class="fas fa-check"></i> Simpan</button>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Cari No aktanotaris -->
    <div class="modal fade" id="modal-no-akta">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Data Transaksi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="table-data-transaksi">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="text-nowrap" width="20%">No. Transaksi</th>
                                    <th class="text-nowrap">Pekerjaan</th>
                                    <th class="text-nowrap">Nama Pemohon</th>
                                    <th class="text-nowrap">No Telp</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                            class="fas fa-times mr-2"></i>
                        Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cari Petugas -->
    <div class="modal fade" id="modal-petugas">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Data Petugas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="table-petugas">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="text-nowrap" width="20%">Nama</th>
                                    <th class="text-nowrap">Jenis Kelamin</th>
                                    <th>No.HP</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                            class="fas fa-times mr-2"></i>
                        Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cari Pemohon -->
    <div class="modal fade" id="modal-pemohon">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Data Pemohon</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="table-pemohon">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="text-nowrap">NIK</th>
                                    <th class="text-nowrap" width="20%">Nama</th>
                                    <th class="text-nowrap">Jenis Kelamin</th>
                                    <th>No.HP</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                            class="fas fa-times mr-2"></i>
                        Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Pemohon -->
    <div class="modal fade" id="modal-tambah-pemohon">
        <div class="modal-dialog" role="document">
            <form id="form-pemohon" method="post" action="{{ url('administrator/master/pemohon') }}"
                enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pemohon</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body mx-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="hidden" name="id">
                                            <label>NIK</label>
                                            <input name="nik" class="form-control" type="number" autocomplete="off"
                                                required>
                                            <small class="message-error text-danger" data-target="nik_error"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="hidden" name="id">
                                            <label>Nama</label>
                                            <input name="nama" class="form-control" type="text" autocomplete="off"
                                                required>
                                            <small class="message-error text-danger" data-target="nama_error"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Jenis Kelamin</label>
                                            <select name="jenis_kelamin" target="jenis_kelamin"
                                                class="form-control select2" required>
                                                <option value="" disabled>--pilih--</option>
                                            </select>
                                            <small class="message-error text-danger"
                                                data-target="jenis_kelamin_error"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Telp</label>
                                            <input name="no_telp" class="form-control" type="text"
                                                autocomplete="off">
                                            <small class="message-error text-danger" data-target="no_telp_error"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mt-2">
                                    <label>Alamat</label>
                                    <textarea name="alamat" class="form-control" rows="5"></textarea>
                                    <small class="message-error text-danger" data-target="alamat_error"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                                class="fas fa-times mr-2"></i> Tutup</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i> Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal detail Proses notaris-->
    <div class="modal fade" id="modal-detal-proses">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Proses Notaris</h5>
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
                                    <input type="text" class="form-control bg-white" id="detail_proses_pekerjaan"
                                        name="detail_proses_pekerjaan" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="detail_proses_kategori">Kategori</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-white" id="detail_proses_kategori"
                                        name="detail_proses_kategori" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="detail_proses_nama">Proses</label>
                                <input type="text" class="form-control bg-white" id="detail_proses_nama"
                                    name="detail_proses_nama" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3" id="label-atribut-container">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Daftar List</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="_proses_catatdetailan">Catatan</label>
                                <div class="input-group">
                                    <textarea class="form-control bg-white" name="detail_proses_catatan" id="proses_catatan" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label for="validasi_proses">Validasi Proses</label>
                            <div class="row-action">
                                <label class="switch switch-info">
                                    <input type="checkbox" name="validasi" id="validasi">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                            class="fas fa-times mr-2"></i>
                        Tutup</button>
                    <button type="button" class="btn btn-success" id="simpan-proses-notaris"><i
                            class="fas fa-check mr-2"></i> Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal detail Proses PPAT-->
    <div class="modal fade" id="modal-detal-proses-ppat">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Proses PPAT</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <input type="hidden" id="id_proses_ppat" name="id_proses_ppat">
                                <input type="hidden" id="id_pekerjaan_ppat" name="id_pekerjaan_ppat">
                                <input type="hidden" id="id_kategori_pekerjaan_ppat" name="id_kategori_pekerjaan_ppat">
                                <label for="detail_proses_pekerjaan_ppat">Pekerjaan</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-white" id="detail_proses_pekerjaan_ppat"
                                        name="detail_proses_pekerjaan_ppat" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="detail_proses_kategori_ppat">Kategori</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-white" id="detail_proses_kategori_ppat"
                                        name="detail_proses_kategori_ppat" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="detail_proses_nama_ppat">Proses</label>
                                <input type="text" class="form-control bg-white" id="detail_proses_nama_ppat"
                                    name="detail_proses_nama_ppat" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3" id="label-atribut-container-ppat">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Daftar List</label>
                            </div>
                            <div class="card">
                                <div class="card-body">

                                    <div class="row">
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="_proses_catatdetailan_ppat">Catatan</label>
                                <div class="input-group">
                                    <textarea class="form-control bg-white" name="detail_proses_catatan_ppat" id="proses_catatan_ppat" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label for="validasi_proses">Validasi Proses</label>
                            <div class="row-action">
                                <label class="switch switch-info">
                                    <input type="checkbox" name="validasi_ppat" id="validasi_ppat">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                            class="fas fa-times mr-2"></i>
                        Tutup</button>
                    <button type="button" class="btn btn-success" id="simpan-proses-ppat"><i
                            class="fas fa-check mr-2"></i> Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- begin Modal Riwayat Pembayaran -->
    <div class="modal fade" id="modal_riwayat_pembayaran" tabindex="" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="documpent">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Riwayat Pembayaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">No. Transaksi</div>
                        <div class="col-md-9 riwayat-pembayaran-no-akta">:</div>
                        <div class="col-md-3">Total Sudah Dibayar</div>
                        <div class="col-md-9 riwayat-pembayaran-total-dibayar">:</div>
                        <div class="col-md-3">Sisa Pembayaran</div>
                        <div class="col-md-9 riwayat-pembayaran-sisa-hutang">:</div>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hovered" id="table-riwayat-pembayaran">
                            <thead>
                                <tr>
                                    <th class="text-nowrap">Pembayaran ke</th>
                                    <th class="text-nowrap">Jumlah Dibayar</th>
                                    <th class="text-nowrap">Tanggal dan Waktu</th>
                                    <th class="text-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>

    </div>

    <div class="modal" id="modal-list-ppat" tabindex="" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Daftar Proses PPAT</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table w-100 table-proses-ppat table-bordered table-hovered">
                            <thead>
                                <tr>
                                    <td class="text-center text-nowrap">
                                        #
                                    </td>
                                    <td class="text-center text-nowrap">Daftar Proses</td>
                                    <td class="text-center text-nowrap">Status Proses</td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Kalkulator Pajak --}}
    <div class="modal fade" id="modal-kalkulator-pajak" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Kalkulator Pajak</h5>
                </div>

                <div class="modal-body">
                    <form id="kalkulatorForm">
                        <div class="mb-3">
                            <label for="jenis_pajak" class="form-label">Jenis Pajak</label>
                            <select name="jenis_pajak" target="jenis_pajak" id="jenis_pajak" class="form-control">
                                <option value="0" selected>-- Pilih Jenis Pajak --</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="njop" class="form-label">Nilai NJOP:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" target="besaran_pajak_pertama" class="form-control"
                                        id="njop" placeholder="Masukkan NJOP" required onkeyup="formatUang(this)">
                                    <input type="hidden" target="pajak_pihak_pertama">
                                </div>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="n" class="form-label">Nilai Pengurang (n):</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" target="besaran_pajak_kedua" class="form-control"
                                        id="n" placeholder="Masukkan nilai pengurang" required
                                        onkeyup="formatUang(this)">
                                    <input type="hidden" target="pajak_pihak_kedua">
                                </div>
                            </div>

                            <div class="mb-3 d-none col-md-7 besaran_tidak_kena_pajak">
                                <label for="njop" class="form-label">Besaran Objek Tidak Kena Pajak:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" target="besaran_tidak_kena_pajak" class="form-control"
                                        id="objek_tidak_kena_pajak" placeholder="Masukkan Besaran Objek Tidak Kena Pajak:"
                                        required onkeyup="formatUang(this)"
                                        value="{{ formatUang(App\Models\KonfigurasiUmum::query()->select('besaran_nilai_tidak_kena_pajak')->first()->besaran_nilai_tidak_kena_pajak ?? '') }}">

                                    <input type="hidden" target="objek_tidak_kena_pajak" value="">
                                </div>
                            </div>
                        </div>

                        <div id="check-skb"></div>

                        <div class="form-check ml-1  mb-3">
                            <input class="form-check-input" id="checked-skb" type="checkbox">
                            <label class="form-check-label" id="checked-skb-label" for="checked-skb">
                                Surat Keterangan Bebas (SKB)
                            </label>
                        </div>
                    </form>

                    <div>
                        <table id="table-result" class="d-none table table-bordered">
                            <tbody>
                                <tr>
                                    <td><strong id="label-pihak-pertama"></strong></td>
                                    <td class="d-none" id="additional-table"><strong id="label-pihak-kedua"></strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button id="simpan-pajak" class="btn btn-success">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCetakPemohon" tabindex="-1" aria-labelledby="modalCetakPemohonLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCetakPemohonLabel">Data Cetak Pemohon</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-end pr-3">
                        <button class="btn btn-primary" id="tambah-serah-terima">+ Tambah Data</button>
                    </div>
                    <table class="table table-bordered table-serah-terima">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor Transaksi</th>
                                <th>Uraian</th>
                                <th>Keperluan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan diisi oleh DataTable -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Start Modal Tambah Cetah Serah Terima --}}
    <div class="modal fade" id="modal-tambah-serah-terima" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Cetak Pemohon</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-tambah-cetak-pemohon" method="post">
                        {{-- <div class="form-group">
                            <label for="daftar-list">Daftar List</label>
                            <textarea name="daftar-list" id="daftar-list-tambah" class="form-control" rows="8"></textarea>
                        </div> --}}
                        <div class="form-group">
                            <label for="uraian">Uraian</label>
                            <textarea name="uraian" class="form-control" id="uraian-tambah" rows="8"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="keperluan">Keperluan</label>
                            <textarea name="keperluan" class="form-control" id="keperluan-tambah" rows="8"></textarea>
                        </div>
                        <div class="message-error text-danger"></div>
                        <button type="button" class="btn btn-primary btn-simpan-cetak-pemohon">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- start modal edit cetak pemohon -->
    <div class="modal fade" id="modal-edit-cetak-pemohon" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Cetak Pemohon</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-edit-cetak-pemohon" method="post"
                        action="{{ url('/administrator/transaksi/cetakPemohon') }}">
                        @csrf
                        <input type="hidden" name="id">
                        {{-- <div class="form-group">
                            <label for="daftar-list">Daftar List</label>
                            <textarea name="daftar_list" id="daftar-list-edit" class="form-control"></textarea>
                        </div> --}}
                        <div class="form-group">
                            <label for="uraian">Uraian</label>
                            <textarea name="uraian" class="form-control" id="uraian-edit"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="keperluan">Keperluan</label>
                            <textarea name="keperluan" class="form-control" id="keperluan-edit"></textarea>
                        </div>
                        <div class="message-error text-danger"></div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="modal-list-ppat" tabindex="" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Daftar Proses PPAT</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table w-100 table-proses-ppat table-bordered table-hovered">
                            <thead>
                                <tr>
                                    <td class="text-center text-nowrap">
                                        #
                                    </td>
                                    <td class="text-center text-nowrap">Daftar Proses</td>
                                    <td class="text-center text-nowrap">Status Proses</td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Status PPAT --}}
    <div class="modal fade" id="modal-status-ppat">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal Status PPAT</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control select2" name="setter_status_ppat">
                            @foreach ($transaksiStatus as $val)
                                <option value="{{ $val->id }}">{{ $val->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row container-status-ppat-selesai d-none">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Judul</label>
                                <input type="text" name="setter_judul_ppat" class="form-control"
                                    placeholder="Judul">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Nomor Akta</label>
                                <input type="text" name="setter_no_akta_ppat" class="form-control"
                                    placeholder="Nomor Akta">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Tangal Akta</label>
                                <input type="date" name="setter_tgl_akta_ppat" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Tutup
                    </button>
                    <button type="button" class="btn btn-primary" id="simpan-status-ppat">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit Riwayat Bayar --}}
    <div class="modal fade" id="modal-edit-riwayat-bayar">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="form-edit-riwayat-bayar"
                    action="{{ url('/administrator/transaksi/riwayat-pembayaran/edit') }}" method="post">
                    <input type="hidden" name="id">
                    <input type="hidden" class="dibayar_value">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Riwayat Bayar</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pembayaran Ke</label>
                                    <input type="number" name="pembayaran_ke" class="form-control" placeholder="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jumlah Dibayar</label>
                                    <input type="text" name="jumlah_dibayar" class="form-control currency-input"
                                        placeholder="50.000">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Tutup
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        const access = {
            create: "{{ $groups->create }}",
            update: "{{ $groups->update }}",
            delete: "{{ $groups->delete }}",
            role: "{{ $groupData->group_jenis }}",
        };
    </script>
    <script src="{{ asset('assets/scripts/administrator/transaksi/index.js') }}"></script>
@endsection
