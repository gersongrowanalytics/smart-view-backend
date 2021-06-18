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
use App\proproductos;
use App\usuusuarios;
use App\carcargasarchivos;
use App\fecfechas;
use App\sucsucursales;
use App\prlpromocionesliquidadas;

class PromocionesLiquidadasController extends Controller
{
    public function PromocionesLiquidadas(Request $request)
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
            "NO_SE_ENCONTRO_SUCURSAL" => [],
            "NO_SE_ENCONTRO_PRODUCTO" => [],
            "NO_SE_ENCONTRO_PRODUCTO_BONIFICADO" => [],
        );

        $exitoSubirExcel = false;
        
        DB::beginTransaction();

        try{

            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/promocionesliquidadas/'.basename($usuusuario->usuid.'-'.$usuusuario->usuusuario.'-'.$fechaActual.'-'.$_FILES['file']['name']);

            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {

                $objPHPExcel    = IOFactory::load($fichero_subido);
                $objPHPExcel->setActiveSheetIndex(0);
                $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();

                prlpromocionesliquidadas::where('prlid', '>', 0)->delete();

                for ($i=2; $i <= $numRows ; $i++) {
                    $dia = '01';

                    $anioPromocion = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                    $mesPromocion  = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                    $concepto      = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                    $ejecutivo     = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                    $grupo         = $objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
                    $soldto        = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                    $compra        = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
                    $bonificacion  = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                    $mecanica      = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
                    $categoria     = $objPHPExcel->getActiveSheet()->getCell('M'.$i)->getCalculatedValue();

                    $sku           = $objPHPExcel->getActiveSheet()->getCell('N'.$i)->getCalculatedValue();
                    $skuproducto   = $objPHPExcel->getActiveSheet()->getCell('N'.$i)->getCalculatedValue();
                    $skubonificado = $objPHPExcel->getActiveSheet()->getCell('P'.$i)->getCalculatedValue();
                    $productoBonif = $objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getCalculatedValue();

                    $plancha       = $objPHPExcel->getActiveSheet()->getCell('R'.$i)->getCalculatedValue();
                    $combo         = $objPHPExcel->getActiveSheet()->getCell('S'.$i)->getCalculatedValue();
                    $reconocerxcombo       = $objPHPExcel->getActiveSheet()->getCell('T'.$i)->getCalculatedValue();
                    $reconocerxplancha     = $objPHPExcel->getActiveSheet()->getCell('U'.$i)->getCalculatedValue();
                    $totalsoles            = $objPHPExcel->getActiveSheet()->getCell('V'.$i)->getCalculatedValue();
                    $liquidacionso         = $objPHPExcel->getActiveSheet()->getCell('W'.$i)->getCalculatedValue();
                    $liquidacioncombo      = $objPHPExcel->getActiveSheet()->getCell('X'.$i)->getCalculatedValue();
                    $liquidacionvalorizado = $objPHPExcel->getActiveSheet()->getCell('Y'.$i)->getCalculatedValue();
                    $liquidaciontotal      = $objPHPExcel->getActiveSheet()->getCell('Z'.$i)->getCalculatedValue();

                    if($plancha == "-"){
                        $plancha = "0";
                    }

                    $fec = fecfechas::where('fecdia', $dia)
                                        ->where('fecmes', $mesPromocion)
                                        ->where('fecano', $anioPromocion)
                                        ->first(['fecid']);
                                        
                    $fecid = 0;
                    if($fec){
                        $fecid = $fec->fecid;

                        $suc = sucsucursales::where('sucsoldto', $soldto)->first(['sucid']);

                        if($suc){
                            $sucid = $suc->sucid;

                            $prln = new prlpromocionesliquidadas;
                            $prln->fecid            = $fecid;
                            $prln->sucid            = $sucid;

                            $prlsku           = $sku;
                            $prlproducto      = $skuproducto;
                            $prlskubonificado = $skubonificado;
                            $prlproductobonificado = $productoBonif;

                            $prln->prlconcepto      = $concepto;
                            $prln->prlejecutivo     = $ejecutivo;
                            $prln->prlgrupo         = $grupo;
                            $prln->prlcompra        = $compra;
                            $prln->prlbonificacion  = $bonificacion;
                            $prln->prlmecanica      = $mecanica;
                            $prln->prlcategoria     = $categoria;
                            $prln->prlplancha       = $plancha;
                            $prln->prlcombo         = $combo;
                            $prln->prlreconocerxcombo   = $reconocerxcombo;
                            $prln->prlreconocerxplancha = $reconocerxplancha;
                            $prln->prltotal             = $totalsoles;
                            $prln->prlliquidacionso     = $liquidacionso;
                            $prln->prlliquidacioncombo  = $liquidacioncombo;
                            $prln->prlliquidacionvalorizado = $liquidacionvalorizado;
                            $prln->prlliquidaciontotalpagar = $liquidaciontotal;
                            $prln->save();

                        }else{
                            $log["NO_SE_ENCONTRO_SUCURSAL"][] = "No se encontro la sucursal: ".$soldto." en la linea: ".$i;
                            $mensaje = 'Lo sentimos, se encontraron algunas observaciones en la columna de soldto';
                            $respuesta = false;
                        }

                    }else{
                        $log["NO_SE_ENCONTRO_FECHA"][] = "En la linea: ".$i.", registrado con el mes: ".$mesPromocion." en el año: ".$anioPromocion;
                        $mensaje = 'Lo sentimos, se encontraron algunas observaciones en las columnas de mes y año';
                        $respuesta = false;
                    }
                }
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
            "logs"           => $log
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            $usuusuario->usuid,
            null,
            $fichero_subido,
            $requestsalida,
            'CARGAR DATA DE PROMOCIONES LIQUIDADAS ',
            'IMPORTAR',
            '/cargarArchivo/promociones-liquidadas', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
    }
}