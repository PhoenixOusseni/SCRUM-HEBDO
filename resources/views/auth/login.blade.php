<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion – SCRUM Hebdo</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --c-navy: #1a3c5e;
            --c-navy-dark: #0f2740;
            --c-orange: #e65c00;
            --c-orange-light: #ffb347;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, "Segoe UI", sans-serif;
            background: linear-gradient(135deg, var(--c-navy), var(--c-navy-dark));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            border: none;
            border-radius: 16px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(110deg, var(--c-orange), var(--c-orange-light));
            padding: 2.25rem 2rem;
            text-align: center;
        }

        .login-header h1 {
            font-size: 1.4rem;
            font-weight: 800;
            color: #1a1a1a;
            margin: 0;
        }

        .badge-app {
            background: rgba(0, 0, 0, 0.18);
            color: #fff;
            font-weight: 600;
            font-size: 0.75rem;
            padding: 3px 10px;
            border-radius: 20px;
            margin-top: 6px;
            display: inline-block;
        }

        .btn {
            border-radius: 25px;
            font-weight: 600;
        }

        .btn-login {
            background: var(--c-navy);
            border: none;
            color: #fff;
            font-weight: 700;
            width: 100%;
            padding: 10px;
        }

        .btn-login:hover {
            background: var(--c-navy-dark);
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="card login-card">
        <div class="login-header">
            <i class="fas fa-calendar-week fa-2x mb-2"></i>
            <h1>SCRUM Hebdo</h1>
            <span class="badge-app">AUXFIN BF</span>
        </div>

        <div class="card-body p-4">
            <h5 class="mb-3 text-center text-secondary">Connexion</h5>

            @if ($errors->any())
                <div class="alert alert-danger py-2">
                    @foreach ($errors->all() as $error)
                        <div><i class="fas fa-exclamation-circle me-1"></i>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="email">
                        <i class="fas fa-envelope me-1 text-secondary"></i>Adresse e-mail
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        autofocus
                        autocomplete="email"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="password">
                        <i class="fas fa-lock me-1 text-secondary"></i>Mot de passe
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        autocomplete="current-password"
                        required>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Se souvenir de moi</label>
                </div>

                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
</body>

</html>
