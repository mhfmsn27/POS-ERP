<div class="col-xl-5 col-md-5 col-sm-12 billing-pos">
    <div class="table-responsive pos-billing">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>{{__('pos.qty')}}</th>
                    <th>{{__('sidebar.product')}}</th>
                    <th>{{__('pos.price')}}</th>
                    <th>{{__('general.subtotal')}}</th>
                    <th><i class="fa fa-trash"></i></th>
                </tr>
            </thead>
            <tbody id="cartProduct">
                <tr class="table-success cart0 d-none"> <input type="hidden" id="productDetect"> </tr>
            </tbody>
        </table>
    </div>
    <table class="table mb-0">
        <thead>
            <tr>
                <th style="width: 50%"></th>
                <th style="width: 50%"></th>
            </tr>
        </thead>
        <tbody onchange="otherPriceTotal()">
            <tr class="">
                <td>
                    <label for="discount">{{__('purchase.discount_amount')}}</label>
                    <select class="form-control" name="discount" id="discount">
                        <option value="0" typediscount="fix">Pilih Diskon</option>
                        @foreach($discount as $d)
                        <option value="{{(int)$d->discount_amount}}" typediscount="{{$d->type}}">{{$d->name}} ( {{number_format($d->discount_amount)}} @if($d->type == 'percentase') % @endif ) </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <label for="tax">{{__('purchase.tax')}}</label>
                    <select class="form-control" name="tax" id="taxPrice">
                        <option value="0">Pilih Pajak</option>
                        @foreach($taxrate as $t)
                        <option value="{{(int)$t->taxrate}}">{{$t->name}}% </option>
                        @endforeach
                    </select> 
                </td>
            </tr>
            <tr class="">
                <td>
                    <label for="shipping">{{__('purchase.shipping_cost')}}</label>
                    <input type="number" id="shipping" class="form-control" name="shipping" min="0" value="0">
                </td>

                <td>
                    <label for="other_price">{{__('purchase.other_payment')}}</label>
                    <input type="number" id="other_price" class="form-control" name="other_price" min="0" value="0">
                </td>
            </tr>
        </tbody>
    </table>

    <a href="javascript:void(0)" id="pay_shop" class="card text-white my-2">
        <div>
            <div class="float-end">
                <div class="text-white">
                    <input type="hidden" name="fixtotal" id="jumlahtotal" value="0">
                    <p class="mb-0 font-weight-bold text-white" id="fixTotal">0</p>
                </div>
            </div>
            <p class="text-white-50 mb-0 mt-1">
                <i class="fas fa-shopping-basket h5 text-white"></i>
            </p>
        </div>
    </a>
    <a href="javascript:void(0)" class="d-none" id="pay_modal_click" data-bs-toggle="modal" data-bs-target="#paymodal"></a>
</div>