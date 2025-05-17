<?php
$router->group(['prefix'=>'equipo-futbol'],function() use($router){
    
    $router->get('/acciones','EquipoFutbolController@getAcciones');

    $router->get('','EquipoFutbolController@getEquipos');

    $router->post('/{id:[0-9]+}','EquipoFutbolController@createEquipoFutbol');

    $router->put('/{id:[0-9]+}','EquipoFutbolController@updateEquipoFutbol');

    $router->get('/socios','EquipoFutbolController@getSocios');

    $router->get('/{id:[0-9]+}/jugadores','EquipoFutbolController@getJugadoresEquipo');
    
    $router->get('/{id:[0-9]+}/jugadores-activo','EquipoFutbolController@getJugadoresEquipoActivo');

    $router->post('/{id:[0-9]+}/jugador','EquipoFutbolController@addJugador');

    $router->put('/{id:[0-9]+}/jugador','EquipoFutbolController@bajaJugador');

    $router->get('/credencial','EquipoFutbolController@createCredenciales');

    $router->get('/foto','EquipoFutbolController@getViewFoto');
    
    $router->get('/reporte-jugadores/{id:[0-9]+}','EquipoFutbolController@getReporteJugadores');
});