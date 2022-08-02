<?php

namespace App\Http\Controllers\Sistema\Status;

use App\areareas;
use App\badbasedatos;
use App\coacontrolarchivos;
use App\Http\Controllers\Controller;
use App\Mail\MailInformarStatus;
use App\Mail\MailInformarStatusV2;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
// use Symfony\Component\HttpFoundation\Cookie;
use function PHPSTORM_META\map;

class MetObtenerStatusController extends Controller
{
    
    public function MetObtenerStatus(Request $request)
    {
        // header_remove("X-Powered-By");
        // header_remove("Host");
        $respuesta = false;
        $mensaje   = "";
        $datos     = [];
        $cuadros   = [];

        $req_anio = $request['req_anio'];
        $req_mes  = $request['req_mes'];

        $coas = coacontrolarchivos::leftJoin('carcargasarchivos as car', 'car.carid', 'coacontrolarchivos.carid')
                                    ->leftJoin('usuusuarios as usucar', 'usucar.usuid', 'car.usuid')
                                    ->leftJoin('perpersonas as percar', 'percar.perid', 'usucar.perid')
                                    ->join('usuusuarios as usu', 'usu.usuid', 'coacontrolarchivos.usuid')
                                    ->join('perpersonas as perresp', 'perresp.perid', 'usu.perid')
                                    ->join('estestados as est' ,'est.estid', 'coacontrolarchivos.estid')
                                    ->join('badbasedatos as bad', 'bad.badid', 'coacontrolarchivos.badid')
                                    ->join('areareas as are', 'are.areid', 'bad.areid')
                                    ->join('fecfechas as fec', 'fec.fecid', 'coacontrolarchivos.fecid')
                                    ->where('fec.fecmes', $req_mes)
                                    ->where('fec.fecano', $req_anio)
                                    ->orderby('coaid')
                                    ->get([
                                        'coaid',
                                        'bad.badnombre',
                                        'are.areid',
                                        'are.arenombre',
                                        'car.carid',
                                        'car.carnombrearchivo',  
                                        'car.carurl',
                                        'usu.usuid as usuidresponsable',  
                                        'usucar.usuid as usuidsubida',
                                        'percar.pernombrecompleto as pernombrecompletosubida', 
                                        'est.estid',
                                        'est.estnombre',
                                        'coafechacaducidad',
                                        'car.created_at as fechaCar',
                                        'perresp.pernombrecompleto as pernombrecompletoresponsable', 
                                    ]);

        if (sizeof($coas) > 0) {
            $respuesta = true;
            $mensaje   = "Se obtuvieron los registros con exito";


                foreach ($coas as $posicioncoa => $coa) {
                    $coas[$posicioncoa]['key'] = $posicioncoa;
                    $coas[$posicioncoa]['item'] = $posicioncoa+1;
    
                    // CALCULAR DIAS DE RETRASO
    
                    if(isset($coas[$posicioncoa]['fechaCar'])){
                        
                        $fechaActual = date('Y-m-d');
    
                        if(isset( $coas[$posicioncoa]['coafechacaducidad'] )){
                            $date1 = date("Y-m-d", strtotime($coas[$posicioncoa]['coafechacaducidad']));
                            $date1 = new DateTime($date1);
                        }else{
                            $date1 = new DateTime($fechaActual);
                        } 
                            
                        $fecha_carga_real = date("Y-m-d", strtotime($coas[$posicioncoa]['fechaCar']));
    
                        $date2 = new DateTime($fecha_carga_real);
    
                        if($date1 == $date2){
                            $diaRetraso = "0";
                        }else{
                            if($date1 < $date2){
                                $diff = $date1->diff($date2);
    
                                if($diff->days > 0){
                                    $diaRetraso = $diff->days;
                                }else{
                                    $diaRetraso = "0";
                                }
    
                            }else{
                                $diaRetraso = "0";
                            }
                        }
    
                    }else{
                        $fechaActual = date('Y-m-d');
    
                        if(isset( $coas[$posicioncoa]['coafechacaducidad'] )){
                            $date1 = date("Y-m-d", strtotime($coas[$posicioncoa]['coafechacaducidad']));
                            $date1 = new DateTime($date1);
                        }else{
                            $date1 = new DateTime($fechaActual);
                        }

                        $date2 = new DateTime($fechaActual);

                        if($date1 == $date2){
                            $diaRetraso = "0";
                        }else{
                            if($date1 < $date2){
                                $diff = $date1->diff($date2);
    
                                if($diff->days > 0){
                                    $diaRetraso = $diff->days;
                                }else{
                                    $diaRetraso = "0";
                                }
    
                            }else{
                                $diaRetraso = "0";
                            }
                        }
                    }
                    $coas[$posicioncoa]['diasretraso'] = $diaRetraso;

                    //ALMACENANDO LOS DATOS EN UN ARRAY PARA EL ENVIO DE CORREO
                    $coa['fechaCar'] = ($this->MetFormatearFecha($coa->fechaCar) == '1 Ene 1970') ? '': $this->MetFormatearFecha($coa->fechaCar) ;
                    $coa['coafechacaducidad'] = $this->MetFormatearFecha($coa->coafechacaducidad);
                    $datos[] = [
                        "areaid"       => $coa->areid,
                        "area"         => $coa->arenombre,
                        "base_datos"   => $coa->badnombre,
                        "responsable"  => $coa->pernombrecompletoresponsable,
                        "usuario"      => $coa->pernombrecompletosubida,
                        "deadline"     => $coa->coafechacaducidad,
                        "fecha_carga"  => $coa->fechaCar,
                        "dias_retraso" => $coa->diasretraso,
                        "status"       => $coa->estnombre

                    ];
    
                }

            //OBTENER LAS AREAS 

            $areas = array();
            $promedio = 0;
            $ares = areareas::get(['areid','arenombre']);
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
        
            if (sizeof($areas) > 0) {
                foreach ($areas as $key => $area) {
                    $cuadros[] = [
                        "arenombre"     => $area['area'],
                        "areporcentaje" => $area['promedio'] 
                    ];
                }
                $fechas = ["25.07.2022"];
                foreach ($fechas as $key => $fecha) {
                    Mail::to(['gerson.vilca@grow-analytics.com.pe'])->send(new MailInformarStatus($datos, $cuadros, $fecha));
                }
            }
        }else{
            $respuesta = false;
            $mensaje   = "Lo siento, no se encontraron registros";

            $bads = badbasedatos::join('areareas as are', 'are.areid', 'badbasedatos.areid')
                                    ->get([
                                        'are.areid',
                                        'are.arenombre',
                                        'badid', 
                                        'badnombre'
                                    ]);

            if (sizeof($bads) > 0) {
                foreach ($bads as $key => $bad) {
                    $coas[] = array(
                        "areid"                        => $bad->areid,
                        "arenombre"                    => $bad->arenombre,
                        "carurl"                       => "",
                        "badnombre"                    => $bad->badnombre,
                        "pernombrecompletoresponsable" => "",
                        "pernombrecompletosubida"      => "",
                        "coafechacaducidad"            => "",
                        "fechaCar"                     => "",
                        "diasretraso"                  => "",
                        "estnombre"                    => "No Cargado"
                    );
                }
            }
        }

        return response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            "datos"     => $coas
        ]);

    }

    public function MetFormatearFecha($fecha)
    {
        $meses = array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Set","Oct","Nov","Dic");
        $anioActualizacion = date("Y", strtotime($fecha));
        $mesActualizacion = $meses[date('n', strtotime($fecha))-1];
        $diaActualizacion = date("j", strtotime($fecha));
        $fechaFormateada = $diaActualizacion." ".$mesActualizacion." ".$anioActualizacion;
        return $fechaFormateada;
    }

    public function MetObtenerStatusV2 ()
    {
        Mail::to(['marco.espinoza@grow-analytics.com.pe','director.creativo@grow-analytics.com.pe'])->send(new MailInformarStatusV2());
        // return view('CorreoInformarStatusV2');
    } 
}
