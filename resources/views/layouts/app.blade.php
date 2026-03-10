<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SCRUM Hebdo') – AUXFIN BF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --fait: #1e8449;
            --en-cours: #e67e22;
            --reporte: #d4ac0d;
            --bloque: #2980b9;
            --maj: #7f8c8d;
        }

        body {
            font-size: 13px;
            background: #f4f6f9;
        }

        .navbar {
            background: linear-gradient(to right, #e65c00, #ffb347) !important;
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: .4px;
            font-size: 1rem;
            color: #000 !important;
        }

        .navbar .nav-link {
            color: #000 !important;
        }

        .navbar .nav-link:hover {
            color: #333 !important;
        }

        /* ── Table canevas ──────────────────────────────── */
        .canevas-table {
            font-size: 12px;
            border-collapse: collapse;
        }

        .canevas-table th {
            background: #1a3c5e;
            color: #fff;
            font-size: 11px;
            text-align: center;
            vertical-align: middle;
            padding: 6px 8px;
            border: 1px solid #0d2438;
        }

        .canevas-table td {
            vertical-align: top;
            padding: 5px 7px;
            border: 1px solid #c8d0da;
        }

        .canevas-table tr:hover td {
            background: #f0f4f8;
        }

        .th-detail {
            color: #ff6b6b !important;
            font-style: italic;
        }

        /* Colonne Acteurs */
        .td-acteur {
            background: #eaf0f6;
            font-weight: 600;
            color: #1a3c5e;
            min-width: 120px;
            max-width: 140px;
            text-align: center;
            vertical-align: middle;
        }

        .td-acteur .poste {
            font-size: 10px;
            color: #666;
            font-weight: 400;
        }

        /* Colonne activité */
        .td-activite {
            min-width: 220px;
            max-width: 300px;
        }

        .td-activite-suivante {
            min-width: 220px;
            max-width: 280px;
        }

        /* Colonne obstacles */
        .td-obstacles {
            min-width: 130px;
            max-width: 160px;
            vertical-align: middle;
            text-align: center;
            color: #c0392b;
            font-weight: 500;
            font-size: 11px;
        }

        /* Colonne raison */
        .td-raison {
            min-width: 140px;
            max-width: 200px;
            font-style: italic;
            color: #555;
            font-size: 11px;
        }

        /* ── Statut select ──────────────────────────────── */
        .statut-select {
            border: none;
            padding: 3px 6px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 4px;
            cursor: pointer;
            color: #fff;
            outline: none;
            -webkit-appearance: none;
            appearance: none;
            min-width: 90px;
            text-align: center;
        }

        .statut-select option {
            color: #333;
            background: #fff;
        }

        /* ── Boutons arrondis ───────────────────────────── */
        .btn {
            border-radius: 25px;
        }

        /* ── Boutons actions miniatures ─────────────────── */
        .btn-xs {
            padding: 1px 6px;
            font-size: 10px;
            line-height: 1.4;
        }

        .action-row {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 6px;
        }

        /* ── Séparateur entre employés ──────────────────── */
        .sep-employe td {
            border-top: 2px solid #1a3c5e !important;
        }

        /* ── Texte RAS ──────────────────────────────────── */
        .ras {
            color: #c0392b;
            font-weight: 600;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                font-size: 10px;
            }

            .navbar {
                display: none;
            }
        }
    </style>
    @yield('styles')
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-calendar-week me-2"></i>SCRUM Hebdo &nbsp;<span
                    class="badge bg-warning text-dark">AUXFIN BF</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}"><i class="fas fa-home me-1"></i>Canevas
                            courant</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('semaines.index') }}"><i class="fas fa-list me-1"></i>Toutes
                            les semaines</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('employes.index') }}"><i
                                    class="fas fa-users me-1"></i>Employés</a>
                        </li>
                    @endauth
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
                        </li>
                    @endguest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('statistiques.index') }}"><i class="fas fa-chart-bar me-1"></i>Statistiques</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3 no-print">
                    <span class="navbar-text text-dark small">
                        <i class="fas fa-clock me-1"></i>{{ now()->format('d/m/Y') }}
                    </span>
                    @auth
                        <div class="dropdown">
                            <button class="btn btn-sm btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i>{{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('password.edit') }}">
                                        <i class="fas fa-key me-2 text-secondary"></i>Changer le mot de passe
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-3">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2 no-print" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show py-2 no-print">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>

</html>
