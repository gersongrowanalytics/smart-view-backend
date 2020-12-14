<?php

namespace App\Http\Controllers\Sistema\Trimestre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\tritrimestres;
use App\ttrtritre;
use App\trftrimestresfechas;
use App\tsutipospromocionessucursales;
use App\sucsucursales;
use App\tprtipospromociones;
use App\catcategorias;
use App\scasucursalescategorias;

class ActualizarTrimestreController extends Controller
{
    public function ActualizarTrimestre(Request $request)
    {

        $logs = array();
        $respuesta      = true;
        $mensaje        = 'Se actualizo correctamente el Rebate Trimestral';
        $datos          = [];
        $mensajeDetalle = '';
        $mensajedev     = null;

        $tris = tritrimestres::where('triestado', 1)
                                ->get(['triid', 'fecid']);


        $sucs = sucsucursales::where('sucestado', 1)->get(['sucid', 'treid']);
        $tprs = tprtipospromociones::all();
        $cats = catcategorias::all();

        DB::beginTransaction();
        try{
            foreach($tris as $tri){
                $trfs = trftrimestresfechas::where('triid', $tri->triid)
                                            ->get();
    
                foreach($tprs as $tpr){
                    foreach($sucs as $suc){
    
                        $tsuobjetivotrimestral = 0;
                        $tsurealtrimestral = 0;
    
                        foreach($trfs as $trf){
                            $sumaObjetivoTsu = tsutipospromocionessucursales::where('fecid', $trf->fecid)
                                                                        ->where('sucid', $suc->sucid)
                                                                        ->where('tprid', $tpr->tprid)
                                                                        ->sum('tsuvalorizadoobjetivo');
    
                            $tsuobjetivotrimestral = $tsuobjetivotrimestral + $sumaObjetivoTsu;
    
                            $sumaRealTsu = tsutipospromocionessucursales::where('fecid', $trf->fecid)
                                                                        ->where('sucid', $suc->sucid)
                                                                        ->where('tprid', $tpr->tprid)
                                                                        ->sum('tsuvalorizadoreal');
    
                            $tsurealtrimestral = $tsurealtrimestral + $sumaRealTsu;
    
    
                        }
    
                        foreach($cats as $cat){
    
                            $scaobjetivotrimestral = 0;
                            $scarealtrimestral = 0;
    
                            foreach($trfs as $trf){
                                $sumaObjetivoSca = scasucursalescategorias::join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                                                        ->where('tsu.fecid', $trf->fecid)
                                                                        ->where('tsu.sucid', $suc->sucid)
                                                                        ->where('tsu.tprid', $tpr->tprid)
                                                                        ->where('catid', $cat->catid)
                                                                        ->sum('scavalorizadoobjetivo');
    
                                $scaobjetivotrimestral = $scaobjetivotrimestral + $sumaObjetivoSca;
    
                                $sumaRealSca = scasucursalescategorias::join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                                                        ->where('tsu.fecid', $trf->fecid)
                                                                        ->where('tsu.sucid', $suc->sucid)
                                                                        ->where('tsu.tprid', $tpr->tprid)
                                                                        ->where('catid', $cat->catid)
                                                                        ->sum('scavalorizadoreal');
    
                                $scarealtrimestral = $scarealtrimestral + $sumaRealSca;
                            }
    
                            $scafacturartrimestral = $scaobjetivotrimestral - $scarealtrimestral;
    
                            if($scaobjetivotrimestral > 0){
                                $scacumplimientotrimestral = ($scarealtrimestral*100)/$scaobjetivotrimestral;
                            }else{
                                $scacumplimientotrimestral = 0;
                            }
                            
    
                            $scae = scasucursalescategorias::join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                                            ->where('tsu.fecid', $tri->fecid)
                                                            ->where('tsu.sucid', $suc->sucid)
                                                            ->where('tsu.tprid', $tpr->tprid)
                                                            ->where('catid', $cat->catid)
                                                            ->first(['scasucursalescategorias.scaid']);
    
                            if($scae){
                                $scae->scaobjetivotrimestral     = $scaobjetivotrimestral;
                                $scae->scarealtrimestral         = $scarealtrimestral;
                                $scae->scafacturartrimestral     = $scafacturartrimestral;
                                $scae->scacumplimientotrimestral = $scacumplimientotrimestral;
                                $scae->update();
                            }
                        }
    
                        $tsufacturartrimestral = $tsuobjetivotrimestral - $tsurealtrimestral;
    
                        if($tsuobjetivotrimestral > 0){
                            $tsucumplimientotrimestral = ($tsurealtrimestral*100)/$tsuobjetivotrimestral;
                        }else{
                            $tsucumplimientotrimestral = 0;
                        }
    
                        $tsue = tsutipospromocionessucursales::where('fecid', $tri->fecid)
                                                        ->where('sucid', $suc->sucid)
                                                        ->where('tprid', $tpr->tprid)
                                                        ->first(['tsuid']);
    
                        if($tsue){
                            $tsue->tsuobjetivotrimestral     = $tsuobjetivotrimestral;
                            $tsue->tsurealtrimestral         = $tsurealtrimestral;
                            $tsue->tsufacturartrimestral     = $tsufacturartrimestral;
                            $tsue->tsucumplimientotrimestral = $tsucumplimientotrimestral;
                            if($tsue->update()){
    
                                $ttrs = ttrtritre::where('fecid', $tri->fecid)
                                                ->where('triid', $tri->triid)
                                                ->where('treid', $suc->treid)
                                                ->where('ttrporcentajedesde', '<=', round($tsucumplimientotrimestral))
                                                ->where('ttrporcentajehasta', '>=', round($tsucumplimientotrimestral))
                                                ->get([
                                                    'ttrid',
                                                    'ttrporcentajedesde',
                                                    'ttrporcentajehasta',
                                                    'ttrporcentajerebate',
                                                    'catid'
                                                ]);
    
                                $totalRebate = 0;
                                
                                if(sizeof($ttrs) > 0){
                                    foreach($ttrs as $ttr){
    
                                        $logs[$tri->fecid]["entra"][] = "Si entra en la escala rebate: ".$tsue->tsuid." de la sucursal: ".$suc->sucid." con un cumplimiento de: ".round($tsucumplimientotrimestral)." y escalas desde: ".$ttr->ttrporcentajedesde." y hasta: ".$ttr->ttrporcentajehasta;
        
                                        $sca = scasucursalescategorias::where('tsuid', $tsue->tsuid)
                                                                        ->where('fecid', $tri->fecid)
                                                                        ->where('catid', $ttr->catid)
                                                                        ->first([
                                                                            'scaid',
                                                                            'scarealtrimestral'
                                                                        ]);
        
                                        if($sca){
                                            $nuevoRebate = ($sca->scarealtrimestral * $ttr->ttrporcentajerebate)/100;
                                            $totalRebate = $totalRebate + $nuevoRebate;
                                        }else{
        
                                        }
                                    }
                                }else{
                                    $logs[$tri->fecid]["noentra"][] = "Si entra en la escala rebate: ".$tsue->tsuid." de la sucursal: ".$suc->sucid." con un cumplimiento de: ".round($tsucumplimientotrimestral)." y escalas desde: ".$ttr->ttrporcentajedesde." y hasta: ".$ttr->ttrporcentajehasta;
                                }
    
                                $tsuu = tsutipospromocionessucursales::find($tsue->tsuid);
                                $tsuu->tsurebatetrimestral = $totalRebate;
                                $tsuu->update();
    
    
                            }else{
                                
                            }
                        }
                    }
                }
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            $mensajedev = $e->getMessage();
            $respuesta  = false;
            $mensaje    = 'Lo sentimos ocurrio un error al momento de actualizar el rebate trimestral';
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev,
            "logs"           => $logs
        ]);
        
        return $requestsalida;

    }
}
