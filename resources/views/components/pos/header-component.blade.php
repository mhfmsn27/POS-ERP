<div class="row pos-top">
    <div class=" table-responsive">
        <table class="table table-header-pos">
            <tr>
                <th class="formsearch">
                    <div class="input-group" id="seacrhform">
                        <input type="text" class="form-control form-pencarian" placeholder="Cari / Scan Produk" id="searchProduct" style="margin-top:0px;">
                        <span class="input-group-text">
                            <i class="fas fa-barcode"></i>
                        </span>
                    </div>
                    <div class="d-none" id="choosecustomer">
                        <div class="row">
                            <div class="col-md-4 col-sm-12">
                                <label class="text-white">Pilih Kategori Produk</label>
                                <div class="form-group">
                                    <select class="select2 form-control form-category" style="width: 100%;" name="category" id="category">
                                        <option value="all">{{__('pos.all_category')}}</option>
                                        @foreach ($category as $c)
                                        <optgroup label="{{ $c->name }}">
                                            <option value="{{$c->id}}">{{$c->name}}</option>
                                            @foreach ($c->children as $sub)
                                            <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <label class="text-white">Pilih Pelanggan / Customer</label>
                                <div class="form-group">
                                    <select class="select2 form-control form-user" name="customer_id" id="customer_id" style="width:100%; ">
                                        @foreach ($customer as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @if($agent_commisiion != null) 
                            <div class="col-md-4 col-sm-12">
                                <label class="text-white">Pilih Agent Komisi</label>
                                <div class="form-group">
                                    <select class="select2 form-control form-user" name="agent_commission_id" id="agent_commission_id" style="width:100%; ">
                                        @foreach ($agent_commisiion as $agent)
                                        @if($store->commission_type == 'employee') 
                                        <option value="{{ $agent->id }}">{{ $agent->user->name ?? '' }}</option>
                                        @else 
                                        @endif
                                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                        @endforeach 
                                    </select>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </th>
                <th class="text-right">
                    <button onclick="swicthcustomer()" class="btn btn-lg btn-light btn-rounded btn-primary float-end swicthcustomer" type="button">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                    <button onclick="swicthsearch()" class="btn btn-lg btn-light btn-rounded btn-primary float-end swicthsearch d-none" type="button">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                    @if($store->shift_register == 'active' && request()->is('pos/layer'))
                    <a data-bs-container="#tooltip-container" data-bs-toggle="tooltip" data-bs-placement="right" title="Laporan Shift Register" href="{{route('register.today')}}" class="btn btn-lg btn-light btn-rounded btn-primary float-end" style="margin-right: 5px;">
                        <i class="fas fa-chart-area"></i>
                    </a>
                    @endif
                </th>
            </tr>
        </table>
    </div>
</div>