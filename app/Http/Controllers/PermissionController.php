<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PermissionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view permissions', only: ['index']),
            new Middleware('permission:edit permissions', only: ['edit']),
            new Middleware('permission:create permissions', only: ['create']),
            new Middleware('permission:delete permissions', only: ['delete']),

        ];
    }
    // This method will show permission page
    public function index()
    {
        $permissions = Permission::orderBy('created_at', 'ASC')->paginate(10);
        return view('permissions.list', compact('permissions'));
    }

    // This method will show create permission page
    public function create()
    {
        return view('permissions.create');
    }

    // This method will insert a permission in DB
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions|min:3'
        ]);

        if ($validator->passes()) {
            Permission::create(['name' => $request->name]);
            return redirect(route('permissions.index'))->with('success', 'Permissions Added Successfully');
        } else {
            return redirect()->route('permissions.create')->withInput()->withErrors($validator);
        }
    }

    // This method will show edit permission page
    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        return view('permissions.edit', compact('permission'));
    }

    // This method will update a permission
    public function update($id, Request $request)
    {
        $permission = Permission::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name,' . $id . ',id|min:3'
        ]);

        if ($validator->passes()) {
            $permission->name = $request->name;
            $permission->save();
            return redirect(route('permissions.index'))->with('success', 'Permissions Updated Successfully');
        } else {
            return redirect()->route('permissions.edit', $id)->withInput()->withErrors($validator);
        }
    }

    // This method will delete a permission
    public function delete($id)
    {
        $permission = Permission::findOrFail($id);
        if (!is_null($permission)) {
            $permission->delete();
            return redirect(route('permissions.index'))->with('success', 'Permissions Deleted Successfully');
        } else {
            return redirect(route('permissions.index'))->with('error', 'Permissions not deleted due to some unconvinense plz check');
        }
    }
}
