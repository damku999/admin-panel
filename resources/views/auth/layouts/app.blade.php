<!DOCTYPE html>
<html lang="en">

{{-- Head Before AUTH--}}
@include('auth.includes.head')

<body>

    {{-- Content Goes Here FOR Before AUTH --}}
    @yield('content')

    {{-- Scripts Before AUTH --}}
    @include('auth.includes.scripts')

</body>

</html>