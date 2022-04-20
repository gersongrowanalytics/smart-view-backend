<?php

namespace App\Http\Controllers\Sistema\Promociones\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Promociones\MailPromocionesNuevas;

class EnviarPromocionesNuevasController extends Controller
{
    public function EnviarPromocionesNuevas(Request $request)
    {
        $respuesta = true;
        $mensaje = 'El correo se envio exitosamente';

        $correo = "gerson.vilca@grow-analytics.com.pe";
        // $correo = "director.creativo@grow-analytics.com.pe";
        // $correo = "jeanmarcoe@gmail.com";

        $re_sucursales = $request['sucursales'];
        $re_fecha = $request['fecha'];

        $txtSucursales = "";

        foreach($re_sucursales as $posicionSucursal => $sucursal){

            if($posicionSucursal == 0){
                $txtSucursales = $sucursal;
            }else{
                $txtSucursales = $txtSucursales.", ".$sucursal;
            }
            
        }


        $data = ['txtSucursales' => $txtSucursales, 're_fecha' => $re_fecha];

        Mail::to($correo)->send(new MailPromocionesNuevas($data));

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
        ]);

        return $requestsalida;
    }
}
