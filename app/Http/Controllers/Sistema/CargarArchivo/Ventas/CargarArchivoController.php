<?php

namespace App\Http\Controllers\Sistema\CargarArchivo\Ventas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;
use App\carcargasarchivos;
use App\fecfechas;
use App\usuusuarios;
use App\perpersonas;
use App\ussusuariossucursales;
use App\sucsucursales;
use App\tsutipospromocionessucursales;
use App\scasucursalescategorias;
use App\rtprebatetipospromociones;
use App\proproductos;
use App\trrtiposrebatesrebates;
use App\tuptiposusuariospermisos;
use App\vsiventasssi;
use App\vsoventassso;

class CargarArchivoController extends Controller
{
    public function CargarArchivo(Request $request)
    {
        $respuesta      = true;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;
        $numeroCelda    = 0;
        $usutoken       = $request->header('api_token');
        $archivo        = $_FILES['file']['name'];
        $skusNoExisten  = [];
        $log            = [];
        $pkid           = 0;

        $cargarData = false;
        
        $usuusuario = usuusuarios::join('tputiposusuarios as tpu', 'tpu.tpuid', 'usuusuarios.tpuid')
                                ->where('usuusuarios.usutoken', $usutoken)
                                ->first([
                                    'usuusuarios.usuid', 
                                    'usuusuarios.tpuid', 
                                    'tpu.tpuprivilegio'
                                ]);

        $fichero_subido = '';


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

        
        
        try{

            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/ventas/sellin/'.basename($_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {
                $fecid = 0;

                $objPHPExcel    = IOFactory::load($fichero_subido);
                $objPHPExcel->setActiveSheetIndex(0);
                $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
                
                if($cargarData == true){
                    for ($i=2; $i <= $numRows; $i++) {
                        $dia = '01';
    
                        $ano        = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                        $mesTxt     = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
    
                        $soldto     = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                        $cliente    = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
                        $sku        = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
                        $producto   = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                        $sector     = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
                        $real       = $objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();
                        
                        if(isset($mesTxt)){
                            if(isset($ano)){
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
            
                                if($i == 2){
                                    $scas = scasucursalescategorias::join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                                                    ->where('tsu.fecid', $fecid)
                                                                    ->where('tsu.tprid', 1)
                                                                    ->get(['scasucursalescategorias.scaid']);
            
                                    foreach($scas as $sca){
                                        $scae = scasucursalescategorias::find($sca->scaid);
            
                                        $scae->scavalorizadoreal = 0;
                                        $scae->scavalorizadotogo = 0;
                                        if($scae->update()){
            
                                        }else{
                                            $log[] = "No se pudo editar el sca: ".$sca->scaid;
                                        }
                                    }
            
                                    $tsus = tsutipospromocionessucursales::where('fecid', $fecid)
                                                                        ->where('tprid', 1)
                                                                        ->get(['tsuid']);
            
                                    foreach($tsus as $tsu){
                                        $tsue = tsutipospromocionessucursales::find($tsu->tsuid);
                                        $tsue->tsuvalorizadoreal = 0;
                                        $tsue->tsuvalorizadotogo = 0;
                                        $tsue->tsuporcentajecumplimiento = 0;
                                        $tsue->tsuvalorizadorebate = 0;
                                        if($tsue->update()){
            
                                        }else{
                                            $log[] = "No se pudo editar el tsu: ".$tsu->tsuid;
                                        }
            
                                    }

                                    vsiventasssi::where('fecid', $fecid)->update(['vsivalorizado' => 0]);
            
                                }
            
                                if($cliente != null){
            
                                    $separarsku = explode("0000000000", $sku);
            
                                    if(sizeof($separarsku) > 1){
                                        $sku = $separarsku[1];
                                    }else{
                                        $sku = $separarsku[0];
                                    }
            
                                    $pro = proproductos::join('catcategorias as cat', 'cat.catid', 'proproductos.catid')
                                                    ->where('proproductos.prosku', 'LIKE', '%'.$sku)
                                                    ->first([
                                                        'proproductos.proid',
                                                        'proproductos.catid',
                                                        'cat.catnombre'
                                                    ]);
                                    
                                    if($pro){

                                        $categoriaid     = $pro->catid;
                                        $categoriaNombre = $pro->catnombre;
            
                                        // VERIFICAR SI EXISTE LA PERSONA PARA EL CLIENTE
                                        $clienteperpersona = perpersonas::where('pernombrecompleto', $cliente)->first(['perid']);
                                        $clienteperid = 0;
                                        if($clienteperpersona){
                                            $clienteperid = $clienteperpersona->perid;
                                        }else{
                                            $clienteNuevaPersona = new perpersonas;
                                            $clienteNuevaPersona->tdiid                         = 2;
                                            $clienteNuevaPersona->pernombrecompleto             = $cliente;
                                            $clienteNuevaPersona->pernumerodocumentoidentidad   = null;
                                            $clienteNuevaPersona->pernombre                     = null;
                                            $clienteNuevaPersona->perapellidopaterno            = null;
                                            $clienteNuevaPersona->perapellidomaterno            = null;
                                            if($clienteNuevaPersona->save()){
                                                $clienteperid = $clienteNuevaPersona->perid;
                                            }else{
                            
                                            }
                                        }
            
                                        // $separarsoldto = explode("'00", $soldto);
            
            
                                        // if(sizeof($separarsoldto) > 1){
                                        //     $soldto = $separarsoldto[1];
                                        // }else{
                                        //     $soldto = $separarsoldto[0];
                                        // }
                                        
                                        $soldto = substr($soldto, 3);
            
                                        // VERIFICAR SI EXISTE EL USUARIO
                                        $usuCliente = usuusuarios::where('tpuid', 2)
                                                                    // ->where('perid', $clienteperid)
                                                                    ->where('ususoldto', 'LIKE', '%'.$soldto)
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
                                            $clienteNuevoUsuario->ususoldto     = $soldto;
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

                                        // 
                                        $vsi = vsiventasssi::where('fecid', $fecid)
                                                            ->where('proid', $pro->proid)
                                                            ->where('sucid', $sucursalClienteId)
                                                            ->where('tpmid', 1)
                                                            ->first();

                                        if($vsi){

                                            $vsi->vsivalorizado = $real + $vsi->vsivalorizado;
                                            $vsi->update();

                                        }else{
                                            $vsin = new vsiventasssi;
                                            $vsin->fecid         = $fecid;
                                            $vsin->proid         = $pro->proid;
                                            $vsin->sucid         = $sucursalClienteId;
                                            $vsin->tpmid         = 1;
                                            $vsin->vsicantidad   = 0;
                                            $vsin->vsivalorizado = $real;
                                            $vsin->save();
                                        }
            
                                        $tsu = tsutipospromocionessucursales::where('fecid', $fecid)
                                                                            ->where('sucid', $sucursalClienteId)
                                                                            ->where('tprid', 1)
                                                                            ->first(['tsuid', 'tsuvalorizadoreal', 'tsuvalorizadoobjetivo', 'treid']);
                                        $tsuid = 0;
                                        if($tsu){
                                            $tsuid = $tsu->tsuid;
                                            $nuevoReal = $tsu->tsuvalorizadoreal+$real;
            
                                            if($tsu->tsuvalorizadoobjetivo == 0){
                                                $porcentajeCumplimiento = $nuevoReal;
                                            }else{
                                                $porcentajeCumplimiento = (100*$nuevoReal)/$tsu->tsuvalorizadoobjetivo;
                                            }
                                            
                                            
                                            // OBTENER INFORMACION DEL REBATE
                                            // $rtp = trrtiposrebatesrebates::join('rtprebatetipospromociones as rtp', 'rtp.rtpid', 'trrtiposrebatesrebates.rtpid')
                                            //                             ->where('trrtiposrebatesrebates.treid', $tsu->treid)
                                            //                             ->where('rtp.fecid', $fecid)
                                            //                             // ->where('rtp.tprid', 1) // TIPO DE PROMOCION SELL IN
                                            //                             ->where('rtp.rtpporcentajedesde', '<=', round($porcentajeCumplimiento))
                                            //                             ->where('rtp.rtpporcentajehasta', '>=', round($porcentajeCumplimiento))
                                            //                             ->first([
                                            //                                 'rtp.rtpporcentajedesde',
                                            //                                 'rtp.rtpporcentajehasta',
                                            //                                 'rtp.rtpporcentajerebate'
                                            //                             ]);
            
                                            // $rtp = rtprebatetipospromociones::where('fecid', $fecid)
                                            //                                 ->where('tprid', 1) // TIPO DE PROMOCION SELL IN
                                            //                                 ->where('rtpporcentajedesde', '<=', round($porcentajeCumplimiento))
                                            //                                 ->where('rtpporcentajehasta', '>=', round($porcentajeCumplimiento))
                                            //                                 ->first([
                                            //                                     'rtpporcentajedesde',
                                            //                                     'rtpporcentajehasta',
                                            //                                     'rtpporcentajerebate'
                                            //                                 ]);
                                            $totalRebate = 0;
                                            // if($rtp){
                                            //     $totalRebate = $nuevoReal*$rtp->rtpporcentajerebate;
                                            // }else{
                                            //     $log[] = "No existe el grupo rebate: ".$tsu->treid;
                                            // }
                                            
                                            $tsu->tsuvalorizadoreal         = $nuevoReal;
                                            $tsu->tsuvalorizadotogo         = $tsu->tsuvalorizadoobjetivo - $nuevoReal;
                                            $tsu->tsuporcentajecumplimiento = $porcentajeCumplimiento;
                                            $tsu->tsuvalorizadorebate       = $totalRebate;
                                            if($tsu->update()){
            
                                            }else{
            
                                            }
                                        }else{

                                            $suc = sucsucursales::find($sucursalClienteId);
                                            $treid = 0;
                                            if($suc){
                                                $treid = $suc->treid;
                                            }


                                            $nuevotsu = new tsutipospromocionessucursales;
                                            $nuevotsu->fecid = $fecid;
                                            $nuevotsu->treid = $treid;
                                            $nuevotsu->sucid = $sucursalClienteId;
                                            $nuevotsu->tprid = 1;
                                            $nuevotsu->tsuporcentajecumplimiento = 0;
                                            $nuevotsu->tsuvalorizadoobjetivo  = 0;
                                            $nuevotsu->tsuvalorizadoreal      = $real;
                                            $nuevotsu->tsuvalorizadorebate    = 0;
                                            $nuevotsu->tsuvalorizadotogo      = 0;
                                            if($nuevotsu->save()){
                                                $tsuid = $nuevotsu->tsuid;
                                            }else{
            
                                            }
                                        }
            
                                        $sca = scasucursalescategorias::where('fecid', $fecid)
                                                                    ->where('sucid', $sucursalClienteId)
                                                                    ->where('catid', $categoriaid)
                                                                    ->where('tsuid', $tsuid)
                                                                    ->first(['scaid', 'scavalorizadoreal', 'scavalorizadoobjetivo']);
            
                                        $scaid = 0;
                                        if($sca){
                                            $scaid = $sca->scaid;
            
                                            $nuevoRealSca = $real + $sca->scavalorizadoreal;
                                            $sca->scavalorizadoreal = $nuevoRealSca;
                                            $sca->scavalorizadotogo = $sca->scavalorizadoobjetivo - $nuevoRealSca;
                                            $sca->scaiconocategoria = env('APP_URL').'/Sistema/categorias-tiposPromociones/img/iconos/'.$categoriaNombre.'-Sell In.png';
                                            if($sca->update()){
            
                                            }else{
            
                                            }
                                        }else{
            
                                            $nuevosca = new scasucursalescategorias;
                                            $nuevosca->sucid                 = $sucursalClienteId;
                                            $nuevosca->catid                 = $categoriaid;
                                            $nuevosca->fecid                 = $fecid;
                                            $nuevosca->tsuid                 = $tsuid;
                                            $nuevosca->scavalorizadoobjetivo = 0;
                                            $nuevosca->scaiconocategoria     = env('APP_URL').'/Sistema/categorias-tiposPromociones/img/iconos/'.$categoriaNombre.'-Sell In.png';
                                            $nuevosca->scavalorizadoreal     = $real;
                                            $nuevosca->scavalorizadotogo     = 0;
                                            if($nuevosca->save()){
                                                $scaid = $nuevosca->scaid;
                                            }else{
            
                                            }
                                            
                                        }  
                                    }else{
                                        $skusNoExisten[] = $sku;
                                        $respuesta = false;
                                        $mensaje   = "Hay algunos skus no identificados";
                                        $linea     = __LINE__;
                                    }  
                                }else{
                                    $respuesta = false;
                                    $mensaje   = "No hay un cliente";
                                    $log[]     = "No hay cliente";
                                    $linea     = __LINE__;
                                }
                            }else{
                                $respuesta = false;
                                $mensaje   = "No se encontro el año en el excel";
                                $log[]     = "No hay año";
                                $linea     = __LINE__;
                            }
                        }else{
                            $respuesta = false;
                            $mensaje   = "No se encontro el mes en el excel";
                            $log[]     = "No hay mes";
                            $linea     = __LINE__;
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

                $nuevoCargaArchivo = new carcargasarchivos;
                $nuevoCargaArchivo->tcaid            = 3;
                $nuevoCargaArchivo->fecid            = $fecid;
                $nuevoCargaArchivo->usuid            = $usuusuario->usuid;
                $nuevoCargaArchivo->carnombrearchivo = $archivo;
                $nuevoCargaArchivo->carubicacion     = $fichero_subido;
                $nuevoCargaArchivo->carexito         = $cargarData;
                $nuevoCargaArchivo->carurl           = env('APP_URL').'/Sistema/cargaArchivos/ventas/sellin/'.$archivo;
                if($nuevoCargaArchivo->save()){
                    $pkid = "CAR-".$nuevoCargaArchivo->carid;
                }else{

                }
            }else{

            }

        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
            $log[]      = $mensajedev;
        }


        $datos = array(
            array(
                "skusnoexisten" => $skusNoExisten
            )
        );

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
            "linea"          => $linea,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev,
            "numeroCelda"    => $numeroCelda,
            "logs"           => $log,
        ]);
        
        $descripcion = "CARGAR DATA DE UN EXCEL AL SISTEMA DE VENTAS SELL IN";

        if($cargarData == false){
            $descripcion = "SUBIR EXCEL PARA REVISAR Y POSTERIORMENTE CARGAR DICHA DATA EN SELL IN";
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
            '/cargarArchivo/ventas/sellin', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
    }

    public function cargarVentasSellOut(Request $request)
    {
        $respuesta      = true;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;
        $numeroCelda    = 0;
        $usutoken       = $request->header('api_token');
        $archivo        = $_FILES['file']['name'];
        $skusNoExisten  = [];
        $log            = [];
        $pkid           = 0;

        $cargarData = false;
        
        $usuusuario = usuusuarios::join('tputiposusuarios as tpu', 'tpu.tpuid', 'usuusuarios.tpuid')
                                ->where('usuusuarios.usutoken', $usutoken)
                                ->first([
                                    'usuusuarios.usuid', 
                                    'usuusuarios.tpuid', 
                                    'tpu.tpuprivilegio'
                                ]);

        $fichero_subido = '';
        
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

        $fecid = 0;

        try{

            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/ventas/sellout/'.basename($_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {
                $objPHPExcel    = IOFactory::load($fichero_subido);
                $objPHPExcel->setActiveSheetIndex(0);
                $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
                
                if($cargarData == true){
                    for ($i=2; $i <= $numRows ; $i++) {
                        $dia = '01';
    
                        $ano        = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                        $mesTxt     = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
    
                        $soldto     = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                        $cliente    = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
                        $sku        = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
                        $producto   = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                        $sector     = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
                        $real       = $objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();
                        
                        if(isset($mesTxt)){
                            if(isset($ano)){
                                // $fecid = 45;
                                $fecfecha = fecfechas::where('fecdia', $dia)
                                                        ->where('fecmes', $mesTxt)
                                                        ->where('fecano', $ano)
                                                        ->first(['fecid']);
                                
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
            
                                if($i == 2){
                                    $scas = scasucursalescategorias::join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                                                    ->where('tsu.fecid', $fecid)
                                                                    ->where('tsu.tprid', 2)
                                                                    ->get(['scasucursalescategorias.scaid']);
            
                                    foreach($scas as $sca){
                                        $scae = scasucursalescategorias::find($sca->scaid);
            
                                        $scae->scavalorizadoreal = 0;
                                        $scae->scavalorizadotogo = 0;
                                        if($scae->update()){
            
                                        }else{
                                            $log[] = "No se pudo editar el sca: ".$sca->scaid;
                                        }
                                    }
            
                                    $tsus = tsutipospromocionessucursales::where('fecid', $fecid)
                                                                        ->where('tprid', 2)
                                                                        ->get(['tsuid']);
            
                                    foreach($tsus as $tsu){
                                        $tsue = tsutipospromocionessucursales::find($tsu->tsuid);
                                        $tsue->tsuvalorizadoreal = 0;
                                        $tsue->tsuvalorizadotogo = 0;
                                        $tsue->tsuporcentajecumplimiento = 0;
                                        $tsue->tsuvalorizadorebate = 0;
                                        if($tsue->update()){
            
                                        }else{
                                            $log[] = "No se pudo editar el tsu: ".$tsu->tsuid;
                                        }
            
                                    }

                                    // vsoventassso::join('fecfechas as fec', 'fec.fecid', 'vsoventassso.fecid')
                                    //             ->where('fec.fecano', $ano)
                                    //             ->where('fec.fecmes', $mesTxt)
                                    //             // ->where('fec.fecdia', $diaTmp)
                                    //             ->update(['vsovalorizado' => 0]);

                                    // vsoventassso::where('fecid', $fecid)->update(['vsovalorizado' => 0]);
            
                                }
            
                                if($cliente != null){
            
                                    $separarsku = explode("0000000000", $sku);
            
                                    if(sizeof($separarsku) > 1){
                                        $sku = $separarsku[1];
                                    }else{
                                        $sku = $separarsku[0];
                                    }
            
                                    $pro = proproductos::join('catcategorias as cat', 'cat.catid', 'proproductos.catid')
                                                    ->where('proproductos.prosku', 'LIKE', '%'.$sku)
                                                    ->first([
                                                        'proproductos.proid',
                                                        'proproductos.catid',
                                                        'cat.catnombre'
                                                    ]);

                                    if($pro){
                                        $categoriaid     = $pro->catid;
                                        $categoriaNombre = $pro->catnombre;
            
                                        // VERIFICAR SI EXISTE LA PERSONA PARA EL CLIENTE
                                        $clienteperpersona = perpersonas::where('pernombrecompleto', $cliente)->first(['perid']);
                                        $clienteperid = 0;
                                        if($clienteperpersona){
                                            $clienteperid = $clienteperpersona->perid;
                                        }else{
                                            $clienteNuevaPersona = new perpersonas;
                                            $clienteNuevaPersona->tdiid                         = 2;
                                            $clienteNuevaPersona->pernombrecompleto             = $cliente;
                                            $clienteNuevaPersona->pernumerodocumentoidentidad   = null;
                                            $clienteNuevaPersona->pernombre                     = null;
                                            $clienteNuevaPersona->perapellidopaterno            = null;
                                            $clienteNuevaPersona->perapellidomaterno            = null;
                                            if($clienteNuevaPersona->save()){
                                                $clienteperid = $clienteNuevaPersona->perid;
                                            }else{
                            
                                            }
                                        }
                                        
                                        $soldto = substr($soldto, 3);
            
                                        // VERIFICAR SI EXISTE EL USUARIO
                                        $usuCliente = usuusuarios::where('tpuid', 2)
                                                                    // ->where('perid', $clienteperid)
                                                                    ->where('ususoldto', 'LIKE', '%'.$soldto)
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
                                            $clienteNuevoUsuario->ususoldto     = $soldto;
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

                                        // 
                                        // $vso = vsoventassso::where('fecid', $fecid)
                                        //                     ->where('proid', $pro->proid)
                                        //                     ->where('sucid', $sucursalClienteId)
                                        //                     ->where('tpmid', 1)
                                        //                     ->first();

                                        // if($vso){

                                        //     $vso->vsovalorizado = $real + $vso->vsovalorizado;
                                        //     $vso->update();

                                        // }else{
                                        //     $vson = new vsoventassso;
                                        //     $vson->fecid         = $fecid;
                                        //     $vson->proid         = $pro->proid;
                                        //     $vson->sucid         = $sucursalClienteId;
                                        //     $vson->tpmid         = 1;
                                        //     $vson->vsocantidad   = 0;
                                        //     $vson->vsovalorizado = $real;
                                        //     $vson->save();
                                        // }
            
                                        $tsu = tsutipospromocionessucursales::where('fecid', $fecid)
                                                                            ->where('sucid', $sucursalClienteId)
                                                                            ->where('tprid', 2)
                                                                            ->first(['tsuid', 'tsuvalorizadoreal', 'tsuvalorizadoobjetivo', 'treid']);
                                        $tsuid = 0;
                                        if($tsu){
                                            $tsuid = $tsu->tsuid;
                                            $nuevoReal = $tsu->tsuvalorizadoreal+$real;
            
                                            if($tsu->tsuvalorizadoobjetivo == 0){
                                                $porcentajeCumplimiento = $nuevoReal;
                                            }else{
                                                $porcentajeCumplimiento = (100*$nuevoReal)/$tsu->tsuvalorizadoobjetivo;
                                            }
                                            
                                            
                                            // OBTENER INFORMACION DEL REBATE
                                            // $rtp = trrtiposrebatesrebates::join('rtprebatetipospromociones as rtp', 'rtp.rtpid', 'trrtiposrebatesrebates.rtpid')
                                            //                             ->where('trrtiposrebatesrebates.treid', $tsu->treid)
                                            //                             ->where('rtp.fecid', $fecid)
                                            //                             ->where('rtp.tprid', $tsu->tprid) // TIPO DE PROMOCION SELL OUT
                                            //                             ->where('rtp.rtpporcentajedesde', '<=', round($porcentajeCumplimiento))
                                            //                             ->where('rtp.rtpporcentajehasta', '>=', round($porcentajeCumplimiento))
                                            //                             ->first([
                                            //                                 'rtp.rtpporcentajedesde',
                                            //                                 'rtp.rtpporcentajehasta',
                                            //                                 'rtp.rtpporcentajerebate'
                                            //                             ]);
            
                                            $totalRebate = 0;
                                            // if($rtp){
                                            //     $totalRebate = $nuevoReal*$rtp->rtpporcentajerebate;
                                            // }else{
                                            //     $log[] = "No existe el grupo rebate: ".$tsu->treid;
                                            // }
                                            
                                            $tsu->tsuvalorizadoreal         = $nuevoReal;
                                            $tsu->tsuvalorizadotogo         = $tsu->tsuvalorizadoobjetivo - $nuevoReal;
                                            $tsu->tsuporcentajecumplimiento = $porcentajeCumplimiento;
                                            $tsu->tsuvalorizadorebate       = $totalRebate;
                                            if($tsu->update()){
            
                                            }else{
            
                                            }
                                        }else{

                                            $suc = sucsucursales::find($sucursalClienteId);
                                            $treid = 0;
                                            if($suc){
                                                $treid = $suc->treid;
                                            }

                                            $nuevotsu = new tsutipospromocionessucursales;
                                            $nuevotsu->fecid = $fecid;
                                            $nuevotsu->treid = $treid;
                                            $nuevotsu->sucid = $sucursalClienteId;
                                            $nuevotsu->tprid = 2;
                                            $nuevotsu->tsuporcentajecumplimiento = 0;
                                            $nuevotsu->tsuvalorizadoobjetivo  = 0;
                                            $nuevotsu->tsuvalorizadoreal      = $real;
                                            $nuevotsu->tsuvalorizadorebate    = 0;
                                            $nuevotsu->tsuvalorizadotogo      = 0;
                                            if($nuevotsu->save()){
                                                $tsuid = $nuevotsu->tsuid;
                                            }else{
            
                                            }
                                        }
            
                                        $sca = scasucursalescategorias::where('fecid', $fecid)
                                                                    ->where('sucid', $sucursalClienteId)
                                                                    ->where('catid', $categoriaid)
                                                                    ->where('tsuid', $tsuid)
                                                                    ->first(['scaid', 'scavalorizadoreal', 'scavalorizadoobjetivo']);
            
                                        $scaid = 0;
                                        if($sca){
                                            $scaid = $sca->scaid;
            
                                            $nuevoRealSca = $real + $sca->scavalorizadoreal;
                                            $sca->scavalorizadoreal = $nuevoRealSca;
                                            $sca->scavalorizadotogo = $sca->scavalorizadoobjetivo + $nuevoRealSca;
                                            $sca->scaiconocategoria = env('APP_URL').'/Sistema/categorias-tiposPromociones/img/iconos/'.$categoriaNombre.'-Sell Out.png';
                                            if($sca->update()){
            
                                            }else{
            
                                            }
                                        }else{
            
                                            $nuevosca = new scasucursalescategorias;
                                            $nuevosca->sucid                 = $sucursalClienteId;
                                            $nuevosca->catid                 = $categoriaid;
                                            $nuevosca->fecid                 = $fecid;
                                            $nuevosca->tsuid                 = $tsuid;
                                            $nuevosca->scavalorizadoobjetivo = 0;
                                            $nuevosca->scaiconocategoria     = env('APP_URL').'/Sistema/categorias-tiposPromociones/img/iconos/'.$categoriaNombre.'-Sell Out.png';
                                            $nuevosca->scavalorizadoreal     = $real;
                                            $nuevosca->scavalorizadotogo     = 0;
                                            if($nuevosca->save()){
                                                $scaid = $nuevosca->scaid;
                                            }else{
            
                                            }
                                            
                                        }
                                    }else{
                                        $skusNoExisten[] = $sku;
                                        $respuesta = false;
                                        $mensaje   = "Hay algunos skus no identificados";
                                        $linea     = __LINE__;
                                    }  
                                }else{
                                    $respuesta = false;
                                    $mensaje   = "No hay un cliente";
                                    $log[]     = "No hay cliente";
                                    $linea     = __LINE__;
                                }

                            }else{
                                $respuesta = false;
                                $mensaje   = "No se encontro el año en el excel";
                                $log[]     = "No hay año";
                                $linea     = __LINE__;
                            }
                        }else{
                            $respuesta = false;
                            $mensaje   = "No se encontro el mes en el excel";
                            $log[]     = "No hay mes";
                            $linea     = __LINE__;
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

                // $nuevoCargaArchivo = new carcargasarchivos;
                // $nuevoCargaArchivo->tcaid             = 3;
                // $nuevoCargaArchivo->fecid             = $fecid;
                // $nuevoCargaArchivo->usuid             = $usuusuario->usuid;
                // $nuevoCargaArchivo->carnombrearchivo  = $archivo;
                // $nuevoCargaArchivo->carubicacion      = $fichero_subido;
                // $nuevoCargaArchivo->carexito          = $cargarData;
                // $nuevoCargaArchivo->carurl            = env('APP_URL').'/Sistema/cargaArchivos/ventas/sellout/'.$archivo;
                // if($nuevoCargaArchivo->save()){
                //     $pkid = "CAR-".$nuevoCargaArchivo->carid;
                // }else{

                // }
            }else{
                $respuesta = false;
                $mensaje   = "No se pudo subir el excel";
                $log[]     = "No hay acceso a la carpeta sellout";
                $linea     = __LINE__;
            }

        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
            $log[]      = $mensajedev;
        }


        $datos = array(
            array(
                "skusnoexisten" => $skusNoExisten
            )
        );

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
            "linea"          => $linea,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev,
            "numeroCelda"    => $numeroCelda,
            "logs"           => $log,
            "fecid"          => $fecid
        ]);
        
        $descripcion = "CARGAR DATA DE UN EXCEL AL SISTEMA DE VENTAS SELL OUT";

        if($cargarData == false){
            $descripcion = "SUBIR EXCEL PARA REVISAR Y POSTERIORMENTE CARGAR DICHA DATA EN SELL OUT";
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
            '/cargarArchivo/ventas/sellout', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
    }

    public function cargarVentasSellOutbk(Request $request)
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
        $skusNoExisten  = [];
        $log            = [];
        $pkid           = 0;

        $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['usuid']);

        $fichero_subido = '';

        try{

            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/ventas/sellout/'.basename($_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {
                $objPHPExcel    = IOFactory::load($fichero_subido);
                $objPHPExcel->setActiveSheetIndex(0);
                $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
                
                for ($i=2; $i <= $numRows ; $i++) {
                    $dia = '01';
        
                    $ano        = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                    $mesTxt     = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();

                    $soldto     = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                    $cliente    = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
                    $sku        = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
                    $producto   = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                    $sector     = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
                    $real       = $objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();
                    $fecfecha = fecfechas::where('fecdia', $dia)
                                            ->where('fecmes', $mesTxt)
                                            ->where('fecano', $ano)
                                            ->first(['fecid']);
                    $fecid = 0;
                    if($fecfecha){
                        $fecid = $fecfecha->fecid;
                    }else{
                        $mes = "0";
                        if($mesTxt == "AGO"){
                            $mes = "08";
                        }else if($mesTxt == "SET"){
                            $mes = "09";
                        }else if($mesTxt == "OCT"){
                            $mes = "10";
                        }
                        
                        $nuevaFecha = new fecfechas;
                        $nuevaFecha->fecfecha = new \DateTime(date("Y-m-d", strtotime($ano.'-'.$mes.'-'.$dia)));
                        $nuevaFecha->fecdia   = $dia;
                        $nuevaFecha->fecmes   = $mesTxt;
                        $nuevaFecha->fecano   = $ano;
                        if($nuevaFecha->save()){
                            $fecid = $nuevaFecha->fecid;
                        }else{
        
                        }
                    }

                    if($cliente != null){

                        $separarsku = explode("0000000000", $sku);

                        if(sizeof($separarsku) > 1){
                            $sku = $separarsku[1];
                        }else{
                            $sku = $separarsku[0];
                        }

                        $pro = proproductos::join('catcategorias as cat', 'cat.catid', 'proproductos.catid')
                                        ->where('proproductos.prosku', 'LIKE', '%'.$sku)
                                        ->first([
                                            'proproductos.catid',
                                            'cat.catnombre'
                                        ]);
                        
                        if($pro){
                            $categoriaid     = $pro->catid;
                            $categoriaNombre = $pro->catnombre;

                            // VERIFICAR SI EXISTE LA PERSONA PARA EL CLIENTE
                            $clienteperpersona = perpersonas::where('pernombrecompleto', $cliente)->first(['perid']);
                            $clienteperid = 0;
                            if($clienteperpersona){
                                $clienteperid = $clienteperpersona->perid;
                            }else{
                                $clienteNuevaPersona = new perpersonas;
                                $clienteNuevaPersona->tdiid                         = 2;
                                $clienteNuevaPersona->pernombrecompleto             = $cliente;
                                $clienteNuevaPersona->pernumerodocumentoidentidad   = null;
                                $clienteNuevaPersona->pernombre                     = null;
                                $clienteNuevaPersona->perapellidopaterno            = null;
                                $clienteNuevaPersona->perapellidomaterno            = null;
                                if($clienteNuevaPersona->save()){
                                    $clienteperid = $clienteNuevaPersona->perid;
                                }else{
                
                                }
                            }
                            
                            $soldto = substr($soldto, 3);

                            // VERIFICAR SI EXISTE EL USUARIO
                            $usuCliente = usuusuarios::where('tpuid', 2)
                                                        // ->where('perid', $clienteperid)
                                                        ->where('ususoldto', 'LIKE', '%'.$soldto)
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
                                $clienteNuevoUsuario->ususoldto     = $soldto;
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

                            $tsu = tsutipospromocionessucursales::where('fecid', $fecid)
                                                                ->where('sucid', $sucursalClienteId)
                                                                ->where('tprid', 2)
                                                                ->first(['tsuid', 'tsuvalorizadoreal', 'tsuvalorizadoobjetivo', 'treid']);
                            $tsuid = 0;
                            if($tsu){
                                $tsuid = $tsu->tsuid;
                                $nuevoReal = $tsu->tsuvalorizadoreal+$real;

                                if($tsu->tsuvalorizadoobjetivo == 0){
                                    $porcentajeCumplimiento = $nuevoReal;
                                }else{
                                    $porcentajeCumplimiento = (100*$nuevoReal)/$tsu->tsuvalorizadoobjetivo;
                                }
                                
                                
                                // OBTENER INFORMACION DEL REBATE
                                // $rtp = trrtiposrebatesrebates::join('rtprebatetipospromociones as rtp', 'rtp.rtpid', 'trrtiposrebatesrebates.rtpid')
                                //                             ->where('trrtiposrebatesrebates.treid', $tsu->treid)
                                //                             ->where('rtp.fecid', $fecid)
                                //                             // ->where('tprid', 2) // TIPO DE PROMOCION SELL OUT
                                //                             ->where('rtp.rtpporcentajedesde', '<=', round($porcentajeCumplimiento))
                                //                             ->where('rtp.rtpporcentajehasta', '>=', round($porcentajeCumplimiento))
                                //                             ->first([
                                //                                 'rtp.rtpporcentajedesde',
                                //                                 'rtp.rtpporcentajehasta',
                                //                                 'rtp.rtpporcentajerebate'
                                //                             ]);
                                // $rtp = rtprebatetipospromociones::where('fecid', $fecid)
                                //                                 ->where('tprid', 2) // TIPO DE PROMOCION SELL OUT
                                //                                 ->where('rtpporcentajedesde', '<=', round($porcentajeCumplimiento))
                                //                                 ->where('rtpporcentajehasta', '>=', round($porcentajeCumplimiento))
                                //                                 ->first([
                                //                                     'rtpporcentajedesde',
                                //                                     'rtpporcentajehasta',
                                //                                     'rtpporcentajerebate'
                                //                                 ]);
                                $totalRebate = 0;
                                // if($rtp){
                                //     $totalRebate = $nuevoReal*$rtp->rtpporcentajerebate;
                                // }else{
                                //     $log[] = "No existe el grupo rebate: ".$tsu->treid;
                                // }
                                
                                $tsu->tsuvalorizadoreal         = $nuevoReal;
                                $tsu->tsuvalorizadotogo         = $tsu->tsuvalorizadoobjetivo - $nuevoReal;
                                $tsu->tsuporcentajecumplimiento = $porcentajeCumplimiento;
                                $tsu->tsuvalorizadorebate       = $totalRebate;
                                if($tsu->update()){

                                }else{

                                }
                            }else{
                                $nuevotsu = new tsutipospromocionessucursales;
                                $nuevotsu->fecid = $fecid;
                                $nuevotsu->sucid = $sucursalClienteId;
                                $nuevotsu->tprid = 2;
                                $nuevotsu->tsuporcentajecumplimiento = 0;
                                $nuevotsu->tsuvalorizadoobjetivo  = 0;
                                $nuevotsu->tsuvalorizadoreal      = $real;
                                $nuevotsu->tsuvalorizadorebate    = 0;
                                $nuevotsu->tsuvalorizadotogo      = 0;
                                if($nuevotsu->save()){
                                    $tsuid = $nuevotsu->tsuid;
                                }else{

                                }
                            }

                            $sca = scasucursalescategorias::where('fecid', $fecid)
                                                        ->where('sucid', $sucursalClienteId)
                                                        ->where('catid', $categoriaid)
                                                        ->where('tsuid', $tsuid)
                                                        ->first(['scaid', 'scavalorizadoreal', 'scavalorizadoobjetivo']);

                            $scaid = 0;
                            if($sca){
                                $scaid = $sca->scaid;

                                $nuevoRealSca = $real + $sca->scavalorizadoreal;
                                $sca->scavalorizadoreal = $nuevoRealSca;
                                $sca->scavalorizadotogo = $sca->scavalorizadoobjetivo - $nuevoRealSca;
                                if($sca->update()){

                                }else{

                                }
                            }else{

                                $nuevosca = new scasucursalescategorias;
                                $nuevosca->sucid                 = $sucursalClienteId;
                                $nuevosca->catid                 = $categoriaid;
                                $nuevosca->fecid                 = $fecid;
                                $nuevosca->tsuid                 = $tsuid;
                                $nuevosca->scavalorizadoobjetivo = 0;
                                $nuevosca->scaiconocategoria     = env('APP_URL').'/Sistema/categorias-tiposPromociones/img/iconos/'.$categoriaNombre.'-Sell Out.png';
                                $nuevosca->scavalorizadoreal     = $real;
                                $nuevosca->scavalorizadotogo     = 0;
                                if($nuevosca->save()){
                                    $scaid = $nuevosca->scaid;
                                }else{

                                }
                                
                            }  
                        }else{
                            $skusNoExisten[] = $sku;
                        }  
                    }
                }


                date_default_timezone_set("America/Lima");
                $fechaActual = date('Y-m-d H:i:s');

                $nuevoCargaArchivo = new carcargasarchivos;
                $nuevoCargaArchivo->tcaid = 2;
                $nuevoCargaArchivo->fecid = $fecid;
                $nuevoCargaArchivo->usuid = $usuusuario->usuid;
                $nuevoCargaArchivo->carnombrearchivo = $archivo;
                $nuevoCargaArchivo->carubicacion = $fichero_subido;
                $nuevoCargaArchivo->carexito = true;
                if($nuevoCargaArchivo->save()){
                    $pkid = "CAR-".$nuevoCargaArchivo->carid;
                }else{

                }
            }

        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
        }


        $datos = array(
            array(
                "skusnoexisten" => $skusNoExisten
            )
        );

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
            "linea"          => $linea,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev,
            "numeroCelda"    => $numeroCelda,
            "logs"           => $log,
            "fecid"           => $fecid,
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            $usuusuario->usuid,
            null,
            $fichero_subido,
            $requestsalida,
            'CARGAR DATA DE UN EXCEL AL SISTEMA DE VENTAS SELL OUT',
            'IMPORTAR',
            '/cargarArchivo/ventas/sellout', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
    }
}
