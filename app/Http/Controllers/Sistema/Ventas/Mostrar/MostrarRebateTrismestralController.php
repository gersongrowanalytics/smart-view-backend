<?php

namespace App\Http\Controllers\Sistema\Ventas\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ttrtritre;
use App\trftrimestresfechas;
use App\tritrimestres;
use App\tretiposrebates;
use App\catcategorias;

class MostrarRebateTrismestralController extends Controller
{
    public function MostrarRebateTrismestral(Request $request)
    {

        $re_mes = $request['re_mes'];
        $re_anio = $request['re_anio'];

        $data = array();

        $trf = trftrimestresfechas::join('fecfechas as fec', 'fec.fecid', 'trftrimestresfechas.fecid')
                                    ->where('fec.fecano', $re_anio)
                                    ->where('fec.fecmes', $re_mes)
                                    ->where('fec.fecdia', "01")
                                    ->first();

        if($trf){

            $cats = catcategorias::all();

            $ttrs = ttrtritre::join('tretiposrebates as tre', 'tre.treid', 'ttrtritre.treid')
                            ->join('tprtipospromociones as tpr', 'tpr.tprid', 'ttrtritre.tprid')
                            ->where('ttrtritre.triid', $trf->triid)
                            ->distinct('treid')
                            ->get([
                                'tre.treid',
                                'tre.trenombre',
                                'tpr.tprid',
                                'tpr.tprnombre',
                            ]);

            if(sizeof($ttrs) > 0){

                foreach($ttrs as $posicionTtr => $ttr){

                    if($posicionTtr == 0){
                        $ttrs[$posicionTtr]['mostrando'] = true;
                    }else{
                        $ttrs[$posicionTtr]['mostrando'] = false;
                    }
    
                    $ttrs[$posicionTtr]['ocultando'] = false;
                    $ttrs[$posicionTtr]['retroceder'] = false;
    
                    $ttrs[$posicionTtr]['data'] = array();
                    $arr_datas = array();
    
                    $ttrsPorcentajes = ttrtritre::join('tretiposrebates as tre', 'tre.treid', 'ttrtritre.treid')
                                ->where('ttrtritre.triid', $trf->triid)
                                ->where('tre.treid', $ttr->treid)
                                ->distinct('ttrporcentajedesde')
                                ->distinct('ttrporcentajehasta')
                                ->get([
                                    'ttrporcentajedesde',
                                    'ttrporcentajehasta'
                                ]);
    
                    foreach($ttrsPorcentajes as $ttrsPorcentaje){
                        $arr_data = array(
                            'tprid'  => $ttr->tprid,
                            'tprnombre'  => $ttr->tprnombre,
                            'trenombre'  => $ttr->trenombre,
                            'ttrporcentajedesde'  => $ttrsPorcentaje->ttrporcentajedesde,
                            'ttrporcentajehasta'  => $ttrsPorcentaje->ttrporcentajehasta,
                            'ttrporcentajerebate' => "1",
                        );
    
                        $ttrscats = ttrtritre::join('tretiposrebates as tre', 'tre.treid', 'ttrtritre.treid')
                                            ->where('ttrtritre.triid', $trf->triid)
                                            ->where('tre.treid', $ttr->treid)
                                            ->where('ttrporcentajedesde', $ttrsPorcentaje->ttrporcentajedesde)
                                            ->where('ttrporcentajehasta', $ttrsPorcentaje->ttrporcentajehasta)
                                            ->get([
                                                'catid',
                                                'ttrporcentajerebate'
                                            ]);
    
    
                        foreach($cats as $cat){
                            
                            $porcentajeTtr = 0;
    
                            foreach($ttrscats as $ttrscat){
                                
                                if($ttrscat->catid == $cat->catid){
                                    $porcentajeTtr = $ttrscat->ttrporcentajerebate;
                                }
    
                            }
    
                            $arr_data['cat-'.$cat->catid] = $porcentajeTtr;
    
                        }
    
                        $arr_datas[] = $arr_data;
                    }
    
                    $ttrs[$posicionTtr]['data'] = $arr_datas;
    
    
                }
    
                $data = $ttrs;

            }else{
                $data = $this->ObtenerDataVacia();
            }

        }else{

            $data = $this->ObtenerDataVacia();

            

        }

        return response()->json([
            "respuesta" => true,
            "datos"     => $data
        ]);

    }

    public function ObtenerDataVacia()
    {
        $data = array();

        $tre = tretiposrebates::where('trenombre', 'ZB')
                                ->first();

        if($tre){

            $data[] = array(
                "treid"      => $tre->treid,
                "trenombre"  => $tre->trenombre,
                "mostrando"  => true,
                "ocultando"  => false,
                "retroceder" => false,
                "tprid"      => 1,
                "tprnombre"  => "Sell In",
                "data" => array(
                    array(
                        "cat-1" => "1",
                        "cat-2" => "1",
                        "cat-3" => "1",
                        "cat-4" => "1",
                        "cat-5" => "1",
                        "cat-6" => 0,
                        "cat-7" => 0,
                        "tprid" => 1,
                        "tprnombre" => "Sell In",
                        "trenombre" => "ZB",
                        "ttrporcentajedesde"  => "100",
                        "ttrporcentajehasta"  => "104",
                        "ttrporcentajerebate" => "1",
                    )
                )
            );

        }

        return $data;
    }
}
