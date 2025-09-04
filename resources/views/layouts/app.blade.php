<!DOCTYPE html>
<html lang="en">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
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
    <script src="{{ asset('admin/toastr/toastr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('datepicker/js/bootstrap-datepicker.min.js') }}"></script>

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

        function delete_conf_common(record_id, model, display_title, table_id_or_url = '') {
            $('.module_action').html('Delete');
            $('#module_title').html(" " + display_title);
            table_id_or_url = window.location.href;
            $('#delete-btn').attr('onclick', 'delete_common("' + record_id + '","' + model + '","' + table_id_or_url +
                '","' + display_title + '")');
            $('#delete_confirm').modal('show');
            return true;
        }

        function delete_common(record_id, model, table_id_or_url = '', display_title = '') {
            var token = "{{ csrf_token() }}";
            $.ajax({
                type: "POST",
                url: "{{ url(config('app.url')) }}delete_common",
                data: {
                    _token: token,
                    record_id: record_id,
                    model: model,
                    table_id_or_url: table_id_or_url,
                    display_title: display_title
                },
                dataType: "json",
                success: function(data) {
                    console.log(data);
                    $('#delete_confirm').modal('hide');
                    if (data.status == 'success') {
                        show_notification(data.status, data.message);
                        $('#cover-spin').hide();
                        setTimeout(function() {
                            window.location.href = table_id_or_url;
                        }, 1000);
                    } else {
                        show_notification(data.status, data.message);
                    }
                },
                complete: function(e) {

                },
                error: function(e) {
                    $('#cover-spin').hide();
                }
            });
        }

        function show_notification(type, message) {
            if (type == 'success') {
                toastr.success(message);
            } else if (type == 'error') {
                toastr.error(message);
            } else if (type == 'warning') {
                toastr.warning(message);
            } else if (type == 'information') {
                toastr.info(message);
            }
        }
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'dd-mm-yyyy', // Adjust the format as per your requirement
                autoclose: true
            });

            // Fix menu collapse functionality
            $('[data-toggle="collapse"]').on('click', function(e) {
                e.preventDefault();
                var target = $(this).attr('data-target');
                $(target).collapse('toggle');
                
                // Toggle collapsed class on the link
                $(this).toggleClass('collapsed');
                
                // Update aria-expanded attribute
                var isExpanded = $(this).attr('aria-expanded') === 'true';
                $(this).attr('aria-expanded', !isExpanded);
            });
        });
    </script>
</body>

</html>
