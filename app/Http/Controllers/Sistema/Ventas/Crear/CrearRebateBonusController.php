<?php

namespace App\Http\Controllers\Sistema\Ventas\Crear;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\rbbrebatesbonus;
use App\catcategorias;
use App\rscrbsscategorias;
use App\usuusuarios;
use App\fecfechas;

class CrearRebateBonusController extends Controller
{
    public function CrearRebateBonus(Request $request)
    {
        $respuesta = true;

        $re_mes = $request['re_mes'];
        $re_anio = $request['re_anio'];

        $re_porcentaje = $request['re_porcentaje'];
        // $re_cumplimiento = $request[''];
        $re_cumplimiento = 100;
        $re_descripcion = $request['re_descripcion'];
        $re_cats = $request['re_cats'];


        $usutoken = $request->header('api_token');
        $usu = usuusuarios::where('usutoken', $usutoken)->first(['usuid', 'usuusuario']);

        if($usu){

            $fec = fecfechas::where('fecmes', $re_mes)
                            ->where('fecano', $re_anio)
                            ->first();

            if($fec){

                $rbb = rbbrebatesbonus::where('fecid', $fec->fecid)->first();
                $rbbid = 0;
                if($rbb){
                    $rbbid = $rbb->rbbid;
                    $rbb->usuid = $usu->usuid;
                    $rbb->rbbporcentaje = $re_porcentaje;
                    $rbb->rbbcumplimiento = $re_cumplimiento;
                    $rbb->rbbdescripcion = $re_descripcion;
                    $rbb->update();

                }else{
                    $rbbn = new rbbrebatesbonus;
                    $rbbn->fecid = $fec->fecid;
                    $rbbn->usuid = $usu->usuid;
                    $rbbn->rbbporcentaje = $re_porcentaje;
                    $rbbn->rbbcumplimiento = $re_cumplimiento;
                    $rbbn->rbbdescripcion = $re_descripcion;
                    $rbbn->save();
                    $rbbid = $rbbn->rbbid;
                }

                rscrbsscategorias::where('rbbid', $rbbid)
                                ->where('fecid', $fec->fecid)
                                ->delete();

                foreach($re_cats as $re_cat){
                    if($re_cat['seleccionado'] == true){
                        $rscn = new rscrbsscategorias;
                        $rscn->fecid = $fec->fecid;
                        $rscn->rbbid = $rbbid;
                        $rscn->sucid = 1;
                        // $rscn->rbsid = ;
                        $rscn->catid = $re_cat['catid'];
                        $rscn->rscestado = true;
                        $rscn->save();
                    }
                }



                
            }else{

            }

        }else{

        }

        return response()->json([
            "respuesta" => $respuesta
        ]);

    }
}
