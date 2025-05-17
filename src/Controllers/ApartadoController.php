<?php

namespace App\Controllers;

use App\DAO\ApartadoDAO;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class ApartadoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }


    public function getApartadosVivo()
    {
        return ApartadoDAO::getApartadosVivo();
    }

    public function getCanchasHorarios()
    {
        return ApartadoDAO::GetCanchasHorarios();
    }

   
}