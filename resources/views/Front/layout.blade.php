<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Roza Store')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Purple Admin CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/front.css') }}">

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Dancing+Script:wght@400;700&family=Lato:wght@400&display=swap"
        rel="stylesheet">


    @stack('styles')
</head>

<body class="front-body">

    <nav class="navbar navbar-expand-lg front-navbar fixed-top">
        <div class="container">
            <a class="navbar-brand text-white" href="{{ route('front.home') }}">
                <i class="mdi mdi-flower-outline me-1"></i> Roza
            </a>

            <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse"
                data-bs-target="#frontNavbar" aria-controls="frontNavbar" aria-expanded="false">
                <span class="mdi mdi-menu"></span>
            </button>

            <div class="collapse navbar-collapse" id="frontNavbar">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <li class="nav-item">
                        <a href="{{ route('front.home') }}"
                            class="nav-link {{ request()->routeIs('front.home') ? 'active' : '' }}">
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('front.products.index') }}"
                            class="nav-link {{ request()->routeIs('front.products.*') ? 'active' : '' }}">
                            Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('front.cart.index') }}"
                            class="nav-link {{ request()->routeIs('front.cart.*') ? 'active' : '' }}">
                            Cart
                        </a>
                    </li>

                    @auth
                        <li class="nav-item dropdown front-account-dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
                                id="accountDropdown" data-bs-toggle="dropdown">
                                <i class="mdi mdi-account-circle-outline me-1"></i>
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
                                <li><a class="dropdown-item" href="{{ route('front.account.profile') }}">Profile</a></li>
                                <li><a class="dropdown-item" href="{{ route('front.account.orders') }}">My Orders</a></li>
                                <li><a class="dropdown-item" href="{{ route('front.account.address') }}">Address</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('auth.logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item logout">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                            <a href="{{ route('auth.login') }}" class="btn btn-sm btn-outline-light">
                                Login
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    {{-- ✅ Floating alert component --}}
    @include('components.alerts.front.floating_alert')

    <main class="front-main">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <footer class="front-footer py-4">
        <div class="container text-center">
            © {{ date('Y') }} Roza Store · Inspired by Purple Admin & soft pastel decor.
        </div>
    </footer>

    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>
    <script src="{{ asset('assets/js/front/common.js') }}"></script>
    @stack('scripts')

</body>

</html>
