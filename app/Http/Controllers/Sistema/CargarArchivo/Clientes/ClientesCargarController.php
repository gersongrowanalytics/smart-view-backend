<?php

namespace App\Http\Controllers\Sistema\CargarArchivo\Clientes;

use App\badbasedatos;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;
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
use App\coacontrolarchivos;
use App\Http\Controllers\Sistema\CargarArchivo\MetRegistrarMovimientoStatusController;
use App\Mail\MailCargaArchivos;
use App\tcatiposcargasarchivos;
use Illuminate\Support\Facades\Mail;
use Exception;

class ClientesCargarController extends Controller
{

    // ACTUALIZA LOS DATOS DE LAS SUCURSALES YA EXISTENTES Y SOLO CANAL TRADICIONAL (DTT)

    public function CargarClientes(Request $request)
    {   
        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d');

        $respuesta      = true;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;
        $numeroCelda    = 0;
        $usutoken       = $request->header('api_token');
        $archivo        = $_FILES['file']['name'];

        $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['usuid', 'usucorreo', 'usuusuario']);
        $fichero_subido = '';

        $pkid = 0;
        $log  = [];

        try{

            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/clientes/'.basename($_FILES['file']['name']);
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
                    $codSoldTo        = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue(); //
                    $soldTo           = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                    $clienteHml       = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue(); //
                    $clienteSucHml    = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                    $localidad        = $objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
                    $codEjecutivo     = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue(); //
                    $ejecutivo        = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
                    $gerenciaZonal    = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
                    $zona             = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue(); //
                    $canal            = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue(); //
                    $gerenciaRegional = $objPHPExcel->getActiveSheet()->getCell('M'.$i)->getCalculatedValue();
                    $gbaRegional      = $objPHPExcel->getActiveSheet()->getCell('N'.$i)->getCalculatedValue(); //
                    $customerGroup    = $objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();
                    $cg2              = $objPHPExcel->getActiveSheet()->getCell('P'.$i)->getCalculatedValue();

                    $suc = sucsucursales::where('sucsoldto', $codSoldTo)->first();
                    if($suc){
                        $suc->sucnombre = $clienteHml;
                        $suc->sucregionalgba = $gbaRegional;
                        
                        if($gbaRegional == "DTT"){
                            
                            $tre = tretiposrebates::where('trenombre', 'like', $codEjecutivo)
                                                    ->first();
                            $treid = 0;
                            if($tre){
                                $treid = $tre->treid;
                            }else{
                                $treid = 0;
                            }
                            
                            $cas = cascanalessucursales::where('casnombre', 'LIKE', $canal)
                                                        ->first();

                            $casid = 0;
                            if($cas){
                                $casid = $cas->casid;
                            }else{
                                $casid = null;
                            }
                            
                            $zon = zonzonas::where('zonnombre', 'LIKE', $zona)
                                            ->first();

                            $zonid = 0;
                            if($zon){
                                $zonid = $zon->zonid;
                            }else{
                                $zonn = new zonzonas;
                                $zonn->zonnombre = $zona;
                                $zonn->zonestado = 1;
                                $zonn->zonregionalgba = $gbaRegional;
                                $zonn->casid = $casid;
                                if($zonn->save()){
                                    $zonid = $zonn->zonid;  
                                }
                            }

                            $suc->zonid = $zonid;
                            $suc->casid = $casid;
                        }

                        $suc->update();
                    }
                    // CREAR UNA NUEVA SUCURSAL
                    else{

                        $sucn = new sucsucursales;
                        $sucn->sucsoldto = $codSoldTo;
                        $sucn->sucnombre = $clienteHml;
                        $sucn->sucregionalgba = $gbaRegional;

                        if($gbaRegional == "DTT"){
                            
                            $tre = tretiposrebates::where('trenombre', 'like', $codEjecutivo)
                                                    ->first();
                            $treid = 0;
                            if($tre){
                                $treid = $tre->treid;
                            }else{
                                $treid = 0;
                            }
                            
                            $cas = cascanalessucursales::where('casnombre', 'LIKE', $canal)
                                                        ->first();

                            $casid = 0;
                            if($cas){
                                $casid = $cas->casid;
                            }else{
                                $casid = null;
                            }
                            
                            $zon = zonzonas::where('zonnombre', 'LIKE', $zona)
                                            ->first();

                            $zonid = 0;
                            if($zon){
                                $zonid = $zon->zonid;
                            }else{
                                $zonn = new zonzonas;
                                $zonn->zonnombre = $zona;
                                $zonn->zonestado = 1;
                                $zonn->zonregionalgba = $gbaRegional;
                                $zonn->casid = $casid;
                                if($zonn->save()){
                                    $zonid = $zonn->zonid;  
                                }
                            }

                            $sucn->treid = $treid;
                            $sucn->gsuid = 1;
                            $sucn->zonid = $zonid;
                            $sucn->casid = $casid;
                        }

                        $sucn->save();

                    }


                }
            }

            if($usuusuario->usuid != 1){
                // $fecid = 165;
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

                    $tca = tcatiposcargasarchivos::where('tcaid',$nuevoCargaArchivo->tcaid)
                                                    ->first(['tcanombre']);

                    $data = ['linkArchivoSubido' => $nuevoCargaArchivo->carurl , 'nombre' => $nuevoCargaArchivo->carnombrearchivo , 'tipo' => $tca->tcanombre, 'usuario' => $usuusuario->usuusuario];
                    Mail::to([
                        'gerson.vilca@grow-analytics.com.pe',
                        'Jose.Cruz@grow-analytics.com.pe',
                        'Frank.Martinez@grow-analytics.com.pe'
                    ])->send(new MailCargaArchivos($data));
                    
                    $bad = badbasedatos::where('badnombre', 'Maestra de Clientes')->first('badid');
                    if ($bad) {
                        $registro_coa = new MetRegistrarMovimientoStatusController;
                        $registro_coa->MetRegistrarMovimientoStatus($bad->badid, $nuevoCargaArchivo->carid);
                    }
                }else{

                }
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

    public function ActualizarZonaClientes(Request $request)
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
                    $ano = '2020';
                    $dia = '01';

                    $codSoldTo = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                    $zona      = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();

                    $zon = zonzonas::where('zonnombre', $zona)->first(['zonid']);

                    $zonid = 0;
                    if($zon){
                        $zonid = $zon->zonid;
                    }else{
                        $nuevazona = new zonzonas;
                        $nuevazona->zonnombre = $zona;
                        if($nuevazona->save()){
                            $zonid = $nuevazona->zonid;
                        }else{

                        }
                    }

                    $cliente = usuusuarios::where('ususoldto', $codSoldTo )->first(['usuid']);

                    if($cliente){
                        $cliente->zonid = $zonid;
                        if($cliente->update()){

                        }
                    }else{
                        $log[] = "No se encontro la sucursal: ".$codSoldTo;
                    }

                    $suc = sucsucursales::where('sucsoldto', $codSoldTo)->first();

                    if($suc){
                        $suc->zonid = $zonid;
                        $suc->update();
                    }else{

                    }


                }

                $nuevoCargaArchivo = new carcargasarchivos;
                $nuevoCargaArchivo->tcaid = 8; // Actualizar Clientes
                $nuevoCargaArchivo->fecid = null;
                $nuevoCargaArchivo->usuid = $usuusuario->usuid;
                $nuevoCargaArchivo->carnombrearchivo = $archivo;
                $nuevoCargaArchivo->carubicacion = $fichero_subido;
                $nuevoCargaArchivo->carexito = true;
                if($nuevoCargaArchivo->save()){
                    $pkid = "CAR-".$nuevoCargaArchivo->carid;
                    $respuesta = true;
                    $mensaje        = 'La actualización de zonas estuvo correcta';
                }else{

                }
            }else{
                $respuesta      = false;
                $mensaje        = 'No se pudo guardar el archivo en el servidor';
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
            'ACTUALIZAR LAS ZONAS DE UN CLIENTE ',
            'IMPORTAR',
            '/cargarArchivo/clientes/acutalizarzonas', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
    }

    public function ActualizarGrupoRebateOctubre(Request $request)
    {
        // $fecid = 8;
        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d H:i:s');

        $respuesta      = true;
        $mensaje        = 'Los clientes se actualizaron correctamente!';
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
        $log  = array(
            "NO_EXISTE_FECHA"             => [],
            "NO_EXISTE_SUCURSAL"          => [],
            "NO_EXISTE_SUCURSAL_ASIGNADA" => [],
            "NUEVO_TRE_CREADO"            => [],
            "TSU_ACTUALIZADO"             => [],
            "NUEVO_CANAL"                 => [],
            "ZONA_ACTUALIZADA"            => [],
            "NUEVA_ZONA"                  => [],
            "ACTUALIZANDO_TRE_SUCURSAL"   => [],
            "ACTUALIZANDO_CAS_SUCURSAL"   => [],
            "ACTUALIZANDO_ZON_SUCURSAL"   => [],
        );

        try{

            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/clientes/actualizar/'.basename($usuusuario->usuid.'-'.$usuusuario->usuusuario.'-'.$fechaActual.'-'.$_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {
                $objPHPExcel    = IOFactory::load($fichero_subido);
                $objPHPExcel->setActiveSheetIndex(0);
                $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();

                for ($i=2; $i <= $numRows ; $i++) {
                    $ano = '2020';
                    $dia = '01';

                    $codSoldTo        = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                    $soldTo           = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                    $codEjecutivo     = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                    $zona             = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                    $canal            = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
                    $anio             = $objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getCalculatedValue();
                    $mes              = $objPHPExcel->getActiveSheet()->getCell('R'.$i)->getCalculatedValue();

                    $fec =  fecfechas::where('fecmes', $mes)
                                    ->where('fecano', $anio)
                                    ->where('fecdia', '01')
                                    ->first();

                    if($fec){
                        $fecid = $fec->fecid;
                        // VERIFICAR SI EXISTE EL USUARIO
                        $usuCliente = usuusuarios::where('tpuid', 2)
                                                ->where('ususoldto', $codSoldTo)
                                                ->first(['usuid']);

                        $clienteusuid = 0;
                        $sucursalClienteId = 0;
                        if($usuCliente){
                            $clienteusuid = $usuCliente->usuid;
                            
                            $sucursalesCliente = ussusuariossucursales::where('usuid', $clienteusuid)->first(['sucid']);
                            if($sucursalesCliente){
                                $sucursalClienteId = $sucursalesCliente->sucid;

                                $tre = tretiposrebates::where('trenombre', $codEjecutivo)->first(['treid']);
                                
                                $treid = 0;
                                if($tre){
                                    $treid = $tre->treid;
                                }else{
                                    $tren = new tretiposrebates;
                                    $tren->trenombre = $codEjecutivo;
                                    if($tren->save()){
                                        $treid = $tren->treid;
                                        $log["NUEVO_TRE_CREADO"][] = "CodigoSoldTo: ".$codSoldTo." - ClienteId: ".$clienteusuid." - TRE: ".$codEjecutivo;
                                    }
                                }

                                $tsus = tsutipospromocionessucursales::where('sucid', $sucursalClienteId)
                                                                    ->where('fecid', $fecid)
                                                                    ->get(['tsuid', 'treid']);

                                                                    
                                foreach($tsus as $tsu){
                                    if($tsu->treid != $treid){
                                        $tsue = tsutipospromocionessucursales::find($tsu->tsuid);
                                        $tsue->treid = $treid;
                                        $tsue->update();
                                        
                                        $log["TSU_ACTUALIZADO"][] = "TSU: ".$tsu->tsuid." CodigoSoldTo: ".$codSoldTo." - ClienteId: ".$clienteusuid." - TRE: ".$codEjecutivo;
                                    }
                                }

                                $cas = cascanalessucursales::where('casnombre', $canal)
                                                            ->first();

                                $casid = 0;
                                if($cas){
                                    $casid = $cas->casid;
                                }else{
                                    $casn = new cascanalessucursales;
                                    $casn->casnombre = $canal;
                                    if($casn->save()){
                                        $casid = $casn->casid;
                                        $log["NUEVO_CANAL"][] = $canal;
                                    }
                                }

                                $zon = zonzonas::where('zonnombre', $zona)
                                                ->first();

                                $zonid = 0;
                                if($zon){
                                    $zonid = $zon->zonid;

                                    if($zon->casid != $casid){
                                        $zon->casid = $casid;
                                        $zon->update();

                                        $log["ZONA_ACTUALIZADA"][] = "ZONA: ".$zona." CANAL ASIGNADO: ".$canal;
                                    }

                                }else{
                                    $zonn = new zonzonas;
                                    $zonn->zonnombre = $zona;
                                    $zonn->casid = $casid;
                                    if($zonn->save()){
                                        $zonn = $zon->zonid;
                                        $log["NUEVA_ZONA"][] = $zona;
                                    }
                                }

                                $suc = sucsucursales::find($sucursalClienteId);
                                if($suc->treid != $treid){
                                    $suc->treid = $treid;

                                    $log["ACTUALIZANDO_TRE_SUCURSAL"][] = "TRE: ".$codEjecutivo." CodigoSoldTo: ".$codSoldTo." - SUCID: ".$sucursalClienteId." SUCURSAL: ".$suc->sucnombre;
                                }

                                if($suc->casid != $casid){
                                    $suc->casid = $casid;
                                    $log["ACTUALIZANDO_CAS_SUCURSAL"][] = "CAS: ".$canal." CodigoSoldTo: ".$codSoldTo." - SUCID: ".$sucursalClienteId." SUCURSAL: ".$suc->sucnombre;
                                }

                                if($suc->zonid != $zonid){
                                    $suc->zonid = $zonid;
                                    $log["ACTUALIZANDO_ZON_SUCURSAL"][] = "ZON: ".$zona." CodigoSoldTo: ".$codSoldTo." - SUCID: ".$sucursalClienteId." SUCURSAL: ".$suc->sucnombre;
                                }

                                $suc->update();

                            }else{
                                $log["NO_EXISTE_SUCURSAL_ASIGNADA"][] = "CodigoSoldTo: ".$codSoldTo." - ClienteId: ".$clienteusuid;
                                $respuesta = false;
                                $mensaje = "Lo sentimos ocurrio un error al momento de actualizar los clientes";
                            }

                        }else{
                            $log["NO_EXISTE_SUCURSAL"][] = $codSoldTo;
                            $respuesta = false;
                            $mensaje = "Lo sentimos el soldto de la linea: ".$i." no existe";
                        }
                    }else{
                        $log["NO_EXISTE_FECHA"][] = $anio." - ".$mes;
                        $respuesta = false;
                        $mensaje = "Lo sentimos la fecha ingresa no existe";
                    }
                }
                
            }else{
                $respuesta = false;
                $mensaje   = "Lo sentimos el archivo no se puede leer";
            }

            $nuevoCargaArchivo = new carcargasarchivos;
            $nuevoCargaArchivo->tcaid = 9; // Carga de actualizacion de grupos rebate para clientes
            $nuevoCargaArchivo->fecid = null;
            $nuevoCargaArchivo->usuid = $usuusuario->usuid;
            $nuevoCargaArchivo->carnombrearchivo = $archivo;
            $nuevoCargaArchivo->carubicacion = $fichero_subido;
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
            "numeroCelda"    => $numeroCelda,
            "log"    => $log,
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            $usuusuario->usuid,
            null,
            $fichero_subido,
            $requestsalida,
            'CARGAR LA ACTUALIZACION CLIENTES ',
            'IMPORTAR',
            '/cargarArchivo/clientes/actualizargruporebate', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;

    }

    public function ActualizarNombres(Request $request)
    {   
        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d');

        $respuesta      = true;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;
        $numeroCelda    = 0;
        $usutoken       = $request->header('api_token');
        $archivo        = $_FILES['file']['name'];

        $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['usuid', 'usucorreo', 'usuusuario']);
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
                        $suc->sucnombre = $clienteHml;
                        $suc->sucregionalgba = $gbaRegional;
                        
                        if($gbaRegional == "UTT"){
                            
                            $zon = zonzonas::where('zonnombre', 'LIKE', '%'.$zona.'%')
                                            ->first();

                            $zonid = 0;
                            if($zon){
                                $zonid = $zon->zonid;
                            }else{
                                $zonid = null;
                            }
                            
                            $cas = cascanalessucursales::where('casnombre', 'LIKE', "%".$canal."%")
                                                        ->first();

                            $casid = 0;
                            if($cas){
                                $casid = $cas->casid;
                            }else{
                                $casid = null;
                            }

                            $suc->zonid = $zonid;
                            $suc->casid = $casid;

                        }

                        $suc->update();
                    }

                    // VERIFICAR SI EXISTE LA PERSONA DEL EJECUTIVO
                    // $perpersonaEjecutivo = perpersonas::where('pernombrecompleto', $ejecutivo)->first(['perid']);
                    // $perEjecutivoid = 0;
                    // if($perpersonaEjecutivo){
                    //     $perEjecutivoid = $perpersonaEjecutivo->perid;
                    // }else{
                    //     $nuevoEjecutivoPersona                               = new perpersonas;
                    //     $nuevoEjecutivoPersona->tdiid                        = 2;
                    //     $nuevoEjecutivoPersona->pernombrecompleto            = $ejecutivo;
                    //     $nuevoEjecutivoPersona->pernumerodocumentoidentidad  = null;
                    //     $nuevoEjecutivoPersona->pernombre                    = null;
                    //     $nuevoEjecutivoPersona->perapellidopaterno           = null;
                    //     $nuevoEjecutivoPersona->perapellidomaterno           = null;
                    //     if($nuevoEjecutivoPersona->save()){
                    //         $perEjecutivoid = $nuevoEjecutivoPersona->perid;
                    //     }else{
        
                    //     }
                    // }

                    // VERIFICAR SI EXISTE EL USUARIO DEL EJECUTIVO
                    // $distribuidor = usuusuarios::where('tpuid', 3)
                    //                             ->where('perid', $perEjecutivoid)
                    //                             ->first(['usuid']);
                    // $usuEjecutivoid = 0;
                    // if($distribuidor){
                    //     $usuEjecutivoid = $distribuidor->usuid;
                    // }else{
                    //     $nuevoEjecutivoUsuario = new usuusuarios;
                    //     $nuevoEjecutivoUsuario->tpuid         = 3;
                    //     $nuevoEjecutivoUsuario->perid         = $perEjecutivoid;
                    //     $nuevoEjecutivoUsuario->ususoldto     = null;
                    //     $nuevoEjecutivoUsuario->usuusuario    = null;
                    //     $nuevoEjecutivoUsuario->usucorreo     = null;
                    //     $nuevoEjecutivoUsuario->usucontrasena = null;
                    //     $nuevoEjecutivoUsuario->usutoken      = Str::random(60);
                    //     if($nuevoEjecutivoUsuario->save()){
                    //         $usuEjecutivoid = $nuevoEjecutivoUsuario->usuid;
                    //     }else{
        
                    //     }
                    // }

                    // VERIFICAR SI EXISTE LA PERSONA
                    // $perpersona = perpersonas::where('pernombrecompleto', $soldTo)->first(['perid']);
                    // $perid = 0;
                    // if($perpersona){
                    //     $perid = $perpersona->perid;
                    // }else{
                    //     $nuevaPersona                               = new perpersonas;
                    //     $nuevaPersona->tdiid                        = 2;
                    //     $nuevaPersona->pernombrecompleto            = $soldTo;
                    //     $nuevaPersona->pernumerodocumentoidentidad  = null;
                    //     $nuevaPersona->pernombre                    = $clienteHml;
                    //     $nuevaPersona->perapellidopaterno           = null;
                    //     $nuevaPersona->perapellidomaterno           = null;
                    //     if($nuevaPersona->save()){
                    //         $perid = $nuevaPersona->perid;
                    //     }else{
        
                    //     }
                    // }

                    // VERIFICAR SI EXISTE EL USUARIO
                    // $usuCliente = usuusuarios::where('tpuid', 2)
                    //                             ->where('perid', $perid)
                    //                             ->where('ususoldto', $codSoldTo)
                    //                             ->first(['usuid']);
                    // $clienteusuid = 0;
                    // $sucursalClienteId = 0;
                    // if($usuCliente){
                    //     $clienteusuid = $usuCliente->usuid;
                        
                    //     $sucursalesCliente = ussusuariossucursales::where('usuid', $clienteusuid)->first(['sucid']);
                    //     if($sucursalesCliente){
                    //         $sucursalClienteId = $sucursalesCliente->sucid;
                    //     }else{
                    //         $nuevaSucursal            = new sucsucursales;
                    //         $nuevaSucursal->sucnombre = $clienteSucHml;
                    //         if($nuevaSucursal->save()){
                    //             $sucursalClienteId = $nuevaSucursal->sucid;

                    //             $sucursalUsuario        = new ussusuariossucursales;
                    //             $sucursalUsuario->usuid = $clienteusuid;
                    //             $sucursalUsuario->suci  = $sucursalClienteId;
                    //             if($sucursalUsuario->save()){

                    //             }else{

                    //             }

                    //         }else{

                    //         }
                    //     }

                    // }else{
                    //     $clienteNuevoUsuario = new usuusuarios;
                    //     $clienteNuevoUsuario->tpuid         = 2; // tipo de usuario (cliente)
                    //     $clienteNuevoUsuario->perid         = $perid;
                    //     $clienteNuevoUsuario->ususoldto     = $codSoldTo;
                    //     $clienteNuevoUsuario->usuusuario    = null;
                    //     $clienteNuevoUsuario->usucorreo     = null;
                    //     $clienteNuevoUsuario->usucontrasena = null;
                    //     $clienteNuevoUsuario->usutoken      = Str::random(60);
                    //     if($clienteNuevoUsuario->save()){
                    //         $clienteusuid             = $clienteNuevoUsuario->usuid;
                    //         $nuevaSucursal            = new sucsucursales;
                    //         $nuevaSucursal->sucnombre = $clienteSucHml;
                    //         if($nuevaSucursal->save()){
                    //             $sucursalClienteId = $nuevaSucursal->sucid;

                    //             $sucursalUsuario = new ussusuariossucursales;
                    //             $sucursalUsuario->usuid = $clienteusuid;
                    //             $sucursalUsuario->sucid = $sucursalClienteId;
                    //             if($sucursalUsuario->save()){

                    //             }else{

                    //             }
                    //         }else{

                    //         }
                    //     }else{

                    //     }
                    // }

                    // $nuevocej = new cejclientesejecutivos;
                    // $nuevocej->cejejecutivo = $usuEjecutivoid;
                    // $nuevocej->cejcliente   = $clienteusuid;
                    // if($nuevocej->save()){

                    // }else{

                    // }



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

                $tca = tcatiposcargasarchivos::where('tcaid',$nuevoCargaArchivo->tcaid)
                                                ->first(['tcanombre']);

                $data = ['linkArchivoSubido' => $nuevoCargaArchivo->carurl , 'nombre' => $nuevoCargaArchivo->carnombrearchivo , 'tipo' => $tca->tcanombre, 'usuario' => $usuusuario->usuusuario];
                Mail::to([
                    'gerson.vilca@grow-analytics.com.pe',
                    'Jose.Cruz@grow-analytics.com.pe',
                    'Frank.Martinez@grow-analytics.com.pe'
                ])->send(new MailCargaArchivos($data));

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
