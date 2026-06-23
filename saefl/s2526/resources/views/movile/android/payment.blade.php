@extends('movile.android.layouts.app')

@section('content')

        <div class="content p-2" style="background-image: url('{{asset('images/background/service/credicard_opacity.png')}}');">
            
            <div class="p-2 bg-white rounded shadow-sm">
                <img class="img-thumbnail " src="{{ asset('images/logo/btnPayment/tpv-bg.png') }}" alt="" width="96" height="96">
                <livewire:service.payment.button.credicard.index-component />
            </div>

        </div>

@endsection

@section('stylesheets')
	@parent
	<link href="{{ asset('vendor/stepper/css/bs-stepper.min.css') }}" rel="stylesheet">
@endsection
