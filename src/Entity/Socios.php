<?php
namespace App\Entity;
use Illuminate\Database\Eloquent\Model;

class Socios extends Model 
{
    protected $table = 'socios';
    protected $primaryKey = 'cve_socio';
    public $timestamps = false;

    // public function jugadores()
    // {
    //     return $this->belongsTo(Accionista::class,'cve_dueno');
    // }   

}