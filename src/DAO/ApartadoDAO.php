<?php

namespace App\DAO;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class ApartadoDAO
{

   public function __construct()
   {
   }

   public static function getApartadosVivo()
   {
      $fecha_actual=Carbon::now()->format('Y-m-d');
      $now=Carbon::now();
      $query =DB::select(
     "SELECT 
     equipo.cve_equipo,
     now() as actual,
     apartados.cve_apartado, DATE_FORMAT((
     SELECT MAX(apartados.fecha_fin)
     FROM apartados
     WHERE apartados.cve_equipo=equipo.cve_equipo AND CONVERT(apartados.fecha_registro, DATE)=:dia_actual),'%H:%i') AS disponible,
     equipo.nombre as nombre_equipo,equipo.descripcion, DATE_FORMAT(apartados.fecha_inicio, '%H:%i') AS inicio, DATE_FORMAT(apartados.fecha_fin, '%H:%i') AS fin, IFNULL(socios.foto,'NA') AS foto, IFNULL(persona.nombre,'-') AS nombre, IFNULL(persona.apellido_paterno,'-') AS paterno, IFNULL(persona.apellido_materno,'-') AS materno, CONCAT(acciones.numero_accion, CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END,'-',socios.posicion) AS nip
     FROM equipo
     LEFT JOIN apartados ON equipo.cve_equipo=apartados.cve_equipo AND CONVERT(apartados.fecha_registro, DATE)=:dia_actual AND DATE_FORMAT(:_NOW, '%H:%i') BETWEEN DATE_FORMAT(apartados.fecha_inicio, '%H:%i') AND DATE_FORMAT(apartados.fecha_fin, '%H:%i') AND apartados.estatus=1
     LEFT JOIN validacion_apartado ON apartados.cve_apartado=validacion_apartado.cve_apartado
     LEFT JOIN socios ON validacion_apartado.cve_socio=socios.cve_socio
     LEFT JOIN persona ON socios.cve_persona=persona.cve_persona
     LEFT JOIN acciones ON socios.cve_accion=acciones.cve_accion
     WHERE cve_espacio_deportivo IN (10,18) AND equipo.estatus=1",["dia_actual"=>$fecha_actual,"_NOW"=>$now]); 

      return $query;

      
   }

   public static function GetCanchasHorarios()
   {

      $canchas=DB::table("equipo")->where("cve_espacio_deportivo",10)->select('cve_equipo','nombre')->get();

      $canchas_map=$canchas->map(function($item){
          return [
            "equipo"=>$item->nombre,
            "horarios"=>self::getHorarioEquipo($item->cve_equipo)           
          ];
      });

      return $canchas_map;

   }

   public static function getHorarioEquipo($id){
     
   //   return Carbon::now()->timezone('America/Mexico_City');
      $apartados = DB::table("apartados")
         ->where("cve_equipo", $id)
         ->where("fecha_fin", ">", Carbon::now()->timezone('America/Mazatlan')->toDateTimeString())
         ->where("apartados.estatus",1);   
         
      $total = $apartados->count();
      $hora_maxima = $apartados->max("fecha_fin");
            
      
      $apartados_activos = $apartados->select("cve_apartado",DB::raw("CONVERT(fecha_inicio ,TIME) AS a"), DB::raw("CONVERT(fecha_fin,TIME) AS b"), DB::raw("2 AS c"))->get();
      $horarios = []; 
      
      $apartados_activos_map=$apartados_activos->map(function($item){
         $socios=DB::Table("validacion_apartado")
         ->join("socios","validacion_apartado.cve_socio","socios.cve_socio")
         ->join("persona","socios.cve_persona","persona.cve_persona")
         ->where("cve_apartado",$item->cve_apartado)
         ->selectRaw("CONCAT_WS(' ',nombre,apellido_paterno,apellido_materno) AS socio_name")
         ->pluck("socio_name");

      return [
         "cve_apartado"=>$item->cve_apartado,
         "a"=>$item->a,
         "b"=>$item->b,
         "c"=>$item->c,
         "socios"=>$socios
      ];
      });
         
      
      $duracion=DB::table("equipo")->where("cve_equipo",$id)->value("duracion_prestamo");
      
   
      switch ($total) {
         case 0:
            $horarios = DB::select("SELECT tabl.cve_apartado, tabl.a,tabl.b,tabl.c FROM (
            SELECT 0 AS cve_apartado, CURTIME() AS a,date_add(CURTIME(),interval :duracion minute) AS b, 1 AS c UNION ALL
            select 0 AS cve_apartado, DATE_ADD( date_add(CURTIME(),interval 5 minute) , INTERVAL  :duracion MINUTE) AS a, date_add(CURTIME(),interval :duracion*2 minute) AS b, 0 AS c union ALL
            select 0 AS cve_apartado, DATE_ADD( date_add(CURTIME(),interval 5 MINUTE) , INTERVAL  :duracion*2 MINUTE) AS a, date_add(CURTIME(),interval :duracion*3 minute) AS b, 0 AS c
            ) AS tabl;",["duracion"=>$duracion ?? 30]);          
            break;
         case 1:
            $time_apartado = (new Carbon($hora_maxima))->format("H:i:s");
            $horarios = DB::select("SELECT tabl.cve_apartado, tabl.a,tabl.b,tabl.c FROM (
            SELECT 0 AS cve_apartado, date_add(CONVERT(:hora,TIME),interval 5 minute) AS a,date_add(CONVERT(:hora,TIME),interval :duracion minute) AS b, 1 AS c UNION ALL
            select 0 AS cve_apartado, DATE_ADD( date_add(CONVERT(:hora,TIME),interval 5 minute) , INTERVAL  :duracion MINUTE) AS a, date_add(CONVERT(:hora,TIME),interval :duracion*2 minute) AS b, 0 AS c
            ) AS tabl;", ["hora" => $time_apartado,"duracion"=>$duracion ?? 30]);
            $horarios = $apartados_activos_map->concat($horarios);
            break;
         case 2:
            $time_apartado = (new Carbon($hora_maxima))->format("H:i:s");
            $horarios = DB::select("SELECT tabl.cve_apartado, tabl.a,tabl.b,tabl.c FROM (
            SELECT 0 AS cve_apartado, date_add(CONVERT(:hora,TIME),interval 5 minute) AS a,date_add(CONVERT(:hora,TIME),interval :duracion minute) AS b, 1 AS c
            ) AS tabl;", ["hora" => $time_apartado,"duracion"=>$duracion ?? 30]);
            $horarios = $apartados_activos_map->concat($horarios);
            break;
         case 3:
            $horarios = $apartados_activos_map;
            break;
      }
      return $horarios;
   }

}
