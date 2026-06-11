<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreRecuRequest;
use App\Models\Recu;
use App\Enums\StatutRecu;
use Illuminate\Http\Request;

class RecuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
             $recus = auth()->user()->recus()
            ->with('depenses')->latest()->get();
        return view('recus.index', compact('recus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('recus.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRecuRequest $request)
    {

   
        $validated = $request->validated();

        $validated['user_id']=auth()->id();
        $validated['status']= StatutRecu::EnAttente;
        
        Recu::create($validated);
       
         return redirect()->route('recus.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Recu $recu)

    {
        $this->authorize('view', $recu);
        return view('recus.show',compact('recu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recu $recu)
    {
        $this->authorize('update', $recu);
        return view('recu.edit',compact('recu'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recu $recu)
    {
        $this->authorize('delete', $recu);
        $recu->delete();
        return redirect()->route('recu.index');
    }
}
