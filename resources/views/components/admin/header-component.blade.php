<div class="iq-top-navbar">
    <div class="iq-navbar-custom">
        <div class="iq-sidebar-logo">
            <div class="top-logo">
                <a href="{{ route('index') }}" class="logo">
                    <span>{{$settings->name}}</span>
                </a>
            </div>
        </div>
        <div class="navbar-breadcrumb">
            <h5 class="mb-0 pageName"></h5>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Toko</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$storeSettings->name}} </li>
                </ul>
            </nav>
        </div>
        <nav class="navbar navbar-expand-lg navbar-light p-0">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="ri-menu-3-line"></i>
            </button>
            <button class="navbar-toggler iq-menu-bt align-self-center" type="button">
                <div class="wrapper-menu">
                    <div class="menu-close"><i class="ri-arrow-left-line"></i></div>
                    <div class="menu-open"><i class="ri-arrow-right-line"></i></div>
                </div>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto navbar-list">
                    <li class="nav-item dropdown">
                        @if (Auth()->user()->can('Check Int Check Out') == true)
                        <div class="dropdown d-none d-lg-inline-block button-header">
                            @if ($attendance == null)
                            <a href="javascript:void(0)" id="checkint_attendance" class="btn  btn-block btn-primary mt-3"> {{__('attendance.check_int')}}</a>
                            <a href="javascript:void(0)" id="checkout_attendance" class="btn btn-block btn-danger mt-3 d-none"> {{__('attendance.check_out')}}</a>
                            <a href="javascript:void(0)" class="btn btn-block btn-success mt-3 d-none" id="attendance_clear"> {{__('attendance.end')}} </a>
                            @elseif($attendance->check_out == null)
                            <a href="javascript:void(0)" id="checkout_attendance" class="btn btn-block btn-danger mt-3"> {{__('attendance.check_out')}}</a>
                            <a href="javascript:void(0)" class="btn btn-block btn-success mt-3 d-none" id="attendance_clear"> {{__('attendance.end')}}</a>
                            @else
                            <a href="javascript:void(0)" class="btn btn-block btn-success mt-3" id="attendance_clear"> {{__('attendance.end')}}</a>
                            @endif
                        </div>
                        @endif
                    </li>

                    <li class="nav-item dropdown">
                        <a href="#" class="search-toggle iq-waves-effect">
                            <i class="fa fa-flag"></i>
                        </a>
                        <div class="iq-sub-dropdown">
                            <div class="card shadow-none m-0">
                                <div class="card-body p-0 ">
                                    <div class="bg-primary p-3">
                                        <h5 class="mb-0 text-white">Semua Bahasa <small class="badge  badge-light float-right pt-1">{{app()->getLocale()}}</small></h5>
                                    </div>
                                    @foreach($lang as $key => $value)
                                    <a href="{{ url('locale',$key) }}" class="iq-sub-card">
                                        <div class="media align-items-center">
                                            <div class="">
                                                <img class="avatar-40 rounded" src="{{ asset('assets/icon/lang/'.$key.'.png') }}" alt="{{ __($value) }}">
                                            </div>
                                            <div class="media-body ml-3">
                                                <h6 class="mb-0 ">{{ __($value) }}</h6>
                                            </div>
                                        </div>
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" class="search-toggle iq-waves-effect">
                            <i class="ri-notification-2-line"></i>
                            <span class="badge badge-pill badge-primary badge-up count-mail">{{number_format($totalStock)}}</span>
                        </a>
                        <div class="iq-sub-dropdown">
                            <div class="card card-block card-stretch card-height shadow-none m-0">
                                <div class="card-body p-0 ">
                                    <div class="bg-primary p-3">
                                        <h5 class="mb-0 text-white">Stok Di Bawah Mininum<small class="badge  badge-light float-right pt-1">{{number_format($totalStock)}}</small></h5>
                                    </div>
                                    @foreach($product as $p)
                                    <!-- <a href="#" class="iq-sub-card">
                                        <div class="media align-items-center">
                                            <div class="">
                                                <img class="avatar-40 rounded" src="{{$p['image']}}" alt="">
                                            </div>
                                            <div class="media-body ml-3">
                                                <h6 class="mb-0 ">{{$p['name']}}</h6>
                                                <small class="float-left font-size-12">Tersisa {{$p['stock']}} </small>
                                            </div>
                                        </div>
                                    </a> -->
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item iq-full-screen"><a href="#" class="iq-waves-effect" id="btnFullscreen"><i class="ri-fullscreen-line"></i></a></li>
                </ul>
            </div>
            <ul class="navbar-list">
                <li>
                    <a href="#" class="search-toggle iq-waves-effect bg-primary text-white"><img src="{{asset(Auth()->user()->photo)}}" class="img-fluid rounded" alt="{{Auth()->user()->name}}"></a>
                    <div class="iq-sub-dropdown iq-user-dropdown">
                        <div class="card shadow-none m-0">
                            <div class="card-body p-0 ">
                                <div class="bg-primary p-3">
                                    <h5 class="mb-0 text-white line-height">Hallo {{Auth()->user()->name}}</h5>
                                    <span class="text-white font-size-12">Sedang Online</span>
                                </div>
                                <a href="{{route('profile')}}" class="iq-sub-card iq-bg-primary-success-hover">
                                    <div class="media align-items-center">
                                        <div class="rounded card-icon iq-bg-success">
                                            <i class="ri-profile-line"></i>
                                        </div>
                                        <div class="media-body ml-3">
                                            <h6 class="mb-0 ">Edit Profile</h6>
                                            <p class="mb-0 font-size-12">Edit Personal Profil Anda</p>
                                        </div>
                                    </div>
                                </a>

                                <div class="d-inline-block w-100 text-center p-3">
                                    <a class="iq-bg-danger iq-sign-btn" href="{{route('logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" role="button">Keluar Aplikasi<i class="ri-login-box-line ml-2"></i></a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</div>