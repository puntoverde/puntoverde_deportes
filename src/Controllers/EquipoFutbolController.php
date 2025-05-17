<?php

namespace App\Controllers;

use App\DAO\EquipoFutbolDAO;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class EquipoFutbolController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function getAcciones(Request $req)
    {
        return EquipoFutbolDAO::getAcciones();
    }

    public function getEquipos(Request $req)
    {
        // dd($req);
        return EquipoFutbolDAO::getEquipos((object)$req->all());
    }

    public function createEquipoFutbol($id, Request $req)
    {
        return EquipoFutbolDAO::createEquipoFutbol($id, (object)$req->all());
    }

    public function updateEquipoFutbol($id, Request $req)
    {
        return EquipoFutbolDAO::updateEquipoFutbol($id, (object)$req->all());
    }

    public function getSocios(Request $req)
    {
        return EquipoFutbolDAO::getSocios((object)$req->all());
    }

    public function getJugadoresEquipo($id)
    {        
        
        return response()->json(EquipoFutbolDAO::getJugadoresEquipo($id))->setEncodingOptions(JSON_NUMERIC_CHECK);        
    }

    public function getJugadoresEquipoActivo($id)
    {
        return EquipoFutbolDAO::getJugadoresEquipoActivo($id);
    }

    public function addJugador($id, Request $req)
    {
        return EquipoFutbolDAO::addJugador($id, (object)$req->all());
    }

    public function bajaJugador($id, Request $req)
    {
        return EquipoFutbolDAO::bajaJugador($id, (object)$req->all());
    }

    //  return response($img)->header('Content-type','image/png');
    public function getViewFoto(Request $req)
    {    
    //     $foto=$req->input('foto');         
    //     $type = pathinfo($foto, PATHINFO_EXTENSION);
    //      $img=file_get_contents("../upload/$foto");
    //      $base64 = 'data:image/' . $type . ';base64,' . base64_encode($img);
    //      return response($base64);

        $cve_socio=$req->input("foto");
        $img=EquipoFutbolDAO::getFotoSocio($cve_socio);
        if($img==null){            
             $img=file_get_contents("../upload/no-foto.png");
            //  $base64 = 'data:image/' . "png" . ';base64,' . base64_encode($img);
            return response();
            //  return response($img)->header('Content-type','image/png');
        }
        else{
            $base64 = 'data:image/' . "png" . ';base64,' . base64_encode($img);
            return response($base64);
            // return response($img)->header('Content-type','image/png');
        }


        }

    public function createCredenciales()
    {              
          
    }

    public function getReporteJugadores($id)
    {
        return EquipoFutbolDAO::getReporteJugadores($id);
    }
}