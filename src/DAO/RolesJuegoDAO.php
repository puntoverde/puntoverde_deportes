<?php

namespace App\DAO;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class RolesJuegoDAO
{

   public function __construct() {}

   public static function getTorneo()
   {
     /*
      SELECT 
	      id_torneo_futbol,
	      nombre,
	      categoria,
	      limite_edad,
	      fecha_inicio,
	      fecha_fin
      FROM torneo_futbol
      WHERE estatus=1
     */

     return DB::table("torneo_futbol")
     ->where("estatus",1)
     ->select(
         "id_torneo_futbol",
	      "nombre",
	      "categoria",
	      "limite_edad",
	      "fecha_inicio",
	      "fecha_fin"
     )
     ->get();
    
   }

   public static function getEquiposByTorneo($id_torneo)
   {
      /*
         SELECT id_equipo_futbol,nombre FROM equipo_futbol WHERE id_torneo_futbol=11 AND estatus=1
      */
      return DB::table("equipo_futbol")->where("id_torneo_futbol",$id_torneo)->select("id_equipo_futbol","nombre")->get();

   }


   public static function getFixture($id_torneo)
   {


      /*
         SELECT 
	         torneo_futbol_fixture.cve_torneo_futbol_fixture,
	         torneo_futbol_fixture.jornada,
	         torneo_futbol_fixture.`local`,
	         equipo_futbol.nombre as local_nombre,
	         torneo_futbol_fixture.visita,
	         visita.nombre as visita_nombre,
	         torneo_futbol_fixture.fecha,
	         torneo_futbol_fixture.gol_local,
	         torneo_futbol_fixture.gol_visita,
	         torneo_futbol_fixture.suspende 
         FROM torneo_futbol_fixture 
         INNER JOIN equipo_futbol ON torneo_futbol_fixture.`local`=equipo_futbol.id_equipo_futbol
         INNER JOIN equipo_futbol AS visita ON torneo_futbol_fixture.visita=visita.id_equipo_futbol
         WHERE cve_torneo_futbol=1
      */

      return DB::table("torneo_futbol_fixture")
      ->join("equipo_futbol" , "torneo_futbol_fixture.local","equipo_futbol.id_equipo_futbol")
      ->join("equipo_futbol AS visita" , "torneo_futbol_fixture.visita","visita.id_equipo_futbol")
      ->where("cve_torneo_futbol",$id_torneo)
      ->select(
            "torneo_futbol_fixture.cve_torneo_futbol_fixture",
	         "torneo_futbol_fixture.jornada",
	         "torneo_futbol_fixture.local",
	         "equipo_futbol.nombre AS local_nombre",
	         "torneo_futbol_fixture.visita",
	         "visita.nombre AS visita_nombre",
	         "torneo_futbol_fixture.fecha",
	         "torneo_futbol_fixture.gol_local",
	         "torneo_futbol_fixture.gol_visita",
	         "torneo_futbol_fixture.suspende"
      )
      ->get();



   }

 

   public static function createFixture($insc)
   {  

     DB::table("torneo_futbol_fixture")->insert($insc);


   }



   public static function AsignarFechaPartido($id, $fecha)
   {
      return DB::table("torneo_futbol_fixture")->where("cve_torneo_futbol_fixture",$id)->update(["fecha"=>$fecha,"suspende"=>0]);
   }
   
   public static function SuspenderPartido($id)
   {
      return DB::table("torneo_futbol_fixture")->where("cve_torneo_futbol_fixture",$id)->update(["suspende"=>1]);
   }




}
