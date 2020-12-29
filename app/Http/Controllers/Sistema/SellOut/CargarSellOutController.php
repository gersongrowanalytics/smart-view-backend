<?php

namespace App\Http\Controllers\Sistema\SellOut;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use App\fecfechas;
use App\ussusuariossucursales;
use App\tsutipospromocionessucursales;
use App\scasucursalescategorias;
use App\proproductos;
use App\vsoventassso;
use App\sucsucursales;

class CargarSellOutController extends Controller
{
    public function CargarSellOutTodo()
    {
        $tprid = 2;
        $tprnombre = "Sell Out";
        $respuesta = true;
        $mensaje = "Todo el sell Out se actualizo correctamente";

        $logs = array(
            "SKUS_FALTANTES" => [],
            "SUCS_FALTANTES" => [],
        );

        $pks = array(
            "PK_FECHAS" => array(
                "NUEVOS" => [],
            ),
            "PK_VENTAS_SSO" => array(
                "NUEVOS" => [],
                "EDITADOS"
            ),
            "PK_TSU" => array(
                "NUEVOS" => [],
                "EDITADOS"
            ),
            "PK_SCA" => array(
                "NUEVOS" => [],
                "EDITADOS"
            ),
        );

        // REINICAR DATA A 0
        scasucursalescategorias::join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                ->where('tsu.tprid', $tprid)
                                ->where('scavalorizadoreal', 0)
                                ->where('scavalorizadotogo', 0)
                                ->update([
                                    'scavalorizadoreal' => 0, 
                                    'scavalorizadotogo' => 0
                                ]);

        tsutipospromocionessucursales::where('tprid', $tprid)
                                    ->where('tsuvalorizadoreal', '!=', 0)
                                    ->where('tsuvalorizadotogo', '!=', 0)
                                    ->where('tsuporcentajecumplimiento', '!=', 0)
                                    ->where('tsuvalorizadorebate', '!=', 0)
                                    ->update([
                                        'tsuvalorizadoreal' => 0, 
                                        'tsuvalorizadotogo' => 0,
                                        'tsuporcentajecumplimiento' => 0,
                                        'tsuvalorizadorebate' => 0,
                                    ]);
                                    
        vsoventassso::where('vsoid', '!=', 0)->update(['vsovalorizado' => 0]);

        $arrayMeses = array(
            array(
                "mes" => "Jan",
                "numero" => "01",
                "espaniol" => "ENE"
            ),
            array(
                "mes" => "Feb",
                "numero" => "02",
                "espaniol" => "FEB"
            ),
            array(
                "mes" => "Mar",
                "numero" => "03",
                "espaniol" => "MAR"
            ),
            array(
                "mes" => "Apr",
                "numero" => "04",
                "espaniol" => "ABR"
            ),
            array(
                "mes" => "May",
                "numero" => "05",
                "espaniol" => "MAY"
            ),
            array(
                "mes" => "Jun",
                "numero" => "06",
                "espaniol" => "JUN"
            ),
            array(
                "mes" => "Jul",
                "numero" => "07",
                "espaniol" => "JUL"
            ),
            array(
                "mes" => "Aug",
                "numero" => "08",
                "espaniol" => "AGO"
            ),
            array(
                "mes" => "Sep",
                "numero" => "09",
                "espaniol" => "SET"
            ),
            array(
                "mes" => "Oct",
                "numero" => "10",
                "espaniol" => "OCT"
            ),
            array(
                "mes" => "Nov",
                "numero" => "11",
                "espaniol" => "NOV"
            ),
            array(
                "mes" => "Dec",
                "numero" => "12",
                "espaniol" => "DIC"
            )
        );

        $datos = json_decode( file_get_contents('http://backend-api.leadsmartview.com/ws/obtenerSellOutTodo'), true );
        foreach($datos as $posicion => $dato){
            $soldto    = $dato['COD_SOLD_TO'];
            $sku       = $dato['SKU'];
            $sell      = $dato['SELLS'];

            echo $soldto.'<br>';
            echo $sku.'<br>';
            echo $sell.'<br>';

        }
        
        /*
        foreach($datos as $posicion => $dato){

            $soldto    = $dato['COD_SOLD_TO'];
            $sku       = $dato['SKU'];
            $sell      = $dato['SELLS'];

            $dia       = $dato['DAY'];
            $mesNumero = "0";
            $mesTxt    = "";
            $anio      = $dato['YEAR'];

            $fecid = 0;

            foreach ($arrayMeses as $key => $value) {
                if($value['mes'] == $dato['MONTH']){
                    $mesNumero = $value['numero'];
                    $mesTxt    = $value['espaniol'];
                    break;
                }
            }

            $fec = fecfechas::where('fecdia', $dia)
                            ->where('fecmes', $mesTxt)
                            ->where('fecano', $anio)
                            ->first(['fecid']);

            if($fec){
                $fecid = $fec->fecid;

            }else{

                $fecn = new fecfechas;
                $fecn->fecfecha     = new \DateTime(date("Y-m-d", strtotime($anio.'-'.$mesNumero.'-'.$dia)));
                $fecn->fecdia       = $dia;
                $fecn->fecmes       = $mesTxt;
                $fecn->fecmesnumero = $mesNumero;
                $fecn->fecano       = $anio;
                if($fecn->save()){
                    $fecid = $fecn->fecid;
                    $pks["PK_FECHAS"]["NUEVOS"][] = "NUEVA FEC-".$fecid;
                }
            }

            $pro = proproductos::join('catcategorias as cat', 'cat.catid', 'proproductos.catid')
                                ->where('proproductos.prosku', 'LIKE', '%'.$sku)
                                ->first([
                                    'proproductos.proid',
                                    'proproductos.catid',
                                    'cat.catnombre'
                                ]);

            if($pro){

                $categoriaid     = $pro->catid;
                $categoriaNombre = $pro->catnombre;

                $usu = ussusuariossucursales::join('usuusuarios as usu', 'usu.usuid', 'ussusuariossucursales.usuid')
                                            ->where('ususoldto', 'LIKE', '%'.$soldto)
                                            ->first(['usuid', 'ussusuariossucursales.sucid']);

                $sucid = 0;

                if($usu){
                    
                    $sucid = $usu->sucid;

                    $vso = vsoventassso::where('fecid', $fecid)
                                        ->where('proid', $pro->proid)
                                        ->where('sucid', $sucid)
                                        ->where('tpmid', 1)
                                        ->first();

                    if($vso){

                        $vso->vsovalorizado = $real + $vso->vsovalorizado;
                        if($vso->update()){
                            $pks['PK_VENTAS_SSO']["EDITADOS"][] = "VSO-".$vso->vsoid;
                        }
                        
                    }else{
                        $vson = new vsoventassso;
                        $vson->fecid         = $fecid;
                        $vson->proid         = $pro->proid;
                        $vson->sucid         = $sucid;
                        $vson->tpmid         = 1;
                        $vson->vsocantidad   = 0;
                        $vson->vsovalorizado = $real;
                        if($vson->save()){
                            $pks['PK_VENTAS_SSO']["NUEVO"][] = "VSO-".$vson->vsoid;
                        }
                    }

                    $tsu = tsutipospromocionessucursales::where('fecid', $fecid)
                                                        ->where('sucid', $sucid)
                                                        ->where('tprid', $tprid)
                                                        ->first([
                                                            'tsuid', 
                                                            'tsuvalorizadoreal', 
                                                            'tsuvalorizadoobjetivo', 
                                                            'treid'
                                                        ]);
                    if($tsu){
                        $tsuid = $tsu->tsuid;
                        $nuevoReal = $tsu->tsuvalorizadoreal+$real;

                        if($tsu->tsuvalorizadoobjetivo == 0){
                            $porcentajeCumplimiento = $nuevoReal;
                        }else{
                            $porcentajeCumplimiento = (100*$nuevoReal)/$tsu->tsuvalorizadoobjetivo;
                        }

                        $totalRebate = 0;
                        
                        $tsu->tsuvalorizadoreal         = $nuevoReal;
                        $tsu->tsuvalorizadotogo         = $tsu->tsuvalorizadoobjetivo - $nuevoReal;
                        $tsu->tsuporcentajecumplimiento = $porcentajeCumplimiento;
                        $tsu->tsuvalorizadorebate       = $totalRebate;
                        if($tsu->update()){
                            $pks['PK_TSU']["EDITADOS"][] = "TSU-".$tsuid;
                        }

                    }else{
                        $nuevotsu = new tsutipospromocionessucursales;
                        $nuevotsu->fecid = $fecid;
                        $nuevotsu->sucid = $sucid;
                        $nuevotsu->tprid = $tprid;
                        $nuevotsu->tsuporcentajecumplimiento = 0;
                        $nuevotsu->tsuvalorizadoobjetivo  = 0;
                        $nuevotsu->tsuvalorizadoreal      = $real;
                        $nuevotsu->tsuvalorizadorebate    = 0;
                        $nuevotsu->tsuvalorizadotogo      = 0;
                        if($nuevotsu->save()){
                            $tsuid = $nuevotsu->tsuid;
                            $pks['PK_TSU']["NUEVOS"][] = "TSU-".$tsuid;
                        }
                    }

                    $sca = scasucursalescategorias::where('fecid', $fecid)
                                                ->where('sucid', $sucid)
                                                ->where('catid', $categoriaid)
                                                ->where('tsuid', $tsuid)
                                                ->first([
                                                    'scaid', 
                                                    'scavalorizadoreal', 
                                                    'scavalorizadoobjetivo'
                                                ]);

                    $scaid = 0;
                    if($sca){
                        $scaid = $sca->scaid;

                        $nuevoRealSca = $real + $sca->scavalorizadoreal;
                        $sca->scavalorizadoreal = $nuevoRealSca;
                        $sca->scavalorizadotogo = $sca->scavalorizadoobjetivo + $nuevoRealSca;
                        $sca->scaiconocategoria = env('APP_URL').'/Sistema/categorias-tiposPromociones/img/iconos/'.$categoriaNombre.'-'.$tprnombre.'.png';
                        if($sca->update()){
                            $pks['PK_SCA']["EDITADOS"][] = "SCA-".$scaid;
                        }

                    }else{

                        $nuevosca = new scasucursalescategorias;
                        $nuevosca->sucid                 = $sucid;
                        $nuevosca->catid                 = $categoriaid;
                        $nuevosca->fecid                 = $fecid;
                        $nuevosca->tsuid                 = $tsuid;
                        $nuevosca->scavalorizadoobjetivo = 0;
                        $nuevosca->scaiconocategoria     = env('APP_URL').'/Sistema/categorias-tiposPromociones/img/iconos/'.$categoriaNombre.'-'.$tprnombre.'.png';
                        $nuevosca->scavalorizadoreal     = $real;
                        $nuevosca->scavalorizadotogo     = 0;
                        if($nuevosca->save()){
                            $scaid = $nuevosca->scaid;
                            $pks['PK_SCA']["NUEVO"][] = "SCA-".$scaid;
                        }

                    }

                }else{
                    $logs['SUCS_FALTANTES'][] = $soldto;
                    $respuesta = false;
                    $mensaje = "Lo sentimos, hubieron algunas sucursales (soldto) que no se encontraron";
                }

            }else{
                $logs['SKUS_FALTANTES'][] = $sku;
                $respuesta = false;
                $mensaje = "Lo sentimos, hubieron algunos productos (skus) que no se encontraron";
            }
        }
        */

        // $requestsalida = response()->json([
        //     "respuesta" => $respuesta,
        //     "mensaje"   => $mensaje,
        //     "logs"      => $logs,
        // ]);


        // $AuditoriaController = new AuditoriaController;
        // $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
        //     "ADMIN",
        //     "1",
        //     null,
        //     $datos, //audjsonentrada
        //     $requestsalida, //audjsonsalida
        //     $mensaje, //auddescripcion
        //     "ACTUALIZACION", //audaccion,
        //     "/obtenerSellOutTodo", //audruta,
        //     $pks, //audpk,
        //     $logs
        // );
    }

    public function CargarSellOutDiario()
    {
        $tprid = 2;
        $tprnombre = "Sell Out";
        $respuesta = true;
        $mensaje =  "El sell Out diario se actualizo correctamente";

        $logs = array(
            "SKUS_FALTANTES" => [],
            "SUCS_FALTANTES" => [],
        );

        $pks = array(
            "PK_FECHAS" => array(
                "NUEVOS" => [],
            ),
            "PK_VENTAS_SSO" => array(
                "NUEVOS" => [],
                "EDITADOS"
            ),
            "PK_TSU" => array(
                "NUEVOS" => [],
                "EDITADOS"
            ),
            "PK_SCA" => array(
                "NUEVOS" => [],
                "EDITADOS"
            ),
        );

        $arrayMeses = array(
            array(
                "mes" => "Jan",
                "numero" => "01",
                "espaniol" => "ENE"
            ),
            array(
                "mes" => "Feb",
                "numero" => "02",
                "espaniol" => "FEB"
            ),
            array(
                "mes" => "Mar",
                "numero" => "03",
                "espaniol" => "MAR"
            ),
            array(
                "mes" => "Apr",
                "numero" => "04",
                "espaniol" => "ABR"
            ),
            array(
                "mes" => "May",
                "numero" => "05",
                "espaniol" => "MAY"
            ),
            array(
                "mes" => "Jun",
                "numero" => "06",
                "espaniol" => "JUN"
            ),
            array(
                "mes" => "Jul",
                "numero" => "07",
                "espaniol" => "JUL"
            ),
            array(
                "mes" => "Aug",
                "numero" => "08",
                "espaniol" => "AGO"
            ),
            array(
                "mes" => "Sep",
                "numero" => "09",
                "espaniol" => "SET"
            ),
            array(
                "mes" => "Oct",
                "numero" => "10",
                "espaniol" => "OCT"
            ),
            array(
                "mes" => "Nov",
                "numero" => "11",
                "espaniol" => "NOV"
            ),
            array(
                "mes" => "Dec",
                "numero" => "12",
                "espaniol" => "DIC"
            )
        );

        $datos = json_decode( file_get_contents('http://backend-api.leadsmartview.com/obtenerSellOut'), true );

        $mesSelec  = "";
        $anioSelec = "";

        foreach($datos as $posicion => $dato){

            $soldto    = $dato['COD_SOLD_TO'];
            $sku       = $dato['SKU'];
            $sell      = $dato['SELLS'];

            $dia       = $dato['DAY'];
            $mesNumero = "0";
            $mesTxt    = "";
            $anio      = $dato['YEAR'];

            $fecid = 0;

            foreach ($arrayMeses as $key => $value) {
                if($value['mes'] == $dato['MONTH']){
                    $mesNumero = $value['numero'];
                    $mesTxt    = $value['espaniol'];
                    break;
                }
            }

            $fec = fecfechas::where('fecdia', $dia)
                            ->where('fecmes', $mesTxt)
                            ->where('fecano', $anio)
                            ->first(['fecid']);

            if($fec){
                $fecid = $fec->fecid;

            }else{

                $fecn = new fecfechas;
                $fecn->fecfecha     = new \DateTime(date("Y-m-d", strtotime($anio.'-'.$mesNumero.'-'.$dia)));
                $fecn->fecdia       = $dia;
                $fecn->fecmes       = $mesTxt;
                $fecn->fecmesnumero = $mesNumero;
                $fecn->fecano       = $anio;
                if($fecn->save()){
                    $fecid = $fecn->fecid;
                    $pks["PK_FECHAS"]["NUEVOS"][] = "NUEVA FEC-".$fecid;
                }
            }

            if($posicion == 0){
                
                $mesSelec  = $mesTxt;
                $anioSelec = $anio;

                vsoventassso::where('fecid', $fecid)
                            ->update(['vsovalorizado' => 0]);

            }


            $pro = proproductos::join('catcategorias as cat', 'cat.catid', 'proproductos.catid')
                                ->where('proproductos.prosku', 'LIKE', '%'.$sku)
                                ->first([
                                    'proproductos.proid',
                                    'proproductos.catid',
                                    'cat.catnombre'
                                ]);

            if($pro){

                $categoriaid     = $pro->catid;
                $categoriaNombre = $pro->catnombre;

                $usu = ussusuariossucursales::join('usuusuarios as usu', 'usu.usuid', 'ussusuariossucursales.usuid')
                                            ->where('ususoldto', 'LIKE', '%'.$soldto)
                                            ->first(['usuid', 'ussusuariossucursales.sucid']);

                $sucid = 0;

                if($usu){
                    
                    $sucid = $usu->sucid;

                    $vso = vsoventassso::where('fecid', $fecid)
                                        ->where('proid', $pro->proid)
                                        ->where('sucid', $sucid)
                                        ->where('tpmid', 1)
                                        ->first();

                    if($vso){

                        $vso->vsovalorizado = $real + $vso->vsovalorizado;
                        if($vso->update()){
                            $pks['PK_VENTAS_SSO']["EDITADOS"][] = "VSO-".$vso->vsoid;
                        }
                        
                    }else{
                        $vson = new vsoventassso;
                        $vson->fecid         = $fecid;
                        $vson->proid         = $pro->proid;
                        $vson->sucid         = $sucid;
                        $vson->tpmid         = 1;
                        $vson->vsocantidad   = 0;
                        $vson->vsovalorizado = $real;
                        if($vson->save()){
                            $pks['PK_VENTAS_SSO']["NUEVO"][] = "VSO-".$vson->vsoid;
                        }
                    }
                    
                }else{
                    $logs['SUCS_FALTANTES'][] = $soldto;
                    $respuesta = false;
                    $mensaje = "Lo sentimos, hubieron algunas sucursales (soldto) que no se encontraron";
                }

            }else{
                $logs['SKUS_FALTANTES'][] = $sku;
                $respuesta = false;
                $mensaje = "Lo sentimos, hubieron algunos productos (skus) que no se encontraron";
            }
        }

        $sucs = sucsucursales::where('sucestado', 1)->get();

        foreach ($sucs as $key => $suc) {

            $real = vsoventassso::join('fecfechas as fec', 'fec.fecid', 'vsoventassso.fecid')
                                ->join('proproductos as pro', 'pro.proid', 'vsoventassso.proid')
                                ->where('fec.fecano', $anioSelec)
                                ->where('fec.fecmes', $mesSelec)
                                ->where('sucid', $suc->sucid)
                                ->sum('vsovalorizado');

            $tsu = tsutipospromocionessucursales::join('fecfechas as fec', 'fec.fecid', 'tsutipospromocionessucursales.fecid')
                                                ->where('fec.fecano', $anioSelec)
                                                ->where('fec.fecmes', $mesSelec)
                                                ->where('fec.fecdia', "01")
                                                ->where('sucid', $suc->sucid)
                                                ->where('tprid', $tprid)
                                                ->first([
                                                    'tsuid', 
                                                    'tsuvalorizadoreal', 
                                                    'tsuvalorizadoobjetivo', 
                                                    'treid'
                                                ]);
            if($tsu){
                $tsuid     = $tsu->tsuid;
                $nuevoReal = $real;

                if($tsu->tsuvalorizadoobjetivo == 0){
                    $porcentajeCumplimiento = $nuevoReal;
                    $togo = $nuevoReal;
                }else{
                    $porcentajeCumplimiento = (100*$nuevoReal)/$tsu->tsuvalorizadoobjetivo;
                    $togo = $tsu->tsuvalorizadoobjetivo - $nuevoReal;
                }

                $totalRebate = 0;
                
                $tsu->tsuvalorizadoreal         = $nuevoReal;
                
                $tsu->tsuvalorizadotogo         = $togo;
                $tsu->tsuporcentajecumplimiento = $porcentajeCumplimiento;
                $tsu->tsuvalorizadorebate       = $totalRebate;
                if($tsu->update()){
                    $pks['PK_TSU']["EDITADOS"][] = "TSU-".$tsuid;
                }

            }else{
                $nuevotsu = new tsutipospromocionessucursales;
                $nuevotsu->fecid = $fecid;
                $nuevotsu->sucid = $suc->sucid;
                $nuevotsu->tprid = $tprid;
                $nuevotsu->tsuporcentajecumplimiento = 0;
                $nuevotsu->tsuvalorizadoobjetivo     = 0;
                $nuevotsu->tsuvalorizadoreal         = $real;
                $nuevotsu->tsuvalorizadorebate       = 0;
                $nuevotsu->tsuvalorizadotogo         = 0;
                if($nuevotsu->save()){
                    $tsuid = $nuevotsu->tsuid;
                    $pks['PK_TSU']["NUEVOS"][] = "TSU-".$tsuid;
                }
            }

        }





    }

    public function CargarSellOutEspecifico()
    {

    }


}
