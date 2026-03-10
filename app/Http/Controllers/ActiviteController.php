<?php

namespace App\Http\Controllers;

use App\Models\Activite;
use Illuminate\Http\Request;

class ActiviteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'employe_id'     => 'required|exists:employes,id',
            'semaine_id'     => 'required|exists:semaines,id',
            'type'           => 'required|in:courante,suivante',
            'descriptions'   => 'required|array|min:1',
            'descriptions.*' => 'required|string|max:1000',
            'statuts'        => 'nullable|array',
            'statuts.*'      => 'nullable|in:fait,en_cours,reporte,bloque,a_mettre_a_jour',
            'raisons'        => 'nullable|array',
            'raisons.*'      => 'nullable|string|max:1000',
        ]);

        $type        = $request->type;
        $descriptions = $request->descriptions;
        $statuts     = $request->statuts ?? [];
        $raisons     = $request->raisons ?? [];

        // Valider les raisons obligatoires avant de créer quoi que ce soit
        if ($type === 'courante') {
            foreach ($descriptions as $i => $description) {
                $statut = $statuts[$i] ?? null;
                $raison = $raisons[$i] ?? null;
                if (! empty($statut) && $statut !== 'fait' && empty($raison)) {
                    return back()->withErrors(['raison' => 'La précision est obligatoire si l\'activité n\'est pas terminée (ligne ' . ($i + 1) . ').'])->withInput();
                }
            }
        }

        $ordre = Activite::where('employe_id', $request->employe_id)
            ->where('semaine_id', $request->semaine_id)
            ->where('type', $type)
            ->max('ordre') + 1;

        foreach ($descriptions as $i => $description) {
            Activite::create([
                'employe_id'  => $request->employe_id,
                'semaine_id'  => $request->semaine_id,
                'type'        => $type,
                'description' => $description,
                'statut'      => $type === 'courante' ? ($statuts[$i] ?? null) : null,
                'raison'      => $type === 'courante' ? ($raisons[$i] ?? null) : null,
                'ordre'       => $ordre++,
            ]);
        }

        $count = count($descriptions);
        return redirect()->back()->with('success', $count > 1 ? "{$count} activités ajoutées." : 'Activité ajoutée avec succès.');
    }

    public function update(Request $request, Activite $activite)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:1000',
            'statut'      => 'nullable|in:fait,en_cours,reporte,bloque,a_mettre_a_jour',
            'raison'      => 'nullable|string|max:1000',
        ]);

        if (
            $activite->type === 'courante' &&
            ! empty($validated['statut']) &&
            $validated['statut'] !== 'fait' &&
            empty($validated['raison'])
        ) {
            return back()->withErrors(['raison' => 'La précision est obligatoire si l\'activité n\'est pas terminée.'])->withInput();
        }

        if ($activite->type === 'suivante') {
            $validated['statut'] = null;
            $validated['raison'] = null;
        }

        $activite->update($validated);

        return redirect()->back()->with('success', 'Activité mise à jour.');
    }

    public function updateStatut(Request $request, Activite $activite)
    {
        $validated = $request->validate([
            'statut' => 'required|in:fait,en_cours,reporte,bloque,a_mettre_a_jour',
        ]);

        // Si statut change de "non-fait" à "fait", effacer la raison
        if ($validated['statut'] === 'fait') {
            $activite->update(['statut' => 'fait', 'raison' => null]);
        } else {
            $activite->update(['statut' => $validated['statut']]);
        }

        return redirect()->back();
    }

    public function destroy(Activite $activite)
    {
        $activite->delete();
        return redirect()->back()->with('success', 'Activité supprimée.');
    }
}
