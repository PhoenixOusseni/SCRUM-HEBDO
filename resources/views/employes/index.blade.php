@extends('layouts.app')

@section('title', 'Gestion de l\'équipe')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0"><i class="fas fa-users text-primary me-2"></i>Équipe AUXFIN BF</h5>
    <a href="{{ route('employes.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-user-plus me-1"></i>Ajouter un employé
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th style="width:50px">#</th>
                    <th>Nom</th>
                    <th>Poste</th>
                    <th class="text-center">Ordre</th>
                    <th class="text-center">Statut</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employes as $employe)
                    <tr class="{{ !$employe->actif ? 'text-muted' : '' }}">
                        <td>{{ $employe->id }}</td>
                        <td><strong>{{ $employe->nom }}</strong></td>
                        <td>{{ $employe->poste ?? '–' }}</td>
                        <td class="text-center">{{ $employe->ordre }}</td>
                        <td class="text-center">
                            @if ($employe->actif)
                                <span class="badge bg-success">Actif</span>
                            @else
                                <span class="badge bg-secondary">Inactif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('employes.edit', $employe) }}" class="btn btn-xs btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('employes.destroy', $employe) }}" class="d-inline"
                                  onsubmit="return confirm('Supprimer {{ $employe->nom }} ? Toutes ses activités seront supprimées.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Aucun employé. <a href="{{ route('employes.create') }}">Ajouter le premier</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
