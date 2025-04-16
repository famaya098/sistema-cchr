<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('permiso.index');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas para el formulario de permiso
Route::get('/permiso', [App\Http\Controllers\PermisoController::class, 'index'])->name('permiso.index');
//Route::post('/permiso/generar-pdf', [App\Http\Controllers\PermisoController::class, 'generarPDF'])->name('permiso.pdf');
Route::post('/permiso/generar-pdf', [App\Http\Controllers\PermisoController::class, 'generarPDF'])->name('permiso.pdf');
require __DIR__.'/auth.php';
