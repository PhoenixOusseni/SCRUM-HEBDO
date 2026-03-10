@extends('layouts.app')

@section('title', 'Changer le mot de passe')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header rounded-top-4 py-3" style="background: linear-gradient(to right, #e65c00, #ffb347);">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-key me-2"></i>Changer le mot de passe
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if (session('success'))
                        <div class="alert alert-success py-2">
                            <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger py-2">
                            @foreach ($errors->all() as $error)
                                <div><i class="fas fa-exclamation-circle me-1"></i>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.change') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="current_password">
                                <i class="fas fa-lock me-1 text-secondary"></i>Mot de passe actuel
                            </label>
                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                autocomplete="current-password"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="password">
                                <i class="fas fa-lock me-1 text-secondary"></i>Nouveau mot de passe
                            </label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                autocomplete="new-password"
                                required>
                            <div class="form-text">Minimum 8 caractères.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold" for="password_confirmation">
                                <i class="fas fa-lock me-1 text-secondary"></i>Confirmer le nouveau mot de passe
                            </label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control"
                                autocomplete="new-password"
                                required>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning fw-semibold">
                                <i class="fas fa-save me-1"></i>Enregistrer
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
