<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelController;
use L5Swagger\Http\Controllers\SwaggerController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', function () {
    return response()->json(['message' => 'Ruta de prueba funcionando']);
});

// Endpoints para la gestión de datos Excel
Route::post('/import-reports', [ExcelController::class, 'importReports']); // Subir archivo Excel
Route::get('/excel-data', [ExcelController::class, 'getExcelData']); // Obtener todos los datos
Route::put('/excel-data/{id}', [ExcelController::class, 'updateData']); // Actualizar dato específico
Route::delete('/excel-data/{id}', [ExcelController::class, 'deleteData']); // Eliminar dato específico
Route::get('/categories', [ExcelController::class, 'getCategories']); // Obtener categorías
Route::get('/highlight-cell/{coordinateId}', [ExcelController::class, 'highlightCell']); // Resaltar celda en Excel
Route::get('/download-original', [ExcelController::class, 'getOriginalExcel']); // Descargar archivo original

Route::get('/csrf-test', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});



Route::get('/api/documentation', function () {
    return view('l5-swagger::index');
});