<?php

namespace App\Http\Controllers\Sistema\Usu\NuevoUsuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\sucsucursales;
use App\zonzonas;

class MostrarSucursalesController extends Controller
{
    public function MostrarSucursales()
    {

        $datos = array(
            array(
                "zona" => "",
                "sucs" => []
            )
        );

        $zons = zonzonas::where('zonestado', 1)->get(['zonid', 'zonnombre']);

        foreach ($zons as $key => $zon) {

            $sucs = sucsucursales::where('zonid', $zon->zonid)
                                ->where('sucestado', 1)
                                ->get();

            $datos[$key]['zona'] = $zon->zonnombre;
            $datos[$key]['sucs'] = $sucs;
        }

        $requestsalida = response()->json([
            'respuesta'  => true,
            'mensaje'    => "Sucursales cargadas satisfactoriamente",
            'datos'      => $datos,
        ]);
        
        return $requestsalida;
    }
}
