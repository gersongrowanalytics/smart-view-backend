<?php

namespace App\Http\Controllers\Sistema\CargarArchivo\ListaPrecios\CanalesSucursales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\cascanalessucursales;

class MostrarCanalesSucursalesDescargaLPController extends Controller
{
    public function MostrarCanalesSucursales ()
    {
        //1 => DTT 1 - LIMA
        //2 => DTT 2 - PROVINCIAS

        $canales = ["1","2"];
        $mensaje = "Se obtuvieron los canales sucursales exitosamente";
        $respuesta = true;

        foreach ($canales as $key => $canal) {            
            $cas = cascanalessucursales::where("casid", $canal)
                                            ->first();
            if ($cas) {
                $arrayCanalesSucursales[] = $cas;
            }else{
                $respuesta = false;
                $mensaje = "No existe el id del canal sucursal ingresado: ".$rebate;
                $arrayCanalesSucursales = [];
                break;
            }
        }

        $requestsalida = response()->json([
            "respuesta"  => $respuesta,
            "mensaje"    => $mensaje,
            "data"       => $arrayCanalesSucursales,
        ]);
        
        return $requestsalida;
    }
}
