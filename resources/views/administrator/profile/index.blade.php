@extends('layout.administrator')

@section('css')
    <style>
        .foto-profile {
            width: 100%;
            height: 152px;
            object-fit: contain;
        }

        .foto-profile-card {
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <div class="d-flex justify-content-between">
        <div class="breadcrumb">
            <h1 class="mr-2">Profile</h1>
            <ul>
                <li>User</li>
                <li>Profile</li>
            </ul>
        </div>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <form id="form-pengguna" method="post" action="{{ url('/administrator/profile') }}" enctype="multipart/form-data">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <div class="row border-bottom pb-2 mb-2">
                            <div class="col-md-3">
                                <div class="card foto-profile-card">
                                    <div class="card-body">
                                        <img class="foto-profile"
                                            src="{{ asset('uploads/profile') }}/{{ System::getProfile('image', session()->get('uid')) ?: 'no-image.png' }}"
                                            alt="" srcset="">
                                    </div>
                                </div>
                                <input class="d-none" type="file" name="user_img" id="user-img-file"
                                    accept=".jpg,.jpeg,.png">
                                <small class="text-center">Format : JPG, JPEG, PNG | Max : 512KB</small>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Nama</label>
                                            <input name="nama" class="form-control" type="text" autocomplete="off"
                                                value="{{ $user->nama }}" required>
                                            <small class="message-error text-danger" data-target="user_nama_error"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Telp</label>
                                            <input name="no_telp" value="{{ $user->no_telp }}" class="form-control"
                                                type="text" autocomplete="off">
                                            <small class="message-error text-danger" data-target="user_telp_error"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input name="email" value="{{ $user->email }}" class="form-control"
                                                type="text" autocomplete="off" required>
                                            <small class="message-error text-danger" data-target="user_email_error"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Username</label>
                                            <input name="username" value="{{ $user->username }}" class="form-control"
                                                type="text" autocomplete="off" required>
                                            <small class="message-error text-danger" data-target="username_error"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Password</label>
                                            <input name="password" class="form-control" type="password" autocomplete="off">
                                            <small class="message-error text-danger" data-target="password_error"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Password Konfirmasi</label>
                                            <input name="password_confirm" class="form-control" type="password"
                                                autocomplete="off">
                                            <small class="message-error text-danger"
                                                data-target="password_confirm_error"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group pt-2">
                                    <label>Alamat</label>
                                    <textarea name="alamat" class="form-control" rows="5">{{ $user->alamat }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="d-inline-block">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-2"></i> Simpan</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('javascript')
    <script src="{{ asset('assets/scripts/administrator/profile/index.js') }}"></script>
@endsection
