@extends('layouts.app')

@section('title', 'Statistiques d\'exécution')

@section('styles')
<style>
    .stat-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,.08);
    }
    .pct-badge {
        font-size: 1.6rem;
        font-weight: 700;
        line-height: 1;
    }
    
    .progress { height: 14px; border-radius: 8px; }
    .chart-container { position: relative; }
    .employe-card { border-left: 4px solid #1a3c5e; }
    .legend-dot {
        display: inline-block;
        width: 12px; height: 12px;
        border-radius: 50%;
        margin-right: 4px;
    }
    .filter-bar {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 6px rgba(0,0,0,.07);
        padding: 14px 18px;
        margin-bottom: 20px;
    }
    .global-kpi { background: linear-gradient(135deg, #1a3c5e 0%, #2980b9 100%); }
</style>
@endsection

@section('content')

{{-- ── Titre ──────────────────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center mb-3 gap-2">
    <i class="fas fa-chart-bar fa-lg text-primary"></i>
    <h5 class="mb-0 fw-bold">Statistiques d'exécution des activités</h5>
</div>

{{-- ── Filtres ─────────────────────────────────────────────────────────────── --}}
<div class="filter-bar no-print">
    <form method="GET" action="{{ route('statistiques.index') }}" class="row g-2 align-items-end">

        <div class="col-auto">
            <div class="btn-group btn-group-sm" role="group">
                <input type="radio" class="btn-check" name="mode" id="modeUnique" value="unique"
                    {{ $modeUnique ? 'checked' : '' }} onchange="toggleMode(true)">
                <label class="btn btn-outline-primary" for="modeUnique">
                    <i class="fas fa-calendar-day me-1"></i>Semaine unique
                </label>
                <input type="radio" class="btn-check" name="mode" id="modePlage" value="plage"
                    {{ !$modeUnique ? 'checked' : '' }} onchange="toggleMode(false)">
                <label class="btn btn-outline-primary" for="modePlage">
                    <i class="fas fa-calendar-week me-1"></i>Plage de semaines
                </label>
            </div>
        </div>

        {{-- Mode : semaine unique --}}
        <div id="zoneSemaineUnique" class="col-auto {{ !$modeUnique ? 'd-none' : '' }}">
            <label class="form-label form-label-sm mb-1">Semaine</label>
            <select name="semaine_id" class="form-select form-select-sm" style="min-width:230px">
                @foreach ($semaines as $s)
                    <option value="{{ $s->id }}" {{ (string)$semaineId === (string)$s->id ? 'selected' : '' }}>
                        {{ $s->label() }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Mode : plage --}}
        <div id="zonePlage" class="col-auto {{ $modeUnique ? 'd-none' : '' }} d-flex gap-2 align-items-end">
            <div>
                <label class="form-label form-label-sm mb-1">De la semaine</label>
                <select name="periode_debut" class="form-select form-select-sm" style="min-width:190px">
                    <option value="">– début –</option>
                    @foreach ($semaines->sortBy('date_debut') as $s)
                        <option value="{{ $s->id }}" {{ (string)$periodeDebut === (string)$s->id ? 'selected' : '' }}>
                            S{{ $s->numero_semaine }}/{{ $s->annee }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label form-label-sm mb-1">À la semaine</label>
                <select name="periode_fin" class="form-select form-select-sm" style="min-width:190px">
                    <option value="">– fin –</option>
                    @foreach ($semaines->sortBy('date_debut') as $s)
                        <option value="{{ $s->id }}" {{ (string)$periodeFin === (string)$s->id ? 'selected' : '' }}>
                            S{{ $s->numero_semaine }}/{{ $s->annee }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Filtre employé --}}
        <div class="col-auto">
            <label class="form-label form-label-sm mb-1">Employé</label>
            <select name="employe_id" class="form-select form-select-sm" style="min-width:160px">
                <option value="">– Tous –</option>
                @foreach ($employes as $e)
                    <option value="{{ $e->id }}" {{ (string)$employeId === (string)$e->id ? 'selected' : '' }}>
                        {{ $e->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-auto">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-filter me-1"></i>Filtrer
            </button>
            <a href="{{ route('statistiques.index') }}" class="btn btn-outline-secondary btn-sm ms-1">
                <i class="fas fa-redo me-1"></i>Réinitialiser
            </a>
        </div>
    </form>
</div>

@if ($statsParEmploye->isEmpty())
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Aucune activité avec statut renseigné pour la période sélectionnée.
    </div>
@else

{{-- ── KPI Globaux ─────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card global-kpi text-white h-100">
            <div class="card-body text-center py-3">
                <div class="text-white-50 small mb-1"><i class="fas fa-tasks me-1"></i>Total activités</div>
                <div class="pct-badge">{{ $totalGlobal }}</div>
                <div class="small mt-1 text-white-50">avec statut renseigné</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card h-100" style="border-left:4px solid #1e8449">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1"><i class="fas fa-check-circle me-1" style="color:#1e8449"></i>Activités faites</div>
                <div class="pct-badge" style="color:#1e8449">{{ $faitGlobal }}</div>
                <div class="small text-muted mt-1">sur {{ $totalGlobal }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card h-100" style="border-left:4px solid #e65c00">
            <div class="card-body text-center py-3">
                <div class="text-muted small mb-1"><i class="fas fa-percentage me-1" style="color:#e65c00"></i>Taux d'exécution global</div>
                <div class="pct-badge" style="color:#e65c00">
                    {{ $pctGlobal !== null ? $pctGlobal.'%' : '–' }}
                </div>
                @if ($pctGlobal !== null)
                    <div class="progress mt-2">
                        <div class="progress-bar" role="progressbar"
                            style="width:{{ $pctGlobal }}%; background:#e65c00"
                            aria-valuenow="{{ $pctGlobal }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body py-3">
                <div class="text-muted small mb-2"><i class="fas fa-layer-group me-1"></i>Répartition globale</div>
                @foreach (\App\Models\Activite::STATUTS as $key => $label)
                    @php $cnt = $totauxParStatut[$key] ?? 0; @endphp
                    @if ($cnt > 0)
                        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:11px">
                            <span>
                                <span class="legend-dot" style="background:{{ \App\Models\Activite::STATUT_COLORS[$key] }}"></span>
                                {{ $label }}
                            </span>
                            <span class="fw-semibold">{{ $cnt }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ── Graphique : Barres horizontales taux d'exécution par employé ─────────── --}}
<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="card stat-card h-100">
            <div class="card-header bg-white fw-semibold border-0 pb-0">
                <i class="fas fa-chart-bar me-2 text-primary"></i>Taux d'exécution par employé (%)
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:{{ max(150, $statsParEmploye->count() * 45) }}px">
                    <canvas id="chartBarres"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card stat-card h-100">
            <div class="card-header bg-white fw-semibold border-0 pb-0">
                <i class="fas fa-chart-pie me-2 text-warning"></i>Répartition globale des statuts
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div class="chart-container" style="height:230px; width:100%">
                    <canvas id="chartDoughnut"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Graphique évolution (plage de semaines) ─────────────────────────────── --}}
@if (!$modeUnique && count($evolution) > 1)
<div class="card stat-card mb-4">
    <div class="card-header bg-white fw-semibold border-0 pb-0">
        <i class="fas fa-chart-line me-2 text-success"></i>Évolution du taux d'exécution global sur la période
    </div>
    <div class="card-body">
        <div class="chart-container" style="height:220px">
            <canvas id="chartEvolution"></canvas>
        </div>
    </div>
</div>
@endif

{{-- ── Tableau détaillé par employé ────────────────────────────────────────── --}}
<div class="card stat-card mb-4">
    <div class="card-header bg-white fw-semibold border-0 pb-0 d-flex align-items-center gap-2">
        <i class="fas fa-table text-secondary"></i>Détail par employé
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:12px">
                <thead class="table-dark">
                    <tr>
                        <th>Employé</th>
                        <th class="text-center">Total</th>
                        @foreach (\App\Models\Activite::STATUTS as $key => $label)
                            <th class="text-center">
                                <span class="legend-dot" style="background:{{ \App\Models\Activite::STATUT_COLORS[$key] }}"></span>
                                {{ $label }}
                            </th>
                        @endforeach
                        <th class="text-center" style="min-width:140px">Taux d'exécution</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($statsParEmploye as $stat)
                        <tr>
                            <td class="fw-semibold" style="color:#1a3c5e">
                                {{ $stat['employe']->nom }}
                                @if ($stat['employe']->poste)
                                    <div class="text-muted fw-normal" style="font-size:10px">{{ $stat['employe']->poste }}</div>
                                @endif
                            </td>
                            <td class="text-center fw-bold">{{ $stat['total'] }}</td>
                            @foreach (\App\Models\Activite::STATUTS as $key => $label)
                                <td class="text-center">
                                    @php $cnt = $stat['par_statut'][$key] ?? 0; @endphp
                                    @if ($cnt > 0)
                                        <span class="badge" style="background:{{ \App\Models\Activite::STATUT_COLORS[$key] }}">
                                            {{ $cnt }}
                                        </span>
                                    @else
                                        <span class="text-muted">–</span>
                                    @endif
                                </td>
                            @endforeach
                            <td>
                                @if ($stat['pct'] !== null)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height:10px">
                                            @php
                                                $color = $stat['pct'] >= 75 ? '#1e8449' : ($stat['pct'] >= 50 ? '#e67e22' : '#c0392b');
                                            @endphp
                                            <div class="progress-bar" role="progressbar"
                                                style="width:{{ $stat['pct'] }}%; background:{{ $color }}"
                                                aria-valuenow="{{ $stat['pct'] }}" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="fw-bold" style="color:{{ $color }}; min-width:38px">{{ $stat['pct'] }}%</span>
                                    </div>
                                @else
                                    <span class="text-muted small">–</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── Cartes détail par employé (doughnut individuel) ─────────────────────── --}}
<h6 class="fw-semibold mb-3 text-secondary">
    <i class="fas fa-users me-2"></i>Détail individuel
</h6>
<div class="row g-3 mb-3">
    @foreach ($statsParEmploye as $i => $stat)
        <div class="col-md-6 col-xl-4">
            <div class="card stat-card employe-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="fw-bold" style="color:#1a3c5e; font-size:13px">{{ $stat['employe']->nom }}</div>
                            @if ($stat['employe']->poste)
                                <div class="text-muted" style="font-size:10px">{{ $stat['employe']->poste }}</div>
                            @endif
                        </div>
                        @if ($stat['pct'] !== null)
                            @php $color = $stat['pct'] >= 75 ? '#1e8449' : ($stat['pct'] >= 50 ? '#e67e22' : '#c0392b'); @endphp
                            <span class="fw-bold fs-5" style="color:{{ $color }}">{{ $stat['pct'] }}%</span>
                        @endif
                    </div>

                    <div class="row align-items-center g-0">
                        <div class="col-6">
                            <canvas id="chartEmploye{{ $i }}" height="120"></canvas>
                        </div>
                        <div class="col-6 ps-2">
                            @foreach (\App\Models\Activite::STATUTS as $key => $label)
                                @php $cnt = $stat['par_statut'][$key] ?? 0; @endphp
                                @if ($cnt > 0)
                                    <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:11px">
                                        <span>
                                            <span class="legend-dot" style="background:{{ \App\Models\Activite::STATUT_COLORS[$key] }}"></span>
                                            {{ $label }}
                                        </span>
                                        <span class="fw-semibold">{{ $cnt }}</span>
                                    </div>
                                @endif
                            @endforeach
                            <div class="border-top mt-1 pt-1" style="font-size:11px">
                                <span class="text-muted">Total :</span>
                                <span class="fw-bold ms-1">{{ $stat['total'] }}</span>
                            </div>
                        </div>
                    </div>

                    @if ($stat['pct'] !== null)
                        <div class="progress mt-2">
                            <div class="progress-bar" role="progressbar"
                                style="width:{{ $stat['pct'] }}%; background:{{ $color }}"
                                aria-valuenow="{{ $stat['pct'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

@endif {{-- fin statsParEmploye not empty --}}

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
// ── Données depuis PHP ────────────────────────────────────────────────────────
const statsParEmploye = @json($statsParEmploye);
const statuts = @json(\App\Models\Activite::STATUTS);
const statutColors = @json(\App\Models\Activite::STATUT_COLORS);
const totauxParStatut = @json($totauxParStatut);
const evolution = @json($evolution);
const modeUnique = {{ $modeUnique ? 'true' : 'false' }};

// ── Options communes ──────────────────────────────────────────────────────────
Chart.defaults.font.family = "'Segoe UI', system-ui, sans-serif";
Chart.defaults.font.size   = 11;

// ── 1. Barres horizontales : taux d'exécution ─────────────────────────────────
(function() {
    const ctx = document.getElementById('chartBarres');
    if (!ctx) return;
    const labels = statsParEmploye.map(s => s.employe.nom);
    const data   = statsParEmploye.map(s => s.pct ?? 0);
    const colors = data.map(v => v >= 75 ? '#1e8449' : v >= 50 ? '#e67e22' : '#c0392b');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: "Taux d'exécution (%)",
                data,
                backgroundColor: colors,
                borderRadius: 5,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.x}%  (${statsParEmploye[ctx.dataIndex].fait}/${statsParEmploye[ctx.dataIndex].total} faites)`
                    }
                }
            },
            scales: {
                x: {
                    min: 0, max: 100,
                    ticks: { callback: v => v + '%' },
                    grid: { color: '#f0f0f0' }
                },
                y: { grid: { display: false } }
            }
        }
    });
})();

// ── 2. Doughnut global ────────────────────────────────────────────────────────
(function() {
    const ctx = document.getElementById('chartDoughnut');
    if (!ctx) return;

    const labels = [];
    const data   = [];
    const colors = [];
    Object.entries(statuts).forEach(([key, label]) => {
        const cnt = totauxParStatut[key] || 0;
        if (cnt > 0) {
            labels.push(label);
            data.push(cnt);
            colors.push(statutColors[key]);
        }
    });

    new Chart(ctx, {
        type: 'doughnut',
        data: { labels, datasets: [{ data, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }] },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '62%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 10, boxWidth: 12, font: { size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label} : ${ctx.parsed} activité(s)`
                    }
                }
            }
        }
    });
})();

// ── 3. Courbe évolution ───────────────────────────────────────────────────────
(function() {
    const ctx = document.getElementById('chartEvolution');
    if (!ctx || !evolution || evolution.length < 2) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: evolution.map(e => e.label),
            datasets: [{
                label: "Taux d'exécution global (%)",
                data: evolution.map(e => e.pct),
                borderColor: '#1a3c5e',
                backgroundColor: 'rgba(26,60,94,.08)',
                borderWidth: 2,
                pointBackgroundColor: '#e65c00',
                pointRadius: 5,
                fill: true,
                tension: .3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.y}%  (${evolution[ctx.dataIndex].fait}/${evolution[ctx.dataIndex].total})`
                    }
                }
            },
            scales: {
                y: { min: 0, max: 100, ticks: { callback: v => v + '%' }, grid: { color: '#f0f0f0' } },
                x: { grid: { display: false } }
            }
        }
    });
})();

// ── 4. Doughnuts individuels ──────────────────────────────────────────────────
statsParEmploye.forEach((stat, i) => {
    const ctx = document.getElementById('chartEmploye' + i);
    if (!ctx) return;

    const labels = [];
    const data   = [];
    const colors = [];
    Object.entries(statuts).forEach(([key, label]) => {
        const cnt = stat.par_statut[key] || 0;
        if (cnt > 0) {
            labels.push(label);
            data.push(cnt);
            colors.push(statutColors[key]);
        }
    });

    new Chart(ctx, {
        type: 'doughnut',
        data: { labels, datasets: [{ data, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }] },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '58%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: { label: ctx => ` ${ctx.label} : ${ctx.parsed}` }
                }
            }
        }
    });
});

// ── Toggle mode filtre ────────────────────────────────────────────────────────
function toggleMode(unique) {
    document.getElementById('zoneSemaineUnique').classList.toggle('d-none', !unique);
    document.getElementById('zonePlage').classList.toggle('d-none', unique);
}
</script>
@endsection
