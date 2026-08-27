<div class="main-header side-header sticky nav nav-item">
    <div class="container-fluid main-container ">
        <div class="main-header-left ">
            <div class="app-sidebar__toggle mobile-toggle" data-bs-toggle="sidebar">
                <a class="open-toggle" href="javascript:void(0);"><i class="header-icons" data-eva="menu-outline"></i></a>
                <a class="close-toggle" href="javascript:void(0);"><i class="header-icons" data-eva="close-outline"></i></a>
            </div>
            <div class="responsive-logo">
                <a href="index.html" class="header-logo"><img src="{{asset('admin/img/brand/logo.webp')}}" class="logo-11"></a>
                <a href="index.html" class="header-logo"><img src="{{asset('admin/img/brand/logo-white.png')}}" class="logo-1"></a>
            </div>
            <ul class="header-megamenu-dropdown  nav">
                <li class="nav-item">
                    <div class="btn-group dropdown">
                        <button aria-expanded="false" aria-haspopup="true" class="btn btn-link dropdown-toggle" data-bs-toggle="dropdown" id="dropdownMenuButton2" type="button"><span><i class="fe fe-settings"></i> Settings </span></button>
                        <div class="dropdown-menu">
                            <div class="dropdown-menu-header header-img p-3">
                                <div class="drop-menu-inner">
                                    <div class="header-content text-start d-flex">
                                        <div class="text-white">
                                            <h5 class="menu-header-title">Setting</h5>
                                            <h6 class="menu-header-subtitle mb-0">Overview of theme</h6>
                                        </div>
                                        <div class="my-auto ms-auto">
                                            <span class="badge bg-pill bg-warning float-end">View all</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="setting-scroll">
                                <div>
                                    <div class="setting-menu ">
                                        <a class="dropdown-item" href="profile.html"><i class="mdi mdi-account-outline tx-16 me-2 mt-1"></i>Profile</a>
                                        <a class="dropdown-item" href="contacts.html"><i class="mdi mdi-account-box-outline tx-16 me-2"></i>Contacts</a>
                                        <a class="dropdown-item" href="settings.html"><i class="mdi mdi-account-location tx-16 me-2"></i>Accounts</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="about.html"><i class="typcn typcn-briefcase tx-16 me-2"></i>About us</a>
                                        <a class="dropdown-item" href="javascript:void(0);"><i class="mdi mdi-application tx-16 me-2"></i>Getting start</a>
                                    </div>
                                </div>
                            </div>
                            <ul class="setting-menu-footer flex-column ps-0">
                                <li class="divider mb-0 pb-0 "></li>
                                <li class="setting-menu-btn">
                                    <button class=" btn-shadow btn btn-success btn-sm">Cancel</button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li> 
            </ul>
        </div>
        <button class="navbar-toggler nav-link icon navresponsive-toggler vertical-icon ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent-4" aria-controls="navbarSupportedContent-4" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fe fe-more-vertical header-icons navbar-toggler-icon"></i>
        </button>
        <div class="mb-0 navbar navbar-expand-lg navbar-nav-right responsive-navbar navbar-dark p-0  mg-lg-s-auto">
            <div class="collapse navbar-collapse" id="navbarSupportedContent-4">
                <div class="main-header-right">
                    
                    <li class="dropdown nav-item main-layout">
                        <a class="new theme-layout nav-link-bg layout-setting">
                            <span class="dark-layout"><i class="fe fe-moon"></i></span>
                            <span class="light-layout"><i class="fe fe-sun"></i></span>
                        </a>
                    </li>
                    <div class="nav nav-item  navbar-nav-right mg-lg-s-auto">
                        <div class="nav-item full-screen fullscreen-button">
                            <a class="new nav-link full-screen-link" href="javascript:void(0);"><i class="fe fe-maximize"></i></span></a>
                        </div>
                      
                        <!-- <div class="dropdown nav-item main-header-notification">
                            <a class="new nav-link" href="javascript:void(0);"><i class="fe fe-bell"></i><span class=" pulse"></span></a>
                            <div class="dropdown-menu">
                                <div class="menu-header-content bg-primary-gradient text-start d-flex">
                                    <div class="">
                                        <h6 class="menu-header-title text-white mb-0">7 new Notifications</h6>
                                    </div>
                                    <div class="my-auto ms-auto">
                                        <a class="badge bg-pill bg-warning float-end" href="javascript:void(0);">Mark All Read</a>
                                    </div>
                                </div>
                                <div class="main-notification-list Notification-scroll">
                                    <a class="d-flex p-3 border-bottom" href="javascript:void(0);">
                                        <div class="notifyimg bg-success-transparent">
                                            <i class="la la-shopping-basket text-success"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="notification-label mb-1">New Order Received</h5>
                                            <div class="notification-subtext">1 hour ago</div>
                                        </div>
                                        <div class="ms-auto">
                                            <i class="las la-angle-right text-end text-muted"></i>
                                        </div>
                                    </a>
                                    <a class="d-flex p-3 border-bottom" href="javascript:void(0);">
                                        <div class="notifyimg bg-danger-transparent">
                                            <i class="la la-user-check text-danger"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="notification-label mb-1">22 verified registrations</h5>
                                            <div class="notification-subtext">2 hour ago</div>
                                        </div>
                                        <div class="ms-auto">
                                            <i class="las la-angle-right text-end text-muted"></i>
                                        </div>
                                    </a>
                                    <a class="d-flex p-3 border-bottom" href="javascript:void(0);">
                                        <div class="notifyimg bg-primary-transparent">
                                            <i class="la la-check-circle text-primary"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="notification-label mb-1">Project has been approved</h5>
                                            <div class="notification-subtext">4 hour ago</div>
                                        </div>
                                        <div class="ms-auto">
                                            <i class="las la-angle-right text-end text-muted"></i>
                                        </div>
                                    </a>
                                    <a class="d-flex p-3 border-bottom" href="javascript:void(0);">
                                        <div class="notifyimg bg-pink-transparent">
                                            <i class="la la-file-alt text-pink"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="notification-label mb-1">New files available</h5>
                                            <div class="notification-subtext">10 hour ago</div>
                                        </div>
                                        <div class="ms-auto">
                                            <i class="las la-angle-right text-end text-muted"></i>
                                        </div>
                                    </a>
                                    <a class="d-flex p-3 border-bottom" href="javascript:void(0);">
                                        <div class="notifyimg bg-warning-transparent">
                                            <i class="la la-envelope-open text-warning"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="notification-label mb-1">New review received</h5>
                                            <div class="notification-subtext">1 day ago</div>
                                        </div>
                                        <div class="ms-auto">
                                            <i class="las la-angle-right text-end text-muted"></i>
                                        </div>
                                    </a>
                                    <a class="d-flex p-3" href="javascript:void(0);">
                                        <div class="notifyimg bg-purple-transparent">
                                            <i class="la la-gem text-purple"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="notification-label mb-1">Updates Available</h5>
                                            <div class="notification-subtext">2 days ago</div>
                                        </div>
                                        <div class="ms-auto">
                                            <i class="las la-angle-right text-end text-muted"></i>
                                        </div>
                                    </a>
                                </div>
                                <div class="dropdown-footer">
                                    <a href="javascript:void(0);">VIEW ALL</a>
                                </div>
                            </div>
                        </div> -->

                        <div class="dropdown main-profile-menu nav nav-item nav-link">

                            <a class="profile-user d-flex" href=""><img src="{{asset(auth()->user()->image_data)}}" alt="user-img" class="rounded-circle mCS_img_loaded"><span></span></a>

                            <div class="dropdown-menu">
                                <div class="main-header-profile header-img">
                                    <div class="main-img-user"><img alt="" src="{{asset(auth()->user()->image_data)}}"></div>
                                    <h6>{{auth()->user()->name}}</h6><span>{{auth()->user()->email}}</span>
                                </div> 
                                <a class="dropdown-item" href="{{route('admin.profile')}}"><i class="far fa-edit"></i> Edit Profile</a> 
                                <a class="dropdown-item" href="{{route('logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" role="button"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
       
    </div>
</div>