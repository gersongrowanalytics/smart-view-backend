<?php

namespace App\Http\Controllers\Sistema\Promociones\Editar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\cspcanalessucursalespromociones;

class PromocionEditarGratisController extends Controller
{
    public function QuitarGratisPromociones($fecid)
    {

        $logs = array();

        $csps = cspcanalessucursalespromociones::join('prmpromociones as prm', 'prm.prmid', 'cspcanalessucursalespromociones.prmid')
                                                ->join('prbpromocionesbonificaciones as prb', 'prb.prmid', 'prm.prmid')
                                                ->where('cspcanalessucursalespromociones.fecid', $fecid)
                                                ->where('prbproductoppt' , 'LIKE', '%Precio Promocional%')
                                                ->get([
                                                    'cspcanalessucursalespromociones.cspid',
                                                    'cspgratis'
                                                ]);

        foreach($csps as $csp){
            $cspe = cspcanalessucursalespromociones::find($csp->cspid);
            $cspe->cspgratis = 0;
            if($cspe->update()){
                $logs["CSP_EDITADO"][] = $cspe->cspid;
            }else{
                $logs["CSP_NO_EDITADO"][] = $cspe->cspid;
            }
        }

        $csps = cspcanalessucursalespromociones::join('prmpromociones as prm', 'prm.prmid', 'cspcanalessucursalespromociones.prmid')
                                                ->join('prbpromocionesbonificaciones as prb', 'prb.prmid', 'prm.prmid')
                                                ->where('cspcanalessucursalespromociones.fecid', $fecid)
                                                ->where('prbproductoppt' , 'LIKE', 'Reconocimiento')
                                                ->get([
                                                    'cspcanalessucursalespromociones.cspid',
                                                    'cspgratis'
                                                ]);

        foreach($csps as $csp){
            $cspe = cspcanalessucursalespromociones::find($csp->cspid);
            $cspe->cspgratis = 0;
            if($cspe->update()){
                $logs["CSP_EDITADO"][] = $cspe->cspid;
            }else{
                $logs["CSP_NO_EDITADO"][] = $cspe->cspid;
            }
        }

        $csps = cspcanalessucursalespromociones::join('prmpromociones as prm', 'prm.prmid', 'cspcanalessucursalespromociones.prmid')
                                                ->join('prbpromocionesbonificaciones as prb', 'prb.prmid', 'prm.prmid')
                                                ->where('cspcanalessucursalespromociones.fecid', $fecid)
                                                ->where('prbcomprappt' , 'LIKE', 'Dscto.')
                                                ->get([
                                                    'cspcanalessucursalespromociones.cspid',
                                                    'cspgratis'
                                                ]);

        foreach($csps as $csp){
            $cspe = cspcanalessucursalespromociones::find($csp->cspid);
            $cspe->cspgratis = 0;
            if($cspe->update()){
                $logs["CSP_EDITADO"][] = $cspe->cspid;
            }else{
                $logs["CSP_NO_EDITADO"][] = $cspe->cspid;
            }
        }

        dd($logs);


    }
}
