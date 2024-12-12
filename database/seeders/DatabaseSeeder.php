<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Llama al CategorySeeder
        $this->call(CategorySeeder::class);
    }
}
