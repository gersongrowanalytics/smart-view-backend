<?php

namespace App\Http\Controllers\Bolivia\CuboSO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\carcargasarchivos;

class MetObtenerArchivoSOController extends Controller
{
    public function MetObtenerArchivoSO(Request $request)
    {
        $re_anio = $request['anio'];
        $re_mes  = $request['mes'];

        $nuevo_anio = $re_anio[strlen($re_anio)-2];
        $nuevo_anio = $nuevo_anio.$re_anio[strlen($re_anio)-1];

        $car = carcargasarchivos::where('tcaid', 17)
                                ->where('carurl', 'Ventas SO ('.$re_mes." ".$nuevo_anio.')')
                                ->first(['carnombrearchivo']);

        $nombre_archivo = "";

        if($car){
            $nombre_archivo = $car->carnombrearchivo;
        }

        $requestsalida = response()->json([
            "data" => $nombre_archivo,
        ]);

    }
}
