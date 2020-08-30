<?php

namespace App\Http\Controllers\Sistema\CargarArchivo\Promociones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
// use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


use Illuminate\Support\Str;
use App\fecfechas;
use App\tputiposusuarios;
use App\perpersonas;
use App\usuusuarios;
use App\catcategorias;
use App\proproductos;
use App\tprtipospromociones;
use App\cancanales;

class CargarArchivoController extends Controller
{
    public function CargarArchivo(Request $request)
    {



        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;

        $archivo        = $_FILES['file']['name'];

        // file_put_contents(base_path().'/public/'.$archivo, $_FILES['file']['tmp_name']);
        $fichero_subido = base_path().'/public/'.basename($_FILES['file']['name']);
        if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {
            
        } else {
            
        }

        
        $objPHPExcel    = IOFactory::load($fichero_subido);
        $objPHPExcel->setActiveSheetIndex(0);
        $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();

        $os = 2;
        // echo $numRows;
        // echo $objPHPExcel->getActiveSheet()->getCell('J'.$os)->getCalculatedValue();
        // return $archivo;
        try{
            
            for ($i=2; $i <= $numRows ; $i++) {
                $ano = '2020';
                $dia = '01';
    
                $mes        = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                $subCanal   = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
                $ejecutivo  = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
                $soldTo     = $objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();
                $cliente    = $objPHPExcel->getActiveSheet()->getCell('P'.$i)->getCalculatedValue();
                // *
                $cantCompra = $objPHPExcel->getActiveSheet()->getCell('R'.$i)->getCalculatedValue();
                $cantBonifi = $objPHPExcel->getActiveSheet()->getCell('S'.$i)->getCalculatedValue();
                $mecanica   = $objPHPExcel->getActiveSheet()->getCell('U'.$i)->getCalculatedValue();
                // *
                $categoria  = $objPHPExcel->getActiveSheet()->getCell('V'.$i)->getCalculatedValue();
                $sku        = $objPHPExcel->getActiveSheet()->getCell('W'.$i)->getCalculatedValue();
                $producto   = $objPHPExcel->getActiveSheet()->getCell('X'.$i)->getCalculatedValue();
                $skuBonifi  = $objPHPExcel->getActiveSheet()->getCell('Y'.$i)->getCalculatedValue();
                $productoBo = $objPHPExcel->getActiveSheet()->getCell('Z'.$i)->getCalculatedValue();
                $tipoPromo  = $objPHPExcel->getActiveSheet()->getCell('AA'.$i)->getCalculatedValue();
                $tipoClien  = $objPHPExcel->getActiveSheet()->getCell('AD'.$i)->getCalculatedValue();
                $combos     = $objPHPExcel->getActiveSheet()->getCell('AH'.$i)->getCalculatedValue();
    
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
    
                $tipoUsuario = tputiposusuarios::where('tpunombre', $subCanal)->first(['tpuid']);
                $tpuid = 0;
                if($tipoUsuario){
                    $tpuid = $tipoUsuario->tpuid;
                }else{
                    $nuevoTipoUsuario = new tputiposusuarios;
                    $nuevoTipoUsuario->tpunombre     = $subCanal;
                    $nuevoTipoUsuario->tpuprivilegio = null;
                    if($nuevoTipoUsuario->save()){
                        $tpuid = $nuevoTipoUsuario->tpuid;
                    }else{
    
                    }
                }
    
                // VERIFICAR SI EXISTE LA PERSONA
                $perpersona = perpersonas::where('pernombrecompleto', $ejecutivo)->first(['perid']);
                $perid = 0;
                if($perpersona){
                    $perid = $perpersona->perid;
                }else{
                    $nuevaPersona = new perpersonas;
                    $nuevaPersona->tdiid    = 2;
                    $nuevaPersona->pernombrecompleto = $ejecutivo;
                    $nuevaPersona->pernumerodocumentoidentidad = null;
                    $nuevaPersona->pernombre = null;
                    $nuevaPersona->perapellidopaterno   = null;
                    $nuevaPersona->perapellidomaterno   = null;
                    if($nuevaPersona->save()){
                        $perid = $nuevaPersona->perid;
                    }else{
    
                    }
                }
    
                // VERIFICAR SI EXISTE EL USUARIO
                $distribuidor = usuusuarios::where('tpuid', $tpuid)
                                            ->where('perid', $perid)
                                            ->first(['usuid']);
                $usuid = 0;
                if($distribuidor){
                    $usuid = $distribuidor->usuid;
                }else{
                    $nuevoUsuario = new usuusuarios;
                    $nuevoUsuario->tpuid         = $tpuid;
                    $nuevoUsuario->perid         = $perid;
                    $nuevoUsuario->usuusuario    = null;
                    $nuevoUsuario->usucorreo     = null;
                    $nuevoUsuario->usucontrasena = null;
                    $nuevoUsuario->usutoken      = Str::random(60);
                    if($nuevoUsuario->save()){
                        $usuid = $nuevoUsuario->usuid;
                    }else{
    
                    }
                }
    
                // VERIFICAR SI EXISTE LA PERSONA PARA EL CLIENTE
                $clienteperpersona = perpersonas::where('pernombrecompleto', $cliente)->first(['perid']);
                $clienteperid = 0;
                if($clienteperpersona){
                    $clienteperid = $clienteperpersona->perid;
                }else{
                    $clienteNuevaPersona = new perpersonas;
                    $clienteNuevaPersona->tdiid    = 2;
                    $clienteNuevaPersona->pernombrecompleto = $cliente;
                    $clienteNuevaPersona->pernumerodocumentoidentidad = null;
                    $clienteNuevaPersona->pernombre = null;
                    $clienteNuevaPersona->perapellidopaterno   = null;
                    $clienteNuevaPersona->perapellidomaterno   = null;
                    if($clienteNuevaPersona->save()){
                        $clienteperid = $clienteNuevaPersona->perid;
                    }else{
    
                    }
                }
    
                // VERIFICAR SI EXISTE EL USUARIO
                $usuCliente = usuusuarios::where('tpuid', $tpuid)
                                            ->where('perid', $clienteperid)
                                            ->first(['usuid']);
                $clienteusuid = 0;
                if($usuCliente){
                    $clienteusuid = $usuCliente->usuid;
                }else{
                    $clienteNuevoUsuario = new usuusuarios;
                    $clienteNuevoUsuario->tpuid         = $tpuid;
                    $clienteNuevoUsuario->perid         = $perid;
                    $clienteNuevoUsuario->usuusuario    = null;
                    $clienteNuevoUsuario->usucorreo     = null;
                    $clienteNuevoUsuario->usucontrasena = null;
                    $clienteNuevoUsuario->usutoken      = Str::random(60);
                    if($clienteNuevoUsuario->save()){
                        $clienteusuid = $clienteNuevoUsuario->usuid;
                    }else{
    
                    }
                }
    
                $catcategoria = catcategorias::where('catnombre', $categoria)
                                                ->first(['catid']);
                
                $catid = 0;
                if($catcategoria){
                    $catid = $catcategoria->catid;
                }else{
                    $nuevacategoria                 = new catcategorias;
                    $nuevacategoria->catnombre      = '';
                    $nuevacategoria->catimagenfondo = '';
                    $nuevacategoria->caticono       = '';
                    $nuevacategoria->catcolorhover  = '';
                    if($nuevacategoria->save()){
                        $catid = $nuevacategoria->catid;
                    }else{
    
                    }
                }
     
                // VERIFICAR SI EL PRODUCTO ESTA REGISTRADO
                $proproducto = proproductos::where('catid', $catid)
                                            ->where('prosku', $sku)
                                            ->first(['proid']);
                
                $proid = 0;
                if($proproducto){
                    $proid = $proproducto->proid;
                }else{
                    $nuevoProducto = new proproductos;
                    $nuevoProducto->catid     = $catid;
                    $nuevoProducto->prosku    = $sku;
                    $nuevoProducto->pronombre = $producto;
                    $nuevoProducto->proimagen = env('APP_URL').'/Sistema/abs/img/nohay.png';
                    if($nuevoProducto->save()){
                        $proid = $nuevoProducto->proid;
                    }else{
    
                    }
                }
    
                // VERIFICAR SI EL PRODUCTO BONIFICADO ESTA REGISTRADO
                $proproductoBonificado = proproductos::where('catid', $catid)
                                            ->where('prosku', $skuBonifi)
                                            ->first(['proid']);
                
                $bonificadoproid = 0;
                if($proproductoBonificado){
                    $bonificadoproid = $proproductoBonificado->proid;
                }else{
                    $nuevoProductoBonificado = new proproductos;
                    $nuevoProductoBonificado->catid     = $catid;
                    $nuevoProductoBonificado->prosku    = $skuBonifi;
                    $nuevoProductoBonificado->pronombre = $productoBo;
                    $nuevoProductoBonificado->proimagen = env('APP_URL').'/Sistema/abs/img/nohay.png';
                    if($nuevoProductoBonificado->save()){
                        $bonificadoproid = $nuevoProductoBonificado->proid;
                    }else{
    
                    }
                }
    
                // VERIFICAR SI EXISTE EL TIPO DE PROMOCION
                $tprtipopromocion = tprtipospromociones::where('tprnombre', $tipoPromo)->first(['tprid']);
                $tprid = 0;
                if($tprtipopromocion){
                    $tprid = $tprtipopromocion->tprid;
                }else{
                    $nuevoTipoPromocion = new tprtipospromociones;
                    $nuevoTipoPromocion->tprnombre  = $tipoPromo;
                    $nuevoTipoPromocion->tpricono   = null;
                    if($nuevoTipoPromocion->save()){
                        $tprid = $nuevoTipoPromocion->tprid;
                    }else{
    
                    }
                }
    
                // VERIFICAR SI EXISTE EL CANAL O TIPO DE CLIENTE
                $cancanal = cancanales::where('cannombre', $tipoClien)->first(['canid']);
    
                $canid = 0;
                if($cancanal){
                    $canid = $cancanal->canid;
                }else{
                    $nuevoCanal = new cancanales;
                    $nuevoCanal->cannombre = $tipoClien;
                    if($nuevoCanal->save()){
                        $canid = $nuevoCanal->canid;
                    }else{
    
                    }
                }
            }

        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
            "linea"          => $linea,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            null,
            $request['ip'],
            $request,
            $requestsalida,
            'Mostrar todas las fechas registradas ordenadas por la mas reciente',
            'MOSTRAR',
            '', //ruta
            null
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;

    }
}
