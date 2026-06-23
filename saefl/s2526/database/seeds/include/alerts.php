<?php 

$alertestados = ['Visto','No Visto'];
foreach ($alertestados as $key => $value) {
    DB::table('select_opts')->insert([
        'table' => "alerts",
        'name' => "estado",
        'value' => $value,
        'view' => "alerts.create",
    ]);
}

$talertatipo = ['primary'=>'Primaria', 'secondary'=>'Secundaria', 'success'=>'Alternativa', 'info'=>'Informativa', 'warning'=>'De alerta', 'danger'=>'Importante'];
foreach ($talertatipo as $key => $value) {
    DB::table('select_opts')->insert([
        'table' => "alerts",
        'name' => "tipo",
        'key' => $key,
        'value' => $value,
        'view' => "alerts.create",
    ]);
}

?>