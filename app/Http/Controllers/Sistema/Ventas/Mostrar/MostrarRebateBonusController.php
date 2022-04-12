<?php

namespace App\Http\Controllers\Sistema\Ventas\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\rbbrebatesbonus;
use App\catcategorias;
use App\rscrbsscategorias;

class MostrarRebateBonusController extends Controller
{
    public function MostrarRebateBonus(Request $request)
    {

        $re_anio = $request['re_anio'];
        $re_mes = $request['re_mes'];

        $cats = catcategorias::where('catid', '!=', 6)
                            ->get();

        $rbb = rbbrebatesbonus::join('fecfechas as fec', 'rbbrebatesbonus.fecid', 'fec.fecid')
                                    ->where('fec.fecano', $re_anio)
                                    ->where('fec.fecmes', $re_mes)
                                    ->where('fec.fecdia', "01")
                                    ->first();

        $data = array();

        if($rbb){

            $data = $rbb;

            $rscs = rscrbsscategorias::where('rbbid', $rbb->rbbid)
                                    ->where('catid', '!=', 6)
                                    ->get();

            foreach($cats as $posicionCat => $cat){
                $cats[$posicionCat]['seleccionado'] = false;
                foreach($rscs as $rsc){

                    if($rsc->catid == $cat->catid){
                        $cats[$posicionCat]['seleccionado'] = true;
                    }

                }

            }

        }else{

            foreach($cats as $posicionCat => $cat){
                $cats[$posicionCat]['seleccionado'] = false;
            }

        }

        $data["cats"] = $cats;

        return response()->json([
            "respuesta" => true,
            "datos"     => $data
        ]);

    }
}
