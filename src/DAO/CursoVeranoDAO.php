<?php

namespace App\DAO;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class CursoVeranoDAO
{

   public function __construct() {}

   public static function getCargoCursoVeranoByFolio($folio)
   {
      /*SELECT 
         cargo.cve_cargo,
         cargo.cve_cuota,
      	cargo.cve_accion,
      	cargo.cve_persona,
      	cargo.concepto,
      	cargo.total,
      	cargo.periodo,
      	cargo.fecha_cargo,
      	persona.nombre,
      	persona.apellido_paterno,
      	persona.apellido_materno,
      	CONCAT(acciones.numero_accion,case acciones.clasificacion when 1 then 'A' when 2 then 'B' when 3 then 'C' ELSE '' END) AS accion_ 
      FROM pago
      INNER JOIN cargo ON pago.idpago=cargo.idpago
      INNER JOIN persona ON cargo.cve_persona=persona.cve_persona
      INNER JOIN acciones ON cargo.cve_accion=acciones.cve_accion
      LEFT JOIN curso_verano_inscripcion ON cargo.cve_cargo=curso_verano_inscripcion.cve_cargo AND curso_verano_inscripcion.cve_persona=cargo.cve_persona
      WHERE pago.folio= 111854 AND pago.estatus=1 AND cargo.cve_cuota in(43,43,102)*/

      $query = DB::table("pago")
         ->join("cargo", "pago.idpago", "cargo.idpago")
         ->join("persona", "cargo.cve_persona", "persona.cve_persona")
         ->join("acciones", "cargo.cve_accion", "acciones.cve_accion")
         ->leftJoin("curso_verano_inscripcion", "curso_verano_inscripcion.cve_cargo", "cargo.cve_cargo")
         ->where("pago.folio", $folio)
         ->where("pago.estatus", 1)
         ->whereIn("cargo.cve_cuota", [42, 43, 102])
         ->whereNull("curso_verano_inscripcion.cve_curso_verano_inscripcion")
         ->select(
            "cargo.cve_cargo",
            "cargo.cve_cuota",
            "cargo.cve_accion",
            "cargo.cve_persona",
            "cargo.concepto",
            "cargo.total",
            "cargo.periodo",
            "cargo.fecha_cargo",
            "persona.nombre",
            "persona.apellido_paterno",
            "persona.apellido_materno"
         )
         ->selectRaw("CONCAT(acciones.numero_accion,case acciones.clasificacion when 1 then 'A' when 2 then 'B' when 3 then 'C' ELSE '' END) AS accion_")
         ->get();

      return $query;
   }

   public static function getInscritoExistente($cve_persona)
   {
      $query = DB::table("persona")
         ->where("cve_persona", $cve_persona)
         ->select(
            "cve_persona",
            "nombre",
            "apellido_paterno",
            "apellido_materno",
            "sexo",
            "fecha_nacimiento"
         )
         ->first();

      return $query;
   }
   public static function getProgramasCursoVerano($curso_verano)
   {


      $query = DB::table("curso_verano_programa")
         ->where("curso_verano_programa.cve_curso_verano", $curso_verano)
         ->select(
            "curso_verano_programa.cve_curso_verano_programa",
            "curso_verano_programa.nombre",
            "curso_verano_programa.estatus"
         )
         ->get();

      return $query;
   }

   public static function getGrupoCursoVerano($curso_verano_programa)
   {


      $query = DB::table("curso_verano _programa_grupo")
         ->where("curso_verano_programa_grupo.cve_curso_vereno_programa", $curso_verano_programa)
         ->select(
            "curso_verano_programa_grupo.cve_curso_verano_programa_grupo",
            "curso_verano_programa_grupo.nombre",
            "curso_verano_programa_grupo.edad_min",
            "curso_verano_programa_grupo.edad_max",
            "curso_verano_programa_grupo.cupo"
         )
         ->get();

      return $query;
   }

   public static function createInscripcion($insc)
   {

     

      // return DB::table("socios")->where("cve_socio",11499)->update(["foto_socio"=>file_get_contents($insc->foto)]);

      //  dd($insc->foto);

      if (!collect([42, 43, 102])->contains($insc->cve_cuota)) {
         return "no es un cargo de curso verano";
      }

      return DB::transaction(function () use ($insc) {



         if ($insc->cve_cuota == 42) {
            DB::table("persona")->where("cve_persona",$insc->cve_persona)->update(["nombre" => $insc->nombre, "apellido_paterno" => $insc->materno, "apellido_materno" => $insc->paterno, "sexo" => $insc->genero, "fecha_nacimiento" => $insc->nacimiento]);
            DB::table("socios")->where("cve_persona",$insc->cve_persona)->update(["foto_socio"=>file_get_contents($insc->foto),"is_foto"=>11]);
         } else if ($insc->cve_cuota == 43) {

            /*if(DB::table("persona")->where("cve_persona",$insc->cve_persona)->exists())
      {
         DB::table("persona")->where("cve_persona")->update(["nombre"=>$insc->nombre,"apellido_paterno"=>$insc->materno,"apellido_materno"=>$insc->paterno,"sexo"=>$insc->genero,"fecha_nacimiento"=>$insc->nacimiento]);
      }
      else{*/

            $cve_persona_invitado = DB::table("persona")
               ->insertGetId(["nombre" => $insc->nombre, "apellido_paterno" => $insc->materno, "apellido_materno" => $insc->paterno, "sexo" => $insc->genero, "fecha_nacimiento" => $insc->nacimiento, "estado_civil" => "Soltero", "cve_pais" => 121, "estatus" => 1]);

            $cve_direcion_invitado = DB::table("direccion")->insertGetId(["cve_colonia" => 1, "calle" => $insc->calle_numero]);

            DB::table("socios")
               ->insert(["cve_accion" => 1830, "cve_persona" => $cve_persona_invitado, "cve_direccion" => $cve_direcion_invitado, "cve_profesion" => 16, "cve_parentesco" => 12, "estatus" => 1, "fecha_ingreso_accion" => Carbon::now(), "fecha_ingreso_club" => Carbon::now(), "fecha_alta" => Carbon::now(),"foto_socio"=>file_get_contents($insc->foto),"is_foto"=>11]);

            $insc->cve_persona = $cve_persona_invitado;
            //}


         } else if ($insc->cve_cuota == 102) {

            /*if(DB::table("persona")->where("cve_persona",$insc->cve_persona)->exists())
      {
         DB::table("persona")->where("cve_persona",$insc->cve_persona)->update(["nombre"=>$insc->nombre,"apellido_paterno"=>$insc->materno,"apellido_materno"=>$insc->paterno,"sexo"=>$insc->genero,"fecha_nacimiento"=>$insc->nacimiento]);
      }

      else{*/

            $cve_persona_invitado = DB::table("persona")
               ->insertGetId(["nombre" => $insc->nombre, "apellido_paterno" => $insc->materno, "apellido_materno" => $insc->paterno, "sexo" => $insc->genero, "fecha_nacimiento" => $insc->nacimiento, "estado_civil" => "Soltero", "cve_pais" => 121, "estatus" => 1]);

            $cve_direcion_invitado = DB::table("direccion")->insertGetId(["cve_colonia" => 1, "calle" => $insc->calle_numero]);

            DB::table("socios")
               ->insert(["cve_accion" => 1948, "cve_persona" => $cve_persona_invitado, "cve_direccion" => $cve_direcion_invitado, "cve_profesion" => 16, "cve_parentesco" => 12, "estatus" => 1, "fecha_ingreso_accion" => Carbon::now(), "fecha_ingreso_club" => Carbon::now(), "fecha_alta" => Carbon::now(), "foto_socio"=>file_get_contents($insc->foto),"is_foto"=>11]);

            $insc->cve_persona = $cve_persona_invitado;
            //}

         }

         $id =  DB::table("curso_verano_inscripcion")->insertGetId([
            "cve_curso_verano" => 1,
            "cve_curso_verano_programa" => $insc->programa,
            "cve_curso_verano_programa_grupo" => $insc->grupo,
            "cve_cargo" => $insc->cve_cargo,
            "cve_persona" => $insc->cve_persona,
            "cve_accion" => $insc->cve_accion,
            "folio_pago" => $insc->folio,


            "responsable" => $insc->tutor,
            "telefono_contacto" => $insc->telefono_contacto,
            "fecha_inscripcion" => Carbon::now(),

            "nadar" => $insc->nadar,
            "semana1" => $insc->semana1 ?? null,
            "semana2" => $insc->semana2 ?? null,
            "semana3" => $insc->semana3 ?? null,
            "semana4" => $insc->semana4 ?? null,

            "calle_numero"=>$insc->calle_numero,
            "colonia"=>$insc->colonia,

         ]);

         return $id;
      });
   }

   public static function getSociosAccion($cve_accion)
   {


      // SELECT persona.cve_persona,socios.posicion,socios.foto_socio,persona.nombre,persona.apellido_paterno,persona.apellido_materno,persona.fecha_nacimiento,persona.sexo 
      // FROM socios
      // INNER JOIN persona ON socios.cve_persona=persona.cve_persona
      // WHERE cve_accion=150

      $query = DB::table("socios")
         ->join("persona", "socios.cve_persona", "persona.cve_persona")
         ->where("cve_accion", $cve_accion)
         ->select(
            "persona.cve_persona",
            "socios.posicion",
            //"socios.foto_socio",
            "persona.nombre",
            "persona.apellido_paterno",
            "persona.apellido_materno",
            "persona.fecha_nacimiento",
            "persona.sexo"
         )
         ->get();

         // dd($query);

      return $query;
   }

   // public static function GetCanchasHorarios()
   // {

   //    $canchas=DB::table("equipo")->where("cve_espacio_deportivo",10)->select('cve_equipo','nombre')->get();

   //    $canchas_map=$canchas->map(function($item){
   //        return [
   //          "equipo"=>$item->nombre,
   //          "horarios"=>self::getHorarioEquipo($item->cve_equipo)           
   //        ];
   //    });

   //    return $canchas_map;

   // }

   // public static function getHorarioEquipo($id){

   // //   return Carbon::now()->timezone('America/Mexico_City');
   //    $apartados = DB::table("apartados")
   //       ->where("cve_equipo", $id)
   //       ->where("fecha_fin", ">", Carbon::now()->timezone('America/Mazatlan')->toDateTimeString())
   //       ->where("apartados.estatus",1);   

   //    $total = $apartados->count();
   //    $hora_maxima = $apartados->max("fecha_fin");


   //    $apartados_activos = $apartados->select("cve_apartado",DB::raw("CONVERT(fecha_inicio ,TIME) AS a"), DB::raw("CONVERT(fecha_fin,TIME) AS b"), DB::raw("2 AS c"))->get();
   //    $horarios = []; 

   //    $apartados_activos_map=$apartados_activos->map(function($item){
   //       $socios=DB::Table("validacion_apartado")
   //       ->join("socios","validacion_apartado.cve_socio","socios.cve_socio")
   //       ->join("persona","socios.cve_persona","persona.cve_persona")
   //       ->where("cve_apartado",$item->cve_apartado)
   //       ->selectRaw("CONCAT_WS(' ',nombre,apellido_paterno,apellido_materno) AS socio_name")
   //       ->pluck("socio_name");

   //    return [
   //       "cve_apartado"=>$item->cve_apartado,
   //       "a"=>$item->a,
   //       "b"=>$item->b,
   //       "c"=>$item->c,
   //       "socios"=>$socios
   //    ];
   //    });


   //    $duracion=DB::table("equipo")->where("cve_equipo",$id)->value("duracion_prestamo");


   //    switch ($total) {
   //       case 0:
   //          $horarios = DB::select("SELECT tabl.cve_apartado, tabl.a,tabl.b,tabl.c FROM (
   //          SELECT 0 AS cve_apartado, CURTIME() AS a,date_add(CURTIME(),interval :duracion minute) AS b, 1 AS c UNION ALL
   //          select 0 AS cve_apartado, DATE_ADD( date_add(CURTIME(),interval 5 minute) , INTERVAL  :duracion MINUTE) AS a, date_add(CURTIME(),interval :duracion*2 minute) AS b, 0 AS c union ALL
   //          select 0 AS cve_apartado, DATE_ADD( date_add(CURTIME(),interval 5 MINUTE) , INTERVAL  :duracion*2 MINUTE) AS a, date_add(CURTIME(),interval :duracion*3 minute) AS b, 0 AS c
   //          ) AS tabl;",["duracion"=>$duracion ?? 30]);          
   //          break;
   //       case 1:
   //          $time_apartado = (new Carbon($hora_maxima))->format("H:i:s");
   //          $horarios = DB::select("SELECT tabl.cve_apartado, tabl.a,tabl.b,tabl.c FROM (
   //          SELECT 0 AS cve_apartado, date_add(CONVERT(:hora,TIME),interval 5 minute) AS a,date_add(CONVERT(:hora,TIME),interval :duracion minute) AS b, 1 AS c UNION ALL
   //          select 0 AS cve_apartado, DATE_ADD( date_add(CONVERT(:hora,TIME),interval 5 minute) , INTERVAL  :duracion MINUTE) AS a, date_add(CONVERT(:hora,TIME),interval :duracion*2 minute) AS b, 0 AS c
   //          ) AS tabl;", ["hora" => $time_apartado,"duracion"=>$duracion ?? 30]);
   //          $horarios = $apartados_activos_map->concat($horarios);
   //          break;
   //       case 2:
   //          $time_apartado = (new Carbon($hora_maxima))->format("H:i:s");
   //          $horarios = DB::select("SELECT tabl.cve_apartado, tabl.a,tabl.b,tabl.c FROM (
   //          SELECT 0 AS cve_apartado, date_add(CONVERT(:hora,TIME),interval 5 minute) AS a,date_add(CONVERT(:hora,TIME),interval :duracion minute) AS b, 1 AS c
   //          ) AS tabl;", ["hora" => $time_apartado,"duracion"=>$duracion ?? 30]);
   //          $horarios = $apartados_activos_map->concat($horarios);
   //          break;
   //       case 3:
   //          $horarios = $apartados_activos_map;
   //          break;
   //    }
   //    return $horarios;
   // }

}
