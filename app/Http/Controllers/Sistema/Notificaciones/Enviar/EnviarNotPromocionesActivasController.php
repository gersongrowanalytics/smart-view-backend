<?php

namespace App\Http\Controllers\Sistema\Notificaciones\Enviar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\tnotiposnotificaciones;
use App\ussusuariossucursales;
use App\nusnotificacionesusuarios;
use App\fecfechas;

class EnviarNotPromocionesActivasController extends Controller
{
    public function EnviarNotPromocionesActivas(Request $request)
    {

        $respuesta = true;
        $mensaje   = "Se asignaron las notificaciones correspondientes";

        date_default_timezone_set("America/Lima");
        $fechaActualEspecifica = date('Y-m-d H:i:s');
        $fechaActual = date('Y-m-d');

        $sucursales = $request['sucursales'];
        $mesTxt = $request['mestxt'];

        $fecid = 0;

        $fec = fecfechas::where('fecfecha', $fechaActual)->first();

        if($fec){

            $fecid = $fec->fecid;

        }else{
            $anioActual = date('Y');
            $mesActual  = date('m');

            $fecn = new fecfechas;
            $fecn->fecfecha = $fechaActual;
            $fecn->fecdia   = "00";
            $fecn->fecmes   = "";
            $fecn->fecano   = $anioActual;
            $fecn->fecmesnumero = $mesActual;
            if($fecn->save()){

            }else{
                $respuesta = false;
                // $mensaje   = "Lo sentimos, ocurrio un error al momento de asignar una notificación";
                $mensaje   = "Lo sentimos, la fecha ingresada no se encuentra registrada y no pudo ser guardada";
            }
        }

        if($fecid != 0){
            $tnon = new tnotiposnotificaciones;
            $tnon->tnotipo   = "Promociones Activas";
            $tnon->tnotitulo = "";
            $tnon->tnodescripcion = "Tienes las Promociones del mes de ".$mesTxt." Activas";
            $tnon->tnoimagen = env('APP_URL').'/Sistema/notificaciones/notAjustesVerde.png';
            $tnon->tnolink   = "/promociones";

            if($tnon->save()){

                $usss = ussusuariossucursales::where(function ($query) use($sucursales) {
                                                foreach($sucursales as $sucursal){
                                                    $query->orwhere('sucid', $sucursal);
                                                }
                                            })
                                            ->distinct('usuid')
                                            ->get(['usuid']);

                foreach($usss as $uss){
                    $nusn = new nusnotificacionesusuarios;
                    $nusn->tnoid = $tnon->tnoid;
                    $nusn->usuid = $uss->usuid;
                    $nusn->fecid = null;
                    $nusn->nusfechaenviada = $fechaActual;
                    $nusn->nusleyo = false;
                    $nusn->nusfechaleida = null;
                    $nusn->save();
                }


            }else{

                $respuesta = false;
                $mensaje   = "Lo sentimos, ocurrio un error al momento de asignar un tipo de notificación";

            }
        }

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje
        ]);
        
        return $requestsalida;

    }
}
