<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcelData extends Model
{
    use HasFactory;

    protected $fillable = ['coordinate_id', 'value', 'category']; // Incluir 'category'


    public function coordinate()
    {
        return $this->belongsTo(Coordinate::class);
    }
}
