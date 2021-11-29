<?php //Promociones Set 2021.xlsx

namespace App\Http\Controllers\Sistema\CargarArchivo\Promociones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
// use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


use Illuminate\Support\Str;
use App\zonzonas;
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
use \DateTime;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CargarArchivoPromocionesController extends Controller
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

        $cargarData = true;
        
        $usuusuario = usuusuarios::join('tputiposusuarios as tpu', 'tpu.tpuid', 'usuusuarios.tpuid')
                                ->where('usuusuarios.usutoken', $usutoken)
                                ->first([
                                    'usuusuarios.usuid', 
                                    'usuusuarios.tpuid', 
                                    'tpu.tpuprivilegio'
                                ]);
        // if($usuusuario->tpuprivilegio == "todo"){
        //     $cargarData = true;
        // }else{
        //     $tup = tuptiposusuariospermisos::join('pempermisos as pem', 'pem.pemid', 'tuptiposusuariospermisos.pemid')
        //                                     ->where('tuptiposusuariospermisos.tpuid', $usuusuario->tpuid)
        //                                     ->where('pem.pemslug', "cargar.data.servidor.promociones")
        //                                     ->first([
        //                                         'tuptiposusuariospermisos.tpuid'
        //                                     ]);

        //     if($tup){
        //         $cargarData = true;
        //     }else{
        //         $cargarData = false;
        //     }
        // }
        

        $fichero_subido = '';

        $pkid = 0;
        $log  = array(
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
            "NUEVA_SUCURSAL"               => [],
            "PRODUCTO_NO_EXISTE"           => [],
            "SUCURSALES_NO_IDENTIFICADAS"  => []
        );

        $fecActual = new \DateTime(date("Y-m-d", strtotime("2020-10-20")));

        try{
            // file_put_contents(base_path().'/public/'.$archivo, $_FILES['file']['tmp_name']);
            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/promociones/'.basename($_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {
                $fecid = 0;

                $objPHPExcel    = IOFactory::load($fichero_subido);
                $objPHPExcel->setActiveSheetIndex(0);
                $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();

                if($cargarData == true){

                    $soldtosBorrar = [];

                    for ($i=2; $i <= $numRows ; $i++) {
                        // $ano = '2020';
                        $dia = '01';
            
                        // $ano        = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                        // $mesTxt     = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                        // $subCanal   = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                        // $ejecutivo  = $objPHPExcel->getActiveSheet()->getCell('M'.$i)->getCalculatedValue();
                        // $soldTo     = $objPHPExcel->getActiveSheet()->getCell('P'.$i)->getCalculatedValue();
                        // $cliente    = $objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getCalculatedValue();
                        // $accion     = $objPHPExcel->getActiveSheet()->getCell('R'.$i)->getCalculatedValue();
                        // $cantCompra = $objPHPExcel->getActiveSheet()->getCell('S'.$i)->getCalculatedValue();
                        // $cantBonifi = $objPHPExcel->getActiveSheet()->getCell('T'.$i)->getCalculatedValue();
                        // $mecanica   = $objPHPExcel->getActiveSheet()->getCell('V'.$i)->getCalculatedValue();
                        // $categoria  = $objPHPExcel->getActiveSheet()->getCell('W'.$i)->getCalculatedValue();
    
                        // /**NUEVOS CAMPOS */
                        // $codPromoc  = $objPHPExcel->getActiveSheet()->getCell('X'.$i)->getCalculatedValue();
                        // $codPrinci  = $objPHPExcel->getActiveSheet()->getCell('Y'.$i)->getCalculatedValue();
                        // /***/
    
                        // $sku        = $objPHPExcel->getActiveSheet()->getCell('Z'.$i)->getCalculatedValue();
                        // $producto   = $objPHPExcel->getActiveSheet()->getCell('AA'.$i)->getCalculatedValue();
    
                        // /**NUEVOS CAMPOS */
                        // $productoPpt = $objPHPExcel->getActiveSheet()->getCell('AB'.$i)->getCalculatedValue();
                        // $compraPpt   = $objPHPExcel->getActiveSheet()->getCell('AC'.$i)->getCalculatedValue();
                        // /***/
    
                        // $skuBonifi  = $objPHPExcel->getActiveSheet()->getCell('AD'.$i)->getCalculatedValue();
                        // $productoBo = $objPHPExcel->getActiveSheet()->getCell('AE'.$i)->getCalculatedValue();
    
    
                        // /**NUEVOS CAMPOS */
                        // $proBoniPpt = $objPHPExcel->getActiveSheet()->getCell('AF'.$i)->getCalculatedValue();
                        // $compBonPpt = $objPHPExcel->getActiveSheet()->getCell('AG'.$i)->getCalculatedValue();
                        // /***/
    
                        // $tipoPromo  = $objPHPExcel->getActiveSheet()->getCell('AH'.$i)->getCalculatedValue();
                        // $tipoClien  = $objPHPExcel->getActiveSheet()->getCell('AK'.$i)->getCalculatedValue();
                        // $planchas   = $objPHPExcel->getActiveSheet()->getCell('AN'.$i)->getCalculatedValue();
                        // $combos     = $objPHPExcel->getActiveSheet()->getCell('AO'.$i)->getCalculatedValue();
                        // $precXcombo = $objPHPExcel->getActiveSheet()->getCell('AP'.$i)->getCalculatedValue();
                        // $precXplanc = $objPHPExcel->getActiveSheet()->getCell('AQ'.$i)->getCalculatedValue();
                        // $precXtodo  = $objPHPExcel->getActiveSheet()->getCell('AR'.$i)->getCalculatedValue();
                        // $nuevoProm  = $objPHPExcel->getActiveSheet()->getCell('AS'.$i)->getCalculatedValue();
                        

                        $ano        = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                        $mesTxt     = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                        $ex_zona    = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                        // $subCanal   = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                        // $ejecutivo  = $objPHPExcel->getActiveSheet()->getCell('M'.$i)->getCalculatedValue();
                        $soldTo     = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue(); //P
                        // $cliente    = $objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getCalculatedValue();
                        $accion     = $objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getCalculatedValue(); // R
                        $cantCompra = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue(); // S
                        $cantBonifi = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue(); // T
                        $mecanica   = $objPHPExcel->getActiveSheet()->getCell('W'.$i)->getCalculatedValue(); // V
                        $categoria  = $objPHPExcel->getActiveSheet()->getCell('X'.$i)->getCalculatedValue(); // W
    
                        /**NUEVOS CAMPOS */
                        $codPromoc  = $objPHPExcel->getActiveSheet()->getCell('Y'.$i)->getCalculatedValue(); // X
                        // $codPrinci  = $objPHPExcel->getActiveSheet()->getCell('Y'.$i)->getCalculatedValue();
                        /***/
    
                        $sku        = $objPHPExcel->getActiveSheet()->getCell('R'.$i)->getCalculatedValue(); // Z
                        $producto   = $objPHPExcel->getActiveSheet()->getCell('S'.$i)->getCalculatedValue(); // AA
    
                        /**NUEVOS CAMPOS */
                        $productoPpt = $objPHPExcel->getActiveSheet()->getCell('AA'.$i)->getCalculatedValue(); // AB
                        $compraPpt   = $objPHPExcel->getActiveSheet()->getCell('AB'.$i)->getCalculatedValue(); // AC
                        /***/
    
                        $skuBonifi  = $objPHPExcel->getActiveSheet()->getCell('U'.$i)->getCalculatedValue(); // AD
                        $productoBo = $objPHPExcel->getActiveSheet()->getCell('V'.$i)->getCalculatedValue(); // AE
    
    
                        /**NUEVOS CAMPOS */
                        $proBoniPpt = $objPHPExcel->getActiveSheet()->getCell('AC'.$i)->getCalculatedValue(); // AF
                        $compBonPpt = $objPHPExcel->getActiveSheet()->getCell('AD'.$i)->getCalculatedValue(); // AG
                        /***/
    
                        $tipoPromo  = $objPHPExcel->getActiveSheet()->getCell('AE'.$i)->getCalculatedValue(); // AH
                        $tipoClien  = $objPHPExcel->getActiveSheet()->getCell('AF'.$i)->getCalculatedValue(); // AK
                        $planchas   = $objPHPExcel->getActiveSheet()->getCell('AG'.$i)->getCalculatedValue(); // AN
                        $combos     = $objPHPExcel->getActiveSheet()->getCell('AH'.$i)->getCalculatedValue(); // AO
                        $precXcombo = $objPHPExcel->getActiveSheet()->getCell('AI'.$i)->getCalculatedValue(); // AP
                        $precXplanc = $objPHPExcel->getActiveSheet()->getCell('AJ'.$i)->getCalculatedValue(); // AQ
                        $precXtodo  = $objPHPExcel->getActiveSheet()->getCell('AK'.$i)->getCalculatedValue(); // AR
                        $ex_iniciopromo = $objPHPExcel->getActiveSheet()->getCell('AL'.$i)->getCalculatedValue(); // AR
                        $ex_finpromo    = $objPHPExcel->getActiveSheet()->getCell('AM'.$i)->getCalculatedValue(); // AR
                        $nuevoProm      = $objPHPExcel->getActiveSheet()->getCell('AO'.$i)->getCalculatedValue();


                        $arrayFecha = explode(".", $ex_iniciopromo);

                        if(sizeof($arrayFecha) == 3){
                            $encontroFecha = true;
                            $ex_iniciopromo = $arrayFecha[0]."-".$arrayFecha[1]."-".$arrayFecha[2];
                        }else{
                            $arrayFecha = explode("/", $ex_iniciopromo);
                            if(sizeof($arrayFecha) == 3){
                                $encontroFecha = true;
                                $ex_iniciopromo = $arrayFecha[0]."-".$arrayFecha[1]."-".$arrayFecha[2];

                            }else{

                                $ex_iniciopromo = Date::excelToDateTimeObject($ex_iniciopromo);
                                $ex_iniciopromo = json_encode($ex_iniciopromo);
                                $ex_iniciopromo = json_decode($ex_iniciopromo);
                                $ex_iniciopromo = date("d-m-Y", strtotime($ex_iniciopromo->date));

                                
                            }
                        }

                        $arrayFecha = explode(".", $ex_finpromo);

                        if(sizeof($arrayFecha) == 3){
                            $encontroFecha = true;
                            $ex_finpromo = $arrayFecha[0]."-".$arrayFecha[1]."-".$arrayFecha[2];
                        }else{
                            $arrayFecha = explode("/", $ex_finpromo);
                            if(sizeof($arrayFecha) == 3){
                                $encontroFecha = true;
                                $ex_finpromo = $arrayFecha[0]."-".$arrayFecha[1]."-".$arrayFecha[2];

                            }else{

                                $ex_finpromo = Date::excelToDateTimeObject($ex_finpromo);
                                $ex_finpromo = json_encode($ex_finpromo);
                                $ex_finpromo = json_decode($ex_finpromo);
                                $ex_finpromo = date("d-m-Y", strtotime($ex_finpromo->date));
                                
                            }
                        }




                        $codPrinci  = $soldTo.$sku.$skuBonifi.$tipoClien;

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
                                            ->where('sucestado', "!=", 1)
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

                            // ELIMINAR PRP Y PRB
                            // if($i == 2){
                            //     prppromocionesproductos::join('prmpromociones as prm', 'prm.prmid', 'prppromocionesproductos.prmid')
                            //                             ->where('prm.fecid', $fecid)
                            //                             ->where('prpzona', $ex_zona)
                            //                             ->delete();

                            //     prbpromocionesbonificaciones::join('prmpromociones as prm', 'prm.prmid', 'prbpromocionesbonificaciones.prmid')
                            //                                 ->where('prm.fecid', $fecid)
                            //                                 ->where('prbzona', $ex_zona)
                            //                                 ->delete();

                            // }

                            $limpiarDataSoldto = true;

                            if(sizeof($soldtosBorrar) > 0){

                                $encontroSoldto = false;

                                foreach($soldtosBorrar as $soldtoBorrar){
                                    if($soldtoBorrar == $soldTo ){

                                        $encontroSoldto = true;
                                        $limpiarDataSoldto = false;

                                    }
                                }

                                if($encontroSoldto == false){
                                    $soldtosBorrar[] = $soldTo;
                                }

                            }else{
                                $soldtosBorrar[] = $soldTo;
                            }

                            if($limpiarDataSoldto == true){
                                
                                cspcanalessucursalespromociones::join('csccanalessucursalescategorias as csc', 'csc.cscid', 'cspcanalessucursalespromociones.cscid')
                                                                ->join('scasucursalescategorias as sca', 'sca.scaid', 'csc.scaid')
                                                                -join('sucsucursales as suc', 'suc.sucid', 'sca.sucid')
                                                                ->where('cspcanalessucursalespromociones.fecid', $fecid)
                                                                ->where('sucsoldto', $soldTo)
                                                                ->delete();

                                prmpromociones::join('cspcanalessucursalespromociones as csp', 'csp.prmid', 'prmpromociones.prmid')
                                                ->join('csccanalessucursalescategorias as csc', 'csc.cscid', 'csp.cscid')
                                                ->join('scasucursalescategorias as sca', 'sca.scaid', 'csc.scaid')
                                                ->join('sucsucursales as suc', 'suc.sucid', 'sca.sucid')
                                                ->where('prmpromociones.fecid', $fecid)
                                                ->where('sucsoldto', $soldTo)
                                                ->delete();


                                prppromocionesproductos::join('prmpromociones as prm', 'prm.prmid', 'prppromocionesproductos.prmid')
                                                        ->join('cspcanalessucursalespromociones as csp', 'csp.prmid', 'prm.prmid')
                                                        ->join('csccanalessucursalescategorias as csc', 'csc.cscid', 'csp.cscid')
                                                        ->join('scasucursalescategorias as sca', 'sca.scaid', 'csc.scaid')
                                                        ->join('sucsucursales as suc', 'suc.sucid', 'sca.sucid')
                                                        ->where('prm.fecid', $fecid)
                                                        ->where('sucsoldto', $soldTo)
                                                        ->delete();

                                prbpromocionesbonificaciones::join('prmpromociones as prm', 'prm.prmid', 'prbpromocionesbonificaciones.prmid')
                                                            ->join('cspcanalessucursalespromociones as csp', 'csp.prmid', 'prm.prmid')
                                                            ->join('csccanalessucursalescategorias as csc', 'csc.cscid', 'csp.cscid')
                                                            ->join('scasucursalescategorias as sca', 'sca.scaid', 'csc.scaid')
                                                            ->join('sucsucursales as suc', 'suc.sucid', 'sca.sucid')
                                                            ->where('prm.fecid', $fecid)
                                                            ->where('sucsoldto', $soldTo)
                                                            ->delete();
                            }




                            // if($i == 2){
                            //     cspcanalessucursalespromociones::join('prmpromociones as prm', 'prm.prmid', 'cspcanalessucursalespromociones.prmid')
                            //                                     ->where('cspcanalessucursalespromociones.fecid', $fecid)
                            //                                     ->where('cspzona', $ex_zona)
                            //                                     ->delete();

                            //     prmpromociones::where('fecid', $fecid)
                            //                     ->where('prmzona', $ex_zona)
                            //                     ->delete();
                            // }
                
                            
                            // VERIFICAR SI EXISTE EL USUARIO
                            $usuCliente = sucsucursales::where('sucsoldto', $soldTo) 
                                                        ->first();

                            if($usuCliente){
                                
                                $sucursalClienteId = $usuCliente->sucid;

                                // $catcategoria = catcategorias::where('catnombre', $categoria)
                                //                             ->first(['catid']);

                                $catcategoria = proproductos::where('prosku', $sku)
                                                            ->first(['proid', 'catid']);
                            
                                $catid = 0;
                                if($catcategoria){
                                    $catid = $catcategoria->catid;

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
                                            $log["NUEVO_CATEGORIA_ASIGNADO"][] = $i."-".$scaid;
                                        }else{
            
                                        }
                                    }
            
                                    // Sacando los espacios del ultimo digito y limpiando caracteres
                                        // $catEspa = substr($categoria, -1, 1);
                                        // $nuevonombrecategoria = "";
                                        // if($catEspa == " "){
                                        //     $nuevonombrecategoria = substr($categoria, 0, strlen($categoria)-1);
                                        // }else{
                                        //     $nuevonombrecategoria = $categoria;
                                        // }
            
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
                                    $existeProducto = true;
                                    $imagenProducto = "/";
                                    $imagenProductoBonificado = "/";

                                    $proproducto = proproductos::where('prosku', $sku)
                                                                ->first(['proid', 'proimagen', 'proespromocion']);
                                    
                                    $proid = 0;
                                    if($proproducto){
                                        $proid = $proproducto->proid;
                                        $imagenProducto = $proproducto->proimagen;

                                        if($proproducto->proespromocion != 1){
                                            $proproducto->proimagen = "/";
                                            $proproducto->proespromocion = true;
                                            $proproducto->update();
                                        }
            
                                    }else{

                                        $existeProducto = false;
                                        $log['PRODUCTO_NO_EXISTE'][] = "El producto: ".$sku;
                                    }
                        
                                    // VERIFICAR SI EL PRODUCTO BONIFICADO ESTA REGISTRADO
                                    $proproductoBonificado = proproductos::where('prosku', $skuBonifi)
                                                                        ->first(['proid', 'proimagen', 'proespromocion']);
                                    
                                    $bonificadoproid = 0;
                                    if($proproductoBonificado){
                                        $bonificadoproid = $proproductoBonificado->proid;

                                        $imagenProductoBonificado = $proproductoBonificado->proimagen;

                                        if($proproductoBonificado->proespromocion != 1){
                                            $proproductoBonificado->proimagen = "/";
                                            $proproductoBonificado->proespromocion = true;
                                            $proproductoBonificado->update();
                                        }
                                        // if($proproductoBonificado->proimagen == env('APP_URL').'/Sistema/promociones/'.strtoupper($nuevonombrecategoria).'/'.strtoupper($nuevonombretipocliente).'/'.strtoupper($nuevonombrecodpromoc).'/'.$nuevonombreprobonippt.' - Gratis.png' ){
            
                                        // }else{
                                        //     $proproductoBonificado->proimagen = env('APP_URL').'/Sistema/promociones/'.strtoupper($nuevonombrecategoria).'/'.strtoupper($nuevonombretipocliente).'/'.strtoupper($nuevonombrecodpromoc).'/'.$nuevonombreprobonippt.' - Gratis.png';
                                        //     if($proproductoBonificado->update()){
            
                                        //     }else{
                                                
                                        //     }
                                        // }
            
                                    }else{
                                        // $nuevoProductoBonificado = new proproductos;
                                        // $nuevoProductoBonificado->catid     = $catid;
                                        // $nuevoProductoBonificado->prosku    = $skuBonifi;
                                        // $nuevoProductoBonificado->pronombre = $productoBo;
                                        // $nuevoProductoBonificado->proimagen = env('APP_URL').'/Sistema/promociones/'.strtoupper($nuevonombrecategoria).'/'.strtoupper($nuevonombretipocliente).'/'.strtoupper($nuevonombrecodpromoc).'/'.$nuevonombreprobonippt.' - Gratis.png';
                                        // if($nuevoProductoBonificado->save()){
                                        //     $bonificadoproid = $nuevoProductoBonificado->proid;
                                        // }else{
                        
                                        // }
                                        $existeProducto = false;
                                        $log['PRODUCTO_NO_EXISTE'][] = "El producto: ".$skuBonifi;
                                    }
                                    

                                    if($existeProducto == true){

                                        // VERIFICAR SI EXISTE EL TIPO DE PROMOCION
                                        $tprtipopromocion = tprtipospromociones::where('tprnombre', $tipoPromo)->first(['tprid']);
                                        $tprid = 0;
                                        if($tprtipopromocion){
                                            $tprid = $tprtipopromocion->tprid;

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
                                                    $log["NUEVO_CANAL_ASIGNADO"][] = $i."-".$cscid;
                                                }else{
                    
                                                }
                                            }
                    
                                            $prm = prmpromociones::where('tprid', $tprid)
                                                            // ->where('prmcodigo', $codPromoc)
                                                            ->where('prmmecanica', $mecanica)
                                                            ->where('prmsku', $sku)
                                                            ->where('prmaccion', $accion)
                                                            ->where('fecid', $fecid)
                                                            ->where('prmcodigoprincipal', $codPrinci)
                                                            ->first(['prmid']);
                    
                                            $prmid = 0;
                                            if($prm){
                                                $prmid = $prm->prmid;
                    
                                            }else{
                                                $nuevoPrm = new prmpromociones;
                                                $nuevoPrm->tprid                = $tprid;
                                                $nuevoPrm->prmzona              = $ex_zona;
                                                $nuevoPrm->fecid                = $fecid;
                                                $nuevoPrm->prmcodigoprincipal   = $codPrinci;
                                                // $nuevoPrm->prmcodigo            = $codPromoc;
                                                $nuevoPrm->prmcodigo            = "-";
                                                $nuevoPrm->prmmecanica          = $mecanica;
                                                $nuevoPrm->prmaccion            = $accion;
                                                $nuevoPrm->prmsku            = $sku;
                                                if($nuevoPrm->save()){
                                                    $prmid = $nuevoPrm->prmid;
                                                    $log["NUEVO_PROMOCION_CREADO"][] = $i."-".$prmid;
                                                }else{
                    
                                                }
                                            }
                    
                                            $prb = prbpromocionesbonificaciones::where('prmid', $prmid)
                                                                            ->where('proid', $bonificadoproid) 
                                                                            ->where('prbcodigoprincipal', $codPrinci) 
                                                                            // ->where('prbcomprappt', $compBonPpt) 
                                                                            ->where('prbproductoppt', $proBoniPpt) 
                                                                            ->where('prbcantidad', $cantBonifi) 
                                                                            ->first(['prbid']);
                    
                                            $prbid = 0;
                                            if($prb){
                                                $prbid = $prb->prbid;
                                                $prb->prbcomprappt = $compBonPpt;
                                                $prb->prbzona      = $ex_zona;
                                                $prb->update();
                                            }else{
                                                $nuevoPrb = new prbpromocionesbonificaciones;
                                                $nuevoPrb->prmid                = $prmid;
                                                $nuevoPrb->prbzona              = $ex_zona;
                                                $nuevoPrb->proid                = $bonificadoproid;
                                                $nuevoPrb->prbcantidad          = $cantBonifi;
                                                $nuevoPrb->prbproductoppt       = $proBoniPpt;
                                                $nuevoPrb->prbcomprappt         = $compBonPpt;
                                                $nuevoPrb->prbcodigoprincipal   = $codPrinci;
                                                // $nuevoPrb->prbimagen            = env('APP_URL').'/Sistema/promociones/'.strtoupper($nuevonombrecategoria).'/'.strtoupper($nuevonombretipocliente).'/'.strtoupper($nuevonombrecodpromoc).'/'.$nuevonombreprobonippt.' - Gratis.png';
                                                // $nuevoPrb->prbimagen            = env('APP_URL')."/Sistema/promociones/IMAGENES/BONIFICADOS/".$fecid."-".$prmid."-".$bonificadoproid."-".$proBoniPpt."-".$compBonPpt.".png";
                                                $nuevoPrb->prbimagen            = $imagenProductoBonificado;
                    
                                                if($nuevoPrb->save()){
                                                    $prbid = $nuevoPrb->prbid;
                                                    $log["NUEVO_PRB_CREADO"][] = $i."-".$prbid;
                                                }else{
                    
                                                }
                                            }
                    
                                            
                                            $prp = prppromocionesproductos::where('prmid', $prmid)
                                                                            ->where('proid', $proid)
                                                                            ->where('prpcodigoprincipal', $codPrinci)
                                                                            // ->where('prpcomprappt', $compraPpt)
                                                                            ->where('prpproductoppt', $productoPpt)
                                                                            ->where('prpcantidad', $cantCompra)
                                                                            ->first(['prpid']);
                                            $prpid = 0;
                                            if($prp){
                                                $prpid = $prp->prpid;
                                                $prp->prpcomprappt = $compraPpt;
                                                $prp->prpzona      = $ex_zona;
                                                $prp->update();
                                            }else{
                                                $nuevoPrp = new prppromocionesproductos;
                                                $nuevoPrp->prmid                = $prmid;
                                                $nuevoPrp->prpzona              = $ex_zona;
                                                $nuevoPrp->proid                = $proid;
                                                $nuevoPrp->prpcantidad          = $cantCompra;
                                                $nuevoPrp->prpproductoppt       = $productoPpt;
                                                $nuevoPrp->prpcomprappt         = $compraPpt;
                                                $nuevoPrp->prpcodigoprincipal   = $codPrinci;
                                                // $nuevoPrp->prpimagen            = env('APP_URL').'/Sistema/promociones/'.strtoupper($nuevonombrecategoria).'/'.strtoupper($nuevonombretipocliente).'/'.strtoupper($nuevonombrecodpromoc).'/'.$nuevonombreproductoppt.'.png';
                                                // $nuevoPrp->prpimagen            = env('APP_URL').'/Sistema/promociones/IMAGENES/PRODUCTOS/'.$fecid."-".$prmid."-".$proid."-".$productoPpt."-".$compraPpt.".png";
                                                $nuevoPrp->prpimagen            = $imagenProducto;
                                                if($nuevoPrp->save()){
                                                    $prpid = $nuevoPrp->prpid;
                                                    $log["NUEVO_PRP_CREADO"][] = $i."-".$prpid;
                                                }else{
                    
                                                }
                                            }
                    
                                            $csp = cspcanalessucursalespromociones::join('prmpromociones as prm', 'prm.prmid', 'cspcanalessucursalespromociones.prmid')
                                                                            ->where('cspcanalessucursalespromociones.cscid', $cscid)
                                                                            ->where('cspcanalessucursalespromociones.fecid', $fecid)
                                                                            // ->where('cspcanalessucursalespromociones.prmid', $prmid)
                                                                            // ->where('prm.prmcodigo', $codPromoc)
                                                                            ->where('prm.prmmecanica', $mecanica)
                                                                            ->where('prm.prmsku', $sku)
                                                                            ->first([
                                                                                'cspcanalessucursalespromociones.cspid', 
                                                                                'cspcanalessucursalespromociones.cspcantidadcombo', 
                                                                                'cspcanalessucursalespromociones.cspcantidadplancha', 
                                                                                'cspcanalessucursalespromociones.created_at'
                                                                            ]);
                    
                                            $cspid = 0;
                                            if($csp){
                                                
                                                $cspid = $csp->cspid;
                                                // SI EL CODIGO DE LA PROMOCION SE REPITE SUMAR LA CANTIDAD DE COMBOS Y PLANCHAS
                    
                                                $csp->csptotalcombo        = $precXcombo;
                                                $csp->csptotalplancha      = $precXplanc;
                                                $csp->csptotal             = $precXtodo;
                                                $csp->cspestado            = 1;
                                                $csp->cspnuevo             = $nuevoProm;
                                                $csp->cspzona              = $ex_zona;
                                                
                                                if($combos != 'NA'){
                                                    $csp->cspcantidadcombo   = $csp->cspcantidadcombo + $combos;
                                                }else{
                                                    $csp->cspcantidadcombo   = $combos;
                                                }

                                                if($planchas != 'NA'){
                                                    $csp->cspcantidadplancha = $csp->cspcantidadplancha + $planchas;
                                                }else{
                                                    $csp->cspcantidadplancha   = $planchas;
                                                }
                                                
                                                $csp->cspiniciopromo = $ex_iniciopromo;
                                                $csp->cspfinpromo    = $ex_finpromo;
                                                
                                                if($csp->update()){
                    
                                                }else{
                                                    
                                                }
                                                
                    
                                            }else{
                                                $nuevoCsp = new cspcanalessucursalespromociones;
                                                $nuevoCsp->cscid                = $cscid;
                                                $nuevoCsp->fecid                = $fecid;
                                                $nuevoCsp->prmid                = $prmid;
                                                $nuevoCsp->cspzona              = $ex_zona;
                                                // $nuevoCsp->cspcodigoprincipal   = $codPrinci;
                                                $nuevoCsp->cspvalorizado        = 0;
                                                $nuevoCsp->cspplanchas          = 0;
                                                $nuevoCsp->cspcompletado        = 0;
                    
                                                $nuevoCsp->cspcantidadcombo     = $combos;
                                                $nuevoCsp->cspcantidadplancha   = $planchas;
                                                $nuevoCsp->csptotalcombo        = $precXcombo;
                                                $nuevoCsp->csptotalplancha      = $precXplanc;
                                                $nuevoCsp->csptotal             = $precXtodo;
                                                $nuevoCsp->cspnuevo             = $nuevoProm;

                                                $nuevoCsp->cspiniciopromo = $ex_iniciopromo;
                                                $nuevoCsp->cspfinpromo    = $ex_finpromo;

                                                if($nuevoCsp->save()){
                                                    $cspid = $nuevoCsp->cspid;
                                                    $log["NUEVO_PROMOCIONES_ASIGNDADAS"][] = $i."-".$cspid;
                                                }else{
                                                    
                                                }
                                            }

                                        }else{

                                            $log['TIPO_PROMOCION_NO_EXISTE'][] = "El tpr: ".$tipoPromo;

                                            // $nuevoTipoPromocion = new tprtipospromociones;
                                            // $nuevoTipoPromocion->tprnombre  = $tipoPromo;
                                            // $nuevoTipoPromocion->tpricono   = null;
                                            // if($nuevoTipoPromocion->save()){
                                            //     $tprid = $nuevoTipoPromocion->tprid;
                                            // }else{
                            
                                            // }
                                        }
                            
                                        

                                    }else{
                                        
                                    }

                                    
                                }else{
                                    $log['CATEGORIAS_NO_IDENTIFICADAS'][] = "PRODUCTO DE CATEGORIA: ".$categoria." LINEA: ".$i;
                                    $respuesta = false;
                                    $mensaje   = "Hay categorias que no existen (".$categoria.")";
                                }
        
                            }else{
                                $log['SUCURSALES_NO_IDENTIFICADAS'][] = "SOLDTO: ".$soldTo." LINEA: ".$i;
                                $respuesta = false;
                                $mensaje   = "Hay algunas sucursales que no existen (".$soldTo.")";
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
                $respuesta = false;
                $mensaje   = "El excel no se pudo guardar en el servidor";
            }

            $nuevoCargaArchivo = new carcargasarchivos;
            $nuevoCargaArchivo->tcaid            = 1;
            $nuevoCargaArchivo->fecid            = $fecid;
            $nuevoCargaArchivo->usuid            = $usuusuario->usuid;
            $nuevoCargaArchivo->carnombrearchivo = $archivo;
            $nuevoCargaArchivo->carubicacion     = $fichero_subido;
            $nuevoCargaArchivo->carexito         = $cargarData;
            $nuevoCargaArchivo->carurl           = env('APP_URL').'/Sistema/cargaArchivos/promociones/'.$archivo;
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
            "respuesta"      => true,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
            "linea"          => $linea,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev,
            "numeroCelda"    => $numeroCelda,
            "log"    => $log,
        ]);
        
        $descripcion = "CARGAR DATA DE PROMOCIONES DE UN EXCEL AL SISTEMA";

        if($cargarData == false){
            $descripcion = "SUBIR EXCEL PARA REVISAR Y POSTERIORMENTE CARGAR DICHA DATA EN PROMOCIONES";
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
