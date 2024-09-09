@include('layout.sidebar')
<header class="header">
    <h1 class="text-center">Create Project</h1>
    <div>
        <a class="btnType_1" href="{{ route('projects.index') }}">Back</a>
    </div>
</header>

<style>
    .form_wrapper {
        background: black;
        max-width: 700px;
        width: 100%;
        margin: 0 auto;
        padding: 40px 70px;
        border-radius: 30px;
    }

    .permission_form {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .btnType_1 {
        width: 80px;
        height: 40px;
        padding: 20px 76px;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto;
        border-radius: 20px;
        background: white;
        color: black;
        text-transform: uppercase;
        font-weight: 600;
        transition: 0.3s all ease-in;
    }

    .btnType_1:hover {
        background: rgb(105, 103, 103);
        color: rgb(240, 222, 222);
    }
</style>


<section class="create_permissions mt-5">
    <div class="container">
        <div class="col-md-12">
            <div class="form_wrapper">
                <h2 class="text-center text-white mb-5">Project Update Form</h2>
                <form class="permission_form" action="{{route('projects.update',$project->id)}}" method="post">
                    @csrf
                    <div class="col-md-8">
                        <label class="text-white" for="">Project Name</label>
                        <input placeholder="Enter Project Name" name="name" class="form-control" type="text"
                            value="{{ old('name',$project->name) }}" />

                        @error('name')
                            <p class=" mt-2 text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-md-8 mt-3">
                        <label class="text-white" for="">Description</label>
                        <textarea class="form-control" placeholder="Project Discription Here..." name="description" id=""
                            rows="3">{{ old('description',$project->description) }}</textarea>
                        @error('description')
                            <p class=" mt-2 text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-md-8 mt-3">
                        <label class="text-white" for="">Client Name</label>
                        <select class="form-control" name="client_name">
                            <option  value="">Select Your Client</option>
                            <option {{ $project->client_name == 'Denial' ? 'selected' : '' }} value="Denial">Denial</option>
                            <option {{ $project->client_name == 'Mayur' ? 'selected' : '' }} value="Mayur">Mayur</option>
                            <option {{ $project->client_name == 'Chad' ? 'selected' : '' }} value="Chad">Chad</option>
                        </select>

                        @error('client_name')
                            <p>{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-md-8 mt-3">
                        <label class="text-white" for="">Project Type</label>
                        <select class="form-control" name="project_type">
                            <option value="">Select Your Project Type</option>
                            <option value="Fixed Coast" {{ $project->project_type == 'Fixed Coast' ? 'selected' : '' }}>Fixed Coast</option>
                            <option value="Full Seat" {{ $project->project_type == 'Full Seat' ? 'selected' : '' }} >Full Seat</option>
                            <option value="Hourly" {{ $project->project_type == 'Hourly' ? 'selected' : '' }} >Hourly</option>
                        </select>
                        @error('project_type')
                            <p>{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-md-8 mt-3">
                        <label class="text-white" for="">Total Hours</label>
                        <input class="form-control" value="{{ old('total_hours',$project->total_hours) }}" type="text" name="total_hours"
                            placeholder="Total Hours">
                        @error('total_hours')
                            <p>{{ $message }}</p>
                        @enderror
                    </div>
                    
                    
                    <div class="col-md-8 mt-3">
                        <label class="text-white" for="">Project Start Date</label>
                        <input class="form-control" value="{{ old('project_start_date',$project->project_start_date) }}" type="date" name="project_start_date"
                            placeholder="Project Start Date">
                        @error('project_start_date')
                            <p>{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-md-8 mt-3">
                        <label class="text-white" for="">Project End Date</label>
                        <input class="form-control" value="{{ old('project_end_date',$project->project_end_date) }}" type="date" name="project_end_date"
                            placeholder="Project End Date">
                        @error('project_end_date')
                            <p>{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-md-8 mt-4 gap-2">
                        <button class="btnType_1"> Submit </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>
