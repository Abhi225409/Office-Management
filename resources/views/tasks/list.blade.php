@include('layout.sidebar')
<header class="header">
    <h1 class="text-center">Tasks</h1>
    @can('create projects')
        <div>
            <a class="btnType_1" href="{{ route('tasks.create') }}">Create</a>
        </div>
    @endcan

    <div>
        <a class="btnType_1" href="{{ route('tasks.trash') }}">Trash</a>
    </div>
</header>

@include('components.message')

<table class="table table-dark position-relative ">
    <thead>
        <tr>
            <th class="col-md-1">#</th>
            <th class="col-md-2">Task Name</th>
            <th class="col-md-2">Assigned To</th>
            <th class="col-md-2">Project Name</th>
            <th class="col-md-2">Assigned Hours</th>
            <th class="col-md-3 text-center" colspan="3">Actions</th>
        </tr>
    </thead>
    <tbody id="allProjects">
        @foreach ($tasks as $task)
            <tr>
                <th class="col-md-1" scope="row">{{ $task->id }}</th>
                <td class="col-md-2">{{ $task->name }}</td>
                <td class="col-md-2">
                    @foreach ($users as $user)
                        @php
                            $user_id = $user->id;
                        @endphp
                        @if ($user_id == $task->user_id)
                            {{ $user->name }}
                        @endif
                    @endforeach
                </td>
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
                <td class="col-md-2">{{ $task->assigned_hours }}</td>
                <td class="col-md-3">
                    <div style="gap: 40px; display:flex; justify-content:center;">
                        @can('edit projects')
                            <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-success px-3 py-2">Edit</a>
                        @endcan

                        @can('delete projects')
                            <a href="{{ route('tasks.delete', $task->id) }}"
                                class="btn btn-danger px-3 py-2">Delete</a>
                        @endcan

                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $tasks->links() }}
