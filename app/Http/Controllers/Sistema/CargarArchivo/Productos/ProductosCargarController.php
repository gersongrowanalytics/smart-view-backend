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

class ProductosCargarController extends Controller
{
    public function CargarProductos(Request $request)
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

        $exitoSubirExcel = false;
        try{
            
            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/productos/'.basename($usuusuario->usuid.'-'.$usuusuario->usuusuario.'-'.$fechaActual.'-'.$_FILES['file']['name']);

            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {

                $objPHPExcel    = IOFactory::load($fichero_subido);
                $objPHPExcel->setActiveSheetIndex(0);
                $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
                
                for ($i=2; $i <= $numRows ; $i++) {
                    $ano = '2020';
                    $dia = '01';

                    $codigoMaterial = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                    $material       = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                    $categoria      = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                    $subCategoria   = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                    $formato        = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                    $mes = 'AGO';

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
                        }

                        $pro = proproductos::where('prosku', $codigoMaterial)
                                            ->first(['proid']);

                        if($pro){

                        }else{
                            $nuevopro = new proproductos;
                            $nuevopro->catid     = $categoriaid;
                            $nuevopro->prosku    = $codigoMaterial;
                            $nuevopro->pronombre = $material;
                            $nuevopro->proimagen = env('APP_URL').'/Sistema/abs/img/nohay.png';
                            if($nuevopro->save()){

                            }else{

                            }
                        }
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
            $request['ip'],
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
