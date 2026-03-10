@extends('layouts.app')

@section('title', $mode === 'create' ? 'Ajouter un employé' : 'Modifier l\'employé')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-{{ $mode === 'create' ? 'user-plus' : 'user-edit' }} me-2"></i>
                    {{ $mode === 'create' ? 'Ajouter un employé' : 'Modifier : ' . $employe->nom }}
                </h5>
            </div>
            <div class="card-body">
                <form method="POST"
                      action="{{ $mode === 'create' ? route('employes.store') : route('employes.update', $employe) }}">
                    @csrf
                    @if ($mode === 'edit') @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nom complet <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                               value="{{ old('nom', $employe->nom) }}" placeholder="Ex : Mr Kafando" required>
                        @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Poste / Fonction</label>
                        <input type="text" name="poste" class="form-control"
                               value="{{ old('poste', $employe->poste) }}" placeholder="Ex : Chargé de programmes">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ordre d'affichage</label>
                        <input type="number" name="ordre" class="form-control" min="0"
                               value="{{ old('ordre', $employe->ordre) }}">
                        <div class="form-text">Les employés s'affichent dans l'ordre croissant.</div>
                    </div>

                    @if ($mode === 'edit')
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="actif" id="actif" value="1"
                                   {{ old('actif', $employe->actif) ? 'checked' : '' }}>
                            <label class="form-check-label" for="actif">Employé actif</label>
                        </div>
                    </div>
                    @endif

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Enregistrer
                        </button>
                        <a href="{{ route('employes.index') }}" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
