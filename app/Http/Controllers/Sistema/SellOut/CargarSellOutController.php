<?php

namespace App\Http\Controllers\Sistema\SellOut;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CargarSellOutController extends Controller
{
    public function CargarSellOut()
    {
        $data = json_decode( file_get_contents('http://localhost/mostrar/tdis'), true );
        
        $datos = $data['datos'];
        foreach($datos as $dato){
            echo $dato['tdiid'];
        }

        
    }
}
