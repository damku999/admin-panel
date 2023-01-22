<!DOCTYPE html>
<html lang="en">

{{-- Include Head --}}
@include('common.head')

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('common.sidebar')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                @include('common.header')
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                @yield('content')
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            @include('common.footer')
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    @include('common.logout-modal')

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('js/app.js') }}"></script>


    <!-- Custom scripts for all pages-->
    <script src="{{ asset('admin/js/sb-admin-2.min.js') }}"></script>

    @yield('scripts')
    <script>
        function filterDataAjax(url, search_serialized = null) {
            $.ajax({
                async: true,
                type: "GET",
                url: "{{ env('APP_URL') }}/" + url,
                data: search_serialized,
                success: function(res) {
                    $("#list_load").html(res);
                    // $(table_id).load(location.href + ' '+table_id);
                },
                error: function(xhr, status, error) {
                    $('#cover-spin').hide();
                },
                complete: function(result) {
                    if (result.responseText == '{"error":"Unauthenticated."}') {
                        window.location.href = "login";
                    }

                    if (search_serialized == '&reset=yes') {
                        if ($('#search_form select[name=product_type]').length) {
                            $('#search_form select[name=product_type]').val('');
                        }
                        if ($('#search_form select[name=packaging_type]').length) {
                            $('#search_form select[name=packaging_type]').val('');
                        }
                        $('.select2').select2().on('select2:close', function name(e) {
                            $(this).valid();
                        });
                    }
                    $('#cover-spin').hide();
                }
            });
        }
    </script>
</body>

</html>
