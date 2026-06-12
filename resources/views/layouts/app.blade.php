<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SCRUM Hebdo') – AUXFIN BF</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --c-navy: #1a3c5e;
            --c-navy-dark: #0f2740;
            --c-navy-light: #2f5c8a;
            --c-orange: #e65c00;
            --c-orange-light: #ffb347;
            --c-bg: #f3f5f9;
            --c-border: #e3e8ef;
            --c-text: #2d3748;
            --c-muted: #768294;
            --radius: 14px;
            --radius-sm: 8px;
            --shadow: 0 2px 16px rgba(26, 60, 94, .07);

            --fait: #1e8449;
            --en-cours: #e67e22;
            --reporte: #d4ac0d;
            --bloque: #2980b9;
            --maj: #7f8c8d;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, "Segoe UI", sans-serif;
            font-size: 13.5px;
            color: var(--c-text);
            background: var(--c-bg);
        }

        h1, h2, h3, h4, h5, h6 {
            color: var(--c-navy);
            font-weight: 700;
        }

        /* ── Navbar ───────────────────────────────────────────── */
        .navbar {
            background: linear-gradient(100deg, #e65c00, #ff9a3c) !important;
            box-shadow: 0 2px 14px rgba(230, 92, 0, .25);
            padding: .65rem 0;
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: .3px;
            font-size: 1.05rem;
            color: #1a1a1a !important;
        }

        .navbar .nav-link {
            color: rgba(0, 0, 0, .65) !important;
            font-weight: 600;
            border-radius: var(--radius-sm);
            padding: .4rem .7rem !important;
            transition: background-color .15s ease;
        }

        .navbar .nav-link:hover {
            color: #000 !important;
            background-color: rgba(255, 255, 255, .25);
        }

        .badge-app {
            background: rgba(0, 0, 0, .18);
            color: #fff;
            font-weight: 600;
            font-size: .7rem;
            padding: 3px 10px;
            border-radius: 20px;
            vertical-align: middle;
        }

        .navbar .btn-dark {
            background: rgba(0, 0, 0, .2);
            border: none;
        }

        .navbar .btn-dark:hover {
            background: rgba(0, 0, 0, .32);
        }

        .navbar .dropdown-menu {
            border: none;
            border-radius: var(--radius-sm);
            box-shadow: var(--shadow);
        }

        /* ── Cartes ───────────────────────────────────────────── */
        .card {
            border: none;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .card-header {
            border-radius: var(--radius) var(--radius) 0 0 !important;
            border-bottom: 1px solid var(--c-border);
            font-weight: 700;
        }

        .card-header.bg-primary {
            background: var(--c-navy) !important;
        }

        /* ── Boutons ──────────────────────────────────────────── */
        .btn {
            border-radius: 25px;
            font-weight: 600;
        }

        .btn-primary {
            background: var(--c-navy);
            border-color: var(--c-navy);
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: var(--c-navy-dark);
            border-color: var(--c-navy-dark);
        }

        .btn-outline-primary {
            color: var(--c-navy);
            border-color: var(--c-navy);
        }

        .btn-outline-primary:hover {
            background: var(--c-navy);
            border-color: var(--c-navy);
        }

        .text-primary {
            color: var(--c-navy) !important;
        }

        /* ── Boutons actions miniatures ──────────────────────── */
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

        /* ── Tableaux génériques ──────────────────────────────── */
        .table-dark {
            --bs-table-bg: var(--c-navy);
            --bs-table-border-color: var(--c-navy-light);
        }

        .table > :not(caption) > * > * {
            padding: .65rem .85rem;
        }

        /* ── Alertes ──────────────────────────────────────────── */
        .alert {
            border: none;
            border-radius: var(--radius-sm);
        }

        /* ── Table canevas ────────────────────────────────────── */
        .canevas-wrapper {
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .canevas-table {
            font-size: 12px;
            border-collapse: collapse;
        }

        .canevas-table th {
            background: var(--c-navy);
            color: #fff;
            font-size: 11px;
            text-align: center;
            vertical-align: middle;
            padding: 10px 8px;
            border: 1px solid var(--c-navy-dark);
        }

        .canevas-table td {
            vertical-align: top;
            padding: 8px 10px;
            border: 1px solid var(--c-border);
        }

        .canevas-table tr:hover td {
            background: #f3f7fb;
        }

        .th-detail {
            color: #ff8a65 !important;
            font-style: italic;
        }

        /* Colonne Acteurs */
        .td-acteur {
            background: #eef3f8;
            font-weight: 700;
            color: var(--c-navy);
            min-width: 120px;
            max-width: 140px;
            text-align: center;
            vertical-align: middle;
        }

        .td-acteur .poste {
            font-size: 10px;
            color: var(--c-muted);
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
            font-weight: 600;
            font-size: 11px;
        }

        /* Colonne raison */
        .td-raison {
            min-width: 140px;
            max-width: 200px;
            font-style: italic;
            color: var(--c-muted);
            font-size: 11px;
        }

        /* ── Statut select ────────────────────────────────────── */
        .statut-select {
            border: none;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 20px;
            cursor: pointer;
            color: #fff;
            outline: none;
            -webkit-appearance: none;
            appearance: none;
            min-width: 92px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .15);
        }

        .statut-select option {
            color: #333;
            background: #fff;
        }

        /* ── Séparateur entre employés ───────────────────────── */
        .sep-employe td {
            border-top: 2px solid var(--c-navy) !important;
        }

        /* ── Texte RAS ────────────────────────────────────────── */
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
                background: #fff;
            }

            .navbar {
                display: none;
            }

            .card,
            .canevas-wrapper {
                box-shadow: none;
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
                    class="badge-app">AUXFIN BF</span>
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

    <div class="container-fluid py-4 px-3 px-md-4">
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>

</html>
