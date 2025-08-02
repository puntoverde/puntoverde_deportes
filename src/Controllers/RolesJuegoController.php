<?php

namespace App\Controllers;

use App\DAO\RolesJuegoDAO;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class RolesJuegoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }


    public function getTorneo(Request $req)
    {
        return RolesJuegoDAO::getTorneo();
    }
    
    public function getEquiposByTorneo(Request $req)
    {
        $reglas = ["id_torneo_futbol"=>"required"];        
        $this->validate($req, $reglas);
        return RolesJuegoDAO::getEquiposByTorneo($req->input("id_torneo_futbol"));
    }
    

    public function getFixture(Request $req)
    {
        $reglas = ["torneo"=>"required"];        
        $this->validate($req, $reglas);
        $id_torneo=$req->input("torneo");
        return RolesJuegoDAO::getFixture($id_torneo);
    }
    
    
    public function createFixture(Request $req)
    {
        $reglas = [
         "*.cve_torneo_futbol"=>"required",
         "*.jornada"=>"required",
         "*.local"=>"required",
         "*.visita"=>"required",
        ];        

        // dd($req->all());

        $this->validate($req, $reglas);
        return RolesJuegoDAO::createFixture($req->all());
    }

    public function AsignarFechaPartido(Request $req)
    {
        $reglas = [
         "partido"=>"required",                 
         "fecha"=>"required",
        ];        

        $this->validate($req, $reglas);

        $partido=$req->input("partido");
        $fecha=$req->input("fecha");
        return RolesJuegoDAO::AsignarFechaPartido($partido,$fecha);
    }
    
    public function SuspenderPartido(Request $req)
    {
        $reglas = ["partido"=>"required"];        

        $this->validate($req, $reglas);

        $partido=$req->input("partido");
        return RolesJuegoDAO::SuspenderPartido($partido);
    }
   
}