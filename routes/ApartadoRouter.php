<?php
$router->group(['prefix'=>'apartados'],function() use($router){
    
    $router->get('','ApartadoController@getApartadosVivo');
    $router->get('/reporte-horarios','ApartadoController@getCanchasHorarios');

});