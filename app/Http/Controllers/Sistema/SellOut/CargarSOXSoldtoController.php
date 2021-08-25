<?php

namespace App\Http\Controllers\Sistema\SellOut;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use App\vsoventassso;
use App\fecfechas;
use App\proproductos;
use App\sucsucursales;
use App\osoobjetivossso;

class CargarSOXSoldtoController extends Controller
{
    public function CargarSOXSoldTo($anioSelec, $mesSelec)
    {

        ini_set('max_execution_time', '300');
        set_time_limit(300);

        if(strlen($mesSelec) == 1){
            $mesSelec = "0$mesSelec";
        }

        $tprid = 2;
        $tprnombre = "Sell Out";
        $respuesta = true;
        $mensaje =  "El sell Out especifico X soldto se actualizo correctamente";

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

        foreach ($arrayMeses as $key => $value) {
            if($value['numero'] == $mesSelec){
                $mesNumeroFec = $value['numero'];
                $mesTxtFec    = $value['espaniol'];
                $mesTxtIngles = $value['mes'];
                break;
            }
        }

        vsoventassso::join('fecfechas as fec', 'fec.fecid', 'vsoventassso.fecid')
                    ->where('fec.fecano', $anioSelec)
                    ->where('fec.fecmes', $mesTxtFec)
                    ->update(['vsovalorizado' => 0, 'vsovalorizadoniv' => 0]);

        // 

        $fecMes = fecfechas::where('fecdia', "01")
                        ->where('fecmes', $mesTxtFec)
                        ->where('fecano', $anioSelec)
                        ->first(['fecid']);

        if($fecMes){
            $fecidFec = $fecMes->fecid;

        }else{

            $fecn = new fecfechas;
            $fecn->fecfecha     = new \DateTime(date("Y-m-d", strtotime($anioSelec.'-'.$mesNumeroFec.'-01')));
            $fecn->fecdia       = "01";
            $fecn->fecmes       = $mesTxtFec;
            $fecn->fecmesnumero = $mesNumeroFec;
            $fecn->fecano       = $anioSelec;
            if($fecn->save()){
                $fecidFec = $fecn->fecid;
                $pks["PK_FECHAS"]["NUEVOS"][] = "NUEVA FEC-".$fecid;
            }
        }

        $datos = json_decode( file_get_contents('http://backend-api.leadsmartview.com/ws/obtenerSOconsolidado/'.$anioSelec.'/'.$mesTxtIngles), true );

        if(sizeof($datos) > 0){
            foreach($datos as $posicion => $dato){
                $soldto      = $dato['COD_SOLD_TO'];
                $categoria   = $dato['CATEGORY'];
                $codMaterial = $dato['COD_MATERIAL'];
                $real        = $dato['SELLS'];
                $niv         = $dato['NIV'];
    
                if($dato['SELLS'] == null){
                    $real = 0;
                }else{
                    $real = $dato['SELLS'];
                }
    
                if($dato['NIV'] == null){
                    $niv = 0;
                }else{
                    $niv = $dato['NIV'];
                }

                $pro = proproductos::where('prosku', $codMaterial)->first();
    
                if($pro){
                    $proid = $pro->proid;
    
                    $suc = sucsucursales::where('sucsoldto', $soldto)
                                            ->first();
    
                    $sucid = 0;
    
                    if($suc){
                        $sucid = $suc->sucid;
                        
                        $vso = vsoventassso::where('fecid', $fecidFec)
                                            ->where('proid', $proid)
                                            ->where('sucid', $sucid)
                                            ->where('tpmid', 1)
                                            ->first();
    
                        if($vso){
    
                            $vso->vsovalorizado    = $real + $vso->vsovalorizado;
                            $vso->vsovalorizadoniv = $niv + $vso->vsovalorizadoniv;
                            $vso->update();
    
                        }else{
                            $vson = new vsoventassso;
                            $vson->fecid         = $fecidFec;
                            $vson->proid         = $proid;
                            $vson->sucid         = $sucid;
                            $vson->tpmid         = 1;
                            $vson->vsocantidad   = 0;
                            $vson->vsovalorizado = $real;
                            $vson->vsovalorizadoniv = $niv;
                            $vson->save();
                        }

                        $oso = osoobjetivossso::where('fecid', $fecidFec)
                                            ->where('proid', $proid)
                                            ->where('sucid', $sucid)
                                            ->where('tpmid', 1)
                                            ->first();

                        if(!$oso){
                            $oson = new osoobjetivossso;
                            $oson->fecid         = $fecidFec;
                            $oson->proid         = $proid;
                            $oson->sucid         = $sucid;
                            $oson->tpmid         = 1;
                            $oson->osocantidad   = 0;
                            $oson->osovalorizado = 0;
                            $oson->save();
                        }
    
                    }else{
                        $logs['SUCS_FALTANTES'][] = $soldto;
                        $respuesta = false;
                        $mensaje = "Lo sentimos, hubieron algunas sucursales (soldto) que no se encontraron ".$soldto;
                    }
    
                }else{
                    $logs['PRODUCTO_NO_EXISTE'][] = $codMaterial;
                    $respuesta = false;
                    $mensaje = "Lo sentimos, hubieron algunos productos que no se encontraron ".$codMaterial;
                }
            }
        }else{
            $respuesta = false;
            $mensaje = "Lo sentimos, no se encontro data en la request";
        }

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            "logs"      => $logs,
            "pks"       => $pks,
            "datos"     => $datos,
            "fecha"     => $anioSelec.'/'.$mesSelec
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            "ADMIN",
            "1",
            null,
            $datos, //audjsonentrada
            $requestsalida, //audjsonsalida
            $mensaje, //auddescripcion
            "ACTUALIZACION", //audaccion,
            "/obtenerSellOutEspecifico", //audruta,
            $pks, //audpk,
            $logs
        );

        return $requestsalida;



    }
}
