<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        // Devuelve una lista de categorías
        return response()->json([
            ['id' => 1, 'name' => 'Categoría A'],
            ['id' => 2, 'name' => 'Categoría B'],
            ['id' => 3, 'name' => 'Categoría C'],
        ]);
    }
}
