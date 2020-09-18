<?php

namespace App\Http\Controllers\Sistema\Usuario\Sucursales\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use App\usuusuarios;
use App\ussusuariossucursales;

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

        try{
            
            $usuusuario = usuusuarios::where('usutoken', $request->header('api_token'))->first(['usuid', 'tpuid']);

            if($usuusuario){
                
                if($usuusuario->tpuid == 1){

                    $zonas = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                                    ->join('usuusuarios as usu', 'usu.usuid', 'ussusuariossucursales.usuid')
                                                    ->join('zonzonas as zon', 'zon.zonid', 'usu.zonid')
                                                    ->where('usu.estid', 1)
                                                    ->distinct('zon.zonid')
                                                    ->get([
                                                        'zon.zonid',
                                                        'zon.zonnombre',
                                                    ]);

                    $ussusuariossucursales = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                                                    ->join('usuusuarios as usu', 'usu.usuid', 'ussusuariossucursales.usuid')
                                                                    ->join('zonzonas as zon', 'zon.zonid', 'usu.zonid')
                                                                    ->where('usu.estid', 1)
                                                                    ->get([
                                                                        'ussusuariossucursales.ussid',
                                                                        'zon.zonid',
                                                                        'zon.zonnombre',
                                                                        'suc.sucid',
                                                                        'suc.sucnombre'
                                                                    ]);
                }else{
                    $zonas = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                                    ->join('zonzonas as zon', 'zon.zonid', 'usu.zonid')
                                                    ->where('ussusuariossucursales.usuid', $usuusuario->usuid )
                                                    ->distinct('zon.zonid')
                                                    ->get([
                                                        'zon.zonid',
                                                        'zon.zonnombre'
                                                    ]);

                    $ussusuariossucursales = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                                            ->join('zonzonas as zon', 'zon.zonid', 'usu.zonid')
                                                            ->where('ussusuariossucursales.usuid', $usuusuario->usuid )
                                                            ->get([
                                                                'ussusuariossucursales.ussid',
                                                                'zon.zonid',
                                                                'zon.zonnombre',
                                                                'suc.sucid',
                                                                'suc.sucnombre'
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
            'zonas'          => $zonas
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            null,
            $request['ip'],
            $request,
            $requestsalida,
            'Mostrar las sucursales que tiene un usuario',
            'MOSTRAR',
            '',
            null
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;

    }
}
