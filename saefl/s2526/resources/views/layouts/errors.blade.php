<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>SAEFL, Ha pasado algo inusual</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            margin-right: auto;
            margin-left: auto;
            padding-right: 15px;
            padding-left: 15px;
        }

        .navbar {
            padding: 1rem 0;
            color: #fff;
            text-align: center;
        }

        .bg-success {
            background-color: rgb(28, 69, 23) !important;
        }

        .navbar-brand {
            font-size: 1.25rem;
            text-decoration: none;
            color: #fff;
            font-weight: bold;
        }

        .py-4 {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }

        .mt-5 {
            margin-top: 3rem;
        }

        .text-center {
            text-align: center;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: .25rem;
        }

        .alert-danger {
            color: #842029;
            background-color: #f8d7da;
            border-color: #f5c2c7;
        }

        .alert-heading {
            margin-top: 0;
            color: inherit;
            font-size: 1.5rem;
            margin-bottom: .5rem;
        }

        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            padding: 10px 0;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }

        .text-muted {
            color: #6c757d;
        }
    </style>
</head>

<body>

    <nav class="navbar bg-success">
        <div class="container">
            <span class="navbar-brand">U.E Colegio Fray Luis Amigó</span>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>

    <footer>
        <div class="container">
            <small class="text-muted">© {{ date('Y') }} U.E. Colegio Fray Luis Amigó. Todos los derechos
                reservados.</small>
        </div>
    </footer>
</body>

</html>
