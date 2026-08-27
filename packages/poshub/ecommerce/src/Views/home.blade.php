@extends('ecommerce::layouts.web')

@section('content')


<!--End header-->
<main class="main">

      <!-- Slider Component -->
      <x-ecommerce-slider-component></x-ecommerce-slider-component>
      <!--End hero slider-->

      <!-- Category Component -->
      <x-ecommerce-category-component></x-ecommerce-category-component>
      <!--End category slider-->

      <!-- Banner Component -->
      <x-ecommerce-deafult-banner-component></x-ecommerce-deafult-banner-component>
      <!-- End Banner Component -->

      <!-- Produk Terlaris -->
      <x-ecommerce-product-popular-component></x-ecommerce-product-popular-component>
      <!-- End Terlaris -->

      <!-- New Product -->
      <x-ecommerce-new-products-component></x-ecommerce-new-products-component>
      <!-- End New Produc -->

      
      <!-- Our Products -->
      <x-ecommerce-our-products-component></x-ecommerce-our-products-component>
      <!-- End Our Products -->
</main>

@endsection