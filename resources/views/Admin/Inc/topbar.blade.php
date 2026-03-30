<nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        <a class="navbar-brand brand-logo" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('assets/images/logo.svg') }}" alt="logo" />
        </a>

        <a class="navbar-brand brand-logo-mini" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('assets/images/logo-mini.svg') }}" alt="logo" />
        </a>
    </div>

    <div class="navbar-menu-wrapper d-flex align-items-stretch justify-content-between">
        {{-- LEFT SIDE --}}
        <div class="d-flex align-items-center">

            {{-- View Storefront --}}
            <a href="{{ route('front.home') }}"
               class="btn btn-sm btn-gradient-primary d-none d-sm-inline-flex align-items-center">
                <i class="mdi mdi-storefront-outline me-2"></i>
                View Storefront
            </a>

            <a href="{{ route('front.home') }}"
               class="btn btn-sm btn-gradient-primary d-inline-flex d-sm-none ms-2">
                <i class="mdi mdi-storefront-outline"></i>
            </a>

            {{-- Search --}}
            <div class="search-field flex-grow-1 ms-3">
                <form class="d-flex align-items-center h-100" action="#">
                    <div class="input-group w-100 admin-search-input-group">
                        <span class="input-group-text bg-transparent border-0 px-2">
                            <i class="mdi mdi-magnify text-muted"></i>
                        </span>

                        <input type="text"
                               class="form-control bg-transparent border-0 py-1"
                               placeholder="Search">
                    </div>
                </form>
            </div>

        </div>

        {{-- RIGHT SIDE --}}
        <div class="d-flex align-items-center">

            <ul class="navbar-nav navbar-nav-right">

                {{-- Messages --}}
                <li class="nav-item dropdown">
                    <a class="nav-link count-indicator dropdown-toggle"
                       id="messageDropdown"
                       href="#"
                       data-bs-toggle="dropdown">
                        <i class="mdi mdi-email-outline"></i>
                        <span class="count-symbol bg-warning"></span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end navbar-dropdown preview-list">
                        <h6 class="p-3 mb-0">Messages</h6>
                        <div class="dropdown-divider"></div>
                        <p class="text-center p-3 small text-muted mb-0">No new messages</p>
                    </div>
                </li>

                {{-- Notifications --}}
                <li class="nav-item dropdown">
                    <a class="nav-link count-indicator dropdown-toggle"
                       id="notificationDropdown"
                       href="#"
                       data-bs-toggle="dropdown">
                        <i class="mdi mdi-bell-outline"></i>
                        <span class="count-symbol bg-danger"></span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end navbar-dropdown preview-list">
                        <h6 class="p-3 mb-0">Notifications</h6>
                        <div class="dropdown-divider"></div>
                        <p class="p-3 mb-0 small text-center text-muted">No notifications</p>
                    </div>
                </li>

            </ul>

            {{-- Logout --}}
            <form method="POST" action="{{ route('auth.logout') }}" class="ms-2">
                @csrf

                {{-- Desktop --}}
                <button type="submit"
                        class="btn btn-sm btn-logout d-none d-sm-inline-flex align-items-center">
                    <i class="mdi mdi-logout me-2"></i>
                    Logout
                </button>

                {{-- Mobile --}}
                <button type="submit"
                        class="btn btn-sm btn-logout d-inline-flex d-sm-none">
                    <i class="mdi mdi-logout"></i>
                </button>
            </form>

            {{-- Menu --}}
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center ms-2"
                    type="button"
                    data-toggle="offcanvas">
                <span class="mdi mdi-menu"></span>
            </button>

        </div>
    </div>
</nav>
