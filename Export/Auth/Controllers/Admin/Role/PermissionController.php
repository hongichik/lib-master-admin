<?php

namespace App\Http\Controllers\Admin\Role;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PermissionController extends Controller
{
    /**
     * Display a listing of the permissions.
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $query = Permission::select(['id', 'name', 'slug', 'description', 'icon']);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('icon', function ($row) {
                    return $row->icon ? '<i class="' . e($row->icon) . '"></i>' : '';
                })
                ->addColumn('action', function ($row) {
                    $edit = route('admin.role.permission.edit', $row->id);
                    $del = route('admin.role.permission.destroy', $row->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');
                    $btns = '<a href="' . $edit . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a> ';
                    $btns .= '<form action="' . $del . '" method="POST" style="display:inline-block; margin-left:4px;">' . $csrf . $method . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Bạn có chắc muốn xóa?\')"><i class="fas fa-trash"></i></button></form>';
                    return $btns;
                })
                ->rawColumns(['icon', 'action'])
                ->make(true);
        }

        return view('admin.role.permission.index');
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        return view('admin.role.permission.create');
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
        ]);
        Permission::create($request->only(['name', 'slug', 'description', 'icon']));
        return redirect()->route('admin.role.permission.index')->with('success', 'Thêm quyền thành công!');
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission)
    {
        return view('admin.role.permission.edit', compact('permission'));
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions,slug,' . $permission->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
        ]);
        $permission->update($request->only(['name', 'slug', 'description', 'icon']));
        return redirect()->route('admin.role.permission.index')->with('success', 'Cập nhật quyền thành công!');
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('admin.role.permission.index')->with('success', 'Xóa quyền thành công!');
    }
}
