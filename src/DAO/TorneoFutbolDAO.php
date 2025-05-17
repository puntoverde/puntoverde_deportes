<?php

namespace App\DAO;

use App\Entity\Accion;
use App\Entity\EquipoFutbol;
use App\Entity\Socios;
use App\Entity\TorneoFutbol;
use Illuminate\Support\Facades\DB;


class TorneoFutbolDAO
{

   public function __construct()
   {
   }

   public static function getTorneos()
   {
      //busca las accions de tipo futbol(6)
      return TorneoFutbol::get();
   }

   public static function getEquipos($id)
   {
      //busca los equipos de futbol por accion
      return EquipoFutbol::where("cve_accion", $id)->get();
   }

   public static function createTorneoFutbol($p)
   {
      // $accion = Accion::find($id);
      $equipo_futbol = new TorneoFutbol();
      // $equipo_futbol->accion()->associate($accion);
      $equipo_futbol->nombre = $p->nombre;
      $equipo_futbol->categoria = $p->categoria;
      $equipo_futbol->limite_edad = $p->limite_edad;
      $equipo_futbol->fecha_inicio = $p->fecha_inicio;
      $equipo_futbol->fecha_fin = $p->fecha_fin;
      $equipo_futbol->estatus = 1;
      $equipo_futbol->save();
      return $equipo_futbol->id_torneo_futbol;
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
         ->leftJoin("equipo_futbol_jugador",function($join){$join->on("socios.cve_socio","equipo_futbol_jugador.cve_socio")->where("equipo_futbol_jugador.estatus",1);})
         ->where("socios.estatus", 1)
         ->where("persona.estatus", 1)
         ->whereIn("acciones.estatus", [1, 2])
         ->whereNull("equipo_futbol_jugador.id_equipo_futbol_jugador")
         ->select("acciones.cve_accion", "socios.cve_socio", "persona.cve_persona")
         ->selectRaw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS nombre")
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
         ->join("persona", "socios.cve_persona", "persona.cve_persona")
         ->leftJoin("acciones","socios.cve_accion","acciones.cve_accion")
         ->where("equipo_futbol_jugador.cve_accion", $id)
         ->select("id_equipo_futbol_jugador", "socios.cve_socio","equipo_futbol_jugador.estatus")
         ->addSelect("persona.nombre AS nombre_player", "persona.apellido_paterno AS paterno","persona.apellido_materno AS materno")
         ->addSelect("socios.posicion","socios.foto")
         ->selectRaw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS nombre")
         //   ->selectRaw("CONCAT(numero_accion,CASE clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion")
         ->selectRAW("TIMESTAMPDIFF(YEAR,persona.fecha_nacimiento,CURDATE()) AS edad")
         ->selectRAW("CONCAT(numero_accion,CASE clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion")
         ->get();
   }

   public static function getJugadoresEquipoActivo($id)
   {
      return DB::table("equipo_futbol_jugador")
         ->join("socios", "equipo_futbol_jugador.cve_socio", "socios.cve_socio")
         ->join("persona", "socios.cve_persona", "persona.cve_persona")
         ->leftJoin("acciones","socios.cve_accion","acciones.cve_accion")
         ->where("equipo_futbol_jugador.cve_accion", $id)
         ->where("equipo_futbol_jugador.estatus",1)
         ->select("id_equipo_futbol_jugador", "socios.cve_socio","equipo_futbol_jugador.estatus")
         ->addSelect("persona.nombre AS nombre_player", "persona.apellido_paterno AS paterno","persona.apellido_materno AS materno")
         ->addSelect("socios.posicion","socios.foto","equipo_futbol_jugador.numero_jugador")         
         ->selectRAW("TIMESTAMPDIFF(YEAR,persona.fecha_nacimiento,CURDATE()) AS edad")
         ->selectRAW("CONCAT(numero_accion,CASE clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion")
         ->get();
   }

   public static function addJugador($id, $p)
   {
      $accion = Accion::find($id);
      $ok = $accion->jugadores()
      ->attach($p->cve_socio,['estatus'=>1,"numero_jugador"=>$p->numero_jugador]);
      return ["ok" => $ok];
   }

   public static function bajaJugador($id, $p)
   {
      $accion = Accion::find($id);
      $ok = $accion->jugadores()->updateExistingPivot($p->cve_socio,['estatus'=>$p->estatus]);
      // $ok = $accion->jugadores()->detach($p->cve_socio);
      return ["ok" => $ok];
   }
}
