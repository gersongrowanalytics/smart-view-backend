<?php

namespace App\Http\Controllers\Sistema\Promociones\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Promociones\MailPromocionesActivas;
use App\uceusuarioscorreosenviados;
use App\dcedestinatarioscorreosenviados;
use App\usuusuarios;

class EnviarPromocionesActivasController extends Controller
{
    public function EnviarPromocionesActivas(Request $request)
    {
        $respuesta = true;
        $mensaje = 'El correo se envio exitosamente';

        $usutoken   = $request->header('api_token');
        $re_sucursales = $request['sucursales'];
        $re_fecha = $request['fecha'];
        $re_reenviado = $request['reenviado'];

        $usu = usuusuarios::where('usutoken', $usutoken)->first();

        // $correo = "gerson.vilca@grow-analytics.com.pe";
        $correo = "director.creativo@grow-analytics.com.pe";
        // $correo = "jeanmarcoe@gmail.com";

        if($usu){

            // OBTENER FECHAS

            date_default_timezone_set("America/Lima");
            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
            $fecha = date('Y-m-d');
            $hora  = date('H:i:s');

            $anioActualizacion = date("Y", strtotime($fecha));
            $mesActualizacion = $meses[date('n', strtotime($fecha))-1];
            $diaActualizacion = date("j", strtotime($fecha));

            $horaActualizacion = date("H", strtotime($hora));
            $minutoActualizacion = date("i", strtotime($hora));

            $ucen = new uceusuarioscorreosenviados;
            $ucen->usuid = $usu->usuid;
            $ucen->ucetipo        = "Promociones Activas";
            // $ucen->ucenombreexcel = ;
            $ucen->uceasunto      = "Promociones Activas";
            // $ucen->ucecontenido   = ;
            // $ucen->ucecolumnas    = ;
            $ucen->ucesucursales  = json_encode($re_sucursales);
            $ucen->uceanio        = $anioActualizacion;
            $ucen->ucemes         = $mesActualizacion;
            $ucen->ucedia         = $diaActualizacion;
            $ucen->ucehora        = $horaActualizacion.":".$minutoActualizacion;
            $ucen->ucefecha       = $fecha;
            if($ucen->save()){
                $dcen = new dcedestinatarioscorreosenviados;
                $dcen->uceid = $ucen->uceid;
                $dcen->dcedestinatario = $correo;

                if(isset($re_reenviado)){
                    if($re_reenviado == true){
                        $dcen->dceestado = 'R';
                    }else{
                        $dcen->dceestado = 'E';
                    }
                }else{
                    $dcen->dceestado = 'E';
                }
                $dcen->save();
            }
        }

        $txtSucursales = "";

        foreach($re_sucursales as $posicionSucursal => $sucursal){

            if($posicionSucursal == 0){
                $txtSucursales = $sucursal;
            }else{
                $txtSucursales = $txtSucursales.", ".$sucursal;
            }
            
        }


        $data = ['txtSucursales' => $txtSucursales, 're_fecha' => $re_fecha];

        Mail::to($correo)->send(new MailPromocionesActivas($data));

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
        ]);

        return $requestsalida;
    }
}
