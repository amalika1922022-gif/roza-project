<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">

        <li class="nav-item nav-profile">
            <a href="{{ route('admin.dashboard') }}" class="nav-link">
                <div class="nav-profile-image">
                    <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile" />
                    <span class="login-status online"></span>
                </div>
                <div class="nav-profile-text d-flex flex-column">
                    <span class="font-weight-bold mb-2">Admin</span>
                    <span class="text-secondary text-small">Admin Panel</span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
        </li>

        {{-- Dashboard --}}
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>

        {{-- Categories --}}
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.categories.index') }}">
                <span class="menu-title">Categories</span>
                <i class="mdi mdi-contacts menu-icon"></i>
            </a>
        </li>

        {{-- Products --}}
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.products.index') }}">
                <span class="menu-title">Products</span>
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
            </a>
        </li>

        {{-- Orders --}}
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.orders.index') }}">
                <span class="menu-title">Orders</span>
                <i class="mdi mdi-table-large menu-icon"></i>
            </a>
        </li>

        {{-- Users --}}
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.users.index') }}">
                <span class="menu-title">Users</span>
                <i class="mdi mdi-lock menu-icon"></i>
            </a>
        </li>

    </ul>
</nav>
