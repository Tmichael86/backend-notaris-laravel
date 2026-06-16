<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Http\Libraries\System;
use App\Models\Sidebar;
use Yajra\DataTables\Facades\DataTables;

class SidebarController extends Controller
{
    public function index()
    {
        $this->unlockData();
        return view('administrator.sidebars.index');
    }

    public function fetch()
    {
        $DB = Sidebar::get();

        return DataTables::of($DB)
            ->editColumn('id', function (Sidebar $sidebar) {
                return System::strEncode($sidebar->id);
            })
            ->editColumn('sidebar_parent_id', function (Sidebar $sidebar) {
                if ($sidebar->sidebar_parent_id == 0) {
                    return 'Parent';
                }

                $parent = Sidebar::where('id', $sidebar->sidebar_parent_id)
                    ->first();

                return @$parent->sidebar_nama ?: '';
            })
            ->toJson();
    }
}
