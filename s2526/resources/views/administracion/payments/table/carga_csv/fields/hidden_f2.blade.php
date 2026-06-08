<span class=" btn badge badge-success p-1 m-1" title="REFERENCIA ENCONTRADA EN MOV. BCO. CSV">
    SI
</span>

{{ Form::hidden('index['.$loop->iteration.']' , $loop->iteration ) }}
{{ Form::hidden('representant_id['.$loop->iteration.']' , $datas['representant_id']) }}
{{ Form::hidden('method_pay_id['.$loop->iteration.']' , $datas['method_pay_id'] ) }}
{{ Form::hidden('number_i_pay['.$loop->iteration.']' , $datas['number_i_pay'] ) }}
{{ Form::hidden('banco_id['.$loop->iteration.']' , $datas['banco_id'] ) }}
{{ Form::hidden('ingreso_ammount['.$loop->iteration.']' , $datas['ingreso_ammount'] ) }}
{{ Form::hidden('date_transaction['.$loop->iteration.']' , $datas['date_transaction'] ) }}
{{ Form::hidden('contact['.$loop->iteration.']' , $datas['contact'] ) }}
{{ Form::hidden('comment['.$loop->iteration.']' , $datas['comments'] ) }}