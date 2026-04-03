<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Purple Admin</title>

    {{-- Vendor CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}">

    {{-- Plugin CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">

    {{-- Theme + Admin CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />

    {{-- Page Styles (per page) --}}
    @stack('styles')
</head>

<body>
    <div class="container-scroller">
        @include('Admin.inc.topbar')

        <div class="container-fluid page-body-wrapper">
            @include('Admin.inc.sidebar')
            @include('Admin.inc.mainpanel')
        </div>
    </div>

    {{-- Vendor JS --}}
    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>

    {{-- Plugin JS --}}
    <script src="{{ asset('assets/vendors/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

    {{-- Template JS --}}
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    <script src="{{ asset('assets/js/todolist.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.cookie.js') }}"></script>

    {{-- Dashboard JS (template) --}}
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- ✅ ملف عام فقط --}}
    {{-- <script src="{{ asset('assets/js/admin/admin.js') }}"></script> --}}

    {{-- ✅ سكربتات الصفحات هون --}}
    @stack('scripts')
</body>

</html>
