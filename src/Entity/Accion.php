<?php
namespace App\Entity;
use Illuminate\Database\Eloquent\Model;

class Accion extends Model 
{
    protected $table = 'acciones';
    protected $primaryKey = 'cve_accion';
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