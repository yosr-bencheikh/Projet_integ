<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Student;
use App\Models\Enseignant;
use App\Models\Soutenance;
use Illuminate\Http\Request;

class SoutenanceController extends Controller
{
    public function showPFE()
    {
        $soutenances = Soutenance::where('type', 'PFE')->get();
        $teachers = Enseignant::all(); // Fetch PFE records
        $classes = Classe::all();
        $students = Student::all();

        return view('stageDePFEInterface', compact('soutenances', 'teachers', 'classes', 'students')); // Pass data to the view
    }


    public function showInitiation()
    {
        $soutenances = Soutenance::where('type', 'initiation')->get();
        return view('stageDinitationInterface', compact('soutenances'));
    }

    public function showPerfectionnement()
    {
        $soutenances = Soutenance::where('type', 'perfectionnement')->get();
        return view('stageDePerfectionnemantInterface', compact('soutenances'));
    }
    public function destroy($id)
    {
        try {
            // Find the soutenance by ID
            $soutenance = Soutenance::findOrFail($id);

            // Delete the soutenance
            $soutenance->delete();

            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'Soutenance deleted successfully!'
            ]);
        } catch (\Exception $e) {
            // Log the error and return a failure response
            Log::error('Error deleting soutenance: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting soutenance.'
            ]);
        }
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'etudiant' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = Soutenance::where('etudiant', $value)
                        ->where('date_soutenance', $request->date_soutenance)
                        ->where('heure', $request->heure)
                        ->exists();
                    if ($exists) {
                        $fail('L\'étudiant ne peut pas participer à deux soutenances à la même heure.');
                    }
                },
            ],
            'encadrant' => [
                'nullable',
                'string',
                'max:255',
                'different:jury1',
                'different:jury2',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value) {
                        $encadrantCount = Soutenance::where('encadrant', $value)->count();
                        if ($encadrantCount >= 5) {
                            $fail('L\'encadrant ne peut pas encadrer plus de 5 soutenances.');
                        }
                        $exists = Soutenance::where('encadrant', $value)
                            ->where('date_soutenance', $request->date_soutenance)
                            ->where('heure', $request->heure)
                            ->exists();
                        if ($exists) {
                            $fail('L\'encadrant ne peut pas participer à deux soutenances à la même heure.');
                        }
                    }
                },
            ],
            'jury1' => [
                'required',
                'string',
                'max:255',
                'different:jury2',
                function ($attribute, $value, $fail) use ($request) {
                    $jury1Count = Soutenance::where('jury1', $value)->orWhere('jury2', $value)->count();
                    if ($jury1Count >= 4) {
                        $fail('Jury 1 ne peut pas superviser plus de 4 soutenances.');
                    }
                    $exists = Soutenance::where(function ($query) use ($value) {
                        $query->where('jury1', $value)->orWhere('jury2', $value);
                    })
                        ->where('date_soutenance', $request->date_soutenance)
                        ->where('heure', $request->heure)
                        ->exists();
                    if ($exists) {
                        $fail('Jury 1 ne peut pas participer à deux soutenances à la même heure.');
                    }
                },
            ],
            'jury2' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    $jury2Count = Soutenance::where('jury1', $value)->orWhere('jury2', $value)->count();
                    if ($jury2Count >= 4) {
                        $fail('Jury 2 ne peut pas superviser plus de 4 soutenances.');
                    }
                    $exists = Soutenance::where(function ($query) use ($value) {
                        $query->where('jury1', $value)->orWhere('jury2', $value);
                    })
                        ->where('date_soutenance', $request->date_soutenance)
                        ->where('heure', $request->heure)
                        ->exists();
                    if ($exists) {
                        $fail('Jury 2 ne peut pas participer à deux soutenances à la même heure.');
                    }
                },
            ],
            'salle' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = Soutenance::where('salle', $value)
                        ->where('date_soutenance', $request->date_soutenance)
                        ->where('heure', $request->heure)
                        ->exists();
                    if ($exists) {
                        $fail('La salle est déjà réservée pour une autre soutenance à la même heure.');
                    }
                },
            ],
            'societe' => 'nullable|string|max:255',
            'type' => 'required|string',
            'date_debut' => 'nullable|date|before_or_equal:date_fin',
            'date_fin' => 'nullable|date',
            'classe' => 'nullable|string|max:255',
            'heure' => [
                'nullable',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    if ($value < '08:00' || $value > '18:00') {
                        $fail('L\'heure doit être comprise entre 08:00 et 18:00.');
                    }
                },
            ],
            'date_soutenance' => 'required|date|after_or_equal:today',
        ], [
            // Messages personnalisés
            'encadrant.different' => 'L\'encadrant doit être différent des jurys.',
            'jury1.different' => 'Jury 1 et Jury 2 doivent être différents.',
            'date_debut.before_or_equal' => 'La date de début doit être antérieure ou égale à la date de fin.',
            'date_soutenance.after_or_equal' => 'La date de soutenance doit être aujourd\'hui ou dans le futur.',
        ]);

        // Création de la soutenance si la validation est réussie
        Soutenance::create($validated);

        return back()->with('success', 'Soutenance ajoutée avec succès!');
    }
}
