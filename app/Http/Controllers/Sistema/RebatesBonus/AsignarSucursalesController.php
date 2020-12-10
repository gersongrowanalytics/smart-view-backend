<?php

namespace App\Http\Controllers\Sistema\RebatesBonus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\sucsucursales;
use App\rbbrebatesbonus;
use App\rbsrebatesbonussucursales;
use App\rscrbsscategorias;
use App\scasucursalescategorias;
use App\tsutipospromocionessucursales;

class AsignarSucursalesController extends Controller
{
    public function AsiganarSucursales()
    {

        $logs = array(
            "oct" => [],
            "novydic" => []
        );

        $rbbs = rbbrebatesbonus::all();
        $sucs = sucsucursales::where('sucestado', 1)->get(['sucid']);

        foreach($rbbs as $rbb){
            $rbss = rbsrebatesbonussucursales::where('rbbid', $rbb->rbbid)
                                            ->get(['rbsid']);

            if(sizeof($rbss) > 0){
                foreach($rbss as $rbs){

                    $rscs = rscrbsscategorias::where('rbsid', $rbs->rbsid)->get();

                    foreach($rscs as $rsc){
                        $rscd = rscrbsscategorias::find($rsc->rscid);
                        $rscd->delete();
                    }

                    $rbbd = rbsrebatesbonussucursales::find($rbs->rbsid);
                    $rbbd->delete();
                }
            }



            foreach($sucs as $suc){

                if($rbb->fecid == 3){
                    $scas = scasucursalescategorias::join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                            ->where('scasucursalescategorias.fecid', $rbb->fecid )
                                            ->where('tsu.tprid', 1)
                                            ->where('scasucursalescategorias.sucid', $suc->sucid)
                                            ->where('catid', '!=', 4)
                                            ->get();
                }else{
                    $scas = scasucursalescategorias::join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                            ->where('scasucursalescategorias.fecid', $rbb->fecid )
                                            ->where('tsu.tprid', 1)
                                            ->where('scasucursalescategorias.sucid', $suc->sucid)
                                            ->where('catid', 1)
                                            ->get();
                }
                

                $rbsobjetivo = 0;
                $rbsreal = 0;
                $rbscumplimiento = 0;
                $rbsrebate = 0;

                foreach($scas as $sca){
                    $rbsobjetivo     = $rbsobjetivo + $sca->scavalorizadoobjetivo;
                    $rbsreal         = $rbsreal + $sca->scavalorizadoreal;
                }

                if($rbsobjetivo > 1){
                    $rbscumplimiento = (100*$rbsreal)/$rbsobjetivo;
                }

                if($rbscumplimiento >= $rbb->rbbcumplimiento){
                    $tsu = tsutipospromocionessucursales::where('tprid', 1)
                                                        ->where('sucid', $suc->sucid)
                                                        ->where('fecid', $rbb->fecid)
                                                        ->first();

                    if($tsu){
                        $rbsrebate = ($tsu->tsuvalorizadoreal*$rbb->rbbporcentaje)/100;
                        
                        if($rbb->fecid == 3){
                            $logs['oct'][] = "Entra en el rango la sucursal: ".$suc->sucid." con un porcentaje de :".$rbscumplimiento." y un rebate de: ".$rbsrebate;
                        }else{
                            $logs['novydic'][] = "Entra en el rango la sucursal: ".$suc->sucid." con un porcentaje de :".$rbscumplimiento." y un rebate de: ".$rbsrebate;
                        }
                        
                    }
                    
                }

                $rbsn = new rbsrebatesbonussucursales;
                $rbsn->fecid           = $rbb->fecid;
                $rbsn->rbbid           = $rbb->rbbid;
                $rbsn->sucid           = $suc->sucid;
                $rbsn->rbsobjetivo     = $rbsobjetivo;
                $rbsn->rbsreal         = $rbsreal;
                $rbsn->rbscumplimiento = $rbscumplimiento;
                $rbsn->rbsrebate       = $rbsrebate;
                $rbsn->save();

            }

            
        }

        dd($logs);
    }
}