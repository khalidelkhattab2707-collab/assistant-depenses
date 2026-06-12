@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Mes dépenses</h2>
    <a href="{{ route('depenses.create') }}" class="btn btn-primary">Créer une dépense</a>
</div>

<ul class="nav nav-pills mb-3">
    <li class="nav-item">
        <a class="nav-link {{ !request('categorie') ? 'active' : '' }}"
           href="{{ route('depenses.index') }}">Toutes</a>
    </li>
    @foreach($categories as $cat)
        <li class="nav-item">
            <a class="nav-link {{ request('categorie') === $cat->value ? 'active' : '' }}"
               href="{{ route('depenses.index', ['categorie' => $cat->value]) }}">
                {{ $cat->label() }}
            </a>
        </li>
    @endforeach
</ul>

@if($depenses->isEmpty())
    <p class="text-muted">Aucune dépense pour l'instant.</p>
@else
<table class="table table-bordered bg-white">
    <thead class="table-dark">
        <tr>
            <th>Libellé</th>
            <th>Quantité</th>
            <th>Prix unitaire</th>
            <th>Catégorie</th>
            <th>Reçu</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    @foreach($depenses as $d)
    <tr>
        <td><a href="{{ route('depenses.show', $d) }}">{{ $d->libelle }}</a></td>
        <td>{{ $d->quantite }}</td>
        <td>{{ number_format($d->prix_unitaire, 2) }} MAD</td>
        <td><span class="badge bg-secondary">{{ $d->categorie->label() }}</span></td>
        <td>
            <a href="{{ route('recus.show', $d->recu) }}" class="btn btn-sm btn-outline-primary">
                Voir le reçu
            </a>
        </td>
        <td>
            <a href="{{ route('depenses.edit', $d) }}" class="btn btn-sm btn-warning">Modifier</a>
            <form method="POST" action="{{ route('depenses.destroy', $d) }}" style="display:inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger" type="submit"
                    onclick="return confirm('Supprimer cette dépense ?')">Supprimer</button>
            </form>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
@endif
{{ $depenses->links() }}
@endsection
