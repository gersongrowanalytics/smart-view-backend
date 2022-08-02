<?php

namespace App\Http\Controllers\Sistema\ControlArchivos\Complementos;

use App\badbasedatos;
use App\coacontrolarchivos;
use App\fecfechas;
use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Http\Request;

class MetCrearRegistrosControlArchivosController extends Controller
{
    public function MetCrearRegistrosControlArchivos (Request $request)
    {
        $respuesta = false;
        $mensaje = [];

        $mesAbrev = array("ENE","FEB","MAR","ABR","MAY","JUN","JUL","AGO","SET","OCT","NOV","DIC");
        
        $req_anio = $request['req_anio'];
        $req_mes  = $request['req_mes'];

        date_default_timezone_set("America/Lima");
        $fecha = "01-".$req_mes."-".$req_anio;
        $fechaDate = new DateTime($fecha);

        $fecn = new fecfechas();
        $fecn->fecfecha     = $fechaDate;
        $fecn->fecdia       = '01';
        $fecn->fecano       = $req_anio;
        $fecn->fecmes       = $mesAbrev[$req_mes-1];
        $fecn->fecmesnumero = $req_mes;

        if ($fecn->save()) {
            
            $bads = badbasedatos::get(['badid', 'badnombre', 'areid', 'usuid']);

            if (sizeof($bads) > 0) {
                foreach ($bads as $key => $bad) {

                    $coan = new coacontrolarchivos();
                    $coan->usuid = $bad['usuid'];
                    $coan->fecid = $fecn->fecid;
                    $coan->estid = 4;
                    $coan->badid = $bad['badid'];
                    $coan->coafechacaducidad = date("Y-m-t", strtotime($fecha));
                    if ($coan->save()) {
                        $respuesta = true;
                        $mensaje   = ["Los registros del coacontrolarchivos fueron creados exitosamente"];
                    }else{
                        break;
                        $respuesta = false;
                        $mensaje   = ["Lo siento, error al momento de crear un registro del coacontrolarchivos"];
                    }
                }
            } 
        }else{
            $respuesta = false;
            $mensaje   = ["Lo siento, error al momento de crear un registro de la fecha"];
        }

        return response()->json([
            'respuesta' => $respuesta,
            'mensaje'   => $mensaje
        ]);
    }
}
