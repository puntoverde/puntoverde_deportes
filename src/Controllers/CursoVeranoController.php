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
        //  "cve_cargo"=>"required",
         "cve_cuota"=>"required",
         "cve_persona"=>"required",
         "cve_accion"=>"required",
        //  "folio"=>"required",
         "folio_boleta"=>"required",
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

    public function InscripcionesCurso()
    {
       return CursoVeranoDAO::getInscripcionesCurso(); 
    }

    public function getViewFoto(Request $req)
    {    
        // $foto=$req->input('foto');
        //  $img=file_get_contents("../upload/$foto");
        //  return response($img)->header('Content-type','image/png');

        $cve_persona=$req->input("cve_persona");
        $img= CursoVeranoDAO::getFotoSocio($cve_persona);                            
        return response($img)->header('Content-type','image/png');
    }

    public function getSemanasRestantes(Request $req)
    {
         $cve_curso_inscripcion=$req->input("cve_curso_inscripcion");
        $semanas= CursoVeranoDAO::getSemanasRestantes($cve_curso_inscripcion);   
        return response()->json($semanas);
    }
    
    public function getColaboradorByNomina(Request $req)
    {
        $nomina=$req->input("nomina");
        $colaborador= CursoVeranoDAO::getColaboradorByNomina($nomina);   
        return response()->json($colaborador);
    }
    
    public function bajaCursoVerano(Request $req)
    {
        $cve_curso_inscripcion=$req->input("cve_curso_inscripcion");
        return CursoVeranoDAO::bajaCursoVerano($cve_curso_inscripcion);   
       
    }
    
    public function reporteCursoVerano(Request $req)
    {
        $data_send=(object)$req->all();
        return CursoVeranoDAO::reporteCursoVerano($data_send);   
       
    }

   
}