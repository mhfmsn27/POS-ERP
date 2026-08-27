<div class="modal fade" id="showPayment" tabindex="-1" role="dialog" aria-labelledby="add-pay" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full modal-xl payment_modal" role="document">
        <div class="modal-content">
            <div class="modal-header header-modal "> 
                <h5 class="modal-title" id="">Lihat Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <i class="fa fa-times"></i> </button>
            </div>
            <div class="modal-body">
                <div class="col-12 p-2">
                    <table class="table">
                        <thead>
                            <tr class="table-primary">
                                <th scope="col">Tanggal Pembayaran</th>  
                                <th scope="col">Metode Pembayaran</th>
                                <th scope="col">Total Pembayaran</th>
                                <th scope="col">Dari Bank</th>
                                <th scope="col">Ke Bank</th>
                                <th scope="col">Nomor Rekening</th>
                                <th scope="col">Bukti TF</th> 
                                <th scope="col">Status</th> 
                                <th scope="col">Aksi </th>
                            </tr>
                        </thead>
                        <tbody id="paymentList">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-block btn-danger ml-1" data-dismiss="modal">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">{{ __('general.close') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>