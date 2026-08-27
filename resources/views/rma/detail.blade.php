@extends('layouts.rma')

@section('styles')
<style>

</style>
@endsection

@section('content')

<div class="container-fluid p-4">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row border-bottom">
                        <div class="col-lg-6">
                            <img src="{{asset('images/logo.png')}}" style="max-height: 50px; width:auto" alt="POSHUB Logo" />
                        </div>
                        <div class="col-lg-6 align-self-center text-right">
                            <h2 class="name">{{$transaction->store->name ?? ''}}</h2>
                            <div>+{{$transaction->store->phone ?? ''}}</div>
                            <div>{{$transaction->store->address ?? ''}}</div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-12 mt-3 row">
                            <div class="col-lg-6">
                                <div class="to">No.Ref : {{$transaction->ref_no}}</div>
                                <div class="name">Nama Pemilik : {{$transaction->customer_name ?? ($transaction->customer->name ?? '')}}</div>
                                <div class="address">Alamat : {{$transaction->customer->address ?? ''}} </div>
                                <div class="email">No HP : {{$transaction->phone ?? ($transaction->customer->phone ?? '')}} </div>
                            </div>
                            <div class="col-lg-6 text-right">
                                <div class="date">Tanggal : {{$transaction->created_at->format('Y-m-d')}}</div>
                                <div class="date">Estimasi Selesai : {{substr($transaction->estimate_date,0,10)}}</div>
                                <div class="date">Estimasi Biaya : Rp. {{number_format($transaction->estimate_price)}} </div>
                            </div>
                        </div>
                        <div class="col-lg-12 mt-3">
                            <div class="table-responsive-sm">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Nama Barang</th>
                                            <th scope="col">Keluhan </th>
                                            <th scope="col">Kelengkapan </th>
                                            <th scope="col">Aksi </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transaction->details as $detail)
                                        <tr>
                                            <td>{{$detail->product_name}} </td>
                                            <td>{{$detail->complaint}} </td>
                                            <td>{{$detail->completeness}} </td>
                                            <td>
                                                <button class="btn btn-info" type="button" data-toggle="modal" data-target="#track{{$detail->id}}">
                                                    <i class="fa fa-list"></i> Lihat Progress
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-sm-12 mt-5">
                            <b class="text-danger">Catatan:</b>
                            <p>{{$transaction->note}}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@foreach ($transaction->details as $detail)
<div class="modal fade bd-example-modal-xl" id="track{{$detail->id}}" tabindex="-1" role="dialog" aria-labelledby="trackTitle" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalScrollableTitle">Progress {{$detail->product_name}} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body row p-3">
                <div class="col-12">
                    <ul class="iq-timeline">
                        @foreach ($detail->record as $record)
                        <li>
                            <div class="timeline-dots border-info"></div>
                            <h6 class="float-left mb-1">{{$record->status_name}}</h6>
                            <small class="float-right mt-1">{{$record->created_at->format('Y-m-d H:i')}}</small>
                            <div class="d-inline-block w-100">
                                <p>{{$record->subject}}</p>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection