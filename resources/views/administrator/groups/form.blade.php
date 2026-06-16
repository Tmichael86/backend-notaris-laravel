@extends('layout.administrator')

@section('css')
    <style>
        .text-default {
            color: black;
        }

        .text-default:hover {
            color: black !important;
        }
    </style>
@endsection

@section('content')
    <div class="d-flex justify-content-between">
        <div class="breadcrumb">
            <h1>Administrator</h1>
            <ul>
                <li><a href="">Grup</a></li>
                <li>Form</li>
            </ul>
        </div>
    </div>
    <div class="">
        <div class="card">
            <div class="card-body">
                <form id="form-data" method="post"
                    action="{{ @$group[0]['id'] ? url('administrator/groups/form/' . $group[0]['id']) : url('administrator/groups/form') }}">
                    @if (@$group[0]['id'])
                        <input value="{{ $group[0]['id'] }}" name="group_id" class="d-none" type="hidden">
                    @endif
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label>Nama Grup</label>
                                <input name="group_nama" value="{{ @$group[0]['group_nama'] }}" class="form-control"
                                    type="text" autocomplete="off">
                                <small class="text-danger" data-target="group_nama_error"></small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Jenis Grup</label>
                                <select class="form-control select2" name="group_jenis">
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="superadmin"
                                        {{ @$group[0]['group_jenis'] == 'superadmin' ? 'selected' : '' }}>
                                        Super Admin
                                    </option>
                                    <option value="user" {{ @$group[0]['group_jenis'] == 'user' ? 'selected' : '' }}>
                                        User
                                    </option>
                                </select>
                                <small class="text-danger" data-target="group_jenis_error"></small>
                            </div>
                        </div>
                    </div>
                    <hr class="my-3">
                    <h3 class="mt-2 mb-3">Akses Halaman</h3>
                    <div id="akses">
                        <div class="accordion" id="accordionRightIcon">
                            @foreach ($sidebars as $sidebarsKey => $sidebar)
                                <div class="card">
                                    <div class="card-header header-elements-inline py-3">
                                        <div class="d-flex justify-content-start">
                                            <label class="checkbox checkbox-secondary">
                                                <input
                                                    {{ isset($sidebar['read']) && $sidebar['read'] ? 'checked="true"' : '' }}
                                                    type="checkbox" onchange="check_all_childs(this)"><span
                                                    class="checkmark"></span>
                                            </label>
                                            <h6 class="card-title ul-collapse__icon--size ul-collapse__right-icon mb-0">
                                                <a class="text-default collapsed" data-toggle="collapse"
                                                    href="#accordion-item-{{ $sidebar['id'] }}" aria-expanded="true">
                                                    <span><i
                                                            class="{{ $sidebar['sidebar_icon'] }} ul-accordion__font ml-2 mr-1"></i></span>
                                                    <span class="text-nowrap"><?php echo $sidebar['sidebar_nama']; ?></span>
                                                </a>
                                            </h6>
                                        </div>
                                    </div>
                                    <div class="collapse" id="accordion-item-{{ $sidebar['id'] }}"
                                        data-parent="#accordionRightIcon">
                                        <div class="card-body">
                                            <div class="row mt-3">
                                                <div class="col-sm-4 col-md-4 col-lg-4">
                                                    <label>{{ $sidebar['sidebar_nama'] }}</label>
                                                    <input type="hidden" autocomplete="off" class="form-control"
                                                        value="{{ $sidebar['id'] }}" name="sidebar_id[]">
                                                </div>
                                                <div class="col-sm-2 col-md-2 col-lg-2">
                                                    <label class="checkbox checkbox-secondary">
                                                        <input
                                                            {{ isset($sidebar['read']) && $sidebar['read'] ? 'checked="true"' : '' }}
                                                            name="read[{{ $sidebar['id'] }}]" value="1"
                                                            type="checkbox" /><span>Lihat</span><span
                                                            class="checkmark"></span>
                                                    </label>
                                                </div>
                                                <div class="col-sm-2 col-md-2 col-lg-2">
                                                    <label class="checkbox checkbox-secondary">
                                                        <input
                                                            {{ isset($sidebar['create']) && $sidebar['create'] ? 'checked="true"' : '' }}
                                                            name="create[{{ $sidebar['id'] }}]" value="1"
                                                            onchange="check(this)" type="checkbox" /><span>Buat</span><span
                                                            class="checkmark"></span>
                                                    </label>
                                                </div>
                                                <div class="col-sm-2 col-md-2 col-lg-2">
                                                    <label class="checkbox checkbox-secondary">
                                                        <input
                                                            {{ isset($sidebar['update']) && $sidebar['update'] ? 'checked="true"' : '' }}
                                                            name="update[{{ $sidebar['id'] }}]" value="1"
                                                            onchange="check(this)"
                                                            type="checkbox" /><span>Perbarui</span><span
                                                            class="checkmark"></span>
                                                    </label>
                                                </div>
                                                <div class="col-sm-2 col-md-2 col-lg-2">
                                                    <label class="checkbox checkbox-secondary">
                                                        <input
                                                            {{ isset($sidebar['delete']) && $sidebar['delete'] ? 'checked="true"' : '' }}
                                                            name="delete[{{ $sidebar['id'] }}]" value="1"
                                                            onchange="check(this)" type="checkbox" /><span>Hapus</span><span
                                                            class="checkmark"></span>
                                                    </label>
                                                </div>
                                            </div>

                                            @if (isset($sidebar['childs']))
                                                @foreach ($sidebar['childs'] as $childKey => $child)
                                                    <div class="row mt-3">
                                                        <div class="col-sm-4 col-md-4 col-lg-4">
                                                            <label>{{ $child['sidebar_nama'] }}</label>
                                                            <input type="hidden" autocomplete="off" class="form-control"
                                                                value="{{ $child['id'] }}" name="sidebar_id[]">
                                                        </div>
                                                        <div class="col-sm-2 col-md-2 col-lg-2">
                                                            <label class="checkbox checkbox-secondary">
                                                                <input
                                                                    {{ isset($child['read']) && $child['read'] ? 'checked="true"' : '' }}
                                                                    name="read[{{ $child['id'] }}]" value="1"
                                                                    type="checkbox" /><span>Lihat</span><span
                                                                    class="checkmark"></span>
                                                            </label>
                                                        </div>
                                                        <div class="col-sm-2 col-md-2 col-lg-2">
                                                            <label class="checkbox checkbox-secondary">
                                                                <input
                                                                    {{ isset($child['create']) && $child['create'] ? 'checked="true"' : '' }}
                                                                    name="create[{{ $child['id'] }}]" value="1"
                                                                    onchange="check(this)"
                                                                    type="checkbox" /><span>Buat</span><span
                                                                    class="checkmark"></span>
                                                            </label>
                                                        </div>
                                                        <div class="col-sm-2 col-md-2 col-lg-2">
                                                            <label class="checkbox checkbox-secondary">
                                                                <input
                                                                    {{ isset($child['update']) && $child['update'] ? 'checked="true"' : '' }}
                                                                    name="update[{{ $child['id'] }}]" value="1"
                                                                    onchange="check(this)"
                                                                    type="checkbox" /><span>Perbarui</span><span
                                                                    class="checkmark"></span>
                                                            </label>
                                                        </div>
                                                        <div class="col-sm-2 col-md-2 col-lg-2">
                                                            <label class="checkbox checkbox-secondary">
                                                                <input
                                                                    {{ isset($child['delete']) && $child['delete'] ? 'checked="true"' : '' }}
                                                                    name="delete[{{ $child['id'] }}]" value="1"
                                                                    onchange="check(this)"
                                                                    type="checkbox" /><span>Hapus</span><span
                                                                    class="checkmark"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="{{ asset('assets/scripts/administrator/groups/form.js') }}"></script>
@endsection
