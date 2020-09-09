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

class ObjetivoCargarController extends Controller
{
    public function CargarObjetivo(Request $request)
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

            $fichero_subido = base_path().'/public/'.basename($_FILES['file']['name']);
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


                    $usuarioCliente = usuusuarios::join('ussusuariossucursales as uss', 'uss.usuid', 'usuusuarios.usuid')
                                                    ->where('usuusuarios.ususoldto', $soldto)
                                                    ->first(['uss.sucid']);                                                


                    $tsu = tsutipospromocionessucursales::where('fecid', $fecid)
                                                        ->where('sucid', $usuarioCliente->sucid)
                                                        ->first(['tsuid']);
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
                        $nuevotsu->sucid = $usuarioCliente->sucid;
                        $nuevotsu->tprid = 1;
                        $nuevotsu->tsuporcentajecumplimiento = 0;
                        $nuevotsu->tsuvalorizadoobjetivo = $objetivo;
                        $nuevotsu->tsuvalorizadoreal = 0;
                        $nuevotsu->tsuvalorizadorebate = 0;
                        $nuevotsu->tsuvalorizadotogo = 0;
                        if($nuevotsu->save()){

                        }else{

                        }
                    }

                    $sca = scasucursalescategorias::where('fecid', $fecid)
                                                ->where('sucid', $usuarioCliente->sucid)
                                                ->where(function ($query) use($sector) {

                                                    if($sector == 'Family'){
                                                        $query->where('catid', 1);
                                                    }else if($sector == 'Wipes'){
                                                        $query->where('catid', 4);
                                                    }else if($sector == 'Adult'){
                                                        $query->where('catid', 3);
                                                    }else if($sector == 'Feminine'){
                                                        $query->where('catid', 5);
                                                    }else if($sector == 'Infant + Child'){
                                                        $query->where('catid', 2);
                                                    }else{
                                                        $query->where('catid', 0);
                                                    }
                                                })
                                                ->first(['scaid']);

                    $scaid = 0;
                    if($sca){
                        $scaid = $sca->scaid;

                        $sca->scavalorizadoobjetivo = $sca->scavalorizadoobjetivo+$objetivo;
                        if($sca->update()){

                        }else{

                        }
                    }else{
                        $categoriaid = 0;
                        $categoriaNombre = '';
                        if($sector == 'Family'){
                            $categoriaid = 1;
                            $categoriaNombre = 'Family Care';
                        }else if($sector == 'Wipes'){
                            $categoriaid = 4; 
                            $categoriaNombre = 'Wipes';
                        }else if($sector == 'Adult'){
                            $categoriaid = 3;
                            $categoriaNombre = 'Adult Care';
                        }else if($sector == 'Feminine'){
                            $categoriaid = 5;
                            $categoriaNombre = 'Fem Care';
                        }else if($sector == 'Infant + Child'){
                            $categoriaid = 2;
                            $categoriaNombre = 'Infant Care';
                        }

                        $nuevosca = new scasucursalescategorias;
                        $nuevosca->sucid                 = $usuarioCliente->sucid;
                        $nuevosca->catid                 = $categoriaid;
                        $nuevosca->fecid                 = $fecid;
                        $nuevosca->tsuid                 = $tsuid;
                        $nuevosca->scavalorizadoobjetivo = $objetivo;
                        $nuevosca->scaiconocategoria     = env('APP_URL').'/Sistema/categorias-tiposPromociones/img/iconos/'.$categoriaNombre.'-Sell In.png';
                        $nuevosca->scavalorizadoreal     = 0;
                        $nuevosca->scavalorizadotogo     = 0;
                        if($nuevosca->save()){

                        }else{

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



    }
}
