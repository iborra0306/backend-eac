<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Factories\HasFactory;

class FamiliaProfesional extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'codigo', 'descripcion'];
    protected $table = 'familias_profesionales';

    public function ciclosFormativos(): HasMany
    {
        return $this->hasMany(CicloFormativo::class);
    }
}
