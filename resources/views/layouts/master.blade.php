<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GAC System')</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        height: 100vh;
    }

    .card {
        color: #FFD700;
    }

    .card-header {
        font-size: 1.5rem;
        font-weight: bold;
        text-align: center;
    }

    label {
        color: #FFD700;
    }

    .text-gray {
        color: #738494;
    }

    a {
        color: #FFD700 !important;
    }

    .nav-item.active {
        border: 1px solid #FFD700;
        background-color: #000b2d;
    }

    .positive-value {
        color: green;
    }

    .negative-value {
        color: red;
    }

</style>
@yield('page-style')

<body>
    @if (auth()->check() && Route::has('login') && route('login') != request()->url())
    @include('layouts.navbar')
    @endif
    @yield('content')
</body>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
@if (auth()->check() && Route::has('login') && route('login') != request()->url())
<script>
    const navHeader = document.getElementById('nav_header');

    navHeader.addEventListener('click', (e) => {
        const navItems = navHeader.getElementsByClassName('nav-item');
        for (let i = 0; i < navItems.length; i++) {
            navItems[i].classList.remove('active');
        }
        e.target.parentElement.classList.add('active');
    });

    window.onload = () => {
        const path = window.location.pathname;
        const navItems = navHeader.getElementsByClassName('nav-item');
        for (let i = 0; i < navItems.length; i++) {
            if (navItems[i].id === 'nav_extrato' && path === '/extrato') {
                navItems[i].classList.add('active');
            } else if (navItems[i].id === 'nav_operar' && path === '/operar') {
                navItems[i].classList.add('active');
            }
        }
    };
</script>
@endif
@yield('page-script')

</html>