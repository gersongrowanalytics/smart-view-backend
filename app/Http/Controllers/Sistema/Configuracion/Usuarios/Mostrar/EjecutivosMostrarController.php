<?php

namespace App\Http\Controllers\Sistema\Configuracion\Usuarios\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\cejclientesejecutivos;
use App\usuusuarios;

class EjecutivosMostrarController extends Controller
{
    public function mostrarEjecutivos(Request $request)
    {

        $usutoken = $request->header('api_token');

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;

        try{
            $usuarios = usuusuarios::join('perpersonas as per', 'per.perid', 'usuusuarios.perid')
                                ->join('tputiposusuarios as tpu', 'tpu.tpuid', 'usuusuarios.tpuid')
                                ->where('usuusuarios.tpuid', 3 )
                                ->get([
                                    'usuusuarios.usuid',
                                    'tpu.tpuid',
                                    'tpu.tpunombre',
                                    'tpu.tpuprivilegio',
                                    'per.perid',
                                    'per.pernombrecompleto',
                                    'usuusuarios.usuusuario',
                                    'usuusuarios.usucorreo'
                                ]);

            if(sizeof($usuarios) > 0){
                
                foreach($usuarios as $posicion => $usuario){
                    
                    $cej = cejclientesejecutivos::join('usuusuarios as usu', 'usu.usuid', 'cejclientesejecutivos.cejcliente')
                                            ->join('tputiposusuarios as tpu', 'tpu.tpuid', 'usu.tpuid')
                                            ->join('perpersonas as per', 'per.perid', 'usu.perid')
                                            ->where('cejclientesejecutivos.cejejecutivo', $usuario->usuid)
                                            ->distinct('cejclientesejecutivos.cejcliente')
                                            ->get([
                                                'cejclientesejecutivos.cejid',
                                                'usuusuarios.usuid',
                                                'tpu.tpuid',
                                                'tpu.tpunombre',
                                                'tpu.tpuprivilegio',
                                                'per.perid',
                                                'per.pernombrecompleto',
                                                'usuusuarios.usuusuario',
                                                'usuusuarios.usucorreo',
                                            ]);
                    if(sizeof($cej) > 0){
                        $usuarios[$posicion]['clientes'] = $cej;
                    }else{
                        $usuarios[$posicion]['clientes'] = [];
                    }
                }

                $respuesta      = true;
                $datos          = $usuarios;
                $linea          = __LINE__;
                $mensaje        = 'Los usuarios ejecutvis se cargaron satisfactoriamente.';
                $mensajeDetalle = sizeof($usuarios).' registros encontrados.';

            }else{
                $respuesta      = false;
                $datos          = [];
                $linea          = __LINE__;
                $mensaje        = 'Lo sentimos, no se econtraron usuarios ejecutivos registrados';
                $mensajeDetalle = sizeof($usuarios).' registros encontrados.';
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
}
