@extends('ecommerce::layouts.mobile')
@section('content')
<div class="header fixed-top line4-bt">
    <div class="left">
        <a href="{{ route('ecommerce.mobile.dashboard') }}" class="icon back-btn"><i class="icon-left-btn"></i></a>
    </div>
    <h6>Alamat Saya</h6>

</div>
<div class="app-content style-7">
    <div class="py-16 bg-white">
        <div class="tf-container">
            @foreach ($data as $address)
            <a href="{{route('ecommerce.mobile.address.update',$address->id)}}" class="d-flex align-items-center gap-20 pb-16 line-bt">
                <span class="flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                        <path d="M3 9.5L12 2.5L21 9.5V20.5C21 21.0304 20.7893 21.5391 20.4142 21.9142C20.0391 22.2893 19.5304 22.5 19 22.5H5C4.46957 22.5 3.96086 22.2893 3.58579 21.9142C3.21071 21.5391 3 21.0304 3 20.5V9.5Z" stroke="#787982" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M9 22.5V12.5H15V22.5" stroke="#787982" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <div class="content flex-grow-1">
                    <h6>{{$address->name}} @if($address->default == 'yes') ( Default ) @endif  </h6>
                    <p class="mt-4 text-caption text-secondary">{{$address->phone}}</p>
                    <p class="mt-4 text-caption text-secondary">{{$address->address}} </p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    <div class="mt-16">
        
    </div>

</div>
<div class="footer-fixed p-16">
    <a href="{{route('ecommerce.mobile.address.create')}}" class="tf-btn primary">Tambah Alamat</a>
</div>
@endsection

@section('scripts')
<script>

</script>
@endsection