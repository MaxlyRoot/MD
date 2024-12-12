<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\CategoryController;

// Página de bienvenida
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Dashboard con middleware de autenticación
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas protegidas por middleware de autenticación
Route::middleware('auth')->group(function () {
    // Rutas de perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas relacionadas con el manejo de archivos Excel
Route::middleware('auth')->group(function () {
    Route::post('/import-reports', [ExcelController::class, 'importReports']); // Subir archivo Excel
    

Route::get('/excel-data', [ExcelController::class, 'getExcelData']);
// Obtener todos los datos
    Route::put('/excel-data/{id}', [ExcelController::class, 'updateData']); // Actualizar dato
    Route::delete('/excel-data/{id}', [ExcelController::class, 'deleteData']); // Eliminar dato
    Route::get('/categories', [ExcelController::class, 'getCategories']); // Obtener categorías
    Route::get('/highlight-cell/{coordinateId}', [ExcelController::class, 'highlightCell']); // Resaltar celda en Excel
    Route::get('/download-original', [ExcelController::class, 'getOriginalExcel']); // Descargar archivo original
});

Route::get('/test', function () {
    return response()->json(['message' => 'Ruta de prueba funcionando']);
});

// Ruta de prueba para obtener el token CSRF
Route::get('/csrf-test', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

// Rutas de autenticación generadas automáticamente
require __DIR__.'/auth.php';
