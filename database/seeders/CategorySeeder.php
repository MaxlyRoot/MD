<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        Category::insert([
            ['name' => 'Categoría 1'],
            ['name' => 'Categoría 2'],
            ['name' => 'Categoría 3'],
        ]);
    }
}
