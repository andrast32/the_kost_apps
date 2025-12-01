<!-- Preloader -->
    <!-- <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="{{ asset('UI/utama/assets/images/info-icon-03.png') }}" height="60" width="60">
    </div> -->

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">

        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- Notifications Dropdown Menu -->
            <li class="nav-item dropdown">

                <a class="nav-link" data-toggle="dropdown" href="#">
                    <img src="{{ asset('UI/dashboard/dist/img/avatar5.png') }}" class="img-circle elevation-2 img-sm" alt="User Image" style="margin: 0 10px;">
                    <span>{{ Auth::user()->name }}</span>
                </a>

                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

                    <span class="dropdown-item dropdown-header">User Area</span>

                    <div class="dropdown-divider"></div>

                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="fas fa-user mr-2"></i> Profile
                    </a>

                    <div class="dropdown-divider"></div>

                    <a href="#" class="dropdown-item">
                        <i class="fas fa-download mr-2"></i> Download Database
                    </a>

                    <div class="dropdown-divider"></div>

                    <a href="#" class="dropdown-item">
                        <i class="fas fa-upload mr-2"></i> Upload Database
                    </a>

                    <div class="dropdown-divider"></div>

                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <a :href="route('logout')" class="dropdown-item" onclick="event.preventDefault(); this.closest('form').submit();">
                            <i class="fas fa-power-off mr-2"></i> Logout
                        </a>
                    </form>

                    <div class="dropdown-divider"></div>

                </div>
            </li>
        </ul>

    </nav>
<!-- /.navbar -->
