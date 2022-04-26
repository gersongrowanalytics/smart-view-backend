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
use App\ltplistaprecios;
use App\proproductos;
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

        $subirData = true;

        $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['usuid', 'usuusuario']);

        $fichero_subido = '';

        $pkid = 0;
        $log  = array(
            "categorias" => [],
            "marcaje" => [],
            "pvp" => []
        );

        $exitoSubirExcel = false;

        $nombres = [];
        $fechaSeleccionada = 153;
        $gruposEncontrados = [];

        DB::beginTransaction();
        try{
            $nombreArchivoGuardado = basename($fechaActual."-".$_FILES['file']['name']);
            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/listaprecios/'.$nombreArchivoGuardado;

            // ltplistaprecios::where('ltpid', '>', 0)->delete();
            ltplistaprecios::where('fecid', $fechaSeleccionada)->delete();

            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {

                if($subirData == true){
                    $objPHPExcel    = IOFactory::load($fichero_subido);

                    $nombres = $objPHPExcel->getSheetNames();
                    $nombresHojas = $objPHPExcel->getSheetNames();

                    $grupoSeleccionado = 0;

                    // 15 -> ZB
                    // 26 -> ZA
                    // 24 -> ZC

                    foreach($nombresHojas as $posicionHoja => $nombresHoja){

                        $grupoA = 'TRATEGICO';
                        $grupoB = 'CTICO';
                        $grupoC = 'BROKER';

                        if (strpos($nombresHoja, $grupoA) !== false) {
                            $grupoSeleccionado = 26;
                            $gruposEncontrados[] = $grupoSeleccionado;
                        }else if(strpos($nombresHoja, $grupoB) !== false){
                            $grupoSeleccionado = 15;
                            $gruposEncontrados[] = $grupoSeleccionado;
                        }else if(strpos($nombresHoja, $grupoC) !== false){
                            $grupoSeleccionado = 24;
                            $gruposEncontrados[] = $grupoSeleccionado;
                        }else{
                            $grupoSeleccionado = 0;
                        }

                        if($grupoSeleccionado != 0){
                            $objPHPExcel->setActiveSheetIndex($posicionHoja);
                            $numRows        = $objPHPExcel->setActiveSheetIndex($posicionHoja)->getHighestRow();
                            $ultimaColumna  = $objPHPExcel->setActiveSheetIndex($posicionHoja)->getHighestColumn();

                            $treidSeleccionado = $grupoSeleccionado;

                            $this->AgregarDataGrupo($numRows, $objPHPExcel, $fechaSeleccionada, $treidSeleccionado);
                        }


                    }

                    $this->IdentificarDuplicadosComplejos($fechaSeleccionada);

                    



                    // $objPHPExcel->setActiveSheetIndex(0);
                    // $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                    // $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
                    

                    // $fechaSeleccionada = 69;

                    // AGREGAR LISTA DE PRECIOS DE ZA
                    // $treidSeleccionado = 26;
                    // $this->AgregarDataGrupo($numRows, $objPHPExcel, $fechaSeleccionada, $treidSeleccionado);


                    // $objPHPExcel->setActiveSheetIndex(3);
                    // $numRows        = $objPHPExcel->setActiveSheetIndex(3)->getHighestRow();
                    // $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(3)->getHighestColumn();

                    // // $nombres[] = $objPHPExcel->getSheetNames()[1];

                    // // AGREGAR LISTA DE PRECIOS DE ZB
                    // $treidSeleccionado = 15;
                    // $this->AgregarDataGrupo($numRows, $objPHPExcel, $fechaSeleccionada, $treidSeleccionado);



                    // $objPHPExcel->setActiveSheetIndex(4);
                    // $numRows        = $objPHPExcel->setActiveSheetIndex(4)->getHighestRow();
                    // $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(4)->getHighestColumn();
                    // // $nombres[] = $objPHPExcel->getSheetNames()[1];
                    // // AGREGAR LISTA DE PRECIOS DE ZC
                    // $treidSeleccionado = 24;
                    // $this->AgregarDataGrupo($numRows, $objPHPExcel, $fechaSeleccionada, $treidSeleccionado);


                    $exitoSubirExcel = true;
                }else{

                }

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
            "logs"           => $log,
            "nombres" => $nombres,
            "gruposEncontrados" => $gruposEncontrados,
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


    public function AgregarDataGrupo($numRows, $objPHPExcel, $fechaSeleccionada, $treidSeleccionado)
    {

        $skus = array();

        for ($i=6; $i <= $numRows ; $i++) {
            $dia = '01';

            $ex_categoria           = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
            $ex_subcategoria        = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
            $ex_codigosap           = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
            $ex_ean                 = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
            $ex_descripcionproducto = $objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
            $ex_unidadventa         = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
            $ex_preciolistasinigv   = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
            $ex_alza                = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
            $ex_sdtpr               = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
            $ex_preciolistaconigv   = $objPHPExcel->getActiveSheet()->getCell('M'.$i)->getCalculatedValue();
            $ex_mfrutamayorista     = $objPHPExcel->getActiveSheet()->getCell('N'.$i)->getCalculatedValue();
            $ex_reventamayorista    = $objPHPExcel->getActiveSheet()->getCell('P'.$i)->getCalculatedValue();
            $ex_margenmayorista     = $objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getCalculatedValue();
            $ex_marcajemayorista    = $objPHPExcel->getActiveSheet()->getCell('S'.$i)->getCalculatedValue();
            $ex_mfrutaminorista     = $objPHPExcel->getActiveSheet()->getCell('U'.$i)->getCalculatedValue();
            $ex_reventaminorista    = $objPHPExcel->getActiveSheet()->getCell('W'.$i)->getCalculatedValue();
            $ex_margenminorista     = $objPHPExcel->getActiveSheet()->getCell('X'.$i)->getCalculatedValue();
            $ex_marcajeminorista    = $objPHPExcel->getActiveSheet()->getCell('Z'.$i)->getCalculatedValue();
            $ex_mfrutahorizontal    = $objPHPExcel->getActiveSheet()->getCell('AB'.$i)->getCalculatedValue();
            $ex_reventabodega       = $objPHPExcel->getActiveSheet()->getCell('AD'.$i)->getCalculatedValue();
            $ex_margenbodega        = $objPHPExcel->getActiveSheet()->getCell('AE'.$i)->getCalculatedValue();
            $ex_pvp                 = $objPHPExcel->getActiveSheet()->getCell('AG'.$i)->getCalculatedValue();
            
            $encontroSku = false;
            $noAgregarFila = false;
            $tieneZona = false;
            $zonaSeleccionada = "LIMA";

            foreach($skus as $sku){
                if($sku['sku'] == $ex_codigosap){

                    if(
                        $sku['ean'] == $ex_ean && 
                        $sku['nombre'] == $ex_descripcionproducto && 
                        $sku['undventa'] == $ex_unidadventa && 
                        $sku['precio'] == $ex_preciolistasinigv && 
                        $sku['alza'] == $ex_alza && 
                        $sku['precioconigv'] == $ex_preciolistaconigv
                    ){

                        $noAgregarFila = true;
                        break;
                    }else if($sku['nombre'] != $ex_descripcionproducto){

                        $zonaLim  = 'LIMA';
                        $zonaProv = 'PROVINCIA';

                        if (strpos($ex_descripcionproducto, $zonaLim) !== false) {
                            $zonaSeleccionada = "LIMA";
                            $tieneZona = true;
                        }else if(strpos($ex_descripcionproducto, $zonaProv) !== false){
                            $zonaSeleccionada = "PROVINCIA";
                            $tieneZona = true;
                        }

                        $encontroSku = false;
                        break;

                    }else{
                        $encontroSku = true;
                    }
                    // $encontroSku = true;
                    
                }
            }

            if($encontroSku == true || $noAgregarFila == true){

            }else{
                $skus[] = array(
                    "sku" => $ex_codigosap,
                    "ean" => $ex_ean,
                    "nombre" => $ex_descripcionproducto,
                    "undventa" => $ex_unidadventa,
                    "precio" => $ex_preciolistasinigv,
                    "alza" => $ex_alza,
                    "precioconigv" => $ex_preciolistaconigv,
                );
            }


            if($noAgregarFila != true){
                $pro = proproductos::where('prosku', 'LIKE', "%".$ex_codigosap."%")->first();
                $proid = 2122;
                if($pro){
                    $proid = $pro->proid;
                }

                $ltpn = new ltplistaprecios;
                $ltpn->treid = $treidSeleccionado;
                $ltpn->proid = $proid;
                $ltpn->fecid = $fechaSeleccionada;

                $ltpn->ltpduplicadocomplejo = $encontroSku;

                $ltpn->ltpzona = $zonaSeleccionada;
                $ltpn->ltptienezona = $tieneZona;

                $ltpn->ltpcategoria             = $ex_categoria;
                $ltpn->ltpsubcategoria          = $ex_subcategoria;
                $ltpn->ltpcodigosap             = $ex_codigosap;
                $ltpn->ltpean                   = $ex_ean;
                $ltpn->ltpdescripcionproducto   = $ex_descripcionproducto;
                $ltpn->ltpunidadventa           = $ex_unidadventa;
                $ltpn->ltppreciolistasinigv     = $ex_preciolistasinigv;
                $ltpn->ltpalza                  = $ex_alza;
                $ltpn->ltpsdtpr                 = $ex_sdtpr;
                $ltpn->ltppreciolistaconigv     = $ex_preciolistaconigv;
                $ltpn->ltpmfrutamayorista       = $ex_mfrutamayorista;
                $ltpn->ltpreventamayorista      = $ex_reventamayorista;
                $ltpn->ltpmargenmayorista       = $ex_margenmayorista;
                $ltpn->ltpmarcajemayorista      = $ex_marcajemayorista;
                $ltpn->ltpmfrutaminorista       = $ex_mfrutaminorista;
                $ltpn->ltpreventaminorista      = $ex_reventaminorista;
                $ltpn->ltpmargenminorista       = $ex_margenminorista;
                $ltpn->ltpmarcajeminorista      = $ex_marcajeminorista;
                $ltpn->ltpmfrutahorizontal      = $ex_mfrutahorizontal;
                $ltpn->ltpreventabodega         = $ex_reventabodega;
                $ltpn->ltpmargenbodega          = $ex_margenbodega;
                $ltpn->ltppvp                   = $ex_pvp;
                $ltpn->save();
            }
        }

    }

    public function IdentificarDuplicadosComplejos($fechaSeleccionada)
    {
        $ltps = ltplistaprecios::where('fecid', $fechaSeleccionada)
                                ->where('ltpduplicadocomplejo', true)
                                ->get();

        foreach($ltps as $ltp){
            
            $ltp = ltplistaprecios::where('fecid', $fechaSeleccionada)
                                    ->where('treid', $ltp->treid)
                                    ->where('ltpcodigosap', $ltp->ltpcodigosap)
                                    ->update(['ltpduplicadocomplejo' => true]);


        }
    }

}
