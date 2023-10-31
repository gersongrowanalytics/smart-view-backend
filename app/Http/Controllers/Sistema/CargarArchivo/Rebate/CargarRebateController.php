<?php

namespace App\Http\Controllers\Sistema\CargarArchivo\Rebate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\usuusuarios;
use App\tuptiposusuariospermisos;
use App\fecfechas;
use App\carcargasarchivos;
use App\tcatiposcargasarchivos;
use App\Http\Controllers\AuditoriaController;
use Illuminate\Support\Facades\Mail;

class CargarRebateController extends Controller
{
    public function CargarRebate(Request $request)
    {

        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d');

        $respuesta      = true;
        $mensaje        = '';
        $datos          = [];
        $skusNoExisten  = [0];
        $soldtosNoExisten  = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;
        $numeroCelda    = 0;
        $usutoken       = $request->header('api_token');
        $archivo        = $_FILES['file']['name'];

        $fichero_subido = '';

        $pkid = 0;
        $log  = [];
        $observaciones  = [];

        $cargarData = false;
        
        $usuusuario = usuusuarios::join('tputiposusuarios as tpu', 'tpu.tpuid', 'usuusuarios.tpuid')
                                ->where('usuusuarios.usutoken', $usutoken)
                                ->first([
                                    'usuusuarios.usuid', 
                                    'usuusuarios.tpuid',
                                    'usuusuarios.usucorreo', 
                                    'usuusuarios.usuusuario',
                                    'tpu.tpuprivilegio'
                                ]);

        if($usuusuario->tpuprivilegio == "todo"){
            $cargarData = true;
        }else{
            $tup = tuptiposusuariospermisos::join('pempermisos as pem', 'pem.pemid', 'tuptiposusuariospermisos.pemid')
                                            ->where('tuptiposusuariospermisos.tpuid', $usuusuario->tpuid)
                                            ->where('pem.pemslug', "cargar.data.servidor")
                                            ->first([
                                                'tuptiposusuariospermisos.tpuid'
                                            ]);

            if($tup){
                $cargarData = true;
            }else{
                $cargarData = false;
            }
        }


        $fichero_subido = base_path().'/public/Sistema/cargaArchivos/rebate/'.basename($_FILES['file']['name']);

        if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {

            if($respuesta == true){
                date_default_timezone_set("America/Lima");
                $fechaActual = date('Y-m-d H:i:s');

                $anioActual = date('Y');
                $mesActual  = date('m');
                $diaActual  = '01';

                $fecfecha = fecfechas::where('fecdia', $diaActual)
                                    ->where('fecmesnumero', $mesActual)
                                    ->where('fecano', $anioActual)
                                    ->first(['fecid']);

                $fecid = 0;
                if($fecfecha){
                    $fecid = $fecfecha->fecid;
                }else{
                    $mesTxt = "";

                    if($mesActual == "01"){
                        $mesTxt = "ENE";
                    }else if($mesActual == "02"){
                        $mesTxt = "FEB";
                    }else if($mesActual == "03"){
                        $mesTxt = "MAR";
                    }else if($mesActual == "04"){
                        $mesTxt = "ABR";
                    }else if($mesActual == "05"){
                        $mesTxt = "MAY";
                    }else if($mesActual == "06"){
                        $mesTxt = "JUN";
                    }else if($mesActual == "07"){
                        $mesTxt = "JUL";
                    }else if($mesActual == "08"){
                        $mesTxt = "AGO";
                    }else if($mesActual == "09"){
                        $mesTxt = "SET";
                    }else if($mesActual == "10"){
                        $mesTxt = "OCT";
                    }else if($mesActual == "11"){
                        $mesTxt = "NOV";
                    }else if($mesActual == "12"){
                        $mesTxt = "DIC";
                    }

                    $nuevaFecha = new fecfechas;
                    $nuevaFecha->fecfecha     = new \DateTime(date("Y-m-d", strtotime($anioActual.'-'.$mesActual.'-'.$diaActual)));
                    $nuevaFecha->fecdia       = $diaActual;
                    $nuevaFecha->fecmes       = $mesTxt;
                    $nuevaFecha->fecmesnumero = $mesActual;
                    $nuevaFecha->fecano       = $anioActual;
                    if($nuevaFecha->save()){
                        $fecid = $nuevaFecha->fecid;
                    }else{
    
                    }
                }

                $nuevoCargaArchivo = new carcargasarchivos;
                $nuevoCargaArchivo->tcaid             = 14;
                $nuevoCargaArchivo->fecid             = $fecid;
                $nuevoCargaArchivo->usuid             = $usuusuario->usuid;
                $nuevoCargaArchivo->carnombrearchivo  = $archivo;
                $nuevoCargaArchivo->carubicacion      = $fichero_subido;
                $nuevoCargaArchivo->carexito          = $cargarData;
                $nuevoCargaArchivo->carurl            = env('APP_URL').'/Sistema/cargaArchivos/rebate/'.$archivo;
                if($nuevoCargaArchivo->save()){
                    $pkid = "CAR-".$nuevoCargaArchivo->carid;
                    $tca = tcatiposcargasarchivos::where('tcaid',$nuevoCargaArchivo->tcaid)
                                        ->first(['tcanombre']);

                    $data = [
                        'linkArchivoSubido' => $nuevoCargaArchivo->carurl , 
                        'nombre' => $nuevoCargaArchivo->carnombrearchivo , 
                        'tipo' => $tca->tcanombre, 
                        'usuario' => $usuusuario->usuusuario
                    ];

                    Mail::to([
                        'gerson.vilca@grow-analytics.com.pe',
                        'Jose.Cruz@grow-analytics.com.pe',
                        'Frank.Martinez@grow-analytics.com.pe'
                    ])->send(new MailCargaArchivos($data));

                }else{

                }

                // DB::commit();
            }else{
                // DB::rollBack();
            }

        }else{
            $respuesta  = false;
            $mensaje    = "No se pudo guardar el excel en el sistema";
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
            "linea"          => $linea,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev,
            "numeroCelda"    => $numeroCelda,
            "skusNoExisten"  => $skusNoExisten,
            "soldtosNoExisten" => $soldtosNoExisten,
            "observaciones" => $observaciones
        ]);

        if($respuesta == true){
            $AuditoriaController = new AuditoriaController;
            $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
                $usutoken,
                $usuusuario->usuid,
                null,
                $fichero_subido,
                $requestsalida,
                'CARGAR DATA DE OBJETIVOS SELL OUT',
                'IMPORTAR',
                '/cargarArchivo/ventas/obejtivossellout', //ruta
                $pkid,
                $log
            );

            if($registrarAuditoria == true){

            }else{
                
            }
        }
        
        return $requestsalida;

    }
}
