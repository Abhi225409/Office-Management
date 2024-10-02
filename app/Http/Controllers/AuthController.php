<?php

namespace App\Http\Controllers;

use App\Models\Loginlogout;
use App\Models\Lunch;
use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
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

        // Attempt to log in using email and password
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $loginlogout = new Loginlogout;
            $loginlogout->user_id = $user->id;
            $loginlogout->date = now()->toDateString();
            $loginlogout->login_time = now();
            $loginlogout->save();
            return redirect()->route('dashboard');
        } else {
            Log::info('Login attempt failed', ['email' => $credentials['email'], 'password' => $credentials['password']]);
            return redirect('/')->with('error', 'Login details are not valid');
        }
    }


    public function home()
    {
        $tasks = Task::where('user_id', auth()->user()->id)->get();
        $projects = Project::orderBy('name', 'ASC')->get();
        $breaks = Lunch::where('user_id', auth()->user()->id)->whereDate('date', Carbon::today())->get();
        $loginlogouts = Loginlogout::where('user_id', auth()->user()->id)->whereDate('date', Carbon::today())->get();
        return view('/dashboard', compact('tasks', 'projects', 'breaks', 'loginlogouts'));
    }

    public function logout()
    {
        $user = Auth::user();
        // Find the break record for the current date, where end_time is null
        $loginlogout = Loginlogout::where('user_id', $user->id)
            ->where('date', now()->toDateString())
            ->whereNull('logout_time')
            ->first();

        // If a matching break is found, update the end_time
        if ($loginlogout) {
            $loginlogout->logout_time = now(); // Set the current time as the end time
            $loginlogout->save();
        }
        Auth::logout();
        return redirect('');
    }
}
