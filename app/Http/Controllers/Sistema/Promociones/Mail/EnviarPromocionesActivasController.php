<?php

namespace App\Http\Controllers\Sistema\Promociones\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Promociones\MailPromocionesActivas;
use App\uceusuarioscorreosenviados;
use App\dcedestinatarioscorreosenviados;
use App\sucsucursales;
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

        // $correo = "euni_tkm@hotmail.com";
        // $correo = "eunicecallecahuana@gmail.com";
        // $correo = "jeanmarcoe@gmail.com";

        // $correo = "gerson.vilca@grow-analytics.com.pe";
        // $correo = "director.creativo@grow-analytics.com.pe";
        // $correo = "miguel.caballero@grow-analytics.com.pe";
        $correo = "mzorrilla@kcc.com";

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
        $nombre = "";

        $arr_nombreGrupos = [];
        $nombreGrupos = "";

        foreach($re_sucursales as $posicionSucursal => $sucursal){

            // if($posicionSucursal == 0){
            //     $txtSucursales = $sucursal;
            //     $gsu = sucsucursales::where('sucnombre', 'LIKE', "%".$sucursal."%")
            //                     ->join('gsugrupossucursales as gsu', 'gsu.gsuid', 'sucsucursales.gsuid')
            //                     ->first(['gsu.gsunombre']);

            //     if ($gsu) {
            //         $nombre = $gsu->nombre;
            //         if ($gsu->nombre == 'Clientes') {
            //             $nombre = $sucursal;
            //         }
            //     }else{
            //         $nombre = $sucursal;
            //     }

            // }else{
            //     $txtSucursales = $txtSucursales.", ".$sucursal;
            // }


            
            $gsu = sucsucursales::where('sucnombre', 'LIKE', "%".$sucursal."%")
                            ->join('gsugrupossucursales as gsu', 'gsu.gsuid', 'sucsucursales.gsuid')
                            ->first(['gsu.gsunombre']);
                            
            $grupoSeleccionado = "";

            if ($gsu) {
                
                $grupoSeleccionado = $gsu->gsunombre;
                
                if ($gsu->nombre == 'Clientes') {
                    $grupoSeleccionado = $sucursal;    
                }

            }else{
                
                $grupoSeleccionado = $sucursal;
            }

            $encontroGrupo = false;

            foreach($arr_nombreGrupos as $arr_nombreGrupo){
                if($arr_nombreGrupo == $grupoSeleccionado){
                    $encontroGrupo = true;
                }
            }

            if($encontroGrupo == false){
                $arr_nombreGrupos[] = $grupoSeleccionado;
            }
            
        }

        foreach($arr_nombreGrupos as $posArr_NombreGrupos => $arr_nombreGrupo){
            if($posArr_NombreGrupos == 0){
                $nombreGrupos = $arr_nombreGrupo;
            }else{
                $nombreGrupos = $nombreGrupos.", ".$arr_nombreGrupo;
            }
            
        }

        


        $anio = date("Y");

        // $data = ['txtSucursales' => $txtSucursales, 're_fecha' => $re_fecha];
        // $data = ['txtSucursales' => $nombre, 're_fecha' => $re_fecha];
        $data = ['txtSucursales' => $nombreGrupos, 're_fecha' => $re_fecha];

        $asunto = "Kimberly Clark (PE): PROMOCIONES ".$re_fecha." ".$anio." (".$nombreGrupos.")";

        Mail::to($correo)->send(new MailPromocionesActivas($data, $asunto));

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
        ]);

        return $requestsalida;
    }

    // public function EnviarPromocionesActivas(Request $request){
    //     $asunto = "ASUNTO-1";
    //     $data = ['txtSucursales' => "HOLA", 're_fecha' => "2021-10-12"];
    //     Mail::to("jeanmarcoe2@gmail.com")->send(new MailPromocionesActivas($data, $asunto));
    // }
}


