@include('layout.sidebar')
<header class="header">
    <h1 class="text-center">Projects</h1>
    @can('create projects')
        <div>
            <a class="btnType_1" href="{{ route('projects.index') }}">Back</a>
        </div>
    @endcan

   

</header>

@include('components.message')

<table class="table table-dark position-relative ">
    <thead>
        <tr>
            <th class="col-md-1">#</th>
            <th class="col-md-2">Name</th>
            <th class="col-md-2">Client Name</th>
            <th class="col-md-2">Project Type</th>
            <th class="col-md-2">Total Hours</th>
            <th class="col-md-3 text-center" colspan="3">Actions</th>
        </tr>
    </thead>
    <tbody id="allProjects">
        @foreach ($projects as $project)
            <tr>
                <th class="col-md-1" scope="row">{{ $project->id }}</th>
                <td class="col-md-2">{{ $project->name }}</td>
                <td class="col-md-2">{{ $project->client_name }}</td>
                <td class="col-md-2">{{ $project->project_type }}</td>
                <td class="col-md-2">{{ $project->total_hours }}</td>
                <td class="col-md-3">
                    <div style="gap: 40px; display:flex; justify-content:center;">
                        @can('edit projects')
                            <a href="{{ route('projects.restore', $project->id) }}" class="btn btn-success px-3 py-2">Restore</a>
                        @endcan

                        @can('delete projects')
                            <a href="{{ route('projects.permanetdelete', $project->id) }}"
                                class="btn btn-danger px-3 py-2">Delete</a>
                        @endcan

                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $projects->links() }}
