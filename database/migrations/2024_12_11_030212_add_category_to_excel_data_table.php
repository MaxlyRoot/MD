<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryToExcelDataTable extends Migration
{
    public function up()
    {
        Schema::table('excel_data', function (Blueprint $table) {
            $table->string('category')->nullable()->after('value'); // Agregar columna de categoría
        });
    }

    public function down()
    {
        Schema::table('excel_data', function (Blueprint $table) {
            $table->dropColumn('category'); // Eliminar la columna en caso de rollback
        });
    }
}
