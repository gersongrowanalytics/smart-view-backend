<?php

namespace App\Http\Controllers\Sistema\CargarArchivo\Sucursales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\fecfechas;
use App\tputiposusuarios;
use App\perpersonas;
use App\usuusuarios;
use App\ussusuariossucursales;
use App\sucsucursales;
use App\carcargasarchivos;
use App\cejclientesejecutivos;
use App\zonzonas;
use App\tsutipospromocionessucursales;
use App\tretiposrebates;
use App\trrtiposrebatesrebates;
use App\scasucursalescategorias;
use App\cascanalessucursales;

class ActualizarSucursalesController extends Controller
{
    public function ActualizarSucursales(Request $request)
    {
        $logs = array(
            "ERRORES"   => "",
            "NO_SE_SUBIO_ARCHIVO"   => "",
            "NO_EXISTE_CANAL"   => [],
            "NO_EXISTE_ZONA"    => [],
            "NUEVAS_SUCURSALES" => [],
            "NUEVO_TRE" => [],
            "EDITAR_SUCNOMBRE"  => [],
            "EDITAR_TREID"      => [],
            "EDITAR_ZONID"      => [],
            "EDITAR_CASID"      => []
        );

        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d H:i:s');
        $mensajedev = "";
        $respuesta      = true;
        $mensaje        = '';
        $datos          = [];
        $usutoken = $request->header('api_token');
        $archivo  = $_FILES['file']['name'];

        $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['usuid', 'usuusuario']);
        $fichero_subido = '';

        try{

            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/clientes/'.basename($usuusuario->usuid.'-'.$usuusuario->usuusuario.'-'.$fechaActual.'-'.$_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {

                $objPHPExcel    = IOFactory::load($fichero_subido);
                $objPHPExcel->setActiveSheetIndex(0);
                $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();

                for ($i=2; $i <= $numRows ; $i++) {
                    $soldto        = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                    // $sucursal      = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                    $sucursal      = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                    $customerGroup = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                    $zona          = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                    $canal         = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();

                    $cas = cascanalessucursales::where('casnombre', $canal)->first();

                    if($cas){
                        $zon = zonzonas::where('zonnombre', $zona)->first();
                        if($zon){

                            $tre = tretiposrebates::where('trenombre', $customerGroup)->first();
                            $treid = 0;
                            $trenombre = "";
                            if($tre){
                                $treid = $tre->treid;
                                $trenombre = $tre->trenombre;
                            }else{
                                $tren = new tretiposrebates;
                                $tren->trenombre = $customerGroup;
                                $tren->save();

                                $treid = $tren->treid;

                                $logs["NUEVO_TRE"][] = $treid."  ".$tren->trenombre;
                            }

                            $suc = sucsucursales::join('tretiposrebates as tre', 'tre.treid', 'sucsucursales.treid' )
                                                 ->where('sucsoldto', $soldto)
                                                 ->first([
                                                     'sucid',
                                                     'sucnombre',
                                                     'zonid',
                                                     'casid',
                                                     'tre.treid',
                                                     'trenombre',
                                                 ]);
                            if($suc){
                                $sucnombre = $suc->sucnombre;
                                if($suc->sucnombre != $sucursal){
                                    $logs["EDITAR_SUCNOMBRE"][] = "SOLDTO: ".$soldto." SUCNOMBRE: ".$sucnombre." ANTERIOR: ".$suc->sucnombre." NUEVA: ".$sucursal;
                                    $suc->sucnombre = $sucursal;
                                }

                                if($suc->treid != $treid){
                                    $logs["EDITAR_TREID"][] = "SOLDTO: ".$soldto." SUCNOMBRE: ".$sucnombre." ANTERIOR: ".$suc->trenombre." (".$suc->treid.") "." NUEVA: ".$trenombre." (".$treid.")";
                                    $suc->treid = $treid;
                                }

                                if($suc->zonid != $zon->zonid){
                                    $logs["EDITAR_ZONID"][] = "SOLDTO: ".$soldto." SUCNOMBRE: ".$sucnombre." ANTERIOR: ".$suc->zonid." NUEVA: ".$zon->zonid;
                                    $suc->zonid = $zon->zonid;
                                }

                                if($suc->casid != $cas->casid){
                                    $logs["EDITAR_CASID"][] = "SOLDTO: ".$soldto." SUCNOMBRE: ".$sucnombre." ANTERIOR: ".$suc->casid." NUEVA: ".$cas->casid;
                                    $suc->casid = $cas->casid;
                                }
                                
                                $suc->update();
                            }else{
                                $sucn = new sucsucursales;
                                $sucn->sucsoldto = $soldto;
                                $sucn->sucnombre = $sucursal;
                                $sucn->treid = $treid;
                                $sucn->zonid = $zon->zonid;
                                $sucn->casid = $cas->casid;
                                $sucn->save();

                                $logs["NUEVAS_SUCURSALES"][] = "SUCID: ".$sucn->sucid." SOLDTO: ".$soldto." SUCURSAL: ".$sucursal;
                            }

                        }else{
                            $logs["NO_EXISTE_ZONA"][] = $zona;
                            $respuesta = false;
                            $mensaje = "NO SE ENCONTRO UNA ZONA";
                        }
                    }else{
                        $logs["NO_EXISTE_CANAL"][] = $canal;
                        $respuesta = false;
                        $mensaje = "NO SE ENCONTRO UN CANAL";
                    }
                }

            }else{
                $logs["NO_SE_SUBIO_ARCHIVO"] = "No se pudo subir el archivo";
                $respuesta = false;
                $mensaje = "NO SE PUDO GUARDAR EL FILE";
            }

        } catch (Exception $e) {
            $logs["ERRORES"] = $e->getMessage();
            $mensajedev = $e->getMessage();
            $respuesta = false;
            $mensaje = "ERROR EN EL SERVIDOR";
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
            "mensajedev"     => $mensajedev,
            "logs" => $logs
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            $usuusuario->usuid,
            null,
            $fichero_subido,
            $requestsalida,
            'CARGAR DATA DE SUCURSALES AL SISTEMA ',
            'IMPORTAR',
            '/cargarArchivo/sucursales', //ruta
            0,
            $logs
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;


    }

    public function ActualizarNombres(Request $request)
    {   
        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d H:i:s');

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;
        $numeroCelda    = 0;
        $usutoken       = $request->header('api_token');
        $archivo        = $_FILES['file']['name'];

        $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['usuid', 'usuusuario']);
        $fichero_subido = '';

        $pkid = 0;
        $log  = [];

        try{

            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/clientes/'.basename($usuusuario->usuid.'-'.$usuusuario->usuusuario.'-'.$fechaActual.'-'.$_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {
                $objPHPExcel    = IOFactory::load($fichero_subido);
                $objPHPExcel->setActiveSheetIndex(0);
                $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();

                for ($i=2; $i <= $numRows ; $i++) {
                    $ano = '2021';
                    $dia = '01';

                    $codShipTo        = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                    $shipTo           = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                    $codSoldTo        = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                    $soldTo           = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                    $clienteHml       = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                    $clienteSucHml    = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                    $localidad        = $objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
                    $codEjecutivo     = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                    $ejecutivo        = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
                    $gerenciaZonal    = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
                    $zona             = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                    $canal            = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
                    $gerenciaRegional = $objPHPExcel->getActiveSheet()->getCell('M'.$i)->getCalculatedValue();
                    $gbaRegional      = $objPHPExcel->getActiveSheet()->getCell('N'.$i)->getCalculatedValue();
                    $customerGroup    = $objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();
                    $cg2              = $objPHPExcel->getActiveSheet()->getCell('P'.$i)->getCalculatedValue();

                    $suc = sucsucursales::where('sucsoldto', $codSoldTo)->first();
                    if($suc){
                        if($clienteHml){
                            $suc->sucnombre = $clienteHml;
                            $suc->update();
                        }
                    }




                }
            }

            $nuevoCargaArchivo = new carcargasarchivos;
            $nuevoCargaArchivo->tcaid            = 6; // Carga de Clientes
            $nuevoCargaArchivo->fecid            = null;
            $nuevoCargaArchivo->usuid            = $usuusuario->usuid;
            $nuevoCargaArchivo->carnombrearchivo = $archivo;
            $nuevoCargaArchivo->carubicacion     = $fichero_subido;
            $nuevoCargaArchivo->carurl           = env('APP_URL').'/public/Sistema/cargaArchivos/clientes/'.$archivo;
            $nuevoCargaArchivo->carexito = true;
            if($nuevoCargaArchivo->save()){
                $pkid = "CAR-".$nuevoCargaArchivo->carid;
            }else{

            }

        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
            $log[]      = $mensajedev;
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
            "linea"          => $linea,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev,
            "numeroCelda"    => $numeroCelda
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            $usuusuario->usuid,
            null,
            $fichero_subido,
            $requestsalida,
            'CARGAR DATA DE CLIENTES AL SISTEMA ',
            'IMPORTAR',
            '/cargarArchivo/clientes', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;

    }
}
