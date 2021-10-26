<?php

namespace App\Http\Controllers\Sistema\CargarArchivo\ReportePagos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\DB;
use App\usuusuarios;
use App\carcargasarchivos;
use App\fecfechas;
use App\repreconocimientopago;
use App\sucsucursales;

class ReconocimientoPagosController extends Controller
{
    public function CargarReconocimiento(Request $request)
    {

        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d');

        $respuesta      = true;
        $mensaje        = 'La información se cargo correctamente';
        $datos          = [];
        $mensajeDetalle = '';
        $mensajedev     = null;
        $numeroCelda    = 0;
        $usutoken       = $request->header('api_token');
        $archivo        = $_FILES['file']['name'];

        $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['usuid', 'usuusuario']);

        $fichero_subido = '';

        $pkid = 0;
        $log  = array(
            "MENSAJE_DEV" => "",
            "NO_SE_ENCONTRO_FECHA" => [],
            "NO_SE_ENCONTRO_SUCURSAL" => []
        );

        $fecid = 1;
        $exitoSubirExcel = false;

        DB::beginTransaction();
        try{

            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/reconocimientopagos/'.basename($usuusuario->usuid.'-'.$usuusuario->usuusuario.'-'.$fechaActual.'-'.$_FILES['file']['name']);

            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {

                $objPHPExcel    = IOFactory::load($fichero_subido);
                $objPHPExcel->setActiveSheetIndex(0);
                $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();

                for ($i=2; $i <= $numRows ; $i++) {
                    $dia = '01';

                    $gbaZona         = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                    $anioPromocion   = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                    $mesPromocion    = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                    $concepto        = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                    $soldto          = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                    $tipoDocumento   = $objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
                    $fechaDocumento  = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                    $numeroDocumento = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
                    $importeSinIgv   = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
                    $monedaLocal     = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                    $categoria       = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
                    $texto           = $objPHPExcel->getActiveSheet()->getCell('M'.$i)->getCalculatedValue();

                    $fechaDocumento = Date::excelToDateTimeObject($fechaDocumento);
                    $fechaDocumento = json_encode($fechaDocumento);
                    $fechaDocumento = json_decode($fechaDocumento);
                    $fechaDocumento = date("Y-m-d", strtotime($fechaDocumento->date));

                    $fec = fecfechas::where('fecdia', $dia)
                                        ->where('fecmes', 'LIKE', "%".$mesPromocion."%")
                                        ->where('fecano', $anioPromocion)
                                        ->first(['fecid']);
                                        
                    $fecid = 0;
                    if($fec){

                        if($i == 2){
                            // repreconocimientopago::where('fecid', $fec->fecid)->delete();
                            // repreconocimientopago::where('repid', '>', 0)->delete();
                        }

                        $fecid = $fec->fecid;

                        $suc = sucsucursales::where('sucsoldto', $soldto)->first(['sucid']);

                        $sucid = 1;
                        if($suc){

                            if($suc->sucestado != 1){
                                $suc->sucestado = 1;
                                $suc->update();
                            }

                            $sucid = $suc->sucid;


                        }else{
                            $log["NO_SE_ENCONTRO_SUCURSAL"][] = "No se encontro la sucursal: ".$soldto." en la linea: ".$i;
                            $mensaje = 'Lo sentimos, se encontraron algunas observaciones en la columna de soldto';
                            $respuesta = false;
                        }

                        $repn = new repreconocimientopago;
                        $repn->fecid              = $fecid;
                        $repn->sucid              = $sucid;
                        $repn->repsoldto          = $soldto;
                        $repn->sucid              = $sucid;
                        $repn->repconcepto        = $concepto;
                        $repn->reptipodocumento   = $tipoDocumento;
                        $repn->repnumerodocumento = $numeroDocumento;
                        $repn->repfechadocumento  = $fechaDocumento;
                        $repn->repcategoria       = $categoria;
                        $repn->repimporte         = $importeSinIgv;
                        $repn->repmonedalocal     = $monedaLocal;
                        $repn->reptexto           = $texto;
                        $repn->save();

                    }else{
                        $log["NO_SE_ENCONTRO_FECHA"][] = "En la linea: ".$i.", registrado con el mes: ".$mesPromocion." en el año: ".$anioPromocion;
                        $mensaje = 'Lo sentimos, se encontraron algunas observaciones en las columnas de mes y año';
                        $respuesta = false;
                    }
                }

                $exitoSubirExcel = true;

            }else{
                $respuesta       = false;
                $mensaje         = "Lo sentimos, no se guardar el archivo";
                $mensajeDetalle  = "";
                $exitoSubirExcel = false;
            }

            $nuevoCargaArchivo = new carcargasarchivos;
            $nuevoCargaArchivo->tcaid            = 13; // Carga Reconocimiento de Pagos
            $nuevoCargaArchivo->fecid            = $fecid;
            $nuevoCargaArchivo->usuid            = $usuusuario->usuid;
            $nuevoCargaArchivo->carnombrearchivo = $archivo;
            $nuevoCargaArchivo->carubicacion     = $fichero_subido;
            $nuevoCargaArchivo->carexito         = $exitoSubirExcel;
            $nuevoCargaArchivo->carurl           = env('APP_URL').'/Sistema/cargaArchivos/reconocimientopagos/'.$archivo;
            if($nuevoCargaArchivo->save()){
                $pkid = "CAR-".$nuevoCargaArchivo->carid;
            }else{

            }

            DB::commit();

        } catch (Exception $e) {
            $respuesta = false;
            DB::rollBack();
            $mensajedev = $e->getMessage();
            $log["MENSAJE_DEV"] = $mensajedev;
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev,
            "numeroCelda"    => $numeroCelda,
            "logs"           => $log,
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            $usuusuario->usuid,
            null,
            $fichero_subido,
            $requestsalida,
            'CARGAR DATA DE RECONOCIMIENTO DE PAGOS ',
            'IMPORTAR',
            '/cargarArchivo/reconocimiento-pagos', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
    }
}
