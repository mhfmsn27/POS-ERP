<div class="col-xl-3 primary-sidebar sticky-sidebar mt-30">
      <div class="sidebar-widget widget-category-2 mb-30">
            <h5 class="section-title style-1 mb-30">Kategori</h5>
            <ul>
                  @foreach($category as $c)
                  <li>
                        <a href="{{route('ecommerce.shop')}}?category={{$c->id}}"> <img src="{{asset($c->image)}}" alt="" />{{$c->name}}</a><span class="count">{{count($c->product)}}</span>
                  </li>
                  @endforeach
            </ul>
      </div>

      <!-- Product sidebar Widget -->
      <div class="sidebar-widget product-sidebar mb-30 p-30 bg-grey border-radius-10">
            <h5 class="section-title style-1 mb-30">Produk Terbaru</h5>

            @foreach($products as $product)
            <div class="single-post clearfix">
                  <div class="image">
                        <img src="{{asset($product->image)}}" alt="{{$product->name}}" />
                  </div>
                  <div class="content pt-10">
                        <h5><a href="{{route('ecommerce.shop_detail',$product->id)}}">{{$product->name}}</a></h5>
                        <p class="price mb-0 mt-5">Rp {{$product->price_sell_range}}</p>
                        <div class="product-rate">
                              <div class="product-rating" style="width: 90%"></div>
                        </div>
                  </div>
            </div>
            @endforeach

      </div>

</div>