<div class=" h-100 border float-right w-25" data-url='{{ route("api_fix_registro_pagos", ["start"=>"_start_","size"=>"_size_"] )}}'>
    <div class=" font-weight-bold text-center">
        Pagos con irregularidades
    </div>
    <div id="container"></div>
</div>

@section('scripts')
    @parent
    <script>
        $(document).ready(function () {
            var count = {{ $registro_pago_combinados->count() ?? 0 }}
            var start = 1;
            var size = count < 100 ? count : 100;
            fix_registro_pagos(start,size,count);
         });

        function fix_registro_pagos(start=1,size=100,count=100){
            var container = '#container'; //console.log(container);

            var ajaxurl = '{{ route("api_fix_registro_pagos", ["start"=>"_start_","size"=>"_size_"] )}}'; //console.log(ajaxurl);

            ajaxurl = ajaxurl.replace('_start_', start).replace('_size_', size); //console.log(ajaxurl);

            $.ajax({
                url: ajaxurl,
                type: "GET",
                success: function(data){

                    $(container).append(data);

                    start = start + size ;
                    if ( start < count ) {
                        size = (start+size) > count ? (count - start) : 100;
                        setTimeout ( fix_registro_pagos(start, size, count), 60000 );
                    }

                }
            });
        }
    </script>

@endsection
