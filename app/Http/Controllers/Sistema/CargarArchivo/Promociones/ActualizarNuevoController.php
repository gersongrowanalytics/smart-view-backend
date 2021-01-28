<?php

namespace App\Http\Controllers\Sistema\CargarArchivo\Promociones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
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
use App\tuptiposusuariospermisos;

class ActualizarNuevoController extends Controller
{
    public function ActualizarPromociones(Request $request)
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
            "SCA_ACTUALIZADO"      => [],
            "SCA_NO_SE_ACTUALIZO"      => [],
            "SCA_NO_SE_ECONTRO"      => [],
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
            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/promociones/eliminados/'.basename($_FILES['file']['name']);
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
                        $nuevoProm  = $objPHPExcel->getActiveSheet()->getCell('AS'.$i)->getCalculatedValue();
                        
                        if($nuevoProm == "x"){
                            $nuevoProm = 1;
                        }else{
                            $nuevoProm = 0;
                        }

                        if($tipoClien == "Puesto de mercado"){
                            $tipoClien = "PDM";
                        }else if($tipoClien == "Bodegas"){
                            $tipoClien = "Bodega";
                        }
    
                        $suce = sucsucursales::where('sucsoldto', $soldTo)
                                            ->where('sucestado', 0)
                                            ->first();
                        
                        if($suce){
                            if($suce->sucestado != 1){
                                $suce->sucestado = 1;
                                $suce->update();
                            }

                            $zone = zonzonas::find($suce->zonid);
                            if($zone){
                                if($zone->zonestado != 1){
                                    $zone->zonestado = 1;
                                    $zone->update();
                                }
                            }
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
                                
                            }
                
                          
                
                            // VERIFICAR SI EXISTE LA PERSONA
                            $perpersona = perpersonas::where('pernombrecompleto', $ejecutivo)->first(['perid']);
                            $perid = 0;
                            if($perpersona){
                                $perid = $perpersona->perid;
                            }else{
                               
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
                               
                            }
                
                            // VERIFICAR SI EXISTE LA PERSONA PARA EL CLIENTE
                            $clienteperpersona = perpersonas::where('pernombrecompleto', $cliente)->first(['perid']);
                            $clienteperid = 0;
                            if($clienteperpersona){
                                $clienteperid = $clienteperpersona->perid;
                            }else{
                               
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
                               
                            }
                            
    
    
    
                            $catcategoria = catcategorias::where('catnombre', $categoria)
                                                            ->first(['catid']);
                            
                            $catid = 0;
                            if($catcategoria){
                                $catid = $catcategoria->catid;
                            }else{

                                $catid = 0;
                            }
    
                            $scasucursalescategorias = scasucursalescategorias::where('fecid', $fecid)
                                                                            ->where('catid', $catid)
                                                                            ->where('sucid', $sucursalClienteId)
                                                                            ->where('tsuid', null)
                                                                            ->first(['scaid']);
                            
                            $scaid = 0;
                            if($scasucursalescategorias){
                                $scaid = $scasucursalescategorias->scaid;
                                $scasucursalescategorias->catid = 4;
                                if($scasucursalescategorias->update()){
                                    $log["SCA_ACTUALIZADO"][] = "EL SCA SE ACTUALIZO CORRECTAMENTE: ".$fecid." - ".$catid." - ".$sucursalClienteId;
                                }else{
                                    $log["SCA_NO_SE_ACTUALIZO"][] = "EL SCA SE ACTUALIZO CORRECTAMENTE: ".$fecid." - ".$catid." - ".$sucursalClienteId;
                                }
                            }else{
                                $log["SCA_NO_SE_ECONTRO"][] = "EL SCA SE ACTUALIZO CORRECTAMENTE: ".$fecid." - ".$catid." - ".$sucursalClienteId;
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
       
        
        
        return $requestsalida;

    }
}
