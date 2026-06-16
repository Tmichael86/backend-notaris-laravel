@extends('layout.administrator')

@section('css')
    <style>
        /* Tambahkan style khusus jika diperlukan */
    </style>
@endsection

@section('content')
    <div class="d-flex justify-content-between">
        <div class="breadcrumb">
            <h1 class="mr-2">Pekerjaan PPAT</h1>
            <ul>
                <li>Master</li>
                <li>Pekerjaan PPAT</li>
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
                <table class="table table-pekerjaan-ppat table-bordered table-hovered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th style="width: 20%">Harga</th>
                            <th style="width: 25%">Estimasi Waktu</th>
                            <th style="width: 12%">Created At</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data tabel akan diisi di sini -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-form">
        <div class="modal-dialog modal-lg" role="document">
            <form id="form-pekerjaan" method="post" action="{{ url('administrator/master/pekerjaan_ppat') }}"
                enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Buat Pekerjaan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body mx-3">
                        <div class="row">
                            <input type="hidden" name="id">
                            <div class="col-xl-12 col-md-12 col-12">
                                <div class="form-group">
                                    <label for="nama">Nama Pekerjaan</label>
                                    <input type="text" class="form-control" id="nama" name="nama"
                                        placeholder="Nama">
                                    <small class="message-error text-danger" data-target="nama_error"></small>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-harga table-bordered table-hovered">
                                    <thead>
                                        <tr>
                                            <td class="text-center">
                                                <button class="btn btn-icon btn-md btn-danger btn-add-row-harga"
                                                    type="button">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </td>
                                            <td class="text-center text-nowrap">Kategori</td>
                                            <td class="text-center text-nowrap" style="width:25%">Harga</td>
                                            <td class="text-center text-nowrap">Estimasi Waktu</td>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-proses table-bordered table-hovered">
                                    <thead>
                                        <tr>
                                            <td class="text-center">
                                                <button class="btn btn-icon btn-md btn-danger btn-add-row-proses"
                                                    type="button">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </td>
                                            <td class="text-center text-nowrap"> Nama Proses</td>
                                        </tr>
                                    </thead>
                                    <tbody id="proses-table-body">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-2"></i> Tutup
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i> Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    {{-- Kenapa Clone Harga Bisa Double Formnya ketika di inisiai --}}
    {{-- Ya nggak tau, _layhome12 --}}
    <div class="d-none">
        <div id="clone_aksi_harga">
            <div class="text-center">
                <input type="hidden" name="harga[pekerjaan_harga_id][]" value="">
                <button type="button" class="btn btn-icon btn-md btn-danger text-center" onclick="removeRowHarga(this)">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div id="clone_pekerjaan_harga">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">Rp</div>
                </div>
                <input type="text" class="form-control bg-white" target="pekerjaan_harga"
                    name="harga[pekerjaan_harga][]">
            </div>
        </div>
        <div id="clone_kategori_pekerjaan_id">
            <select name="harga[kategori_pekerjaan_id][]" class="form-control select2">
                <!-- Opsi kategori akan diisi di sini -->
            </select>
        </div>
        <div id="clone_estimasi_waktu">
            <input type="text" class="form-control" name="harga[estimasi_waktu][]">
        </div>

        <div id="clone_aksi_proses">
            <div class="text-center">
                <button type="button" class="btn btn-icon btn-md btn-danger text-center"
                    onclick="removeRowProses(this)">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div id="clone_pekerjaan_proses">
            <input type="hidden" name="proses_id[]" value="">
            <input type="text" class="form-control bg-white" target="pekerjaan_proses_nama" name="proses[]">
            <textarea class="form-control mt-2" name="proses_detail[]"></textarea>

            <div class="pt-2 ms-4">
                <p class="bg-white d-flex justify-content-center">Tambah List</p>
                <div class="d-flex pb-4 justify-content-center">
                    <input type="hidden" class="atribut_value" value="0">
                    <button id="btn-add-atribute" class="btn btn-success btn-add-atribute" type="button">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>

                <div id="proses-atribut-container-{index}" class="mt-2 proses-atribut-container"></div>

            </div>
        </div>

        <div id="clone_proses_pekerjaan_atribut" class="d-none">
            <div class="d-flex align-items-center mt-2">
                <button class="btn btn-danger btn-remove-atribute" type="button">
                    <i class="fas fa-minus"></i>
                </button>
                <input type="hidden" name="attribut[][proses_pekerjaan_atribut_id][]" class="atribut_list_id"
                    value="">
                <input type="text" name="atribut[][proses_pekerjaan_atribut][]"
                    class="atribut_list form-control ms-2">
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
    <script src="{{ asset('assets/scripts/administrator/master/pekerjaan_ppat/index.js') }}"></script>
@endsection
