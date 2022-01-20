<?php

namespace App\Http\Controllers\Sistema\CargarArchivo\ListaPrecios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\carcargasarchivos;
use App\usuusuarios;
use Illuminate\Support\Facades\DB;

class CargarListaPreciosController extends Controller
{
    public function CargarListaPrecios(Request $request)
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

        $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['usuid', 'usuusuario']);

        $fichero_subido = '';

        $pkid = 0;
        $log  = array(
            "categorias" => [],
            "marcaje" => [],
            "pvp" => []
        );

        $exitoSubirExcel = false;

        DB::beginTransaction();
        try{
            $nombreArchivoGuardado = basename($fechaActual."-".$_FILES['file']['name']);
            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/listaprecios/'.$nombreArchivoGuardado;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {

                $objPHPExcel    = IOFactory::load($fichero_subido);
                $objPHPExcel->setActiveSheetIndex(2);
                $numRows        = $objPHPExcel->setActiveSheetIndex(2)->getHighestRow();
                $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(2)->getHighestColumn();
                

                for ($i=6; $i <= $numRows ; $i++) {
                    $dia = '01';

                    $ex_categoria = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                    $ex_marcaje_mayorista = $objPHPExcel->getActiveSheet()->getCell('S'.$i)->getCalculatedValue();
                    $ex_pvp       = $objPHPExcel->getActiveSheet()->getCell('AG'.$i)->getCalculatedValue();
                    
                    $log["categorias"][] = $ex_categoria;
                    $log["marcaje"][] = $ex_marcaje_mayorista;
                    $log["pvp"][] = $ex_pvp;
   

                }
                
                $exitoSubirExcel = true;

                DB::commit();

            }else{
                $respuesta       = false;
                $mensaje         = "Lo sentimos, no se guardar el archivo";
                $mensajeDetalle  = "";
                $linea           = __LINE__;
                $exitoSubirExcel = false;
            }

            $nuevoCargaArchivo = new carcargasarchivos;
            $nuevoCargaArchivo->tcaid            = 15;
            $nuevoCargaArchivo->fecid            = 69;
            $nuevoCargaArchivo->usuid            = $usuusuario->usuid;
            $nuevoCargaArchivo->carnombrearchivo = $archivo;
            $nuevoCargaArchivo->carubicacion     = $fichero_subido;
            $nuevoCargaArchivo->carexito         = $exitoSubirExcel;
            $nuevoCargaArchivo->carurl           = env('APP_URL').'/Sistema/Sistema/cargaArchivos/listaprecios/'.$nombreArchivoGuardado;
            if($nuevoCargaArchivo->save()){
                $pkid = "CAR-".$nuevoCargaArchivo->carid;
            }else{

            }

            DB::commit();


        } catch (Exception $e) {
            $respuesta = false;
            DB::rollBack();
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
            "logs"           => $log
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            $usuusuario->usuid,
            null,
            $fichero_subido,
            $requestsalida,
            'CARGAR LISTA DE PRECIOS AL SISTEMA ',
            'IMPORTAR',
            '/cargarArchivo/lista-precios', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
    }
}
