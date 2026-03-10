<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activite extends Model
{
    protected $fillable = [
        'employe_id', 'semaine_id', 'type',
        'description', 'statut', 'raison', 'ordre',
    ];

    const STATUTS = [
        'fait'          => 'Fait',
        'en_cours'      => 'En cours',
        'reporte'       => 'Reporté',
        'bloque'        => 'Bloqué',
        'a_mettre_a_jour' => 'A mettre à jour',
    ];

    const STATUT_COLORS = [
        'fait'            => '#1e8449',
        'en_cours'        => '#e67e22',
        'reporte'         => '#d4ac0d',
        'bloque'          => '#2980b9',
        'a_mettre_a_jour' => '#7f8c8d',
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    public function semaine()
    {
        return $this->belongsTo(Semaine::class);
    }

    public function statutLabel(): string
    {
        return self::STATUTS[$this->statut] ?? '–';
    }

    public function statutColor(): string
    {
        return self::STATUT_COLORS[$this->statut] ?? '#6c757d';
    }

    public function necessiteRaison(): bool
    {
        return $this->statut !== null && $this->statut !== 'fait';
    }
}
