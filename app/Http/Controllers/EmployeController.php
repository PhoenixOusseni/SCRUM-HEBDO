<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use Illuminate\Http\Request;

class EmployeController extends Controller
{
    public function index()
    {
        $employes = Employe::orderBy('ordre')->get();
        return view('employes.index', compact('employes'));
    }

    public function create()
    {
        return view('employes.form', ['employe' => new Employe(), 'mode' => 'create']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'   => 'required|string|max:100',
            'poste' => 'nullable|string|max:100',
            'ordre' => 'nullable|integer|min:0',
        ]);

        $validated['actif'] = true;
        $validated['ordre'] = $validated['ordre'] ?? (Employe::max('ordre') + 1);

        Employe::create($validated);

        return redirect()->route('employes.index')->with('success', 'Employé ajouté avec succès.');
    }

    public function edit(Employe $employe)
    {
        return view('employes.form', ['employe' => $employe, 'mode' => 'edit']);
    }

    public function update(Request $request, Employe $employe)
    {
        $validated = $request->validate([
            'nom'   => 'required|string|max:100',
            'poste' => 'nullable|string|max:100',
            'ordre' => 'nullable|integer|min:0',
        ]);

        $validated['actif'] = $request->boolean('actif', false);

        $employe->update($validated);

        return redirect()->route('employes.index')->with('success', 'Employé mis à jour.');
    }

    public function destroy(Employe $employe)
    {
        $employe->delete();
        return redirect()->route('employes.index')->with('success', 'Employé supprimé.');
    }
}
