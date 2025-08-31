<?php

namespace App\Http\Controllers\Admin\Role;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Admin::select(['id', 'name', 'email', 'is_active', 'created_at'])->where('id', '!=', 1);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('is_active', function ($row) {
                    return $row->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>';
                })
                ->addColumn('roles', function ($row) {
                    return $row->roles->pluck('name')->implode(', ');
                })
                ->addColumn('action', function ($row) {
                    $edit = route('admin.role.admin.edit', $row->id);
                    $del = route('admin.role.admin.destroy', $row->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');
                    $btns = '<a href="' . $edit . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a> ';
                    $btns .= '<form action="' . $del . '" method="POST" style="display:inline-block; margin-left:4px;">' . $csrf . $method . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Bạn có chắc muốn xóa?\')"><i class="fas fa-trash"></i></button></form>';
                    return $btns;
                })
                ->rawColumns(['is_active', 'action'])
                ->make(true);
        }

        return view('admin.role.admin.index');
    }

    public function create()
    {
        $roles = Role::where('is_active', true)->get();
        return view('admin.role.admin.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6|confirmed',
            'roles' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->filled('roles')) {
            $admin->roles()->sync($request->roles);
        }

        return redirect()->route('admin.role.admin.index')->with('success', 'Thêm tài khoản quản trị thành công!');
    }

    public function edit(Admin $admin)
    {
        $roles = Role::where('is_active', true)->get();
        return view('admin.role.admin.edit', compact('admin', 'roles'));
    }

    public function update(Request $request, Admin $admin)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'password' => 'nullable|string|min:6|confirmed',
            'roles' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $admin->update($data);

        if ($request->has('roles')) {
            $admin->roles()->sync($request->roles);
        } else {
            $admin->roles()->sync([]);
        }

        return redirect()->route('admin.role.admin.index')->with('success', 'Cập nhật tài khoản quản trị thành công!');
    }

    public function destroy(Admin $admin)
    {
        $admin->delete();
        return redirect()->route('admin.role.admin.index')->with('success', 'Xóa tài khoản quản trị thành công!');
    }
}
