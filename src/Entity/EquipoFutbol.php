<?php
namespace App\Entity;
use Illuminate\Database\Eloquent\Model;

class EquipoFutbol extends Model 
{
    protected $table = 'equipo_futbol';
    protected $primaryKey = 'id_equipo_futbol';
    public $timestamps = false;

    public function accion()
    {
        return $this->belongsTo(Accion::class,'cve_accion');
    }

    public function torneo()
    {
        return $this->belongsTo(TorneoFutbol::class,'id_torneo_futbol');
    }

    public function jugadores()
    {
        return $this->belongsToMany(Socios::class, 'equipo_futbol_jugador', 'id_equipo_futbol', 'cve_socio')->withPivot('estatus','numero_jugador');
    }    

}