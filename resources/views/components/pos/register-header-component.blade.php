<div class="row pos-top">
    <div class="table-responsive">
        <table class="table table-header-pos">
            <tr>
                <th class="formsearch">
                    @if($getShift != null)
                    <h3 class="text-white mt-2">Sedang Buka</h3>
                    @else
                    <h2 class="text-white">{{date("d M, Y")}} </h2>
                    @endif
                </th>
                <th class="text-right">
                    <a data-bs-container="#tooltip-container" data-bs-toggle="tooltip" data-bs-placement="right" title="Kembali Ke Halaman POS" href="{{route('pos.index')}}" class="btn btn-lg btn-light btn-rounded float-end" style="margin-right: 5px;">
                        <i class="fa fa-desktop"></i> POS
                    </a>
                    @if($getShift != null)
                    <a data-bs-container="#tooltip-container" data-bs-toggle="tooltip" data-bs-placement="right" title="Tutup Shift Register" href="{{route('register.close')}}" class="btn btn-lg btn-danger btn-rounded float-end" style="margin-right: 5px;">
                        <i class="fas fa-power-off"></i>
                    </a>
                    @endif
                </th>
            </tr>
        </table>
    </div>
</div>