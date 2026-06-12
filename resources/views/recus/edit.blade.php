@extends('layouts.app')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h2 class="mb-4">Modifier le reçu</h2>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('recus.update', $recu) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-medium">Texte du reçu</label>
                        <textarea name="text_brut" rows="12"
                            class="form-control @error('text_brut') is-invalid @enderror"
                        >{{ old('text_brut', $recu->text_brut) }}</textarea>
                        @error('text_brut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Statut</label>
                            <select name="status"
                                class="form-select @error('status') is-invalid @enderror">
                                @foreach(App\Enums\StatutRecu::cases() as $s)
                                    <option value="{{ $s->value }}"
                                        {{ old('status', $recu->status->value) === $s->value ? 'selected' : '' }}>
                                        {{ $s->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Devise</label>
                            <select name="devis"
                                class="form-select @error('devis') is-invalid @enderror">
                                <option value="">—</option>
                                <option value="MAD" {{ old('devis', $recu->devis) === 'MAD' ? 'selected' : '' }}>MAD</option>
                                <option value="EUR" {{ old('devis', $recu->devis) === 'EUR' ? 'selected' : '' }}>EUR</option>
                                <option value="USD" {{ old('devis', $recu->devis) === 'USD' ? 'selected' : '' }}>USD</option>
                            </select>
                            @error('devis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Total estimé</label>
                            <input type="number" step="0.01" min="0" name="total_estime"
                                value="{{ old('total_estime', $recu->total_estime) }}"
                                class="form-control @error('total_estime') is-invalid @enderror">
                            @error('total_estime')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" type="submit">Enregistrer</button>
                        <a href="{{ route('recus.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
