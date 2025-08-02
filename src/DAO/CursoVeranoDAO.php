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
      LEFT JOIN curso_verano_inscripcion ON cargo.cve_cargo=curso_verano_inscripcion.cve_cargo OR (curso_verano_inscripcion.semana1_reingreso = cargo.cve_cargo || curso_verano_inscripcion.semana2_reingreso = cargo.cve_cargo || curso_verano_inscripcion.semana3_reingreso = cargo.cve_cargo || curso_verano_inscripcion.semana4_reingreso = cargo.cve_cargo)
      WHERE pago.folio= 111854 AND pago.estatus=1 AND cargo.cve_cuota in(42,43,102) AND curso_verano_inscripcion.cve_curso_verano_inscripcion IS NULL */


      $query = DB::table("pago")
         ->join("cargo", "pago.idpago", "cargo.idpago")
         ->join("persona", "cargo.cve_persona", "persona.cve_persona")
         ->join("acciones", "cargo.cve_accion", "acciones.cve_accion")
         ->leftJoin("curso_verano_inscripcion",function($join){ 
            $join->on("curso_verano_inscripcion.cve_cargo", "cargo.cve_cargo")
            ->orWhere(function($or_where){
             $or_where->orWhereColumn("curso_verano_inscripcion.semana1_reingreso","cargo.cve_cargo")
             ->orWhereColumn("curso_verano_inscripcion.semana2_reingreso","cargo.cve_cargo")
             ->orWhereColumn("curso_verano_inscripcion.semana3_reingreso","cargo.cve_cargo")
             ->orWhereColumn("curso_verano_inscripcion.semana4_reingreso","cargo.cve_cargo");  
            })
            ;
         })
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


      /*
         SELECT 
	         curso_verano_programa_grupo.cve_curso_verano_programa_grupo,
            curso_verano_programa_grupo.nombre,
            curso_verano_programa_grupo.edad_min,
            curso_verano_programa_grupo.edad_max,
            curso_verano_programa_grupo.cupo,
            #COUNT(curso_verano_inscripcion.cve_curso_verano_inscripcion) AS cupo_actual
             ifnull(sum( CAST(curso_verano_inscripcion.estatus AS SIGNED)),0) AS cupo_actual,
            sum( case CAST(curso_verano_inscripcion.estatus AS SIGNED) when 0 then 1 ELSE 0 END) AS bajas
         FROM  curso_verano_programa_grupo 
         LEFT JOIN curso_verano_inscripcion ON curso_verano_programa_grupo.cve_curso_verano_programa_grupo=curso_verano_inscripcion.cve_curso_verano_programa_grupo 
         #AND curso_verano_inscripcion.estatus=1
         where curso_verano_programa_grupo.cve_curso_vereno_programa=1 GROUP BY 	curso_verano_programa_grupo.cve_curso_verano_programa_grupo
       */

      $query = DB::table("curso_verano_programa_grupo")
         // ->leftJoin("curso_verano_inscripcion",function($join){ $join->on("curso_verano_programa_grupo.cve_curso_verano_programa_grupo", "curso_verano_inscripcion.cve_curso_verano_programa_grupo")->where("curso_verano_inscripcion.estatus",1);})
         ->leftJoin("curso_verano_inscripcion", "curso_verano_programa_grupo.cve_curso_verano_programa_grupo", "curso_verano_inscripcion.cve_curso_verano_programa_grupo")
         ->where("curso_verano_programa_grupo.cve_curso_vereno_programa", $curso_verano_programa)
         ->groupBy("curso_verano_programa_grupo.cve_curso_verano_programa_grupo")
         ->select(
            "curso_verano_programa_grupo.cve_curso_verano_programa_grupo",
            "curso_verano_programa_grupo.nombre",
            "curso_verano_programa_grupo.edad_min",
            "curso_verano_programa_grupo.edad_max",
            "curso_verano_programa_grupo.cupo"
         )
         // ->selectRaw("COUNT(curso_verano_inscripcion.cve_curso_verano_inscripcion) AS cupo_actual")
         ->selectRaw("ifnull(sum( CAST(curso_verano_inscripcion.estatus AS SIGNED)),0) AS cupo_actual")
         ->selectRaw("sum(case CAST(curso_verano_inscripcion.estatus AS SIGNED) when 0 then 1 ELSE 0 END) AS bajas")
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
            DB::table("persona")->where("cve_persona", $insc->cve_persona)->update(["nombre" => $insc->nombre, "apellido_paterno" => $insc->materno, "apellido_materno" => $insc->paterno, "sexo" => $insc->genero, "fecha_nacimiento" => $insc->nacimiento]);
            DB::table("socios")->where("cve_persona", $insc->cve_persona)->update(["foto_socio" => file_get_contents($insc->foto), "is_foto" => 11]);
         } else if ($insc->cve_cuota == 43) {

            /*if(DB::table("persona")->where("cve_persona",$insc->cve_persona)->exists())
      {
         DB::table("persona")->where("cve_persona")->update(["nombre"=>$insc->nombre,"apellido_paterno"=>$insc->materno,"apellido_materno"=>$insc->paterno,"sexo"=>$insc->genero,"fecha_nacimiento"=>$insc->nacimiento]);
      }
      else{*/

            $cve_persona_invitado = DB::table("persona")
               ->insertGetId(["nombre" => $insc->nombre, "apellido_paterno" => $insc->paterno, "apellido_materno" => $insc->materno, "sexo" => $insc->genero, "fecha_nacimiento" => $insc->nacimiento, "estado_civil" => "Soltero", "cve_pais" => 121, "estatus" => 1]);

            $cve_direcion_invitado = DB::table("direccion")->insertGetId(["cve_colonia" => 1, "calle" => $insc->calle_numero]);

            DB::table("socios")
               ->insert(["cve_accion" => 1830, "cve_persona" => $cve_persona_invitado, "cve_direccion" => $cve_direcion_invitado, "cve_profesion" => 16, "cve_parentesco" => 12, "estatus" => 1, "fecha_ingreso_accion" => Carbon::now(), "fecha_ingreso_club" => Carbon::now(), "fecha_alta" => Carbon::now(), "foto_socio" => file_get_contents($insc->foto), "is_foto" => 11]);

            $insc->cve_persona = $cve_persona_invitado;
            //}


         } else if ($insc->cve_cuota == 102) {

            /*if(DB::table("persona")->where("cve_persona",$insc->cve_persona)->exists())
      {
         DB::table("persona")->where("cve_persona",$insc->cve_persona)->update(["nombre"=>$insc->nombre,"apellido_paterno"=>$insc->materno,"apellido_materno"=>$insc->paterno,"sexo"=>$insc->genero,"fecha_nacimiento"=>$insc->nacimiento]);
      }

      else{*/

            $cve_persona_invitado = DB::table("persona")
               ->insertGetId(["nombre" => $insc->nombre, "apellido_paterno" => $insc->paterno, "apellido_materno" => $insc->materno, "sexo" => $insc->genero, "fecha_nacimiento" => $insc->nacimiento, "estado_civil" => "Soltero", "cve_pais" => 121, "estatus" => 1]);

            $cve_direcion_invitado = DB::table("direccion")->insertGetId(["cve_colonia" => 1, "calle" => $insc->calle_numero]);

            DB::table("socios")
               ->insert(["cve_accion" => 1948, "cve_persona" => $cve_persona_invitado, "cve_direccion" => $cve_direcion_invitado, "cve_profesion" => 16, "cve_parentesco" => 12, "estatus" => 1, "fecha_ingreso_accion" => Carbon::now(), "fecha_ingreso_club" => Carbon::now(), "fecha_alta" => Carbon::now(), "foto_socio" => file_get_contents($insc->foto), "is_foto" => 11]);


            $cuota_ = DB::table("cuota")->where("cve_cuota", 102)->select("cuota", "precio")->first();
            $cve_cargo_ = DB::table("cargo")->insertGetId([
               "cve_accion" => 1914,
               "cve_cuota" => 102,
               "cve_persona" => $insc->cve_persona,
               "concepto" => $cuota_->cuota,
               "total" => $cuota_->precio,
               "subtotal" => ($cuota_->precio / 116) * 100,
               "iva" => (($cuota_->precio / 116) * 100) * .16,
               "periodo" => date("m-Y")
            ]);

            $insc->cve_cargo = $cve_cargo_;
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
            "folio_pago" => $insc->folio ?? '',
            "folio_boleta" => $insc->folio_boleta,


            "responsable" => $insc->tutor,
            "telefono_contacto" => $insc->telefono_contacto,
            "fecha_inscripcion" => Carbon::now(),

            "nadar" => $insc->nadar,
            "semana1" => $insc->semana1 ?? null,
            "semana2" => $insc->semana2 ?? null,
            "semana3" => $insc->semana3 ?? null,
            "semana4" => $insc->semana4 ?? null,

            "calle_numero" => $insc->calle_numero,
            "colonia" => $insc->colonia,
            "observaciones" => $insc->observaciones,

         ]);

         return $id;
      });
   }


   public static function updateInscripcion($id, $insc)
   {






      return DB::transaction(function () use ($id, $insc) {



         DB::table("persona")
            ->where("cve_persona", $insc->cve_persona)
            ->update([
               "nombre" => $insc->nombre,
               "apellido_paterno" => $insc->paterno,
               "apellido_materno" => $insc->materno,
               "sexo" => $insc->genero,
               "fecha_nacimiento" => $insc->nacimiento,
            ]);

         $id =  DB::table("curso_verano_inscripcion")
            ->where("cve_curso_verano_inscripcion", $id)
            ->update([

               "folio_boleta" => $insc->folio_boleta,
               "responsable" => $insc->tutor,
               "telefono_contacto" => $insc->telefono_contacto,
               "nadar" => $insc->nadar,
               "calle_numero" => $insc->calle_numero,
               "colonia" => $insc->colonia,
               "observaciones" => $insc->observaciones,

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


   public static function getInscripcionesCurso()
   {

      /*
         select 
               curso_verano_inscripcion.cve_curso_verano_inscripcion,
               curso_verano_inscripcion.folio_pago,
               if(curso_verano_inscripcion.semana1 is not null,1,0)+if(curso_verano_inscripcion.semana2 is not null,1,0)+if(curso_verano_inscripcion.semana3 is not null,1,0)+if(curso_verano_inscripcion.semana4 is not null,1,0) as semanas,
               if(curso_verano_inscripcion.semana1 is not null,1,0) as semana1,
               if(curso_verano_inscripcion.semana2 is not null,1,0) as semana2,
               if(curso_verano_inscripcion.semana3 is not null,1,0) as semana3,
               if(curso_verano_inscripcion.semana4 is not null,1,0) as semana4,
               curso_verano_inscripcion.nadar,
               curso_verano_inscripcion.responsable,
               curso_verano_inscripcion.telefono_contacto,
               curso_verano_inscripcion.fecha_inscripcion,
               persona.nombre,
               persona.apellido_paterno,
               persona.apellido_materno,
               persona.sexo,
               persona.fecha_nacimiento,
               curso_verano_programa.nombre as programa,
               curso_verano_programa_grupo.nombre as grupo,
               TIMESTAMPDIFF(YEAR, persona.fecha_nacimiento, CURDATE()) as edad
         from curso_verano_inscripcion
         inner join persona on curso_verano_inscripcion.cve_persona=persona.cve_persona
         inner join curso_verano_programa on curso_verano_inscripcion.cve_curso_verano_programa=curso_verano_programa.cve_curso_verano_programa
         inner join curso_verano_programa_grupo on curso_verano_inscripcion.cve_curso_verano_programa_grupo=curso_verano_programa_grupo.cve_curso_verano_programa_grupo
       */



      $query = DB::table("curso_verano_inscripcion")
         ->join("persona", "curso_verano_inscripcion.cve_persona", "persona.cve_persona")
         ->join("curso_verano_programa", "curso_verano_inscripcion.cve_curso_verano_programa", "curso_verano_programa.cve_curso_verano_programa")
         ->join("curso_verano_programa_grupo", "curso_verano_inscripcion.cve_curso_verano_programa_grupo", "curso_verano_programa_grupo.cve_curso_verano_programa_grupo")
         ->select(
            "curso_verano_inscripcion.cve_curso_verano_inscripcion",
            "curso_verano_inscripcion.folio_pago",
            "curso_verano_inscripcion.folio_boleta",
            "curso_verano_inscripcion.nadar",
            "curso_verano_inscripcion.responsable",
            "curso_verano_inscripcion.telefono_contacto",
            "curso_verano_inscripcion.fecha_inscripcion",
            "persona.nombre",
            "persona.apellido_paterno",
            "persona.apellido_materno",
            "persona.sexo",
            "persona.fecha_nacimiento",
            "curso_verano_programa.nombre as programa",
            "curso_verano_programa_grupo.nombre as grupo",
            "curso_verano_inscripcion.estatus"
         )
         ->selectRaw("if(curso_verano_inscripcion.semana1 is not null,1,0)+if(curso_verano_inscripcion.semana2 is not null,1,0)+if(curso_verano_inscripcion.semana3 is not null,1,0)+if(curso_verano_inscripcion.semana4 is not null,1,0) as semanas")
         ->selectRaw("if(curso_verano_inscripcion.semana1 is not null,1,0) as semana1")
         ->selectRaw("if(curso_verano_inscripcion.semana2 is not null,1,0) as semana2")
         ->selectRaw("if(curso_verano_inscripcion.semana3 is not null,1,0) as semana3")
         ->selectRaw("if(curso_verano_inscripcion.semana4 is not null,1,0) as semana4")
         ->selectRaw("TIMESTAMPDIFF(YEAR, persona.fecha_nacimiento, CURDATE()) as edad")
         ->get();

      // dd($query);

      return $query;
   }


   public static function getFotoSocio($id)
   {
      try {
         return DB::table("socios")->where("socios.cve_persona", $id)->value("foto_socio");
      } catch (\Exception $th) {
      }
   }


   public static function getSemanasRestantes($id_inscripcion)
   {

      //select if(semana1 is null,1,0) as semana1,if(semana2 is null,1,0) as semana2,if(semana3 is null,1,0) as semana3,if(semana4 is null,1,0) as semana4 from curso_verano_inscripcion where cve_curso_verano_inscripcion=1
      try {
         return DB::table("curso_verano_inscripcion")->where("cve_curso_verano_inscripcion", $id_inscripcion)
            ->selectRaw("if(semana1 is null,1,0) as semana1")
            ->selectRaw("if(semana2 is null,1,0) as semana2")
            ->selectRaw("if(semana3 is null,1,0) as semana3")
            ->selectRaw("if(semana4 is null,1,0) as semana4")
            ->first();
      } catch (\Exception $th) {
      }
   }


   public static function getColaboradorByNomina($nomina)
   {

      // SELECT persona.cve_persona,persona.nombre,persona.apellido_paterno,persona.apellido_materno 
      // FROM colaborador
      // INNER JOIN persona ON colaborador.cve_persona=persona.cve_persona
      // WHERE colaborador.nomina=1007
      try {
         return DB::table("colaborador")
            ->join("persona", "colaborador.cve_persona", "persona.cve_persona")
            ->where("colaborador.nomina", $nomina)
            ->select("persona.cve_persona", "persona.nombre", "persona.apellido_paterno", "persona.apellido_materno")
            ->first();
      } catch (\Exception $th) {
      }
   }


   public static function bajaCursoVerano($id_inscripcion)
   {

      //select if(semana1 is null,1,0) as semana1,if(semana2 is null,1,0) as semana2,if(semana3 is null,1,0) as semana3,if(semana4 is null,1,0) as semana4 from curso_verano_inscripcion where cve_curso_verano_inscripcion=1
      try {
         return DB::table("curso_verano_inscripcion")->where("cve_curso_verano_inscripcion", $id_inscripcion)->update(["estatus" => 0]);
      } catch (\Exception $th) {
      }
   }

   public static function reporteCursoVerano($params)
   {

      /*
            SELECT 
               curso_verano_inscripcion.cve_curso_verano_programa_grupo,
               curso_verano_inscripcion.cve_accion,
	            curso_verano_inscripcion.folio_pago,
	            curso_verano_inscripcion.folio_boleta,
	            persona.nombre,
	            persona.apellido_paterno,
	            persona.apellido_materno,
	            persona.sexo,
	            persona.fecha_nacimiento,
               TIMESTAMPDIFF(YEAR, persona.fecha_nacimiento, CURDATE()) as edad,
	            curso_verano_inscripcion.responsable,
	            curso_verano_inscripcion.telefono_contacto,
	            curso_verano_inscripcion.calle_numero,
	            curso_verano_inscripcion.colonia,
	            curso_verano_inscripcion.semana1,
	            curso_verano_inscripcion.semana2,
	            curso_verano_inscripcion.semana3,
	            curso_verano_inscripcion.semana4,
	            curso_verano_inscripcion.fecha_inscripcion,
	            curso_verano_inscripcion.nadar,
	            curso_verano_programa.nombre AS programa,
	            curso_verano_programa_grupo.nombre AS grupo,
	            cargo.concepto,
	            concat(acciones.numero_accion,case acciones.clasificacion when 1 then 'A' when 2 then 'B' when 3 then 'C' ELSE '' END) AS accion,
	            curso_verano_inscripcion.estatus,
               case curso_verano_inscripcion.cve_accion when 1830 then 'I' when 1948 then 'C' ELSE 'S' END AS tipo,
            FROM curso_verano_inscripcion
            INNER JOIN cursos_verano ON curso_verano_inscripcion.cve_curso_verano=cursos_verano.cve_curso
            INNER JOIN curso_verano_programa ON curso_verano_inscripcion.cve_curso_verano_programa=curso_verano_programa.cve_curso_verano_programa
            INNER JOIN curso_verano_programa_grupo ON curso_verano_inscripcion.cve_curso_verano_programa_grupo=curso_verano_programa_grupo.cve_curso_verano_programa_grupo
            INNER JOIN cargo On curso_verano_inscripcion.cve_cargo=cargo.cve_cargo
            INNER JOIN persona ON curso_verano_inscripcion.cve_persona=persona.cve_persona
            INNER JOIN acciones ON curso_verano_inscripcion.cve_accion=acciones.cve_accion
      */

      try {
         $query = DB::table("curso_verano_inscripcion")
            ->join("cursos_verano", "curso_verano_inscripcion.cve_curso_verano", "cursos_verano.cve_curso")
            ->join("curso_verano_programa", "curso_verano_inscripcion.cve_curso_verano_programa", "curso_verano_programa.cve_curso_verano_programa")
            ->join("curso_verano_programa_grupo", "curso_verano_inscripcion.cve_curso_verano_programa_grupo", "curso_verano_programa_grupo.cve_curso_verano_programa_grupo")
            ->join("cargo", "curso_verano_inscripcion.cve_cargo", "cargo.cve_cargo")
            ->join("persona", "curso_verano_inscripcion.cve_persona", "persona.cve_persona")
            ->join("acciones", "curso_verano_inscripcion.cve_accion", "acciones.cve_accion")
            ->select(
               "curso_verano_inscripcion.cve_curso_verano_programa_grupo",
               "curso_verano_inscripcion.cve_accion",
               "curso_verano_inscripcion.folio_pago",
               "curso_verano_inscripcion.folio_boleta",
               "persona.nombre",
               "persona.apellido_paterno",
               "persona.apellido_materno",
               "persona.sexo",
               "persona.fecha_nacimiento",
               "curso_verano_inscripcion.responsable",
               "curso_verano_inscripcion.telefono_contacto",
               "curso_verano_inscripcion.calle_numero",
               "curso_verano_inscripcion.colonia",
               "curso_verano_inscripcion.semana1",
               "curso_verano_inscripcion.semana2",
               "curso_verano_inscripcion.semana3",
               "curso_verano_inscripcion.semana4",
               "curso_verano_inscripcion.fecha_inscripcion",
               "curso_verano_inscripcion.nadar",
               "curso_verano_programa.nombre AS programa",
               "curso_verano_programa_grupo.nombre AS grupo",
               "cargo.concepto",
               "curso_verano_inscripcion.estatus"
            )
            ->selectRaw("concat(acciones.numero_accion,case acciones.clasificacion when 1 then 'A' when 2 then 'B' when 3 then 'C' ELSE '' END) AS accion")
            ->selectRaw("TIMESTAMPDIFF(YEAR, persona.fecha_nacimiento, CURDATE()) as edad")
            ->selectRaw("if(curso_verano_inscripcion.semana1 IS NULL,0,1)+if(curso_verano_inscripcion.semana2 IS NULL,0,1)+if(curso_verano_inscripcion.semana3 IS NULL,0,1)+if(curso_verano_inscripcion.semana4 IS NULL,0,1) AS semanas")
            ->selectRaw("case curso_verano_inscripcion.cve_accion when 1830 then 'I' when 1948 then 'C' ELSE 'S' END AS tipo");


         return $query->get();
      } catch (\Exception $th) {
      }
   }



   public static function getDatosInscripcion($id_inscripcion)
   {

      /*
         SELECT 
	         curso_verano_inscripcion.folio_pago,
	         curso_verano_inscripcion.folio_boleta,
	         cargo.total,
	         concat (acciones.numero_accion ,case acciones.clasificacion when 1 then 'A' when 2 then 'B' when 3 then 'C' ELSE'' END) AS accion_,
	         curso_verano_inscripcion.semana1,
	         curso_verano_inscripcion.semana2,
	         curso_verano_inscripcion.semana3,
	         curso_verano_inscripcion.semana4,
	         curso_verano_inscripcion.cve_persona,
	         persona.nombre,
	         persona.apellido_paterno,
	         persona.apellido_materno,
	         persona.fecha_nacimiento,
	         persona.sexo,
	         curso_verano_inscripcion.responsable,
	         curso_verano_inscripcion.telefono_contacto,
	         curso_verano_inscripcion.calle_numero,
	         curso_verano_inscripcion.colonia,
	         curso_verano_inscripcion.fecha_inscripcion,
	         curso_verano_inscripcion.nadar,
	         curso_verano_inscripcion.cve_curso_verano_programa,
	         curso_verano_inscripcion.cve_curso_verano_programa_grupo,
            curso_verano_inscripcion.observaciones
            cargo.cve_cuota,
            acciones.cve_accion
         FROM curso_verano_inscripcion
         INNER JOIN persona ON curso_verano_inscripcion.cve_persona=persona.cve_persona
         INNER JOIN cargo ON curso_verano_inscripcion.cve_cargo=cargo.cve_cargo
         INNER JOIN acciones ON curso_verano_inscripcion.cve_accion=acciones.cve_accion
         WHERE curso_verano_inscripcion.cve_curso_verano_inscripcion=1
      */


      try {
         return DB::table("curso_verano_inscripcion")
            ->join("persona", "curso_verano_inscripcion.cve_persona", "persona.cve_persona")
            ->join("cargo", "curso_verano_inscripcion.cve_cargo", "cargo.cve_cargo")
            ->join("acciones", "curso_verano_inscripcion.cve_accion", "acciones.cve_accion")
            ->where("curso_verano_inscripcion.cve_curso_verano_inscripcion", $id_inscripcion)
            ->select(
               "curso_verano_inscripcion.folio_pago",
               "curso_verano_inscripcion.folio_boleta",
               "cargo.total",
               "curso_verano_inscripcion.semana1",
               "curso_verano_inscripcion.semana2",
               "curso_verano_inscripcion.semana3",
               "curso_verano_inscripcion.semana4",
               "curso_verano_inscripcion.cve_persona",
               "persona.nombre",
               "persona.apellido_paterno",
               "persona.apellido_materno",
               "persona.fecha_nacimiento",
               "persona.sexo",
               "curso_verano_inscripcion.responsable",
               "curso_verano_inscripcion.telefono_contacto",
               "curso_verano_inscripcion.calle_numero",
               "curso_verano_inscripcion.colonia",
               "curso_verano_inscripcion.fecha_inscripcion",
               "curso_verano_inscripcion.nadar",
               "curso_verano_inscripcion.cve_curso_verano_programa",
               "curso_verano_inscripcion.cve_curso_verano_programa_grupo",
               "curso_verano_inscripcion.observaciones",
               "cargo.cve_cuota",
               "acciones.cve_accion"
            )
            ->selectRaw("concat (acciones.numero_accion ,case acciones.clasificacion when 1 then 'A' when 2 then 'B' when 3 then 'C' ELSE'' END) AS accion_")
            ->first();
      } catch (\Exception $th) {
      }
   }



   

public static function createReingreso($id,$insc)
   {

      return DB::table("curso_verano_inscripcion")->where("cve_curso_verano_inscripcion",$id)->update($insc);
   }




}
