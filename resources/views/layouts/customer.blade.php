<!DOCTYPE html>
<html lang="en">
{{-- Include Customer Head --}}
@include('common.customer-head')

<body id="page-top">

    <!-- Customer Layout - No Sidebar -->
    <div id="wrapper">

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Customer Header -->
                @include('customer.partials.header')
                <!-- End of Customer Header -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            @auth('customer')
                @include('customer.partials.footer')
            @endauth
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

    <!-- Customer Logout Modal-->
    @include('customer.partials.logout-modal')

    <!-- Bootstrap 5 JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous">
    </script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Toastr -->
    <script src="{{ asset('admin/toastr/toastr.min.js') }}"></script>

    @yield('scripts')

    <script>
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

        // Show session messages as notifications
        @if (session('message'))
            show_notification('success', '{{ session('message') }}');
        @endif

        @if (session('error'))
            show_notification('error', '{{ session('error') }}');
        @endif

        @if (session('info'))
            show_notification('info', '{{ session('info') }}');
        @endif
    </script>
</body>

</html>
