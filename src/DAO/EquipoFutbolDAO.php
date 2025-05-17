<?php

namespace App\DAO;

use App\Entity\Accion;
use App\Entity\EquipoFutbol;
use App\Entity\Socios;
use App\Entity\TorneoFutbol;
use Illuminate\Support\Facades\DB;


class EquipoFutbolDAO
{

   public function __construct()
   {
   }

   public static function getAcciones()
   {
      //busca las accions de tipo futbol(6)
      // return Accion::join("dueno", "acciones.cve_dueno", "dueno.cve_dueno")
      //    ->join("persona", "dueno.cve_persona", "persona.cve_persona")
      //    ->leftJoin("equipo_futbol_jugador",function($join){$join->on("equipo_futbol_jugador.cve_accion", "acciones.cve_accion")->where("equipo_futbol_jugador.estatus",1);})
      //    ->select("acciones.cve_accion", "dueno.cve_dueno", "persona.cve_persona")
      //    ->selectRaw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS nombre")
      //    ->selectRaw("COUNT(equipo_futbol_jugador.cve_socio) AS jugadores")
      //    ->selectRaw("CONCAT(acciones.numero_accion,CASE clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion")
      //    ->where("cve_tipo_accion", 6)
      //    ->groupBy("acciones.cve_accion")
      //    ->get();
      return Accion::join("dueno", "acciones.cve_dueno", "dueno.cve_dueno")
      ->join("persona", "dueno.cve_persona", "persona.cve_persona")
      ->select("acciones.cve_accion", "dueno.cve_dueno", "persona.cve_persona")
      ->selectRaw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS nombre")
      ->selectRaw("CONCAT(acciones.numero_accion,CASE clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion")
      ->where("cve_tipo_accion", 6)
      ->where("acciones.estatus", 1)
      ->groupBy("acciones.cve_accion")
      ->get();
   }

   public static function getEquipos($p)
   {
      //busca los equipos de futbol por accion
      $query=EquipoFutbol::join("torneo_futbol","equipo_futbol.id_torneo_futbol","torneo_futbol.id_torneo_futbol")
      ->join("acciones","equipo_futbol.cve_accion","acciones.cve_accion")
      ->leftJoin("equipo_futbol_jugador",function($join){$join->on("equipo_futbol_jugador.id_equipo_futbol", "equipo_futbol.id_equipo_futbol")->where("equipo_futbol_jugador.estatus",1);})
      ->select("equipo_futbol.id_equipo_futbol")
      ->addSelect("torneo_futbol.nombre AS torneo","torneo_futbol.categoria","equipo_futbol.nombre AS equipo")
      ->selectRaw("CONCAT(numero_accion,CASE clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion")
      ->selectRaw("COUNT(equipo_futbol_jugador.id_equipo_futbol_jugador) AS n_jugadores")
      ->groupBy("equipo_futbol.id_equipo_futbol");
      if(($p->id_torneo??false) || ($p->id_accion??false))
      {
        if($p->id_torneo??false)
        $query->where("torneo_futbol.id_torneo_futbol",$p->id_torneo);
        if($p->id_accion??false)
        $query->where("acciones.cve_accion", $p->id_accion);
        return $query->get();
      }
      return [];
   }

   public static function createEquipoFutbol($id, $p)
   {
      $torneo = TorneoFutbol::find($id);
      $accion = Accion::find($p->id_accion);
      $equipo_futbol = new EquipoFutbol();
      $equipo_futbol->torneo()->associate($torneo);
      $equipo_futbol->accion()->associate($accion);
      $equipo_futbol->nombre = $p->nombre;
      $equipo_futbol->estatus = 1;
      $equipo_futbol->save();
      return $equipo_futbol->id_equipo_futbol;
   }

   public static function updateEquipoFutbol($id, $p)
   {
      $equipo_futbol = EquipoFutbol::find($id);
      $equipo_futbol->nombre = $p->nombre;
      $equipo_futbol->estatus = $p->estatus;
      $ok = $equipo_futbol->save();
      return $ok;
   }

   public static function getSocios($p)
   {

      $query = Socios::join("persona", "socios.cve_persona", "persona.cve_persona")
         ->join("acciones", "socios.cve_accion", "acciones.cve_accion")
         // ->leftJoin("equipo_futbol_jugador",function($join)
         // {$join->on("socios.cve_socio","equipo_futbol_jugador.cve_socio")
         //    ->where("equipo_futbol_jugador.estatus",1);})
         // ->leftJoin("equipo_futbol",function($join)use($p){
         //    $join->on("equipo_futbol_jugador.id_equipo_futbol","equipo_futbol.id_equipo_futbol")
         //    ->where("equipo_futbol.id_torneo_futbol",$p->id_torneo);})
         ->where("socios.estatus", 1)
         ->where("persona.estatus", 1)
         ->whereIn("acciones.estatus", [1, 2])
         // ->whereNull("equipo_futbol_jugador.id_equipo_futbol_jugador")
         // ->whereNull("equipo_futbol.id_equipo_futbol")
         ->select("acciones.cve_accion", "socios.cve_socio", "persona.cve_persona","persona.nombre","persona.apellido_paterno","persona.apellido_materno")
         //->selectRaw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS nombre")
         ->selectRaw("CONCAT(numero_accion,CASE clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion")
         ->selectRAW("TIMESTAMPDIFF(YEAR,persona.fecha_nacimiento,CURDATE()) AS edad");

      if (($p->numero_accion ?? false) == true && isset($p->clasificacion)) {
         $query->where("numero_accion", $p->numero_accion)
            ->where("clasificacion", $p->clasificacion);
      }

      if ($p->nombre ?? false) {
         $query->whereRaw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) LIKE ?", ["%" . $p->nombre . "%"]);
      }

      return $query->get();
   }

   public static function getJugadoresEquipo($id)
   {
      return DB::table("equipo_futbol_jugador")
         ->join("socios", "equipo_futbol_jugador.cve_socio", "socios.cve_socio")
         ->join("acciones","socios.cve_accion","acciones.cve_accion")
         ->join("persona", "socios.cve_persona", "persona.cve_persona")
         ->leftJoin("equipo_futbol","equipo_futbol_jugador.id_equipo_futbol","equipo_futbol.id_equipo_futbol")
         ->where("equipo_futbol_jugador.id_equipo_futbol", $id)
         ->select("id_equipo_futbol_jugador", "socios.cve_socio","equipo_futbol_jugador.estatus")
         ->addSelect("persona.nombre AS nombre_player", "persona.apellido_paterno AS paterno","persona.apellido_materno AS materno")
         ->addSelect("socios.posicion","socios.foto")
         ->selectRaw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS nombre")
         ->selectRaw("CONCAT(numero_accion,CASE clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion")
         ->selectRAW("TIMESTAMPDIFF(YEAR,persona.fecha_nacimiento,CURDATE()) AS edad")
         // ->selectRAW("CONCAT(numero_accion,CASE clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion")      
         ->get();
   }

   public static function getJugadoresEquipoActivo($id)
   {
      return DB::table("equipo_futbol_jugador")
         ->join("socios", "equipo_futbol_jugador.cve_socio", "socios.cve_socio")
         ->join("persona", "socios.cve_persona", "persona.cve_persona")
         ->leftJoin("equipo_futbol","equipo_futbol_jugador.id_equipo_futbol","equipo_futbol.id_equipo_futbol")
         ->where("equipo_futbol_jugador.id_equipo_futbol", $id)
         ->where("equipo_futbol_jugador.estatus",1)
         ->select("id_equipo_futbol_jugador", "socios.cve_socio","equipo_futbol_jugador.estatus")
         ->addSelect("persona.nombre AS nombre_player", "persona.apellido_paterno AS paterno","persona.apellido_materno AS materno")
         ->addSelect("socios.posicion","socios.foto")
         ->selectRaw("CASE equipo_futbol_jugador.numero_jugador WHEN '1000' THEN 'D.T.' WHEN '1001' THEN 'AUX.' ELSE equipo_futbol_jugador.numero_jugador END AS numero_jugador")       
         ->selectRAW("TIMESTAMPDIFF(YEAR,persona.fecha_nacimiento,CURDATE()) AS edad")
         ->get();
   }

   public static function addJugador($id, $p)
   {
            
      $exist=EquipoFutbol::join("equipo_futbol_jugador","equipo_futbol_jugador.id_equipo_futbol","equipo_futbol.id_equipo_futbol")
      ->where("cve_socio",$p->cve_socio)->where("equipo_futbol.id_torneo_futbol",$p->id_torneo)->where("equipo_futbol_jugador.estatus",1)
      ->count();
       if($exist==0)
       {
      $accion = EquipoFutbol::find($id);
      $ok = $accion->jugadores()->attach($p->cve_socio,['estatus'=>1,"numero_jugador"=>$p->numero_jugador]);
      return ["ok" => $ok];
   }
   else return ["ok" => 0];
   }

   public static function bajaJugador($id, $p)
   {
      $accion = EquipoFutbol::find($id);
      $ok = $accion->jugadores()->updateExistingPivot($p->cve_socio,['estatus'=>$p->estatus]); 
      return ["ok" => $ok];
   }


   public static function getReporteJugadores($id)
   {
      /*
         SELECT 
		         CONCAT(acciones.numero_accion,case acciones.clasificacion when 1 then 'A' when 2 then 'B' when 3 then 'C' ELSE '' END) AS accion_name,
		         persona.nombre,
		         persona.apellido_paterno,
		         persona.apellido_materno,
		         socios.posicion,
		         equipo_futbol.nombre AS equipo_name,
		         socios.fecha_alta,
		         acciones.estatus AS estatus_accion 
         FROM equipo_futbol
         INNER JOIN equipo_futbol_jugador ON equipo_futbol.id_equipo_futbol=equipo_futbol_jugador.id_equipo_futbol
         INNER JOIN socios ON equipo_futbol_jugador.cve_socio=socios.cve_socio
         INNER JOIN persona ON socios.cve_persona=persona.cve_persona
         INNER JOIN acciones ON socios.cve_accion=acciones.cve_accion
         WHERE equipo_futbol.id_torneo_futbol=1
         ORDER BY equipo_futbol.id_equipo_futbol
      */

      return DB::table("equipo_futbol")
      ->join("equipo_futbol_jugador" , "equipo_futbol.id_equipo_futbol","equipo_futbol_jugador.id_equipo_futbol")
      ->join("socios" , "equipo_futbol_jugador.cve_socio","socios.cve_socio")
      ->join("persona" , "socios.cve_persona","persona.cve_persona")
      ->join("acciones" , "socios.cve_accion","acciones.cve_accion")
      ->leftJoin("socio_accion",function($join){$join->on("socios.cve_socio","socio_accion.cve_socio")->whereIn("socio_accion.movimiento",['Alta','ReIngreso']);})
      ->where("equipo_futbol.id_torneo_futbol",$id)
      ->orderBy("equipo_futbol.id_equipo_futbol")
      ->select(
            "persona.nombre",
            "persona.apellido_paterno",
            "persona.apellido_materno",
            "socios.posicion",
            "equipo_futbol.nombre AS equipo_name",
            // "socios.fecha_alta",
            "acciones.estatus AS estatus_accion")
      ->selectRaw("MAX(IFNULL(CONVERT(fecha_hora_movimiento,DATE),socios.fecha_alta)) AS fecha_alta")
      ->selectRaw("CONCAT(acciones.numero_accion,case acciones.clasificacion when 1 then 'A' when 2 then 'B' when 3 then 'C' ELSE '' END) AS accion_name")
      ->groupBy("equipo_futbol_jugador.cve_socio")
      ->get();
   }



   public static function getFotoSocio($id)
   {
      return DB::table("socios")->where("socios.cve_socio", $id)->value("foto_socio");
   }


}


