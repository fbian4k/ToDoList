<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Auth::routes();



Route::middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\TaskController::class, 'index1'])->name('tasks.index1');
    Route::get('/inicio', [App\Http\Controllers\TaskController::class, 'index1'])->name('tasks.index1');
    // Route::get('/tareas/getTasks', [App\Http\Controllers\TaskController::class, 'getTasks'])->name('tasks.getTasks');
    Route::post('/tareas', [App\Http\Controllers\TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tareas/{task}', [App\Http\Controllers\TaskController::class, 'show'])->name('tasks.show'); 
    Route::put('/tareas/{task}', [App\Http\Controllers\TaskController::class, 'update'])->name('tasks.update'); 
    // Route::put('/tareas/{task}/completar', [App\Http\Controllers\TaskController::class, 'complete'])->name('tasks.complete'); 
    Route::delete('/tareas/{task}', [App\Http\Controllers\TaskController::class, 'destroy'])->name('tasks.destroy'); 
    Route::get('/tareas/{tarea}/editar', [App\Http\Controllers\TaskController::class, 'edit'])->name('tasks.edit');
    Route::post('tareas/{id}/toggle-estado', [App\Http\Controllers\TaskController::class, 'toggleStatus'])->name('tasks.toggleStatus');
});