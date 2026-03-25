<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SituacionCompetencia extends Model
{
    protected $table = 'situaciones_competencia';

    protected $fillable = [
        'ecosistema_laboral_id', 'codigo', 'titulo', 'descripcion', 'umbral_maestria', 'nivel_complejidad', 'activa'
    ];

    //Relación con EcosistemaLaboral (belongsTo)
    public function ecosistemaLaboral (): BelongsTo
    {
        return $this->belongsTo(EcosistemaLaboral::class);
    }

    //Relación con NodoRequisito (hasMany)

    public function nodoRequisito (): HasMany
    {
        return $this->hasMany(NodoRequisito::class);
    }

    //Relación de prerequisitos (belongsToMany a sí mismo)
    public function situacionesCompetenciaPrerequisitos(): BelongsToMany
    {
        return $this->belongsToMany(
            SituacionCompetencia::class, // Tabla a la que va
            'situaciones_competencia_prerequisitos', // Tabla intermedia
            'situacion_competencia_id', // FK en la clave intermedia
            'prerequisito_id' // FK en la clave intermedia
        );
    }

    //Relación de dependientes (belongsToMany a sí mismo)
    public function situacionesCompetenciaDependientes(): BelongsToMany
    {
        return $this->belongsToMany(
            SituacionCompetencia::class,
            'situaciones_competencia_dependientes',
            'situacion_competencia_id',
            'dependiente_id'
        );
    }

    //Relación con CriterioEvaluacion (belongsToMany)
    public function criterioEvaluacion(): BelongsToMany
    {
        return $this->belongsToMany(
            CriterioEvaluacion::class,
            'sc_criterios_evaluacion',
            'situacion_competencia_id',
            'criterio_evaluacion_id'
        )->withPivot('peso_en_sc');
    }

    //Relación con PerfilesHabilitacion a través de PerfilSituacion (belongsToMany)
    public function perfilesHabilitacion(): BelongsToMany
    {
        return $this->belongsToMany(
            PerfilHabilitacion::class,
            'perfil_situacion',
            'situacion_competencia_id',
            'perfil_habilitacion_id'
        );
    }

    // Cast
    protected function casts(): array
    {
        return [
            'umbral_maestria' => 'decimal',
            'activa' => 'boolean'
        ];
    }

}
