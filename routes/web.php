<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BreakController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'guest'], function () {
    Route::get('/', [AuthController::class, 'login_form'])->name('login');
    Route::post('/', [AuthController::class, 'login'])->name('login.submit');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [AuthController::class, 'home'])->name('dashboard');

    //########## Permission Module ##########//
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permissions/{id}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::post('/permissions/{id}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::get('/permissions/{id}', [PermissionController::class, 'delete'])->name('permissions.delete');

    //########## Role Module ##########//
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::post('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::get('/roles/{id}', [RoleController::class, 'delete'])->name('roles.delete');

    //########## Projects Module ##########//
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/trash', [ProjectController::class, 'trash'])->name('projects.trash');
    Route::get('/projects/restore/{id}', [ProjectController::class, 'restore'])->name('projects.restore');
    Route::get('/projects/{id}/delete', [ProjectController::class, 'permanetDelete'])->name('projects.permanetdelete');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{id}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::post('/projects/{id}', [ProjectController::class, 'update'])->name('projects.update');
    Route::get('/projects/{id}', [ProjectController::class, 'delete'])->name('projects.delete');
    Route::get('/projects/detail/{id}', [ProjectController::class, 'detail'])->name('projects.detail');


    //########## Users Module ##########//
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::get('/users/{id}', [UserController::class, 'delete'])->name('users.delete');

    //########## Task Module ##########//
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/trash', [TaskController::class, 'trash'])->name('tasks.trash');
    Route::get('/projects/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{id}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::post('/tasks/{id}', [TaskController::class, 'update'])->name('tasks.update');
    Route::get('/tasks/{id}', [TaskController::class, 'delete'])->name('tasks.delete');
    Route::get('/tasks/restore/{id}', [TaskController::class, 'restore'])->name('tasks.restore');
    Route::get('/tasks/{id}/delete', [TaskController::class, 'permanetDelete'])->name('tasks.permanetdelete');
    Route::post('/saveHours', [TaskController::class, 'saveTaskTimer'])->name('tasks.saveHours');

    Route::get('/filter-projects', [ProjectController::class, 'filterProjects'])->name('filter.projects');
    Route::get('//filter-tasks', [TaskController::class, 'filterTasks'])->name('filter.tasks');

    // Client Module
    Route::resource('clients', ClientController::class);


    // Management Module
    Route::post('/break/start', [BreakController::class, 'startBreak'])->name('break.start');
    Route::post('/break/end/', [BreakController::class, 'endBreak'])->name('break.end');


    //########## User Logout Module ##########//
    Route::get('/logout', [AuthController::class, 'logout'])->name('users.logout');
});
