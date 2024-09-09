@include('layout.sidebar')

<header class="header">
    <h1 class="text-center">Welcome to Dashboard Mr/Mes {{ Auth::user()->name }}</h1>
    <h2 class="digital-watch" id="digitalWatch">{{ Auth::user()->name }}
        ({{ Auth::user()->roles->pluck('name')->implode(', ') }}) </h2>
</header>

<h2 class="mt-5 text-center">Assigned Tasks</h2>

<table class="table table-dark position-relative ">
    <thead>
        <tr>
            <th class="col-md-1">#</th>
            <th class="col-md-3">Task Name</th>
            <th class="col-md-2">Project Name</th>
            <th class="col-md-1">Assigned Hours</th>
            <th class="col-md-1">Consumed Hours</th>
            <th class="col-md-1">Remaining Hours</th>
            <th class="col-md-3 text-center" colspan="3">Actions</th>
        </tr>
    </thead>
    <tbody id="allProjects">
        @foreach ($tasks as $task)
            <tr>
                <th class="col-md-1" scope="row">{{ $task->id }}</th>
                <td class="col-md-3">{{ $task->name }}</td>
                <td class="col-md-2">
                    @foreach ($projects as $project)
                        @php
                            $project_id = $project->id;
                        @endphp
                        @if ($project_id == $task->project_id)
                            {{ $project->name }}
                        @endif
                    @endforeach
                </td>
                <td class="col-md-1" id="assigned_hours_{{ $task->id }}">{{ $task->assigned_hours }}</td>
                <td class="col-md-1" id="consumed_hours_{{ $task->id }}"> {{$task->consumed_hours}} </td>
                <td class="col-md-1" id="remaining_hours_{{ $task->id }}">{{ $task->reamining_hours }}</td>

                <td class="col-md-3">
                    <input class="form-check-input task-checkbox" type="checkbox" name="select_task"
                        id="select_task_{{ $task->id }}" data-task-id="{{ $task->id }}">
                    <label class="form-check-label" for="select_task_{{ $task->id }}">Select Task</label>
                </td>

            </tr>
        @endforeach
    </tbody>
</table>
