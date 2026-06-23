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

?>