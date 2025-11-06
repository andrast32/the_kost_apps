<!DOCTYPE html>
<html lang="en">

    <head>

        <x-dashboard.head-dashboard />

        <title>{{ config('app.name') }} | {{ $title ?? 'Dashboard' }}</title>

    </head>

    <body>

        <!-- header area start -->
            <header class="header-area header-sticky">
                <x-dashboard.navbar-dashboard />
            </header>
        <!-- header area end -->

        <!-- Page Content start-->

            <div>
                {{ $slot }}
            </div>

        <!-- Page Content end-->

        <!-- footer start -->
            <footer>
                <div class="container">
                    <div class="col-lg-8">
                        <p>
                            Copyright © 2048 {{ config('app.name') }} Agency Co., Ltd. All rights reserved. Design by: <a rel="nofollow" href="#">andrast_32</a>
                        </p>
                    </div>
                </div>
            </footer>
        <!-- footer end -->

        <!-- script area start-->
            <div>
                <x-dashboard.script-dashboard />
            </div>
        <!-- script area end -->

    </body>

</html>
