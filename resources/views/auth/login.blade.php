<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion – SCRUM Hebdo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e65c00, #ffb347);
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
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
        }

        .login-header {
            background: linear-gradient(to right, #e65c00, #ffb347);
            border-radius: 16px 16px 0 0;
            padding: 2rem;
            text-align: center;
        }

        .login-header h1 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #000;
            margin: 0;
        }

        .badge-app {
            background: rgba(0, 0, 0, 0.15);
            color: #000;
            font-size: 0.75rem;
            padding: 3px 10px;
            border-radius: 20px;
            margin-top: 4px;
            display: inline-block;
        }

        .btn {
            border-radius: 25px;
        }

        .btn-login {
            background: linear-gradient(to right, #e65c00, #ffb347);
            border: none;
            color: #000;
            font-weight: 600;
            width: 100%;
            padding: 10px;
        }

        .btn-login:hover {
            opacity: 0.9;
            color: #000;
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
