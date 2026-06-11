@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Détail du reçu</h2>
    <a href="{{ route('recus.index') }}" class="btn btn-outline-secondary">← Retour</a>
</div>
<div class="row">
    <div class="col-md-5">
        <div class="card mb-3">
            <div class="card-header">Texte source</div>
            <div class="card-body">
                @php $color = match($recu->statut) {
                    \App\Enums\StatutRecu::Traite  => 'success',
                    \App\Enums\StatutRecu::Echoue  => 'danger',
                    default => 'warning'
                }; @endphp
                <span class="badge bg-{{ $color }} mb-2">{{ $recu->statut->label() }}</span>
                <pre class="bg-light p-2 rounded" style="font-size:13px;white-space:pre-wrap">{{ $recu->texte_brut }}</pre>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        @if($recu->depenses->isEmpty())
            <div class="alert alert-info">
                @if($recu->statut === \App\Enums\StatutRecu::EnAttente)
                    Extraction en cours… Rafraîchis la page dans quelques secondes.
                @elseif($recu->statut === \App\Enums\StatutRecu::Echoue)
                    L'extraction a échoué. Essaie de soumettre à nouveau.
                @else
                    Aucun article extrait.
                @endif
            </div>
        @else
        <div class="card">
            <div class="card-header">Dépenses extraites ({{ $recu->depenses->count() }})</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr><th>Libellé</th><th>Qté</th><th>Prix unit.</th><th>Catégorie</th></tr>
                    </thead>
                    <tbody>
                    @foreach($recu->depenses as $d)
                    <tr>
                        <td>{{ $d->libelle }}</td>
                        <td>{{ $d->quantite }}</td>
                        <td>{{ number_format($d->prix_unitaire, 2) }} MAD</td>
                        <td><span class="badge bg-secondary">{{ $d->categorie->label() }}</span></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

resources/views/depenses/index.blade.php

@extends('layouts.app')
@section('content')
<h2 class="mb-4">Mes dépenses</h2>
<form method="GET" class="d-flex gap-2 mb-4">
    <select name="categorie" class="form-select w-auto">
        <option value="">Toutes les catégories</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->value }}"
                {{ request('categorie') == $cat->value ? 'selected' : '' }}>
                {{ $cat->label() }}
            </option>
        @endforeach
    </select>
    <button class="btn btn-outline-secondary">Filtrer</button>
    @if(request('categorie'))
        <a href="{{ route('depenses.index') }}" class="btn btn-outline-danger">Réinitialiser</a>
    @endif
</form>
@if($depenses->isEmpty())
    <p class="text-muted">Aucune dépense trouvée.</p>
@else
<table class="table table-bordered bg-white">
    <thead class="table-dark">
        <tr><th>Libellé</th><th>Qté</th><th>Prix unitaire</th><th>Catégorie</th><th>Reçu</th></tr>
    </thead>
    <tbody>
    @foreach($depenses as $d)
    <tr>
        <td>{{ $d->libelle }}</td>
        <td>{{ $d->quantite }}</td>
        <td>{{ number_format($d->prix_unitaire, 2) }} MAD</td>
        <td><span class="badge bg-secondary">{{ $d->categorie->label() }}</span></td>
        <td><a href="{{ route('recus.show', $d->recu) }}">Voir reçu</a></td>
    </tr>
    @endforeach
    </tbody>
</table>
@endif
@endsection

