<?php

namespace App\Http\Controllers;

use App\Models\Lunch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BreakController extends Controller
{
    public function startBreak()
    {
        $user = Auth::user();
        if ($user) {
            $break = new Lunch();
            $break->user_id = $user->id;
            $break->date = now()->toDateString();
            $break->start_time = now();
            $break->save();

            return response()->json(['message' => 'Break started successfully', 'break' => $break], 200);
        } else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }

    public function endBreak()
    {
        $user = Auth::user();
        // Find the break record for the current date, where end_time is null
        $break = Lunch::where('user_id', $user->id)
            ->where('date', now()->toDateString())
            ->whereNull('end_time')
            ->first();

        // If a matching break is found, update the end_time
        if ($break) {
            $break->end_time = now(); // Set the current time as the end time
            $break->save();

            return response()->json(['message' => 'Break ended successfully', 'break' => $break], 200);
        } else {
            return response()->json(['message' => 'No active break found for today'], 404);
        }
    }
}
