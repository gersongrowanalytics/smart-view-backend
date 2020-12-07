<?php

namespace App\Http\Controllers\Sistema\Ventas\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\tsutipospromocionessucursales;
use App\scasucursalescategorias;
use App\tprtipospromociones;
use App\catcategorias;
use App\carcargasarchivos;
use App\usuusuarios;
use App\trrtiposrebatesrebates;

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
                                                                        ->join('tretiposrebates as tre', 'tre.treid', 'tsutipospromocionessucursales.treid')
                                                                        ->where('tsutipospromocionessucursales.sucid', $sucid)
                                                                        ->where('fec.fecano', $ano)
                                                                        ->where('fec.fecmes', $mes)
                                                                        ->where('fec.fecdia', $dia)
                                                                        ->OrderBy('tpr.tprnombre', 'ASC')
                                                                        ->get([
                                                                            'tsutipospromocionessucursales.tsuid',
                                                                            'tpr.tprid',
                                                                            'fec.fecid',
                                                                            'tre.treid',
                                                                            'tre.trenombre',
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

                    $car = carcargasarchivos::where('tcaid', 2)
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
                                                                    ->where('cat.catid', '<', 6)
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

                    $trrs = trrtiposrebatesrebates::join('rtprebatetipospromociones as rtp', 'rtp.rtpid', 'trrtiposrebatesrebates.rtpid')
                                                    ->where('treid', $tsutipopromocionsucursal->treid)
                                                    ->where('rtp.tprid', $tsutipopromocionsucursal->tprid)
                                                    ->where('rtp.fecid', $tsutipopromocionsucursal->fecid)
                                                    ->distinct('rtpid')
                                                    ->get([
                                                        'rtp.rtpid',
                                                        'rtpporcentajedesde',
                                                        'rtpporcentajehasta',
                                                        'rtpporcentajerebate'
                                                    ]);
                    if(sizeof($trrs) > 0){

                    }else{
                        $trrs = array(
                            array(
                                "rtpid" => 0,
                                "rtpporcentajedesde" => "95",
                                "rtpporcentajehasta" => "99",
                                "rtpporcentajerebate" => "0"
                            ),
                            array(
                                "rtpid" => 0,
                                "rtpporcentajedesde" => "100",
                                "rtpporcentajehasta" => "104",
                                "rtpporcentajerebate" => "0"
                            ),
                            array(
                                "rtpid" => 0,
                                "rtpporcentajedesde" => "105",
                                "rtpporcentajehasta" => "10000",
                                "rtpporcentajerebate" => "0"
                            ),
                        );


                    }
                    
                    $tsutipospromocionessucursales[$posicion]["trrs"] = $trrs;


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

                $trrs = array(
                    array(
                        "rtpid" => 0,
                        "rtpporcentajedesde" => "95",
                        "rtpporcentajehasta" => "99",
                        "rtpporcentajerebate" => "0"
                    ),
                    array(
                        "rtpid" => 0,
                        "rtpporcentajedesde" => "100",
                        "rtpporcentajehasta" => "104",
                        "rtpporcentajerebate" => "0"
                    ),
                    array(
                        "rtpid" => 0,
                        "rtpporcentajedesde" => "105",
                        "rtpporcentajehasta" => "10000",
                        "rtpporcentajerebate" => "0"
                    ),
                );

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
                    $dataVacia[$posicionTpr]["trrs"] = $trrs;
                    $dataVacia[$posicionTpr]['categorias'] = array(array());
                    foreach($categorias as $posicion => $categoria){     
                        $dataVacia[$posicionTpr]['categorias'][$posicion]['catnombre']              = $categoria->catnombre;
                        $dataVacia[$posicionTpr]['categorias'][$posicion]['catimagenfondo']         = $categoria->catimagenfondo;
                        $dataVacia[$posicionTpr]['categorias'][$posicion]['catimagenfondoopaco']    = $categoria->catimagenfondoopaco;
                        $dataVacia[$posicionTpr]['categorias'][$posicion]['caticono']               = $categoria->caticono;
                        $dataVacia[$posicionTpr]['categorias'][$posicion]['scavalorizadoobjetivo']  = 0;
                        $dataVacia[$posicionTpr]['categorias'][$posicion]['scavalorizadoreal']      = 0;
                        $dataVacia[$posicionTpr]['categorias'][$posicion]['scavalorizadotogo']      = 0;
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
        
        return $requestsalida;
    }

    public function mostrarVentasXZona(Request $request)
    {
        $usutoken   = $request['usutoken'];
        $zonid      = $request['zonid'];
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
            
            $dataarray = array(
                array(

                    'fecid' => "",
                    'treid' => "",
                    'trenombre' => "",
                    "tsuid"                     => "",
                    "tprid"                     => "",
                    "tprnombre"                 => "",
                    "tpricono"                  => "",
                    "tprcolorbarra"             => "",
                    "tprcolortooltip"           => "",

                    "tsuvalorizadoobjetivo"     => "",
                    "tsuvalorizadoreal"         => "",
                    "tsuvalorizadotogo"         => "",
                    "tsuporcentajecumplimiento" => "",
                    "tsuvalorizadorebate"       => "",

                    "fechaActualizacion"        => "",

                    "categorias"                => array(
                        array(
                            "catnombre"             => "",
                            "catimagenfondo"        => "",
                            "catimagenfondoopaco"   => "",
                            "caticono"              => "",
                            "scavalorizadoobjetivo" => "",
                            "scavalorizadoreal"     => "",
                            "scavalorizadotogo"     => "",
                            "scaiconocategoria"     => ""
                        )
                    )
                )
            );
            
            $usus = usuusuarios::join('ussusuariossucursales as uss', 'uss.usuid', 'usuusuarios.usuid')
                        ->where('usuusuarios.tpuid', 2) 
                        ->where('usuusuarios.zonid', $zonid)
                        ->where('usuusuarios.estid', 1)
                        ->distinct('uss.sucid')
                        ->get(['usuusuarios.usuid', 'uss.ussid', 'uss.sucid']);
                        
            if(sizeof($usus) > 0){
                
                $tprs = tprtipospromociones::get(['tprid', 'tprnombre', 'tpricono', 'tprcolorbarra', 'tprcolortooltip']);
                

                foreach($tprs as $posicionTpr => $tpr){
                    $dataarray[$posicionTpr]['tsuid']                     = 0;
                    $dataarray[$posicionTpr]['tprid']                     = $tpr->tprid;
                    $dataarray[$posicionTpr]['tprnombre']                 = $tpr->tprnombre;
                    $dataarray[$posicionTpr]['tpricono']                  = $tpr->tpricono;
                    $dataarray[$posicionTpr]['tprcolorbarra']             = $tpr->tprcolorbarra;
                    $dataarray[$posicionTpr]['tprcolortooltip']           = $tpr->tprcolortooltip;
                    $dataarray[$posicionTpr]['tsuvalorizadoobjetivo']     = 0;
                    $dataarray[$posicionTpr]['tsuvalorizadoreal']         = 0;
                    $dataarray[$posicionTpr]['tsuvalorizadotogo']         = 0;
                    $dataarray[$posicionTpr]['tsuporcentajecumplimiento'] = 0;
                    $dataarray[$posicionTpr]['tsuvalorizadorebate']       = 0;

                    $dataarray[$posicionTpr]['categorias'] = array(array());

                    $trrs = array(
                        array(
                            "rtpid" => 0,
                            "rtpporcentajedesde" => "95",
                            "rtpporcentajehasta" => "99",
                            "rtpporcentajerebate" => "0"
                        ),
                        array(
                            "rtpid" => 0,
                            "rtpporcentajedesde" => "100",
                            "rtpporcentajehasta" => "104",
                            "rtpporcentajerebate" => "0"
                        ),
                        array(
                            "rtpid" => 0,
                            "rtpporcentajedesde" => "105",
                            "rtpporcentajehasta" => "10000",
                            "rtpporcentajerebate" => "0"
                        ),
                    );

                    $dataarray[$posicionTpr]['trrs'] = $trrs;
                    $dataarray[$posicionTpr]['fecid'] = "";
                    $dataarray[$posicionTpr]['treid'] = "";
                    $dataarray[$posicionTpr]['trenombre'] = "";

                    foreach($usus as $usu){
                        $tsu = tsutipospromocionessucursales::join('fecfechas as fec', 'tsutipospromocionessucursales.fecid', 'fec.fecid')
                                                            ->join('tretiposrebates as tre', 'tre.treid', 'tsutipospromocionessucursales.treid')
                                                            ->where('tsutipospromocionessucursales.sucid', $usu->sucid)
                                                            ->where('tsutipospromocionessucursales.tprid', $tpr->tprid)
                                                            ->where('fec.fecano', $ano)
                                                            ->where('fec.fecmes', $mes)
                                                            ->where('fec.fecdia', $dia)
                                                            ->first([
                                                                'fec.fecid',
                                                                'tre.treid',
                                                                'tre.trenombre',
                                                                'tsutipospromocionessucursales.tsuid',
                                                                'tsutipospromocionessucursales.tsuvalorizadoobjetivo',
                                                                'tsutipospromocionessucursales.tsuvalorizadoreal',
                                                                'tsutipospromocionessucursales.tsuvalorizadotogo',
                                                                'tsutipospromocionessucursales.tsuporcentajecumplimiento',
                                                                'tsutipospromocionessucursales.tsuvalorizadorebate'
                                                            ]);
                        if($tsu){
                            
                            
                            $dataarray[$posicionTpr]['tsuvalorizadoobjetivo']     = $dataarray[$posicionTpr]['tsuvalorizadoobjetivo']     + $tsu->tsuvalorizadoobjetivo;
                            $dataarray[$posicionTpr]['tsuvalorizadoreal']         = $dataarray[$posicionTpr]['tsuvalorizadoreal']         + $tsu->tsuvalorizadoreal;
                            $dataarray[$posicionTpr]['tsuvalorizadotogo']         = $dataarray[$posicionTpr]['tsuvalorizadotogo']         + $tsu->tsuvalorizadotogo;
                            $dataarray[$posicionTpr]['tsuporcentajecumplimiento'] = $dataarray[$posicionTpr]['tsuporcentajecumplimiento'] + $tsu->tsuporcentajecumplimiento;
                            $dataarray[$posicionTpr]['tsuvalorizadorebate']       = $dataarray[$posicionTpr]['tsuvalorizadorebate']       + $tsu->tsuvalorizadorebate;
                            
                            $categorias = catcategorias::where('catnombre', '!=', 'MultiCategoria')->orderBy('catid')->get(['catid', 'catnombre', 'catimagenfondo', 'catimagenfondoopaco', 'caticono']);

                            if(sizeof($categorias) > 0){

                                foreach($categorias as $posicionCat => $categoria){

                                    $dataarray[$posicionTpr]['categorias'][$posicionCat]['catnombre']             = $categoria->catnombre;
                                    $dataarray[$posicionTpr]['categorias'][$posicionCat]['catimagenfondo']        = $categoria->catimagenfondo;
                                    $dataarray[$posicionTpr]['categorias'][$posicionCat]['catimagenfondoopaco']   = $categoria->catimagenfondoopaco;
                                    $dataarray[$posicionTpr]['categorias'][$posicionCat]['caticono']              = $categoria->caticono;

                                    if(!isset($dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadoobjetivo'])){
                                        $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadoobjetivo'] = 0;
                                        $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadoreal']     = 0;
                                        $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadotogo']     = 0;
                                        $dataarray[$posicionTpr]['categorias'][$posicionCat]['scaiconocategoria']     = env('APP_URL').'/Sistema/categorias-tiposPromociones/img/iconos/'.$categoria->catnombre.'-'.$tpr->tprnombre.'.png';
                                    }

                                    $scas = scasucursalescategorias::where('tsuid', $tsu->tsuid )
                                                                    ->where('catid', $categoria->catid)
                                                                    ->get(['scavalorizadoobjetivo', 'scavalorizadoreal', 'scavalorizadotogo', 'scaiconocategoria']);

                                    if(sizeof($scas) > 0){

                                        foreach($scas as $sca){
                                            $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadoobjetivo'] = $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadoobjetivo'] + $sca->scavalorizadoobjetivo;
                                            $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadoreal']     = $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadoreal']     + $sca->scavalorizadoreal;
                                            $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadotogo']     = $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadotogo']     + $sca->scavalorizadotogo;
                                            $dataarray[$posicionTpr]['categorias'][$posicionCat]['scaiconocategoria']     = $sca->scaiconocategoria;
                                        }

                                    }else{

                                    }
                                    
                                }
                            }else{

                            }
                        }else{
                            $dataarray[$posicionTpr]['tsuvalorizadoobjetivo']     = $dataarray[$posicionTpr]['tsuvalorizadoobjetivo']     + 0;
                            $dataarray[$posicionTpr]['tsuvalorizadoreal']         = $dataarray[$posicionTpr]['tsuvalorizadoreal']         + 0;
                            $dataarray[$posicionTpr]['tsuvalorizadotogo']         = $dataarray[$posicionTpr]['tsuvalorizadotogo']         + 0;
                            $dataarray[$posicionTpr]['tsuporcentajecumplimiento'] = $dataarray[$posicionTpr]['tsuporcentajecumplimiento'] + 0;
                            $dataarray[$posicionTpr]['tsuvalorizadorebate']       = $dataarray[$posicionTpr]['tsuvalorizadorebate']       + 0;

                            $categorias = catcategorias::where('catnombre', '!=', 'MultiCategoria')->orderBy('catid')->get(['catid', 'catnombre', 'catimagenfondo', 'catimagenfondoopaco', 'caticono']);

                            if(sizeof($categorias) > 0){

                                foreach($categorias as $posicionCat => $categoria){

                                    $dataarray[$posicionTpr]['categorias'][$posicionCat]['catnombre']             = $categoria->catnombre;
                                    $dataarray[$posicionTpr]['categorias'][$posicionCat]['catimagenfondo']        = $categoria->catimagenfondo;
                                    $dataarray[$posicionTpr]['categorias'][$posicionCat]['catimagenfondoopaco']   = $categoria->catimagenfondoopaco;
                                    $dataarray[$posicionTpr]['categorias'][$posicionCat]['caticono']              = $categoria->caticono;

                                    if(!isset($dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadoobjetivo'])){
                                        $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadoobjetivo'] = 0;
                                        $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadoreal']     = 0;
                                        $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadotogo']     = 0;
                                        $dataarray[$posicionTpr]['categorias'][$posicionCat]['scaiconocategoria']     = env('APP_URL').'/Sistema/categorias-tiposPromociones/img/iconos/'.$categoria->catnombre.'-'.$tpr->tprnombre.'.png';
                                    }
                                    
                                }
                            }else{

                            }
                        }
                    }
                }
            }else{

            }
            
            $datos = $dataarray;


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
        
        return $requestsalida;

    }
}
