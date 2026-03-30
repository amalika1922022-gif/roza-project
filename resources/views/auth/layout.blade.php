<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Auth')</title>

    {{-- نفس ملفات الـ theme اللي عندك --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">

    @stack('styles')
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">

                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo mb-4">
                                <img src="{{ asset('assets/images/logo.svg') }}" alt="logo">
                            </div>

                            {{-- ✅ تنبيه السيشن مرة وحدة فقط --}}
                            @include('Components.alerts.auth.session')

                            @yield('content')
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- plugins:js --}}
    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>

    {{-- inject:js --}}
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    <script src="{{ asset('assets/js/todolist.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.cookie.js') }}"></script>
    <script src="{{ asset('assets/js/auth/auth.js') }}"></script>

    @stack('scripts')
</body>

</html>
