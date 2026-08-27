<div class="col-12 header-menu mb-4">
      <div class="tabs effect-3">
            <a href="{{route('ecommerce.admin.blogs')}}" id="tab-1" class="tab-effect blog-component {{ request()->is('pos-admin/ecommerce/blogs/article*') ? 'active' : '' }}">
                  <span>
                        <i class="fa fa-newspaper"></i>
                        <span class="title-tab">Blog</span>
                  </span>
                  <div class="line ease"></div>
            </a>

            <a href="{{route('ecommerce.admin.blog_category')}}" id="tab-2" class="tab-effect blog-component {{ request()->is('pos-admin/ecommerce/blogs/categories*') ? 'active' : '' }} ">
                  <span>
                        <i class="fa fa-list"></i>
                        <span class="title-tab">Kategori Blog</span>
                  </span>
                  <div class="line ease"></div>
            </a>

            <a href="{{route('ecommerce.admin.about')}}" id="tab-3" class="tab-effect blog-component {{ request()->is('pos-admin/ecommerce/blogs/abouts') ? 'active' : '' }}">
                  <span>
                        <i class="fa fa-file"></i>
                        <span class="title-tab">About Us</span>
                  </span>
                  <div class="line ease"></div>
            </a>

            <a href="{{route('ecommerce.admin.about.social')}}" id="tab-4" class="tab-effect blog-component {{ request()->is('pos-admin/ecommerce/blogs/abouts/social') ? 'active' : '' }}">
                  <span>
                        <i class="fa fa-info"></i>
                        <span class="title-tab">Social Media </span>
                  </span>
                  <div class="line ease"></div>
            </a>
 
      </div>
</div>