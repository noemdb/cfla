@section('scripts')
    @parent
    <script type="text/javascript">
        $(document).ready( function(){
            Swal.fire({
                icon: 'error',
                title: 'Ocurrieron errores',
                html: 'Click en el botón <b>Ok</b> para intentar nuevamente un registro de pago de éste representante',
                footer: '<a href="{{ route('administracion.registropagos.asistent')}}">Iniciar el Asistente</a>'
            })
        });
    </script>
@endsection
