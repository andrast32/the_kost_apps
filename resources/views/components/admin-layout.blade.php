<!DOCTYPE html>
<html lang="en">

    <head>

        <x-admin.header-admin />

        <title>{{ config('app.name') }} | {{ $title ?? 'Dashboard' }}</title>


    </head>

    <body class="hold-transition sidebar-mini layout-fixed sidebar-collapse">

        <div class="wrapper">

            <!-- navbar -->
            <x-admin.navbar-admin />

            <!-- Main Sidebar Container -->
            <x-admin.sidebar-admin />

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h5 class="text-muted">Halaman {{ $title ?? 'Dashboard' }}</h5>
                            </div><!-- /.col -->
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                    <li class="breadcrumb-item active">{{ $title ?? 'Dashboard' }}</li>
                                </ol>
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content-header -->

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                {{ $slot }}
                            </div>
                        </div>
                    </div>
                </section>
                <!-- /.content -->
            </div>

            <!-- /.content-wrapper -->
            <footer class="main-footer">
                <strong> Copyright © 2026 {{ config('app.name') }} Agency Co., Ltd. All rights reserved. Design by: <a rel="nofollow" href="#">andrast_32</a>.</strong>
            </footer>

        </div>
        <!-- ./wrapper -->

        <x-admin.script-admin />

    </body>

</html>
