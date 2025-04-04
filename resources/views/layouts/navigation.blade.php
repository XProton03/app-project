<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            {{-- <img src="{{ asset('dist/assets/images/icon.png') }}" class="img-fluid" alt="logo"> --}}
            <h4>DEA GROUP</h4>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                <li class="pc-item">
                    <a href="{{ route('dashboard') }}"
                        class="{{ request()->routeIs('dashboard') ? 'active' : '' }} pc-link">
                        <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>
                <li class="pc-item">
                    <a href="{{ route('prospect') }}"
                        class="{{ request()->routeIs('prospect') ? 'active' : '' }} pc-link">
                        <span class="pc-micon"><i class="ti ti-database"></i></span>
                        <span class="pc-mtext">Data Prospect</span>
                    </a>
                </li>
                <li class="pc-item">
                    <a href="{{ route('activities') }}"
                        class="{{ request()->routeIs('activities') ? 'active' : '' }} pc-link">
                        <span class="pc-micon"><i class="ti ti-history"></i></span>
                        <span class="pc-mtext">Activities</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<header class="pc-header">
    <div class="header-wrapper"> <!-- [Mobile Media Block] start -->
        <div class="me-auto pc-mob-drp">
            <ul class="list-unstyled">
                <!-- ======= Menu collapse Icon ===== -->
                <li class="pc-h-item pc-sidebar-collapse">
                    <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
                <li class="pc-h-item pc-sidebar-popup">
                    <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
                <li class="dropdown pc-h-item d-inline-flex d-md-none">
                    <a class="pc-head-link dropdown-toggle arrow-none m-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-search"></i>
                    </a>
                </li>
            </ul>
        </div>
        <!-- [Mobile Media Block end] -->
        <div class="ms-auto">
            <ul class="list-unstyled">
                <li class="dropdown pc-h-item header-user-profile">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
                        <img src="{{ asset('dist/assets/images/user/avatar-2.jpg') }}" alt="user-image"
                            class="user-avtar">
                        <span>Guest</span>
                    </a>
                </li>
                <li class="pc-h-item">
                    <a href="/admin" class="pc-head-link">
                        <i class="ti ti-login"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>
<!-- [ Header ] end -->