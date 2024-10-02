@include('layout.sidebar')

<header class="header">
    <h1 class="text-center">Welcome to Dashboard Mr/Mes {{ Auth::user()->name }}</h1>
    <h2 class="digital-watch" id="digitalWatch">{{ Auth::user()->name }}
        ({{ Auth::user()->roles->pluck('name')->implode(', ') }}) </h2>
</header>

@php
    $allIntervels = [];
    $totalSeconds = 0;

    $allLogins = [];
    $totallogseconds = 0;
@endphp

@foreach ($breaks as $break)
    @php
        $datetime1 = new DateTime($break->start_time);
        $datetime2 = new DateTime($break->end_time);
        $interval = $datetime1->diff($datetime2);
        $allIntervels[] = $interval;

        // Convert the interval to seconds and add to total
        $seconds = $interval->h * 3600 + $interval->i * 60 + $interval->s;
        $totalSeconds += $seconds;
    @endphp
@endforeach

@foreach ($loginlogouts as $loginlogout)
    @php
        $logdatetime1 = new DateTime($loginlogout->login_time);
        $logdatetime2 = new DateTime($loginlogout->logout_time);
        $loginterval = $logdatetime1->diff($logdatetime2);
        $allLogins[] = $loginterval;

        // Convert the interval to seconds and add to total
        $logseconds = $loginterval->h * 3600 + $loginterval->i * 60 + $loginterval->s;
        $totallogseconds += $logseconds;
    @endphp
@endforeach

{{-- Create Lunch and break Button --}}
<section class="break_lunch">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="wprapper">
                    <div>
                        <label class="form-check-label" for="select_break">Check If You are Taking a Break</label>
                        <input class="form-check-input break-checkbox" type="checkbox" name="select_break"
                            id="select_break">
                    </div>
                    <div class="diffrence">
                        @php
                            $hours = floor($totalSeconds / 3600);
                            $minutes = floor(($totalSeconds % 3600) / 60);
                            $seconds = $totalSeconds % 60;

                            echo "Today's Break Time : {$hours} hours, {$minutes} minutes, {$seconds} seconds";
                        @endphp
                    </div>
                    <div class="breakstatus">
                        Status : Working

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="break_lunch">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="wprapper">
                    <div class="col-md-3">
                        <h4>Login Details</h4>
                    </div>

                    <table class=" col-md-6">
                        <tr>
                            <th>Login Time</th>
                            <th>Logoutin Time</th>
                        </tr>
                        @foreach ($loginlogouts as $value)
                            <tr>
                                <td>
                                    @php $login_time = $value->login_time; @endphp
                                    {{ date('H:i:s', strtotime($login_time)) }}
                                </td>
                                <td>
                                    @php $logout_time = $value->logout_time ? date('H:i:s', strtotime($value->logout_time)) : 'Not Define'; @endphp
                                    {{ $logout_time }}
                                </td>
                            </tr>
                        @endforeach
                    </table>

                    <div class="col-md-3">
                        @php
                            $loghours = floor($totallogseconds / 3600);
                            $logminutes = floor(($totallogseconds % 3600) / 60);
                            $logseconds = $totallogseconds % 60;

                            echo "Office Hours : {$loghours}H, {$logminutes}M, {$logseconds}S";
                        @endphp
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>







{{-- Show Assigned Task to The User --}}
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
                <td class="col-md-1" id="consumed_hours_{{ $task->id }}"> {{ $task->consumed_hours }} </td>
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
