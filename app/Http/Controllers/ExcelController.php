<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coordinate;
use App\Models\ExcelData;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Category;

/**
 * @OA\Info(
 *     title="API de Gestión de Excel",
 *     version="1.0",
 *     description="API para la gestión de datos de Excel, incluyendo importación, edición, eliminación y consulta."
 * )
 * @OA\Tag(name="Excel", description="Operaciones relacionadas con la gestión de Excel")
 */
class ExcelController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/import-reports",
     *     tags={"Excel"},
     *     summary="Importar un archivo Excel",
     *     description="Sube e importa un archivo Excel para almacenar datos en la base de datos.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="file",
     *                     type="string",
     *                     format="binary",
     *                     description="Archivo Excel a importar"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Archivo procesado con éxito"),
     *     @OA\Response(response=400, description="Error de validación"),
     *     @OA\Response(response=500, description="Error interno del servidor")
     * )
     */
    public function importReports(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('file');
            $data = Excel::toArray([], $file);

            $importedData = [];
            foreach ($data[0] as $rowIndex => $row) {
                foreach ($row as $columnIndex => $value) {
                    if (!is_null($value) && trim($value) !== '') {
                        $coordinate = Coordinate::create([
                            'row' => $rowIndex + 1,
                            'column' => $columnIndex + 1,
                        ]);

                        $excelData = ExcelData::create([
                            'coordinate_id' => $coordinate->id,
                            'value' => $value,
                        ]);

                        $importedData[] = $excelData->load('coordinate');
                    }
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'Archivo procesado con éxito.',
                'data' => $importedData
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al procesar el archivo: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/excel-data/{id}",
     *     tags={"Excel"},
     *     summary="Actualizar un dato",
     *     description="Actualiza un dato específico almacenado en la base de datos.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del dato a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="value", type="string", description="Nuevo valor del dato"),
     *             @OA\Property(property="category", type="string", description="Nueva categoría")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Dato actualizado con éxito"),
     *     @OA\Response(response=404, description="Dato no encontrado"),
     *     @OA\Response(response=500, description="Error interno del servidor")
     * )
     */
    public function updateData(Request $request, $id)
    {
        $request->validate([
            'value' => 'required|string',
            'category' => 'nullable|string',
        ]);

        try {
            $data = ExcelData::findOrFail($id);
            $data->value = $request->input('value');
            $data->category = $request->input('category');
            $data->save();

            return response()->json(['message' => 'Dato actualizado con éxito.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar el dato: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/excel-data/{id}",
     *     tags={"Excel"},
     *     summary="Eliminar un dato",
     *     description="Elimina un dato específico de la base de datos.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del dato a eliminar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Dato eliminado con éxito"),
     *     @OA\Response(response=404, description="Dato no encontrado"),
     *     @OA\Response(response=500, description="Error interno del servidor")
     * )
     */
    public function deleteData($id)
    {
        try {
            $data = ExcelData::findOrFail($id);
            $data->delete();
            return response()->json(['message' => 'Dato eliminado con éxito.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el dato: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/excel-data",
     *     tags={"Excel"},
     *     summary="Obtener todos los datos",
     *     description="Retorna todos los datos almacenados junto con sus coordenadas asociadas.",
     *     @OA\Response(response=200, description="Datos obtenidos con éxito"),
     *     @OA\Response(response=500, description="Error interno del servidor")
     * )
     */
    public function getExcelData()
    {
        return response()->json(ExcelData::with('coordinate')->get());
    }

    /**
     * @OA\Get(
     *     path="/api/highlight-cell/{coordinateId}",
     *     tags={"Excel"},
     *     summary="Resaltar celda en Excel",
     *     description="Genera un archivo Excel con una celda específica resaltada.",
     *     @OA\Parameter(
     *         name="coordinateId",
     *         in="path",
     *         required=true,
     *         description="ID de la coordenada",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Archivo generado con éxito"),
     *     @OA\Response(response=404, description="Dato o archivo no encontrado"),
     *     @OA\Response(response=500, description="Error interno del servidor")
     * )
     */
    public function highlightCell($coordinateId)
    {
        // Implementación existente...
    }

    /**
     * @OA\Get(
     *     path="/api/download-original",
     *     tags={"Excel"},
     *     summary="Descargar archivo original",
     *     description="Permite descargar el archivo Excel original.",
     *     @OA\Response(response=200, description="Archivo descargado con éxito"),
     *     @OA\Response(response=404, description="Archivo no encontrado"),
     *     @OA\Response(response=500, description="Error interno del servidor")
     * )
     */
    public function getOriginalExcel()
    {
        $filePath = storage_path('app/public/limpio.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'limpio.xlsx');
        }

        return response()->json(['error' => 'Archivo no encontrado.'], 404);
    }

    /**
     * @OA\Get(
     *     path="/api/categories",
     *     tags={"Categorías"},
     *     summary="Obtener categorías",
     *     description="Obtiene todas las categorías disponibles.",
     *     @OA\Response(response=200, description="Categorías obtenidas con éxito"),
     *     @OA\Response(response=500, description="Error interno del servidor")
     * )
     */
    public function getCategories()
    {
        return response()->json(Category::all());
    }
}
