<?php
$router->group(['prefix'=>'curso-verano'],function() use($router){
    
    // $router->get('','CursoVeranoController@getApartadosVivo');
    // $router->get('/reporte-horarios','CursoVeranoController@getCanchasHorarios');
    
    $router->get('/','CursoVeranoController@InscripcionesCurso');
    
    $router->get('/cargo-by-folio','CursoVeranoController@getCargoCursoVeranoByFolio');
    
    $router->get('/persona-exist','CursoVeranoController@getInscritoExistente');
    
    $router->get('/programa','CursoVeranoController@getProgramasCursoVerano');

    $router->get('/programa-grupo','CursoVeranoController@getGrupoCursoVerano');
    
    $router->post('/inscripcion','CursoVeranoController@createInscripcion');
    
    $router->get('/socios-in-accion','CursoVeranoController@getSociosAccion');

    $router->get('/foto','CursoVeranoController@getViewFoto');
    
    $router->get('/semanas-restantes','CursoVeranoController@getSemanasRestantes');
    
    $router->get('/colaborador','CursoVeranoController@getColaboradorByNomina');
    
    $router->delete('/baja','CursoVeranoController@bajaCursoVerano');
    
    $router->get('/reporte','CursoVeranoController@reporteCursoVerano');

});