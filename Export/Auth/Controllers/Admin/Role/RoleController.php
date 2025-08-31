<?php

namespace App\Http\Controllers\Admin\Role;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Role::select(['id', 'name', 'slug', 'description', 'is_active']);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('is_active', function ($row) {
                    return $row->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    $edit = route('admin.role.role.edit', $row->id);
                    $del = route('admin.role.role.destroy', $row->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');
                    $btns = '<a href="' . $edit . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a> ';
                    $btns .= '<form action="' . $del . '" method="POST" style="display:inline-block; margin-left:4px;">' . $csrf . $method . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Bạn có chắc muốn xóa?\')"><i class="fas fa-trash"></i></button></form>';
                    return $btns;
                })
                ->rawColumns(['is_active', 'action'])
                ->make(true);
        }

        return view('admin.role.role.index');
    }

    public function create()
    {
    $permissions = Permission::all();
    return view('admin.role.role.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $role = Role::create($request->only(['name', 'slug', 'description', 'is_active']));

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('admin.role.role.index')->with('success', 'Thêm vai trò thành công!');
    }

    public function edit(Role $role)
    {
    $permissions = Permission::all();
    return view('admin.role.role.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $role->update($request->only(['name', 'slug', 'description', 'is_active']));

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        } else {
            $role->permissions()->sync([]);
        }

        return redirect()->route('admin.role.role.index')->with('success', 'Cập nhật vai trò thành công!');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.role.role.index')->with('success', 'Xóa vai trò thành công!');
    }
}
