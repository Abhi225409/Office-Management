<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view users', only: ['index']),
            new Middleware('permission:edit users', only: ['edit']),
            new Middleware('permission:create users', only: ['create']),
            new Middleware('permission:delete users', only: ['delete']),

        ];
    }

    //########## This method will show Users page ##########//
    public function index()
    {
        $users = User::orderBy('created_at', 'ASC')->paginate(10);
        return view('users.list', compact('users'));
    }

    //########## This method will show Create User page ##########//
    public function create()
    {
        return view('users.create');
    }

    //########## This method will store User in DB ##########//
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            'password_confirmation' => 'required'
        ]);

        if ($validator->passes()) {
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            return redirect(route('users.index'))->with('success', 'User Profile Created Successfully');
        } else {
            return redirect()->route('users.create')->withInput()->withErrors($validator);
        }
    }

    //########## This method will show edit User page ##########//
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::orderBy('name', 'DESC')->get();
        $hasRole = $user->roles->pluck('id');
        return view('users.edit', compact('user', 'roles', 'hasRole'));
    }

    //########## This method will update User ##########//
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $id . ',id',
        ]);

        if ($validator->passes()) {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();

            $user->syncRoles($request->role);

            return redirect(route('users.index'))->with('success', 'User Profile Updated Successfully');
        } else {
            return redirect()->route('users.edit')->withInput()->withErrors($validator);
        }
    }

    //########## This method will delete Project ##########//
    public function delete($id)
    {
        $user = User::findOrFail($id);
        if (!is_null($user)) {
            $user->delete();
            return redirect(route('users.index'))->with('success', 'User Deleted Successfully');
        } else {
            return redirect(route('users.index'))->with('error', 'User not deleted due to some unconvinense plz check');
        }
    }


    
}
