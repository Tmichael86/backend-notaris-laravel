@extends('layout.administrator')

@section('css')
    {{-- Nothing in Here --}}
@endsection

@section('content')
    <div class="d-flex justify-content-between">
        <div class="breadcrumb">
            <h1 class="mr-2">Konfigurasi Umum</h1>
            <ul>
                <li>Page</li>
                <li>Konfigurasi Umum</li>
            </ul>
        </div>
        <div class="d-inline-block">
        </div>
    </div>

    <div class="d-flex justify-content-end py-4">
        <button type="button" class="btn btn-primary" id="btn-change-konfigurasi">
            + Ubah Nilai
        </button>
    </div>

    <div class="separator-breadcrumb border-top"></div>

    <div class="card shadow-sm">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th class="text-center">Label</th>
                            <th class="text-center">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data Diri Notaris -->
                        <tr>
                            <td class="text-start">Alamat</td>
                            <td>{{ $data->alamat }}</td>
                        </tr>
                        <tr>
                            <td class="text-start">Telepon Rumah</td>
                            <td>{{ $data->telp_rumah }}</td>
                        </tr>
                        <tr>
                            <td class="text-start">Telepon Pertama</td>
                            <td>{{ $data->telp_pertama }}</td>
                        </tr>
                        <tr>
                            <td class="text-start">Telepon Kedua</td>
                            <td>{{ $data->telp_kedua }}</td>
                        </tr>
                        <tr>
                            <td class="text-start">Email</td>
                            <td>{{ $data->email }}</td>
                        </tr>
                        <tr>
                            <td class="text-start">Cetak : Dari Notaris Bersangkutan</td>
                            <td>{{ $data->notaris_bersangkutan }}</td>
                        </tr>
                        <tr>
                            <td class="text-start">Cetak : Dari PPAT Bersangkutan</td>
                            <td>{{ $data->ppat_bersangkutan }}</td>
                        </tr>
                        <!-- Informasi Pajak -->
                        <tr>
                            <td class="text-start">Nilai Besaran Tidak Kena Pajak</td>
                            <td>Rp. {{ formatUang($data->besaran_nilai_tidak_kena_pajak) }}</td>
                        </tr>
                        <tr>
                            <td class="text-start">Nilai Pajak Pengecekan</td>
                            <td>Rp. {{ formatUang($data->pengecekan) }}</td>
                        </tr>
                        <tr>
                            <td class="text-start">Nilai Pajak SKMHT</td>
                            <td>Rp. {{ formatUang($data->surat_kuasa_membebankan_hak_tanggungan) }}</td>
                        </tr>
                        <tr>
                            <td class="text-start">Ploting Validasi</td>
                            <td>Rp. {{ formatUang($data->ploting_validasi) }}</td>
                        </tr>
                        <tr>
                            <td class="text-start">Harga Beli Materai</td>
                            <td>Rp. {{ formatUang($data->harga_beli_materai) }}</td>
                        </tr>
                        <tr>
                            <td class="text-start">Harga Jual Materai</td>
                            <td>Rp. {{ formatUang($data->harga_jual_materai) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-form-konfigurasi">
        <div class="modal-dialog modal-lg">
            <form id="form-konfigurasi" method="post" action="{{ route('konfigurasi.update') }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Konfigurasi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Data Diri Notaris -->
                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <textarea name="alamat" id="alamat" class="form-control" rows="3" required>{{ $data->alamat }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label for="telp_rumah">Telepon Rumah</label>
                                    <input type="text" name="telp_rumah" id="telp_rumah" class="form-control"
                                        value="{{ $data->telp_rumah }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="telp_pertama">Telepon Pertama</label>
                                    <input type="text" name="telp_pertama" id="telp_pertama" class="form-control"
                                        value="{{ $data->telp_pertama }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="telp_kedua">Telepon Kedua</label>
                                    <input type="text" name="telp_kedua" id="telp_kedua" class="form-control"
                                        value="{{ $data->telp_kedua }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email" class="form-control"
                                        value="{{ $data->email }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="notaris_bersangkutan">Cetak : Dari Notaris Bersangkutan</label>
                                    <input type="text" name="notaris_bersangkutan" id="notaris_bersangkutan"
                                        class="form-control" value="{{ $data->notaris_bersangkutan }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="ppat_bersangkutan">Cetak : Dari PPAT Bersangkutan</label>
                                    <input type="text" name="ppat_bersangkutan" id="ppat_bersangkutan"
                                        class="form-control" value="{{ $data->ppat_bersangkutan }}" required>
                                </div>

                                <!-- Informasi Pajak -->
                                <div class="form-group">
                                    <label for="besaran_nilai_tidak_kena_pajak">Nilai Besaran Tidak Kena Pajak</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp.</span>
                                        </div>
                                        <input type="number" name="besaran_nilai_tidak_kena_pajak"
                                            id="besaran_nilai_tidak_kena_pajak" class="form-control"
                                            value="{{ $data->besaran_nilai_tidak_kena_pajak }}" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="pengecekan">Nilai Pajak Pengecekan</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp.</span>
                                        </div>
                                        <input type="number" name="pengecekan" id="pengecekan" class="form-control"
                                            value="{{ $data->pengecekan }}" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="surat_kuasa_membebankan_hak_tanggungan">Nilai Pajak SKMHT</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp.</span>
                                        </div>
                                        <input type="number" name="surat_kuasa_membebankan_hak_tanggungan"
                                            id="surat_kuasa_membebankan_hak_tanggungan" class="form-control"
                                            value="{{ $data->surat_kuasa_membebankan_hak_tanggungan }}" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="ploting_validasi">Ploting Validasi</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp.</span>
                                        </div>
                                        <input type="number" name="ploting_validasi" id="ploting_validasi"
                                            class="form-control" value="{{ $data->ploting_validasi }}" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="harga_beli_materai">Harga Beli Materai</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp.</span>
                                        </div>
                                        <input type="number" name="harga_beli_materai" id="harga_beli_materai"
                                            class="form-control" value="{{ $data->harga_beli_materai }}" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="harga_jual_materai">Harga Jual Materai</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp.</span>
                                        </div>
                                        <input type="number" name="harga_jual_materai" id="harga_jual_materai"
                                            class="form-control" value="{{ $data->harga_jual_materai }}" required>
                                    </div>
                                </div>
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
@endsection

@section('javascript')
    <script>
        const access = {
            update: "{{ $groups->update }}",
            delete: "{{ $groups->delete }}"
        }
    </script>

    <script src="{{ asset('assets/scripts/administrator/konfigurasi-umum/index.js') }}"></script>
@endsection
