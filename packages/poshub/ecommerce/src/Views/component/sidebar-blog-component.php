<div class="col-lg-3 primary-sidebar sticky-sidebar">
      <div class="widget-area">
            <div class="sidebar-widget-2 widget_search mb-50">
                  <div class="search-form">
                        <form action="<?= route('ecommerce.blog'); ?>">
                              <input type="text" name="name" placeholder="Cari Blog Disini..." />
                              <button type="submit"><i class="fi-rs-search"></i></button>
                        </form>
                  </div>
            </div>
            <div class="sidebar-widget widget-category-2 mb-50">
                  <h5 class="section-title style-1 mb-30">Kategori Blog</h5>
                  <ul>
                        <?php

                        foreach ($category as $c) { ?>
                              <li>
                                    <a href="<?= route('ecommerce.blog') . '?category=' . $c->id; ?>"> <img src="<?= asset($c->image); ?>" alt="" /><?= $c->name; ?></a><span class="">(<?= count($c->blog); ?>)</span>
                              </li>
                        <?php } ?>

                  </ul>
            </div>
            <!-- Product sidebar Widget -->
            <div class="sidebar-widget product-sidebar mb-50 p-30 bg-grey border-radius-10">
                  <h5 class="section-title style-1 mb-30">Teratas</h5>

                  <?php

                  foreach ($data as $d) { ?>
                        <div class="single-post clearfix">
                              <div class="image">
                                    <img src="<?= asset($d->thumbnail); ?>" alt="#" />
                              </div>
                              <div class="content pt-10">
                                    <h5><a href="<?= route('ecommerce.blog_detail', $d->slug); ?>"><?= $d->title; ?></a></h5>
                                    <p class="price mb-0 mt-5">(<?=number_format($d->views);?>) Dilihat</p>
                                    
                              </div>
                        </div>
                  <?php } ?>

               
            </div>


      </div>
</div>