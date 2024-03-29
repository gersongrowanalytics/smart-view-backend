<?php

namespace App\Http\Controllers\Sistema\Modulos\Control\ControlPromociones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\fecfechas;
use App\cspcanalessucursalespromociones;
use App\prbpromocionesbonificaciones;
use App\prppromocionesproductos;

class TablaPromocionesController extends Controller
{
    public function MostrarPromociones(Request $request)
    {

        // REQUESTS

        $fecha           = $request['fecha'];
        $codigoPromocion = $request['codigoPromocion'];
        $sucnombre       = $request['sucnombre'];
        $catsid          = $request['catsid'];
        $canid           = $request['canid'];

        // DATA
        $log = array(
            "fecha" => []
        );

        $pkid = [];

        $respuesta = true;
        $mensaje   = "Promociones cargadas satisfactoriamente";
        $datos     = [];


        $fecha = new \DateTime(date("Y-m-d", strtotime($fecha)));
        $fecfecha = fecfechas::where('fecfecha', $fecha)->first(['fecid']);
        $fecid = 0;
        if($fecfecha){
            $fecid = $fecfecha->fecid;
            $log['fecha'][] = "Existe la fecha";
        }else{
            $log['fecha'][] = "No existe la fecha";
            $nuevafecha = new fecfechas;
            $nuevafecha->fecfecha = $fecha;
            $nuevafecha->fecdia   = '';
            $nuevafecha->fecmes   = '';
            $nuevafecha->fecano   = '';
            if($nuevafecha->save()){
                $fecid = $nuevafecha->fecid;

                $pkid[] = "FEC-".$fecid." ";
                $log['fecha'][] = "Se agrego la fecha";
            }else{
                $pkid[] = "FEC-0 ";
                $log['fecha'][] = "No se pudo agregar la fecha";
            }
        }

        $csps = cspcanalessucursalespromociones::join('csccanalessucursalescategorias as csc', 'csc.cscid', 'cspcanalessucursalespromociones.cscid')
                                                ->join('cancanales as can', 'csc.canid', 'can.canid')
                                                ->join('scasucursalescategorias as sca', 'sca.scaid', 'csc.scaid')
                                                ->join('catcategorias as cat', 'cat.catid', 'sca.catid')
                                                ->join('sucsucursales as suc', 'suc.sucid', 'sca.sucid')
                                                ->join('prmpromociones as prm', 'prm.prmid', 'cspcanalessucursalespromociones.prmid')
                                                ->where('cspcantidadplancha', '!=', "0")
                                                ->where('sca.tsuid', null)
                                                ->where('sca.fecid', $fecid) //TODOS LOS JOINS COMPARTEN LA MISMA FECHA (FK)
                                                ->orderBy('prm.prmid', 'DESC')
                                                ->where(function ($query) use($request) {

                                                    if($request['codigoPromocion'] != '' && $request['codigoPromocion'] != null) {
                
                                                        $query->where('prmcodigo', 'LIKE', "%".$request['codigoPromocion']."%");
                                                        
                                                    }
                
                                                    if($request['sucnombre'] != '' && $request['sucnombre'] != null) {
                
                                                        $query->where('suc.sucnombre', 'LIKE', "%".$request['sucnombre']."%");
                                                        
                                                    }

                                                    if($request['catsid'] != '' && $request['catsid'] != null && sizeof($request['catsid']) > 0) {
                                                        
                                                        for($i = 0; $i < sizeof($request['catsid']); $i++){
                                                            $query->orwhere('sca.catid', $request['catsid'][$i]);
                                                        }
                                                    }

                                                    if($request['canid'] != '' && $request['canid'] != null) {
                
                                                        $query->where('csc.canid', $request['canid']);
                                                        
                                                    }
                
                                                })
                                                ->get([
                                                    'prm.prmid',
                                                    'prm.prmcodigo',
                                                    'can.cannombre',
                                                    'cat.catnombre',
                                                    'suc.sucnombre'
                                                ]); 

        foreach($csps as $posicionCsp => $csp){
            $prb = prbpromocionesbonificaciones::where('prmid', $csp->prmid)
                                             ->first([
                                                 'prbid',
                                                 'prbimagen'
                                             ]);
            if($prb){
                $csps[$posicionCsp]['prbimagen'] = $prb->prbimagen;
                $csps[$posicionCsp]['prbidex']   = $prb->prbid;
            }else{
                $csps[$posicionCsp]['prbimagen'] = "";
                $csps[$posicionCsp]['prbidex']     = 0;
            }

            $csps[$posicionCsp]['prbid']     = 0;

            $prp = prppromocionesproductos::where('prmid', $csp->prmid)
                                            ->first([
                                                'prpid',
                                                'prpimagen'
                                            ]);

            if($prp){
                $csps[$posicionCsp]['prpimagen'] = $prp->prpimagen;
                $csps[$posicionCsp]['prpidex']     = $prp->prpid;
            }else{
                $csps[$posicionCsp]['prpimagen'] = "";
                $csps[$posicionCsp]['prpidex']     = 0;
            }

            $csps[$posicionCsp]['prpid'] = 0;

            $csps[$posicionCsp]['prpimageneditar']     = 0;
            $csps[$posicionCsp]['prbimageneditar']     = 0;
        }

        $datos = $csps;

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            "datos"     => $datos,
        ]);

        return $requestsalida;



    }
}
