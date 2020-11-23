<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\proproductos;

class salvacionController extends Controller
{
    public function salvacion()
    {
        $pros = proproductos::all();

        foreach($pros as $pro){
            $proe = proproductos::find($pro->proid);
            // http://backs.gavsistemas.com/Sistema/promociones/IMAGENES/PRODUCTOS/7-493-1253-HTP%20talla%20M-31%20a%20mas%20planchas.png
            $ruta = explode("http://backs.gavsistemas.com/", $pro->proimagen);
            // $ruta = $ruta[1];
            $proe->proimagen = "http://backend.leadsmartview.com/".$ruta[1];
            $proe->update();

        }
    }
}
