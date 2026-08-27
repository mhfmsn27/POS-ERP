<div class="col-12 header-menu mb-4">
      <div class="tabs effect-3">
            <a href="{{route('ecommerce.admin.settings')}}" id="tab-1" class="tab-effect {{ request()->is('pos-admin/ecommerce/settings/integrations') ? 'active' : '' }}">
                  <span>
                        <i class="fa fa-cogs"></i>
                        <span class="title-tab">Pengaturan</span>
                  </span>
                  <div class="line ease"></div>
            </a>

            <a href="{{route('ecommerce.sett.province')}}" id="tab-3" class="tab-effect {{ request()->is('pos-admin/ecommerce/settings/province') ? 'active' : '' }}">
                  <span>
                        <i class="fa fa-map"></i>
                        <span class="title-tab">Provinsi</span>
                  </span>
                  <div class="line ease"></div>
            </a>

            <a href="{{route('ecommerce.sett.city')}}" id="tab-4" class="tab-effect {{ request()->is('pos-admin/ecommerce/settings/city') ? 'active' : '' }}">
                  <span>
                        <i class="fa fa-map"></i>
                        <span class="title-tab">Kota</span>
                  </span>
                  <div class="line ease"></div>
            </a>

            <a href="{{route('ecommerce.sett.district')}}" id="tab-5" class="tab-effect {{ request()->is('pos-admin/ecommerce/settings/district') ? 'active' : '' }}">
                  <span>
                        <i class="fa fa-map"></i>
                        <span class="title-tab">Kecamatan</span>
                  </span>
                  <div class="line ease"></div>
            </a>


      </div>
</div>