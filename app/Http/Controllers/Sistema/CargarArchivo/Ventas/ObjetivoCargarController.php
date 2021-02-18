<?php

namespace App\Http\Controllers\Sistema\CargarArchivo\Ventas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\carcargasarchivos;
use App\tsutipospromocionessucursales;
use App\ussusuariossucursales;
use App\usuusuarios;
use App\scasucursalescategorias;
use App\fecfechas;
use App\perpersonas;
use App\sucsucursales;
use Illuminate\Support\Str;
use App\proproductos;
use App\catcategorias;
use App\tretiposrebates;
use App\tuptiposusuariospermisos;
use App\osiobjetivosssi;
use App\osoobjetivossso;
use Illuminate\Support\Facades\DB;

class ObjetivoCargarController extends Controller
{
    public function CargarObjetivo(Request $request)
    {
        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d H:i:s');

        $respuesta      = true;
        $mensaje        = '';
        $datos          = [];
        $skusNoExisten  = [];
        $soldtosNoExisten  = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;
        $numeroCelda    = 0;
        $usutoken       = $request->header('api_token');
        $archivo        = $_FILES['file']['name'];

        $fichero_subido = '';

        $pkid = 0;
        $log  = [];
        $observaciones  = [];

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

        DB::beginTransaction();
        try{

            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/objetivos/sellin/'.basename($usuusuario->usuid.'-'.$usuusuario->usuusuario.'-'.$fechaActual.'-'.$_FILES['file']['name']);

            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {
                $objPHPExcel    = IOFactory::load($fichero_subido);
                $objPHPExcel->setActiveSheetIndex(0);
                $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();

                if($cargarData == true){
                    for ($i=2; $i <= $numRows ; $i++) {
                        $dia = '01';

                        $ano         = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                        $mesTxt      = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                        $regionExcel      = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();

                        $soldto      = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                        $cliente     = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                        $grupoRebate = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                        $sector      = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                        $sku         = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
                        $producto    = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                        $objetivo    = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
            
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
                            $nuevaFecha->fecfecha     = new \DateTime(date("Y-m-d", strtotime($ano.'-'.$mes.'-'.$dia)));
                            $nuevaFecha->fecdia       = $dia;
                            $nuevaFecha->fecmesnumero = $mes;
                            $nuevaFecha->fecmes       = $mesTxt;
                            $nuevaFecha->fecano       = $ano;
                            if($nuevaFecha->save()){
                                $fecid = $nuevaFecha->fecid;
                            }else{
            
                            }
                        }


                        // $usuarioCliente = usuusuarios::join('ussusuariossucursales as uss', 'uss.usuid', 'usuusuarios.usuid')
                        //                                 ->where('usuusuarios.ususoldto', $soldto)
                        //                                 ->first(['uss.sucid']);  
                        // VERIFICAR SI EXISTE LA PERSONA PARA EL CLIENTE
                        // $clienteperpersona = perpersonas::where('pernombrecompleto', $cliente)->first(['perid']);
                        // $clienteperid = 0;
                        // if($clienteperpersona){
                        //     $clienteperid = $clienteperpersona->perid;
                        // }else{
                        //     $clienteNuevaPersona = new perpersonas;
                        //     $clienteNuevaPersona->tdiid    = 2;
                        //     $clienteNuevaPersona->pernombrecompleto = $cliente;
                        //     $clienteNuevaPersona->pernumerodocumentoidentidad = null;
                        //     $clienteNuevaPersona->pernombre = null;
                        //     $clienteNuevaPersona->perapellidopaterno   = null;
                        //     $clienteNuevaPersona->perapellidomaterno   = null;
                        //     if($clienteNuevaPersona->save()){
                        //         $clienteperid = $clienteNuevaPersona->perid;
                        //     }else{
            
                        //     }
                        // }

                        // VERIFICAR SI EXISTE EL USUARIO
                        // $usuCliente = usuusuarios::where('tpuid', 2)
                        //                             ->where('ususoldto', $soldto)
                        //                             ->first(['usuid']);
                        // $clienteusuid = 0;
                        // $sucursalClienteId = 0;
                        // if($usuCliente){
                        //     $clienteusuid = $usuCliente->usuid;
                            
                        //     $sucursalesCliente = ussusuariossucursales::where('usuid', $clienteusuid)->first(['sucid']);
                        //     if($sucursalesCliente){
                        //         $sucursalClienteId = $sucursalesCliente->sucid;
                        //     }else{
                        //         $nuevaSucursal = new sucsucursales;
                        //         $nuevaSucursal->sucnombre = $cliente;
                        //         if($nuevaSucursal->save()){
                        //             $sucursalClienteId = $nuevaSucursal->sucid;

                        //             $sucursalUsuario = new ussusuariossucursales;
                        //             $sucursalUsuario->usuid = $clienteusuid;
                        //             $sucursalUsuario->suci  = $sucursalClienteId;
                        //             if($sucursalUsuario->save()){

                        //             }else{

                        //             }

                        //         }else{

                        //         }
                        //     }

                        // }else{
                        //     $clienteNuevoUsuario = new usuusuarios;
                        //     $clienteNuevoUsuario->tpuid         = 2; // tipo de usuario (cliente)
                        //     $clienteNuevoUsuario->perid         = $clienteperid;
                        //     $clienteNuevoUsuario->ususoldto     = $soldto;
                        //     $clienteNuevoUsuario->usuusuario    = null;
                        //     $clienteNuevoUsuario->usucorreo     = null;
                        //     $clienteNuevoUsuario->usucontrasena = null;
                        //     $clienteNuevoUsuario->usutoken      = Str::random(60);
                        //     if($clienteNuevoUsuario->save()){
                        //         $clienteusuid = $clienteNuevoUsuario->usuid;
                        //         $nuevaSucursal = new sucsucursales;
                        //         $nuevaSucursal->sucnombre = $cliente;
                        //         if($nuevaSucursal->save()){
                        //             $sucursalClienteId = $nuevaSucursal->sucid;

                        //             $sucursalUsuario = new ussusuariossucursales;
                        //             $sucursalUsuario->usuid = $clienteusuid;
                        //             $sucursalUsuario->sucid = $sucursalClienteId;
                        //             if($sucursalUsuario->save()){

                        //             }else{

                        //             }

                        //         }else{

                        //         }
                        //     }else{
            
                        //     }
                        // }

                        if($i == 2){
                            $tsus = tsutipospromocionessucursales::where('fecid', $fecid)
                                                                ->where('tprid', 1)
                                                                ->get(['tsuid']);

                            foreach($tsus as $tsu){
                                $tsue = tsutipospromocionessucursales::find($tsu->tsuid);
                                $tsue->tsuvalorizadoobjetivo = 0;
                                if($tsue->update()){
                                    $scas = scasucursalescategorias::where('tsuid', $tsu->tsuid)
                                                                    ->update(['scavalorizadoobjetivo' => 0]);
                                }
                            }

                            osiobjetivosssi::where('fecid', $fecid)->update(['osivalorizado' => 0]);

                        }

                        $suc = sucsucursales::where('sucsoldto', $soldto)->first();
                        
                        if($suc){
                            $sucursalClienteId = $suc->sucid;
                            // OBTENER EL GRUPO REBATE
                            $treid = $suc->treid;
                            
                            if($suc->casid == 1){
                                if($regionExcel != "DTT1 LIMA"){
                                    $observaciones[] = "La sucursal: ".$suc->sucnombre." (".$soldto.") no coincide su región. La región del excel es: ".$regionExcel." la región de BD es: DTT1 LIMA";
                                }
                            }else if($suc->casid == 2){
                                if($regionExcel != "DTT2 PROVINCIAS"){
                                    $observaciones[] = "La sucursal: ".$suc->sucnombre." (".$soldto.") no coincide su región. La región del excel es: ".$regionExcel." la región de BD es: DTT2 PROVINCIAS";
                                }
                            }else{
                                $observaciones[] = "La sucursal: ".$suc->sucnombre." (".$soldto.") no coincide su región. La región del excel es: ".$regionExcel." la región de BD es: ".$suc->casid;
                            }
                            
                            $tsu = tsutipospromocionessucursales::where('fecid', $fecid)
                                                                ->where('sucid', $sucursalClienteId)
                                                                ->where('tprid', 1)
                                                                ->first([
                                                                    'tsuid', 
                                                                    'tsuvalorizadoobjetivo',
                                                                    'tsuvalorizadoreal'
                                                                ]);
                            $tsuid = 0;
                            if($tsu){
                                $tsuid = $tsu->tsuid;
    
                                $nuevoObjetivo = $tsu->tsuvalorizadoobjetivo+$objetivo;
    
                                if($nuevoObjetivo > 1){
                                    $porcentajeCumplimiento = (100 * $tsu->tsuvalorizadoreal)/$nuevoObjetivo;
                                }else{
                                    $porcentajeCumplimiento = $tsu->tsuvalorizadoreal;
                                }
    
                                $tsu->tsuvalorizadoobjetivo = $nuevoObjetivo;
                                $tsu->tsuvalorizadotogo = $nuevoObjetivo - $tsu->tsuvalorizadoreal;
                                $tsu->tsuporcentajecumplimiento = $porcentajeCumplimiento;
                                if($tsu->update()){
    
                                }else{
    
                                }
                            }else{
                                $nuevotsu = new tsutipospromocionessucursales;
                                $nuevotsu->fecid                     = $fecid;
                                $nuevotsu->sucid                     = $sucursalClienteId;
                                $nuevotsu->tprid                     = 1;
                                $nuevotsu->treid                     = $treid;
                                $nuevotsu->tsuporcentajecumplimiento = 0;
                                $nuevotsu->tsuvalorizadoobjetivo     = $objetivo;
                                $nuevotsu->tsuvalorizadoreal         = 0;
                                $nuevotsu->tsuvalorizadorebate       = 0;
                                $nuevotsu->tsuvalorizadotogo         = $objetivo;
                                if($nuevotsu->save()){
                                    $tsuid = $nuevotsu->tsuid;
                                }else{
    
                                }
                            }
    
    
                            $pro = proproductos::join('catcategorias as cat', 'cat.catid', 'proproductos.catid')
                                                ->where('proproductos.prosku', $sku)
                                                ->first([
                                                    'proproductos.proid',
                                                    'proproductos.catid',
                                                    'proproductos.pronombre',
                                                    'cat.catnombre'
                                                ]);
    
                            if($pro){
                                
                                $pos = strpos($pro->catnombre, $sector);

                                if($pos === false){
                                    $observaciones[] = "El producto: ".$pro->pronombre." (".$sku.") del excel no coincide con el de la BD. El excel tiene una categoria: ".$sector." y en la BD tiene una categoria: ".$pro->catnombre;
                                }

                                $osi = osiobjetivosssi::where('fecid', $fecid)
                                                    ->where('proid', $pro->proid)
                                                    ->where('sucid', $sucursalClienteId)
                                                    ->where('tpmid', 1)
                                                    ->first();
    
                                if($osi){
    
                                    $osi->osivalorizado = $objetivo + $osi->osivalorizado;
                                    $osi->update();
    
                                }else{
                                    $osin = new osiobjetivosssi;
                                    $osin->fecid         = $fecid;
                                    $osin->proid         = $pro->proid;
                                    $osin->sucid         = $sucursalClienteId;
                                    $osin->tpmid         = 1;
                                    $osin->osicantidad   = 0;
                                    $osin->osivalorizado = $objetivo;
                                    $osin->save();
                                }
    
    
    
    
                                $sca = scasucursalescategorias::where('fecid', $fecid)
                                                        ->where('sucid', $sucursalClienteId)
                                                        ->where('tsuid', $tsuid)
                                                        ->where('catid', $pro->catid)
                                                        ->first(['scaid', 'scavalorizadoobjetivo', 'scavalorizadoreal']);
    
                                $scaid = 0;
                                if($sca){
                                    $scaid = $sca->scaid;
                                    $nuevoObjetivoSca = $sca->scavalorizadoobjetivo+$objetivo;
                                    
                                    $sca->scavalorizadoobjetivo = $nuevoObjetivoSca;
                                    $sca->scavalorizadotogo = $nuevoObjetivoSca - $sca->scavalorizadoreal;
                                    if($sca->update()){
    
                                    }else{
    
                                    }
                                }else{
                                    $categoriaid     = $pro->catid;
                                    $categoriaNombre = $pro->catnombre;
    
                                    $scas = scasucursalescategorias::where('fecid', $fecid)
                                                        ->where('sucid', $sucursalClienteId)
                                                        ->where('tsuid', $tsuid)
                                                        ->get(['scaid', 'scavalorizadoobjetivo', 'catid']);
    
                                    $categorias = catcategorias::where('catid', '!=', 6)
                                                        ->get([
                                                            'catid',
                                                            'catnombre'
                                                        ]);
    
                                    foreach($categorias as $categoria){
                                        if($categoriaid == $categoria->catid ){
                                            $nuevosca = new scasucursalescategorias;
                                            $nuevosca->sucid                 = $sucursalClienteId;
                                            $nuevosca->catid                 = $categoriaid;
                                            $nuevosca->fecid                 = $fecid;
                                            $nuevosca->tsuid                 = $tsuid;
                                            $nuevosca->scavalorizadoobjetivo = $objetivo;
                                            $nuevosca->scaiconocategoria     = env('APP_URL').'/Sistema/categorias-tiposPromociones/img/iconos/'.$categoriaNombre.'-Sell In.png';
                                            $nuevosca->scavalorizadoreal     = 0;
                                            $nuevosca->scavalorizadotogo     = $objetivo;
                                            if($nuevosca->save()){
                                                $scaid = $nuevosca->scaid;
                                            }else{
    
                                            }
                                        }else{
    
                                            $agregar = true;
    
                                            foreach ($scas as $key => $sca) {
                                                if($sca->catid == $categoria->catid){
                                                    $agregar = false;
                                                }
                                            }
    
                                            if($agregar == true){
                                                $nuevosca = new scasucursalescategorias;
                                                $nuevosca->sucid                 = $sucursalClienteId;
                                                $nuevosca->catid                 = $categoria->catid;
                                                $nuevosca->fecid                 = $fecid;
                                                $nuevosca->tsuid                 = $tsuid;
                                                $nuevosca->scavalorizadoobjetivo = 0;
                                                $nuevosca->scaiconocategoria     = env('APP_URL').'/Sistema/categorias-tiposPromociones/img/iconos/'.$categoria->catnombre.'-Sell In.png';
                                                $nuevosca->scavalorizadoreal     = 0;
                                                $nuevosca->scavalorizadotogo     = 0;
                                                if($nuevosca->save()){
                                                    $scaid = $nuevosca->scaid;
                                                }else{
    
                                                }
                                            }
                                        }
                                    }
                                }
    
    
                            }else{
                                // $skusNoExisten[] = $sku;
    
                                foreach($skusNoExisten as $posicion => $skuNoExisten){
                                    if($skuNoExisten == $sku){
                                        break;
                                    }
    
                                    if($posicion+1 == sizeof($skusNoExisten)){
                                        $skusNoExisten[] = $sku;
                                        $respuesta = false;
                                        break;
                                    }
                                
                                }
                            }
    
    
    
    
                            // 
                            if($i == $numRows){
                                $scas = scasucursalescategorias::join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                                                ->where('tsu.fecid', $fecid)
                                                                ->where('tsu.tprid', 1)
                                                                ->get(['scasucursalescategorias.scaid', 'scasucursalescategorias.scavalorizadoobjetivo', 'scasucursalescategorias.scavalorizadoreal']);
    
                                foreach($scas as $sca){
                                    $scae = scasucursalescategorias::find($sca->scaid);
                                    $scae->scavalorizadotogo = $sca->scavalorizadoobjetivo - $sca->scavalorizadoreal;
    
                                    if($scae->update()){
    
                                    }else{
                                        $log[] = "No se pudo editar el sca: ".$sca->scaid;
                                    }
                                }
    
                                $tsus = tsutipospromocionessucursales::where('fecid', $fecid)
                                                                    ->where('tprid', 1)
                                                                    ->get(['tsuid', 'tsuvalorizadoreal', 'tsuvalorizadoobjetivo']);
    
                                foreach($tsus as $tsu){
                                    $tsue = tsutipospromocionessucursales::find($tsu->tsuid);
    
                                    if($tsu->tsuvalorizadoobjetivo == 0){
                                        $porcentajeCumplimiento = $tsu->tsuvalorizadoreal;
                                    }else{
                                        $porcentajeCumplimiento = (100*$tsu->tsuvalorizadoreal)/$tsu->tsuvalorizadoobjetivo;
                                    }
                                    
                                    $totalRebate = 0;
                                    
                                    $tsu->tsuvalorizadotogo         = $tsu->tsuvalorizadoobjetivo + $tsu->tsuvalorizadoreal;
                                    $tsu->tsuporcentajecumplimiento = $porcentajeCumplimiento;
                                    $tsu->tsuvalorizadorebate       = $totalRebate;
                                    if($tsue->update()){
    
                                    }else{
                                        $log[] = "No se pudo editar el tsu: ".$tsu->tsuid;
                                    }
    
                                }
                            }
                        }else{
                            $soldtosNoExisten[] = $soldto;
                            $respuesta      = false;
                            $mensaje        = 'Lo sentimos el soldto: '.$soldto.' no se encuentra registrado';
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


                


                if($respuesta == true){
                    date_default_timezone_set("America/Lima");
                    $fechaActual = date('Y-m-d H:i:s');

                    $nuevoCargaArchivo = new carcargasarchivos;
                    $nuevoCargaArchivo->tcaid             = 2;
                    $nuevoCargaArchivo->fecid             = $fecid;
                    $nuevoCargaArchivo->usuid             = $usuusuario->usuid;
                    $nuevoCargaArchivo->carnombrearchivo  = $archivo;
                    $nuevoCargaArchivo->carubicacion      = $fichero_subido;
                    $nuevoCargaArchivo->carexito          = $cargarData;
                    $nuevoCargaArchivo->carurl            = env('APP_URL').'/Sistema/cargaArchivos/objetivos/sellin/'.$archivo;
                    if($nuevoCargaArchivo->save()){
                        $pkid = "CAR-".$nuevoCargaArchivo->carid;
                    }else{

                    }

                    DB::commit();
                }else{
                    DB::rollBack();
                }
                
            }else{
                $respuesta  = false;
                $mensaje    = "No se pudo guardar el excel en el sistema";
            }

            


        } catch (Exception $e) {
            DB::rollBack();
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
            $log[]      = $mensajedev;
            $respuesta  = false;
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
            "linea"          => $linea,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev,
            "numeroCelda"    => $numeroCelda,
            "skusNoExisten"  => $skusNoExisten,
            "soldtosNoExisten"  => $soldtosNoExisten,
            "observaciones"  => $observaciones,
        ]);

        if($respuesta == true){
            $AuditoriaController = new AuditoriaController;
            $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
                $usutoken,
                $usuusuario->usuid,
                null,
                $fichero_subido,
                $requestsalida,
                'CARGAR DATA DE OBJETIVOS SELL IN',
                'IMPORTAR',
                '/cargarArchivo/ventas/obejtivos', //ruta
                $pkid,
                $log
            );

            if($registrarAuditoria == true){

            }else{
                
            }
        }
        
        return $requestsalida;
    }

    public function CargarObjetivoSellOut(Request $request)
    {
        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d H:i:s');

        $respuesta      = true;
        $mensaje        = '';
        $datos          = [];
        $skusNoExisten  = [];
        $soldtosNoExisten  = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;
        $numeroCelda    = 0;
        $usutoken       = $request->header('api_token');
        $archivo        = $_FILES['file']['name'];

        $fichero_subido = '';

        $pkid = 0;
        $log  = [];
        $observaciones  = [];

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

        DB::beginTransaction();
        try{

            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/objetivos/sellout/'.basename($usuusuario->usuid.'-'.$usuusuario->usuusuario.'-'.$fechaActual.'-'.$_FILES['file']['name']);

            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {
                $objPHPExcel    = IOFactory::load($fichero_subido);
                $objPHPExcel->setActiveSheetIndex(0);
                $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
                if($cargarData == true){
                    for ($i=2; $i <= $numRows ; $i++) {
                        $dia = '01';

                        $ano         = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                        $mesTxt      = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                        $regionExcel = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();

                        $soldto      = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                        $cliente     = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                        $grupoRebate = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                        $sector      = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                        $sku         = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
                        $producto    = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                        $objetivo    = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
            
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
                            $nuevaFecha->fecdia       = $dia;
                            $nuevaFecha->fecmesnumero = $mes;
                            $nuevaFecha->fecmes       = $mesTxt;
                            $nuevaFecha->fecano       = $ano;
                            if($nuevaFecha->save()){
                                $fecid = $nuevaFecha->fecid;
                            }else{
            
                            }
                        }


                        // $usuarioCliente = usuusuarios::join('ussusuariossucursales as uss', 'uss.usuid', 'usuusuarios.usuid')
                        //                                 ->where('usuusuarios.ususoldto', $soldto)
                        //                                 ->first(['uss.sucid']);  
                        // VERIFICAR SI EXISTE LA PERSONA PARA EL CLIENTE
                        // $clienteperpersona = perpersonas::where('pernombrecompleto', $cliente)->first(['perid']);
                        // $clienteperid = 0;
                        // if($clienteperpersona){
                        //     $clienteperid = $clienteperpersona->perid;
                        // }else{
                        //     $clienteNuevaPersona = new perpersonas;
                        //     $clienteNuevaPersona->tdiid    = 2;
                        //     $clienteNuevaPersona->pernombrecompleto = $cliente;
                        //     $clienteNuevaPersona->pernumerodocumentoidentidad = null;
                        //     $clienteNuevaPersona->pernombre = null;
                        //     $clienteNuevaPersona->perapellidopaterno   = null;
                        //     $clienteNuevaPersona->perapellidomaterno   = null;
                        //     if($clienteNuevaPersona->save()){
                        //         $clienteperid = $clienteNuevaPersona->perid;
                        //     }else{
            
                        //     }
                        // }

                        // // VERIFICAR SI EXISTE EL USUARIO
                        // $usuCliente = usuusuarios::where('tpuid', 2)
                        //                             ->where('ususoldto', $soldto)
                        //                             ->first(['usuid']);
                        // $clienteusuid = 0;
                        // $sucursalClienteId = 0;
                        // if($usuCliente){
                        //     $clienteusuid = $usuCliente->usuid;
                            
                        //     $sucursalesCliente = ussusuariossucursales::where('usuid', $clienteusuid)->first(['sucid']);
                        //     if($sucursalesCliente){
                        //         $sucursalClienteId = $sucursalesCliente->sucid;
                        //     }else{
                        //         $nuevaSucursal = new sucsucursales;
                        //         $nuevaSucursal->sucnombre = $cliente;
                        //         if($nuevaSucursal->save()){
                        //             $sucursalClienteId = $nuevaSucursal->sucid;

                        //             $sucursalUsuario = new ussusuariossucursales;
                        //             $sucursalUsuario->usuid = $clienteusuid;
                        //             $sucursalUsuario->suci  = $sucursalClienteId;
                        //             if($sucursalUsuario->save()){

                        //             }else{

                        //             }

                        //         }else{

                        //         }
                        //     }

                        // }else{
                        //     $clienteNuevoUsuario = new usuusuarios;
                        //     $clienteNuevoUsuario->tpuid         = 2; // tipo de usuario (cliente)
                        //     $clienteNuevoUsuario->perid         = $clienteperid;
                        //     $clienteNuevoUsuario->ususoldto     = $soldto;
                        //     $clienteNuevoUsuario->usuusuario    = null;
                        //     $clienteNuevoUsuario->usucorreo     = null;
                        //     $clienteNuevoUsuario->usucontrasena = null;
                        //     $clienteNuevoUsuario->usutoken      = Str::random(60);
                        //     if($clienteNuevoUsuario->save()){
                        //         $clienteusuid = $clienteNuevoUsuario->usuid;
                        //         $nuevaSucursal = new sucsucursales;
                        //         $nuevaSucursal->sucnombre = $cliente;
                        //         if($nuevaSucursal->save()){
                        //             $sucursalClienteId = $nuevaSucursal->sucid;

                        //             $sucursalUsuario = new ussusuariossucursales;
                        //             $sucursalUsuario->usuid = $clienteusuid;
                        //             $sucursalUsuario->sucid = $sucursalClienteId;
                        //             if($sucursalUsuario->save()){

                        //             }else{

                        //             }

                        //         }else{

                        //         }
                        //     }else{
            
                        //     }
                        // }


                        if($i == 2){
                            $tsus = tsutipospromocionessucursales::where('fecid', $fecid)
                                                                ->where('tprid', 2)
                                                                ->get(['tsuid']);

                            foreach($tsus as $tsu){
                                $tsue = tsutipospromocionessucursales::find($tsu->tsuid);
                                $tsue->tsuvalorizadoobjetivo = 0;
                                if($tsue->update()){
                                    $scas = scasucursalescategorias::where('tsuid', $tsu->tsuid)
                                                                    ->update(['scavalorizadoobjetivo' => 0]);
                                }
                            }

                            osoobjetivossso::where('fecid', $fecid)->update(['osovalorizado' => 0]);

                        }

                        $suc = sucsucursales::where('sucsoldto', $soldto)->first();
                        
                        if($suc){

                            if($suc->casid == 1){
                                if($regionExcel != "DTT1 LIMA"){
                                    $observaciones[] = "La sucursal: ".$suc->sucnombre." (".$soldto.") no coincide su región. La región del excel es: ".$regionExcel." la región de BD es: DTT1 LIMA";
                                }
                            }else if($suc->casid == 2){
                                if($regionExcel != "DTT2 PROVINCIAS"){
                                    $observaciones[] = "La sucursal: ".$suc->sucnombre." (".$soldto.") no coincide su región. La región del excel es: ".$regionExcel." la región de BD es: DTT2 PROVINCIAS";
                                }
                            }else{
                                $observaciones[] = "La sucursal: ".$suc->sucnombre." (".$soldto.") no coincide su región. La región del excel es: ".$regionExcel." la región de BD es: ".$suc->casid;
                            }

                            $sucursalClienteId = $suc->sucid;
                            // OBTENER EL GRUPO REBATE
                            $treid = $suc->treid;
                            $tsu = tsutipospromocionessucursales::where('fecid', $fecid)
                                                                ->where('sucid', $sucursalClienteId)
                                                                ->where('tprid', 2)
                                                                ->first([
                                                                    'tsuid', 
                                                                    'tsuvalorizadoobjetivo',
                                                                    'tsuvalorizadoreal'
                                                                ]);
                            $tsuid = 0;
                            if($tsu){
                                $tsuid = $tsu->tsuid;

                                $nuevoObjetivo = $tsu->tsuvalorizadoobjetivo+$objetivo;

                                if($nuevoObjetivo > 1){
                                    $porcentajeCumplimiento = (100 * $tsu->tsuvalorizadoreal)/$nuevoObjetivo;
                                }else{
                                    $porcentajeCumplimiento = $tsu->tsuvalorizadoreal;
                                }

                                $tsu->tsuvalorizadoobjetivo = $nuevoObjetivo;
                                $tsu->tsuvalorizadotogo = $nuevoObjetivo - $tsu->tsuvalorizadoreal;
                                $tsu->tsuporcentajecumplimiento = $porcentajeCumplimiento;
                                if($tsu->update()){

                                }else{

                                }
                            }else{
                                $nuevotsu = new tsutipospromocionessucursales;
                                $nuevotsu->fecid                     = $fecid;
                                $nuevotsu->sucid                     = $sucursalClienteId;
                                $nuevotsu->tprid                     = 2;
                                $nuevotsu->treid                     = $treid;
                                $nuevotsu->tsuporcentajecumplimiento = 0;
                                $nuevotsu->tsuvalorizadoobjetivo     = $objetivo;
                                $nuevotsu->tsuvalorizadoreal         = 0;
                                $nuevotsu->tsuvalorizadorebate       = 0;
                                $nuevotsu->tsuvalorizadotogo         = $objetivo;
                                if($nuevotsu->save()){
                                    $tsuid = $nuevotsu->tsuid;
                                }else{

                                }
                            }


                            $pro = proproductos::join('catcategorias as cat', 'cat.catid', 'proproductos.catid')
                                                ->where('proproductos.prosku', $sku)
                                                ->first([
                                                    'proproductos.proid',
                                                    'proproductos.catid',
                                                    'proproductos.pronombre',
                                                    'cat.catnombre'
                                                ]);

                            if($pro){

                                $pos = strpos($pro->catnombre, $sector);

                                if($pos === false){
                                    $observaciones[] = "El producto: ".$pro->pronombre." (".$sku.") del excel no coincide con el de la BD. El excel tiene una categoria: ".$sector." y en la BD tiene una categoria: ".$pro->catnombre;
                                }

                                $oso = osoobjetivossso::where('fecid', $fecid)
                                                    ->where('proid', $pro->proid)
                                                    ->where('sucid', $sucursalClienteId)
                                                    ->where('tpmid', 1)
                                                    ->first();

                                if($oso){

                                    $oso->osovalorizado = $objetivo + $oso->osovalorizado;
                                    $oso->update();

                                }else{
                                    $oson = new osoobjetivossso;
                                    $oson->fecid         = $fecid;
                                    $oson->proid         = $pro->proid;
                                    $oson->sucid         = $sucursalClienteId;
                                    $oson->tpmid         = 1;
                                    $oson->osocantidad   = 0;
                                    $oson->osovalorizado = $objetivo;
                                    $oson->save();
                                }

                                $sca = scasucursalescategorias::where('fecid', $fecid)
                                                        ->where('sucid', $sucursalClienteId)
                                                        ->where('tsuid', $tsuid)
                                                        ->where('catid', $pro->catid)
                                                        ->first(['scaid', 'scavalorizadoobjetivo', 'scavalorizadoreal']);

                                $scaid = 0;
                                if($sca){
                                    $scaid = $sca->scaid;
                                    $nuevoObjetivoSca = $sca->scavalorizadoobjetivo+$objetivo;

                                    $sca->scavalorizadoobjetivo = $nuevoObjetivoSca;
                                    $sca->scavalorizadotogo = $nuevoObjetivoSca - $sca->scavalorizadoreal;

                                    if($sca->update()){

                                    }else{

                                    }
                                }else{
                                    $categoriaid     = $pro->catid;
                                    $categoriaNombre = $pro->catnombre;

                                    $scas = scasucursalescategorias::where('fecid', $fecid)
                                                        ->where('sucid', $sucursalClienteId)
                                                        ->where('tsuid', $tsuid)
                                                        ->get(['scaid', 'scavalorizadoobjetivo', 'catid']);

                                    $categorias = catcategorias::where('catid', '!=', 6)
                                                        ->get([
                                                            'catid',
                                                            'catnombre'
                                                        ]);

                                    foreach($categorias as $categoria){
                                        if($categoriaid == $categoria->catid ){
                                            $nuevosca = new scasucursalescategorias;
                                            $nuevosca->sucid                 = $sucursalClienteId;
                                            $nuevosca->catid                 = $categoriaid;
                                            $nuevosca->fecid                 = $fecid;
                                            $nuevosca->tsuid                 = $tsuid;
                                            $nuevosca->scavalorizadoobjetivo = $objetivo;
                                            $nuevosca->scaiconocategoria     = env('APP_URL').'/Sistema/categorias-tiposPromociones/img/iconos/'.$categoriaNombre.'-Sell Out.png';
                                            $nuevosca->scavalorizadoreal     = 0;
                                            $nuevosca->scavalorizadotogo     = $objetivo;
                                            if($nuevosca->save()){
                                                $scaid = $nuevosca->scaid;
                                            }else{

                                            }
                                        }else{

                                            $agregar = true;

                                            foreach ($scas as $key => $sca) {
                                                if($sca->catid == $categoria->catid){
                                                    $agregar = false;
                                                }
                                            }

                                            if($agregar == true){
                                                $nuevosca = new scasucursalescategorias;
                                                $nuevosca->sucid                 = $sucursalClienteId;
                                                $nuevosca->catid                 = $categoria->catid;
                                                $nuevosca->fecid                 = $fecid;
                                                $nuevosca->tsuid                 = $tsuid;
                                                $nuevosca->scavalorizadoobjetivo = 0;
                                                $nuevosca->scaiconocategoria     = env('APP_URL').'/Sistema/categorias-tiposPromociones/img/iconos/'.$categoria->catnombre.'-Sell Out.png';
                                                $nuevosca->scavalorizadoreal     = 0;
                                                $nuevosca->scavalorizadotogo     = 0;
                                                if($nuevosca->save()){
                                                    $scaid = $nuevosca->scaid;
                                                }else{

                                                }
                                            }
                                        }
                                    }
                                }


                            }else{
                                // $skusNoExisten[] = $sku;

                                foreach($skusNoExisten as $posicion => $skuNoExisten){
                                    if($skuNoExisten == $sku){
                                        break;
                                    }

                                    if($posicion+1 == sizeof($skusNoExisten)){
                                        $skusNoExisten[] = $sku;
                                        $respuesta = false;
                                        break;
                                    }
                                
                                }
                            }

                            if($i == $numRows){
                                $scas = scasucursalescategorias::join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                                                ->where('tsu.fecid', $fecid)
                                                                ->where('tsu.tprid', 2)
                                                                ->get(['scasucursalescategorias.scaid', 'scasucursalescategorias.scavalorizadoobjetivo', 'scasucursalescategorias.scavalorizadoreal']);

                                foreach($scas as $sca){
                                    $scae = scasucursalescategorias::find($sca->scaid);
                                    $scae->scavalorizadotogo = $sca->scavalorizadoobjetivo - $sca->scavalorizadoreal;

                                    if($scae->update()){

                                    }else{
                                        $log[] = "No se pudo editar el sca: ".$sca->scaid;
                                    }
                                }

                                $tsus = tsutipospromocionessucursales::where('fecid', $fecid)
                                                                    ->where('tprid', 2)
                                                                    ->get(['tsuid', 'tsuvalorizadoreal', 'tsuvalorizadoobjetivo']);

                                foreach($tsus as $tsu){
                                    $tsue = tsutipospromocionessucursales::find($tsu->tsuid);

                                    if($tsu->tsuvalorizadoobjetivo == 0){
                                        $porcentajeCumplimiento = $tsu->tsuvalorizadoreal;
                                    }else{
                                        $porcentajeCumplimiento = (100*$tsu->tsuvalorizadoreal)/$tsu->tsuvalorizadoobjetivo;
                                    }
                                    
                                    $totalRebate = 0;
                                    
                                    $tsu->tsuvalorizadotogo         = $tsu->tsuvalorizadoobjetivo + $tsu->tsuvalorizadoreal;
                                    $tsu->tsuporcentajecumplimiento = $porcentajeCumplimiento;
                                    $tsu->tsuvalorizadorebate       = $totalRebate;
                                    if($tsue->update()){

                                    }else{
                                        $log[] = "No se pudo editar el tsu: ".$tsu->tsuid;
                                    }

                                }
                            }
                        }else{
                            $soldtosNoExisten[] = $soldto;
                            $respuesta      = false;
                            $mensaje        = 'Lo sentimos el soldto: '.$soldto.' no se encuentra registrado';
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


                if($respuesta == true){
                    date_default_timezone_set("America/Lima");
                    $fechaActual = date('Y-m-d H:i:s');

                    $nuevoCargaArchivo = new carcargasarchivos;
                    $nuevoCargaArchivo->tcaid             = 4;
                    $nuevoCargaArchivo->fecid             = $fecid;
                    $nuevoCargaArchivo->usuid             = $usuusuario->usuid;
                    $nuevoCargaArchivo->carnombrearchivo  = $archivo;
                    $nuevoCargaArchivo->carubicacion      = $fichero_subido;
                    $nuevoCargaArchivo->carexito          = $cargarData;
                    $nuevoCargaArchivo->carurl            = env('APP_URL').'/Sistema/cargaArchivos/objetivos/sellout/'.$archivo;
                    if($nuevoCargaArchivo->save()){
                        $pkid = "CAR-".$nuevoCargaArchivo->carid;
                    }else{

                    }

                    DB::commit();
                }else{
                    DB::rollBack();
                }
                
            }else{
                $respuesta  = false;
                $mensaje    = "No se pudo guardar el excel en el sistema";
            }

        } catch (Exception $e) {
            DB::rollBack();
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
            $log[]      = $mensajedev;
            $respuesta  = false;
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
            "linea"          => $linea,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev,
            "numeroCelda"    => $numeroCelda,
            "skusNoExisten"  => $skusNoExisten,
            "soldtosNoExisten" => $soldtosNoExisten,
            "observaciones" => $observaciones
        ]);

        if($respuesta == true){
            $AuditoriaController = new AuditoriaController;
            $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
                $usutoken,
                $usuusuario->usuid,
                null,
                $fichero_subido,
                $requestsalida,
                'CARGAR DATA DE OBJETIVOS SELL OUT',
                'IMPORTAR',
                '/cargarArchivo/ventas/obejtivossellout', //ruta
                $pkid,
                $log
            );

            if($registrarAuditoria == true){

            }else{
                
            }
        }
        
        return $requestsalida;
    }
}