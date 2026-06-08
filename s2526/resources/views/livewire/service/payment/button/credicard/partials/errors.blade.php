@if ($errors->any())
    <div class="alert alert-danger py-2 my-2 text-start">
        @foreach ($errors->all() as $error)
            <div class="border border-bottom-0">{{$loop->iteration}}.- {{ $error }}</div>
        @endforeach
    </div>
@endif

@if (Session::has('messengeError')) <div class="alert alert-warning text-start"> {!!Session::get('messengeError')!!} </div> @endif

@if (Session::has('messengeErrorException')) <div class="alert alert-secondary text-start"> {!!Session::get('messengeErrorException')!!} </div> @endif
