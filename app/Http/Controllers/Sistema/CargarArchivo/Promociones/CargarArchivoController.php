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

        $fichero_subido = '';
        try{
            // file_put_contents(base_path().'/public/'.$archivo, $_FILES['file']['tmp_name']);
            $fichero_subido = base_path().'/public/'.basename($_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {
                $objPHPExcel    = IOFactory::load($fichero_subido);
                $objPHPExcel->setActiveSheetIndex(0);
                $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();

                for ($i=3; $i <= $numRows ; $i++) {
                    $ano = '2020';
                    $dia = '01';
        
                    $mes        = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                    $subCanal   = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
                    $ejecutivo  = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
                    $soldTo     = $objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();
                    $cliente    = $objPHPExcel->getActiveSheet()->getCell('P'.$i)->getCalculatedValue();
                    $accion     = $objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getCalculatedValue();
                    $cantCompra = $objPHPExcel->getActiveSheet()->getCell('R'.$i)->getCalculatedValue();
                    $cantBonifi = $objPHPExcel->getActiveSheet()->getCell('S'.$i)->getCalculatedValue();
                    $mecanica   = $objPHPExcel->getActiveSheet()->getCell('U'.$i)->getCalculatedValue();
                    $categoria  = $objPHPExcel->getActiveSheet()->getCell('V'.$i)->getCalculatedValue();

                    /**NUEVOS CAMPOS */
                    $codPromoc  = $objPHPExcel->getActiveSheet()->getCell('W'.$i)->getCalculatedValue();
                    $codPrinci  = $objPHPExcel->getActiveSheet()->getCell('X'.$i)->getCalculatedValue();
                    /***/

                    $sku        = $objPHPExcel->getActiveSheet()->getCell('Y'.$i)->getCalculatedValue();
                    $producto   = $objPHPExcel->getActiveSheet()->getCell('Z'.$i)->getCalculatedValue();

                    /**NUEVOS CAMPOS */
                    $productoPpt = $objPHPExcel->getActiveSheet()->getCell('AA'.$i)->getCalculatedValue();
                    $compraPpt   = $objPHPExcel->getActiveSheet()->getCell('AB'.$i)->getCalculatedValue();
                    /***/

                    $skuBonifi  = $objPHPExcel->getActiveSheet()->getCell('AC'.$i)->getCalculatedValue();
                    $productoBo = $objPHPExcel->getActiveSheet()->getCell('AD'.$i)->getCalculatedValue();
                    /**NUEVOS CAMPOS */
                    $proBoniPpt = $objPHPExcel->getActiveSheet()->getCell('AE'.$i)->getCalculatedValue();
                    $compBonPpt = $objPHPExcel->getActiveSheet()->getCell('AF'.$i)->getCalculatedValue();
                    /***/
                    $tipoPromo  = $objPHPExcel->getActiveSheet()->getCell('AG'.$i)->getCalculatedValue();
                    $tipoClien  = $objPHPExcel->getActiveSheet()->getCell('AJ'.$i)->getCalculatedValue();
                    $planchas   = $objPHPExcel->getActiveSheet()->getCell('AM'.$i)->getCalculatedValue();
                    $combos     = $objPHPExcel->getActiveSheet()->getCell('AN'.$i)->getCalculatedValue();
                    $precXcombo = $objPHPExcel->getActiveSheet()->getCell('AO'.$i)->getCalculatedValue();
                    $precXplanc = $objPHPExcel->getActiveSheet()->getCell('AP'.$i)->getCalculatedValue();
                    $precXtodo  = $objPHPExcel->getActiveSheet()->getCell('AQ'.$i)->getCalculatedValue();


        
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
                    $usuCliente = usuusuarios::where('tpuid', 2)
                                                ->where('perid', $clienteperid)
                                                ->first(['usuid']);
                    $clienteusuid = 0;
                    $sucursalClienteId = 0;
                    if($usuCliente){
                        $clienteusuid = $usuCliente->usuid;
                        
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
                        $nuevoProducto->proimagen = env('APP_URL').'/Sistema/promociones/'.$categoria.'/'.$tipoClien.'/'.$codPromoc.'/'.$productoPpt.'.png';
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
                        $nuevoProductoBonificado->proimagen = env('APP_URL').'/Sistema/promociones/'.$categoria.'/'.$tipoClien.'/'.$codPromoc.'/'.$productoPpt.' - Gratis.png';
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
                                    ->where('prmcantidadcombo', $combos)
                                    ->where('prmcantidadplancha', $planchas)
                                    ->where('prmtotalcombo', $precXcombo)
                                    ->where('prmtotalplancha', $precXplanc)
                                    ->where('prmtotal', $precXtodo)
                                    ->where('prmaccion', $accion)
                                    ->first(['prmid']);

                    $prmid = 0;
                    if($prm){
                        $prmid = $prm->prmid;
                    }else{
                        $nuevoPrm = new prmpromociones;
                        $nuevoPrm->tprid                = $tprid;
                        $nuevoPrm->prmcodigo            = $codPromoc;
                        $nuevoPrm->prmcantidadcombo     = $combos;
                        $nuevoPrm->prmmecanica          = $mecanica;
                        $nuevoPrm->prmcantidadplancha   = $planchas;
                        $nuevoPrm->prmtotalcombo        = $precXcombo;
                        $nuevoPrm->prmtotalplancha      = $precXplanc;
                        $nuevoPrm->prmtotal             = $precXtodo;
                        $nuevoPrm->prmaccion            = $accion;
                        if($nuevoPrm->save()){
                            $prmid = $nuevoPrm->prmid;
                        }else{

                        }
                    }

                    $csp = cspcanalessucursalespromociones::where('cscid', $cscid )
                                                            ->where('fecid', $fecid)
                                                            ->where('prmid', $prmid)
                                                            ->where('cspcodigoprincipal', $codPrinci)
                                                            ->first(['cspid']);
                        
                    $cspid = 0;
                    if($csp){
                        $cspid = $csp->cspid;
                    }else{
                        $nuevoCsp = new cspcanalessucursalespromociones;
                        $nuevoCsp->cscid                = $cscid;
                        $nuevoCsp->fecid                = $fecid;
                        $nuevoCsp->prmid                = $prmid;
                        $nuevoCsp->cspcodigoprincipal   = $codPrinci;
                        $nuevoCsp->cspvalorizado        = 0;
                        $nuevoCsp->cspplanchas          = 0;
                        $nuevoCsp->cspcompletado        = 0;
                        if($nuevoCsp->save()){
                            $cspid = $nuevoCsp->cspid;
                        }else{
                            
                        }
                    }

                    $prb = prbpromocionesbonificaciones::where('prmid', $prmid)
                                                    ->where('prbproductoppt', $proBoniPpt)
                                                    ->where('prbcomprappt', $compBonPpt)
                                                    ->where('proid', $bonificadoproid)
                                                    ->where('prbcantidad', $cantBonifi)
                                                    ->first(['prbid']);

                    if($prb){

                    }else{
                        $nuevoPrb = new prbpromocionesbonificaciones;
                        $nuevoPrb->prmid            = $prmid;
                        $nuevoPrb->proid            = $bonificadoproid;
                        $nuevoPrb->prbcantidad      = $cantBonifi;
                        $nuevoPrb->prbproductoppt   = $proBoniPpt;
                        $nuevoPrb->prbcomprappt     = $compBonPpt;
                        if($nuevoPrb->save()){

                        }else{

                        }

                    }

                    $prp = prppromocionesproductos::where('prmid', $prmid)
                                                ->where('proid', $proid)
                                                ->where('prpproductoppt', $productoPpt)
                                                ->where('prpcomprappt', $compraPpt)
                                                ->where('prpcantidad', $cantCompra)
                                                ->first(['prpid']);

                    if($prp){
                        
                    }else{
                        $nuevoPrp = new prppromocionesproductos;
                        $nuevoPrp->prmid            = $prmid;
                        $nuevoPrp->proid            = $proid;
                        $nuevoPrp->prpcantidad      = $cantCompra;
                        $nuevoPrb->prpproductoppt   = $productoPpt;
                        $nuevoPrb->prpcomprappt     = $compraPpt;
                        if($nuevoPrp->save()){

                        }else{

                        }
                    }

                }                
            } else {
                
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
            "mensajedev"     => $mensajedev,
            "numeroCelda"    => $numeroCelda
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            null,
            null,
            $fichero_subido,
            $requestsalida,
            'CARGAR DATA DE UN EXCEL AL SISTEMA',
            'IMPORTAR',
            '', //ruta
            null
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;

    }
}
