<!doctype html>
<html lang="en">

<head>
    <title>Title</title>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    {{-- Link the bootstrap.css Fle --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap.css') }}">
    {{-- Link the bootstrap.min.css Fle --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap.min.css') }}">
    {{-- Link the fontawesome all.css Fle --}}
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.css') }}">
</head>

<body>
    <main class="h-100">
        <div class="content">
            @php
                $current_page = basename($_SERVER['PHP_SELF']);
            @endphp

            <div class="dashboard-container">
                <div class="sidebar">

                    <ul class="tab-list">

                        <li class="tab"><a class="{{ $current_page == 'dashboard' ? 'active' : '' }}" id="dashboard"
                                href="{{ url('/dashboard') }}">
                                <i class="fa-solid fa-house-chimney"></i></a>
                        </li>

                        @can('view users')
                            <li class="tab"><a class="{{ $current_page == 'users' ? 'active' : '' }}" id="all-users"
                                    href="{{ route('users.index') }}">
                                    <i class="fa-solid fa-users"></i></a>
                            </li>
                        @endcan

                        @can('view projects')
                            <li class="tab"><a class="{{ $current_page == 'projects' ? 'active' : '' }}"
                                    id="all-projects" href="{{ route('projects.index') }}">
                                    <i class="fa-solid fa-book"></i></a>
                            </li>
                        @endcan

                        @can('view projects')
                            <li class="tab profile" data-value=""><a
                                    class="{{ $current_page == 'tasks' ? 'active' : '' }}" id="profile"
                                    href="{{ route('tasks.index') }}">
                                    <i class="fa fa-tasks"></i></a>
                            </li>
                        @endcan

                        @can('view roles')
                            <li class="tab"><a class="{{ $current_page == 'roles' ? 'active' : '' }}" id="all-projects"
                                    href="{{ route('roles.index') }}">
                                    <i class="fas fa-user-shield"></i></a>
                            </li>
                        @endcan

                        @can('view permissions')
                            <li class="tab"><a class="{{ $current_page == 'permissions' ? 'active' : '' }}"
                                    id="dashboard" href="{{ route('permissions.index') }}">
                                    <i class="fas fa-user-lock"></i></a>
                            </li>
                        @endcan


                        <li class="tab"><a class="{{ $current_page == 'usersetting' ? 'active' : '' }}"
                                id="userSettings" href="{{ url('/admin/usersetting') }}">
                                <i class="fa-solid fa-gear"></i></a>
                        </li>

                        <li class="tab last"><a href="{{ route('users.logout') }}"
                                class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"><i
                                    class="fa-solid fa-power-off"></i></a>
                        </li>

                    </ul>
                </div>
