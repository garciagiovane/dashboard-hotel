<!DOCTYPE html>
<html lang={{ App::getLocale() }}>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('pageTitle', 'Hotel')</title>

    @env('production')
    <script src="{{ secure_asset('js/app.js') }}" defer></script>
    <link href="{{ secure_asset('css/app.css') }}" rel="stylesheet">
    @endenv

    @env('dev')
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @endenv

</head>

<body>
    <div class="container">
        <header>
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <a class="navbar-brand" href="/">Home</a>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Quartos
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <a class="dropdown-item" href="/quartos">Listar quartos</a>
                                <a class="dropdown-item" href="/quartos/create">Cadastrar quartos</a>
                                <a class="dropdown-item" href="/quartos?status=AGUARDANDO_LIMPEZA">Manutenção</a>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Serviços
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <a class="dropdown-item" href="/servicos">Listar serviços</a>
                                <a class="dropdown-item" href="/servicos/create">Cadastrar serviços</a>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Reservas
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <a class="dropdown-item" href="/reservations/create">Reservar</a>
                                <a class="dropdown-item" href="/reservations/checkin">Checkin</a>
                                <a class="dropdown-item" href="/reservations/checkout">Checkout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        @yield('show')
    </div>
</body>

</html>
