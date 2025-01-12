<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coordinate extends Model
{
    use HasFactory;

    protected $fillable = ['row', 'column'];

    public function data()
    {
        return $this->hasMany(ExcelData::class);
    }
}
