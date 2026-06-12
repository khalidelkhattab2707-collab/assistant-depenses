<?php

namespace App\Http\Controllers;

use App\Enums\CategorieDepense;
use App\Models\Depense;
use Illuminate\Http\Request;

class DepenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Depense::whereHas('recu', fn($q) =>
            $q->where('user_id', auth()->id())
        )->with('recu');

        if ($request->filled('categorie')) {
            $categorie = $request->categorie;
            $validCategories = CategorieDepense::values();

            if (in_array($categorie, $validCategories, true)) {
                $query->where('categorie', $categorie);
            }
        }

        $depenses = $query->latest()->paginate(20);
        $categories = CategorieDepense::cases();

        return view('depenses.index', compact('depenses', 'categories'));
    }

    public function create()
    {
        $categories = CategorieDepense::cases();
        return view('depenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recu_id' => ['required', 'exists:recus,id'],
            'libelle' => ['required', 'string', 'max:255'],
            'quantite' => ['required', 'integer', 'min:1'],
            'prix_unitaire' => ['required', 'numeric', 'min:0'],
            'categorie' => ['required', 'string', 'in:' . implode(',', CategorieDepense::values())],
        ]);

        $recu = \App\Models\Recu::findOrFail($validated['recu_id']);
        $this->authorize('view', $recu);

        $recu->depenses()->create($validated);

        return redirect()->route('depenses.index')
            ->with('success', 'Dépense créée.');
    }

    public function show(Depense $depense)
    {
        $this->authorize('view', $depense);
        return view('depenses.show', compact('depense'));
    }

    public function edit(Depense $depense)
    {
        $this->authorize('update', $depense);
        $categories = CategorieDepense::cases();
        return view('depenses.edit', compact('depense', 'categories'));
    }

    public function update(Request $request, Depense $depense)
    {
        $this->authorize('update', $depense);

        $validated = $request->validate([
            'libelle' => ['required', 'string', 'max:255'],
            'quantite' => ['required', 'integer', 'min:1'],
            'prix_unitaire' => ['required', 'numeric', 'min:0'],
            'categorie' => ['required', 'string', 'in:' . implode(',', CategorieDepense::values())],
        ]);

        $depense->update($validated);

        return redirect()->route('depenses.index')
            ->with('success', 'Dépense mise à jour.');
    }

    public function destroy(Depense $depense)
    {
        $this->authorize('delete', $depense);
        $depense->delete();

        return redirect()->route('depenses.index')
            ->with('success', 'Dépense supprimée.');
    }
}
