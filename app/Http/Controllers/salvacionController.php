<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\proproductos;

class salvacionController extends Controller
{
    public function salvacion()
    {
        $pros = proproductos::where('proimagen', 'LIKE', '%http://backs.gavsistemas.co%')->get();

        foreach($pros as $pro){
            $proe = proproductos::find($pro->proid);
            // http://backs.gavsistemas.com/Sistema/promociones/IMAGENES/PRODUCTOS/7-493-1253-HTP%20talla%20M-31%20a%20mas%20planchas.png
            $ruta = explode("http://backs.gavsistemas.com/", $pro->proimagen);

            if(sizeof($ruta) > 0){
                $proe->proimagen = "http://backend.leadsmartview.com/".$ruta[1];
                $proe->update();
            }else{
                echo "<br>no tiene: ".$pro->proid.' y nombre: '.$pro->proimagen.'<br>';

            }
            

        }
    }
}
