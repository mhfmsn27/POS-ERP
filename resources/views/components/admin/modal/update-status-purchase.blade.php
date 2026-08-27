<div class="modal fade" id="updatestatus" tabindex="-1" role="dialog" aria-labelledby="update-status" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
        <form method="POST" id="changeReceivedStatus" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="">{{__('general.change_status')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <i data-feather="x"></i> </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="system_name">{{__('purchase.received_status')}}</label>
                    <input type="hidden" name="id" id="ti" value="">
                    <select class="form-control" name="status">
                        <option value="received">{{__('purchase.received')}}</option>
                        <option value="order">{{__('purchase.ordered')}}</option>
                        <option value="pending">{{__('purchase.pending')}}</option>
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