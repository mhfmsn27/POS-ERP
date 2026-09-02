@extends('layouts.pos')

@section('styles')

@endsection

@section("content")
<div id="app" class="pos-page">
    <!-- <div id="main-content">
        <x-pos.footer-component></x-pos.footer-component>
    </div> -->
</div>
@endsection


@section('scripts')
<script src="{{ asset('js/pos.js') }}"></script>
@endsection