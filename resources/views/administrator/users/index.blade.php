@extends('layout.administrator')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/users/index.css') }}">
    <style></style>
@endsection

@section('content')
    <div class="d-flex justify-content-between">
        <div class="breadcrumb">
            <h1 class="mr-2">Pengguna</h1>
            <ul>
                <li>System</li>
                <li>Pengguna</li>
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
                            <th>Group</th>
                            <th>Username</th>
                            <th>Email</th>
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
        <div class="modal-dialog modal-lg" role="document">
            <form id="form-pengguna" method="post" action="{{ url('administrator/users') }}" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pengguna</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body mx-3">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="hidden" name="id">
                                <input class="d-none" type="file" name="user_img" id="user-img-file">
                                <div class="card on-hover foto-profile-card">
                                    <div class="card-body text-center">
                                        <img class="foto-profile" src="{{ asset('uploads/profile/no-image.png') }}"
                                            alt="" srcset="">
                                    </div>
                                </div>
                                <div class="info-upload-images text-center">
                                    <small>Format : JPG, JPEG, PNG | 1MB</small>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-8 col-12">
                                        <div class="form-group">
                                            <label>Nama</label>
                                            <input name="nama" class="form-control" type="text" autocomplete="off"
                                                required>
                                            <small class="message-error text-danger" data-target="nama_error"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <div class="form-group">
                                            <label>Group</label>
                                            <select name="group_id" class="form-control select2" required>
                                                <option value="" disabled>-- Group --</option>
                                                @foreach ($groupData as $key => $group)
                                                    <option value="{{ $group['id'] }}">{{ $group['group_nama'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="message-error text-danger" data-target="group_id_error"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <div class="form-group">
                                            <label>Telp</label>
                                            <input name="no_telp" class="form-control" type="text" autocomplete="off">
                                            <small class="message-error text-danger" data-target="no_telp_error"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input name="email" class="form-control" type="text" autocomplete="off"
                                                required>
                                            <small class="message-error text-danger" data-target="email_error"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <div class="form-group">
                                            <label>Username</label>
                                            <input name="username" class="form-control" type="text" autocomplete="off"
                                                required>
                                            <small class="message-error text-danger" data-target="username_error"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-6">
                                        <div class="form-group">
                                            <label>Password</label>
                                            <input name="password" value="" class="form-control" type="password"
                                                autocomplete="off">
                                            <small class="message-error text-danger" data-target="password_error"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-6">
                                        <div class="form-group">
                                            <label>Password Konfirmasi</label>
                                            <input name="password_confirm" value="" class="form-control"
                                                type="password" autocomplete="off">
                                            <small class="message-error text-danger"
                                                data-target="password_confirm_error"></small>
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
    <script src="{{ asset('assets/scripts/administrator/users/index.js') }}"></script>
@endsection
