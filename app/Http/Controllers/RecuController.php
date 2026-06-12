<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecuRequest;
use App\Models\Recu;
use App\Services\RecuService;
use Illuminate\Http\Request;

class RecuController extends Controller
{
    public function index()
    {
        $recus = auth()->user()->recus()
            ->with('depenses')->latest()->paginate(20);

        return view('recus.index', compact('recus'));
    }

    public function create()
    {
        return view('recus.create');
    }

    public function store(StoreRecuRequest $request, RecuService $service)
    {
        $service->create(auth()->user(), $request->validated());

        return redirect()->route('recus.index')
            ->with('success', 'Reçu en cours de traitement.');
    }

    public function show(Recu $recu)
    {
        $this->authorize('view', $recu);

        $recu->load('depenses');

        return view('recus.show', compact('recu'));
    }

    public function edit(Recu $recu)
    {
        $this->authorize('update', $recu);

        return view('recus.edit', compact('recu'));
    }

    public function update(Request $request, Recu $recu)
    {
        $this->authorize('update', $recu);

        $validated = $request->validate([
            'text_brut' => ['required', 'string', 'min:10', 'max:10000'],
            'status' => ['required', 'string', 'in:en_attente,traite,echoue'],
            'devis' => ['nullable', 'string', 'in:MAD,EUR,USD'],
            'total_estime' => ['nullable', 'numeric', 'min:0'],
        ]);

        $recu->update($validated);

        return redirect()->route('recus.index')
            ->with('success', 'Reçu mis à jour.');
    }

    public function destroy(Recu $recu)
    {
        $this->authorize('delete', $recu);
        $recu->delete();

        return redirect()->route('recus.index')
            ->with('success', 'Reçu supprimé.');
    }
}
