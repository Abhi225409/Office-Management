@include('layout.sidebar')
<header class="header">
    <h1 class="text-center">Users</h1>
    @can('create users')
        <div>
            <a class="btnType_1" href="{{ route('clients.create') }}">Create</a>
        </div>
    @endcan

</header>

@include('components.message')

<table class="table table-dark position-relative col-md-12">
    <thead>
        <tr>
            <th class="col-md-1">#</th>
            <th class="col-md-2">Name</th>
            <th class="col-md-2">Email</th>
            <th class="col-md-4">Projects</th>
            <th class="col-md-3 text-center" colspan="3">Actions</th>
        </tr>
    </thead>
    <tbody id="allProjects">
        @foreach ($clients as $client)
            <tr>
                <th class="col-md-1" scope="row">{{ $client->id }}</th>
                <td class="col-md-2">{{ $client->name }}</td>
                <td class="col-md-2">{{ $client->email }}</td>
                <td class="col-md-2"> {{ $client['project_data']->pluck('name')->implode(', ') }} </td>

                <td class="col-md-3">
                    <div style="gap: 40px; display:flex; justify-content:center;">
                        @can('edit users')
                            <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-success px-3 py-2">Edit</a>
                        @endcan

                        @can('delete users')
                            <form action="{{ route('clients.destroy', $client->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger px-3 py-2"
                                    onclick="return confirm('Are you sure you want to delete this client?');">
                                    Delete
                                </button>
                            </form>
                        @endcan

                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{-- {{ $roles->links() }} --}}
