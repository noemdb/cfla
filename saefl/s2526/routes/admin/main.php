<?php

    //INI Route iniciales
    require (__DIR__ . '/iniciales.php');
    //FIN Route iniciales

    //INI CRUD modelos
    require (__DIR__ . '/crud/resource.php');
    // require (__DIR__ . '/crud/showfull.php');
    // require (__DIR__ . '/crud/createwithid.php');
    //FIN CRUD modelos

    //INI Charts modelos
    require (__DIR__ . '/charts/users.php');
    require (__DIR__ . '/charts/profiles.php');
    require (__DIR__ . '/charts/rols.php');
    require (__DIR__ . '/charts/tasks.php');
    require (__DIR__ . '/charts/alerts.php');
    require (__DIR__ . '/charts/messeges.php');
    require (__DIR__ . '/charts/loginouts.php');
    require (__DIR__ . '/charts/logdbs.php');
    //FIN Charts modelos

    //INI rutas para los json
    // require (__DIR__ . '/json/index.php');
    //FIN rutas para los json

?>