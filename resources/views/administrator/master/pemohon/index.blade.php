@extends('layout.administrator')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/users/index.css') }}">
    <style></style>
@endsection

@section('content')
    <div class="d-flex justify-content-between">
        <div class="breadcrumb">
            <h1 class="mr-2">Pemohon</h1>
            <ul>
                <li>Master</li>
                <li>Pemohon</li>
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
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hovered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>No Telp</th>
                            <th>Created At</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="modal-form">
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
                                            <select name="jenis_kelamin" target="jenis_kelamin" class="form-control select2"
                                                required>
                                                <option value="" disabled>--pilih--</option>
                                            </select>
                                            <small class="message-error text-danger"
                                                data-target="jenis_kelamin_error"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Telp</label>
                                            <input name="no_telp" class="form-control" type="text" autocomplete="off">
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
@endsection

@section('javascript')
    <script>
        const access = {
            update: "{{ $groups->update }}",
            delete: "{{ $groups->delete }}"
        };
    </script>
    <script src="{{ asset('assets/plugins/xlsx/xlsx.full.min.js') }}"></script>
    <script src="{{ asset('assets/scripts/administrator/master/pemohon/index.js') }}"></script>
@endsection
