<?php

namespace App\Http\Controllers\Sistema\CargarArchivo\Productos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\carcargasarchivos;
use App\fecfechas;
use App\proproductos;
use App\catcategorias;
use App\usuusuarios;
use Illuminate\Support\Facades\DB;

class ProductosCargarController extends Controller
{
    public function CargarProductos(Request $request)
    {
        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d H:i:s');

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
            "NUEVO_PRODUCTO"  => [],
            "EDITAR_PRODUCTO" => [],
            "CATEGORIA_NO_ENCONTRADA" => [],
        );

        $exitoSubirExcel = false;

        DB::beginTransaction();
        try{
            
            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/productos/'.basename($usuusuario->usuid.'-'.$usuusuario->usuusuario.'-'.$fechaActual.'-'.$_FILES['file']['name']);

            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {

                $objPHPExcel    = IOFactory::load($fichero_subido);
                $objPHPExcel->setActiveSheetIndex(0);
                $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
                
                for ($i=2; $i <= $numRows ; $i++) {
                    // $ano = '2021';
                    $dia = '01';

                    $codigoMaterial = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                    $material       = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                    $categoria      = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                    $subCategoria   = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                    $formato        = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                    $ano            = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                    $mes            = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
                    // $mes = 'FEB';

                    if($material != null){
                        $fecfecha = fecfechas::where('fecdia', $dia)
                                        ->where('fecmes', $mes)
                                        ->where('fecano', $ano)
                                        ->first(['fecid']);
                                        
                        $fecid = 0;
                        if($fecfecha){
                            $fecid = $fecfecha->fecid;
                        }else{
                            $nuevaFecha = new fecfechas;
                            $nuevaFecha->fecfecha = new \DateTime(date("Y-m-d", strtotime($ano.'-'.$mes.'-'.$dia)));
                            $nuevaFecha->fecdia   = $dia;
                            $nuevaFecha->fecmes   = $mes;
                            $nuevaFecha->fecano   = $ano;
                            if($nuevaFecha->save()){
                                $fecid = $nuevaFecha->fecid;
                            }else{
            
                            }
                        }
                        
                        $categoriaEncontrada = true;

                        $categoriaid = 0;
                        if($categoria == 'Family'){
                            $categoriaid = 1;
                        }else if($categoria == 'Wipes'){
                            $categoriaid = 4; 
                        }else if($categoria == 'Adult'){
                            $categoriaid = 3;
                        }else if($categoria == 'Fem'){
                            $categoriaid = 5;
                        }else if($categoria == 'Infant + Child'){
                            $categoriaid = 2;
                        }else{
                            $categoriaEncontrada = false;
                        }

                        if($categoriaEncontrada == true){
                            $pro = proproductos::where('prosku', $codigoMaterial)
                                                ->first(['proid', 'catid']);

                            if($pro){
                                $anterior = $pro->catid;
                                if($pro->catid != $categoriaid){
                                    $pro->catid = $categoriaid;
                                    $pro->update();
                                    $log["EDITAR_PRODUCTO"][] = $anterior." - ".$codigoMaterial;
                                }
                            }else{
                                $nuevopro = new proproductos;
                                $nuevopro->catid     = $categoriaid;
                                $nuevopro->prosku    = $codigoMaterial;
                                $nuevopro->pronombre = $material;
                                $nuevopro->proimagen = env('APP_URL').'/Sistema/abs/img/nohay.png';
                                if($nuevopro->save()){
                                    $log["NUEVO_PRODUCTO"][] = $codigoMaterial;
                                }else{

                                }
                            }
                        }else{
                            DB::rollBack();
                            $log["CATEGORIA_NO_ENCONTRADA"][] = "En la linea: ".$i." categoria: ".$categoria." sku: ".$codigoMaterial;
                            $respuesta = false;
                        }
                    }else{
                        $respuesta = false;
                    }
                }

                
                $exitoSubirExcel = true;

            }else{
                $respuesta       = false;
                $mensaje         = "Lo sentimos, no se guardar el archivo";
                $mensajeDetalle  = "";
                $linea           = __LINE__;
                $exitoSubirExcel = false;
            }

            $nuevoCargaArchivo = new carcargasarchivos;
            $nuevoCargaArchivo->tcaid            = 7;
            $nuevoCargaArchivo->fecid            = $fecid;
            $nuevoCargaArchivo->usuid            = $usuusuario->usuid;
            $nuevoCargaArchivo->carnombrearchivo = $archivo;
            $nuevoCargaArchivo->carubicacion     = $fichero_subido;
            $nuevoCargaArchivo->carexito         = $exitoSubirExcel;
            $nuevoCargaArchivo->carurl           = env('APP_URL').'/Sistema/cargaArchivos/productos/'.$archivo;
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
            'CARGAR DATA DE PRODUCTOS AL SISTEMA ',
            'IMPORTAR',
            '/cargarArchivo/productos', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
    }

    // CREADA PARA LA MAESTRA DE PRODUCTOS DE MILAGROS

    public function ActualiazarCargarProductos(Request $request)
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
            "NUEVO_PRODUCTO"  => [],
            "EDITAR_PRODUCTO" => [],
            "CATEGORIA_NO_ENCONTRADA" => [],
        );

        $exitoSubirExcel = false;

        DB::beginTransaction();
        try{
            
            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/productos/'.basename($usuusuario->usuid.'-'.$usuusuario->usuusuario.'-'.$fechaActual.'-'.$_FILES['file']['name']);

            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {

                $objPHPExcel    = IOFactory::load($fichero_subido);
                $objPHPExcel->setActiveSheetIndex(0);
                $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
                
                for ($i=2; $i <= $numRows ; $i++) {
                    // $ano = '2021';
                    $dia = '01';

                    $codigoMaterial = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                    $material       = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                    $categoria      = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                    $subCategoria   = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                    $formato        = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                    // $ano            = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                    // $mes            = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
                    // $mes = 'FEB';

                    if($material != null){
                        $fecfecha = fecfechas::where('fecfecha', $fechaActual)->first();
                                        
                        $fecid = 0;
                        if($fecfecha){
                            $fecid = $fecfecha->fecid;
                        }else{
                            // $nuevaFecha = new fecfechas;
                            // $nuevaFecha->fecfecha = new \DateTime(date("Y-m-d", strtotime($ano.'-'.$mes.'-'.$dia)));
                            // $nuevaFecha->fecdia   = $dia;
                            // $nuevaFecha->fecmes   = $mes;
                            // $nuevaFecha->fecano   = $ano;
                            // if($nuevaFecha->save()){
                            //     $fecid = $nuevaFecha->fecid;
                            // }else{
            
                            // }
                        }
                        
                        $categoriaEncontrada = true;

                        $categoriaid = 0;
                        if($categoria == 'Family'){
                            $categoriaid = 1;
                        }else if($categoria == 'Wipes'){
                            $categoriaid = 4; 
                        }else if($categoria == 'Adult'){
                            $categoriaid = 3;
                        }else if($categoria == 'Fem'){
                            $categoriaid = 5;
                        }else if($categoria == 'Infant + Child'){
                            $categoriaid = 2;
                        }else{
                            $categoriaEncontrada = false;
                        }

                        if($categoriaEncontrada == true){
                            $pro = proproductos::where('prosku', $codigoMaterial)
                                                ->first(['proid', 'catid']);

                            if($pro){
                                $anterior = $pro->catid;
                                if($pro->catid != $categoriaid){
                                    $pro->catid = $categoriaid;
                                    $pro->update();
                                    $log["EDITAR_PRODUCTO"][] = "CATEGORIA ANTERIOR: ".$anterior." NUEVA CATEGORIA: ".$categoria."(".$categoriaid.") - SKU: ".$codigoMaterial;
                                }
                            }else{
                                $nuevopro = new proproductos;
                                $nuevopro->catid     = $categoriaid;
                                $nuevopro->prosku    = $codigoMaterial;
                                $nuevopro->pronombre = $material;
                                $nuevopro->proimagen = env('APP_URL').'/Sistema/abs/img/nohay.png';
                                if($nuevopro->save()){
                                    $log["NUEVO_PRODUCTO"][] = $codigoMaterial." CATEGORIA: ".$categoria."(".$categoriaid.")";
                                }else{

                                }
                            }
                        }else{
                            // DB::rollBack();
                            $log["CATEGORIA_NO_ENCONTRADA"][] = "En la linea: ".$i." categoria: ".$categoria." sku: ".$codigoMaterial;
                            $respuesta = false;
                        }
                    }else{
                        $respuesta = false;
                    }
                }

                
                $exitoSubirExcel = true;

            }else{
                $respuesta       = false;
                $mensaje         = "Lo sentimos, no se guardar el archivo";
                $mensajeDetalle  = "";
                $linea           = __LINE__;
                $exitoSubirExcel = false;
            }

            $nuevoCargaArchivo = new carcargasarchivos;
            $nuevoCargaArchivo->tcaid            = 7;
            $nuevoCargaArchivo->fecid            = $fecid;
            $nuevoCargaArchivo->usuid            = $usuusuario->usuid;
            $nuevoCargaArchivo->carnombrearchivo = $archivo;
            $nuevoCargaArchivo->carubicacion     = $fichero_subido;
            $nuevoCargaArchivo->carexito         = $exitoSubirExcel;
            $nuevoCargaArchivo->carurl           = env('APP_URL').'/Sistema/cargaArchivos/productos/'.$archivo;
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
            'CARGAR DATA DE PRODUCTOS AL SISTEMA ',
            'IMPORTAR',
            '/cargarArchivo/productos', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
    }
}
