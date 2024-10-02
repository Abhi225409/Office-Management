<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Symfony\Component\VarDumper\VarDumper;

class TaskController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view projects', only: ['index']),
            new Middleware('permission:edit projects', only: ['edit']),
            new Middleware('permission:create projects', only: ['create']),
            new Middleware('permission:delete projects', only: ['delete']),

        ];
    }

    //########## This method will show Taks page ##########//
    public function index()
    {
        $tasks = Task::orderBy('created_at', 'ASC')->paginate(10);
        $projects = Project::orderBy('name', 'ASC')->get();
        $users = User::orderBy('name', 'ASC')->get();
        return view('tasks.list', compact('tasks', 'projects', 'users'));
    }

    //########## This method will show Create Taks page ##########//
    public function create()
    {
        $users = User::orderBy('name', 'ASC')->get();
        $projects = Project::orderBy('name', 'ASC')->get();
        return view('tasks.create', compact('users', 'projects'));
    }

    //########## This method will store Taks in DB ##########//
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'description' => 'required',
            'assigned_hours' => ['required', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/'],
            'project_id' => 'required',
            'user_id' => 'required',
        ], [
            'assigned_hours.regex' => 'Assigned hours must be in HH:MM:SS format',
        ]);

        if ($validator->passes()) {
            $project = new Task;



            $project->name = $request->name;
            $project->description = $request->description;
            $project->assigned_hours = $request->assigned_hours;
            $project->consumed_hours = "00:00:00";
            $project->reamining_hours = $request->assigned_hours;
            $project->project_id = $request->project_id;
            $project->user_id = $request->user_id;
            $project->save();

            $poject_value = Project::findOrFail($request->project_id);
            $pro_assigned_hours = $poject_value->assigned_hours;
            $time1 = $request->assigned_hours;
            $time2 = $pro_assigned_hours;
            function timeToSeconds($time)
            {
                sscanf($time, "%d:%d:%d", $hours, $minutes, $seconds);
                return $hours * 3600 + $minutes * 60 + $seconds;
            }
            function secondsToTime($seconds)
            {
                $hours = floor($seconds / 3600);
                $minutes = floor(($seconds % 3600) / 60);
                $seconds = $seconds % 60;

                // Return formatted time as H:i:s
                return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            }
            $time1InSeconds = timeToSeconds($time1);
            $time2InSeconds = timeToSeconds($time2);
            $totalSeconds = $time1InSeconds + $time2InSeconds;
            $totalTime = secondsToTime($totalSeconds);
            $poject_value->assigned_hours = $totalTime;
            $poject_value->save();
            return redirect(route('tasks.index'))->with('success', 'Task Created Successfully');
        } else {
            return redirect()->route('tasks.create')->withInput()->withErrors($validator);
        }
    }

    //########## This method will show Taks Edit page ##########//
    public function edit($id)
    {
        $users = User::orderBy('name', 'ASC')->get();
        $projects = Project::orderBy('name', 'ASC')->get();
        $task = Task::findOrFail($id);
        return view('tasks.edit', compact('task', 'users', 'projects'));
    }

    //########## This method will Update Taks ##########//
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'description' => 'required',
            'assigned_hours' => ['required', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/'],
            'project_id' => 'required',
            'user_id' => 'required',
        ], [
            'assigned_hours.regex' => 'Assigned hours must be in HH:MM:SS format',
        ]);

        if ($validator->passes()) {
            $task->name = $request->name;
            $task->description = $request->description;
            $task->assigned_hours = $request->assigned_hours;
            $task->project_id = $request->project_id;
            $task->user_id = $request->user_id;

            $project_value = Project::findOrFail($request->project_id);
            // Convert time format to seconds
            function timeToSeconds($time)
            {
                sscanf($time, "%d:%d:%d", $hours, $minutes, $seconds);
                return $hours * 3600 + $minutes * 60 + $seconds;
            }

            // Convert seconds back to time format
            function secondsToTime($seconds)
            {
                $hours = floor($seconds / 3600);
                $minutes = floor(($seconds % 3600) / 60);
                $seconds = $seconds % 60;

                return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            }
            $pro_assigned_hours = $project_value->assigned_hours;
            $previous_task_hours = $task->assigned_hours;
            $new_task_hours = $request->assigned_hours;

            // Convert all time values to seconds
            $pro_assigned_seconds = timeToSeconds($pro_assigned_hours);
            $previous_task_seconds = timeToSeconds($previous_task_hours);
            $new_task_seconds = timeToSeconds($new_task_hours);
            $updated_project_seconds = $pro_assigned_seconds - $previous_task_seconds;
            $updated_project_seconds += $new_task_seconds;

            // Convert total seconds back to HH:MM:SS format and update the project assigned hours
            $updated_project_time = secondsToTime($updated_project_seconds);
            $project_value->assigned_hours = $updated_project_time;
            $task->reamining_hours = $updated_project_time;

            $task->save();
            $project_value->save();

            return redirect(route('tasks.index'))->with('success', 'Task Updated Successfully');
        } else {
            return redirect()->route('tasks.edit')->withInput()->withErrors($validator);
        }
    }

    //########## This method will show Trashed Taks page ##########//
    public function trash()
    {
        $users = User::orderBy('name', 'ASC')->get();
        $projects = Project::orderBy('name', 'ASC')->get();
        $tasks = Task::onlyTrashed()->orderBy('created_at', 'ASC')->paginate(10);
        return view('tasks.trash', compact('tasks', 'users', 'projects'));
    }

    //########## This method will restore Taks from trashed page to index page ##########//
    public function restore($id)
    {
        $task = Task::withTrashed()->findOrFail($id);
        $taks_assignedHours = $task->assigned_hours;
        if (!is_null($task)) {
            $task->restore();

            function timeToSeconds($time)
            {
                sscanf($time, "%d:%d:%d", $hours, $minutes, $seconds);
                return $hours * 3600 + $minutes * 60 + $seconds;
            }

            // Convert seconds back to time format
            function secondsToTime($seconds)
            {
                $hours = floor($seconds / 3600);
                $minutes = floor(($seconds % 3600) / 60);
                $seconds = $seconds % 60;

                return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            }
            $poject = Project::findOrFail($task->project_id);
            $pro_assigned_hours = $poject->assigned_hours;

            $pro_assigned_seconds = timeToSeconds($pro_assigned_hours);
            $task_seconds = timeToSeconds($taks_assignedHours);
            $updated_project_seconds = $pro_assigned_seconds + $task_seconds;
            $updated_project_seconds = secondsToTime($updated_project_seconds);
            $poject->assigned_hours =  $updated_project_seconds;
            $poject->save();
        }
        return redirect(route('tasks.index'))->with('success', 'Task Restored Successfully');
    }

    //########## This method will move to tash Taks ##########//
    public function delete($id)
    {
        $task = Task::findOrFail($id);
        $taks_assignedHours = $task->assigned_hours;
        if (!is_null($task)) {
            $task->delete();

            function timeToSeconds($time)
            {
                sscanf($time, "%d:%d:%d", $hours, $minutes, $seconds);
                return $hours * 3600 + $minutes * 60 + $seconds;
            }

            // Convert seconds back to time format
            function secondsToTime($seconds)
            {
                $hours = floor($seconds / 3600);
                $minutes = floor(($seconds % 3600) / 60);
                $seconds = $seconds % 60;

                return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            }
            $poject = Project::findOrFail($task->project_id);
            $pro_assigned_hours = $poject->assigned_hours;

            $pro_assigned_seconds = timeToSeconds($pro_assigned_hours);
            $task_seconds = timeToSeconds($taks_assignedHours);
            $updated_project_seconds = $pro_assigned_seconds - $task_seconds;
            $updated_project_seconds = secondsToTime($updated_project_seconds);
            $poject->assigned_hours =  $updated_project_seconds;
            $poject->save();
            return redirect(route('tasks.index'))->with('success', 'Task has been moved to trashed Successfully');
        }
    }

    //########## This method will permanentlely delete Taks ##########//
    public function permanetDelete($id)
    {

        $task = Task::withTrashed()->findOrFail($id);
        $taks_assignedHours = $task->assigned_hours;
        if (!is_null($task)) {
            $task->forceDelete();

            function timeToSeconds($time)
            {
                sscanf($time, "%d:%d:%d", $hours, $minutes, $seconds);
                return $hours * 3600 + $minutes * 60 + $seconds;
            }

            // Convert seconds back to time format
            function secondsToTime($seconds)
            {
                $hours = floor($seconds / 3600);
                $minutes = floor(($seconds % 3600) / 60);
                $seconds = $seconds % 60;

                return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            }
            $poject = Project::findOrFail($task->project_id);
            $pro_assigned_hours = $poject->assigned_hours;

            $pro_assigned_seconds = timeToSeconds($pro_assigned_hours);
            $task_seconds = timeToSeconds($taks_assignedHours);
            $updated_project_seconds = $pro_assigned_seconds - $task_seconds;
            $updated_project_seconds = secondsToTime($updated_project_seconds);
            $poject->assigned_hours =  $updated_project_seconds;
            $poject->save();
        }
        return redirect(route('task.index'))->with('success', 'Task Permanently Deleted Successfully');
    }

    public function saveTaskTimer(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'task_id' => 'required|integer',
            'consumed_hours' => 'required|string',
            'reamining_hours' => 'required|string',
        ]);

        // Find the task by ID
        $task = Task::find($request->task_id);

        // If task exists, update the hours
        if ($task) {
            $task->consumed_hours = $request->consumed_hours;
            $task->reamining_hours = $request->reamining_hours;
            $task->save();

            return response()->json(['success' => true, 'message' => 'Task timer saved successfully!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Task not found!']);
        }
    }



    public function filterTasks(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'from' => 'date|nullable',
            'to' => 'date|nullable',
        ]);

        $users = User::orderBy('name', 'ASC')->get();
        $projects = Project::orderBy('name', 'ASC')->get();

        $fromDate = $request->input('from');
        $toDate = $request->input('to');
        $user_id = $request->user_id;

        if ($fromDate && $toDate) {
            $fromYearMonth = date('Y-m-d', strtotime($fromDate));
            $toYearMonth = date('Y-m-d', strtotime($toDate));
        }

        if (!empty($fromYearMonth) && !empty($toYearMonth) && !empty($user_id)) {
            $filteredData = Task::where('user_id', $user_id)->whereBetween('created_at', [$fromYearMonth, $toYearMonth])->get();
        } elseif (!empty($fromYearMonth) && !empty($toYearMonth)) {
            $filteredData = Task::whereBetween('created_at', [$fromYearMonth, $toYearMonth])->get();
        } else {
            $filteredData = Task::where('user_id', $user_id)->get();
        }

        $output = "";

        // Loop through each project and build the output string
        foreach ($filteredData as $task) {
            $output .= '<tr>';
            $output .= '<th class="col-md-1" scope="row">' . $task->id . '</th>';
            $output .= '<td class="col-md-2">' . $task->name . '</td>';
            $user = $users->firstWhere('id', $task->user_id);
            $output .= '<td class="col-md-2">' . ($user ? $user->name : 'Not Found') . '</td>';
            $project = $projects->firstWhere('id', $task->project_id);
            $output .= '<td class="col-md-2">' . ($project ? $project->name : 'Not Found') . '</td>';
            $output .= '<td class="col-md-2">' . $task->assigned_hours . '</td>';
            $output .= '<td class="col-md-3">';
            $output .= '<div style="gap: 40px; display:flex; justify-content:center;">';

            // Check user permission for editing projects
            if (auth()->user()->can('edit projects')) {
                $output .= '<a href="' .  route('tasks.edit', $task->id)  . '" class="btn btn-success px-3 py-2">Edit</a>';
            }

            // Check user permission for deleting projects
            if (auth()->user()->can('delete projects')) {
                $output .= '<a href="' . route('tasks.delete', $task->id) . '" class="btn btn-danger px-3 py-2">Delete</a>';
            }

            // View button
            $output .= '<a href="' . route('projects.detail', $task->id) . '" class="btn btn-success px-3 py-2">View</a>';

            $output .= '</div>';
            $output .= '</td>';
            $output .= '</tr>';
        }
        // Return the output
        return response()->json(['html' => $output]);
    }
}
