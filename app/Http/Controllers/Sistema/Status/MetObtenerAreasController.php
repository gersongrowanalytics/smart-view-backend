<?php

namespace App\Http\Controllers\Sistema\Status;

use App\areareas;
use App\coacontrolarchivos;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MetObtenerAreasController extends Controller
{
    public function MetObtenerAreas (Request $request)
    {
        $respuesta = false;
        $mensaje   = "";
        $areas = array();

        $req_anio = $request['req_anio'];
        $req_mes  = $request['req_mes'];

        $ares = areareas::get(['areid','arenombre']);
        $coas = coacontrolarchivos::join('badbasedatos as bad', 'bad.badid', 'coacontrolarchivos.badid')
                                    ->join('areareas as are', 'are.areid', 'bad.areid')
                                    ->join('estestados as est', 'est.estid', 'coacontrolarchivos.estid')
                                    ->join('fecfechas as fec', 'fec.fecid', 'coacontrolarchivos.fecid')
                                    ->where('fec.fecmes', $req_mes)
                                    ->where('fec.fecano', $req_anio)
                                    ->get([
                                        'coaid',
                                        'are.areid',
                                        'are.arenombre',
                                        'est.estnombre'
                                    ]);

        if (sizeof($coas) > 0) {
            $promedio = 0;
            foreach ($ares as $key => $are) {
                $cantidad = 0;
                $archivos_subidos = 0;
                foreach ($coas as $key => $coa) {
                    if ($are->arenombre == $coa->arenombre) {
                        if ($coa->estnombre == 'Cargado') {
                            $archivos_subidos++;
                        }
                        $cantidad++;
                    }
                }
                $promedio = round($archivos_subidos*100/$cantidad);
                $areas[] = array(
                    "area"  => $are->arenombre,
                    "archivos" => $archivos_subidos,
                    "cantidad" => $cantidad,
                    "promedio" => $promedio
                );
            }
            $respuesta = true;
            $mensaje   = "Se obtuvieron los registros con exito";
        }else{
            $respuesta = false;
            $mensaje   = "Lo siento, no se encontraron registros";

            foreach ($ares as $key => $are) {
                $areas[] = array(
                    "area"     => $are->arenombre,
                    "promedio" => "0"
                );
            }
        }

        return response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            "datos"     => $areas
        ]);
    }
}
