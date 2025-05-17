<?php
namespace App\Entity;
use Illuminate\Database\Eloquent\Model;

class TorneoFutbol extends Model 
{
    protected $table = 'torneo_futbol';
    protected $primaryKey = 'id_torneo_futbol';
    public $timestamps = false;

    // public function jugadores()
    // {
    //     return $this->belongsTo(Accionista::class,'cve_dueno');
    // } 
    
    public function jugadores()
    {
        return $this->belongsToMany(Socios::class, 'equipo_futbol_jugador', 'cve_accion', 'cve_socio')->withPivot('estatus','numero_jugador');
    }

}