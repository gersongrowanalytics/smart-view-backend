<?php

namespace App\Http\Controllers\Sistema\Ventas\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use App\tsutipospromocionessucursales;
use App\scasucursalescategorias;
use App\tprtipospromociones;
use App\catcategorias;
use App\carcargasarchivos;

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
            $tsutipospromocionessucursales = tsutipospromocionessucursales::join('fecfechas as fec', 'tsutipospromocionessucursales.fecid', 'fec.fecid')
                                                                        ->join('tprtipospromociones as tpr', 'tpr.tprid', 'tsutipospromocionessucursales.tprid')
                                                                        ->where('tsutipospromocionessucursales.sucid', $sucid)
                                                                        ->where('fec.fecano', $ano)
                                                                        ->where('fec.fecmes', $mes)
                                                                        ->where('fec.fecdia', $dia)
                                                                        ->OrderBy('tpr.tprnombre', 'ASC')
                                                                        ->get([
                                                                            'tsutipospromocionessucursales.tsuid',
                                                                            'tpr.tprid',
                                                                            'tpr.tprnombre',
                                                                            'tpr.tpricono',
                                                                            'tpr.tprcolorbarra',
                                                                            'tpr.tprcolortooltip',
                                                                            'tsutipospromocionessucursales.tsuvalorizadoobjetivo',
                                                                            'tsutipospromocionessucursales.tsuvalorizadoreal',
                                                                            'tsutipospromocionessucursales.tsuvalorizadotogo',
                                                                            'tsutipospromocionessucursales.tsuporcentajecumplimiento',
                                                                            'tsutipospromocionessucursales.tsuvalorizadorebate'
                                                                        ]);
            if(sizeof($tsutipospromocionessucursales) > 0){

                foreach($tsutipospromocionessucursales as $posicion => $tsutipopromocionsucursal){

                    $car = carcargasarchivos::where('tcaid', 3)
                                            ->OrderBy('carcargasarchivos.created_at', 'DESC')
                                            ->first([
                                                'carcargasarchivos.created_at'
                                            ]);
                    
                    $fechaActualizacion = '';
                    if($car){
                        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                        $diaActualizacion   = date("j", strtotime($car->created_at))." de ";
                        $mesActualizacion   = $meses[date('n', strtotime($car->created_at))-1]." del ";
                        $anioActualizacion  = date("Y", strtotime($car->created_at));
                        $fechaActualizacion = $diaActualizacion.$mesActualizacion.$anioActualizacion;
                    }else{

                    }
                    
                    $tsutipospromocionessucursales[$posicion]['fechaActualizacion'] = $fechaActualizacion;

                    $scasucursalescategorias = scasucursalescategorias::join('catcategorias as cat', 'cat.catid', 'scasucursalescategorias.catid')
                                                                    ->where('scasucursalescategorias.tsuid', $tsutipopromocionsucursal->tsuid)
                                                                    ->orderBy('cat.catid')
                                                                    ->get([
                                                                        'cat.catnombre',
                                                                        'cat.catimagenfondo',
                                                                        'cat.catimagenfondoopaco',
                                                                        'cat.caticono',
                                                                        'scasucursalescategorias.scavalorizadoobjetivo',
                                                                        'scasucursalescategorias.scavalorizadoreal',
                                                                        'scasucursalescategorias.scavalorizadotogo',
                                                                        'scasucursalescategorias.scaiconocategoria'
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
                $mensajeDetalle = sizeof($tsutipospromocionessucursales).' registros encontrados.';
            }else{

                $dataVacia = array(array());

                $categorias = catcategorias::where('catnombre', '!=', 'MultiCategoria')->get();
                $tprtipospromociones = tprtipospromociones::all();
                foreach($tprtipospromociones as $posicionTpr => $tpr){
                    
                    $dataVacia[$posicionTpr]['tsuid']                     = 0;
                    $dataVacia[$posicionTpr]['tprid']                     = $tpr->tprid;
                    $dataVacia[$posicionTpr]['tprnombre']                 = $tpr->tprnombre;
                    $dataVacia[$posicionTpr]['tpricono']                  = $tpr->tpricono;
                    $dataVacia[$posicionTpr]['tprcolorbarra']             = $tpr->tprcolorbarra;
                    $dataVacia[$posicionTpr]['tprcolortooltip']           = $tpr->tprcolortooltip;
                    $dataVacia[$posicionTpr]['tsuvalorizadoobjetivo']     = 0;
                    $dataVacia[$posicionTpr]['tsuvalorizadoreal']         = 0;
                    $dataVacia[$posicionTpr]['tsuvalorizadotogo']         = 0;
                    $dataVacia[$posicionTpr]['tsuporcentajecumplimiento'] = 0;
                    $dataVacia[$posicionTpr]['tsuvalorizadorebate']       = 0;
                    $dataVacia[$posicionTpr]['categorias'] = array(array());
                    foreach($categorias as $posicion => $categoria){     
                        $dataVacia[$posicionTpr]['categorias'][$posicion]['catnombre']              = $categoria->catnombre;
                        $dataVacia[$posicionTpr]['categorias'][$posicion]['catimagenfondo']         = $categoria->catimagenfondo;
                        $dataVacia[$posicionTpr]['categorias'][$posicion]['catimagenfondoopaco']    = $categoria->catimagenfondoopaco;
                        $dataVacia[$posicionTpr]['categorias'][$posicion]['caticono']               = $categoria->caticono;
                        $dataVacia[$posicionTpr]['categorias'][$posicion]['scavalorizadoobjetivo']  = 0;
                        $dataVacia[$posicionTpr]['categorias'][$posicion]['scavalorizadoreal']      = 1;
                        $dataVacia[$posicionTpr]['categorias'][$posicion]['scavalorizadotogo']      = 100;
                        $dataVacia[$posicionTpr]['categorias'][$posicion]['scaiconocategoria']      = env('APP_URL').'/Sistema/categorias-tiposPromociones/img/iconos/'.$categoria->catnombre.'-'.$tpr->tprnombre.'.png';
                    }
                }

                $datos = $dataVacia;
                $respuesta      = true;
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
