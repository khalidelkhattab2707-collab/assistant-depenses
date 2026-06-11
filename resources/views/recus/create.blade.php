@extends('layouts.app')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h2 class="mb-4">Soumettre un reçu</h2>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('recus.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-medium">Texte du reçu fournisseur</label>
                        <textarea name="text_brut" rows="12"
                            class="form-control @error('text_brut') is-invalid @enderror"
                            placeholder="Collez ici le texte du reçu (darija, français, abréviations...)..."
                        >{{ old('text_brut') }}</textarea>
                        @error('text_brut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" type="submit">Lancer l'extraction IA</button>
                        <a href="{{ route('recus.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection