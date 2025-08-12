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
         ->where("estatus", 1)
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
      return DB::table("equipo_futbol")->where("id_torneo_futbol", $id_torneo)->select("id_equipo_futbol", "nombre")->get();
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
         ->join("equipo_futbol", "torneo_futbol_fixture.local", "equipo_futbol.id_equipo_futbol")
         ->join("equipo_futbol AS visita", "torneo_futbol_fixture.visita", "visita.id_equipo_futbol")
         ->where("cve_torneo_futbol", $id_torneo)
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
      return DB::table("torneo_futbol_fixture")->where("cve_torneo_futbol_fixture", $id)->update(["fecha" => $fecha, "suspende" => 0]);
   }

   public static function SuspenderPartido($id)
   {
      return DB::table("torneo_futbol_fixture")->where("cve_torneo_futbol_fixture", $id)->update(["suspende" => 1]);
   }

   public static function AgregarGol($jugador_partido)
   {
      // dd($jugador_partido);
      // en este metodo se agrega el gol falta saber si es de visita o es local y aparte quien lo anoto

      if ($jugador_partido->local_visita == "l") {
         DB::table("torneo_futbol_fixture")->where("cve_torneo_futbol_fixture", $jugador_partido->partido)->update(["gol_local" => $jugador_partido->gol]);
      } else {
         DB::table("torneo_futbol_fixture")->where("cve_torneo_futbol_fixture", $jugador_partido->partido)->update(["gol_visita" => $jugador_partido->gol]);
      }

      $goles_jugador = DB::table("torneo_futbol_fixture_estadisticas")->where("cve_torneo_futbol_fixture", $jugador_partido->partido)->where("id_equipo_futbol_jugador", $jugador_partido->jugador)->value("gol");

      return DB::table("torneo_futbol_fixture_estadisticas")->updateOrInsert(["cve_torneo_futbol_fixture" => $jugador_partido->partido, "id_equipo_futbol_jugador" => $jugador_partido->jugador], ["gol" => ($goles_jugador ?? 0) + 1]);
   }

   public static function AgergarTarjetaAmarilla($jugador_partido)
   {
      $tarejetas_amarillas = DB::table("torneo_futbol_fixture_estadisticas")->where("cve_torneo_futbol_fixture", $jugador_partido->partido)->where("id_equipo_futbol_jugador", $jugador_partido->jugador)->value("tarjeta_amarilla");
      // seleccionar al jugador
      if (($tarejetas_amarillas ?? 0) < 2) {
         return DB::table("torneo_futbol_fixture_estadisticas")->updateOrInsert(["cve_torneo_futbol_fixture" => $jugador_partido->partido, "id_equipo_futbol_jugador" => $jugador_partido->jugador], ["tarjeta_amarilla" => ($tarejetas_amarillas ?? 0) + 1]);
      }
   }

   public static function AgergarTarjetaRoja($jugador_partido)
   {
      // seleccionar al jugador 
      return DB::table("torneo_futbol_fixture_estadisticas")->updateOrInsert(["cve_torneo_futbol_fixture" => $jugador_partido->partido, "id_equipo_futbol_jugador" => $jugador_partido->jugador], ["tarejeta_roja" => 1]);
   }



   public static function getHorarioFutbol($torneo)
   {
      /*
         SELECT 
	         torneo_futbol_fixture.cve_torneo_futbol_fixture,
	         torneo_futbol_fixture.cve_torneo_futbol,
	         torneo_futbol_fixture.jornada,
	         torneo_futbol_fixture.`local` AS id_equipo_local,
	         equipo_local.nombre AS nombre_local,
	         torneo_futbol_fixture.gol_local,
	         torneo_futbol_fixture.visita AS id_equipo_visita,
	         equipo_visita.nombre AS nombre_visita,
	         torneo_futbol_fixture.gol_visita,
	         torneo_futbol_fixture.fecha
         FROM torneo_futbol_fixture
         INNER JOIN equipo_futbol AS equipo_local ON torneo_futbol_fixture.`local` =equipo_local.id_equipo_futbol
         INNER JOIN equipo_futbol AS equipo_visita ON torneo_futbol_fixture.visita=equipo_visita.id_equipo_futbol
         where torneo_futbol_fixture.cve_torneo_futbol=2
      */

      return DB::table("torneo_futbol_fixture")
         ->join("equipo_futbol AS equipo_local", "torneo_futbol_fixture.local", "equipo_local.id_equipo_futbol")
         ->join("equipo_futbol AS equipo_visita", "torneo_futbol_fixture.visita", "equipo_visita.id_equipo_futbol")
         ->where("torneo_futbol_fixture.cve_torneo_futbol",$torneo)
         ->select(
            "torneo_futbol_fixture.cve_torneo_futbol_fixture",
            "torneo_futbol_fixture.cve_torneo_futbol",
            "torneo_futbol_fixture.jornada",
            "torneo_futbol_fixture.local AS id_equipo_local",
            "equipo_local.nombre AS nombre_local",
            "torneo_futbol_fixture.gol_local",
            "torneo_futbol_fixture.visita AS id_equipo_visita",
            "equipo_visita.nombre AS nombre_visita",
            "torneo_futbol_fixture.gol_visita",
            "torneo_futbol_fixture.fecha"
         )
         ->get();
   }

   public static function getJugadorEquipoPartido($equipo, $partido)
   {
      /*
         SELECT 
            equipo_futbol_jugador.id_equipo_futbol_jugador,
            equipo_futbol_jugador.numero_jugador,
            persona.nombre,
            persona.apellido_paterno,
            persona.apellido_materno,
            torneo_futbol_fixture_estadisticas.gol,
            torneo_futbol_fixture_estadisticas.tarjeta_amarilla,
            torneo_futbol_fixture_estadisticas.tarejeta_roja 
         FROM equipo_futbol_jugador 
         INNER JOIN socios ON equipo_futbol_jugador.cve_socio=socios.cve_socio
         INNER JOIN persona ON socios.cve_persona=persona.cve_persona
         LEFT JOIN torneo_futbol_fixture_estadisticas ON equipo_futbol_jugador.id_equipo_futbol_jugador=torneo_futbol_fixture_estadisticas.id_equipo_futbol_jugador AND torneo_futbol_fixture_estadisticas.cve_torneo_futbol_fixture=1
         WHERE id_equipo_futbol=36 ORDER BY cast(equipo_futbol_jugador.numero_jugador AS UNSIGNED)
      */

      return DB::table("equipo_futbol_jugador")
         ->join("socios", "equipo_futbol_jugador.cve_socio", "socios.cve_socio")
         ->join("persona", "socios.cve_persona", "persona.cve_persona")
         ->leftJoin("torneo_futbol_fixture_estadisticas", function ($join) use ($partido) {
            $join->on("equipo_futbol_jugador.id_equipo_futbol_jugador", "torneo_futbol_fixture_estadisticas.id_equipo_futbol_jugador")->where("torneo_futbol_fixture_estadisticas.cve_torneo_futbol_fixture", $partido);
         })
         ->where("id_equipo_futbol", $equipo)
         ->orderByRaw("cast(equipo_futbol_jugador.numero_jugador AS UNSIGNED)")
         ->select(
            "equipo_futbol_jugador.id_equipo_futbol_jugador",
            "equipo_futbol_jugador.numero_jugador",
            "persona.nombre",
            "persona.apellido_paterno",
            "persona.apellido_materno",
            "torneo_futbol_fixture_estadisticas.gol",
            "torneo_futbol_fixture_estadisticas.tarjeta_amarilla",
            "torneo_futbol_fixture_estadisticas.tarejeta_roja"
         )
         ->get();
   }

   public static function getFotoJugador($id)
   {
      try {
         return DB::table("equipo_futbol_jugador")
            ->join("socios", "equipo_futbol_jugador.cve_socio", "socios.cve_socio")
            ->where("equipo_futbol_jugador.id_equipo_futbol_jugador", $id)->value("foto_socio");
      } catch (\Exception $th) {
      }
   }

   public static function getEstadisticasPartido($partido)
   {
      /*
         SELECT 
	         persona.nombre,
	         persona.apellido_paterno,
	         persona.apellido_materno,
	         torneo_futbol_fixture_estadisticas.gol,
	         torneo_futbol_fixture_estadisticas.tarjeta_amarilla,
	         torneo_futbol_fixture_estadisticas.tarejeta_roja,
	         equipo_futbol.nombre as equipo
         FROM torneo_futbol_fixture_estadisticas 
         INNER JOIN equipo_futbol_jugador ON torneo_futbol_fixture_estadisticas.id_equipo_futbol_jugador=equipo_futbol_jugador.id_equipo_futbol_jugador
         inner join equipo_futbol ON equipo_futbol_jugador.id_equipo_futbol=equipo_futbol.id_equipo_futbol
         INNER JOIN socios ON equipo_futbol_jugador.cve_socio=socios.cve_socio
         INNER JOIN persona ON socios.cve_persona=persona.cve_persona
         WHERE torneo_futbol_fixture_estadisticas.cve_torneo_futbol_fixture=1
      */

      return DB::table("torneo_futbol_fixture_estadisticas")
         ->join("equipo_futbol_jugador", "torneo_futbol_fixture_estadisticas.id_equipo_futbol_jugador", "equipo_futbol_jugador.id_equipo_futbol_jugador")
         ->join("equipo_futbol", "equipo_futbol_jugador.id_equipo_futbol", "equipo_futbol.id_equipo_futbol")
         ->join("socios", "equipo_futbol_jugador.cve_socio", "socios.cve_socio")
         ->join("persona", "socios.cve_persona", "persona.cve_persona")
         ->where("torneo_futbol_fixture_estadisticas.cve_torneo_futbol_fixture", $partido)
         ->select(
            "equipo_futbol_jugador.id_equipo_futbol_jugador",
            "persona.nombre",
            "persona.apellido_paterno",
            "persona.apellido_materno",
            "torneo_futbol_fixture_estadisticas.gol",
            "torneo_futbol_fixture_estadisticas.tarjeta_amarilla",
            "torneo_futbol_fixture_estadisticas.tarejeta_roja",
            "equipo_futbol.nombre as equipo"
         )
         ->get();
   }

   public static function tblaGeneral($torneo)
   {
      /*
         SELECT 
            id_equipo_futbol,
            nombre,SUM(partido) AS pj,
            SUM(ganado) AS g,
            SUM(empate) AS e,
            SUM(perdido) AS p,
            SUM(gol_favor) AS gf,
            SUM(gol_contra) AS gc,
            SUM(gol_favor)-SUM(gol_contra) AS dg,
            SUM(puntos) AS pts 
         FROM (SELECT 
                  torneo_futbol_fixture.cve_torneo_futbol_fixture,
	               equipo_futbol.id_equipo_futbol,
	               equipo_futbol.nombre,
	               if(torneo_futbol_fixture.gol_local=torneo_futbol_fixture.gol_visita,1,0)+if(torneo_futbol_fixture.gol_local>torneo_futbol_fixture.gol_visita,3,0) AS puntos,
	               1 AS partido,
	               torneo_futbol_fixture.gol_local AS gol_favor,
	               torneo_futbol_fixture.gol_visita AS gol_contra,
	               if(torneo_futbol_fixture.gol_local>torneo_futbol_fixture.gol_visita,1,0) AS ganado,
	               if(torneo_futbol_fixture.gol_local=torneo_futbol_fixture.gol_visita,1,0) AS empate,
	               if(torneo_futbol_fixture.gol_local<torneo_futbol_fixture.gol_visita,1,0) AS perdido
               FROM equipo_futbol 
               LEFT JOIN torneo_futbol_fixture ON equipo_futbol.id_equipo_futbol=torneo_futbol_fixture.`local`
               WHERE equipo_futbol.id_torneo_futbol=2 AND torneo_futbol_fixture.cve_torneo_futbol_fixture IS NOT NULL AND  torneo_futbol_fixture.fecha<NOW() AND torneo_futbol_fixture.suspende=0

               UNION 

               SELECT 
                  torneo_futbol_fixture.cve_torneo_futbol_fixture,
               	equipo_futbol.id_equipo_futbol,
               	equipo_futbol.nombre,
               	if(torneo_futbol_fixture.gol_visita=torneo_futbol_fixture.gol_local,1,0)+if(torneo_futbol_fixture.gol_visita>torneo_futbol_fixture.gol_local,3,0) AS puntos,
               	1 AS partido,
               	torneo_futbol_fixture.gol_visita AS gol_favor,
               	torneo_futbol_fixture.gol_local AS gol_contra,
               	if(torneo_futbol_fixture.gol_visita>torneo_futbol_fixture.gol_local,1,0) AS ganado,
               	if(torneo_futbol_fixture.gol_visita=torneo_futbol_fixture.gol_local,1,0) AS empate,
               	if(torneo_futbol_fixture.gol_visita<torneo_futbol_fixture.gol_local,1,0) AS perdido
               FROM equipo_futbol 
               LEFT JOIN torneo_futbol_fixture ON equipo_futbol.id_equipo_futbol=torneo_futbol_fixture.visita
               WHERE equipo_futbol.id_torneo_futbol=2 AND torneo_futbol_fixture.cve_torneo_futbol_fixture IS NOT NULL AND  torneo_futbol_fixture.fecha<NOW() AND torneo_futbol_fixture.suspende=0
         ) AS tbl_1
         GROUP BY tbl_1.id_equipo_futbol ORDER BY SUM(tbl_1.puntos) desc,SUM(tbl_1.gol_favor) desc
      */


      $data = DB::query()->fromSub(function ($sub) use($torneo){
         $sub->from("equipo_futbol")
            ->leftJoin("torneo_futbol_fixture", "equipo_futbol.id_equipo_futbol", "torneo_futbol_fixture.local")
            ->where("equipo_futbol.id_torneo_futbol", $torneo)
            ->whereNotNull("torneo_futbol_fixture.cve_torneo_futbol_fixture")
            ->whereRaw("torneo_futbol_fixture.fecha<NOW()")
            ->where("torneo_futbol_fixture.suspende",0)
            ->select(
               "torneo_futbol_fixture.cve_torneo_futbol_fixture",
               "equipo_futbol.id_equipo_futbol",
               "equipo_futbol.nombre",
               "torneo_futbol_fixture.gol_local AS gol_favor",
               "torneo_futbol_fixture.gol_visita AS gol_contra",
               DB::raw("1 AS partido")
            )
            ->selectRaw("IF(torneo_futbol_fixture.gol_local=torneo_futbol_fixture.gol_visita,1,0) + IF(torneo_futbol_fixture.gol_local>torneo_futbol_fixture.gol_visita,3,0) AS puntos")
            ->selectRaw("IF(torneo_futbol_fixture.gol_local>torneo_futbol_fixture.gol_visita,1,0) AS ganado")
            ->selectRaw("IF(torneo_futbol_fixture.gol_local=torneo_futbol_fixture.gol_visita,1,0) AS empate")
            ->selectRaw("IF(torneo_futbol_fixture.gol_local<torneo_futbol_fixture.gol_visita,1,0) AS perdido")
            ->union(
               DB::table("equipo_futbol")
                  ->leftJoin("torneo_futbol_fixture", "equipo_futbol.id_equipo_futbol", "torneo_futbol_fixture.visita")
                  ->where("equipo_futbol.id_torneo_futbol", $torneo)
                  ->whereNotNull("torneo_futbol_fixture.cve_torneo_futbol_fixture")
                  ->whereRaw("torneo_futbol_fixture.fecha<NOW()")
                  ->where("torneo_futbol_fixture.suspende",0)
                  ->select(
                     "torneo_futbol_fixture.cve_torneo_futbol_fixture",
                     "equipo_futbol.id_equipo_futbol",
                     "equipo_futbol.nombre",
                     "torneo_futbol_fixture.gol_visita AS gol_favor",
                     "torneo_futbol_fixture.gol_local AS gol_contra",
                     DB::raw("1 AS partido")
                  )
                  ->selectRaw("IF(torneo_futbol_fixture.gol_visita=torneo_futbol_fixture.gol_local,1,0) + IF(torneo_futbol_fixture.gol_visita>torneo_futbol_fixture.gol_local,3,0) AS puntos")
                  ->selectRaw("IF(torneo_futbol_fixture.gol_visita>torneo_futbol_fixture.gol_local,1,0) AS ganado")
                  ->selectRaw("IF(torneo_futbol_fixture.gol_visita=torneo_futbol_fixture.gol_local,1,0) AS empate")
                  ->selectRaw("IF(torneo_futbol_fixture.gol_visita<torneo_futbol_fixture.gol_local,1,0) AS perdido")
            );
      }, "tbl_1")
      ->groupBy("tbl_1.id_equipo_futbol")
      ->orderByRaw("SUM(tbl_1.puntos) desc")
      ->orderByRaw("SUM(tbl_1.gol_favor) desc")
      ->select(
            "id_equipo_futbol",
            "nombre",
            DB::raw("SUM(partido) AS pj"),
            DB::raw("SUM(ganado) AS g"),
            DB::raw("SUM(empate) AS e"),
            DB::raw("SUM(perdido) AS p"),
            DB::raw("SUM(gol_favor) AS gf"),
            DB::raw("SUM(gol_contra) AS gc"),
            DB::raw("SUM(gol_favor)-SUM(gol_contra) AS dg"),
            DB::raw("SUM(puntos) AS pts")
      )
      ->get();

      return $data;
   }

   public static function getEstadisticasGoles($torneo)
   {
      /*
         SELECT 
	         persona.nombre,
	         persona.apellido_paterno,
	         persona.apellido_materno,
	         equipo_futbol.nombre AS equipo,
	         SUM(torneo_futbol_fixture_estadisticas.gol) AS goles
         FROM torneo_futbol_fixture
         INNER JOIN torneo_futbol_fixture_estadisticas ON torneo_futbol_fixture.cve_torneo_futbol_fixture=torneo_futbol_fixture_estadisticas.cve_torneo_futbol_fixture AND torneo_futbol_fixture_estadisticas.gol IS NOT NULL
         INNER JOIN equipo_futbol_jugador ON torneo_futbol_fixture_estadisticas.id_equipo_futbol_jugador=equipo_futbol_jugador.id_equipo_futbol_jugador
         INNER JOIN equipo_futbol ON equipo_futbol_jugador.id_equipo_futbol=equipo_futbol.id_equipo_futbol
         INNER JOIN socios ON equipo_futbol_jugador.cve_socio=socios.cve_socio
         INNER JOIN persona ON socios.cve_persona=persona.cve_persona
         WHERE torneo_futbol_fixture.cve_torneo_futbol=2 AND torneo_futbol_fixture.suspende=0
         GROUP BY  torneo_futbol_fixture_estadisticas.id_equipo_futbol_jugador
         ORDER BY SUM(torneo_futbol_fixture_estadisticas.gol) desc
      */

         return DB::table("torneo_futbol_fixture")
         ->join("torneo_futbol_fixture_estadisticas" ,function($join){$join->on("torneo_futbol_fixture.cve_torneo_futbol_fixture","torneo_futbol_fixture_estadisticas.cve_torneo_futbol_fixture")->whereNotNull("torneo_futbol_fixture_estadisticas.gol");})
         ->join("equipo_futbol_jugador" , "torneo_futbol_fixture_estadisticas.id_equipo_futbol_jugador","equipo_futbol_jugador.id_equipo_futbol_jugador")
         ->join("equipo_futbol" , "equipo_futbol_jugador.id_equipo_futbol","equipo_futbol.id_equipo_futbol")
         ->join("socios" , "equipo_futbol_jugador.cve_socio","socios.cve_socio")
         ->join("persona" , "socios.cve_persona","persona.cve_persona")
         ->where("torneo_futbol_fixture.cve_torneo_futbol",$torneo)
         ->where("torneo_futbol_fixture.suspende",0)
         ->groupBy("torneo_futbol_fixture_estadisticas.id_equipo_futbol_jugador")
         ->orderByRaw("SUM(torneo_futbol_fixture_estadisticas.gol) desc")
         ->select(
            "equipo_futbol_jugador.id_equipo_futbol_jugador",
            "persona.nombre",
	         "persona.apellido_paterno",
	         "persona.apellido_materno",
	         "equipo_futbol.nombre AS equipo"	         
         )
         ->selectRaw("SUM(torneo_futbol_fixture_estadisticas.gol) AS goles")
         ->get();
   }


   public static function getEstadisticasAmarillas($torneo)
   {
      /*
         SELECT 
	         persona.nombre,
	         persona.apellido_paterno,
	         persona.apellido_materno,
	         equipo_futbol.nombre AS equipo,
	         SUM(torneo_futbol_fixture_estadisticas.tarjeta_amarilla) AS amarillas
         FROM torneo_futbol_fixture
         INNER JOIN torneo_futbol_fixture_estadisticas ON torneo_futbol_fixture.cve_torneo_futbol_fixture=torneo_futbol_fixture_estadisticas.cve_torneo_futbol_fixture AND torneo_futbol_fixture_estadisticas.tarjeta_amarilla IS NOT NULL
         INNER JOIN equipo_futbol_jugador ON torneo_futbol_fixture_estadisticas.id_equipo_futbol_jugador=equipo_futbol_jugador.id_equipo_futbol_jugador
         INNER JOIN equipo_futbol ON equipo_futbol_jugador.id_equipo_futbol=equipo_futbol.id_equipo_futbol
         INNER JOIN socios ON equipo_futbol_jugador.cve_socio=socios.cve_socio
         INNER JOIN persona ON socios.cve_persona=persona.cve_persona
         WHERE torneo_futbol_fixture.cve_torneo_futbol=2 AND torneo_futbol_fixture.suspende=0
         GROUP BY  torneo_futbol_fixture_estadisticas.id_equipo_futbol_jugador
         ORDER BY SUM(torneo_futbol_fixture_estadisticas.gol) desc
      */

         return DB::table("torneo_futbol_fixture")
         ->join("torneo_futbol_fixture_estadisticas" ,function($join){$join->on("torneo_futbol_fixture.cve_torneo_futbol_fixture","torneo_futbol_fixture_estadisticas.cve_torneo_futbol_fixture")->whereNotNull("torneo_futbol_fixture_estadisticas.tarjeta_amarilla");})
         ->join("equipo_futbol_jugador" , "torneo_futbol_fixture_estadisticas.id_equipo_futbol_jugador","equipo_futbol_jugador.id_equipo_futbol_jugador")
         ->join("equipo_futbol" , "equipo_futbol_jugador.id_equipo_futbol","equipo_futbol.id_equipo_futbol")
         ->join("socios" , "equipo_futbol_jugador.cve_socio","socios.cve_socio")
         ->join("persona" , "socios.cve_persona","persona.cve_persona")
         ->where("torneo_futbol_fixture.cve_torneo_futbol",$torneo)
         ->where("torneo_futbol_fixture.suspende",0)
         ->groupBy("torneo_futbol_fixture_estadisticas.id_equipo_futbol_jugador")
         ->orderByRaw("SUM(torneo_futbol_fixture_estadisticas.gol) desc")
         ->select(
            "equipo_futbol_jugador.id_equipo_futbol_jugador",
            "persona.nombre",
	         "persona.apellido_paterno",
	         "persona.apellido_materno",
	         "equipo_futbol.nombre AS equipo"	         
         )
         ->selectRaw("SUM(torneo_futbol_fixture_estadisticas.tarjeta_amarilla) AS amarillas")
         ->get();
   }
  
   public static function getEstadisticasRojas($torneo)
   {
      /*
         SELECT 
	         persona.nombre,
	         persona.apellido_paterno,
	         persona.apellido_materno,
	         equipo_futbol.nombre AS equipo,
	         SUM(torneo_futbol_fixture_estadisticas.tarejeta_roja) AS rojas
         FROM torneo_futbol_fixture
         INNER JOIN torneo_futbol_fixture_estadisticas ON torneo_futbol_fixture.cve_torneo_futbol_fixture=torneo_futbol_fixture_estadisticas.cve_torneo_futbol_fixture AND torneo_futbol_fixture_estadisticas.tarejeta_roja IS NOT NULL
         INNER JOIN equipo_futbol_jugador ON torneo_futbol_fixture_estadisticas.id_equipo_futbol_jugador=equipo_futbol_jugador.id_equipo_futbol_jugador
         INNER JOIN equipo_futbol ON equipo_futbol_jugador.id_equipo_futbol=equipo_futbol.id_equipo_futbol
         INNER JOIN socios ON equipo_futbol_jugador.cve_socio=socios.cve_socio
         INNER JOIN persona ON socios.cve_persona=persona.cve_persona
         WHERE torneo_futbol_fixture.cve_torneo_futbol=2 AND torneo_futbol_fixture.suspende=0
         GROUP BY  torneo_futbol_fixture_estadisticas.id_equipo_futbol_jugador
         ORDER BY SUM(torneo_futbol_fixture_estadisticas.gol) desc
      */

         return DB::table("torneo_futbol_fixture")
         ->join("torneo_futbol_fixture_estadisticas" ,function($join){$join->on("torneo_futbol_fixture.cve_torneo_futbol_fixture","torneo_futbol_fixture_estadisticas.cve_torneo_futbol_fixture")->whereNotNull("torneo_futbol_fixture_estadisticas.tarejeta_roja");})
         ->join("equipo_futbol_jugador" , "torneo_futbol_fixture_estadisticas.id_equipo_futbol_jugador","equipo_futbol_jugador.id_equipo_futbol_jugador")
         ->join("equipo_futbol" , "equipo_futbol_jugador.id_equipo_futbol","equipo_futbol.id_equipo_futbol")
         ->join("socios" , "equipo_futbol_jugador.cve_socio","socios.cve_socio")
         ->join("persona" , "socios.cve_persona","persona.cve_persona")
         ->where("torneo_futbol_fixture.cve_torneo_futbol",$torneo)
         ->where("torneo_futbol_fixture.suspende",0)
         ->groupBy("torneo_futbol_fixture_estadisticas.id_equipo_futbol_jugador")
         ->orderByRaw("SUM(torneo_futbol_fixture_estadisticas.gol) desc")
         ->select(
            "equipo_futbol_jugador.id_equipo_futbol_jugador",
            "persona.nombre",
	         "persona.apellido_paterno",
	         "persona.apellido_materno",
	         "equipo_futbol.nombre AS equipo"	         
         )
         ->selectRaw("SUM(torneo_futbol_fixture_estadisticas.tarejeta_roja) AS rojas")
         ->get();
   }


}
