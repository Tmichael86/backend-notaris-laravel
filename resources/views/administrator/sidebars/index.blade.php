@extends('layout.administrator')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/users/index.css') }}">
    <style></style>
@endsection

@section('content')
    <div class="d-flex justify-content-between">
        <div class="breadcrumb">
            <h1 class="mr-2">Sidebar</h1>
            <ul>
                <li>System</li>
                <li>Sidebar</li>
            </ul>
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
                            <th>Parent</th>
                            <th>Index</th>
                            <th>Kode</th>
                            <th>Index</th>
                            <th>Icon</th>
                            <th>Created At</th>
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
    <script src="{{ asset('assets/scripts/administrator/sidebars/index.js') }}"></script>
@endsection
