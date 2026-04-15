<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $fillable = ['nombre', 'provincia_id', 'activo'];

    public function provincia()
    {
        return $this->belongsTo(Provincia::class);
    }

    public function remesas()
    {
        return $this->hasMany(Remesa::class);
    }
}