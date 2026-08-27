@extends("ecommerce::layouts.web")

@section('content')

<main class="main">
      <div class="page-header breadcrumb-wrap">
            <div class="container">
                  <div class="breadcrumb">
                        <a href="{{route('ecommerce.home')}}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                        <span></span> Shop <span></span> Cart
                  </div>
            </div>
      </div>

      <div class="container mb-80 mt-50">
            <div class="row">
                  <div class="col-lg-8 mb-40">
                        <h1 class="heading-2 mb-10">Keranjang Kamu</h1>
                        <div class="d-flex justify-content-between">
                              <h6 class="text-body">Ada <span class="text-brand">{{$carts['total']}}</span> Item Siap Checkout Nih!</h6>
                              <h6 class="text-body"><a href="javascript:void(0);" onclick="removeAll()" class="text-muted"><i class="fi-rs-trash mr-5"></i>Hapus Semua Keranjang</a></h6>
                        </div>
                  </div>
            </div>
            <form class="row" action="{{route('ecommerce.checkout_checked')}}" method="post">
                  <div class="col-lg-8">
                        <div class="table-responsive shopping-summery">
                              <table class="table table-wishlist">
                                    <thead>
                                          <tr class="main-heading">
                                                <th class="custome-checkbox start pl-30">
                                                      <input type="hidden" id="taxstores" value="{{(int)$stores->tax}}">
                                                </th>
                                                <th scope="col" colspan="2">Product</th>
                                                <th scope="col">Quantity</th>
                                                <th scope="col">Harga</th>
                                                <th scope="col">Subtotal</th>
                                                <th scope="col" class="end">Remove</th>
                                          </tr>
                                    </thead>
                                    <tbody class="cartdata">
                                          @foreach($carts['carts'] as $cart)
                                          <tr class="pt-30 cartitems" id="cartid{{$cart->id}}">
                                                <td class="custome-checkbox pl-30"> 
                                                <input  type="hidden" id="cartIdData" value="{{$cart->id}}">
                                                      <input class="form-check-input" type="checkbox" name="choose_cart[]" id="chooseCart{{$cart->id}}" checked value="{{$cart->id}}">
                                                      <label class="form-check-label" for="chooseCart{{$cart->id}}"></label>
                                                </td>
                                                <td class="image product-thumbnail pt-40"><img src="{{asset($cart->variation->product->default_image ?? '')}}" alt="#"></td>
                                                <td class="product-des product-name">
                                                      <h6 class="mb-5"><a class="product-name mb-10 text-heading" href="{{route('ecommerce.shop_detail',$cart->variation->product_id ?? 0)}}">{{$cart->variation->product->name ?? ''}} @if($cart->variation->product->type != 'single') - {{$cart->variation->name ?? ''}} @endif </a></h6>
                                                      <div class="product-rate-cover"> 
                                                            <span class="font-small ml-5 text-muted">Stok Tersedia : {{number_format($cart->variation->stock_in_website->sum('qty_available'))}} </span>
                                                      </div>
                                                </td>
                                                <td class="text-center detail-info" data-title="Stock">
                                                      <div class="detail-extralink mr-15">
                                                            <div class="detail-qty border radius">
                                                                  <input type="number" name="quantity[]" class="qty-val" value="{{(int)$cart->quantity}}" min="1" max="{{(int)$cart->variation->stock_in_website->sum('qty_available')}}">
                                                            </div>
                                                      </div>
                                                </td>
                                                <td class="price" data-title="Price">
                                                      <input type="hidden" id="productPrice" value="{{(int)$cart->variation->selling_price}}">
                                                      <h4 class="text-body">Rp {{number_format((int)$cart->variation->selling_price)}} </h4>
                                                </td>
                                                <td class="price" data-title="Price">
                                                      <h4 class="text-brand">
                                                            @php
                                                            $subtotal = (int)$cart->variation->selling_price * $cart->quantity;
                                                            @endphp

                                                            Rp {{number_format($subtotal)}}
                                                      </h4>
                                                      <input type="hidden" id="subtotalPriceCart" value="{{$subtotal}}" datacart="{{$cart->id}}">
                                                </td>
                                                <td class="action text-center" data-title="Remove"><a href="javascript:void(0);" onclick="removeCart(<?= $cart->id; ?>)" class="text-body"><i class="fi-rs-trash"></i></a></td>
                                          </tr>
                                          @endforeach
                                    </tbody>
                              </table>
                        </div>
                        <div class="divider-2 mb-30"></div>
                        <div class="cart-action d-flex justify-content-between">
                              <a class="btn" href="{{route('ecommerce.shop')}}"><i class="fi-rs-arrow-left mr-10"></i>Mau Belanja Lagi</a>

                        </div>

                  </div>
                  <div class="col-lg-4">
                        <div class="border p-md-4 cart-totals ml-30">
                              <div class="table-responsive">
                                    <table class="table no-border">
                                          <tbody>
                                                <tr>
                                                      <td class="cart_total_label">
                                                            <h6 class="text-muted">Subtotal</h6>
                                                      </td>
                                                      <td class="cart_total_amount">
                                                            <h4 class="text-brand text-end subtotalCart">Rp {{number_format($carts['subtotal'])}} </h4>
                                                      </td>
                                                </tr>
                                                <!-- <tr>
                                                      <td scope="col" colspan="2">
                                                            <div class="divider-2 mt-10 mb-10"></div>
                                                      </td>
                                                </tr>
                                                <tr>
                                                      <td class="cart_total_label">
                                                            <h6 class="text-muted">Pajak PPN</h6>
                                                      </td>
                                                      <td class="cart_total_amount">
                                                            <h4 class="text-brand text-end taxtotalCart ">Rp {{number_format($tax_total)}} ({{number_format($stores->tax)}} %)</h4>
                                                      </td>
                                                </tr> -->

                                                <tr>
                                                      <td scope="col" colspan="2">
                                                            <div class="divider-2 mt-10 mb-10"></div>
                                                      </td>
                                                </tr>
                                                <tr>
                                                      <td class="cart_total_label">
                                                            <h6 class="text-muted">Total</h6>
                                                      </td>
                                                      <td class="cart_total_amount">
                                                            <h4 class="text-brand text-end grandTotalCart">Rp {{number_format($grandTotal)}} </h4>
                                                      </td>
                                                </tr>
                                          </tbody>
                                    </table>
                              </div>
                              <button type="submit" class="btn mb-20 w-100"> Checkout Sekarang<i class="fi-rs-sign-out ml-15"></i></button>
                               
                        </div>
                  </div>
</form>
      </div>

</main>

@endsection

@section("scripts")
<script src="{{asset('ecommerce/js/cart.js')}}"></script>
@endsection