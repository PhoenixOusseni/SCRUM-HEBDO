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
        $semaineId  = $request->input('semaine_id');
        $employeId  = $request->input('employe_id');
        $periodeDebut = $request->input('periode_debut'); // semaine_id de début
        $periodeFin   = $request->input('periode_fin');   // semaine_id de fin

        // Mode : semaine unique ou plage
        $modeUnique = $semaineId !== null;

        // ── Requête de base ────────────────────────────────────────────────
        $query = Activite::query()
            ->where('type', 'courante')
            ->whereNotNull('statut');

        if ($modeUnique && $semaineId) {
            $query->where('semaine_id', $semaineId);
        } elseif ($periodeDebut && $periodeFin) {
            $query->whereBetween('semaine_id', [(int)$periodeDebut, (int)$periodeFin]);
        } elseif ($periodeDebut) {
            $query->where('semaine_id', '>=', (int)$periodeDebut);
        } elseif ($periodeFin) {
            $query->where('semaine_id', '<=', (int)$periodeFin);
        } else {
            // Par défaut : semaine courante
            $semaineCourante = Semaine::courante();
            $semaineId = $semaineCourante->id;
            $query->where('semaine_id', $semaineId);
            $modeUnique = true;
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
