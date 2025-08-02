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

    

});