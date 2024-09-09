@include('layout.sidebar')
<header class="header">
    <h1 class="text-center">Users</h1>
    @can('create users')
        <div>
            <a class="btnType_1" href="{{ route('users.create') }}">Create</a>
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
            <th class="col-md-2">Roles</th>
            <th class="col-md-2">Created</th>
            <th class="col-md-3 text-center" colspan="3">Actions</th>
        </tr>
    </thead>
    <tbody id="allProjects">
        @foreach ($users as $user)
            <tr>
                <th class="col-md-1" scope="row">{{ $user->id }}</th>
                <td class="col-md-2">{{ $user->name }}</td>
                <td class="col-md-2">{{ $user->email }}</td>
                <td class="col-md-2">{{ $user->roles->pluck('name')->implode(', ') }}</td>
                <td class="col-md-2">{{ \Carbon\Carbon::parse($user->created_at)->format('d M , Y') }}</td>
                <td class="col-md-3">
                    <div style="gap: 40px; display:flex; justify-content:center;">
                        @can('edit users')
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-success px-3 py-2">Edit</a>
                        @endcan

                        @can('delete users')
                            <a href="{{ route('users.delete', $user->id) }}" class="btn btn-danger px-3 py-2">Delete</a>
                        @endcan

                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{-- {{ $roles->links() }} --}}
