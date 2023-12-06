<?php

namespace App\Http\Controllers\Sistema\SellOut;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use App\scasucursalescategorias;
use App\tsutipospromocionessucursales;
use App\fecfechas;
use App\catcategorias;
use App\sucsucursales;

class CargarSOXCategoriaController extends Controller
{
    // 2021, 1 2021 DE ENERO
    public function CargarSOXCategoria($anioSelec, $mesSelec)
    {

        ini_set('max_execution_time', '300');
        set_time_limit(300);

        if(strlen($mesSelec) == 1){
            $mesSelec = "0$mesSelec";
        }

        $tprid = 2;
        $tprnombre = "Sell Out";
        $respuesta = true;
        $mensaje =  "El sell Out especifico X categoria se actualizo correctamente";

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

        // REINICAR DATA A 0
        scasucursalescategorias::join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                ->join('fecfechas as fec', 'fec.fecid', 'scasucursalescategorias.fecid')
                                ->where('fec.fecano', $anioSelec)
                                ->where('fec.fecmes', $mesTxtFec)
                                ->where('tsu.tprid', $tprid)
                                ->update([
                                    'scavalorizadoreal' => 0, 
                                    'scavalorizadotogo' => 0
                                ]);

        tsutipospromocionessucursales::join('fecfechas as fec', 'fec.fecid', 'tsutipospromocionessucursales.fecid')
                                    ->where('tprid', $tprid)
                                    ->where('fec.fecano', $anioSelec)
                                    ->where('fec.fecmes', $mesTxtFec)
                                    ->update([
                                        'tsuvalorizadoreal' => 0, 
                                        'tsuvalorizadotogo' => 0,
                                        'tsuporcentajecumplimiento' => 0,
                                        'tsuvalorizadorebate' => 0,
                                    ]);

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
                $pks["PK_FECHAS"]["NUEVOS"][] = "NUEVA FEC-".$fecidFec;
            }
        }


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://back-api-leadsmartview.grow-corporate.com/ws/obtenerSOconsolidadoXCategoria/'.$anioSelec.'/'.$mesTxtIngles);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);

        $datos = json_decode($res, true);

        foreach($datos as $posicion => $dato){
            
            $soldto    = $dato['COD_SOLD_TO'];

            $categoria = $dato['CATEGORY'];
            $real      = $dato['SELLS'];

            if($dato['SELLS'] == null){
                $real = 0;
            }else{
                $real = $dato['SELLS'];
            }

            $cat = catcategorias::where('catnombreopcional', $categoria)->first();

            if($cat){
                $catid = $cat->catid;

                $suc = sucsucursales::where('sucsoldto', $soldto)
                                        ->first();

                $sucid = 0;

                if($suc){
                    $sucid = $suc->sucid;

                    $tsu = tsutipospromocionessucursales::where('fecid', $fecidFec)
                                                ->where('sucid', $sucid)
                                                ->where('tprid', $tprid)
                                                ->first([
                                                    'tsuid', 
                                                    'tsuvalorizadoreal', 
                                                    'tsuvalorizadoobjetivo', 
                                                    'treid'
                                                ]);
                    $tsuid = 0;
                    if($tsu){
                        $tsuid = $tsu->tsuid;
                        $nuevoReal = $tsu->tsuvalorizadoreal + $real;

                        if(intval(round($tsu->tsuvalorizadoobjetivo)) <= 0){
                            $porcentajeCumplimiento = $nuevoReal;
                            $togo = 0;
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
                        $nuevotsu->fecid = $fecidFec;
                        $nuevotsu->sucid = $sucid;
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


                    $sca = scasucursalescategorias::where('tsuid', $tsuid)
                                                ->where('scasucursalescategorias.fecid', $fecidFec)
                                                ->where('sucid', $sucid)
                                                ->where('catid', $catid)
                                                ->first();

                    if($sca){
                        $scaid = $sca->scaid;
                        $sca->scavalorizadoreal = $real;

                        if(intval(round($sca->scavalorizadoobjetivo)) <= 0){
                            $sca->scavalorizadotogo = 0;
                        }else{
                            $sca->scavalorizadotogo = $sca->scavalorizadoobjetivo - $real;
                        }


                        $sca->scaiconocategoria = env('APP_URL').'/Sistema/categorias-tiposPromociones/img/iconos/'.$cat->catnombre.'-'.$tprnombre.'.png';
                        if($sca->update()){
                            $pks['PK_SCA']["EDITADOS"][] = "SCA-".$scaid;
                        }
                    }else{
                        $nuevosca = new scasucursalescategorias;
                        $nuevosca->sucid                 = $sucid;
                        $nuevosca->catid                 = $catid;
                        $nuevosca->fecid                 = $fecidFec;
                        $nuevosca->tsuid                 = $tsuid;
                        $nuevosca->scavalorizadoobjetivo = 0;
                        $nuevosca->scaiconocategoria     = env('APP_URL').'/Sistema/categorias-tiposPromociones/img/iconos/'.$cat->catnombre.'-'.$tprnombre.'.png';
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
                    $mensaje = "Lo sentimos, hubieron algunas sucursales (soldto) que no se encontraron ".$soldto;
                }

            }else{
                $logs['CATEGORIA_NO_EXISTE'][] = $categoria;
                $respuesta = false;
                $mensaje = "Lo sentimos, hubieron algunas categorias que no se encontraron ".$categoria;
            }


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
