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
                <span class="badge bg-{{ $recu->status->color() }} mb-2">{{ $recu->status->label() }}</span>
                @if($recu->image_path)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $recu->image_path) }}"
                             alt="Image du reçu" class="img-fluid rounded"
                             style="max-height:300px;object-fit:contain">
                    </div>
                @endif
                <pre class="bg-light p-2 rounded" style="font-size:13px;white-space:pre-wrap">{{ $recu->text_brut }}</pre>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        @if($recu->depenses->isEmpty())
            <div class="alert alert-info">
                @if($recu->status === \App\Enums\StatutRecu::EnAttente)
                    Extraction en cours… Rafraîchis la page dans quelques secondes.
                @elseif($recu->status === \App\Enums\StatutRecu::Echoue)
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
