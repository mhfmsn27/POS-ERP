<section class="popular-categories section-padding">
      <div class="container wow animate__animated animate__fadeIn">
            <div class="section-title">
                  <div class="title">
                        <h3>Kategori Unggulan</h3>
                        <ul class="list-inline nav nav-tabs links">
                              @foreach($featured as $feature)
                              <li class="list-inline-item nav-item"><a class="nav-link" href="{{route('ecommerce.shop')}}?category={{$feature->id}}">{{$feature->name}}</a></li>
                              @endforeach 
                        </ul>
                  </div>
                  <div class="slider-arrow slider-arrow-2 flex-right carausel-10-columns-arrow" id="carausel-10-columns-arrows"></div>
            </div>
            <div class="carausel-10-columns-cover position-relative">
                  <div class="carausel-10-columns" id="carausel-10-columns">
                        @foreach($data as $category)
                        <div class="card-2 bg-9 wow animate__animated animate__fadeInUp" data-wow-delay=".1s">
                              <figure class="img-hover-scale overflow-hidden">
                                    <a href="{{route('ecommerce.shop')}}?category={{$category->id}}"><img src="{{asset($category->image)}}" alt="" /></a>
                              </figure>
                              <h6><a href="{{route('ecommerce.shop')}}?category={{$category->id}}">{{$category->name}}</a></h6>
                              <span>{{count($category->product)}} Item</span>
                        </div>
                        @endforeach
                         
                  </div>
            </div>
      </div>
</section>