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

        try{

            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/ventas/'.basename($_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {
                $objPHPExcel    = IOFactory::load($fichero_subido);
                $objPHPExcel->setActiveSheetIndex(0);
                $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
                
                for ($i=5; $i <= $numRows ; $i++) {
                    $ano = '2020';
                    $dia = '01';
        
                    // $mes = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                    $mes        = 'AGO';
                    $soldto     = $objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
                    $cliente    = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                    $sku        = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
                    $producto   = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
                    $sector     = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                    $real       = $objPHPExcel->getActiveSheet()->getCell('N'.$i)->getCalculatedValue();
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
                    if($cliente != null){
                        $categoriaid     = 0;
                        $categoriaNombre = '';
                        if($sector == 'Family Care'){
                            $categoriaid = 1;
                            $categoriaNombre = 'Family Care';
                        }else if($sector == 'Wipes'){
                            $categoriaid = 4; 
                            $categoriaNombre = 'Wipes';
                        }else if($sector == 'Adult Care'){
                            $categoriaid = 3;
                            $categoriaNombre = 'Adult Care';
                        }else if($sector == 'Fem Care'){
                            $categoriaid = 5;
                            $categoriaNombre = 'Fem Care';
                        }else if($sector == 'Baby/Child Care'){
                            $categoriaid = 2;
                            $categoriaNombre = 'Infant Care';
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
                                                    // ->where('ususoldto', 'LIKE', '%'.$soldto)
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
                                                            ->first(['tsuid', 'tsuvalorizadoreal', 'tsuvalorizadoobjetivo']);
                        $tsuid = 0;
                        if($tsu){
                            $tsuid = $tsu->tsuid;
                            $nuevoReal = $tsu->tsuvalorizadoreal+$real;
                            $porcentajeCumplimiento = ($nuevoReal/$tsuvalorizadoobjetivo)-1;
                            // OBTENER INFORMACION DEL REBATE
                            $rtp = rtprebatetipospromociones::where('fecid', $fecid)
                                                            ->where('tprid', 1) // TIPO DE PROMOCION SELL IN
                                                            ->where('rtpporcentajedesde', '<=', $porcentajeCumplimiento)
                                                            ->where('rtpporcentajehasta', '>=', $porcentajeCumplimiento)
                                                            ->first([
                                                                'rtpporcentajedesde',
                                                                'rtpporcentajehasta',
                                                                'rtpporcentajerebate'
                                                            ]);
                            $totalRebate = 0;
                            if($rtp){
                                $totalRebate = $nuevoReal*$rtp->rtpporcentajerebate;
                            }else{

                            }



                            
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
                            $sca->scavalorizadotogo = $sca->scavalorizadoobjetivo-$nuevoRealSca;
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
            'CARGAR DATA DE UN EXCEL AL SISTEMA DE VENTAS SELL IN',
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
