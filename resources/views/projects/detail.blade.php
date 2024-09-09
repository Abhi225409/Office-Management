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
                        <div class="right col-ms-12"> {{ $project->consumed_hours ? $project->consumed_hours : 0 }} </div>
                    </div>
                    <div class="content d-flex">
                        <div class="left col-md-4">Remaining Hours</div>
                        <div class="right col-ms-12"> {{ $project->reamining_hours ? $project->reamining_hours : 0 }} </div>
                    </div>
                    <div class="content d-flex">
                        <div class="left col-md-4">Project Start Date</div>
                        <div class="right col-ms-12"> {{ $project->project_start_date ? $project->project_start_date : "Not started Yet" }} </div>
                    </div>
                    <div class="content d-flex">
                        <div class="left col-md-4">Project End Date</div>
                        <div class="right col-ms-12"> {{ $project->project_end_date ? $project->project_end_date : "Ongoing" }} </div>
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

@if (!empty($tasks))
<section class="detail_list mt-5">
    <h2 class="text-center">Project Tasks List</h2>
    <div class="container">
        <div class="col-md-12">
            <div class="wrapper">
                @foreach ($tasks as $task)
                    <div class="content_container" style="border-bottom: 2px solid; margin-bottom:10px">
                        <div class="content d-flex">
                            <div class="left col-md-4">Id</div>
                            <div class="right col-ms-12"> {{ $task->id }} </div>
                        </div>
                        <div class="content d-flex">
                            <div class="left col-md-4">Name</div>
                            <div class="right col-ms-12">{{ $task->name }}</div>
                        </div>
                        <div class="content d-flex">
                            <div class="left col-md-4">Description</div>
                            <div class="right col-ms-12"> {{ $task->description }} </div>
                        </div>
                        <div class="content d-flex">
                            <div class="left col-md-4">Assigned Hours</div>
                            <div class="right col-ms-12">{{ $task->assigned_hours }}</div>
                        </div>
                        <div class="content d-flex">
                            <div class="left col-md-4">Consumed Hours</div>
                            <div class="right col-ms-12"> {{ $task->consumed_hours }} </div>
                        </div>

                        <div class="content d-flex">
                            <div class="left col-md-4">Remaining Hours</div>
                            <div class="right col-ms-12"> {{ $task->reamining_hours }} </div>
                        </div>

                        <div class="content d-flex">
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

                        <div class="content d-flex">
                            <div class="left col-md-4">Task Status</div>
                            <div class="right col-ms-12">
                                {{ $task->status == 0 ? 'Uncompleated Task' : 'Compleated Task' }}
                            </div>
                        </div>

                        <div class="content d-flex">
                            <div class="left col-md-4">Created On</div>
                            <div class="right col-ms-12">
                                {{ \Carbon\Carbon::parse($task->created_at)->format('d M,Y') }}
                            </div>
                        </div>
                    </div>
                @endforeach


            </div>
        </div>
    </div>
</section>
@endif