<div class="col-12 header-menu mb-4">
      <div class="tabs effect-3">
            <a href="{{route('ecommerce.admin.sliders')}}" id="tab-1" class="tab-effect {{ request()->is('pos-admin/ecommerce/media-content/sliders*') ? 'active' : '' }}">
                  <span>
                        <i class="fa fa-list"></i>
                        <span class="title-tab">Slider</span>
                  </span>
                  <div class="line ease"></div>
            </a>

            <a href="{{route('ecommerce.content.banners')}}" id="tab-2" class="tab-effect {{ request()->is('pos-admin/ecommerce/media-content/banners*') ? 'active' : '' }} ">
                  <span>
                        <i class="fa fa-address-card"></i>
                        <span class="title-tab">Banner</span>
                  </span>
                  <div class="line ease"></div>
            </a>

            <a href="{{route('ecommerce.content.featured')}}" id="tab-3" class="tab-effect {{ request()->is('pos-admin/ecommerce/media-content/featured*') ? 'active' : '' }}">
                  <span>
                        <i class="fa fa-bookmark"></i>
                        <span class="title-tab">Featured</span>
                  </span>
                  <div class="line ease"></div>
            </a>

            <a href="{{route('ecommerce.content.category')}}" id="tab-4" class="tab-effect {{ request()->is('pos-admin/ecommerce/media-content/categories') ? 'active' : '' }}">
                  <span>
                        <i class="fa fa-cubes"></i>
                        <span class="title-tab">Kategori</span>
                  </span>
                  <div class="line ease"></div>
            </a>

            <a href="{{route('ecommerce.content.fcategory')}}" id="tab-5" class="tab-effect {{ request()->is('pos-admin/ecommerce/media-content/categories/featured') ? 'active' : '' }}">
                  <span>
                        <i class="fa fa-cube"></i>
                        <span class="title-tab">K Unggulan</span>
                  </span>
                  <div class="line ease"></div>
            </a>


      </div>
</div>