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

class ObjetivoCargarController extends Controller
{
    public function CargarObjetivo(Request $request)
    {
        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d H:i:s');

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $skusNoExisten  = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;
        $numeroCelda    = 0;
        $usutoken       = $request->header('api_token');
        $archivo        = $_FILES['file']['name'];

        $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['usuid', 'usuusuario']);

        $fichero_subido = '';

        try{

            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/objetivos/'.basename($usuusuario->usuid.'-'.$usuusuario->usuusuario.'-'.$fechaActual.'-'.$_FILES['file']['name']);

            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {
                $objPHPExcel    = IOFactory::load($fichero_subido);
                $objPHPExcel->setActiveSheetIndex(0);
                $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();

                for ($i=2; $i <= $numRows ; $i++) {
                    $ano = '2020';
                    $dia = '01';
        
                    // $mes = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                    $mes        = 'AGO';
                    $soldto     = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                    $cliente    = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                    $sector     = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                    $sku        = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                    $producto   = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
                    $objetivo   = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
        
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


                    // $usuarioCliente = usuusuarios::join('ussusuariossucursales as uss', 'uss.usuid', 'usuusuarios.usuid')
                    //                                 ->where('usuusuarios.ususoldto', $soldto)
                    //                                 ->first(['uss.sucid']);  
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
                                                ->where('ususoldto', $soldto)
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
                                                        ->first(['tsuid', 'tsuvalorizadoobjetivo']);
                    $tsuid = 0;
                    if($tsu){
                        $tsuid = $tsu->tsuid;
                        $tsu->tsuvalorizadoobjetivo = $tsu->tsuvalorizadoobjetivo+$objetivo;
                        if($tsu->update()){

                        }else{

                        }
                    }else{
                        $nuevotsu = new tsutipospromocionessucursales;
                        $nuevotsu->fecid = $fecid;
                        $nuevotsu->sucid = $sucursalClienteId;
                        $nuevotsu->tprid = 1;
                        $nuevotsu->tsuporcentajecumplimiento = 0;
                        $nuevotsu->tsuvalorizadoobjetivo = $objetivo;
                        $nuevotsu->tsuvalorizadoreal = 0;
                        $nuevotsu->tsuvalorizadorebate = 0;
                        $nuevotsu->tsuvalorizadotogo = 0;
                        if($nuevotsu->save()){
                            $tsuid = $nuevotsu->tsuid;
                        }else{

                        }
                    }


                    $pro = proproductos::join('catcategorias as cat', 'cat.catid', 'proproductos.catid')
                                        ->where('proproductos.prosku', $sku)
                                        ->first([
                                            'proproductos.catid',
                                            'cat.catnombre'
                                        ]);

                    if($pro){
                        $sca = scasucursalescategorias::where('fecid', $fecid)
                                                ->where('sucid', $sucursalClienteId)
                                                ->where('tsuid', $tsuid)
                                                ->where('catid', $pro->catid)
                                                ->first(['scaid', 'scavalorizadoobjetivo']);

                        $scaid = 0;
                        if($sca){
                            $scaid = $sca->scaid;

                            $sca->scavalorizadoobjetivo = $sca->scavalorizadoobjetivo+$objetivo;
                            if($sca->update()){

                            }else{

                            }
                        }else{
                            $categoriaid     = $pro->catid;
                            $categoriaNombre = $pro->catnombre;

                            $categorias = catcategorias::where('catid', '!=', 6)
                                                ->get([
                                                    'catid',
                                                    'cat.catnombre'
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
                                    $nuevosca->scavalorizadotogo     = 0;
                                    if($nuevosca->save()){
                                        $scaid = $nuevosca->scaid;
                                    }else{

                                    }
                                }else{
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


                    }else{
                        $skusNoExisten[] = $sku;
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

                }else{

                }
            }else{

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
            $usuusuario->usuid,
            null,
            $fichero_subido,
            $requestsalida,
            'CARGAR DATA DE CLIENTES AL SISTEMA ',
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
