<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExcelDataTable extends Migration
{
    public function up()
    {
        Schema::create('excel_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coordinate_id')->constrained('coordinates')->onDelete('cascade'); // Relación con la tabla coordinates
            $table->text('value'); // Valor del dato
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('excel_data');
    }
}
