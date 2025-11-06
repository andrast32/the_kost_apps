<div class="container">
    <div class="row">
        <div class="col-12">
            <nav class="main-nav">

                <a href="/" class="logo">
                    <h1>The_Kost</h1>
                </a>

                <!-- menu start -->
                <ul class="nav">
                    <li>
                        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('kamar') }}" class="{{ request()->routeIs('kamar') ? 'active' : '' }}">
                            Kamar
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">
                            Hubungi kami
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">
                            <i class="fas fa-sign-in-alt"></i>
                            Login
                        </a>
                    </li>
                    <li></li>

                </ul>
                <a class="menu-trigger">
                    <span>Menu</span>
                </a>
                <!-- menu end -->
            </nav>
        </div>
    </div>
</div>
