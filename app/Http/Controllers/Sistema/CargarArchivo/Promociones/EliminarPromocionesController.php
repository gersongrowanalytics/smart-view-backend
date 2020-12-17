<?php

namespace App\Http\Controllers\Sistema\CargarArchivo\Promociones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EliminarPromocionesController extends Controller
{
    public function CargarArchivoEliminarPromociones(Request $request)
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

        $cargarData = false;
        
        $usuusuario = usuusuarios::join('tputiposusuarios as tpu', 'tpu.tpuid', 'usuusuarios.tpuid')
                                ->where('usuusuarios.usutoken', $usutoken)
                                ->first([
                                    'usuusuarios.usuid', 
                                    'usuusuarios.tpuid', 
                                    'tpu.tpuprivilegio'
                                ]);
        if($usuusuario->tpuprivilegio == "todo"){
            $cargarData = true;
        }else{
            $tup = tuptiposusuariospermisos::join('pempermisos as pem', 'pem.pemid', 'tuptiposusuariospermisos.pemid')
                                            ->where('tuptiposusuariospermisos.tpuid', $usuusuario->tpuid)
                                            ->where('pem.pemslug', "cargar.data.servidor")
                                            ->first([
                                                'tuptiposusuariospermisos.tpuid'
                                            ]);

            if($tup){
                $cargarData = true;
            }else{
                $cargarData = false;
            }
        }
        

        $fichero_subido = '';

        $pkid = 0;
        $log  = array(
            "NO_EXISTE_PERSONA_EJECUTIVO" => [],
            "NO_EXISTE_USUARIO_EJECUTIVO" => [],
            "NO_EXISTE_PERSONA_CLIENTE" => [],
            "NO_EXISTE_USUARIO_CLIENTE" => [],

            "NO_EXISTE_CATEGORIA" => [],
            "NO_EXISTE_CATEGORIA_ASIGNADA" => [],
            "NO_EXISTE_PRODUCTO" => [],
            "NO_EXISTE_PRODUCTO_BONIFICADO" => [],
            "NO_EXISTE_TIPO_PROMOCION" => [],
            "NO_EXISTE_CANAL" => [],
            "NO_EXISTE_CANAL_ASIGNADO" => [],
            "NO_EXISTE_PROMOCION_ASIGNADA" => [],


            "NUEVA_PERSONA_EJECUTIVO"      => [],
            "NUEVA_PERSONA_CLIENTE"        => [],
            "NUEVO_USUARIO_EJECUTIVO"      => [],
            "NUEVO_USUARIO_CLIENTE"        => [],
            "NUEVO_PROMOCIONES_ASIGNDADAS" => [],
            "NUEVO_PRP_CREADO"             => [],
            "NUEVO_PRB_CREADO"             => [],
            "NUEVO_PROMOCION_CREADO"       => [],
            "NUEVO_CANAL_ASIGNADO"         => [],
            "NUEVO_CATEGORIA_ASIGNADO"     => [],
            "NUEVA_SUCURSAL"               => []
        );

        $fecActual = new \DateTime(date("Y-m-d", strtotime("2020-10-20")));

        try{
            // file_put_contents(base_path().'/public/'.$archivo, $_FILES['file']['tmp_name']);
            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/promociones/desactivarPromociones/'.basename($_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {
                $fecid = 0;

                $objPHPExcel    = IOFactory::load($fichero_subido);
                $objPHPExcel->setActiveSheetIndex(0);
                $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();

                if($cargarData == true){
                    for ($i=2; $i <= $numRows ; $i++) {
                        // $ano = '2020';
                        $dia = '01';
            
                        $ano        = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                        $mesTxt     = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
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
                        $proBoniPpt = $objPHPExcel->getActiveSheet()->getCell('AF'.$i)->getCalculatedValue();
                        $compBonPpt = $objPHPExcel->getActiveSheet()->getCell('AG'.$i)->getCalculatedValue();
                        /***/
    
                        $tipoPromo  = $objPHPExcel->getActiveSheet()->getCell('AH'.$i)->getCalculatedValue();
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
    
    
    
                        if($mesTxt != null){
                            $fecfecha = fecfechas::where('fecdia', $dia)
                                            ->where('fecmes', $mesTxt)
                                            ->where('fecano', $ano)
                                            ->first(['fecid']);
                            $fecid = 0;
                            if($fecfecha){
                                $fecid = $fecfecha->fecid;
                            }else{
                                $mes = "0";
                                if($mesTxt == "ENE"){
                                    $mes = "01";
                                }else if($mesTxt == "FEB"){
                                    $mes = "02";
                                }else if($mesTxt == "MAR"){
                                    $mes = "03";
                                }else if($mesTxt == "ABR"){
                                    $mes = "04";
                                }else if($mesTxt == "MAY"){
                                    $mes = "05";
                                }else if($mesTxt == "JUN"){
                                    $mes = "06";
                                }else if($mesTxt == "JUL"){
                                    $mes = "07";
                                }else if($mesTxt == "AGO"){
                                    $mes = "08";
                                }else if($mesTxt == "SET"){
                                    $mes = "09";
                                }else if($mesTxt == "OCT"){
                                    $mes = "10";
                                }else if($mesTxt == "NOV"){
                                    $mes = "11";
                                }else if($mesTxt == "DIC"){
                                    $mes = "12";
                                }
    
                                $nuevaFecha = new fecfechas;
                                $nuevaFecha->fecfecha = new \DateTime(date("Y-m-d", strtotime($ano.'-'.$mes.'-'.$dia)));
                                $nuevaFecha->fecdia   = $dia;
                                $nuevaFecha->fecmes   = $mesTxt;
                                $nuevaFecha->fecmesnumero = $mes;
                                $nuevaFecha->fecano   = $ano;
                                if($nuevaFecha->save()){
                                    $fecid = $nuevaFecha->fecid;
                                }else{
                
                                }
                            }
                
                           
                
                            // VERIFICAR SI EXISTE LA PERSONA
                            $perpersona = perpersonas::where('pernombrecompleto', $ejecutivo)->first(['perid']);
                            $perid = 0;
                            if($perpersona){
                                $perid = $perpersona->perid;
                            }else{
                                $log['NO_EXISTE_PERSONA_EJECUTIVO'][] = "Linea Excel: ".$i." EJECUTIVO: ".$ejecutivo;
                            }
                            
                            if($planchas == null){
                                $planchas = 0;
                            }
    
    
                            $distribuidor = usuusuarios::where('perid', $perid)
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
                                $log['NO_EXISTE_USUARIO_EJECUTIVO'][] = "Linea Excel: ".$i." PERID: ".$perid;
                            }
                
                            // VERIFICAR SI EXISTE LA PERSONA PARA EL CLIENTE
                            $clienteperpersona = perpersonas::where('pernombrecompleto', $cliente)->first(['perid']);
                            $clienteperid = 0;
                            if($clienteperpersona){
                                $clienteperid = $clienteperpersona->perid;
                            }else{
                                $log['NO_EXISTE_PERSONA_CLIENTE'][] = "Linea Excel: ".$i." CLIENTE: ".$cliente;
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
                                    
                                }
    
                            }else{
                                $log['NO_EXISTE_USUARIO_CLIENTE'][] = "Linea Excel: ".$i." SOLD TO: ".$soldTo;
                            }
                            
    
    
    
                            $catcategoria = catcategorias::where('catnombre', $categoria)
                                                            ->first(['catid']);
                            
                            $catid = 0;
                            if($catcategoria){
                                $catid = $catcategoria->catid;
                            }else{

                                $catid = 0;

                                $log['NO_EXISTE_CATEGORIA'][] = "Linea Excel: ".$i." categoria: ".$categoria;
                                
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
                                $log['NO_EXISTE_CATEGORIA_ASIGNADA'][] = "Linea Excel: ".$i." FECID: ".$fecid." CATID: ".$catid." SUCURSAL: ".$sucursalClienteId;
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
    
                            }else{
                                $log['NO_EXISTE_PRODUCTO'][] = "Linea Excel: ".$i." SKU: ".$sku;
                            }
                
                            // VERIFICAR SI EL PRODUCTO BONIFICADO ESTA REGISTRADO
                            $proproductoBonificado = proproductos::where('prosku', $skuBonifi)
                                                                ->first(['proid', 'proimagen']);
                            
                            $bonificadoproid = 0;
                            if($proproductoBonificado){
                                $bonificadoproid = $proproductoBonificado->proid;
    
                            }else{
                                $log['NO_EXISTE_PRODUCTO_BONIFICADO'][] = "Linea Excel: ".$i." SKU: ".$sku;
                            }
                
                            // VERIFICAR SI EXISTE EL TIPO DE PROMOCION
                            $tprtipopromocion = tprtipospromociones::where('tprnombre', $tipoPromo)->first(['tprid']);
                            $tprid = 0;
                            if($tprtipopromocion){
                                $tprid = $tprtipopromocion->tprid;
                            }else{
                                $log['NO_EXISTE_TIPO_PROMOCION'][] = "Linea Excel: ".$i." TIPO DE PROMOCION: ".$tipoPromo;
                            }
                
                            // VERIFICAR SI EXISTE EL CANAL O TIPO DE CLIENTE
                            $cancanal = cancanales::where('cannombre', $tipoClien)->first(['canid']);
                
                            $canid = 0;
                            if($cancanal){
                                $canid = $cancanal->canid;
                            }else{
                                $log['NO_EXISTE_CANAL'][] = "Linea Excel: ".$i." CANAL: ".$tipoClien;
                            }
    
                            $csc = csccanalessucursalescategorias::where('canid', $canid)
                                                            ->where('scaid', $scaid)
                                                            ->where('fecid', $fecid)
                                                            ->first(['cscid']);
                            $cscid = 0;
                            if($csc){
                                $cscid = $csc->cscid;
                            }else{
                                $log['NO_EXISTE_CANAL_ASIGNADO'][] = "Linea Excel: ".$i." CANID: ".$canid." SCAID: ".$scaid." FECID: ".$fecid;
                            }
    
                            $prm = prmpromociones::where('tprid', $tprid)
                                            ->where('prmcodigo', $codPromoc)
                                            ->where('prmmecanica', $mecanica)
                                            ->where('prmaccion', $accion)
                                            ->where('fecid', $fecid)
                                            ->where('prmcodigoprincipal', $codPrinci)
                                            ->first(['prmid']);
    
                            $prmid = 0;
                            if($prm){
                                $prmid = $prm->prmid;
    
                            }else{
                                $log['NO_EXISTE_PROMOCION_ASIGNADA'][] = "Linea Excel: ".$i." tprid:".$tprid." CODIGO PROMOCION: ".$codPromoc." CODIGOPRINCIPAL: ".$codPrinci;
                            }
    
                            $prb = prbpromocionesbonificaciones::where('prmid', $prmid)
                                                            ->where('proid', $bonificadoproid) 
                                                            ->where('prbcodigoprincipal', $codPrinci) 
                                                            ->where('prbcomprappt', $compBonPpt) 
                                                            ->where('prbproductoppt', $proBoniPpt) 
                                                            ->where('prbcantidad', $cantBonifi) 
                                                            ->first(['prbid']);
    
                            $prbid = 0;
                            if($prb){
                                $prbid = $prb->prbid;
                            }else{
                                
                            }
    
                            
                            $prp = prppromocionesproductos::where('prmid', $prmid)
                                                            ->where('proid', $proid)
                                                            ->where('prpcodigoprincipal', $codPrinci)
                                                            ->where('prpcomprappt', $compraPpt)
                                                            ->where('prpproductoppt', $productoPpt)
                                                            ->where('prpcantidad', $cantCompra)
                                                            ->first(['prpid']);
                            $prpid = 0;
                            if($prp){
                                $prpid = $prp->prpid;
                            }else{
                                
                            }
    
                            $csp = cspcanalessucursalespromociones::join('prmpromociones as prm', 'prm.prmid', 'cspcanalessucursalespromociones.prmid')
                                                            ->where('cspcanalessucursalespromociones.cscid', $cscid)
                                                            ->where('cspcanalessucursalespromociones.fecid', $fecid)
                                                            // ->where('cspcanalessucursalespromociones.prmid', $prmid)
                                                            ->where('prm.prmcodigo', $codPromoc)
                                                            ->first([
                                                                'cspcanalessucursalespromociones.cspid', 
                                                                'cspcanalessucursalespromociones.cspcantidadcombo', 
                                                                'cspcanalessucursalespromociones.cspcantidadplancha', 
                                                                'cspcanalessucursalespromociones.created_at'
                                                            ]);
    
                            $cspid = 0;
                            if($csp){
                                
                                $cspid = $csp->cspid;
                                $csp->cspestado = 0;
                                $csp->update();

                                
                            }else{
                                
                            }
                        }
            
                        
                    } 
                }else{
                    date_default_timezone_set("America/Lima");
                    $anioActual = date('Y');
                    $mesActual  = date('m');
                    $diaActual  = '01';

                    $fecfecha = fecfechas::where('fecdia', $diaActual)
                                        ->where('fecmesnumero', $mesActual)
                                        ->where('fecano', $anioActual)
                                        ->first(['fecid']);

                    $fecid = 0;
                    if($fecfecha){
                        $fecid = $fecfecha->fecid;
                    }else{
                        $mesTxt = "";

                        if($mesActual == "01"){
                            $mesTxt = "ENE";
                        }else if($mesActual == "02"){
                            $mesTxt = "FEB";
                        }else if($mesActual == "03"){
                            $mesTxt = "MAR";
                        }else if($mesActual == "04"){
                            $mesTxt = "ABR";
                        }else if($mesActual == "05"){
                            $mesTxt = "MAY";
                        }else if($mesActual == "06"){
                            $mesTxt = "JUN";
                        }else if($mesActual == "07"){
                            $mesTxt = "JUL";
                        }else if($mesActual == "08"){
                            $mesTxt = "AGO";
                        }else if($mesActual == "09"){
                            $mesTxt = "SET";
                        }else if($mesActual == "10"){
                            $mesTxt = "OCT";
                        }else if($mesActual == "11"){
                            $mesTxt = "NOV";
                        }else if($mesActual == "12"){
                            $mesTxt = "DIC";
                        }

                        $nuevaFecha = new fecfechas;
                        $nuevaFecha->fecfecha     = new \DateTime(date("Y-m-d", strtotime($anioActual.'-'.$mesActual.'-'.$diaActual)));
                        $nuevaFecha->fecdia       = $diaActual;
                        $nuevaFecha->fecmes       = $mesTxt;
                        $nuevaFecha->fecmesnumero = $mesActual;
                        $nuevaFecha->fecano       = $anioActual;
                        if($nuevaFecha->save()){
                            $fecid = $nuevaFecha->fecid;
                        }else{
        
                        }
                    }
                }
            
            } else {
                
            }

            $nuevoCargaArchivo = new carcargasarchivos;
            $nuevoCargaArchivo->tcaid            = 11;
            $nuevoCargaArchivo->fecid            = $fecid;
            $nuevoCargaArchivo->usuid            = $usuusuario->usuid;
            $nuevoCargaArchivo->carnombrearchivo = $archivo;
            $nuevoCargaArchivo->carubicacion     = $fichero_subido;
            $nuevoCargaArchivo->carexito         = $cargarData;
            $nuevoCargaArchivo->carurl           = env('APP_URL').'/Sistema/cargaArchivos/promociones/desactivarPromociones/'.$archivo;
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
            "numeroCelda"    => $numeroCelda,
            "log"    => $log,
        ]);
        
        $descripcion = "CARGAR DATA DE PROMOCIONES DESACTIVADAS DE UN EXCEL AL SISTEMA";

        if($cargarData == false){
            $descripcion = "SUBIR EXCEL PARA REVISAR Y POSTERIORMENTE CARGAR DICHA DATA EN PROMOCIONES DESACTIVADAS";
        }

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            $usuusuario->usuid,
            null,
            $fichero_subido,
            $requestsalida,
            $descripcion,
            'IMPORTAR',
            '/cargarArchivo/promociones/desactivar', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;

    }
}
