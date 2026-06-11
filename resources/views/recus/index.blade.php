@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Mes reçus</h2>
    <a href="{{ route('recus.create') }}" class="btn btn-primary">+ Nouveau reçu</a>
</div>
@if($recus->isEmpty())
    <p class="text-muted">Aucun reçu pour l'instant.</p>
@else
<table class="table table-bordered bg-white">
    <thead class="table-dark">
        <tr><th>Date</th><th>Aperçu</th><th>Statut</th><th>Articles</th><th>Actions</th></tr>
    </thead>
    <tbody>
    @foreach($recus as $recu)
    <tr>
        <td>{{ $recu->created_at->format('d/m/Y H:i') }}</td>
        <td>{{ Str::limit($recu->texte_brut, 50) }}</td>
        <td>
            @php $color = match($recu->statut) {
                \App\Enums\StatutRecu::Traite  => 'success',
                \App\Enums\StatutRecu::Echoue  => 'danger',
                default => 'warning'
            }; @endphp
            <span class="badge bg-{{ $color }}">{{ $recu->status->label() }}</span>
        </td>
        <td>{{ $recu->depenses->count() }}</td>
        <td class="d-flex gap-1">
            <a href="{{ route('recus.show', $recu) }}" class="btn btn-sm btn-outline-primary">Voir</a>
            <form method="POST" action="{{ route('recus.destroy', $recu) }}">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger"
                    onclick="return confirm('Supprimer ce reçu et ses dépenses ?')">
                    Supprimer
                </button>
            </form>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
@endif
@endsection