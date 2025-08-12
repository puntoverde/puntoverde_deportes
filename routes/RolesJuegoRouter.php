<?php
$router->group(['prefix'=>'roles-juego'],function() use($router){
    
    // $router->get('','RolesJuegoController@getApartadosVivo');
    // $router->get('/reporte-horarios','RolesJuegoController@getCanchasHorarios');
    
    $router->get('/torneos','RolesJuegoController@getTorneo');
    
    $router->get('/equipos-by-torneo','RolesJuegoController@getEquiposByTorneo');
    
    $router->get('/fixture','RolesJuegoController@getFixture');
    
    $router->post('/registrar-fixture','RolesJuegoController@createFixture');
    
    $router->put('/asignar-fecha','RolesJuegoController@AsignarFechaPartido');
    
    $router->put('/suspende-partido','RolesJuegoController@SuspenderPartido');

    $router->put('/gol','RolesJuegoController@AgregarGol');

    $router->put('/amarilla','RolesJuegoController@AgergarTarjetaAmarilla');
    
    $router->put('/roja','RolesJuegoController@AgergarTarjetaRoja');
    
    $router->get('/horario-partidos','RolesJuegoController@getHorarioFutbol');
    
    $router->get('/jugadores-partido','RolesJuegoController@getJugadorEquipoPartido');
    
    $router->get('/foto-jugador','RolesJuegoController@getViewFotoJugador');
    
    $router->get('/estadisticas','RolesJuegoController@getEstadisticasPartido');
    
    $router->get('/tabla-general','RolesJuegoController@tblaGeneral');
    
    $router->get('/estadisticas-gol','RolesJuegoController@getEstadisticasGoles');
    
    $router->get('/estadisticas-amarillas','RolesJuegoController@getEstadisticasAmarillas');
    
    $router->get('/estadisticas-rojas','RolesJuegoController@getEstadisticasRojas');

    

});