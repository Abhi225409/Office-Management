<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProjectController extends Controller implements HasMiddleware
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

    //########## This method will show Project page ##########//
    public function index()
    {
        $projects = Project::orderBy('created_at', 'ASC')->paginate(10);
        return view('projects.list', compact('projects'));
    }

    //########## This method will show Project Trashed page ##########//
    public function trash()
    {
        $projects = Project::onlyTrashed()->orderBy('created_at', 'ASC')->paginate(10);
        return view('projects.trash', compact('projects'));
    }

    //########## This method will Restore Project from Trashed page to listing page ##########//
    public function restore($id)
    {
        $project = Project::withTrashed()->findOrFail($id);
        if (!is_null($project)) {
            $project->restore();
        }
        return redirect(route('projects.index'))->with('success', 'Project Restored Successfully');
    }

    public function permanetDelete($id)
    {
        $project = Project::withTrashed()->findOrFail($id);
        if (!is_null($project)) {
            $project->forceDelete();
        }
        return redirect(route('projects.index'))->with('success', 'Project Permanently Deleted Successfully');
    }

    //########## This method will show Create project page ##########//
    public function create()
    {
        $clients = Client::orderBy('name', 'ASC')->get();
        return view('projects.create', compact('clients'));
    }

    //########## This method will insert a Project in DB ##########//
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'description' => 'required',
            'client_name' => 'required',
            'project_type' => 'required',
            'total_hours' => 'required',
        ]);

        if ($validator->passes()) {
            $project = new Project;
            $project->name = $request->name;
            $project->description = $request->description;
            $project->client_name = $request->client_name;
            $project->project_type = $request->project_type;
            $project->total_hours = $request->total_hours;
            $project->assigned_hours = "00:00:00";
            $project->status = 0;
            $project->save();

            return redirect(route('projects.index'))->with('success', 'Project Created Successfully');
        } else {
            return redirect()->route('projects.create')->withInput()->withErrors($validator);
        }
    }


    //########## This method will show edit Project page ##########//
    public function edit($id)
    {
        $project = Project::findOrFail($id);
        return view('projects.edit', compact('project'));
    }

    //########## This method will update Project ##########//
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'description' => 'required',
            'client_name' => 'required',
            'project_type' => 'required',
            'total_hours' => 'required',
        ]);

        if ($validator->passes()) {
            $project->name = $request->name;
            $project->description = $request->description;
            $project->client_name = $request->client_name;
            $project->project_type = $request->project_type;
            $project->total_hours = $request->total_hours;
            $project->project_start_date = $request->project_start_date;
            $project->project_end_date = $request->project_end_date;
            $project->save();

            return redirect(route('projects.index'))->with('success', 'Project Updated Successfully');
        } else {
            return redirect()->route('projects.edit')->withInput()->withErrors($validator);
        }
    }

    //########## This method will delete Project ##########//
    public function delete($id)
    {
        $project = Project::findOrFail($id);
        if (!is_null($project)) {
            $project->delete();
            return redirect(route('projects.index'))->with('success', 'Project Deleted Successfully');
        }
    }

    public function detail($id)
    {
        $project = Project::findOrFail($id);
        $tasks = Task::where('project_id', $id)->get();
        $users = User::orderBy('name', 'ASC')->get();
        return view('projects.detail', compact('project', 'tasks', 'users'));
    }


    public function filterProjects(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date',
        ]);

        $fromDate = $request->input('from');
        $toDate = $request->input('to');

        if ($fromDate && $toDate) {
            $fromYearMonth = date('Y-m-d', strtotime($fromDate));
            $toYearMonth = date('Y-m-d', strtotime($toDate));
        }

        $filteredData = Project::whereBetween('created_at', [$fromYearMonth, $toYearMonth])->get();

        $output = "";

        // Loop through each project and build the output string
        foreach ($filteredData as $project) {
            $output .= '<tr>';
            $output .= '<th class="col-md-1" scope="row">' . $project->id . '</th>';
            $output .= '<td class="col-md-2">' . $project->name . '</td>';
            $output .= '<td class="col-md-2">' . $project->client_name . '</td>';
            $output .= '<td class="col-md-2">' . $project->project_type . '</td>';
            $output .= '<td class="col-md-1">' . $project->total_hours . '</td>';
            $output .= '<td class="col-md-2">' . ($project->consumed_hours ? $project->consumed_hours : 0) . '</td>';
            $output .= '<td class="col-md-3">';
            $output .= '<div style="gap: 40px; display:flex; justify-content:center;">';

            // Check user permission for editing projects
            if (auth()->user()->can('edit projects')) {
                $output .= '<a href="' . route('projects.edit', $project->id) . '" class="btn btn-success px-3 py-2">Edit</a>';
            }

            // Check user permission for deleting projects
            if (auth()->user()->can('delete projects')) {
                $output .= '<a href="' . route('projects.delete', $project->id) . '" class="btn btn-danger px-3 py-2">Delete</a>';
            }

            // View button
            $output .= '<a href="' . route('projects.detail', $project->id) . '" class="btn btn-success px-3 py-2">View</a>';

            $output .= '</div>';
            $output .= '</td>';
            $output .= '</tr>';
        }

        // Return the output
        return response()->json(['html' => $output]);
    }
}
