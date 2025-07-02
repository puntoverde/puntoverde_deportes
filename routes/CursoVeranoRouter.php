<?php
$router->group(['prefix'=>'curso-verano'],function() use($router){
    
    // $router->get('','CursoVeranoController@getApartadosVivo');
    // $router->get('/reporte-horarios','CursoVeranoController@getCanchasHorarios');
    
    
    $router->get('/cargo-by-folio','CursoVeranoController@getCargoCursoVeranoByFolio');
    
    $router->get('/persona-exist','CursoVeranoController@getInscritoExistente');
    
    $router->get('/programa','CursoVeranoController@getProgramasCursoVerano');

    $router->get('/programa-grupo','CursoVeranoController@getGrupoCursoVerano');
    
    $router->post('/inscripcion','CursoVeranoController@createInscripcion');
    
    $router->get('/socios-in-accion','CursoVeranoController@getSociosAccion');

});