<?php

namespace App\Http\Controllers;

use App\Models\Activite;
use App\Models\Employe;
use App\Models\Semaine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistiqueController extends Controller
{
    public function index(Request $request)
    {
        $semaines = Semaine::orderByDesc('date_debut')->get();
        $employes = Employe::where('actif', true)->orderBy('ordre')->get();

        // Filtres
        $mode         = $request->input('mode'); // 'unique' | 'plage' | null (premier chargement)
        $semaineId    = $request->input('semaine_id');
        $employeId    = $request->input('employe_id');
        $periodeDebut = $request->input('periode_debut'); // semaine_id de début
        $periodeFin   = $request->input('periode_fin');   // semaine_id de fin

        // Mode : semaine unique ou plage (piloté par le radio button)
        $modeUnique = ($mode !== 'plage');

        // ── Requête de base ────────────────────────────────────────────────
        $query = Activite::query()
            ->where('type', 'courante')
            ->whereNotNull('statut');

        if ($modeUnique) {
            if ($semaineId) {
                $query->where('semaine_id', $semaineId);
            } else {
                // Par défaut : semaine courante
                $semaineCourante = Semaine::courante();
                $semaineId = $semaineCourante->id;
                $query->where('semaine_id', $semaineId);
            }
        } else {
            // Mode plage : filtrer par dates pour éviter les problèmes d'IDs non-séquentiels
            if ($periodeDebut && $periodeFin) {
                $debut = Semaine::find((int)$periodeDebut)?->date_debut;
                $fin   = Semaine::find((int)$periodeFin)?->date_fin;
                if ($debut && $fin) {
                    $query->whereHas('semaine', fn($q) => $q->whereBetween('date_debut', [$debut, $fin]));
                } else {
                    $query->whereIn('semaine_id', Semaine::whereBetween('id', [(int)$periodeDebut, (int)$periodeFin])->pluck('id'));
                }
            } elseif ($periodeDebut) {
                $debut = Semaine::find((int)$periodeDebut)?->date_debut;
                if ($debut) {
                    $query->whereHas('semaine', fn($q) => $q->where('date_debut', '>=', $debut));
                }
            } elseif ($periodeFin) {
                $fin = Semaine::find((int)$periodeFin)?->date_fin;
                if ($fin) {
                    $query->whereHas('semaine', fn($q) => $q->where('date_fin', '<=', $fin));
                }
            }
            // Si aucune plage définie, on retourne toutes les activités (pas de filtre semaine)
        }

        if ($employeId) {
            $query->where('employe_id', $employeId);
        }

        $activites = $query->get();

        // ── Stats par employé ──────────────────────────────────────────────
        $statsParEmploye = $employes->map(function ($employe) use ($activites) {
            $acts = $activites->where('employe_id', $employe->id);

            $total = $acts->count();
            $parStatut = [];
            foreach (Activite::STATUTS as $key => $label) {
                $parStatut[$key] = $acts->where('statut', $key)->count();
            }

            $faites = $parStatut['fait'] ?? 0;
            $pct    = $total > 0 ? round($faites / $total * 100, 1) : null;

            return [
                'employe'    => $employe,
                'total'      => $total,
                'par_statut' => $parStatut,
                'fait'       => $faites,
                'pct'        => $pct,
            ];
        })->filter(fn($s) => $s['total'] > 0)->values();

        // ── Totaux globaux ─────────────────────────────────────────────────
        $totalGlobal = $activites->count();
        $faitGlobal  = $activites->where('statut', 'fait')->count();
        $pctGlobal   = $totalGlobal > 0 ? round($faitGlobal / $totalGlobal * 100, 1) : null;

        $totauxParStatut = [];
        foreach (Activite::STATUTS as $key => $label) {
            $totauxParStatut[$key] = $activites->where('statut', $key)->count();
        }

        // ── Évolution par semaine (pour le graphique courbe) ──────────────
        $evolution = [];
        if (!$modeUnique) {
            $semainesIds = $activites->pluck('semaine_id')->unique()->sort()->values();
            foreach ($semainesIds as $sid) {
                $semaineObj = $semaines->find($sid);
                $actsS = $activites->where('semaine_id', $sid);
                $tot   = $actsS->count();
                $fait  = $actsS->where('statut', 'fait')->count();
                $evolution[] = [
                    'label' => $semaineObj ? 'S'.$semaineObj->numero_semaine.'/'.$semaineObj->annee : '#'.$sid,
                    'pct'   => $tot > 0 ? round($fait / $tot * 100, 1) : 0,
                    'total' => $tot,
                    'fait'  => $fait,
                ];
            }
        }

        $semaineCourante = Semaine::courante();

        return view('statistiques.index', compact(
            'semaines', 'employes',
            'statsParEmploye', 'totalGlobal', 'faitGlobal', 'pctGlobal',
            'totauxParStatut', 'evolution',
            'semaineId', 'employeId', 'periodeDebut', 'periodeFin', 'modeUnique',
            'semaineCourante'
        ));
    }
}
