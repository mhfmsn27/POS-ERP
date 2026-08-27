<div class="app-sidebar__overlay" data-bs-toggle="sidebar"></div>
<div class="sticky">
    <aside class="app-sidebar sidebar-scroll">
        <div class="main-sidebar-header active">
            <a class="desktop-logo logo-light active" href="{{route('admin.dashboard')}}"><img src="{{asset('admin/img/brand/logo.webp')}}" class="main-logo" alt="logo"></a>
            <a class="desktop-logo logo-dark active" href="{{route('admin.dashboard')}}"><img src="{{asset('admin/img/brand/logo-white.png')}}" class="main-logo" alt="logo"></a>
            <a class="logo-icon mobile-logo icon-light active" href="{{route('admin.dashboard')}}"><img src="{{asset('admin/img/brand/favicon.png')}}" alt="logo"></a>
            <a class="logo-icon mobile-logo icon-dark active" href="{{route('admin.dashboard')}}"><img src="{{asset('admin/img/brand/favicon-white.png')}}" alt="logo"></a>
        </div>
        <div class="main-sidemenu">
            <div class="main-sidebar-loggedin">
                <div class="app-sidebar__user">
                    <div class="dropdown user-pro-body text-center">
                        <div class="user-pic">
                            <img src="{{asset(auth()->user()->image_data)}}" alt="user-img" class="rounded-circle mCS_img_loaded">
                        </div>
                        <div class="user-info">
                            <h6 class=" mb-0 text-dark">{{auth()->user()->name}}</h6>
                            <span class="text-muted app-sidebar__user-name text-sm">{{auth()->user()->email}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sidebar-navs">
                <ul class="nav  nav-pills-circle d-flex justify-content-center">
                    <li class="nav-item" >
                        <a class="nav-link text-center m-2" href="{{route('administrator.setting')}}">
                            <i class="fe fe-settings"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-center m-2" href="{{route('admin.profile')}}">
                            <i class="fe fe-user"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-center m-2" href="{{route('logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" role="button">
                            <i class="fe fe-power"></i>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
            <div class="slide-left disabled" id="slide-left"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z" />
                </svg></div>
            <ul class="side-menu ">
                <li class="slide">
                    <a class="side-menu__item" href="{{route('admin.dashboard')}}"><i class="side-menu__icon fe fe-airplay"></i><span class="side-menu__label">Dashboard</span></a>
                </li>
                <li class="slide">
                    <a class="side-menu__item" href="{{route('admin.package.index')}}">
                        <i class="side-menu__icon fe fe-box"></i>
                        <span class="side-menu__label">Paket Langganan</span>
                    </a>
                </li>
                <li class="slide">
                    <a class="side-menu__item" href="{{route('admin.merchant')}}">
                        <i class="side-menu__icon fe fe-award "></i>
                        <span class="side-menu__label">Data Merchant</span>
                    </a>

                </li>
                <li class="slide">
                    <a class="side-menu__item" href="{{route('admin.transaction.package')}}">
                        <i class="side-menu__icon fe fe-layers "></i>
                        <span class="side-menu__label">Transaksi Langganan</span>
                    </a>
                </li>
                <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);"><i class="side-menu__icon fe fe-bell"></i><span class="side-menu__label">Whatsapp Notification</span><i class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        <li class="side-menu__label1"><a href="javascript:void(0);">Device</a></li>
                        <li><a class="slide-item" href="{{route('admin.device')}}">Whatsapp Device</a></li> 
                        <li><a class="slide-item" href="{{route('admin.template')}}">Template Notifikasi </a></li> 
                    </ul>
                </li> 
                <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);"><i class="side-menu__icon fe fe-settings"></i><span class="side-menu__label">Pengaturan</span><i class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        <li class="side-menu__label1"><a href="javascript:void(0);">Pengaturan</a></li>
                        <li><a class="slide-item" href="{{route('administrator.user')}}">Data Pengguna</a></li> 
                        <li><a class="slide-item" href="{{route('administrator.setting')}}">Pengaturan Internal</a></li> 
                        <li><a class="slide-item" href="{{route('admin.notification')}}">Pengaturan Notifikasi</a></li> 
                    </ul>
                </li> 
            </ul>

            <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z" />
                </svg></div>
        </div>
    </aside>
</div>