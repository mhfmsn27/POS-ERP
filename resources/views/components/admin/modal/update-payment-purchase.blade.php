<div class="modal fade" id="updatepayment" tabindex="-1" role="dialog" aria-labelledby="update-payment" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
        <form method="POST" id="updatePaymentPurchase" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="">{{__('purchase.change_payment_status')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <i data-feather="x"></i> </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="system_name">{{__('general.payment_status')}}</label>
                    <input type="hidden" name="id" id="up" value="">
                    <select class="form-control" name="payment_status">
                        <option value="paid">{{__('general.paid')}}</option>
                        <option value="due">{{__('general.po_due')}}</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">{{ __('general.close') }}</span>
                </button>
                <button type="submit" class="btn btn-primary ml-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">{{ __('general.save') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>