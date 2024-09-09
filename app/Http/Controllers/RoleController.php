<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view roles', only: ['index']),
            new Middleware('permission:edit roles', only: ['edit']),
            new Middleware('permission:create roles', only: ['create']),
            new Middleware('permission:delete roles', only: ['delete']),

        ];
    }
    //########## This method will show role page ##########//
    public function index()
    {
        $roles = Role::orderBy('created_at', 'ASC')->paginate(10);
        return view('roles.list', compact('roles'));
    }

    //########## This method will show create role page ##########//
    public function create()
    {
        $permissions = Permission::orderBy('name', 'ASC')->get();
        return view('roles.create', compact('permissions'));
    }

    //########## This method will insert a role in DB ##########//
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles|min:3'
        ]);

        if ($validator->passes()) {
            $role =  Role::create(['name' => $request->name]);
            if (!empty($request->permission)) {
                foreach ($request->permission as $name) {
                    $role->givePermissionTo($name);
                }
            }
            return redirect(route('roles.index'))->with('success', 'Role Added Successfully');
        } else {
            return redirect()->route('roles.create')->withInput()->withErrors($validator);
        }
    }

    //########## This method will show edit role page ##########//
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::orderBy('name', 'ASC')->get();
        $hasPermissions = $role->permissions->pluck('name');
        return view('roles.edit', compact('role', 'permissions', 'hasPermissions'));
    }

    //########## This method will update role ##########//
    public function update($id, Request $request)
    {
        $role = Role::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name,' . $id . ',id|min:3'
        ]);

        if ($validator->passes()) {
            $role->name = $request->name;
            $role->save();

            if (!empty($request->permission)) {
                $role->syncPermissions($request->permission);
            } else {
                $role->syncPermissions([]);
            }
            return redirect(route('roles.index'))->with('success', 'Role Updated Successfully');
        } else {
            return redirect()->route('roles.edit', $id)->withInput()->withErrors($validator);
        }
    }

    //########## This method will delete role page ##########//
    public function delete($id)
    {
        $role = Role::findOrFail($id);
        if (!is_null($role)) {
            $role->delete();
            return redirect(route('roles.index'))->with('success', 'Role Deleted Successfully');
        } else {
            return redirect(route('roles.index'))->with('error', 'Role not deleted due to some unconvinense plz check');
        }
    }
}
