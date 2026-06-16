@extends('layout.administrator')

@section('css')
{{-- Nothing in Here --}}
@endsection

@section('content')
<div class="d-flex justify-content-between">
    <div class="breadcrumb">
        <h1 class="mr-2">Materai</h1>
        <ul>
            <li>Page</li>
            <li>Materai</li>
        </ul>
    </div>
    <div class="d-inline-block">
    </div>
</div>

<div class="d-flex justify-content-end py-4">
    <button type="button" class="btn btn-primary" id="btn-add-materai">
        <i class="fa fa-plus mr-2"></i> Tambah
    </button>
</div>

<div class="separator-breadcrumb border-top"></div>
<div class="card mb-2">
    <div class="card-body d-flex w-75 gap-3">
        <div class="mb-3 mr-4">
            <div class="input-group">
                <input autocomplete="off" type="date" id="tanggal_awal" class="form-control bg-white">
                <span class="input-group-text">Sd</span>
                <input autocomplete="off" type="date" id="tanggal_akhir" class="form-control bg-white" value="{{ date('Y-m-d') }}">
            </div>
        </div>

        <div class="mb-3">
            <select name="filter_petugas_id" id="filter_petugas_id"  class="form-control select2">
            </select>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="table-materai" class="table w-100 table-bordered table-hovered table-bordered" id="table-materai">
                <thead>
                    <tr class="text-center">
                        <th rowspan="1">#</th>
                        <th rowspan="1">Tanggal</th>
                        <th rowspan="1">Keterangan</th>
                        <th rowspan="1">Materai Masuk</th>
                        <th rowspan="1">Materai Keluar</th>
                        <th rowspan="1">Stok Materai</th>
                        <th rowspan="1">Petugas</th>
                    </tr>
                </thead>

                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-form-materai">
    <div class="modal-dialog" role="document">
        <form id="form-materai" method="post" action="{{ route('materai.store') }}" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Materai</h5>
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
                                        <input type="hidden" name="id">
                                        <div class="py-2">
                                            <label>Tanggal</label>
                                            <input name="date" class="form-control" id="date" type="date"
                                                autocomplete="off" required>
                                            <small class="message-error text-danger" data-target="nama_error"></small>
                                        </div>

                                        <div class="py-2">
                                            <label>Jumlah Materai</label>
                                            <input name="materai_masuk" class="form-control" id="stok" type="number"
                                                autocomplete="off" required>
                                            <small class="message-error text-danger" data-target="nama_error"></small>
                                        </div>

                                        <div>
                                            <label for="petugas">Petugas</label>
                                            <input type="text" id="get-petugas" name="petugas_id" class="d-none form-control" readonly>
                                            <input type="text" id="username-display" class="form-control" readonly>
                                        </div>

                                        <div class="py-2">
                                            <label>Keterangan</label>
                                            <textarea class="form-control" name="keterangan" id="keterangan" cols="10"
                                                rows="4"></textarea>
                                            <small class="message-error text-danger" data-target="nama_error"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                            class="fas fa-times mr-2"></i> Tutup</button>
                    <button type="submit" id="simpan" class="btn btn-primary"><i class="fas fa-save mr-2"></i>
                        Simpan</button>
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
    }
</script>
<script src="{{ asset('assets/scripts/administrator/laporan/materai/index.js') }}"></script>
@endsection
