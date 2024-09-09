@include('layout.sidebar')
<header class="header">
    <h1 class="text-center">Create Roles</h1>
    <div>
        <a class="btnType_1" href="{{ route('roles.index') }}">Back</a>
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

    .checkboxes_value {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        color: white;
    }
</style>


<section class="create_permissions mt-5">
    <div class="container">
        <div class="col-md-12">
            <div class="form_wrapper">
                <h2 class="text-center text-white mb-5">Role Update Form</h2>
                <form class="permission_form" action="{{ route('roles.store') }}" method="post">
                    @csrf
                    <div class="col-md-6">
                        <input placeholder="Enter Role Name" name="name" class="form-control" type="text"
                            value="{{ old('name') }}" />

                        @error('name')
                            <p class=" mt-2 text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-md-12 checkboxes_value mt-5">
                        @if ($permissions->isNotEmpty())
                            @foreach ($permissions as $permission)
                                <div class="form-check">
                                    <input class="form-check-input" id="permission-{{ $permission->id }}"
                                        type="checkbox" name="permission[]" value="{{ $permission->name }}">
                                    <label class="form-check-label"
                                        for="permission-{{ $permission->id }}">{{ $permission->name }}</label>
                                </div>
                            @endforeach


                        @endif
                    </div>

                    <div class="col-md-6 mt-4 gap-2">
                        <button class="btnType_1"> Submit </button>
                    </div>
                </form>


            </div>
        </div>
    </div>
</section>
