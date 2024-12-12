<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoordinatesTable extends Migration
{
    public function up()
    {
        Schema::create('coordinates', function (Blueprint $table) {
            $table->id();
            $table->integer('row'); // Número de fila
            $table->integer('column'); // Número de columna
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('coordinates');
    }
}
