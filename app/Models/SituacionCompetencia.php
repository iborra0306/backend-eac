<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Factories\HasFactory;

class SituacionCompetencia extends Model
{
    use HasFactory;

    protected $table = 'situaciones_competencia';

    protected $fillable = [
        'ecosistema_laboral_id', 'codigo', 'titulo',
        'descripcion', 'umbral_maestria', 'nivel_complejidad', 'activa'
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

    // SCs que deben estar conquistadas ANTES de acceder a esta SC
    public function prerequisitos(): BelongsToMany
    {
        return $this->belongsToMany(
            SituacionCompetencia::class,
            'sc_precedencia',
            'sc_id',           // esta SC
            'sc_requisito_id'  // sus prerequisitos
        );
    }

    // SCs que requieren esta SC como prerequisito
    public function dependientes(): BelongsToMany
    {
        return $this->belongsToMany(
            SituacionCompetencia::class,
            'sc_precedencia',
            'sc_requisito_id', // esta SC es el requisito
            'sc_id'            // las SCs que la necesitan
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

    public function criteriosEvaluacion(): BelongsToMany
    {
        return $this->belongsToMany(
            CriterioEvaluacion::class,
            'sc_criterios_evaluacion',
            'situacion_competencia_id',
            'criterio_evaluacion_id'
        )->withPivot('peso_en_sc');
    }

    public function perfilesHabilitacion(): HasMany
    {
        return $this->hasMany(PerfilSituacion::class, 'situacion_competencia_id');
    }

    // Cast
    protected function casts(): array
    {
        return [
            'umbral_maestria' => 'decimal:2',
            'activa' => 'boolean'
        ];
    }

}
