<?php

namespace App\Http\Controllers\Sistema\Usuario\Sucursales\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\usuusuarios;
use App\ussusuariossucursales;
use App\tuptiposusuariospermisos;
use App\sucsucursales;
use App\zonzonas;
use App\zgszonasgrupossucursales;

class SucursalesMostrarController extends Controller
{
    public function mostrarSucursales(Request $request)
    {
        $usutoken = $request->header('api_token');
        
        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;
        $zonas = [];
        $gsus  = [];
        $cass  = [];

        try{
            
            $usuusuario = usuusuarios::where('usutoken', $request->header('api_token'))->first(['usuid', 'tpuid']);

            if($usuusuario){
                $tup = tuptiposusuariospermisos::join('pempermisos as pem', 'pem.pemid', 'tuptiposusuariospermisos.pemid')
                                                ->where('pem.pemid', 7)
                                                ->where('tuptiposusuariospermisos.tpuid', $usuusuario->tpuid)
                                                ->first([
                                                    'pem.pemid'
                                                ]);
                                                
                if($tup || $usuusuario->tpuid == 1){

                    $zgss = zgszonasgrupossucursales::all();

                    $zonas = zonzonas::where('zonestado', 1)
                                        ->get([
                                            'zonid',
                                            'zonnombre',
                                            'casid'
                                        ]);

                    // foreach($zonas as $posicionZona => $zona){
                    //     $zonas[$posicionZona]['gsus'] = [];
                    //     foreach($zgss as $zgs){
                    //         if($zona->zonid == $zgs->zonid){
                    //             $zonas[$posicionZona]['gsus'][] = $zgs->gsuid;
                    //         }
                    //     }
                    // }

                    $gsus = sucsucursales::join('gsugrupossucursales as gsu', 'gsu.gsuid', 'sucsucursales.gsuid')
                                        ->where('sucestado', 1)
                                        ->distinct('gsu.gsuid')
                                        ->get([
                                            'gsu.gsuid',
                                            'gsunombre'
                                        ]);

                    foreach($gsus as $posicionGsu => $gsu){
                        $gsus[$posicionGsu]['zonas'] = [];
                        // foreach($zgss as $zgs){
                        //     if($gsu->gsuid == $zgs->gsuid){
                        //         $gsus[$posicionGsu]['zonas'][] = $zgs->zonid;
                        //     }
                        // }
                    }

                    $cass = sucsucursales::join('cascanalessucursales as cas', 'cas.casid', 'sucsucursales.casid')
                                        ->where('sucestado', 1)
                                        ->distinct('cas.casid')
                                        ->get([
                                            'cas.casid',
                                            'casnombre'
                                        ]);
                    

                                                    
                    $ussusuariossucursales = sucsucursales::join('zonzonas as zon', 'zon.zonid', 'sucsucursales.zonid')
                                                            ->where('sucestado', 1)
                                                            ->orderBy('sucsucursales.sucorden', 'DESC')
                                                            ->get([
                                                                'sucsucursales.sucid',
                                                                'zon.zonid',
                                                                'gsuid',
                                                                'sucsucursales.casid',
                                                                'zon.zonnombre',
                                                                'sucsucursales.sucnombre',
                                                                'sucsucursales.sucsoldto'
                                                            ]);                


                }else{
                    $zonas = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                                    ->join('zonzonas as zon', 'zon.zonid', 'suc.zonid')
                                                    ->where('ussusuariossucursales.usuid', $usuusuario->usuid )
                                                    ->distinct('zon.zonid')
                                                    ->get([
                                                        'zon.zonid',
                                                        'zon.zonnombre',
                                                        'zon.casid'
                                                    ]);

                    $gsus = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                                ->join('gsugrupossucursales as gsu', 'gsu.gsuid', 'suc.gsuid')
                                                ->where('ussusuariossucursales.usuid', $usuusuario->usuid )
                                                ->where('sucestado', 1)
                                                ->distinct('gsu.gsuid')
                                                ->get([
                                                    'gsu.gsuid',
                                                    'gsunombre'
                                                ]);

                    $cass = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                                ->join('cascanalessucursales as cas', 'cas.casid', 'suc.casid')
                                                ->where('ussusuariossucursales.usuid', $usuusuario->usuid )
                                                ->where('sucestado', 1)
                                                ->distinct('cas.casid')
                                                ->get([
                                                    'cas.casid',
                                                    'casnombre'
                                                ]);

                    $ussusuariossucursales = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                                            ->join('zonzonas as zon', 'zon.zonid', 'suc.zonid')
                                                            ->where('ussusuariossucursales.usuid', $usuusuario->usuid )
                                                            ->where('suc.sucestado', 1)
                                                            ->distinct('suc.sucid')
                                                            ->get([
                                                                'ussusuariossucursales.ussid',
                                                                'zon.zonid',
                                                                'zon.zonnombre',
                                                                'suc.sucid',
                                                                'suc.sucnombre',
                                                                'suc.casid',
                                                                'gsuid'
                                                            ]);
                }
                                                            
                if(sizeof($ussusuariossucursales) > 0){
                    $datos          = $ussusuariossucursales;
                    $respuesta      = true;
                    $linea          = __LINE__;
                    $mensaje        = 'Se cargaron las sucursales satisfactoriamente.';
                    $mensajeDetalle = sizeof($ussusuariossucursales).' registros encontrados.';
                }else{
                    $respuesta      = false;
                    $linea          = __LINE__;
                    $mensaje        = 'Lo sentimos, el usuario no tiene sucursales asignadas';
                    $mensajeDetalle = sizeof($ussusuariossucursales).' registros encontrados.';
                }
            }else{
                $respuesta = false;
                $linea     = __LINE__;
                $mensaje   = 'Lo sentimos, el usuario no existe';
            }

        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
        }

        $requestsalida = response()->json([
            'respuesta'      => $respuesta,
            'mensaje'        => $mensaje,
            'datos'          => $datos,
            'linea'          => $linea,
            'mensajeDetalle' => $mensajeDetalle,
            'mensajedev'     => $mensajedev,
            'zonas'          => $zonas,
            'gsus'           => $gsus,
            'cass'           => $cass
        ]);
        
        return $requestsalida;

    }
}
