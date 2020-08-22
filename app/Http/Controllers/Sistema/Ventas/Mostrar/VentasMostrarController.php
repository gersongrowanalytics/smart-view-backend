<?php

namespace App\Http\Controllers\Sistema\Ventas\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use App\tsutipospromocionessucursales;
use App\scasucursalescategorias;

class VentasMostrarController extends Controller
{
    /**
     * [{"tipopromocion": "sell in", "obj": x, "real": y, "categorias":[{catnombre: "infant", "real":x, "togo":x}]}]
     */
    public function mostrarVentas(Request $request)
    {
        $usutoken   = $request['usutoken'];
        $sucid      = $request['sucid'];
        $dia        = $request['dia'];
        $mes        = $request['mes'];
        $ano        = $request['ano'];

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;

        try{
            $tsutipospromocionessucursales = tsutipospromocionessucursales::join('tprtipospromociones as tpr', 'tpr.tprid', 'tsutipospromocionessucursales.tprid')
                                                                        ->where('tsutipospromocionessucursales.sucid', $sucid)
                                                                        ->get([
                                                                            'tsutipospromocionessucursales.tsuid',
                                                                            'tpr.tprid',
                                                                            'tpr.tprnombre',
                                                                            'tpr.tpricono',
                                                                            'tsutipospromocionessucursales.tsuvalorizadoobjetivo',
                                                                            'tsutipospromocionessucursales.tsuvalorizadoreal',
                                                                            'tsutipospromocionessucursales.tsuvalorizadotogo',
                                                                            'tsutipospromocionessucursales.tsuporcentajecumplimiento',
                                                                            'tsutipospromocionessucursales.tsuvalorizadorebate'
                                                                        ]);
            if(sizeof($tsutipospromocionessucursales) > 0){

                foreach($tsutipospromocionessucursales as $posicion => $tsutipopromocionsucursal){

                    $scasucursalescategorias = scasucursalescategorias::join('catcategorias as cat', 'cat.catid', 'scasucursalescategorias.catid')
                                                                    ->where('scasucursalescategorias.tsuid', $tsutipopromocionsucursal->tsuid)
                                                                    ->get([
                                                                        'cat.catnombre',
                                                                        'cat.catimagenfondo',
                                                                        'cat.caticono',
                                                                        'scasucursalescategorias.scavalorizadoobjetivo',
                                                                        'scasucursalescategorias.scavalorizadoreal',
                                                                        'scasucursalescategorias.scavalorizadotogo'
                                                                    ]);
                    if(sizeof($scasucursalescategorias) > 0){
                        $tsutipospromocionessucursales[$posicion]['categorias'] = $scasucursalescategorias;
                    }else{
                        $tsutipospromocionessucursales[$posicion]['categorias'] = [];
                    }
                }


                $linea          = __LINE__;
                $datos          = $tsutipospromocionessucursales;
                $respuesta      = true;
                $mensaje        = 'Los tipos de promociones se cargaron satisfactoriamente.';
            }else{
                $respuesta      = false;
                $linea          = __LINE__;
                $mensaje        = 'Lo sentimos no encontramos tipos de promociones registradas a este filtro.';
                $mensajeDetalle = sizeof($tsutipospromocionessucursales).' registros encontrados.';
            }

        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
            "linea"          => $linea,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            null,
            $request['ip'],
            $request,
            $requestsalida,
            'Mostrar las ventas de un tipo de promocion con sus rebate y avances de venta correspondiente segun el filtro de sucursal, fecha (dia, mes, año)',
            'MOSTRAR',
            '', //ruta
            null
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
    }
}
