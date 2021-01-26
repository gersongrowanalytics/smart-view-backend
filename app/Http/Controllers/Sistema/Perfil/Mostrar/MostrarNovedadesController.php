<?php

namespace App\Http\Controllers\Sistema\Perfil\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\usuusuarios;
use App\audauditorias;

class MostrarNovedadesController extends Controller
{
    public function MostrarNovedades(Request $request)
    {
        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d');

        $usutoken = $request->header('api-token');

        $datos = array(
            "audsPersonales" => [],
            "audsSistema"    => []
        );

        $respuesta = true;
        $mensaje = "Tus novedades se cargaron satisfactoriamente";

        $usu = usuusuarios::where('usutoken', $usutoken)
                            ->first();

        if($usu){
            $auds = audauditorias::where('usuid', $usu->usuid)
                                    ->where('tpaid', 1)
                                    ->where('created_at', $fechaActual)
                                    ->get();

            $audsSistema = audauditorias::where('tpaid', 2)
                                        ->where('created_at', $fechaActual)
                                        ->get();

            $datos = array(
                "audsPersonales" => $auds,
                "audsSistema"    => $audsSistema
            );
        }else{
            $respuesta = false;
            $mensaje = "Lo sentimos tu usuario no existe";
        }

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            "datos"     => $datos,
        ]);
    }
}
