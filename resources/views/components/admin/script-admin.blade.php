<!-- jQuery -->
<script src="{{ asset('UI/dashboard/plugins/jquery/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('UI/dashboard/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
$.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('UI/dashboard/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{ asset('UI/dashboard/plugins/chart.js/Chart.min.js') }}"></script>
<!-- Sparkline -->
<script src="{{ asset('UI/dashboard/plugins/sparklines/sparkline.js') }}"></script>
<!-- JQVMap -->
<script src="{{ asset('UI/dashboard/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{ asset('UI/dashboard/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
<!-- jQuery Knob Chart -->
<script src="{{ asset('UI/dashboard/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ asset('UI/dashboard/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('UI/dashboard/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('UI/dashboard/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- Summernote -->
<script src="{{ asset('UI/dashboard/plugins/summernote/summernote-bs4.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('UI/dashboard/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- DataTables  & Plugins -->
<script src="{{ asset('UI/dashboard/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('UI/dashboard/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('UI/dashboard/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('UI/dashboard/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('UI/dashboard/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('UI/dashboard/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('UI/dashboard/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('UI/dashboard/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('UI/dashboard/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('UI/dashboard/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('UI/dashboard/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('UI/dashboard/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

<!-- SweetAlert2 -->
<script src="{{ asset('UI/dashboard/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Toastr -->
<script src="{{ asset('UI/dashboard/plugins/toastr/toastr.min.js') }}"></script>

<!-- AdminLTE App -->
<script src="{{ asset('UI/dashboard/dist/js/adminlte.js') }}"></script>

<!-- buat tabel -->
<script>
    $(function () {

        $('#laporan').DataTable({
            "paging"        : true,
            "lengthChange"  : true,
            "searching"     : true,
            "ordering"      : true,
            "info"          : true,
            "autoWidth"     : false,
            "responsive"    : true,
            "buttons"       : [
                                "excel", "pdf", "print"
                            ]
        }).buttons().container().appendTo('#laporan_wrapper .col-md-6:eq(0)');

        $('#data').DataTable({
            "paging"        : true,
            "lengthChange"  : true,
            "searching"     : true,
            "ordering"      : true,
            "info"          : true,
            "autoWidth"     : false,
            "responsive"    : true,
        });

    })
</script>

<script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });
</script>

@if (session('alert'))
    <script>
        Toast.fire({
            icon                : "{{ session('alert')['icon'] }}",
            title               : "{{ session('alert')['title'] }}",
        })
    </script>
@endif

@if ($errors->any())
    <script>

        let errorMessages = @json($errors->all());

        let combined = errorMessages.json("\n");

        Toast.fire({
            icon: 'error',
            title: combined,
        });

    </script>
@endif

<script>

    // preview image
    function previewImage(input, targetId) {
        const preview = document.getElementById(targetId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // format uang
    $(document).on('keyup', '.input-harga', function() {
        let value = $(this).val();
        let number_string = value.replace(/[^,\d]/g, '').toString();
        let split = number_string.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        $(this).val(rupiah);
    });

</script>
