<?php

namespace App\Http\Controllers;
use App\Enums\CategorieDepense;

use App\Models\Depense;
use Illuminate\Http\Request;

class DepenseController extends Controller
{
     public function index(Request $request) {
        $query = Depense::whereHas('recu', fn($q) =>
            $q->where('user_id', auth()->id())
        )->with('recu');

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }
        $depenses   = $query->latest()->get();
        $categories = CategorieDepense::cases();
        return view('depenses.index', compact('depenses','categories'));
    }
}
