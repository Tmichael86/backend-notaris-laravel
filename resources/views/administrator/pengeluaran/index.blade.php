@extends('layout.administrator')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/users/index.css') }}">
    <style></style>
@endsection

@section('content')
    <div class="d-flex justify-content-between">
        <div class="breadcrumb">
            <h1 class="mr-2">Pengeluaran</h1>
            <ul>
                <li>Page</li>
                <li>Pengeluaran</li>
            </ul>
        </div>
        <div class="d-inline-block">
            <button id="export-to-excel" class="btn btn-success">
                <i class="fa fa-file-excel mr-2"></i> Excel
            </button>
            @if ($groups->create)
                <button type="button" class="btn btn-primary" id="btn-form-add">
                    <i class="fa fa-plus mr-2"></i> Tambah
                </button>
            @endif
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
                    <select name="filter_pengeluaran_id" id="filter_pengeluaran_id" class="form-control select2"
                        target="filter_pengeluaran_id">
                        <option value="0">--Pilih Jenis Pengeluaran--</option>
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
                        <tr>
                            <th>#</th>
                            <th>No Faktur</th>
                            <th>Keterangan</th>
                            <th>Jenis Pengeluaran</th>
                            <th>Pengeluaran</th>
                            <th>Tanggal Pengeluaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot id="footer_data">
                        <tr>
                            <th colspan="4">
                                Total Pengeluaran
                            </th>
                            <th colspan="3" id="total_pengeluaran">-</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="modal-form">
        <div class="modal-dialog" role="document">
            <form id="form-pengeluaran" method="post" action="{{ route('pengeluaran-store') }}"
                enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pengeluaran</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body mx-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>No Faktur</label>
                                            <input type="hidden" name="id">
                                            <input name="no_faktur" id="no_faktur" class="form-control" type="text"
                                                autocomplete="off" readonly>
                                            <small class="message-error text-danger" data-target="no_faktur_error"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label> Jenis Pengeluaran</label>
                                            <select name="jenis_pengeluaran_id" target="jenis_pengeluaran_id"
                                                class="form-control select2"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-6">
                                        <label>Pengeluaran</label>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <div class="input-group-text rounded-left">Rp
                                                    </div>
                                                </div>
                                                <input type="text" id="jumlah" class="form-control" name="jumlah">
                                                <small class="message-error text-danger" data-target="jumlah_error"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-6">
                                        <label>Tanggal Pengeluaran</label>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" id="tanggal_pengeluaran"
                                                    class="form-control datetimepicker" name="tanggal_pengeluaran">
                                                <div class="input-group-append">
                                                    <div class="input-group-text rounded-right"><i
                                                            class="fas fa-calendar"></i>
                                                    </div>
                                                </div>
                                                <small class="message-error text-danger"
                                                    data-target="tanggal_pengeluaran_error"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group mt-2">
                                            <label>Keterangan</label>
                                            <textarea name="keterangan" class="form-control" rows="2"></textarea>
                                            <small class="message-error text-danger"
                                                data-target="keterangan_error"></small>
                                        </div>
                                    </div>
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
@endsection

@section('javascript')
    <script>
        const access = {
            update: "{{ $groups->update }}",
            delete: "{{ $groups->delete }}"
        };
    </script>
    <script src="{{ asset('assets/plugins/xlsx/xlsx.full.min.js') }}"></script>
    <script src="{{ asset('assets/scripts/administrator/pengeluaran/index.js') }}"></script>
@endsection
