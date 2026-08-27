<div class="modal fade" id="addpay" tabindex="-1" role="dialog" aria-labelledby="add-pay" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full modal-xl payment_modal" role="document">
        <form method="POST" id="addPaymentPurchase" class="modal-content">
            @csrf
            <div class="modal-header header-modal ">
                <input type="hidden" name="transaction_id" id="tri" value="">
                <h5 class="modal-title" id="">{{__('general.add_payment')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <i data-feather="x"></i> </button>
            </div>
            <div class="modal-body">
                <div class="form form-horizontal">
                    <div class="form-body p-2">
                        <div class="row payment_modal_" id="paymentsession">
                            <div class="col-md-6 form-group">
                                <label>{{__('general.payment_method')}}</label>
                                <select class=" form-control" name="payment_method" id="payment_method">
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="card">Card</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Integrasikan Dengan Account</label>
                                <select class=" form-control" name="account_id" id="account_id">
                                    {{my_account()}}
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{__('general.payment_date')}}</label>
                                <div class="input-group mb-3">
                                    <input type="hidden" id="maxPayment">
                                    <input type="text" class="form-control" value="{{date("Y-m-d H:i:s")}}" id="paid_date" name="paid_date" readonly="">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="row" id="paymentprocess">
                                    <div class="col-md-6 form-group">
                                        <label>{{__('general.payment_total')}}</label>
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" value="0" id="payment_amount" name="payment_amount">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 form-group">
                                <label>{{__('general.payment_note')}}</label>
                                <textarea class="form-control" name="payment_note" id="paymentnote"></textarea>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">{{ __('general.close') }}</span>
                </button>
                <button type="submit" class="btn btn-primary ml-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">{{__('general.add_payment')}}</span>
                </button>
            </div>
        </form>
    </div>
</div>