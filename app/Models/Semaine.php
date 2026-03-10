<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Semaine extends Model
{
    protected $fillable = ['annee', 'numero_semaine', 'date_debut', 'date_fin'];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin'   => 'date',
    ];

    public function activites()
    {
        return $this->hasMany(Activite::class);
    }

    public static function courante(): static
    {
        $now     = Carbon::now();
        $weekNum = (int) $now->format('W');
        $year    = (int) $now->format('o'); // ISO year

        $monday = $now->copy()->startOfWeek(Carbon::MONDAY);
        $sunday = $now->copy()->endOfWeek(Carbon::SUNDAY);

        return static::firstOrCreate(
            ['annee' => $year, 'numero_semaine' => $weekNum],
            ['date_debut' => $monday->format('Y-m-d'), 'date_fin' => $sunday->format('Y-m-d')]
        );
    }

    public function label(): string
    {
        return 'Semaine ' . $this->numero_semaine . ' / ' . $this->annee
            . ' – du ' . $this->date_debut->format('d/m') . ' au ' . $this->date_fin->format('d/m/Y');
    }

    public function periodeCourante(): string
    {
        return 'du ' . $this->date_debut->format('d/m') . ' au ' . $this->date_fin->format('d/m/Y');
    }

    public function periodeSuivante(): string
    {
        $nextMonday = $this->date_fin->copy()->addDay()->startOfWeek(Carbon::MONDAY);
        $nextSunday = $nextMonday->copy()->endOfWeek(Carbon::SUNDAY);
        return 'du ' . $nextMonday->format('d/m') . ' au ' . $nextSunday->format('d/m/Y');
    }

    public function previousSemaine(): ?static
    {
        return static::where('date_fin', '<', $this->date_debut)
            ->orderByDesc('date_fin')
            ->first();
    }

    public function nextSemaine(): ?static
    {
        return static::where('date_debut', '>', $this->date_fin)
            ->orderBy('date_debut')
            ->first();
    }
}
