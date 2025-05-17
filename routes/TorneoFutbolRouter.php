<?php
$router->group(['prefix'=>'torneo-futbol'],function() use($router){
    
    $router->get('','TorneoFutbolController@getTorneos');

    // $router->get('/{id:[0-9]+}','TorneoFutbolController@getEquipos');

    $router->post('','TorneoFutbolController@createTorneoFutbol');

    $router->put('/{id:[0-9]+}','TorneoFutbolController@updateEquipoFutbol');

    $router->get('/socios','TorneoFutbolController@getSocios');

    $router->get('/{id:[0-9]+}/jugadores','TorneoFutbolController@getJugadoresEquipo');
    
    $router->get('/{id:[0-9]+}/jugadores-activo','TorneoFutbolController@getJugadoresEquipoActivo');

    $router->post('/{id:[0-9]+}/jugador','TorneoFutbolController@addJugador');

    $router->put('/{id:[0-9]+}/jugador','TorneoFutbolController@bajaJugador');

    $router->get('/credencial','TorneoFutbolController@createCredenciales');

    $router->get('/foto','TorneoFutbolController@getViewFoto');
});