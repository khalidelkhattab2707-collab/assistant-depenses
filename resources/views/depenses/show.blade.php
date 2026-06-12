@extends('layouts.app')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <h2 class="mb-4">Détail de la dépense</h2>
        <div class="card">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Libellé</dt>
                    <dd class="col-sm-8">{{ $depense->libelle }}</dd>

                    <dt class="col-sm-4">Quantité</dt>
                    <dd class="col-sm-8">{{ $depense->quantite }}</dd>

                    <dt class="col-sm-4">Prix unitaire</dt>
                    <dd class="col-sm-8">{{ number_format($depense->prix_unitaire, 2) }} MAD</dd>

                    <dt class="col-sm-4">Total</dt>
                    <dd class="col-sm-8">{{ number_format($depense->quantite * $depense->prix_unitaire, 2) }} MAD</dd>

                    <dt class="col-sm-4">Catégorie</dt>
                    <dd class="col-sm-8"><span class="badge bg-secondary">{{ $depense->categorie->label() }}</span></dd>

                    <dt class="col-sm-4">Reçu associé</dt>
                    <dd class="col-sm-8">
                        <a href="{{ route('recus.show', $depense->recu) }}">#{{ $depense->recu_id }}</a>
                    </dd>
                </dl>
            </div>
        </div>
        <div class="mt-3 d-flex gap-2">
            <a href="{{ route('depenses.edit', $depense) }}" class="btn btn-warning">Modifier</a>
            <form method="POST" action="{{ route('depenses.destroy', $depense) }}" style="display:inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger" type="submit"
                    onclick="return confirm('Supprimer cette dépense ?')">Supprimer</button>
            </form>
            <a href="{{ route('depenses.index') }}" class="btn btn-outline-secondary">Retour</a>
        </div>
    </div>
</div>
@endsection
