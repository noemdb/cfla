
<div class="modal fade" id="{{$id_modal ?? ''}}" tabindex="-1" role="dialog" aria-labelledby="CreateNomConceptModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form class="form-signin" action="{{ route('administracion.deudas_anterior.update',$id) }}" method="PUT" id="update_Form">
            <div class="modal-header alert-secondary">
                <h5 class="modal-title" id="CreateNomConceptModalLabel">Editar monto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>            
            <div class="modal-body">                 
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Monto" aria-label="Moto" aria-describedby="button-addon2" id="concepto_ammount" name="concepto_ammount">
                    <div class="input-group-append">
                        <button type="button" id="btn-create-nom-concetp" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </div>
            {{ csrf_field() }}
            </form>
        </div>
    </div>
</div>


@section('scripts')
    @parent


    
    <script type="text/javascript">
        $(document).ready(function() {
            $('#btn-create-nom-concetp').click(function (e) {
                e.preventDefault();
                // var idform = '#registerForm'; console.log('idform: '+idform);
                // var form = $(idform); console.log('form_obj: '+form);

                var form = $(this.form);

                var url = form.attr('action'); console.log('url: '+url);
                var formData = form.serialize(); console.log('data: '+formData);
                $.ajax({
                    type: 'POST',
                    url: '{{ route("administracion.ajax.modal.create.nom_concepto") }}',
                    data: formData,
                    dataType: 'json',
                    statusCode: {
                        200: function () {
                            console.log("received");
                            location.reload(true);
                        },
                        500: function(data) {
                            console.log("not received");
                            console.log(data);
                        }
                    }
                });
            });
        });

        // $(document).ready(function() {
        //     $('#btn-create-nom-concetp').click(function (e) {
        //         e.preventDefault();
        //         console.log('123');

        //         var name = $('#name').val();		
        //         var data = 'name='+name+'?_token={{ csrf_token()}}';

        //         var name = $('#name').val();		
        //         var data = 'name='+name;
        //         var url = "{{ route('administracion.ajax.modal.create.nom_concepto') }}";
        //         $.post(url, data, function (result){
        //             alert('correcto');                    
        //             location.reload(true);
        //         }).fail(function (result) {
        //             $.each(result.responseJSON.errors,function(index,valor){

        //             });
        //         });
        //     });
        // });

        // $(document).ready(function() {	
        //   $('#btn-create-nom-concetp').click(function (e) {
        //       e.preventDefault();
        //       console.log('123');
            //   $('#result-ci_estudiant').html('<i class="fas fa-spinner"></i>').fadeOut(500);
      
            //   var ci_estudiant = $('#ci_estudiant').val();		
            //   var dataString = 'ci_estudiant='+ci_estudiant;
      
            //   $.ajax({
            //       type: "GET",
            //       url: "{{ route('administracion.ajax.validate.exist.studiant_ci') }}",
            //       data: dataString,
            //       success: function(data) {
            //         $("#result-ci_estudiant").removeClass('d-none');
            //         $("#result-ci_estudiant").addClass('d-block');
            //         $('#result-ci_estudiant').fadeIn(500).html(data);
            //       }
            //   });
        //     });              
        //   }); 
    </script>
@endsection


