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
use App\ussusuariossucursales;
use App\sucsucursales;
use App\scasucursalescategorias;
use App\csccanalessucursalescategorias;
use App\prmpromociones;
use App\cspcanalessucursalespromociones;
use App\prbpromocionesbonificaciones;
use App\prppromocionesproductos;
use App\carcargasarchivos;

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
        $numeroCelda    = 0;
        $usutoken       = $request->header('api_token');
        $archivo        = $_FILES['file']['name'];

        $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['usuid']);

        $fichero_subido = '';

        $pkid = 0;
        $log  = [];
        try{
            // file_put_contents(base_path().'/public/'.$archivo, $_FILES['file']['tmp_name']);
            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/promociones/'.basename($_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {
                $objPHPExcel    = IOFactory::load($fichero_subido);
                $objPHPExcel->setActiveSheetIndex(0);
                $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();

                for ($i=3; $i <= $numRows ; $i++) {
                    // $ano = '2020';
                    $dia = '01';
        
                    $ano        = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                    $mes        = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                    $subCanal   = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                    $ejecutivo  = $objPHPExcel->getActiveSheet()->getCell('M'.$i)->getCalculatedValue();
                    $soldTo     = $objPHPExcel->getActiveSheet()->getCell('P'.$i)->getCalculatedValue();
                    $cliente    = $objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getCalculatedValue();
                    $accion     = $objPHPExcel->getActiveSheet()->getCell('R'.$i)->getCalculatedValue();
                    $cantCompra = $objPHPExcel->getActiveSheet()->getCell('S'.$i)->getCalculatedValue();
                    $cantBonifi = $objPHPExcel->getActiveSheet()->getCell('T'.$i)->getCalculatedValue();
                    $mecanica   = $objPHPExcel->getActiveSheet()->getCell('V'.$i)->getCalculatedValue();
                    $categoria  = $objPHPExcel->getActiveSheet()->getCell('W'.$i)->getCalculatedValue();

                    /**NUEVOS CAMPOS */
                    $codPromoc  = $objPHPExcel->getActiveSheet()->getCell('X'.$i)->getCalculatedValue();
                    $codPrinci  = $objPHPExcel->getActiveSheet()->getCell('Y'.$i)->getCalculatedValue();
                    /***/

                    $sku        = $objPHPExcel->getActiveSheet()->getCell('Z'.$i)->getCalculatedValue();
                    $producto   = $objPHPExcel->getActiveSheet()->getCell('AA'.$i)->getCalculatedValue();

                    /**NUEVOS CAMPOS */
                    $productoPpt = $objPHPExcel->getActiveSheet()->getCell('AB'.$i)->getCalculatedValue();
                    $compraPpt   = $objPHPExcel->getActiveSheet()->getCell('AC'.$i)->getCalculatedValue();
                    /***/

                    $skuBonifi  = $objPHPExcel->getActiveSheet()->getCell('AD'.$i)->getCalculatedValue();
                    $productoBo = $objPHPExcel->getActiveSheet()->getCell('AE'.$i)->getCalculatedValue();

                    /**NUEVOS CAMPOS */
                    $proBoniPpt = $objPHPExcel->getActiveSheet()->getCell('AG'.$i)->getCalculatedValue();
                    $compBonPpt = $objPHPExcel->getActiveSheet()->getCell('AH'.$i)->getCalculatedValue();
                    /***/

                    $tipoPromo  = $objPHPExcel->getActiveSheet()->getCell('AF'.$i)->getCalculatedValue();
                    $tipoClien  = $objPHPExcel->getActiveSheet()->getCell('AK'.$i)->getCalculatedValue();
                    $planchas   = $objPHPExcel->getActiveSheet()->getCell('AN'.$i)->getCalculatedValue();
                    $combos     = $objPHPExcel->getActiveSheet()->getCell('AO'.$i)->getCalculatedValue();
                    $precXcombo = $objPHPExcel->getActiveSheet()->getCell('AP'.$i)->getCalculatedValue();
                    $precXplanc = $objPHPExcel->getActiveSheet()->getCell('AQ'.$i)->getCalculatedValue();
                    $precXtodo  = $objPHPExcel->getActiveSheet()->getCell('AR'.$i)->getCalculatedValue();

                    if($tipoClien == "Puesto de mercado"){
                        $tipoClien = "PDM";
                    }else if($tipoClien == "Bodegas"){
                        $tipoClien = "Bodega";
                    }



                    if($mes != null){
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
                        
                        if($planchas == null){
                            $planchas = 0;
                        }

                        // VERIFICAR SI EXISTE EL USUARIO
                        $distribuidor = usuusuarios::where('tpuid', $tpuid)
                                                    ->where('perid', $perid)
                                                    ->first(['usuid', 'estid']);
                        $usuid = 0;
                        if($distribuidor){
                            $usuid = $distribuidor->usuid;

                            if($distribuidor->estid == 2 ){ //SI EL ESTADO ES 2 DE DESACTIVADO CAMBIARLO A 1 DE ACTIVADO
                                $distribuidor->estid = 1;
                                if($distribuidor->update()){

                                }else{

                                }
                            }

                        }else{
                            $nuevoUsuario = new usuusuarios;
                            $nuevoUsuario->tpuid         = $tpuid;
                            $nuevoUsuario->perid         = $perid;
                            $nuevoUsuario->estid         = 1;
                            $nuevoUsuario->ususoldto     = null;
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
                        $usuCliente = usuusuarios::where('tpuid', 2)
                                                    // ->where('perid', $clienteperid)
                                                    ->where('ususoldto', $soldTo)
                                                    ->first(['usuid', 'estid']);
                        $clienteusuid = 0;
                        $sucursalClienteId = 0;
                        if($usuCliente){
                            $clienteusuid = $usuCliente->usuid;
                            
                            if($usuCliente->estid == 2){
                                $usuCliente->estid = 1;
                                if($usuCliente->update()){

                                }else{
                                    
                                }
                            }else{

                            }

                            $sucursalesCliente = ussusuariossucursales::where('usuid', $clienteusuid)->first(['sucid']);
                            if($sucursalesCliente){
                                $sucursalClienteId = $sucursalesCliente->sucid;
                            }else{
                                $nuevaSucursal = new sucsucursales;
                                $nuevaSucursal->sucnombre = $cliente;
                                if($nuevaSucursal->save()){
                                    $sucursalClienteId = $nuevaSucursal->sucid;

                                    $sucursalUsuario = new ussusuariossucursales;
                                    $sucursalUsuario->usuid = $clienteusuid;
                                    $sucursalUsuario->suci  = $sucursalClienteId;
                                    if($sucursalUsuario->save()){

                                    }else{

                                    }

                                }else{

                                }
                            }

                        }else{
                            $clienteNuevoUsuario = new usuusuarios;
                            $clienteNuevoUsuario->tpuid         = 2; // tipo de usuario (cliente)
                            $clienteNuevoUsuario->perid         = $clienteperid;
                            $clienteNuevoUsuario->estid         = 1;
                            $clienteNuevoUsuario->ususoldto     = $soldTo;
                            $clienteNuevoUsuario->usuusuario    = null;
                            $clienteNuevoUsuario->usucorreo     = null;
                            $clienteNuevoUsuario->usucontrasena = null;
                            $clienteNuevoUsuario->usutoken      = Str::random(60);
                            if($clienteNuevoUsuario->save()){
                                $clienteusuid = $clienteNuevoUsuario->usuid;
                                $nuevaSucursal = new sucsucursales;
                                $nuevaSucursal->sucnombre = $cliente;
                                if($nuevaSucursal->save()){
                                    $sucursalClienteId = $nuevaSucursal->sucid;

                                    $sucursalUsuario = new ussusuariossucursales;
                                    $sucursalUsuario->usuid = $clienteusuid;
                                    $sucursalUsuario->sucid = $sucursalClienteId;
                                    if($sucursalUsuario->save()){

                                    }else{

                                    }

                                }else{

                                }
                            }else{
            
                            }
                        }
                        



                        $catcategoria = catcategorias::where('catnombre', $categoria)
                                                        ->first(['catid']);
                        
                        $catid = 0;
                        if($catcategoria){
                            $catid = $catcategoria->catid;
                        }else{
                            $nuevacategoria                       = new catcategorias;
                            $nuevacategoria->catnombre            = $categoria;
                            $nuevacategoria->catimagenfondo       = env('APP_URL').'/Sistema/abs/img/nohay.png';
                            $nuevacategoria->caticono             = env('APP_URL').'/Sistema/abs/img/nohay.png';
                            $nuevacategoria->catcolorhover        = '';
                            $nuevacategoria->catcolor             = '';
                            $nuevacategoria->caticonoseleccionado = env('APP_URL').'/Sistema/abs/img/nohay.png';
                            $nuevacategoria->caticonohover        = env('APP_URL').'/Sistema/abs/img/nohay.png';
                            if($nuevacategoria->save()){
                                $catid = $nuevacategoria->catid;
                            }else{
            
                            }
                        }

                        $scasucursalescategorias = scasucursalescategorias::where('fecid', $fecid)
                                                                        ->where('catid', $catid)
                                                                        ->where('sucid', $sucursalClienteId)
                                                                        ->where('tsuid', null)
                                                                        ->first(['scaid']);
                        
                        $scaid = 0;
                        if($scasucursalescategorias){
                            $scaid = $scasucursalescategorias->scaid;
                        }else{
                            $nuevoSca = new scasucursalescategorias;
                            $nuevoSca->sucid    = $sucursalClienteId;
                            $nuevoSca->catid    = $catid;
                            $nuevoSca->fecid    = $fecid;
                            $nuevoSca->tsuid    = null;
                            $nuevoSca->scavalorizadoobjetivo = null;
                            $nuevoSca->scavalorizadoreal     = null;
                            $nuevoSca->scavalorizadotogo     = null;
                            if($nuevoSca->save()){
                                $scaid = $nuevoSca->scaid;
                            }else{

                            }
                        }

                        // Sacando los espacios del ultimo digito y limpiando caracteres
                        $catEspa = substr($categoria, -1, 1);
                        $nuevonombrecategoria = "";
                        if($catEspa == " "){
                            $nuevonombrecategoria = substr($categoria, 0, strlen($categoria)-1);
                        }else{
                            $nuevonombrecategoria = $categoria;
                        }

                        // 
                        $tpclEspa = substr($tipoClien, -1, 1);
                        $nuevonombretipocliente = "";
                        if($tpclEspa == " "){
                            $nuevonombretipocliente = substr($tipoClien, 0, strlen($tipoClien)-1);
                        }else{
                            $nuevonombretipocliente = $tipoClien;
                        }

                        // 
                        $codprEspa = substr($codPromoc, -1, 1);
                        $nuevonombrecodpromoc = "";
                        if($codprEspa == " "){
                            $nuevonombrecodpromoc = substr($codPromoc, 0, strlen($codPromoc)-1);
                        }else{
                            $nuevonombrecodpromoc = $codPromoc;
                        }

                        $nuevonombrecodpromoc = str_replace("/", "", $nuevonombrecodpromoc);

                        // 
                        $productopptEspa = substr($productoPpt, -1, 1);
                        $nuevonombreproductoppt = "";
                        if($productopptEspa == " "){
                            $nuevonombreproductoppt = substr($productoPpt, 0, strlen($productoPpt)-1);
                        }else{
                            $nuevonombreproductoppt = $productoPpt;
                        }

                        $nuevonombreproductoppt = str_replace("/", "", $nuevonombreproductoppt);

                        // 
                        $productobonipptEspa = substr($proBoniPpt, -1, 1);
                        $nuevonombreprobonippt = "";
                        if($productobonipptEspa == " "){
                            $nuevonombreprobonippt = substr($proBoniPpt, 0, strlen($proBoniPpt)-1);
                        }else{
                            $nuevonombreprobonippt = $proBoniPpt;
                        }

                        $nuevonombreprobonippt = str_replace("/", "", $nuevonombreprobonippt);

                        // Sacando los espacios del ultimo digito y limpiando caracteres

            
                        // VERIFICAR SI EL PRODUCTO ESTA REGISTRADO
                        $proproducto = proproductos::where('prosku', $sku)
                                                    ->first(['proid', 'proimagen']);
                        
                        $proid = 0;
                        if($proproducto){
                            $proid = $proproducto->proid;


                            if($proproducto->proimagen == env('APP_URL').'/Sistema/promociones/'.strtoupper($nuevonombrecategoria).'/'.strtoupper($nuevonombretipocliente).'/'.strtoupper($nuevonombrecodpromoc).'/'.$nuevonombreproductoppt.'.png'){
                                
                            }else{
                                $proproducto->proimagen = env('APP_URL').'/Sistema/promociones/'.strtoupper($nuevonombrecategoria).'/'.strtoupper($nuevonombretipocliente).'/'.strtoupper($nuevonombrecodpromoc).'/'.$nuevonombreproductoppt.'.png';
                                if($proproducto->update()){

                                }
                            }

                        }else{
                            $nuevoProducto = new proproductos;
                            $nuevoProducto->catid     = $catid;
                            $nuevoProducto->prosku    = $sku;
                            $nuevoProducto->pronombre = $producto;

                            // ARMAR UBICACION DE LA IMAGEN

                            $nuevoProducto->proimagen = env('APP_URL').'/Sistema/promociones/'.strtoupper($nuevonombrecategoria).'/'.strtoupper($nuevonombretipocliente).'/'.strtoupper($nuevonombrecodpromoc).'/'.$nuevonombreproductoppt.'.png';

                            /*************************** */
                            if($nuevoProducto->save()){
                                $proid = $nuevoProducto->proid;
                            }else{
            
                            }
                        }
            
                        // VERIFICAR SI EL PRODUCTO BONIFICADO ESTA REGISTRADO
                        $proproductoBonificado = proproductos::where('prosku', $skuBonifi)
                                                            ->first(['proid', 'proimagen']);
                        
                        $bonificadoproid = 0;
                        if($proproductoBonificado){
                            $bonificadoproid = $proproductoBonificado->proid;

                            if($proproductoBonificado->proimagen == env('APP_URL').'/Sistema/promociones/'.strtoupper($nuevonombrecategoria).'/'.strtoupper($nuevonombretipocliente).'/'.strtoupper($nuevonombrecodpromoc).'/'.$nuevonombreprobonippt.' - Gratis.png' ){

                            }else{
                                $proproductoBonificado->proimagen = env('APP_URL').'/Sistema/promociones/'.strtoupper($nuevonombrecategoria).'/'.strtoupper($nuevonombretipocliente).'/'.strtoupper($nuevonombrecodpromoc).'/'.$nuevonombreprobonippt.' - Gratis.png';
                                if($proproductoBonificado->update()){

                                }else{
                                    
                                }
                            }

                        }else{
                            $nuevoProductoBonificado = new proproductos;
                            $nuevoProductoBonificado->catid     = $catid;
                            $nuevoProductoBonificado->prosku    = $skuBonifi;
                            $nuevoProductoBonificado->pronombre = $productoBo;
                            $nuevoProductoBonificado->proimagen = env('APP_URL').'/Sistema/promociones/'.strtoupper($nuevonombrecategoria).'/'.strtoupper($nuevonombretipocliente).'/'.strtoupper($nuevonombrecodpromoc).'/'.$nuevonombreprobonippt.' - Gratis.png';
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

                        $csc = csccanalessucursalescategorias::where('canid', $canid)
                                                        ->where('scaid', $scaid)
                                                        ->where('fecid', $fecid)
                                                        ->first(['cscid']);
                        $cscid = 0;
                        if($csc){
                            $cscid = $csc->cscid;
                        }else{
                            $nuevoCsc = new csccanalessucursalescategorias;
                            $nuevoCsc->canid = $canid;
                            $nuevoCsc->scaid = $scaid;
                            $nuevoCsc->fecid = $fecid;
                            if($nuevoCsc->save()){
                                $cscid = $nuevoCsc->cscid;
                            }else{

                            }
                        }

                        $prm = prmpromociones::where('tprid', $tprid)
                                        ->where('prmcodigo', $codPromoc)
                                        ->where('prmmecanica', $mecanica)
                                        ->where('prmaccion', $accion)
                                        ->first(['prmid']);

                        $prmid = 0;
                        if($prm){
                            $prmid = $prm->prmid;

                        }else{
                            $nuevoPrm = new prmpromociones;
                            $nuevoPrm->tprid                = $tprid;
                            $nuevoPrm->prmcodigo            = $codPromoc;
                            // $nuevoPrm->prmcantidadcombo     = $combos;
                            $nuevoPrm->prmmecanica          = $mecanica;
                            // $nuevoPrm->prmcantidadplancha   = $planchas;
                            // $nuevoPrm->prmtotalcombo        = $precXcombo;
                            // $nuevoPrm->prmtotalplancha      = $precXplanc;
                            // $nuevoPrm->prmtotal             = $precXtodo;
                            $nuevoPrm->prmaccion            = $accion;
                            if($nuevoPrm->save()){
                                $prmid = $nuevoPrm->prmid;
                            }else{

                            }
                        }

                        $prb = prbpromocionesbonificaciones::where('prmid', $prmid)
                                                        ->where('proid', $bonificadoproid) 
                                                        ->where('prbcodigoprincipal', $codPrinci) 
                                                        ->first(['prbid']);

                        if($prb){

                        }else{
                            $nuevoPrb = new prbpromocionesbonificaciones;
                            $nuevoPrb->prmid                = $prmid;
                            $nuevoPrb->proid                = $bonificadoproid;
                            $nuevoPrb->prbcantidad          = $cantBonifi;
                            $nuevoPrb->prbproductoppt       = $proBoniPpt;
                            $nuevoPrb->prbcomprappt         = $compBonPpt;
                            $nuevoPrb->prbcodigoprincipal   = $codPrinci;
                            $nuevoPrb->prbimagen            = env('APP_URL').'/Sistema/promociones/'.strtoupper($nuevonombrecategoria).'/'.strtoupper($nuevonombretipocliente).'/'.strtoupper($nuevonombrecodpromoc).'/'.$nuevonombreprobonippt.' - Gratis.png';

                            if($nuevoPrb->save()){
                                $prp = prppromocionesproductos::where('prmid', $prmid)
                                                                ->where('proid', $proid)
                                                                ->where('prpcodigoprincipal', $codPrinci)
                                                                ->first(['prpid']);

                                if($prp){
                                    
                                }else{
                                    $nuevoPrp = new prppromocionesproductos;
                                    $nuevoPrp->prmid                = $prmid;
                                    $nuevoPrp->proid                = $proid;
                                    $nuevoPrp->prpcantidad          = $cantCompra;
                                    $nuevoPrp->prpproductoppt       = $productoPpt;
                                    $nuevoPrp->prpcomprappt         = $compraPpt;
                                    $nuevoPrp->prpcodigoprincipal   = $codPrinci;
                                    $nuevoPrp->prpimagen            = env('APP_URL').'/Sistema/promociones/'.strtoupper($nuevonombrecategoria).'/'.strtoupper($nuevonombretipocliente).'/'.strtoupper($nuevonombrecodpromoc).'/'.$nuevonombreproductoppt.'.png';
                                    if($nuevoPrp->save()){
                                        $csp = cspcanalessucursalespromociones::where('cscid', $cscid )
                                                                ->where('fecid', $fecid)
                                                                ->where('prmid', $prmid)
                                                                ->first(['cspid', 'cspcantidadcombo', 'cspcantidadplancha']);

                                        $cspid = 0;
                                        if($csp){
                                            
                                            $cspid = $csp->cspid;
                                            // SI EL CODIGO DE LA PROMOCION SE REPITE SUMAR LA CANTIDAD DE COMBOS Y PLANCHAS
                                            $csp->cspcantidadcombo   = $csp->cspcantidadcombo + $combos;
                                            $csp->cspcantidadplancha = $csp->cspcantidadplancha + $planchas;
                                            if($csp->update()){

                                            }else{
                                                
                                            }

                                        }else{
                                            $nuevoCsp = new cspcanalessucursalespromociones;
                                            $nuevoCsp->cscid                = $cscid;
                                            $nuevoCsp->fecid                = $fecid;
                                            $nuevoCsp->prmid                = $prmid;
                                            // $nuevoCsp->cspcodigoprincipal   = $codPrinci;
                                            $nuevoCsp->cspvalorizado        = 0;
                                            $nuevoCsp->cspplanchas          = 0;
                                            $nuevoCsp->cspcompletado        = 0;

                                            $nuevoCsp->cspcantidadcombo     = $combos;
                                            $nuevoCsp->cspcantidadplancha   = $planchas;
                                            $nuevoCsp->csptotalcombo        = $precXcombo;
                                            $nuevoCsp->csptotalplancha      = $precXplanc;
                                            $nuevoCsp->csptotal             = $precXtodo;

                                            if($nuevoCsp->save()){
                                                $cspid = $nuevoCsp->cspid;
                                            }else{
                                                
                                            }
                                        }
                                    }else{

                                    }
                                }
                            }else{

                            }
                        }    
                    }
        
                    
                }                
            
            } else {
                
            }
            date_default_timezone_set("America/Lima");
            $fechaActual = date('Y-m-d H:i:s');

            $nuevoCargaArchivo = new carcargasarchivos;
            $nuevoCargaArchivo->tcaid = 1;
            $nuevoCargaArchivo->fecid = $fecid;
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
            "numeroCelda"    => $numeroCelda
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            $usuusuario->usuid,
            null,
            $fichero_subido,
            $requestsalida,
            'CARGAR DATA DE PROMOCIONES DE UN EXCEL AL SISTEMA',
            'IMPORTAR',
            '/cargarArchivo/promociones', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;

    }
}
