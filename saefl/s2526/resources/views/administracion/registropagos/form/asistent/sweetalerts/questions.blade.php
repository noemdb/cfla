@section('scripts')
    @parent
    <script type="text/javascript">
        $(document).ready( function(){
            Swal.fire({
                icon: 'success',
                title: 'Registro realizado exitosamente...',
                html: 'N. Facturqción:<h5>{{$pago_combinado_id ?? null}}</h5>Click en el botón <b>Ok</b> para continuar con otro registro de pago de éste representante. <hr> <p>Sí es necesario, realice el ajuste con los datos precargados</p>',
                footer: '<a href="{{ route('administracion.representants.recibo.pdf',$pago_combinado_id) }}" target="_blank">Imprimir recibo</a>'
            })
        });
    </script>
@endsection
