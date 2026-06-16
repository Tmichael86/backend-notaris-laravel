@extends('layout.administrator')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/users/index.css') }}">
    <style></style>
@endsection

@section('content')
    <div class="d-flex justify-content-between">
        <div class="breadcrumb">
            <h1 class="mr-2"> Kategori Pekerjaan</h1>
            <ul>
                <li>Master</li>
                <li>Kategori Pekerjaan</li>
            </ul>
        </div>
        <div class="d-inline-block">
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
                            <th>Nama</th>
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
            <form id="form-jenis-harga" method="post" action="{{ url('administrator/master/kategori_pekerjaan') }}"
                enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Jenis Harga</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body mx-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12 col-12">
                                        <div class="form-group">
                                            <label>Nama</label>
                                            <input type="hidden" name="id">
                                            <input name="nama" class="form-control" type="text" autocomplete="off"
                                                required>
                                            <small class="message-error text-danger" data-target="nama_error"></small>
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
    <script src="{{ asset('assets/scripts/administrator/master/kategori_pekerjaan/index.js') }}"></script>
@endsection
