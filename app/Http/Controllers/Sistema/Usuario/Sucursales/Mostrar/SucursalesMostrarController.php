<?php

namespace App\Http\Controllers\Sistema\Usuario\Sucursales\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\usuusuarios;
use App\ussusuariossucursales;
use App\tuptiposusuariospermisos;

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
                $tup = tuptiposusuariospermisos::join('pempermisos as pem', 'pem.pemid', 'tuptiposusuariospermisos.pemid')
                                                ->where('pem.pemid', 7)
                                                ->where('tuptiposusuariospermisos.tpuid', $usuusuario->tpuid)
                                                ->first([
                                                    'pem.pemid'
                                                ]);
                                                
                // if($usuusuario->tpuid == 1){
                if($tup || $usuusuario->tpuid == 1){

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
                                                                    ->distinct('suc.sucnombre')
                                                                    ->get([
                                                                        'ussusuariossucursales.ussid',
                                                                        'zon.zonid',
                                                                        'zon.zonnombre',
                                                                        'suc.sucid',
                                                                        'suc.sucnombre'
                                                                    ]);
                }else{
                    $zonas = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                                    ->join('zonzonas as zon', 'zon.zonid', 'suc.zonid')
                                                    ->where('ussusuariossucursales.usuid', $usuusuario->usuid )
                                                    ->distinct('zon.zonid')
                                                    ->get([
                                                        'zon.zonid',
                                                        'zon.zonnombre'
                                                    ]);

                    $ussusuariossucursales = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                                            ->join('usuusuarios as usu', 'usu.usuid', 'ussusuariossucursales.usuid')
                                                            ->join('zonzonas as zon', 'zon.zonid', 'suc.zonid')
                                                            ->where('ussusuariossucursales.usuid', $usuusuario->usuid )
                                                            ->where('usu.estid', 1)
                                                            ->distinct('suc.sucid')
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
        
        return $requestsalida;

    }
}
