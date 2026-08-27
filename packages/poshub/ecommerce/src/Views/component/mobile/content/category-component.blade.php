<div class="line4-bt pt-16 pb-16">
    <div class="tf-container">
        <div class="d-flex justify-content-between align-items-center">
            <h5>Kategori</h5>
        </div>
        <ul class="mt-16 box-category">
            @foreach($data as $category)
            <li>
                <a href="{{route('ecommerce.mobile.shop')}}?category={{$category->id}}" class="category-item">
                    <div class="box-img-product">
                        <img src="{{asset($category->image)}}" alt="">
                    </div>
                    {{$category->name}}
                </a>
            </li>
            @endforeach
            

        </ul>
    </div>
</div>