{{ Form::hidden('number_i_pay['.$loop->iteration.']' , $mbancarioCSV['number_i_pay'] ,['id'=>'number_i_pay_'.$loop->iteration]) }}
{{ Form::hidden('ingreso_ammount['.$loop->iteration.']' , $mbancarioCSV['ingreso_ammount'] ,['id'=>'ingreso_ammount_'.$loop->iteration]) }}
{{ Form::hidden('date_transaction['.$loop->iteration.']' , $mbancarioCSV['date_transaction'] ,['id'=>'date_transaction_'.$loop->iteration]) }}
