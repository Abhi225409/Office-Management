<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login_form()
    {
        return view('login');
    }

    //############### Loging User Functionality ################
    public function login(Request $request)
    {
        // Validate that only email and password are provided
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // $credentials = $request->only('email', 'password');
        // Attempt to log in using email and password
        if (Auth::attempt($credentials)) {
            return redirect()->route('dashboard');
        } else {
            // Authentication failed, log the attempt
            Log::info('Login attempt failed', ['email' => $credentials['email'], 'password' => $credentials['password']]);

            // Redirect back to login page with error message
            return redirect('/')->with('error', 'Login details are not valid');
        }
    }


    public function home()
    {
        $tasks = Task::where('user_id', auth()->user()->id)->get();
        $projects = Project::orderBy('name', 'ASC')->get();
        return view('/dashboard',compact('tasks','projects'));
    }

    public function logout()
    {
        Auth::logout();
        return redirect('');
    }
}
