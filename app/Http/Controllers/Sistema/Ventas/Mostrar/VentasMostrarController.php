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
use App\rbbrebatesbonus;
use App\rbsrebatesbonussucursales;
use App\tritrimestres;
use App\sucsucursales;

class VentasMostrarController extends Controller
{
    /**
     * [{"tipopromocion": "sell in", "obj": x, "real": y, "categorias":[{catnombre: "infant", "real":x, "togo":x}]}]
     */
    public function mostrarVentas(Request $request)
    {
        $usutoken   = $request['usutoken'];
        $sucid      = $request['sucid'];
        // $dia        = $request['dia'];
        $dia        = "01";
        $mes        = $request['mes'];
        $ano        = $request['ano'];

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;

        $rebatesBonus = array(
            "categorias"   => [],
            "objetivo"     => "",
            "real"         => "",
            "cumplimiento" => "",
            "rebate"       => "",
            "descripcion"  => ""
        );

        try{

            // OBTENER EL REBATE BONUS
            $rbbs = rbbrebatesbonus::join('fecfechas as fec', 'rbbrebatesbonus.fecid', 'fec.fecid')
                                    ->where('fec.fecano', $ano)
                                    ->where('fec.fecmes', $mes)
                                    ->where('fec.fecdia', $dia)
                                    ->get();

            if(sizeof($rbbs) > 0){
                foreach($rbbs as $rbb){
                    $rbs = rbsrebatesbonussucursales::where('rbbid', $rbb->rbbid)
                                                    ->where('sucid', $sucid)
                                                    ->first();

                    if($rbs){
                        $rebatesBonus['objetivo']     = $rbs->rbsobjetivo;
                        $rebatesBonus['real']         = $rbs->rbsreal;
                        $rebatesBonus['cumplimiento'] = $rbs->rbscumplimiento;
                        $rebatesBonus['rebate']       = $rbs->rbsrebate;
                        $rebatesBonus['descripcion']  = $rbb->rbbdescripcion;
                    }

                    $cats = catcategorias::where('catid', '!=', 6)->get();
                    
                    foreach($cats as $posicionCat => $cat){
                        if($rbb->fecid == 3){
                            if($cat->catid == 4){
                                $cats[$posicionCat]['estado'] = 0;
                                $cats[$posicionCat]['caticono'] = "http://backend.leadsmartview.com/Sistema/categorias/img/iconos/iconosDesactivados/".$cat->catnombre.".png";
                            }else{
                                $cats[$posicionCat]['estado'] = 1;
                            }
                        }else{
                            if($cat->catid == 1){
                                $cats[$posicionCat]['estado'] = 1;
                            }else{
                                $cats[$posicionCat]['estado'] = 0;
                                $cats[$posicionCat]['caticono'] = "http://backend.leadsmartview.com/Sistema/categorias/img/iconos/iconosDesactivados/".$cat->catnombre.".png";
                            }
                        }
                    }

                    $rebatesBonus['categorias'] = $cats;
                }
            }

            // SABER SI ESTE MES TIENE REBATE TRIMESTRAL
            $tieneRebateTrimestral = false;
            $nombreTrimestre = "";

            $tri = tritrimestres::join('fecfechas as fec', 'tritrimestres.fecid', 'fec.fecid')
                                ->where('fec.fecano', $ano)
                                ->where('fec.fecmes', $mes)
                                ->where('fec.fecdia', $dia)
                                ->where('triestado', 1)
                                ->first();

            if($tri){
                $tieneRebateTrimestral = true;
                $nombreTrimestre = $tri->trinombre;
            }

            $tsutipospromocionessucursales = tsutipospromocionessucursales::join('fecfechas as fec', 'tsutipospromocionessucursales.fecid', 'fec.fecid')
                                                                        ->join('tprtipospromociones as tpr', 'tpr.tprid', 'tsutipospromocionessucursales.tprid')
                                                                        ->leftjoin('tretiposrebates as tre', 'tre.treid', 'tsutipospromocionessucursales.treid')
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
                                                                            'tsutipospromocionessucursales.tsuvalorizadorebate',

                                                                            'tsuobjetivotrimestral',
                                                                            'tsurealtrimestral',
                                                                            'tsufacturartrimestral',
                                                                            'tsucumplimientotrimestral',
                                                                            'tsurebatetrimestral'
                                                                        ]);
            if(sizeof($tsutipospromocionessucursales) > 0){

                foreach($tsutipospromocionessucursales as $posicion => $tsutipopromocionsucursal){

                    $tsutipospromocionessucursales[$posicion]["tieneRebateTrimestral"] = $tieneRebateTrimestral;
                    $tsutipospromocionessucursales[$posicion]["nombreTrimestre"] = $nombreTrimestre;

                    // $car = carcargasarchivos::where('tcaid', 2)
                    //                         ->OrderBy('carcargasarchivos.created_at', 'DESC')
                    //                         ->first([
                    //                             'carcargasarchivos.created_at'
                    //                         ]);
                    
                    $fechaActualizacion = '';
                    // if($car){
                    //     $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                    //     $diaActualizacion   = date("j", strtotime($car->created_at))." de ";
                    //     $mesActualizacion   = $meses[date('n', strtotime($car->created_at))-1]." del ";
                    //     $anioActualizacion  = date("Y", strtotime($car->created_at));
                    //     $fechaActualizacion = $diaActualizacion.$mesActualizacion.$anioActualizacion;
                    // }else{

                    // }
                    
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

                $categorias = catcategorias::where('catnombre', '!=', 'MultiCategoria')->where('catid', '<', 6)->get();
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
                    
                    
                    $dataVacia[$posicionTpr]["tieneRebateTrimestral"]     = $tieneRebateTrimestral;
                    $dataVacia[$posicionTpr]["tsuobjetivotrimestral"]     = 0;
                    $dataVacia[$posicionTpr]["tsurealtrimestral"]         = 0;
                    $dataVacia[$posicionTpr]["tsufacturartrimestral"]     = 0;
                    $dataVacia[$posicionTpr]["tsucumplimientotrimestral"] = 0;
                    $dataVacia[$posicionTpr]["tsurebatetrimestral"]       = 0;
                    $dataVacia[$posicionTpr]["nombreTrimestre"] = $nombreTrimestre;
                    
                    
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
            "rebatebonus"    => $rebatesBonus,
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
        $gsuid      = $request['gsuid'];
        $casid      = $request['casid'];
        // $dia        = $request['dia'];
        $dia        = "01";
        $mes        = $request['mes'];
        $ano        = $request['ano'];

        $respuesta      = true;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;

        $rebatesBonus = array(
            "categorias"   => [],
            "objetivo"     => "",
            "real"         => "",
            "cumplimiento" => "",
            "rebate"       => ""
        );

        // SABER SI ESTE MES TIENE REBATE TRIMESTRAL
        $tieneRebateTrimestral = false;
        $nombreTrimestre = "";

        $tri = tritrimestres::join('fecfechas as fec', 'tritrimestres.fecid', 'fec.fecid')
                            ->where('fec.fecano', $ano)
                            ->where('fec.fecmes', $mes)
                            ->where('fec.fecdia', $dia)
                            ->where('triestado', 1)
                            ->first();

        if($tri){
            $tieneRebateTrimestral = true;
            $nombreTrimestre = $tri->trinombre;
        }

        try{
            
            // OBTENER EL REBATE BONUS
            $rbbs = rbbrebatesbonus::join('fecfechas as fec', 'rbbrebatesbonus.fecid', 'fec.fecid')
                                    ->where('fec.fecano', $ano)
                                    ->where('fec.fecmes', $mes)
                                    ->where('fec.fecdia', $dia)
                                    ->get();

            if(sizeof($rbbs) > 0){

                $rbsObjetivo = 0;
                $rbsReal     = 0;
                $rbsRebate   = 0;

                $cats = catcategorias::where('catid', '!=', 6)->get();

                foreach($rbbs as $rbb){
                    $rbsSumaObjetivosActual = rbsrebatesbonussucursales::join('sucsucursales as suc', 'suc.sucid', 'rbsrebatesbonussucursales.sucid')
                                                    ->where('rbbid', $rbb->rbbid)
                                                    ->where(function ($query) use($zonid, $gsuid, $casid) {
                                                        if( $zonid != 0 ){
                                                            $query->where('suc.zonid', $zonid);
                                                        }
                    
                                                        if($gsuid != 0){
                                                            $query->where('suc.gsuid', $gsuid);
                                                        }
                                                        
                                                        if($casid != 0){
                                                            $query->where('suc.casid', $casid);
                                                        }
                                                    })
                                                    ->sum('rbsobjetivo');

                    $rbsSumaRealActual = rbsrebatesbonussucursales::join('sucsucursales as suc', 'suc.sucid', 'rbsrebatesbonussucursales.sucid')
                                                    ->where('rbbid', $rbb->rbbid)
                                                    ->where(function ($query) use($zonid, $gsuid, $casid) {
                                                        if( $zonid != 0 ){
                                                            $query->where('suc.zonid', $zonid);
                                                        }
                    
                                                        if($gsuid != 0){
                                                            $query->where('suc.gsuid', $gsuid);
                                                        }
                                                        
                                                        if($casid != 0){
                                                            $query->where('suc.casid', $casid);
                                                        }
                                                    })
                                                    ->sum('rbsreal');
                    
                    $rbsCumplimientoActual = ($rbsSumaRealActual * 100 ) / $rbsSumaObjetivosActual;

                    if($rbb->rbbcumplimiento <= $rbsCumplimientoActual){
                        $rbsRebateActual = ($rbsSumaObjetivosActual * $rbb->rbbporcentaje) / 100;
                    }else{
                        $rbsRebateActual = 0;
                    }



                    $rbsRebate   = $rbsRebate + $rbsRebateActual;
                    $rbsObjetivo = $rbsObjetivo + $rbsSumaObjetivosActual;
                    $rbsReal     = $rbsReal + $rbsSumaRealActual;

                    if($rbsObjetivo > 0){
                        $rbsCumplimiento = ($rbsReal * 100 ) / $rbsObjetivo;
                    }else{
                        $rbsCumplimiento = $rbsReal;
                    }

                    $rebatesBonus['objetivo']     = $rbsObjetivo;
                    $rebatesBonus['real']         = $rbsReal;
                    $rebatesBonus['cumplimiento'] = $rbsCumplimiento;
                    $rebatesBonus['rebate']       = $rbsRebate;
                    $rebatesBonus['descripcion']  = $rbb->rbbdescripcion;
                    
                    foreach($cats as $posicionCat => $cat){
                        if($rbb->fecid == 3){
                            if($cat->catid == 4){
                                $cats[$posicionCat]['estado'] = 0;
                                $cats[$posicionCat]['caticono'] = "http://backend.leadsmartview.com/Sistema/categorias/img/iconos/iconosDesactivados/".$cat->catnombre.".png";
                            }else{
                                $cats[$posicionCat]['estado'] = 1;
                            }
                        }else{
                            if($cat->catid == 1){
                                $cats[$posicionCat]['estado'] = 1;
                            }else{
                                $cats[$posicionCat]['estado'] = 0;
                                $cats[$posicionCat]['caticono'] = "http://backend.leadsmartview.com/Sistema/categorias/img/iconos/iconosDesactivados/".$cat->catnombre.".png";
                            }
                        }
                    }

                    $rebatesBonus['categorias'] = $cats;
                }
            }

            $plantillaTrrs = array(
                array(
                    "rtpid" => 0,
                    "rtpporcentajedesde" => "95",
                    "rtpporcentajehasta" => "99",
                    "rtpporcentajerebate" => "0",
                    "realTotal" => "0"
                ),
                array(
                    "rtpid" => 0,
                    "rtpporcentajedesde" => "100",
                    "rtpporcentajehasta" => "104",
                    "rtpporcentajerebate" => "0",
                    "realTotal" => "0"
                ),
                array(
                    "rtpid" => 0,
                    "rtpporcentajedesde" => "105",
                    "rtpporcentajehasta" => "10000",
                    "rtpporcentajerebate" => "0",
                    "realTotal" => "0"
                ),
            );

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
            
            $usus = sucsucursales::
                                where('sucestado', 1)
                                ->where(function ($query) use($zonid, $gsuid, $casid) {
                                // where(function ($query) use($zonid, $gsuid, $casid) {
                                    if( $zonid != 0 ){
                                        $query->where('zonid', $zonid);
                                    }

                                    if($gsuid != 0){
                                        $query->where('gsuid', $gsuid);
                                    }
                                    
                                    if($casid != 0){
                                        $query->where('casid', $casid);
                                    }
                                })
                                ->get([
                                    'sucid'
                                ]);

            // $usus = usuusuarios::join('ussusuariossucursales as uss', 'uss.usuid', 'usuusuarios.usuid')
            //             ->join('sucsucursales as suc', 'suc.sucid', 'uss.sucid')
            //             ->where('suc.sucestado', 1)
            //             ->where('usuusuarios.tpuid', 2) 
            //             ->where('usuusuarios.zonid', $zonid)
            //             ->where('usuusuarios.estid', 1)
            //             ->distinct('uss.sucid')
            //             ->get(['usuusuarios.usuid', 'uss.ussid', 'uss.sucid']);
            
            if(sizeof($usus) > 0){
                
                $tprs = tprtipospromociones::get(['tprid', 'tprnombre', 'tpricono', 'tprcolorbarra', 'tprcolortooltip']);
                
                // SABER SI ESTE MES TIENE REBATE TRIMESTRAL
                $tieneRebateTrimestral = false;
                $nombreTrimestre = "";
                $tri = tritrimestres::join('fecfechas as fec', 'tritrimestres.fecid', 'fec.fecid')
                                    ->where('fec.fecano', $ano)
                                    ->where('fec.fecmes', $mes)
                                    ->where('fec.fecdia', $dia)
                                    ->where('triestado', 1)
                                    ->first();

                if($tri){
                    $tieneRebateTrimestral = true;
                    $nombreTrimestre = $tri->trinombre;
                }

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

                    $dataarray[$posicionTpr]["tieneRebateTrimestral"]     = $tieneRebateTrimestral;
                    $dataarray[$posicionTpr]["tsuobjetivotrimestral"]     = 0;
                    $dataarray[$posicionTpr]["tsurealtrimestral"]         = 0;
                    $dataarray[$posicionTpr]["tsufacturartrimestral"]     = 0;
                    $dataarray[$posicionTpr]["tsucumplimientotrimestral"] = 0;
                    $dataarray[$posicionTpr]["tsurebatetrimestral"]       = 0;
                    $dataVacia[$posicionTpr]["nombreTrimestre"] = $nombreTrimestre;

                    $dataarray[$posicionTpr]['categorias'] = array(array());

                    $dataarray[$posicionTpr]['fecid'] = "";
                    $dataarray[$posicionTpr]['treid'] = "";
                    $dataarray[$posicionTpr]['trenombre'] = "";

                    foreach($usus as $usu){
                        $tsu = tsutipospromocionessucursales::join('fecfechas as fec', 'tsutipospromocionessucursales.fecid', 'fec.fecid')
                                                            ->join('tretiposrebates as tre', 'tre.treid', 'tsutipospromocionessucursales.treid')
                                                            ->join('sucsucursales as suc', 'suc.sucid', 'tsutipospromocionessucursales.sucid')
                                                            ->where('tsutipospromocionessucursales.sucid', $usu->sucid)
                                                            ->where('suc.sucestado', 1)
                                                            ->where('tsutipospromocionessucursales.tprid', $tpr->tprid)
                                                            ->where('fec.fecano', $ano)
                                                            ->where('fec.fecmes', $mes)
                                                            ->where('fec.fecdia', $dia)
                                                            ->first([
                                                                'fec.fecid',
                                                                'tre.treid',
                                                                'tre.trenombre',
                                                                'tsutipospromocionessucursales.tprid',
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
                            
                            $categorias = catcategorias::where('catnombre', '!=', 'MultiCategoria')
                                                        ->where('catid', '<', 6)
                                                        ->orderBy('catid')->get(['catid', 'catnombre', 'catimagenfondo', 'catimagenfondoopaco', 'caticono']);

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
                                                                    ->get([
                                                                        'catid',
                                                                        'scavalorizadoobjetivo', 
                                                                        'scavalorizadoreal', 
                                                                        'scavalorizadotogo', 
                                                                        'scaiconocategoria'
                                                                    ]);

                                    if(sizeof($scas) > 0){

                                        foreach($scas as $sca){
                                            $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadoobjetivo'] = $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadoobjetivo'] + $sca->scavalorizadoobjetivo;
                                            $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadoreal']     = $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadoreal']     + $sca->scavalorizadoreal;
                                            $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadotogo']     = $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadotogo']     + $sca->scavalorizadotogo;
                                            $dataarray[$posicionTpr]['categorias'][$posicionCat]['scaiconocategoria']     = $sca->scaiconocategoria;

                                            foreach($plantillaTrrs as $posPlantillaTrr => $plantillaTrr){
                                                $trrEsp = trrtiposrebatesrebates::join('rtprebatetipospromociones as rtp', 'rtp.rtpid', 'trrtiposrebatesrebates.rtpid')
                                                                                ->where('treid', $tsu->treid)
                                                                                ->where('rtp.tprid', $tsu->tprid)
                                                                                ->where('rtp.fecid', $tsu->fecid)
                                                                                ->where('trrtiposrebatesrebates.catid', $sca->catid)
                                                                                ->where('rtp.rtpporcentajedesde', $plantillaTrr['rtpporcentajedesde'])
                                                                                ->where('rtp.rtpporcentajehasta', $plantillaTrr['rtpporcentajehasta'])
                                                                                // ->distinct('rtpid')
                                                                                ->first([
                                                                                    'rtp.rtpid',
                                                                                    'rtpporcentajedesde',
                                                                                    'rtpporcentajehasta',
                                                                                    'rtpporcentajerebate'
                                                                                ]);

                                                if($trrEsp){
                                                    

                                                    if($trrEsp->rtpporcentajehasta > 300){
                                                        // $realRebate = (() / 100) ($sca->scavalorizadoobjetivo * $trrEsp->rtpporcentajerebate)/100;
                                                        $realRebate = ( ( ( $sca->scavalorizadoobjetivo * $plantillaTrr['rtpporcentajedesde'] ) / 100 ) * $trrEsp->rtpporcentajerebate ) / 100;
                                                    }else{
                                                        $realRebate = ( ( ( $sca->scavalorizadoobjetivo * $plantillaTrr['rtpporcentajehasta'] ) / 100 ) * $trrEsp->rtpporcentajerebate ) / 100;
                                                        // $realRebate = ($sca->scavalorizadoobjetivo * $trrEsp->rtpporcentajerebate)/100;
                                                    }

                                                    $plantillaTrrs[$posPlantillaTrr]['reales'][]  = $tsu->trenombre." ".$realRebate;
                                                    $plantillaTrrs[$posPlantillaTrr]['realTotal'] = $plantillaTrrs[$posPlantillaTrr]['realTotal'] + $realRebate;
                                                }
                                            }


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

                            $categorias = catcategorias::where('catnombre', '!=', 'MultiCategoria')
                                                        ->where('catid', '<', 6)
                                                        ->orderBy('catid')->get(['catid', 'catnombre', 'catimagenfondo', 'catimagenfondoopaco', 'caticono']);

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

                    $dataarray[$posicionTpr]['trrs'] = $plantillaTrrs;
                }

                $datos = $dataarray;
            }else{
                
                $dataVacia = array(array());

                $categorias = catcategorias::where('catnombre', '!=', 'MultiCategoria')
                                            ->where('catid', '<', 6)->get();
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
                    
                    
                    $dataVacia[$posicionTpr]["tieneRebateTrimestral"]     = $tieneRebateTrimestral;
                    $dataVacia[$posicionTpr]["tsuobjetivotrimestral"]     = 0;
                    $dataVacia[$posicionTpr]["tsurealtrimestral"]         = 0;
                    $dataVacia[$posicionTpr]["tsufacturartrimestral"]     = 0;
                    $dataVacia[$posicionTpr]["tsucumplimientotrimestral"] = 0;
                    $dataVacia[$posicionTpr]["tsurebatetrimestral"]       = 0;
                    $dataVacia[$posicionTpr]["nombreTrimestre"] = $nombreTrimestre;
                    
                    
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
                $mensaje        = 'Lo sentimos no encontramos tipos de promociones registradas a este filtro.';
                $mensajeDetalle = sizeof($usus).' registros encontrados.';
                $respuesta      = true;
            }


        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
            $respuesta  = false;
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
            "rebatebonus"    => $rebatesBonus,
            "linea"          => $linea,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev
        ]);
        
        return $requestsalida;

    }

    public function MostrarVentasXGrupo(Request $request)
    {
        $usutoken   = $request['usutoken'];
        $zonid      = $request['zonid'];
        // $dia        = $request['dia'];
        $dia        = "01";
        $mes        = $request['mes'];
        $ano        = $request['ano'];

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;

        $rebatesBonus = array(
            "categorias"   => [],
            "objetivo"     => "",
            "real"         => "",
            "cumplimiento" => "",
            "rebate"       => ""
        );

        try{
            
            $plantillaTrrs = array(
                array(
                    "rtpid" => 0,
                    "rtpporcentajedesde" => "95",
                    "rtpporcentajehasta" => "99",
                    "rtpporcentajerebate" => "0",
                    "realTotal" => "0"
                ),
                array(
                    "rtpid" => 0,
                    "rtpporcentajedesde" => "100",
                    "rtpporcentajehasta" => "104",
                    "rtpporcentajerebate" => "0",
                    "realTotal" => "0"
                ),
                array(
                    "rtpid" => 0,
                    "rtpporcentajedesde" => "105",
                    "rtpporcentajehasta" => "10000",
                    "rtpporcentajerebate" => "0",
                    "realTotal" => "0"
                ),
            );

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
            
            $usus = sucsucursales::where('sucestado', 1)
                                ->where('zonid', $zonid)
                                ->get([
                                    'sucid'
                                ]);

            // $usus = usuusuarios::join('ussusuariossucursales as uss', 'uss.usuid', 'usuusuarios.usuid')
            //             ->join('sucsucursales as suc', 'suc.sucid', 'uss.sucid')
            //             ->where('suc.sucestado', 1)
            //             ->where('usuusuarios.tpuid', 2) 
            //             ->where('usuusuarios.zonid', $zonid)
            //             ->where('usuusuarios.estid', 1)
            //             ->distinct('uss.sucid')
            //             ->get(['usuusuarios.usuid', 'uss.ussid', 'uss.sucid']);
            
            if(sizeof($usus) > 0){
                
                $tprs = tprtipospromociones::get(['tprid', 'tprnombre', 'tpricono', 'tprcolorbarra', 'tprcolortooltip']);
                
                // SABER SI ESTE MES TIENE REBATE TRIMESTRAL
                $tieneRebateTrimestral = false;
                $nombreTrimestre = "";
                $tri = tritrimestres::join('fecfechas as fec', 'tritrimestres.fecid', 'fec.fecid')
                                    ->where('fec.fecano', $ano)
                                    ->where('fec.fecmes', $mes)
                                    ->where('fec.fecdia', $dia)
                                    ->where('triestado', 1)
                                    ->first();

                if($tri){
                    $tieneRebateTrimestral = true;
                    $nombreTrimestre = $tri->trinombre;
                }

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

                    $dataarray[$posicionTpr]["tieneRebateTrimestral"]     = $tieneRebateTrimestral;
                    $dataarray[$posicionTpr]["tsuobjetivotrimestral"]     = 0;
                    $dataarray[$posicionTpr]["tsurealtrimestral"]         = 0;
                    $dataarray[$posicionTpr]["tsufacturartrimestral"]     = 0;
                    $dataarray[$posicionTpr]["tsucumplimientotrimestral"] = 0;
                    $dataarray[$posicionTpr]["tsurebatetrimestral"]       = 0;
                    $dataVacia[$posicionTpr]["nombreTrimestre"] = $nombreTrimestre;

                    $dataarray[$posicionTpr]['categorias'] = array(array());

                    $dataarray[$posicionTpr]['fecid'] = "";
                    $dataarray[$posicionTpr]['treid'] = "";
                    $dataarray[$posicionTpr]['trenombre'] = "";

                    foreach($usus as $usu){
                        $tsu = tsutipospromocionessucursales::join('fecfechas as fec', 'tsutipospromocionessucursales.fecid', 'fec.fecid')
                                                            ->join('tretiposrebates as tre', 'tre.treid', 'tsutipospromocionessucursales.treid')
                                                            ->join('sucsucursales as suc', 'suc.sucid', 'tsutipospromocionessucursales.sucid')
                                                            ->where('tsutipospromocionessucursales.sucid', $usu->sucid)
                                                            ->where('suc.sucestado', 1)
                                                            ->where('tsutipospromocionessucursales.tprid', $tpr->tprid)
                                                            ->where('fec.fecano', $ano)
                                                            ->where('fec.fecmes', $mes)
                                                            ->where('fec.fecdia', $dia)
                                                            ->first([
                                                                'fec.fecid',
                                                                'tre.treid',
                                                                'tre.trenombre',
                                                                'tsutipospromocionessucursales.tprid',
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
                            
                            $categorias = catcategorias::where('catnombre', '!=', 'MultiCategoria')
                                                        ->where('catid', '<', 6)
                                                        ->orderBy('catid')->get(['catid', 'catnombre', 'catimagenfondo', 'catimagenfondoopaco', 'caticono']);

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
                                                                    ->get([
                                                                        'catid',
                                                                        'scavalorizadoobjetivo', 
                                                                        'scavalorizadoreal', 
                                                                        'scavalorizadotogo', 
                                                                        'scaiconocategoria'
                                                                    ]);

                                    if(sizeof($scas) > 0){

                                        foreach($scas as $sca){
                                            $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadoobjetivo'] = $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadoobjetivo'] + $sca->scavalorizadoobjetivo;
                                            $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadoreal']     = $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadoreal']     + $sca->scavalorizadoreal;
                                            $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadotogo']     = $dataarray[$posicionTpr]['categorias'][$posicionCat]['scavalorizadotogo']     + $sca->scavalorizadotogo;
                                            $dataarray[$posicionTpr]['categorias'][$posicionCat]['scaiconocategoria']     = $sca->scaiconocategoria;

                                            foreach($plantillaTrrs as $posPlantillaTrr => $plantillaTrr){
                                                $trrEsp = trrtiposrebatesrebates::join('rtprebatetipospromociones as rtp', 'rtp.rtpid', 'trrtiposrebatesrebates.rtpid')
                                                                                ->where('treid', $tsu->treid)
                                                                                ->where('rtp.tprid', $tsu->tprid)
                                                                                ->where('rtp.fecid', $tsu->fecid)
                                                                                ->where('trrtiposrebatesrebates.catid', $sca->catid)
                                                                                ->where('rtp.rtpporcentajedesde', $plantillaTrr['rtpporcentajedesde'])
                                                                                ->where('rtp.rtpporcentajehasta', $plantillaTrr['rtpporcentajehasta'])
                                                                                // ->distinct('rtpid')
                                                                                ->first([
                                                                                    'rtp.rtpid',
                                                                                    'rtpporcentajedesde',
                                                                                    'rtpporcentajehasta',
                                                                                    'rtpporcentajerebate'
                                                                                ]);

                                                if($trrEsp){
                                                    

                                                    if($trrEsp->rtpporcentajehasta > 300){
                                                        // $realRebate = (() / 100) ($sca->scavalorizadoobjetivo * $trrEsp->rtpporcentajerebate)/100;
                                                        $realRebate = ( ( ( $sca->scavalorizadoobjetivo * $plantillaTrr['rtpporcentajedesde'] ) / 100 ) * $trrEsp->rtpporcentajerebate ) / 100;
                                                    }else{
                                                        $realRebate = ( ( ( $sca->scavalorizadoobjetivo * $plantillaTrr['rtpporcentajehasta'] ) / 100 ) * $trrEsp->rtpporcentajerebate ) / 100;
                                                        // $realRebate = ($sca->scavalorizadoobjetivo * $trrEsp->rtpporcentajerebate)/100;
                                                    }

                                                    $plantillaTrrs[$posPlantillaTrr]['reales'][]  = $tsu->trenombre." ".$realRebate;
                                                    $plantillaTrrs[$posPlantillaTrr]['realTotal'] = $plantillaTrrs[$posPlantillaTrr]['realTotal'] + $realRebate;
                                                }
                                            }


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

                            $categorias = catcategorias::where('catnombre', '!=', 'MultiCategoria')
                                                        ->where('catid', '<', 6)
                                                        ->orderBy('catid')->get(['catid', 'catnombre', 'catimagenfondo', 'catimagenfondoopaco', 'caticono']);

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

                    $dataarray[$posicionTpr]['trrs'] = $plantillaTrrs;
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
            "rebatebonus"    => $rebatesBonus,
            "linea"          => $linea,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev
        ]);
        
        return $requestsalida;

    }

}
