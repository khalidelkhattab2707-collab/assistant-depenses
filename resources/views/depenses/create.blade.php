@extends('layouts.app')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <h2 class="mb-4">Ajouter une dépense</h2>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('depenses.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-medium">Reçu</label>
                        <select name="recu_id"
                            class="form-select @error('recu_id') is-invalid @enderror">
                            <option value="">Sélectionner un reçu</option>
                            @foreach(auth()->user()->recus as $r)
                                <option value="{{ $r->id }}"
                                    {{ old('recu_id') == $r->id ? 'selected' : '' }}>
                                    #{{ $r->id }} — {{ Str::limit($r->text_brut, 60) }}
                                </option>
                            @endforeach
                        </select>
                        @error('recu_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Libellé</label>
                        <input type="text" name="libelle" value="{{ old('libelle') }}"
                            class="form-control @error('libelle') is-invalid @enderror">
                        @error('libelle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Quantité</label>
                            <input type="number" min="1" name="quantite" value="{{ old('quantite', 1) }}"
                                class="form-control @error('quantite') is-invalid @enderror">
                            @error('quantite')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Prix unitaire</label>
                            <input type="number" step="0.01" min="0" name="prix_unitaire"
                                value="{{ old('prix_unitaire') }}"
                                class="form-control @error('prix_unitaire') is-invalid @enderror">
                            @error('prix_unitaire')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Catégorie</label>
                            <select name="categorie"
                                class="form-select @error('categorie') is-invalid @enderror">
                                @foreach($categories as $c)
                                    <option value="{{ $c->value }}"
                                        {{ old('categorie') === $c->value ? 'selected' : '' }}>
                                        {{ $c->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categorie')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" type="submit">Créer</button>
                        <a href="{{ route('depenses.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
