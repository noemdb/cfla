<span class=" btn badge badge-success p-1 m-1">
    SI
</span>
{{ Form::hidden('grado_id['.$datas['estudiant_id'].']' , $datas['grado_id'] ) }}
{{-- {{ Form::hidden('data_ge_name['.$datas['estudiant_id'].']' , $datas['data_ge_name'] ) }} --}}
{{ Form::hidden('grupo_estable_id['.$datas['estudiant_id'].']' , $datas['grupo_estable_id'] ) }}
{{ Form::hidden('comments['.$datas['estudiant_id'].']' , $datas['comments'] ) }}