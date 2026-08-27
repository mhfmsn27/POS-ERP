@extends('layouts.pos')

@section('content')
<div class="row pos-content">
        <x-pos.product-component></x-pos.product-component>
        <x-pos.bill-component></x-pos.bill-component>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/pos.js') }}"></script>
<script src="{{ asset('js/mobile.js')}}"></script>
@endsection