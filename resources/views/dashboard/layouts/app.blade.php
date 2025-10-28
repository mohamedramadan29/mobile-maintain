<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="rtl">

@include('dashboard.layouts._head_scripts')

<body class="vertical-layout vertical-menu-modern 2-columns menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu-modern" data-col="2-columns">
    <!-- fixed-top-->
    @include('dashboard.layouts._header')
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    @include('dashboard.layouts._sidebar')
    @yield('content')

    <!---------------------------- Success Failes MEssages  ------------------>
    @if (Session::has('Success_message'))
        @php
            toastify()->success(\Illuminate\Support\Facades\Session::get('Success_message'));
        @endphp
    @endif
    @if (Session::has('Error_message'))
        <div class="alert alert-danger">
            {{ \Illuminate\Support\Facades\Session::get('Error_message') }}
        </div>
        @php

            // dd('Reached Error_message block', \Illuminate\Support\Facades\Session::get('Error_message'));
            //  toastify()->error(\Illuminate\Support\Facades\Session::get('Error_message'));
        @endphp
    @endif
    {{-- @if ($errors->any())
        @foreach ($errors->all() as $error)
            @php
                toastify()->error($error);
            @endphp
        @endforeach
    @endif --}}
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    @include('dashboard.layouts._footer')
    @include('dashboard.layouts._footer_scripts')
</body>

</html>
