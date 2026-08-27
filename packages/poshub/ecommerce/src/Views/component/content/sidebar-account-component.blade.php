<div class="col-md-3">
      <div class="dashboard-menu">
            <ul class="nav flex-column" role="tablist">
              
                  <li class="nav-item">
                        <a class="nav-link {{ request()->is('web/account/dashboard') ? 'active' : '' }}" href="{{route('ecommerce.dashboard')}}"><i class="fi-rs-settings-sliders mr-10"></i>Dashboard</a>
                  </li>
                  <li class="nav-item">
                        <a class="nav-link {{ request()->is('web/account/orders') ? 'active' : '' }}" href="{{route('ecommerce.orders')}}" ><i class="fi-rs-shopping-bag mr-10"></i>Pesanan</a>
                  </li> 
                  <li class="nav-item">
                        <a class="nav-link {{ request()->is('web/account/setting-address') ? 'active' : '' }}" href="{{route('ecommerce.address')}}"><i class="fi-rs-marker mr-10"></i>Alamat Saya</a>
                  </li>
                  <li class="nav-item">
                        <a class="nav-link {{ request()->is('web/account/my-profile') ? 'active' : '' }}" href="{{route('ecommerce.profile')}}"><i class="fi-rs-user mr-10"></i>Pengaturan Profile</a>
                  </li>
                  <li class="nav-item">
                        <a class="nav-link" href="{{route('ecommmerce.logout')}}"><i class="fi-rs-sign-out mr-10"></i>Logout</a>
                  </li>
            </ul>
      </div>
</div>