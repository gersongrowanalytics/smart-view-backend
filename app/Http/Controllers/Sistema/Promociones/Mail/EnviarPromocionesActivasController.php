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

        $usutoken   = $request->header('api_token');

        $usu = usuusuarios::where('usutoken', $usutoken)->first();

        $correo = "gerson.vilca@grow-analytics.com.pe";
        // $correo = "director.creativo@grow-analytics.com.pe";

        if($usu){

            // OBTENER FECHAS

            date_default_timezone_set("America/Lima");
            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
            $fecha = date('Y-m-d');
            $hora  = date('H:i:s');

            $anioActualizacion = date("Y", strtotime($fecha));
            $mesActualizacion = $meses[date('n', strtotime($fecha))-1];
            $diaActualizacion = date("j", strtotime($fecha));

            $ucen = new uceusuarioscorreosenviados;
            $ucen->usuid = $usu->usuid;
            $ucen->ucetipo        = "";
            // $ucen->ucenombreexcel = ;
            $ucen->uceasunto      = "";
            // $ucen->ucecontenido   = ;
            // $ucen->ucecolumnas    = ;
            // $ucen->ucesucursales  = ;
            $ucen->uceanio        = $anioActualizacion;
            $ucen->ucemes         = $mesActualizacion;
            $ucen->ucedia         = $diaActualizacion;
            $ucen->ucefecha       = $fecha;
            if($ucen->save()){
                $dcen = new dcedestinatarioscorreosenviados;
                $dcen->uceid = $ucen->uceid;
                $dcen->dcedestinatario = $correo;
                $dcen->save();
            }
        }

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

        Mail::to($correo)->send(new MailPromocionesActivas($data));

    }
}
