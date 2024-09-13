@include('layout.sidebar')
<header class="header">
    <h1 class="text-center">Project Detail</h1>

    <div>
        <a class="btnType_1" href="{{ route('projects.index') }}">Back</a>
    </div>
</header>
<style>
    .detail_list .container .wrapper .content_container {
        background: black;
        color: white;
        padding: 30px;
        border-radius: 20px;
    }

    .detail_list .container .wrapper .content_container .content {
        border-bottom: 1px solid;
        margin-top: 15px;
    }
</style>

<section class="detail_list">
    <div class="container">
        <div class="col-md-12">
            <div class="wrapper">
                <div class="content_container">
                    <div class="content d-flex">
                        <div class="left col-md-4">Id</div>
                        <div class="right col-ms-12">{{ $project->id }}</div>
                    </div>
                    <div class="content d-flex">
                        <div class="left col-md-4">Name</div>
                        <div class="right col-ms-12"> {{ $project->name }} </div>
                    </div>
                    <div class="content d-flex">
                        <div class="left col-md-4">Description</div>
                        <div class="right col-ms-12"> {{ $project->description }} </div>
                    </div>
                    <div class="content d-flex">
                        <div class="left col-md-4">Client</div>
                        <div class="right col-ms-12"> {{ $project->client_name }} </div>
                    </div>
                    <div class="content d-flex">
                        <div class="left col-md-4">Project Type</div>
                        <div class="right col-ms-12"> {{ $project->project_type }} </div>
                    </div>
                    <div class="content d-flex">
                        <div class="left col-md-4">Total Hours</div>
                        <div class="right col-ms-12">{{ $project->total_hours }}</div>
                    </div>
                    <div class="content d-flex">
                        <div class="left col-md-4">Consumed Hours</div>
                        <div class="right col-ms-12"> {{ $project->consumed_hours ? $project->consumed_hours : 0 }}
                        </div>
                    </div>
                    <div class="content d-flex">
                        <div class="left col-md-4">Remaining Hours</div>
                        <div class="right col-ms-12"> {{ $project->reamining_hours ? $project->reamining_hours : 0 }}
                        </div>
                    </div>
                    <div class="content d-flex">
                        <div class="left col-md-4">Project Start Date</div>
                        <div class="right col-ms-12">
                            {{ $project->project_start_date ? $project->project_start_date : 'Not started Yet' }} </div>
                    </div>
                    <div class="content d-flex">
                        <div class="left col-md-4">Project End Date</div>
                        <div class="right col-ms-12">
                            {{ $project->project_end_date ? $project->project_end_date : 'Ongoing' }} </div>
                    </div>
                    <div class="content d-flex">
                        <div class="left col-md-4">Project End Date</div>
                        <div class="right col-ms-12">
                            {{ \Carbon\Carbon::parse($project->created_at)->format('d M,Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<h2 class="text-center mt-5">Project Tasks List</h2>

<table class="table table-dark position-relative mb-5">
    <thead>
        <tr>
            <th class="col-md-1">#</th>
            <th class="col-md-2">Name</th>
            <th class="col-md-2">Assigned To</th>
            <th class="col-md-2">Total Hours</th>
            <th class="col-md-2">Consumed Hours</th>
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

                <td class="col-md-2">{{ $task->assigned_hours }}</td>
                <td class="col-md-2">{{ $task->consumed_hours ? $task->consumed_hours : 0 }}</td>
                <td class="col-md-3">
                    <div style="gap: 40px; display:flex; justify-content:center;">
                        @can('edit projects')
                            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-success px-3 py-2">Edit</a>
                        @endcan

                        @can('delete projects')
                            <a href="{{ route('projects.delete', $project->id) }}"
                                class="btn btn-danger px-3 py-2">Delete</a>
                        @endcan


                        <a href="" data-task-id="{{ $task->id }}" class="btn btn-success px-3 py-2 btn-view-task">View</a>


                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>


<!-- Modal Structure -->
<div class="modal fade mask-custom" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"
            style="border-radius: 2em; backdrop-filter: blur(25px); border: 2px solid rgba(255, 255, 255, 0.05); background-clip: padding-box; box-shadow: 10px 10px 10px rgba(46, 54, 68, 0.03);">
            <div class="modal-header">
                <h5 class="modal-title" id="taskModalLabel">Project Task Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Task details will be loaded here -->
                <div id="modalContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



@if (!empty($tasks))
    @foreach ($tasks as $task)
        <div id="task_detail_{{ $task->id }}" class="detail_list mt-5 d-none">
            <div class="container">
                <div class="col-md-12">
                    <div class="wrapper">

                        <div class="content_container">
                            <div class="content d-flex" style="border-bottom: 2px solid; margin-bottom:10px">
                                <div class="left col-md-4">Id</div>
                                <div class="right col-ms-12"> {{ $task->id }} </div>
                            </div>
                            <div class="content d-flex" style="border-bottom: 2px solid; margin-bottom:10px">
                                <div class="left col-md-4">Name</div>
                                <div class="right col-ms-12">{{ $task->name }}</div>
                            </div>
                            <div class="content d-flex" style="border-bottom: 2px solid; margin-bottom:10px">
                                <div class="left col-md-4">Description</div>
                                <div class="right col-ms-12"> {{ $task->description }} </div>
                            </div>
                            <div class="content d-flex" style="border-bottom: 2px solid; margin-bottom:10px">
                                <div class="left col-md-4">Assigned Hours</div>
                                <div class="right col-ms-12">{{ $task->assigned_hours }}</div>
                            </div>
                            <div class="content d-flex" style="border-bottom: 2px solid; margin-bottom:10px">
                                <div class="left col-md-4">Consumed Hours</div>
                                <div class="right col-ms-12"> {{ $task->consumed_hours }} </div>
                            </div>

                            <div class="content d-flex" style="border-bottom: 2px solid; margin-bottom:10px">
                                <div class="left col-md-4">Remaining Hours</div>
                                <div class="right col-ms-12"> {{ $task->reamining_hours }} </div>
                            </div>

                            <div class="content d-flex" style="border-bottom: 2px solid; margin-bottom:10px">
                                <div class="left col-md-4">Assigned To</div>
                                <div class="right col-ms-12">
                                    @foreach ($users as $user)
                                        @php
                                            $user_id = $user->id;
                                        @endphp
                                        @if ($user_id == $task->user_id)
                                            {{ $user->name }}
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <div class="content d-flex" style="border-bottom: 2px solid; margin-bottom:10px">
                                <div class="left col-md-4">Task Status</div>
                                <div class="right col-ms-12">
                                    {{ $task->status == 0 ? 'Uncompleated Task' : 'Compleated Task' }}
                                </div>
                            </div>

                            <div class="content d-flex" style="border-bottom: 2px solid; margin-bottom:10px">
                                <div class="left col-md-4">Created On</div>
                                <div class="right col-ms-12">
                                    {{ \Carbon\Carbon::parse($task->created_at)->format('d M,Y') }}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
