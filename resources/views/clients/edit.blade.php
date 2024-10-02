@include('layout.sidebar')
<header class="header">
    <h1 class="text-center">Edit Clients</h1>
    <div>
        <a class="btnType_1" href="{{ route('clients.index') }}">Back</a>
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
                <h2 class="text-center text-white mb-5">Client Edit Form</h2>
                <form class="permission_form" action="{{ route('clients.update', $client->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="col-md-8">
                        <input placeholder="Enter Client Name" name="name" class="form-control" type="text"
                            value="{{ old('name', $client->name) }}" />

                        @error('name')
                            <p class=" mt-2 text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-md-8 mt-2">
                        <input placeholder="Enter Client Email" name="email" class="form-control" type="email"
                            value="{{ old('email', $client->email) }}" />

                        @error('email')
                            <p class=" mt-2 text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-md-8 mt-4 gap-2">
                        <button class="btnType_1"> Update </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
