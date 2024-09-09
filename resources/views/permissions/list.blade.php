@include('layout.sidebar')
<header class="header">
    <h1 class="text-center">Permissions</h1>
    @can('create permissions')
        <div>
            <a class="btnType_1" href="{{ route('permissions.create') }}">Create</a>
        </div>
    @endcan

</header>

@include('components.message')

<table class="table table-dark position-relative ">
    <thead>
        <tr>
            <th class="col-md-1">#</th>
            <th class="col-md-4">Name</th>
            <th class="col-md-3">Created</th>
            <th class="col-md-4 text-center" colspan="3">Actions</th>
        </tr>
    </thead>
    <tbody id="allProjects">
        @foreach ($permissions as $permission)
            <tr>
                <th class="col-md-1" scope="row">{{ $permission->id }}</th>
                <td class="col-md-4">{{ $permission->name }}</td>
                <td class="col-md-3">{{ \Carbon\Carbon::parse($permission->created_at)->format('d M , Y') }}</td>
                <td class="col-md-4">
                    <div style="gap: 40px; display:flex; justify-content:center;">
                        @can('edit permissions')
                            <a href="{{ route('permissions.edit', $permission->id) }}"
                                class="btn btn-success px-3 py-2">Edit</a>
                        @endcan

                        @can('delete permissions')
                            <a href="{{ route('permissions.delete', $permission->id) }}"
                                class="btn btn-danger px-3 py-2">Delete</a>
                        @endcan

                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $permissions->links() }}
