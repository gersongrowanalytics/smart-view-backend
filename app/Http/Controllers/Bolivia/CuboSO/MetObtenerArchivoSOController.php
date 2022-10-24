<?php

namespace App\Http\Controllers\Bolivia\CuboSO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\carcargasarchivos;
use App\vsbventassobol;

class MetObtenerArchivoSOController extends Controller
{
    public function MetObtenerArchivoSO(Request $request)
    {
        $re_anio = strval($request['anio']);
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

        return $requestsalida;
    }

    public function MetObtenerFiltros(Request $request)
    {

        $vsb_region = vsbventassobol::distinct('vsbregion')->get(['vsbregion']);

        foreach($vsb_region as $pos_region => $vsbregion){
            $vsb_empresa = vsbventassobol::where('vsbregion', $vsbregion->vsbregion)
                                        ->distinct('vsbempresa')
                                        ->get(['vsbempresa']);

            $vsb_region[$pos_region]['empresas'] = $vsb_empresa;
        }

        $requestsalida = response()->json([
            "data" => $vsb_region,
        ]);

        return $requestsalida;

    }
}
