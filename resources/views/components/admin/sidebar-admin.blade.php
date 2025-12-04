<aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
        <img src="{{ asset('UI/utama/assets/images/info-icon-03.png') }}" alt="The Kost Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-dark">
            <span style="color: #8b0420;">The</span>
            Kost
        </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('UI/dashboard/dist/img/avatar5.png') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="{{ route('profile.edit') }}" class="d-block">{{ Auth::user()->name }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} ">
                        <i class="nav-icon fas fa-home"></i>
                        <p>
                            Home
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.pembayaran') }}" class="nav-link {{ request()->routeIs('admin.pembayaran') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-wallet"></i>
                        <p>
                            Data Pembayaran
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.pemesanan') }}" class="nav-link {{ request()->routeIs('admin.pemesanan') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>
                            Data Pemesanan
                        </p>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('admin.data-kost.*') ? 'menu-open' : '' }}">

                    <a href="#" class="nav-link {{ request()->routeIs('admin.data-kost.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-building"></i>
                        <p>
                            Data Kost
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a href="{{ route('admin.data-kost.kamar.index') }}" class="nav-link {{ request()->routeIs('admin.data-kost.kamar.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Kamar</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.data-kost.fasilitas.index') }}" class="nav-link {{ request()->routeIs('admin.data-kost.fasilitas.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Fasilitas</p>
                            </a>
                        </li>

                    </ul>
                </li>

                <li class="nav-item {{ request()->routeIs('admin.data-user.*') ? 'menu-open' : '' }}">

                    <a href="#" class="nav-link {{ request()->routeIs('admin.data-user.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Data User
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a href="{{ route('admin.data-user.penyewa') }}" class="nav-link {{ request()->routeIs('admin.data-user.penyewa') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Penyewa</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.data-user.petugas') }}" class="nav-link {{ request()->routeIs('admin.data-user.petugas') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Petugas</p>
                            </a>
                        </li>

                    </ul>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->

    </div>
    <!-- /.sidebar -->
</aside>
