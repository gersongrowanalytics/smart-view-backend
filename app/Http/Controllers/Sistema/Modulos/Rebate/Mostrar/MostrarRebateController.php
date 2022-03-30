<?php

namespace App\Http\Controllers\Sistema\Modulos\Rebate\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\fecfechas;
use App\catcategorias;
use App\tprtipospromociones;
use App\trrtiposrebatesrebates;
use App\rtprebatetipospromociones;

class MostrarRebateController extends Controller
{
    public function MostrarRebate(Request $request)
    {

        $re_mes  = $request['mes'];
        $re_anio = $request['anio'];

        $respuesta = true;

        $fec = fecfechas::where('fecmes', $re_mes)
                        ->where('fecano', $re_anio)
                        ->where('fecdia', '01')
                        ->first();

        $arr_data = array();
        $tablas_data = array();

        if($fec){ 

            $tprs = tprtipospromociones::get(['tprid', 'tprnombre']);


            foreach($tprs as $tpr){


                $rtps = rtprebatetipospromociones::where('fecid', $fec->fecid)
                                                ->where('tprid', $tpr->tprid)
                                                ->get();

                foreach($rtps as $rtp){

                    $trrs_grupos = trrtiposrebatesrebates::join('tretiposrebates as tre', 'trrtiposrebatesrebates.treid', 'tre.treid')
                                                    ->where('rtpid', $rtp->rtpid)
                                                    ->distinct('trrtiposrebatesrebates.treid')
                                                    ->get([
                                                        'trrtiposrebatesrebates.treid',
                                                        'trenombre'
                                                    ]);

                    foreach($trrs_grupos as $trrs_grupo){
                        
                        $arr_data_agregar = array();

                        $arr_data_agregar['trenombre'] = $trrs_grupo->trenombre;
                        $arr_data_agregar['rtpporcentajedesde'] = $rtp->rtpporcentajedesde;
                        $arr_data_agregar['rtpporcentajehasta'] = $rtp->rtpporcentajehasta;
                        $arr_data_agregar['rtpporcentajerebate'] = $rtp->rtpporcentajerebate;
                        $arr_data_agregar['tprnombre'] = $tpr->tprnombre;



                        $trrs = trrtiposrebatesrebates::where('rtpid', $rtp->rtpid)
                                                    ->where('treid', $trrs_grupo->treid)
                                                    ->get([
                                                        'catid'
                                                    ]);

                        foreach($trrs as $trr){
                            $arr_data_agregar['cat-'.$trr->catid] = $rtp->rtpporcentajerebate;
                        }

                        $arr_data[] = $arr_data_agregar;

                        if(sizeof($tablas_data) > 0){

                            $encontroData = false;
                            $posicionTablaEncontrada = 0;

                            foreach($tablas_data as $posicionTablasData => $tablaData){
                                if($tablaData['treid'] == $trrs_grupo->treid){
                                    $encontroData = true;
                                    $posicionTablaEncontrada = $posicionTablasData;
                                }
                            }

                            if($encontroData == true){

                                $tablas_data[$posicionTablaEncontrada]['data'][] = $arr_data_agregar;

                            }else{
                                $tablas_data[] = array(
                                    "treid" => $trrs_grupo->treid,
                                    "trenombre" => $trrs_grupo->trenombre,
                                    "data" => [$arr_data_agregar],
                                    "retroceder" => false,
                                    "ocultando"  => false,
                                    "mostrando"  => false,
                                );    
                            }

                        }else{
                            $tablas_data[] = array(
                                "treid" => $trrs_grupo->treid,
                                "trenombre" => $trrs_grupo->trenombre,
                                "data" => [$arr_data_agregar],
                                "retroceder" => false,
                                "ocultando"  => false,
                                "mostrando"  => true,
                            );
                        }

                    }
                }
            }

        }else{
            $respuesta = false;
        }

        $descargar_data = $this->ArmarGeneradorExcelRebate($re_anio, $re_mes, $arr_data);

        return response()->json([
            "descargar_data" => $descargar_data,
            "datos"     => $arr_data,
            "tablas"    => $tablas_data,
            "respuesta" => $respuesta,
            "datos_enviado" => [$re_mes, $re_anio]
        ]);

    }

    public function ArmarGeneradorExcelRebate($anio, $mes, $data)
    {

        $nuevoArray = array(
            array(
                "columns" => [],
                "data"    => []
            )
        );

        foreach($data as $posDat => $dat){

            if($posDat == 0){
                $arrayTitulos = array(
                    array("title" => "", "width" => array("wpx" => 100)),
                );

                $nuevoArray[0]['columns'] = $arrayTitulos;

                $arrayFilaExcel = array(
                    array(
                        "value" => "AÑO",
                        "style" => array(
                            "font" => array(
                                "sz" => "9",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF004FB8"
                                )
                            )
                            
                        )
                    ),
                    array(
                        "value" => "MES",
                        "style" => array(
                            "font" => array(
                                "sz" => "9",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF004FB8"
                                )
                            )
                            
                        )
                    ),
                    array(
                        "value" => "CUSTOMER GROUP",
                        "style" => array(
                            "font" => array(
                                "sz" => "9",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF004FB8"
                                )
                            )
                            
                        )
                    ),
                    array(
                        "value" => "ESCALA INICIAL",
                        "style" => array(
                            "font" => array(
                                "sz" => "9",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF004FB8"
                                )
                            )
                            
                        )
                    ),

                    array(
                        "value" => "ESCALA FINAL",
                        "style" => array(
                            "font" => array(
                                "sz" => "9",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF004FB8"
                                )
                            )
                            
                        )
                    ),
                    array(
                        "value" => "MÉTRICA",
                        "style" => array(
                            "font" => array(
                                "sz" => "9",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF004FB8"
                                )
                            )
                            
                        )
                    ),
                    array(
                        "value" => "INFANT",
                        "style" => array(
                            "font" => array(
                                "sz" => "9",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF004FB8"
                                )
                            )
                            
                        )
                    ),
                    array(
                        "value" => "WIPES",
                        "style" => array(
                            "font" => array(
                                "sz" => "9",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF004FB8"
                                )
                            )
                            
                        )
                    ),
                    array(
                        "value" => "FAMILY",
                        "style" => array(
                            "font" => array(
                                "sz" => "9",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF004FB8"
                                )
                            )
                            
                        )
                    ),
                    array(
                        "value" => "ADULT",
                        "style" => array(
                            "font" => array(
                                "sz" => "9",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF004FB8"
                                )
                            )
                            
                        )
                    ),
                    array(
                        "value" => "FEM",
                        "style" => array(
                            "font" => array(
                                "sz" => "9",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF004FB8"
                                )
                            )
                            
                        )
                    ),
                );

                $nuevoArray[0]['data'][] = $arrayFilaExcel;

            }

            $arrayFilaExcel = array(
                array(
                    "value" => $anio,
                    "style" => array(
                        "font" => array(
                            "sz" => "9",
                            "bold" => true,
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFF2F2F2"
                            )
                        )
                    )
                ),
                array(
                    "value" => $mes,
                    "style" => array(
                        "font" => array(
                            "sz" => "9",
                            "bold" => true,
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFF2F2F2"
                            )
                        )
                    )
                ),
                array(
                    "value" => $dat['trenombre'],
                    "style" => array(
                        "font" => array(
                            "sz" => "9",
                            "bold" => true,
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFF2F2F2"
                            )
                        )
                    )
                ),
                array(
                    "value" => $dat['rtpporcentajedesde'],
                    "style" => array(
                        "font" => array(
                            "sz" => "9",
                            "bold" => true,
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFF2F2F2"
                            )
                        )
                    )
                ),
                array(
                    "value" => $dat['rtpporcentajehasta'],
                    "style" => array(
                        "font" => array(
                            "sz" => "9",
                            "bold" => true,
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFF2F2F2"
                            )
                        )
                    )
                ),
                array(
                    "value" => $dat['tprnombre'],
                    "style" => array(
                        "font" => array(
                            "sz" => "9",
                            "bold" => true,
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFF2F2F2"
                            )
                        )
                    )
                ),
                array(
                    "value" => $dat['cat-1'],
                    "style" => array(
                        "font" => array(
                            "sz" => "9",
                            "bold" => true,
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFF2F2F2"
                            )
                        )
                    )
                ),
                array(
                    "value" => $dat['cat-2'],
                    "style" => array(
                        "font" => array(
                            "sz" => "9",
                            "bold" => true,
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFF2F2F2"
                            )
                        )
                    )
                ),
                array(
                    "value" => $dat['cat-3'],
                    "style" => array(
                        "font" => array(
                            "sz" => "9",
                            "bold" => true,
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFF2F2F2"
                            )
                        )
                    )
                ),
                array(
                    "value" => $dat['cat-4'],
                    "style" => array(
                        "font" => array(
                            "sz" => "9",
                            "bold" => true,
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFF2F2F2"
                            )
                        )
                    )
                ),
                array(
                    "value" => $dat['cat-5'],
                    "style" => array(
                        "font" => array(
                            "sz" => "9",
                            "bold" => true,
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFF2F2F2"
                            )
                        )
                    )
                ),
            );

            $nuevoArray[0]['data'][] = $arrayFilaExcel;
        }

        return $nuevoArray;

    }

    public function MostrarRebateBK(Request $request)
    {

        $re_mes  = $request['mes'];
        $re_anio = $request['anio'];

        $respuesta = true;

        $fec = fecfechas::where('fecmes', $re_mes)
                        ->where('fecano', $re_anio)
                        ->where('fecdia', '01')
                        ->first();

        $arr_data = array();
        $tablas_data = array();

        if($fec){ 

            $tprs = tprtipospromociones::get(['tprid', 'tprnombre']);


            foreach($tprs as $tpr){


                $rtps = rtprebatetipospromociones::where('fecid', $fec->fecid)
                                                ->where('tprid', $tpr->tprid)
                                                ->get();

                foreach($rtps as $rtp){

                    $trrs_grupos = trrtiposrebatesrebates::join('tretiposrebates as tre', 'trrtiposrebatesrebates.treid', 'tre.treid')
                                                    ->where('rtpid', $rtp->rtpid)
                                                    ->distinct('trrtiposrebatesrebates.treid')
                                                    ->get([
                                                        'trrtiposrebatesrebates.treid',
                                                        'trenombre'
                                                    ]);

                    foreach($trrs_grupos as $trrs_grupo){
                        
                        $arr_data_agregar = array();

                        $arr_data_agregar['trenombre'] = $trrs_grupo->trenombre;
                        $arr_data_agregar['rtpporcentajedesde'] = $rtp->rtpporcentajedesde;
                        $arr_data_agregar['rtpporcentajehasta'] = $rtp->rtpporcentajehasta;
                        $arr_data_agregar['rtpporcentajerebate'] = $rtp->rtpporcentajerebate;
                        $arr_data_agregar['tprnombre'] = $tpr->tprnombre;



                        $trrs = trrtiposrebatesrebates::where('rtpid', $rtp->rtpid)
                                                    ->where('treid', $trrs_grupo->treid)
                                                    ->get([
                                                        'catid'
                                                    ]);

                        foreach($trrs as $trr){
                            $arr_data_agregar['cat-'.$trr->catid] = $rtp->rtpporcentajerebate;
                        }

                        $arr_data[] = $arr_data_agregar;

                        if(sizeof($tablas_data) > 0){

                            $encontroData = false;
                            $posicionTablaEncontrada = 0;

                            foreach($tablas_data as $posicionTablasData => $tablaData){
                                if($tablaData['treid'] == $trrs_grupo->treid){
                                    $encontroData = true;
                                    $posicionTablaEncontrada = $posicionTablasData;
                                }
                            }

                            if($encontroData == true){

                                $tablas_data[$posicionTablaEncontrada]['data'][] = $arr_data_agregar;

                            }else{
                                $tablas_data[] = array(
                                    "treid" => $trrs_grupo->treid,
                                    "trenombre" => $trrs_grupo->trenombre,
                                    "data" => [$arr_data_agregar],
                                    "retroceder" => false,
                                    "ocultando"  => false,
                                    "mostrando"  => false,
                                );    
                            }

                        }else{
                            $tablas_data[] = array(
                                "treid" => $trrs_grupo->treid,
                                "trenombre" => $trrs_grupo->trenombre,
                                "data" => [$arr_data_agregar],
                                "retroceder" => false,
                                "ocultando"  => false,
                                "mostrando"  => true,
                            );
                        }

                    }
                }
            }

        }else{
            $respuesta = false;
        }

        $descargar_data = $this->ArmarGeneradorExcelRebate($re_anio, $re_mes, $arr_data);

        return response()->json([
            "descargar_data" => $descargar_data,
            "datos"     => $arr_data,
            "tablas"    => $tablas_data,
            "respuesta" => $respuesta,
            "datos_enviado" => [$re_mes, $re_anio]
        ]);

    }
}
