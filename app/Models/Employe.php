<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Employe extends Model
{
    protected $fillable = ['nom', 'poste', 'ordre', 'actif'];

    protected $casts = ['actif' => 'boolean'];

    public function activites()
    {
        return $this->hasMany(Activite::class);
    }

    public function activitesSemaine(int $semaineId, string $type)
    {
        return $this->activites()
            ->where('semaine_id', $semaineId)
            ->where('type', $type)
            ->orderBy('ordre')
            ->get();
    }

    public function obstaclesSemaine(int $semaineId): ?string
    {
        $pivot = DB::table('employe_semaine')
            ->where('employe_id', $this->id)
            ->where('semaine_id', $semaineId)
            ->first();

        return $pivot?->obstacles;
    }
}
