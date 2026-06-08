<?php 

$arr_settname = [

    //view report
    'numregpag_userlist' => 'numregpag_userlist',
    'numregpag_profilelist' => 'numregpag_profilelist',
    'numregpag_rollist' => 'numregpag_rollist',

    //view topnavbar
    'topnavbar_messages' => 'topnavbar_messages',
    'topnavbar_tasks' => 'topnavbar_tasks',
    'topnavbar_alerts' => 'topnavbar_alerts',
    'topnavbar_logdbs' => 'topnavbar_logdbs',
    'topnavbar_loginouts' => 'topnavbar_loginouts',

    //sidebar nivel 1
    'sidebar_search' => 'sidebar_search',
    'sidebar_dashboard' => 'sidebar_dashboard',
    'sidebar_modelos' => 'sidebar_modelos',
    'sidebar_chart' => 'sidebar_chart',
    'sidebar_forms' => 'sidebar_forms',
    'sidebar_tables' => 'sidebar_tables',

    //sidebar nivel 2
    'sidebar_models_users' => 'sidebar_models_users',
    'sidebar_models_profiles' => 'sidebar_models_profiles',
    'sidebar_models_rols' => 'sidebar_models_rols',
    'sidebar_models_messenges' => 'sidebar_models_messenges',
    'sidebar_models_tasks' => 'sidebar_models_tasks',
    'sidebar_models_alerts' => 'sidebar_models_alerts',
    'sidebar_models_logdbs' => 'sidebar_models_logdbs',
    'sidebar_models_loginouts' => 'sidebar_models_loginouts',

    //sidebar nivel 3
    'sidebar_models_users_crud' => 'sidebar_models_users_crud',
    'sidebar_models_users_chart' => 'sidebar_models_users_chart',
    'sidebar_models_profiles_crud' => 'sidebar_models_profiles_crud',
    'sidebar_models_profiles_chart' => 'sidebar_models_profiles_chart',
    'sidebar_models_rols_chart' => 'sidebar_models_rols_chart',
    'sidebar_models_rols_crud' => 'sidebar_models_rols_crud',
    'sidebar_models_messenges_crud' => 'sidebar_models_messenges_crud',
    'sidebar_models_messenges_chart' => 'sidebar_models_messenges_chart',
    'sidebar_models_tasks_crud' => 'sidebar_models_tasks_crud',
    'sidebar_models_tasks_chart' => 'sidebar_models_tasks_chart',
    'sidebar_models_alerts_crud' => 'sidebar_models_alerts_crud',
    'sidebar_models_alerts_chart' => 'sidebar_models_alerts_chart',
    'sidebar_models_logdbs_crud' => 'sidebar_models_logdbs_crud',
    'sidebar_models_logdbs_chart' => 'sidebar_models_logdbs_chart',
    'sidebar_models_loginouts_crud' => 'sidebar_models_loginouts_crud',
    'sidebar_models_loginouts_chart' => 'sidebar_models_loginouts_chart'
    ];
    foreach ($arr_settname as $key => $value) {
        DB::table('select_opts')->insert([
            'table' => "settings",
            'name' => "name",
            'key' => $key,
            'value' => $value,
            'view' => "settings.create",
        ]);
    }

?>