@extends('layout.administrator')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/pages/users/index.css') }}">
<style></style>
@endsection

@section('content')
<div class="d-flex justify-content-between">
    <div class="breadcrumb">
        <h1 class="mr-2">Groups</h1>
        <ul>
            <li>System</li>
            <li>Groups</li>
        </ul>
    </div>
    <div class="d-inline-block">
        <a href="{{ url('administrator/groups/form') }}" class="btn btn-primary">
            <i class="fa fa-plus mr-2"></i> Tambah
        </a>
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
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{ asset('assets/scripts/administrator/groups/index.js') }}"></script>
@endsection
