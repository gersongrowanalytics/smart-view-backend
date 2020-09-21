<?php

namespace App\Http\Controllers\Sistema\Promociones\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\csccanalessucursalescategorias;
use App\scasucursalescategorias;
use App\prppromocionesproductos;
use App\prbpromocionesbonificaciones;
use App\cspcanalessucursalespromociones;
use App\usuusuarios;
use App\ussusuariossucursales;

class CategoriasPromocionesMostrarController extends Controller
{
    public function mostrarCategoriasPromociones(Request $request)
    {

        $usutoken   = $request['usutoken'];
        $sucid      = $request['sucid'];
        $dia        = $request['dia'];
        $mes        = $request['mes'];
        $anio       = $request['ano'];
        
        $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['ususoldto']);

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;

        $arrCodigosPrincipales = [];

        try{

            $nuevoArray = array(
                array(
                    "ANIO"                  => "",
                    "MES"                   => "",
                    "CATEGORIA"             => "",
                    "CANAL"                 => "",
                    "MECANICA"              => "",
                    "SKU"                   => "",
                    "PRODUCTO"              => "",
                    "SKU BONIFICADO"        => "",
                    "PRODUCTO BONIFICADO"   => "",
                    "PLANCHAS ROTAR"        => "",
                    "#COMBOS"               => "",
                    "RECONOCER X COMBO"     => "",
                    "RECONOCER X PLANCHA"   => "",
                    "TOTAL SOLES"           => "",
                    "ACCION"                => ""
                )
            );

            $scasucursalescategorias = scasucursalescategorias::join('fecfechas as fec', 'scasucursalescategorias.fecid', 'fec.fecid')
                                                                ->join('catcategorias as cat', 'cat.catid', 'scasucursalescategorias.catid')
                                                                ->where('scasucursalescategorias.sucid', $sucid)
                                                                ->where('fec.fecano', $anio)
                                                                ->where('fec.fecmes', $mes)
                                                                ->where('fec.fecdia', $dia)
                                                                ->where('scasucursalescategorias.tsuid', null)
                                                                ->get([
                                                                    'scasucursalescategorias.scaid',
                                                                    'cat.catid',
                                                                    'cat.catnombre',
                                                                    'cat.catimagenfondo',
                                                                    'cat.catimagenfondoseleccionado',
                                                                    'cat.catimagenfondoopaco',
                                                                    'cat.caticono',
                                                                    'cat.caticonohover',
                                                                    'cat.catcolorhover',
                                                                    'cat.catcolor',
                                                                    'cat.caticonoseleccionado',
                                                                    'fec.fecfecha'
                                                                ]);

            if(sizeof($scasucursalescategorias) > 0){
                $contador = 0;
                foreach($scasucursalescategorias as $posicionSca => $sca){
                    $nuevoArray[$contador]['ANIO']      = $anio;
                    $nuevoArray[$contador]['MES']       = $mes;
                    $nuevoArray[$contador]['CATEGORIA'] = $sca->catnombre;


                    $csccanalessucursalescategorias = csccanalessucursalescategorias::join('cancanales as can', 'can.canid', 'csccanalessucursalescategorias.canid')
                                                                        ->where('csccanalessucursalescategorias.scaid', $sca->scaid)
                                                                        ->get([
                                                                            'csccanalessucursalescategorias.cscid',
                                                                            'can.canid',
                                                                            'can.cannombre'
                                                                        ]);
                    //**************************************** */
                    if(sizeof($csccanalessucursalescategorias) > 0){
                        $primeraCsc = false;
                        foreach($csccanalessucursalescategorias as $posicion => $csccanalesucursalcategoria){
                            if($primeraCsc == false){
                                $nuevoArray[$contador]['CANAL'] = $csccanalesucursalcategoria->cannombre;
                                $primeraCsc = true;
                            }else{
                                $contador = $contador+1;
                                $nuevoArray[$contador]['ANIO']      = $anio;
                                $nuevoArray[$contador]['MES']       = $mes;
                                $nuevoArray[$contador]['CATEGORIA'] = $sca->catnombre;
                                $nuevoArray[$contador]['CANAL']     = $csccanalesucursalcategoria->cannombre;
                            }

                            $cspcanalessucursalespromociones = cspcanalessucursalespromociones::join('prmpromociones as prm', 'prm.prmid', 'cspcanalessucursalespromociones.prmid')
                                                                                                ->where('cscid', $csccanalesucursalcategoria->cscid)
                                                                                                ->get([
                                                                                                    'cspcanalessucursalespromociones.cspid',
                                                                                                    'prm.prmid',
                                                                                                    'cspcanalessucursalespromociones.cspvalorizado',
                                                                                                    'cspcanalessucursalespromociones.cspplanchas',
                                                                                                    'cspcanalessucursalespromociones.cspcompletado',
                                                                                                    'cspcanalessucursalespromociones.cspcantidadcombo',
                                                                                                    'prm.prmmecanica',
                                                                                                    'cspcanalessucursalespromociones.cspcantidadplancha',
                                                                                                    'cspcanalessucursalespromociones.csptotalcombo',
                                                                                                    'cspcanalessucursalespromociones.csptotalplancha',
                                                                                                    'cspcanalessucursalespromociones.csptotal',
                                                                                                    'prm.prmaccion'
                                                                                                ]);
                            $primeraCsp = false;
                            if(sizeof($cspcanalessucursalespromociones) > 0){
                                foreach($cspcanalessucursalespromociones as $posicionPromociones => $cspcanalesucursalpromocion){
                                    if($primeraCsp == false){
                                        $nuevoArray[$contador]['MECANICA']            = $cspcanalesucursalpromocion->prmmecanica;
                                        $nuevoArray[$contador]['PLANCHAS ROTAR']      = $cspcanalesucursalpromocion->cspcantidadplancha;
                                        $nuevoArray[$contador]['#COMBOS']             = $cspcanalesucursalpromocion->cspcantidadcombo;
                                        $nuevoArray[$contador]['RECONOCER X COMBO']   = $cspcanalesucursalpromocion->csptotalcombo;
                                        $nuevoArray[$contador]['RECONOCER X PLANCHA'] = $cspcanalesucursalpromocion->csptotalplancha;
                                        $nuevoArray[$contador]['TOTAL SOLES']         = $cspcanalesucursalpromocion->csptotal;
                                        $nuevoArray[$contador]['ACCION']              = $cspcanalesucursalpromocion->prmaccion;
                                        $primeraCsp = true;
                                    }else{
                                        $contador = $contador+1;
                                        $nuevoArray[$contador]['ANIO']                = $anio;
                                        $nuevoArray[$contador]['MES']                 = $mes;
                                        $nuevoArray[$contador]['CATEGORIA']           = $sca->catnombre;
                                        $nuevoArray[$contador]['CANAL']               = $csccanalesucursalcategoria->cannombre;
                                        $nuevoArray[$contador]['MECANICA']            = $cspcanalesucursalpromocion->prmmecanica;
                                        $nuevoArray[$contador]['PLANCHAS ROTAR']      = $cspcanalesucursalpromocion->cspcantidadplancha;
                                        $nuevoArray[$contador]['#COMBOS']             = $cspcanalesucursalpromocion->cspcantidadcombo;
                                        $nuevoArray[$contador]['RECONOCER X COMBO']   = $cspcanalesucursalpromocion->csptotalcombo;
                                        $nuevoArray[$contador]['RECONOCER X PLANCHA'] = $cspcanalesucursalpromocion->csptotalplancha;
                                        $nuevoArray[$contador]['TOTAL SOLES']         = $cspcanalesucursalpromocion->csptotal;
                                        $nuevoArray[$contador]['ACCION']              = $cspcanalesucursalpromocion->prmaccion;
                                    }


                                    // $codigoPrincipal = $usuusuario->ususoldto.;

                                    $prppromocionesproductos = prppromocionesproductos::join('proproductos as pro', 'pro.proid', 'prppromocionesproductos.proid')
                                                                                        ->where('prppromocionesproductos.prmid', $cspcanalesucursalpromocion->prmid )
                                                                                        // ->where(function ($query) use($arrCodigosPrincipales) {

                                                                                        //     for($i = 0; $i < sizeof($arrCodigosPrincipales); $i++){
                                                                                        //         $query->where('prpcodigoprincipal', '!=' , $arrCodigosPrincipales[$i]);
                                                                                        //     }

                                                                                        // })
                                                                                        ->where('prpcodigoprincipal', 'LIKE', $usuusuario->ususoldto.'%')
                                                                                        ->get([
                                                                                            'pro.proid',
                                                                                            'pro.prosku',
                                                                                            'pro.pronombre',
                                                                                            'pro.proimagen',
                                                                                            'prpproductoppt',
                                                                                            'prpcomprappt',
                                                                                            'prpcodigoprincipal'
                                                                                        ]);
                                    $primeraPrp = false;
                                    if(sizeof($prppromocionesproductos) > 0){
                                        foreach($prppromocionesproductos as $posicionProductos => $prp ){
                                            $ignorar = false;
                                            for($i = 0; $i < sizeof($arrCodigosPrincipales); $i++){
                                                if($arrCodigosPrincipales[$i] == $prp->prpcodigoprincipal){
                                                    $ignorar = true;
                                                    break;
                                                }
                                            }
                                            $arrCodigosPrincipales[] = $prp->prpcodigoprincipal;
                                            if($primeraPrp == false){
                                                $nuevoArray[$contador]['SKU']      = $prp->prosku;
                                                $nuevoArray[$contador]['PRODUCTO'] = $prp->prpproductoppt;
                                                $primeraPrp = true;
                                            }else{
                                                $contador = $contador+1;
                                                $nuevoArray[$contador]['ANIO']                = $anio;
                                                $nuevoArray[$contador]['MES']                 = $mes;
                                                $nuevoArray[$contador]['CATEGORIA']           = $sca->catnombre;
                                                $nuevoArray[$contador]['CANAL']               = $csccanalesucursalcategoria->cannombre;
                                                $nuevoArray[$contador]['MECANICA']            = $cspcanalesucursalpromocion->prmmecanica;
                                                $nuevoArray[$contador]['PLANCHAS ROTAR']      = $cspcanalesucursalpromocion->cspcantidadplancha;
                                                $nuevoArray[$contador]['#COMBOS']             = $cspcanalesucursalpromocion->cspcantidadcombo;
                                                $nuevoArray[$contador]['RECONOCER X COMBO']   = $cspcanalesucursalpromocion->csptotalcombo;
                                                $nuevoArray[$contador]['RECONOCER X PLANCHA'] = $cspcanalesucursalpromocion->csptotalplancha;
                                                $nuevoArray[$contador]['TOTAL SOLES']         = $cspcanalesucursalpromocion->csptotal;
                                                $nuevoArray[$contador]['ACCION']              = $cspcanalesucursalpromocion->prmaccion;
                                                $nuevoArray[$contador]['SKU']                 = $prp->prosku;
                                                $nuevoArray[$contador]['PRODUCTO']            = $prp->prpproductoppt;
                                            }



                                            $prbpromocionesbonificaciones = prbpromocionesbonificaciones::join('proproductos as pro', 'pro.proid', 'prbpromocionesbonificaciones.proid')
                                                                                                ->where('prbpromocionesbonificaciones.prmid', $cspcanalesucursalpromocion->prmid )
                                                                                                ->where('prbcodigoprincipal', $prp->prpcodigoprincipal )
                                                                                                ->first([
                                                                                                    'pro.proid',
                                                                                                    'pro.prosku',
                                                                                                    'pro.pronombre',
                                                                                                    'pro.proimagen',
                                                                                                    'prbproductoppt',
                                                                                                    'prbcomprappt',
                                                                                                    'prbcodigoprincipal'
                                                                                                ]);
                                    
                                            if($prbpromocionesbonificaciones){
                                                $nuevoArray[$contador]['SKU BONIFICADO']      = $prbpromocionesbonificaciones->prosku;
                                                $nuevoArray[$contador]['PRODUCTO BONIFICADO'] = $prbpromocionesbonificaciones->prbproductoppt;
                                            }else{
                                                $nuevoArray[$contador]['SKU BONIFICADO']      = '';
                                                $nuevoArray[$contador]['PRODUCTO BONIFICADO'] = '';
                                            }
                                            
                                        }
                                    }else{
                                        $nuevoArray[$contador]['SKU']      = '';
                                        $nuevoArray[$contador]['PRODUCTO'] = '';
                                    }
                                }
                            }else{
                                $nuevoArray[$contador]['MECANICA']            = '';
                                $nuevoArray[$contador]['PLANCHAS ROTAR']      = '';
                                $nuevoArray[$contador]['#COMBOS']             = '';
                                $nuevoArray[$contador]['RECONOCER X COMBO']   = '';
                                $nuevoArray[$contador]['RECONOCER X PLANCHA'] = '';
                                $nuevoArray[$contador]['TOTAL SOLES']         = '';
                                $nuevoArray[$contador]['ACCION']              = '';
                            }
                        }
                    }else{
                        $nuevoArray[$contador]['CANAL'] = '';
                    }

                    
                    $contador = $contador+1;
                    //**************************************** */
                }
                $respuesta = true;
                $datos     = $nuevoArray;
            }else{
                $respuesta      = false;
                $linea          = __LINE__;
                $mensaje        = 'Lo sentimos, no se contramos categorias registradas a este filtro.';
                $mensajeDetalle = sizeof($scasucursalescategorias).' registros encontrados.';
            }

        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
        }

        $requestsalida = response()->json([
            'respuesta'      => $respuesta,
            'mensaje'        => $mensaje,
            'datos'          => $datos,
            'linea'          => $linea,
            'mensajeDetalle' => $mensajeDetalle,
            'mensajedev'     => $mensajedev
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            null,
            $request['ip'],
            $request,
            $requestsalida,
            'MOSTRAR LAS PROMOCIONES DE UN USUARIO PARA DESCARGAR UN EXCEL',
            'DESCARGAR',
            '',
            null
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
    }

    public function mostrarCategoriasPromocionesExcel(Request $request)
    {

        $usutoken   = $request['usutoken'];
        $sucid      = $request['sucid'];
        $dia        = $request['dia'];
        $mes        = $request['mes'];
        $anio       = $request['ano'];
        
        $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['ususoldto']);

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;


        $columnasExcel = [
            "A",
            "B",
            "C",
            "D",
            "E",
            "F",
            "G",
            "H",
            "I",
            "J",
            "K",
            "L",
            "O",
            "P",
            "Q",
            "R",
            "S",
            "T",
            "U",
            "V",
            "W",
            "X",
            "Y",
            "Z",
            "AA",
            "AB",
            "AC",
            "AD",
            "AE",
            "AF",
            "AG",
            "AH",
            "AI",
            "AJ",
            "AK",
            "AL",
            "AM",
            "AN",
            "AO",
            "AP",
            "AQ",
            "AR",
            "AS",
            "AT",
            "AU"
        ];

        $colorPlomo         = "FF595959";
        $colorBlanco        = "FFFFFFFF";
        $colorAzul          = "FF002060";
        $colorVerdeClaro    = "FF66FF33";
        $colorRosa          = "FFFF9999";
        $colorNaranjaClaro  = "FFFFC000";
        $colorPiel          = "FFFFF2CC";
        $colorVerdeLimon    = "FFCCFFCC";        

        try{

            $uss = ussusuariossucursales::join('usuusuarios as usu', 'usu.usuid', 'ussusuariossucursales.usuid' )
                                        ->where('ussusuariossucursales.sucid', $sucid)
                                        ->get([
                                            'ussusuariossucursales.ussid',
                                            'ussusuariossucursales.usuid',
                                            'ussusuariossucursales.sucid',
                                            'usu.ususoldto'
                                        ]);

            $nuevoArray = array(
                array(
                    "columns" => [],
                    "data"    => []
                )
            );


            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/promociones/promociones.xlsx';

            $objPHPExcel    = IOFactory::load($fichero_subido);
            $objPHPExcel->setActiveSheetIndex(0);
            $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
            $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();

            for ($i=2; $i <= $numRows ; $i++) {

                if($i == 2){

                    $arrayTitulos = array(
                        array(
                            "title" => ""
                        )
                    );

                    $contadorTitulos = 0;
                    foreach($columnasExcel as $abc) {  
                        $columnasFilas = $objPHPExcel->getActiveSheet()->getCell($abc.$i)->getCalculatedValue();
                        
                        $arrayTitulos[$contadorTitulos]['title'] = $columnasFilas;
                        $arrayTitulos[$contadorTitulos]['style']['fill']['patternType'] = 'solid';
                        if($abc == "A" || $abc == "B" || $abc == "J" || $abc == "K" || $abc == "M" || $abc == "N" || $abc == "Q" || $abc == "T" || $abc == "U" || $abc == "V" || $abc == "Z" || $abc == "AD" || $abc == "AG" || $abc == "AI" || $abc == "AK" || $abc == "AM" || $abc == "AN" || $abc == "AQ" || $abc == "AR" || $abc == "AS" || $abc == "AT"){
                            $arrayTitulos[$contadorTitulos]['style']['fill']['fgColor']['rgb'] = $colorPlomo;
                            $arrayTitulos[$contadorTitulos]['style']['font']['color']['rgb'] = $colorBlanco;
                        }else if($abc == "C" || $abc == "D" || $abc == "E" || $abc == "F" || $abc == "G" || $abc == "H" || $abc == "I" || $abc == "L" || $abc == "O" || $abc == "P" || $abc == "Y" || $abc == "AC" || $abc == "AH" || $abc == "AL" || $abc == "AU"){
                            $arrayTitulos[$contadorTitulos]['style']['fill']['fgColor']['rgb'] = $colorAzul;
                            $arrayTitulos[$contadorTitulos]['style']['font']['color']['rgb'] = $colorBlanco;
                        }else if($abc == "R" || $abc == "S" || $abc == "AO" || $abc == "AP"){
                            $arrayTitulos[$contadorTitulos]['style']['fill']['fgColor']['rgb'] = $colorNaranjaClaro;
                            $arrayTitulos[$contadorTitulos]['style']['font']['color']['rgb'] = $colorBlanco;
                        }else if($abc == "W" || $abc == "X" || $abc == "AA" || $abc == "AB" || $abc == "AE" || $abc == "AF" || $abc == "R" ){
                            $arrayTitulos[$contadorTitulos]['style']['fill']['fgColor']['rgb'] = $colorVerdeClaro;
                        }else if($abc == "AJ"){
                            $arrayTitulos[$contadorTitulos]['style']['fill']['fgColor']['rgb'] = $colorRosa;
                            $arrayTitulos[$contadorTitulos]['style']['font']['color']['rgb'] = $colorBlanco;
                        }
                        $contadorTitulos = $contadorTitulos+1;
                    }

                    $nuevoArray[0]['columns'] = $arrayTitulos;

                }else{
                    $soldto = $objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();

                    $pertenecedata = false;
                    foreach($uss as $u){
                        if($u->ususoldto == $soldto ){
                            $pertenecedata = true;
                            break;
                        }else{
                            $pertenecedata = false;
                        }
                    }

                    if($pertenecedata == true){
                        $arrayFilaExcel = array(
                            array(
                                "value" => ""
                            )
                        );
                        $contadorColumna = 0;

                        foreach($columnasExcel as $abc) {  
                            $columnasFilas = $objPHPExcel->getActiveSheet()->getCell($abc.$i)->getCalculatedValue();
                            
                            if($columnasFilas == null){
                                $columnasFilas = "";
                            }

                            $arrayFilaExcel[$contadorColumna]['value'] = $columnasFilas;

                            if($abc == "L" || $abc == "O" || $abc == "P" || $abc == "R" || $abc == "S" || $abc == "Y" || $abc == "AC" || $abc == "AL" || $abc == "AU"){
                                $arrayFilaExcel[$contadorColumna]['style']['fill']['patternType']    = 'solid';
                                $arrayFilaExcel[$contadorColumna]['style']['fill']['fgColor']['rgb'] = $colorPiel;
                            }else if($abc == "W" || $abc == "X" ){
                                $arrayFilaExcel[$contadorColumna]['style']['fill']['patternType']    = 'solid';
                                $arrayFilaExcel[$contadorColumna]['style']['fill']['fgColor']['rgb'] = $colorVerdeLimon;
                            }else if($abc == "AH" || $abc == "AR"){
                                $arrayFilaExcel[$contadorColumna]['style']['fill']['patternType']    = 'solid';
                                $arrayFilaExcel[$contadorColumna]['style']['fill']['fgColor']['rgb'] = $colorNaranjaClaro;
                            }

                            $contadorColumna = $contadorColumna+1;
                        }

                        $nuevoArray[0]['data'][] = $arrayFilaExcel;
                    }
                }
            }

            $respuesta = true;
            $datos     = $nuevoArray;

        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
        }

        $requestsalida = response()->json([
            'respuesta'      => $respuesta,
            'mensaje'        => $mensaje,
            'datos'          => $datos,
            'linea'          => $linea,
            'mensajeDetalle' => $mensajeDetalle,
            'mensajedev'     => $mensajedev
        ]);

        return $requestsalida;


    }
}
