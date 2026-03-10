@extends('layouts.app')

@section('title', 'Toutes les semaines')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="fas fa-list text-primary me-2"></i>Historique des semaines</h5>
        @auth
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalNouvelleSemaine">
                <i class="fas fa-calendar-plus me-1"></i>Nouvelle semaine
            </button>
        @endauth
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>N° Semaine</th>
                        <th>Période</th>
                        <th>Année</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($semaines as $sem)
                        @php
                            $isCurrent =
                                $sem->numero_semaine == now()->format('W') && $sem->annee == now()->format('o');
                        @endphp
                        <tr class="{{ $isCurrent ? 'table-success' : '' }}">
                            <td>
                                <strong>S{{ $sem->numero_semaine }}</strong>
                                @if ($isCurrent)
                                    <span class="badge bg-success ms-1">Courante</span>
                                @endif
                            </td>
                            <td>Du {{ $sem->date_debut->format('d/m/Y') }} au {{ $sem->date_fin->format('d/m/Y') }}</td>
                            <td>{{ $sem->annee }}</td>
                            <td class="text-center">
                                <a href="{{ route('semaines.show', $sem) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye me-1"></i>Voir le canevas
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                Aucune semaine enregistrée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal : Nouvelle semaine --}}
    <div class="modal fade" id="modalNouvelleSemaine" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-calendar-plus me-2"></i>Nouvelle semaine</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('semaines.store') }}">
                    @csrf
                    <div class="modal-body">
                        <label class="form-label fw-semibold">Choisir une date dans la semaine</label>
                        <input type="date" name="date_debut" class="form-control" value="{{ date('Y-m-d') }}" required>
                        <div class="form-text">La semaine sera créée du lundi au dimanche de la date choisie.</div>
                    </div>
                    <div class="m-3">
                        <button type="submit" class="btn btn-primary btn-sm">Créer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
