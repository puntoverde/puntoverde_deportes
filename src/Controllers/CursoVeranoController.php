<?php

namespace App\Controllers;

use App\DAO\CursoVeranoDAO;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class CursoVeranoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }


    public function getCargoCursoVeranoByFolio(Request $req)
    {
        $reglas = ["folio"=>"required"];        
        $this->validate($req, $reglas);
        return CursoVeranoDAO::getCargoCursoVeranoByFolio($req->input("folio"));
    }
    
    public function getInscritoExistente(Request $req)
    {
        $reglas = ["cve_persona"=>"required"];        
        $this->validate($req, $reglas);
        return response()->json(CursoVeranoDAO::getInscritoExistente($req->input("cve_persona")));
    }
    
    public function getProgramasCursoVerano(Request $req)
    {
        $reglas = ["curso"=>"required"];        
        $this->validate($req, $reglas);
        return CursoVeranoDAO::getProgramasCursoVerano($req->input("curso"));
    }
    
    public function getGrupoCursoVerano(Request $req)
    {
        $reglas = ["programa"=>"required"];        
        $this->validate($req, $reglas);
        return CursoVeranoDAO::getGrupoCursoVerano($req->input("programa"));
    }
    
    public function createInscripcion(Request $req)
    {
        $reglas = [
         "cve_curso_verano"=>"required",
         "programa"=>"required",
         "grupo"=>"required",
         "cve_cargo"=>"required",
         "cve_cuota"=>"required",
         "cve_persona"=>"required",
         "cve_accion"=>"required",
         "folio"=>"required",
         "tutor"=>"required",
         "telefono_contacto"=>"required",
         "nadar"=>"required",

         "nombre"=>"required",
         "paterno"=>"required",
         "materno"=>"required",
         "nacimiento"=>"required|date",
         "genero"=>"required",
         "calle_numero"=>"required",
         "colonia"=>"required",
         "observaciones"=>"required"
        ];        



        $this->validate($req, $reglas);
        return CursoVeranoDAO::createInscripcion((object)$req->all());
    }

    public function getSociosAccion(Request $req)
    {
        $reglas = ["cve_accion"=>"required"];        
        $this->validate($req, $reglas);
        $cve_accion=$req->input("cve_accion");
        return CursoVeranoDAO::getSociosAccion($cve_accion);
    }

   
}