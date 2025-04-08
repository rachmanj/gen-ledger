<!-- Navbar -->
<nav class="main-header navbar navbar-expand-md navbar-light navbar-dark layout-fixed">
    <div class="container">
        <a href="/"class="navbar-brand">
            <img src="{{ asset('adminlte/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
                class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text text-white font-weight-light"><strong>PROC</strong> App</span>
        </a>

        <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse"
            aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse order-3" id="navbarCollapse">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="#" class="nav-link">Dashboard</a>
                </li>

                {{-- <a href="#" class="nav-link">Search</a> --}}


                @can('akses_master')
                    @include('layout.partials.menu.master')
                @endcan

                @can('akses_admin')
                    @include('layout.partials.menu.admin')
                @endcan

            </ul>
        </div>

        <!-- Right navbar links -->
        <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
            <li class="nav-item dropdown">
                <a id="profileDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    class="nav-link dropdown-toggle d-flex align-items-center">
                    <i class="fas fa-user-circle mr-2" style="font-size: 1.25rem;"></i>
                    <span>{{ auth()->user()->name }}</span>
                    @if (auth()->user()->project)
                        <span class="ml-1 text-sm text-muted">({{ auth()->user()->project }})</span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-right border-0 shadow" aria-labelledby="profileDropdown">
                    <div class="dropdown-header bg-light py-2">
                        <strong>PROFILE</strong>
                    </div>
                    <a href="{{ route('password.change.form') }}" class="dropdown-item">
                        <i class="fas fa-key mr-2"></i> Change Password
                    </a>
                    <div class="dropdown-divider"></div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <a href="#" class="dropdown-item"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>
<!-- /.navbar -->
