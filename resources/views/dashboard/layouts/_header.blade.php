<nav
    class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-semi-dark navbar-shadow">
    <div class="navbar-wrapper">
        <div class="navbar-header">
            <ul class="flex-row nav navbar-nav">
                <li class="mr-auto nav-item mobile-menu d-md-none"><a class="nav-link nav-menu-main menu-toggle hidden-xs"
                        href="#"><i class="ft-menu font-large-1"></i></a></li>
                <li class="mr-auto nav-item">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <img class="brand-logo" alt="modern admin logo"
                            src="{{ asset('assets/admin/') }}/images/logo_mobile.png">
                        <h3 class="brand-text"> بصمة الفرع الثاني </h3>
                    </a>
                </li>
                <li class="float-right nav-item d-none d-md-block"><a class="pr-0 nav-link modern-nav-toggle"
                        data-toggle="collapse"><i class="toggle-icon ft-toggle-right font-medium-3 white"
                            data-ticon="ft-toggle-right"></i></a></li>
                <li class="nav-item d-md-none">
                    <a class="nav-link open-navbar-container" data-toggle="collapse" data-target="#navbar-mobile"><i
                            class="la la-ellipsis-v"></i></a>
                </li>
            </ul>
        </div>
        <div class="navbar-container content">
            <div class="collapse navbar-collapse" id="navbar-mobile">
                <ul class="float-left mr-auto nav navbar-nav">
                    <li class="nav-item d-none d-md-block"><a class="nav-link nav-link-expand" href="#"><i
                                class="ficon ft-maximize"></i></a></li>
                </ul>
                <ul class="float-right nav navbar-nav">
                    <li class="dropdown dropdown-user nav-item">
                        <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                            <span class="mr-1"> مرحبا ,
                                <span class="user-name text-bold-700"> {{ Auth::user()->name }} </span>
                            </span>
                            <span class="avatar avatar-online">
                                <img src="{{ asset('assets/admin/') }}/images/portrait/small/avatar-s-19.png"
                                    alt="avatar"><i></i></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('dashboard.update_profile') }}"><i
                                    class="ft-user"></i> تعديل البيانات </a>
                            <a class="dropdown-item" href="{{ route('dashboard.update_password') }}"><i
                                    class="ft-lock"></i> تغير كلمة المرور </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('dashboard.logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item" href="#"><i class="ft-power"></i>
                                    تسجبل الخروج </button>
                            </form>

                        </div>
                    </li>
                    <li class="dropdown dropdown-notification nav-item">
                        @php
                            $notifications = auth()->user()->unreadNotifications;
                        @endphp
                        <a class="nav-link nav-link-label" href="#" data-toggle="dropdown">
                            <i class="ficon ft-bell"></i>
                            @if ($notifications->count() > 0)
                                <span class="badge badge-pill badge-default badge-danger badge-up badge-glow">
                                    {{ $notifications->count() }}
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                            <li class="dropdown-menu-header">
                                <h6 class="m-0 dropdown-header">
                                    <span class="grey darken-2"> الإشعارات </span>
                                </h6>
                                <span class="float-right m-0 notification-tag badge badge-default badge-danger">
                                    {{ $notifications->count() }} جديد
                                </span>
                            </li>
                            <li class="scrollable-container media-list w-100">
                                @if ($notifications->count() > 0)
                                    @foreach ($notifications as $notification)
                                        <a href="{{ route('dashboard.invoices.invoice-haif-time') }}">
                                            <div class="media">
                                                <div class="media-left align-self-center">
                                                    <i class="ft-bell icon-bg-circle bg-cyan"></i>
                                                </div>
                                                <div class="media-body">
                                                    <h6 class="media-heading">{{ $notification->data['message'] }}</h6>
                                                    <small>
                                                        <time class="media-meta text-muted">
                                                            {{ $notification->created_at->diffForHumans() }}
                                                        </time>
                                                    </small>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                @else
                                    <a href="#">
                                        <div class="media">
                                            <div class="media-left align-self-center">
                                                <i class="ft-bell icon-bg-circle bg-cyan"></i>
                                            </div>
                                            <div class="media-body">
                                                <h6 class="media-heading"> لا يوجد لديك اشعارات جديدة في الوقت الحالي
                                                </h6>

                                            </div>
                                        </div>
                                    </a>
                                @endif

                            </li>
                            <li class="dropdown-menu-footer">
                                <a id="mark-all-read" class="text-center dropdown-item text-muted"
                                    href="{{ route('dashboard.all_read') }}">
                                    جعل جميع الإشعارات مقروءة
                                </a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>
        </div>
    </div>
</nav>
