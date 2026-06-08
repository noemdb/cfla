<div class="card {{ isset($textalign) ?  $textalign:'text-justify'}} bd-callout bd-callout-{{$class ?? ''}} mt-2" id="panel-{{ $id ?? '' }}" {{ isset($height) ? ' style=height:'.$height :null }} >

    @isset($imgurl)
        <img class="card-img-top" src="{{$imgurl}}" alt="Card image cap">
    @endisset

    @isset($header)
        <div class="card-header py-2">
            <i class="{{ $iconTitle ?? '' }} text-{{ $class ?? 'default' }}"></i>
            <strong class="text-{{ $class ?? 'default' }}">{{ $header }}</strong>

            @isset($panelControls)
                <div class="float-right">
                    <a id="minimizer-{{ $id ?? '1' }}" data-id="collapse-{{ $id ?? '1' }}" class="text text-info p-1" data-toggle="collapse">
                        <i class="fas fa-chevron-up"></i>
                    </a>
                    <a id="close-{{ $id ?? '1' }}" data-id="panel-{{ $id ?? '1' }}" class="text text-danger p-1" data-toggle="collapse">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            @endisset

        </div>
    @endisset

    <div id="collapse-{{ $id ?? '' }}" class="collapse {{ isset($collapse) ?  $collapse:'show'}}">

        @isset($body)
            <div class="card-body p-1 m-1">
                {{$body}}
                @isset($buttomtext)
                  <a href="{{$buttomurl ?? ''}}" class="btn btn-{{$btnclass ?? 'primary'}} float-right">{{$buttomtext ?? ''}}</a>
                @endisset
            </div>
        @endisset

        @isset($footertext)
            <div class="card-footer text-muted">
                {{$footertext}}
            </div>
        @endisset

    </div>

</div>


{{-- variables de entrada class,id,panelTitle,badge,panelBody,panelFooter --}}

@section('scripts')
    @parent
    <script type="text/javascript">

        $(function(){
        $('#close-{{ $id ?? '1' }}').on('click',function(){
                var idpanel = $(this).data('id'); //alert('123');
                $('#'+idpanel).fadeOut();
            })
        })

        $(function(){
        $('#minimizer-{{ $id ?? '1' }}').on('click',function(){
                var collapse = $(this).data('id'); //alert(collapse);
                $('#'+collapse).collapse('toggle');
                $(this).children('i').toggleClass('fa-chevron-up fa-chevron-down')
            })
        })
    </script>
@endsection

