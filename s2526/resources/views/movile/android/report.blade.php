@extends('movile.android.layouts.app')

@section('content')

        <div class="content py-2 px-1">

            {{-- <img class="img-thumbnail bi pe-none" src="{{ asset('images/logo/btnPayment/tpv-bg.png') }}" alt="" width="96" height="96"> --}}

            <livewire:welcome.payment.report.list-component />

        </div>

@endsection

@section('stylesheets')
	@parent
	<link href="{{ asset('vendor/stepper/css/bs-stepper.min.css') }}" rel="stylesheet">
@endsection


@section('sweetalert')
    @parent
    <script>
        window.addEventListener('swal',function(e){
            Swal.fire(e.detail);
        });

        window.addEventListener('swal:confirm',function(e){

            Swal.fire({
                title: e.detail.message,
                text: e.detail.text,
                icon: e.detail.type,
                buttons: true,
                dangerMode: true,
                showCancelButton: true,
            })
            .then((result) => {
                if (result.value) {
                    window.livewire.emit('remove',e.detail.id);
                }
            });
        });

        window.addEventListener('swal:question',function(e){
            Swal.fire({
                title: e.detail.message,
                text: e.detail.text,
                icon: e.detail.type,
                buttons: true,
                dangerMode: true,
                showCancelButton: true,
            })
            .then((result) => {
                if (result.value) {
                    window.livewire.emit(e.detail.method,e.detail.id);
                }
            });
        });

    </script>
@endsection
