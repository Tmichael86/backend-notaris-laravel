<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Http\Libraries\System;
use App\Models\Group;
use App\Models\Sidebar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class GroupController extends Controller
{
    public function index()
    {
        $this->unlockData();
        return view('administrator.groups.index');
    }

    public function fetch()
    {
        $DB = Group::where('status', 1)->get();

        return DataTables::of($DB)
            ->editColumn('id', function (Group $group) {
                return System::strEncode($group->id);
            })
            ->toJson();
    }

    public function store(Request $request, Group $groups)
    {
        $validated = Validator::make($request->all(), [
            'group_nama' => 'required',
            'group_jenis' => 'required|in:superadmin,user',
        ]);

        if ($validated->fails()) {
            return $this->badRequest($validated);
        }

        $data = System::crudIdentity('create', [
            'group_nama' => $request->input('group_nama'),
            'group_jenis' => $request->input('group_jenis'),
        ]);

        // ==> Save Data and get the id
        $id = $groups->create($data)->id;

        $sidebar_id = $request->input('sidebar_id');
        $create = $request->input('create');
        $read = $request->input('read');
        $update = $request->input('update');
        $delete = $request->input('delete');

        $akses = [];
        foreach ($sidebar_id as $key => $var) {
            $akses[$key]['sidebar_id'] = $var;
            $akses[$key]['group_id'] = $id;
            $akses[$key]['read'] = isset($read[$var]) ? '1' : '0';
            $akses[$key]['create'] = isset($create[$var]) ? '1' : '0';
            $akses[$key]['update'] = isset($update[$var]) ? '1' : '0';
            $akses[$key]['delete'] = isset($delete[$var]) ? '1' : '0';
            $akses[$key]['created_at'] = date('Y-m-d H:i:s');
            $akses[$key]['created_by'] = Session::get('uid');
        }

        // Saving sidebar akses
        $saveAkses = $groups->simpanAkses($id, $akses);
        if (! $saveAkses) {
            return $this->responseServer(500, [
                'statusCode' => 500,
                'message' => 'Terjadi kesalahan saat menyimpan data',
            ]);
        }

        return $this->responseServer(200, [
            'statusCode' => 200,
            'message' => 'Data telah berhasil disimpan',
        ]);
    }

    public function update(Request $request, Group $groups)
    {
        $validated = Validator::make($request->all(), [
            'group_nama' => 'required',
            'group_jenis' => 'required|in:superadmin,user',
        ]);

        if ($validated->fails()) {
            return $this->badRequest($validated);
        }

        $groupId = System::strDecode($request->input('group_id'));

        $data = System::crudIdentity('update', [
            'group_nama' => $request->input('group_nama'),
            'group_jenis' => $request->input('group_jenis'),
        ]);

        // ==> Update Data
        $groups->where('id', $groupId)->update($data);

        $sidebar_id = $request->input('sidebar_id');
        $create = $request->input('create');
        $read = $request->input('read');
        $update = $request->input('update');
        $delete = $request->input('delete');

        $akses = [];
        foreach ($sidebar_id as $key => $var) {
            $akses[$key]['sidebar_id'] = $var;
            $akses[$key]['group_id'] = $groupId;
            $akses[$key]['read'] = isset($read[$var]) ? '1' : '0';
            $akses[$key]['create'] = isset($create[$var]) ? '1' : '0';
            $akses[$key]['update'] = isset($update[$var]) ? '1' : '0';
            $akses[$key]['delete'] = isset($delete[$var]) ? '1' : '0';
            $akses[$key]['created_at'] = date('Y-m-d H:i:s');
            $akses[$key]['updated_at'] = date('Y-m-d H:i:s');
            $akses[$key]['created_by'] = Session::get('uid');
            $akses[$key]['updated_by'] = Session::get('uid');
        }

        // Saving sidebar akses
        $saveAkses = $groups->simpanAkses($groupId, $akses);
        if (! $saveAkses) {
            return $this->responseServer(500, [
                'statusCode' => 500,
                'message' => 'Terjadi kesalahan saat mengupdate data',
            ]);
        }

        return $this->responseServer(200, [
            'statusCode' => 200,
            'message' => 'Data telah berhasil diupdate',
        ]);
    }

    public function destroy($id)
    {
        $id = $this->decodeId($id);
        if ($id === false) {
            return $this->responseServer(404, [
                'statusCode' => 404,
                'message' => 'Data tidak ditemukan',
            ]);
        }

        $i = Group::where('id', $id)->update(System::crudIdentity('delete'));
        if (! $i) {
            return $this->responseServer(500, [
                'statusCode' => 500,
                'message' => 'Terjadi kesalahan saat hapus data',
            ]);
        }

        return $this->responseServer(200, [
            'statusCode' => 200,
            'message' => 'Data telah berhasil dihapus',
        ]);
    }

    public function form($id = null)
    {
        if (is_null($id)) {
            $sidebars = Sidebar::where('status', 1)
                ->orderBy('sidebar_parent_id', 'asc')
                ->orderBy('sidebar_index', 'asc')
                ->get()->toArray();
        } else {
            $decodedId = System::strDecode($id);
            if (! $decodedId) {
                return $this->responseServer(404, [
                    'statusCode' => 404,
                    'message' => 'Group tidak ditemukan !',
                ]);
            }

            $sidebars = Sidebar::leftJoin('sidebar_akses', function ($join) use ($decodedId) {
                $join->on('sidebar_akses.sidebar_id', '=', 'sidebar.id')->where('sidebar_akses.group_id', '=', $decodedId);
            })
                ->select('sidebar.*', 'create', 'read', 'update', 'delete')
                ->where('status', 1)
                ->orderBy('sidebar_parent_id', 'asc')
                ->orderBy('sidebar_index', 'asc')
                ->get()->toArray();

            $group = Group::where('id', $decodedId)->get()->toArray();
            $group[0]['id'] = System::strEncode($group[0]['id']);
            $data['group'] = $group;
        }

        $sortedSidebars = [];
        foreach ($sidebars as $key => $value) {
            if ($value['sidebar_parent_id'] == 0) {
                $sortedSidebars[$value['id']] = $value;
            } else {
                $sortedSidebars[$value['sidebar_parent_id']]['childs'][] = $value;
            }
        }

        $data['sidebars'] = $sortedSidebars;

        return view('administrator.groups.form', $data);
    }
}
