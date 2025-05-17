<?php

namespace App\Controllers;

use App\DAO\TorneoFutbolDAO;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class TorneoFutbolController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function getTorneos(Request $req)
    {
        return TorneoFutbolDAO::getTorneos();
    }

    public function getEquipos($id)
    {
        return TorneoFutbolDAO::getEquipos($id);
    }

    public function createTorneoFutbol(Request $req)
    {
        return TorneoFutbolDAO::createTorneoFutbol((object)$req->all());
    }

    public function updateEquipoFutbol($id, Request $req)
    {
        return TorneoFutbolDAO::updateEquipoFutbol($id, (object)$req->all());
    }

    public function getSocios(Request $req)
    {
        return TorneoFutbolDAO::getSocios((object)$req->all());
    }

    public function getJugadoresEquipo($id)
    {
        return TorneoFutbolDAO::getJugadoresEquipo($id);
    }

    public function getJugadoresEquipoActivo($id)
    {
        return TorneoFutbolDAO::getJugadoresEquipoActivo($id);
    }

    public function addJugador($id, Request $req)
    {
        return TorneoFutbolDAO::addJugador($id, (object)$req->all());
    }

    public function bajaJugador($id, Request $req)
    {
        return TorneoFutbolDAO::bajaJugador($id, (object)$req->all());
    }

    //  return response($img)->header('Content-type','image/png');
    public function getViewFoto(Request $req)
    {    
        
        $foto=$req->input('foto');         
        $type = pathinfo($foto, PATHINFO_EXTENSION);
         $img=file_get_contents("../upload/$foto");
         $base64 = 'data:image/' . $type . ';base64,' . base64_encode($img);
         return response($base64);

        //  $foto=$req->input('fotox');
        //  $img=file_get_contents("../upload/$foto");
        //  return response($img)->header('Content-type','image/png');

        }

    public function createCredenciales()
    {              
          
    }
}