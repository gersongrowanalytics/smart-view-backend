<?php

namespace App\Http\Controllers\Sistema\ControlArchivos\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\carcargasarchivos;
use App\fecfechas;

class ControlArchivosMostrarController extends Controller
{
    public function MostrarControlArchivos(Request $request)
    {
        $fecha = $request['fecha'];
        $tcaid = $request['tcaid'];

        $fecha = new \DateTime(date("Y-m-d", strtotime($fecha)));
        $fecfecha = fecfechas::where('fecfecha', $fecha)->first(['fecid']);

        $cars = carcargasarchivos::join('usuusuarios as usu', 'usu.usuid', 'carcargasarchivos.usuid')
                                ->join('tcatiposcargasarchivos as tca', 'tca.tcaid', 'carcargasarchivos.tcaid')
                                ->where(function ($query) use($request, $fecfecha) {

                                    if($request['fecha'] != '' && $request['fecha'] != null) {

                                        $query->where('carcargasarchivos.fecid', $fecfecha->fecid);
                                        
                                    }

                                    if($request['tcaid'] != '' && $request['tcaid'] != null) {

                                        $query->where('carcargasarchivos.tcaid', $request['tcaid']);
                                        
                                    }

                                })
                                ->orderBy('carcargasarchivos.created_at', 'DESC')
                                ->get([
                                    'carid',
                                    'tcanombre',
                                    'usuusuario',
                                    'carnombrearchivo',
                                    'carubicacion',
                                    'carexito',
                                    'carcargasarchivos.created_at'
                                    
                                ]);
        $respuesta = true;
        if(sizeof($cars) > 0){
            $mensaje = sizeof($cars)." registros encontrados";
        }else{
            $mensaje = sizeof($cars)." registros encontrados";
            $respuesta = false;
        }

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "datos" => $cars,
        ]);

        return $requestsalida;

    }
}
