<div class="modal fade" id="paymodal" tabindex="-1" role="dialog" aria-labelledby="paymodal" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full payment_modal" role="document">
         <div class="modal-content" style="height: 90vh;">
             <div class="modal-header header-modal" style="height: 5vh;">
                 <h5 class="modal-title text-white" id="">{{__('pos.payment')}}</h5>
                 <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                     <i class="fa fa-times text-white"></i>
                 </button>
             </div>
             <div class="modal-body">
                 <div class="row">

                     <div class="col-12">
                         <div class="tab-content" id="v-pills-tabContent">
                             <div class="tab-pane fade show active" id="cashpay" role="tabpanel" aria-labelledby="cash">
                                 <div class="row m-2 p-2">

                                     <div class="col-12">
                                         <div class="payment-method">
                                            
                                             @foreach($payment as $p)
                                             <label for="card" class="method card">
                                                
                                                 <div class="radio-input">
                                                     <input id="method_payment" type="radio" service_payment="{{(int)$p->amount}}" value="{{$p->id}}" name="payment_method">
                                                     Pembayaran Menggunakan {{$p->name}}
                                                     @if($p->service == 'active')
                                                     - Dikenakan Biaya Tambahan Sebesar {{number_format($p->amount)}}
                                                     @endif
                                                 </div>
                                             </label>
                                             @endforeach


                                         </div>
                                     </div>

                                     <div class="col-6 mb-4">
                                         <button type="button" onclick="payfull()" class="btn btn-primary btn-lg btn-rounded btn-block">
                                             <i class="fa fa-wallet"></i> | Bayar Full
                                         </button>
                                     </div>
                                     <div class="col-6 mb-4">
                                         <button type="button" onclick="duefull()" class="btn btn-warning btn-lg btn-rounded btn-block">
                                             <i class="fas fa-money-check"></i> | Full Hutang
                                         </button>
                                     </div>

                                     <div class="col-md-6 col-sm-12  mb-4">
                                         <div class="input-group" style="height: 8vh;">
                                             <span class="input-group-text" id="dibayarkan">Dibayarkan</span>
                                             <input type="text" class="form-control" id="on_pay" name="on_pay" min="0" value="0">
                                         </div>
                                     </div>
                                     <div class="col-md-6 col-sm-12  mb-4" id="duepay">
                                         <div class="input-group" style="height: 8vh;">
                                             <span class="input-group-text" id="dibayarkan">Hutang</span>
                                             <input type="text" class="form-control" id="on_due" readonly name="on_due">
                                         </div>
                                     </div>
                                     <div class="col-md-6 col-sm-12 d-none  mb-4" id="changepay">
                                         <div class="input-group" style="height: 8vh;">
                                             <span class="input-group-text" id="dibayarkan">Kembalian</span>
                                             <input type="text" class="form-control" id="on_change" readonly name="on_change">
                                         </div>
                                     </div>

                                     <div class="col-12">
                                         <div class="row">
                                             <div class="col-6">
                                                 <div class="col-md-12 col-sm-12">
                                                     <div class="input-group" style="height: 6vh;">
                                                         <input type="text" class="form-control" id="voucher_code" placeholder="Masukan Kode Voucher" name="voucher_code">
                                                         <button class="btn btn-info" type="button" id="send_voucher"><i class="fa fa-paper-plane"></i></button>
                                                     </div>
                                                 </div>
                                                 <div class="col-md-12 col-sm-12 mt-4 text-center">
                                                     <div class="col-8 d-none" id="voucherCard">
                                                         <input type="hidden" name="voucher_id" id="voucherID">
                                                         <input type="hidden" name="voucher_type" id="voucher_type">
                                                         <input type="hidden" name="voucher_amount" id="voucher_amount" value="0">
                                                         <div class="voucher">
                                                             <div class="voucher-body bg-orange-gradient">
                                                                 <div class="voucher-text">
                                                                     <h6 class="text-white mb-0 font-weight-bold" id="voucher_name">Voucher Ongkos Kirim</h6>
                                                                 </div>
                                                                 <div class="voucher-overlay d-none">
                                                                     <button class="btn btn-primary btn-sm">View Details</button>
                                                                 </div>
                                                                 <div class="voucher-border-left"></div>
                                                                 <div class="voucher-border-right"></div>
                                                             </div>
                                                             <div class="voucher-footer">
                                                                 <div class="voucher-details">
                                                                     <div class="details-icon">
                                                                         <i class="fa fa-clock" width="30" height="30" style="color: red;"></i>
                                                                     </div>
                                                                     <div class="details-text">
                                                                         <div class="text-title">Tanggal Kadaluarsa</div>
                                                                         <div class="text-description text-primary" id="voucherDate">12 Jun 2019</div>
                                                                     </div>
                                                                 </div>
                                                                 <div class="voucher-details">
                                                                     <div class="details-icon">
                                                                         <i class="fas fa-money-bill-alt" width="30" height="30" style="color: red;"></i>
                                                                     </div>
                                                                     <div class="details-text">
                                                                         <div class="text-title" style="width: 90px;">Potongan Hingga</div>
                                                                         <div class="text-description text-primary" id="voucherAmount">10.000</div>
                                                                     </div>
                                                                 </div>
                                                             </div>
                                                             <button type="button" id="deleteVoucher" class="btn btn-sm btn-block btn-danger">
                                                                 <i class="bx bx-x d-block d-sm-none"></i>
                                                                 <span class="d-none d-sm-block"><i class="fa fa-trash"></i> Hapus Penggunaan Voucher</span>
                                                             </button>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>
                                             <div class="col-6">
                                                 <div class="col-md-12 col-sm-12">
                                                     <div id="paymentprocess" class="row"></div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                     <input type="hidden" id="discountType" name="discount_type">
                                     <input type="hidden" id="payment_service" name="payment_service" value="0">
                                 </div>
                             </div>

                         </div>
                     </div>
                 </div>

             </div>
             <div class="modal-footer">
                 <table class="table">
                     <tr>
                         <td class="d-none">
                             <button type="button" id="holdbutton" class="btn btn-lg btn-block btn-danger">
                                 <i class="bx bx-x d-block d-sm-none"></i>
                                 <span class="d-none d-sm-block"><i class="far fa-hand-paper"></i> {{__('pos.hold')}}</span>
                             </button>
                             <div id="holdinput" class="d-none"></div>
                         </td>
                         <td>
                             <button type="submit" class="btn btn-lg btn-block btn-primary ml-1">
                                 <span class="d-none d-sm-block"><i class="fas fa-money-bill-alt"></i> {{__('pos.pay')}} </span>
                             </button>
                         </td>
                     </tr>
                 </table>


             </div>
         </div>
     </div>
 </div>

 <div class="modal fade text-left" id="addCustomer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
         <div class="modal-content">
             <div class="modal-header header-modal" style="height: 7vh;">
                 <h4 class="modal-title text-white" id="myModalLabel33">{{__('sidebar.add_customer')}}</h4>
                 <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                     <i class="fa fa-times text-white"></i>
                 </button>
             </div>
             <form id="cCustomer" method="POST">
                 @csrf
                 <div class="modal-body">
                     <label>{{__('customer.name')}} * </label>
                     <div class="form-group">
                         <input type="text" name="name" id="name" class="form-control">
                     </div>
                     <label>{{__('general.phone')}} </label>
                     <div class="form-group">
                         <input type="number" name="phone" class="form-control">
                     </div>
                     <label>{{__('general.email')}} </label>
                     <div class="form-group">
                         <input type="email" name="email" class="form-control">
                     </div>
                     <label>{{__('general.code')}} </label>
                     <div class="form-group">
                         <input type="text" name="code" class="form-control">
                     </div>
                     <label>{{__('general.city')}} </label>
                     <div class="form-group">
                         <input type="text" name="city" class="form-control">
                     </div>
                     {{-- <label>State </label>
                    <div class="form-group">
                        <input type="text" name="state" placeholder="State" class="form-control">
                    </div> --}}
                     <label>{{__('general.address')}} </label>
                     <div class="form-group">
                         <textarea class="form-control" name="address" id="address"></textarea>
                     </div>
                     <label>{{__('general.detail')}} </label>
                     <div class="form-group">
                         <textarea class="form-control" name="detail" id="detail"></textarea>
                     </div>
                 </div>
                 <div class="modal-footer">
                     <a href="javascript:void(0)" id="saveCustomer" class="btn btn-lg btn-block btn-rounded btn-primary ml-1">
                         <i class="bx bx-check d-block d-sm-none"></i>
                         <span class="d-none d-sm-block">{{__('sidebar.add_customer')}}</span>
                     </a>
                 </div>
             </form>
         </div>
     </div>
 </div>

 <div class="modal fade" id="holdmodal" tabindex="-1" role="dialog" aria-labelledby="holdmodal" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full hold_modal" role="document">
         <div class="modal-content" style="height: 90vh">
             <div class="modal-header header-modal" style="height: 5vh;">
                 <h5 class="modal-title text-white" id="">{{__('pos.hold_transac')}}</h5>
                 <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                     <i data-feather="x"></i>
                 </button>
             </div>
             <div class="modal-body">

                 <div class="row" id="holdlist"> </div>

             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-lg btn-block btn-rounded btn-light-secondary" data-bs-dismiss="modal">
                     <span class="d-none d-sm-block">{{__('general.close')}}</span>
                 </button>
             </div>
         </div>
     </div>
 </div>