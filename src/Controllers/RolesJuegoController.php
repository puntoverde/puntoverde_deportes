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
    
    public function AgregarGol(Request $req)
    {
        $reglas = ["partido"=>"required","jugador"=>"required","gol"=>"required","local_visita"=>"required"];        
        $this->validate($req, $reglas);
        $data=(object)$req->all();
        return RolesJuegoDAO::AgregarGol($data);
    }

    public function AgergarTarjetaAmarilla(Request $req)
    {
        $reglas = ["partido"=>"required","jugador"=>"required"];        
        $this->validate($req, $reglas);
        $data=(object)$req->all();
        return RolesJuegoDAO::AgergarTarjetaAmarilla($data);
    }

    public function AgergarTarjetaRoja(Request $req)
    {
        $reglas = ["partido"=>"required","jugador"=>"required"];        
        $this->validate($req, $reglas);
        $data=(object)$req->all();
        return RolesJuegoDAO::AgergarTarjetaRoja($data);
    }

    public function getHorarioFutbol(Request $req)
    {
      $reglas = ["torneo"=>"required"];        
        $this->validate($req, $reglas);
        $torneo=$req->input("torneo");
        return RolesJuegoDAO::getHorarioFutbol($torneo);  
    }
    
    public function getJugadorEquipoPartido(Request $req)
    {
      $reglas = ["partido"=>"required","equipo"=>"required"];
        $this->validate($req, $reglas);
        $equipo=$req->input("equipo");
        $partido=$req->input("partido");
        return RolesJuegoDAO::getJugadorEquipoPartido($equipo,$partido);  
    }

    //  return response($img)->header('Content-type','image/png');
    public function getViewFotoJugador(Request $req)
    {    
        $jugador=$req->input("cve_jugador");
        $img=RolesJuegoDAO::getFotoJugador($jugador);
        if($img==null){            
            $img=file_get_contents("../upload/no-foto.png");          
            return response($img)->header('Content-type','image/png');            
        }
        else{        
            return response($img)->header('Content-type','image/png');
        }
    }

    public function getEstadisticasPartido(Request $req)
    {
        $reglas = ["partido"=>"required"];        
        $this->validate($req, $reglas);
        $data=$req->input("partido");
        return RolesJuegoDAO::getEstadisticasPartido($data);
    }

    public function tblaGeneral(Request $req)
    {
        $reglas = ["torneo"=>"required"];        
        $this->validate($req, $reglas);
        $torneo=$req->input("torneo");
        return RolesJuegoDAO::tblaGeneral($torneo);
    }
    
    public function getEstadisticasGoles(Request $req)
    {
        $reglas = ["torneo"=>"required"];        
        $this->validate($req, $reglas);
        $torneo=$req->input("torneo");
        return RolesJuegoDAO::getEstadisticasGoles($torneo);
    }
    
    public function getEstadisticasAmarillas(Request $req)
    {
        $reglas = ["torneo"=>"required"];        
        $this->validate($req, $reglas);
        $torneo=$req->input("torneo");
        return RolesJuegoDAO::getEstadisticasAmarillas($torneo);
    }
    
    public function getEstadisticasRojas(Request $req)
    {
        $reglas = ["torneo"=>"required"];        
        $this->validate($req, $reglas);
        $torneo=$req->input("torneo");
        return RolesJuegoDAO::getEstadisticasRojas($torneo);
    }
   
}