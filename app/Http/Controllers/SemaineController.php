<?php

namespace App\Http\Controllers;

use App\Models\Activite;
use App\Models\Employe;
use App\Models\Semaine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SemaineController extends Controller
{
    public function index()
    {
        $semaines = Semaine::orderByDesc('date_debut')->get();
        return view('canevas.index', compact('semaines'));
    }

    public function redirectCourante()
    {
        $semaine = Semaine::courante();
        return redirect()->route('semaines.show', $semaine);
    }

    public function show(Semaine $semaine)
    {
        $employes = Employe::where('actif', true)->orderBy('ordre')->get();

        $data = $employes->map(function ($employe) use ($semaine) {
            $courantes = Activite::where('employe_id', $employe->id)
                ->where('semaine_id', $semaine->id)
                ->where('type', 'courante')
                ->orderBy('ordre')
                ->get();

            $suivantes = Activite::where('employe_id', $employe->id)
                ->where('semaine_id', $semaine->id)
                ->where('type', 'suivante')
                ->orderBy('ordre')
                ->get();

            $pivot = DB::table('employe_semaine')
                ->where('employe_id', $employe->id)
                ->where('semaine_id', $semaine->id)
                ->first();

            $maxRows = max($courantes->count(), $suivantes->count(), 1);

            return [
                'employe'   => $employe,
                'courantes' => $courantes,
                'suivantes' => $suivantes,
                'obstacles' => $pivot?->obstacles,
                'maxRows'   => $maxRows,
            ];
        });

        $previousSemaine = $semaine->previousSemaine();
        $nextSemaine     = $semaine->nextSemaine();

        return view('canevas.show', compact('semaine', 'data', 'previousSemaine', 'nextSemaine'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_debut' => 'required|date',
        ]);

        $dateDebut = Carbon::parse($validated['date_debut'])->startOfWeek(Carbon::MONDAY);
        $dateFin   = $dateDebut->copy()->endOfWeek(Carbon::SUNDAY);
        $weekNum   = (int) $dateDebut->format('W');
        $year      = (int) $dateDebut->format('o');

        $semaine = Semaine::firstOrCreate(
            ['annee' => $year, 'numero_semaine' => $weekNum],
            ['date_debut' => $dateDebut->format('Y-m-d'), 'date_fin' => $dateFin->format('Y-m-d')]
        );

        return redirect()->route('semaines.show', $semaine)
            ->with('success', 'Semaine créée avec succès.');
    }

    public function creerSuivante(Semaine $semaine)
    {
        $nextMonday = $semaine->date_fin->copy()->addDay()->startOfWeek(Carbon::MONDAY);
        $nextSunday = $nextMonday->copy()->endOfWeek(Carbon::SUNDAY);
        $weekNum    = (int) $nextMonday->format('W');
        $year       = (int) $nextMonday->format('o');

        $nextSemaine = Semaine::firstOrCreate(
            ['annee' => $year, 'numero_semaine' => $weekNum],
            ['date_debut' => $nextMonday->format('Y-m-d'), 'date_fin' => $nextSunday->format('Y-m-d')]
        );

        // Copier les activités "suivante" comme "courante" de la semaine suivante
        $suivantes = Activite::where('semaine_id', $semaine->id)
            ->where('type', 'suivante')
            ->orderBy('employe_id')
            ->orderBy('ordre')
            ->get();

        foreach ($suivantes as $act) {
            $exists = Activite::where('employe_id', $act->employe_id)
                ->where('semaine_id', $nextSemaine->id)
                ->where('type', 'courante')
                ->where('description', $act->description)
                ->exists();

            if (! $exists) {
                Activite::create([
                    'employe_id'  => $act->employe_id,
                    'semaine_id'  => $nextSemaine->id,
                    'type'        => 'courante',
                    'description' => $act->description,
                    'statut'      => null,
                    'raison'      => null,
                    'ordre'       => $act->ordre,
                ]);
            }
        }

        return redirect()->route('semaines.show', $nextSemaine)
            ->with('success', 'Semaine suivante créée avec ' . $suivantes->count() . ' activité(s) planifiée(s) reportée(s).');
    }

    public function updateObstacles(Request $request, Semaine $semaine)
    {
        $validated = $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'obstacles'  => 'nullable|string|max:1000',
        ]);

        DB::table('employe_semaine')->updateOrInsert(
            ['employe_id' => $validated['employe_id'], 'semaine_id' => $semaine->id],
            ['obstacles' => $validated['obstacles'], 'updated_at' => now(), 'created_at' => now()]
        );

        return redirect()->back()->with('success', 'Obstacles mis à jour.');
    }
}
