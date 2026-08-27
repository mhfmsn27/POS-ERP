@extends("ecommerce::layouts.web")

@section('content')

<main class="main">
      <div class="page-header breadcrumb-wrap">
            <div class="container">
                  <div class="breadcrumb">
                        <a href="{{route('ecommerce.home')}}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                        <span></span> Shop <span></span> Checkout
                  </div>
            </div>
      </div>

      <div class="container mb-80 mt-50">
            <div class="row">
                  <div class="col-lg-8 mb-40">
                        <h1 class="heading-2 mb-10">Checkout</h1>
                        <div class="d-flex justify-content-between">
                              <h6 class="text-body">Ada <span class="text-brand">3</span> Produk yang akan di checkout</h6>
                        </div>
                  </div>
            </div>
            <div class="row">
                  <div class="col-lg-7">

                        <div class="row">
                              <div class="col-12 mb-50">
                                    <h4 class="mb-30">Daftar Produk</h4>
                                    <div class="table-responsive ">
                                          <table class="table table-wishlist">
                                                <thead>
                                                      <tr class="">

                                                            <th scope="col" colspan="2">Product</th>
                                                            <th scope="col">Quantity</th>
                                                            <th scope="col">Harga</th>
                                                            <th scope="col">Subtotal</th>
                                                      </tr>
                                                </thead>
                                                <tbody>
                                                      @foreach($carts['carts'] as $cart)
                                                      <tr class="pt-30">

                                                            <td class="image product-thumbnail pt-40">
                                                                  <img src="{{asset($cart->variation->product->default_image ?? '')}}" alt="#">
                                                                  <input type="hidden" value="{{$cart->id}}" id="cartIdVariation" name="cart_id[]">
                                                                  <input type="hidden" value="{{$cart->variation_id}}" id="variationIdCart" name="variation_id[]">
                                                            </td>
                                                            <td class="product-des product-name">
                                                                  <h6 class="mb-5"><a class="product-name mb-10 text-heading" href="{{route('ecommerce.shop_detail',$cart->variation->product_id ?? 0)}}">{{$cart->variation->product->name ?? ''}} @if($cart->variation->product->type != 'single') - {{$cart->variation->name ?? ''}} @endif</a></h6>
                                                                  <div class="product-rate-cover">
                                                                        <div class="product-rate-cover">
                                                                              <span class="font-small ml-5 text-muted">Stok Tersedia : {{number_format($cart->variation->stock_in_website->sum('qty_available'))}} </span>
                                                                        </div>
                                                                  </div>
                                                            </td>
                                                            <td class="price" data-title="Price">
                                                                  <h4 class="text-body">{{number_format($cart->quantity)}} x </h4>
                                                            </td>
                                                            <td class="price" data-title="Price">
                                                                  <h4 class="text-body">Rp {{number_format((int)$cart->variation->selling_price)}} </h4>
                                                            </td>

                                                            <td class="price" data-title="Price">
                                                                  <h4 class="text-brand">
                                                                        @php
                                                                        $subtotal = (int)$cart->variation->selling_price * $cart->quantity;
                                                                        @endphp

                                                                        Rp {{number_format($subtotal)}}
                                                                  </h4>
                                                            </td>
                                                      </tr>
                                                      @endforeach
                                                </tbody>
                                          </table>
                                    </div>
                              </div>
                              <div class="col-12">
                                    <div class="border cart-totals">
                                          <div class="table-responsive">
                                                <table class="table no-border">
                                                      <tbody>
                                                            <tr>
                                                                  <td class="cart_total_label">
                                                                        <h6 class="text-muted">Subtotal</h6>
                                                                  </td>
                                                                  <td class="cart_total_amount">
                                                                        <input type="hidden" id="subtotalCart" value="{{$carts['subtotal']}}">
                                                                        <input type="hidden" id="subtotalTax" value="{{$tax_total}}">
                                                                        <input type="hidden" id="sp" name="shipping_price">
                                                                        <input type="hidden" id="sc" name="shipping_code">
                                                                        <input type="hidden" id="ss" name="shipping_service">
                                                                        <input type="hidden" id="ci" name="courier_id">
                                                                        <h4 class="text-brand text-end">Rp {{number_format($carts['subtotal'])}} </h4>
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
                                                                        <h4 class="text-brand text-end ">Rp {{number_format($tax_total)}} ({{number_format($stores->tax)}} %)</h4>
                                                                  </td>
                                                            </tr> -->

                                                            <tr>
                                                                  <td scope="col" colspan="2">
                                                                        <div class="divider-2 mt-10 mb-10"></div>
                                                                  </td>
                                                            </tr>

                                                            <tr>
                                                                  <td class="cart_total_label">
                                                                        <h6 class="text-muted">Biaya Kirim</h6>
                                                                  </td>
                                                                  <td class="cart_total_amount">
                                                                        <h4 class="text-brand text-end shippingCost">Rp 0</h4>
                                                                  </td>
                                                            </tr>

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
                                                                        <h4 class="text-brand text-end grandTotal">Rp {{number_format($grandTotal)}} </h4>
                                                                  </td>
                                                            </tr>
                                                      </tbody>
                                                </table>
                                          </div>
                                    </div>
                              </div>

                        </div>
                  </div>
                  <div class="col-lg-5">
                        <div class="border p-40 cart-totals ml-30 mb-50">
                              <div class="d-flex align-items-end justify-content-between mb-30">
                                    <h4>Pilih Alamat Pengiriman</h4>
                              </div>
                              <div class="divider-2 mb-30"></div>
                              <div class="table-responsive order_table checkout">
                                    <table class="table no-border">
                                          <tbody>
                                                @foreach($address as $a)
                                                <tr>
                                                      <td class="custome-radio pl-30">
                                                            <input class="form-check-input" type="radio" name="address_option" value="{{$a->id}}" id="addressOption{{$a->id}}" @if($a->default == 'yes') checked="" @endif>
                                                            <label class="form-check-label" for="addressOption{{$a->id}}"></label>
                                                      </td>

                                                      <td>
                                                            <h6 class="w-160 mb-5">
                                                                  <a href="javascript:void(0);" class="text-heading">{{$a->name}}</a>
                                                            </h6>
                                                            <div class="product-rate-cover">
                                                                  <div class="product-rate-cover">
                                                                        <span class="font-small ml-5 text-muted">Provinsi {{$a->subdistrict->city->province->name ?? ''}} </span>
                                                                  </div>
                                                                  <div class="product-rate-cover">
                                                                        <span class="font-small ml-5 text-muted">{{$a->subdistrict->city->type ?? ''}} {{$a->subdistrict->city->name ?? ''}} </span>
                                                                  </div>
                                                                  <div class="product-rate-cover">
                                                                        <span class="font-small ml-5 text-muted">Kecamatan {{$a->subdistrict->name ?? ''}} </span>
                                                                  </div>
                                                            </div>

                                                      </td>

                                                </tr>
                                                @endforeach

                                          </tbody>
                                    </table>
                              </div>
                        </div>

                        <div class="border p-40 cart-totals ml-30 mb-50">
                              <div class="d-flex align-items-end justify-content-between mb-30">
                                    <h4>Pilih Metode Pengiriman</h4>
                              </div>
                              <div class="divider-2 mb-30"></div>
                              <div class="table-responsive order_table checkout">
                                    <table class="table no-border">
                                          <tbody class="listcostshipping">
                                                <!-- Isi Shipping -->
                                          </tbody>
                                    </table>
                              </div>
                        </div>

                        <div class="payment ml-30">

                              <button type="button" id="getPayment" class="btn btn-fill-out btn-block mt-30">Proses Pembayaran<i class="fi-rs-sign-out ml-15"></i></button>
                        </div>
                  </div>
            </div>
      </div>

</main>

@endsection

@section('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{$settings->client_key}}"></script>
<script src="{{asset('ecommerce/js/checkout.js')}}"></script>
@endsection