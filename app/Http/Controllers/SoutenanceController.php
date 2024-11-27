<?php

namespace App\Http\Controllers;

use App\Models\Soutenance;
use Illuminate\Http\Request;

class SoutenanceController extends Controller
{
    public function index()
    {
        $soutenances = Soutenance::all();
        return view('stageDinitationInterface', compact('soutenances'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|string|max:255',
            'jury1' => 'required|string|max:255',
            'jury2' => 'required|string|max:255',
            'societe' => 'required|string|max:255',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date',
            'classe' => 'required|string|max:255',
            'heure' => 'required|date_format:H:i',
            'date_soutenance' => 'required|date',
        ]);

        Soutenance::create($request->all());
        return redirect()->route('stageDinitationInterface')->with('success', 'Soutenance ajoutée avec succès');
    }

    public function edit($id)
    {
        $soutenance = Soutenance::findOrFail($id);
        return view('soutenances.edit', compact('soutenance'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'etudiant_id' => 'required|string|max:255',
            'jury1' => 'required|string|max:255',
            'jury2' => 'required|string|max:255',
            'societe' => 'required|string|max:255',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date',
            'classe' => 'required|string|max:255',
            'heure' => 'required|date_format:H:i',
            'date_soutenance' => 'required|date',
        ]);

        $soutenance = Soutenance::findOrFail($id);
        $soutenance->update($request->all());
        return redirect()->route('soutenances.index')->with('success', 'Soutenance mise à jour avec succès');
    }

    public function destroy($id)
    {
        $soutenance = Soutenance::findOrFail($id);
        $soutenance->delete();
        return redirect()->route('soutenances.index')->with('success', 'Soutenance supprimée avec succès');
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $soutenances = Soutenance::where('etudiant', 'like', '%' . $searchTerm . '%')->get();
        return view('soutenances.index', compact('soutenances'));
    }
}
