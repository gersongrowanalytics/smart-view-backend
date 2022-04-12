<?php

namespace App\Http\Controllers\Sistema\Ventas\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ttrtritre;
use App\trftrimestresfechas;
use App\tritrimestres;
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
                            ->where('ttrtritre.triid', $trf->triid)
                            ->distinct('treid')
                            ->get([
                                'tre.treid',
                                'tre.trenombre'
                            ]);

            foreach($ttrs as $posicionTtr => $ttr){

                if($posicionTtr == 0){
                    $ttrs[$posicionTtr]['mostrando'] = true;
                }else{
                    $ttrs[$posicionTtr]['mostrando'] = false;
                }

                $ttrs[$posicionTtr]['ocultando'] = false;
                $ttrs[$posicionTtr]['retroceder'] = false;

                $ttrs[$posicionTtr]['data'] = array();


                $ttrs = ttrtritre::join('tretiposrebates as tre', 'tre.treid', 'ttrtritre.treid')
                            ->where('ttrtritre.triid', $trf->triid)
                            ->where('tre.treid', $ttr->treid)
                            ->distinct('ttrporcentajedesde')
                            ->distinct('ttrporcentajehasta')
                            ->get([
                                'ttrporcentajedesde',
                                'ttrporcentajehasta',
                            ]);

                foreach($ttrs as $ttr){
                    $arr_data = array(
                        'ttrporcentajedesde'  => $ttr->ttrporcentajedesde,
                        'ttrporcentajehasta'  => $ttr->ttrporcentajehasta,
                        'ttrporcentajerebate' => "1",
                    );

                    $ttrscats = ttrtritre::join('tretiposrebates as tre', 'tre.treid', 'ttrtritre.treid')
                                        ->where('ttrtritre.triid', $trf->triid)
                                        ->where('tre.treid', $ttr->treid)
                                        ->where('ttrporcentajedesde', $ttr->ttrporcentajedesde)
                                        ->where('ttrporcentajehasta', $ttr->ttrporcentajehasta)
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

                    $ttrs[$posicionTtr]['data'][] = $arr_data;
                }


            }

            $data = $ttrs;

        }else{

        }

        return response()->json([
            "respuesta" => true,
            "datos"     => $data
        ]);

    }
}
