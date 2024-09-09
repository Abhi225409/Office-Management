@include('layout.sidebar')
<header class="header">
    <h1 class="text-center">Roles</h1>
    
    @can('create roles')
        <div>
            <a class="btnType_1" href="{{ route('roles.create') }}">Create</a>
        </div>
    @endcan

</header>

@include('components.message')

<section>
    <table class="table table-dark position-relative ">
        <thead>
            <tr class="col-md-12">
                <th class="col-md-1">#</th>
                <th class="col-md-2">Name</th>
                <th class="col-md-4">Permissions</th>
                <th class="col-md-2">Created</th>
                <th class="col-md-3 text-center" colspan="3">Actions</th>
            </tr>
        </thead>
        <tbody id="allProjects">
            @foreach ($roles as $role)
                <tr class="col-md-12">
                    <th class="col-md-1" scope="row">{{ $role->id }}</th>
                    <td class="col-md-2">{{ $role->name }}</td>
                    <td class="col-md-4">{{ $role->permissions->pluck('name')->implode(', ') }}</td>
                    <td class="col-md-2">{{ \Carbon\Carbon::parse($role->created_at)->format('d M , Y') }}</td>
                    <td class="col-md-3">
                        <div style="gap: 40px; display:flex; justify-content:center;">
                            @can('edit roles')
                                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-success px-3 py-2">Edit</a>
                            @endcan

                            @can('delete roles')
                                <a href="{{ route('roles.delete', $role->id) }}" class="btn btn-danger px-3 py-2">Delete</a>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</section>
{{ $roles->links() }}
