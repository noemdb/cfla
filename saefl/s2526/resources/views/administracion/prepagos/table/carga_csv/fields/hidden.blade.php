{{ Form::hidden('representant_id['.$loop->iteration.']' , $prepagoCSV['representant_id'] ,['id'=>'representant_id_'.$loop->iteration]) }}
{{ Form::hidden('method_pay_id['.$loop->iteration.']' , $prepagoCSV['method_pay_id'] ,['id'=>'method_pay_id_'.$loop->iteration]) }}
{{ Form::hidden('banco_id['.$loop->iteration.']' , $prepagoCSV['banco_id'] ,['id'=>'banco_id_'.$loop->iteration]) }}
{{ Form::hidden('number_i_pay['.$loop->iteration.']' , $prepagoCSV['number_i_pay'] ,['id'=>'number_i_pay_'.$loop->iteration]) }}
{{ Form::hidden('ingreso_ammount['.$loop->iteration.']' , $prepagoCSV['ingreso_ammount'] ,['id'=>'ingreso_ammount_'.$loop->iteration]) }}
{{ Form::hidden('date_transaction['.$loop->iteration.']' , $prepagoCSV['date_transaction'] ,['id'=>'date_transaction_'.$loop->iteration]) }}
{{ Form::hidden('ingreso_observations['.$loop->iteration.']' , $prepagoCSV['telefono'] ,['id'=>'ingreso_observations_'.$loop->iteration]) }}
{{ Form::hidden('comment['.$loop->iteration.']' , $prepagoCSV['comment'] ,['id'=>'comment_'.$loop->iteration]) }}
