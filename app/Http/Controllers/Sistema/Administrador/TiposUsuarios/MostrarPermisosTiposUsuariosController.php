<?php

namespace App\Http\Controllers\Sistema\Administrador\TiposUsuarios;

use App\Http\Controllers\Controller;
use App\pempermisos;
use App\tpetipopermiso;
use App\tputiposusuarios;
use App\tuptiposusuariospermisos;
use Illuminate\Http\Request;

class MostrarPermisosTiposUsuariosController extends Controller
{

    public function MostrarPermisosTiposUsuarios(Request $request)
    {
        $respuesta = false;
        $mensaje = "";
        $array_tp = [];

        $re_tpuid = $request['re_tpuid'];

        $tpu = tputiposusuarios::where('tpuid', $re_tpuid)
                                    ->get([
                                        'tpuid',
                                        'tpunombre',
                                        'tpufechainicio',
                                        'tpufechafinal',
                                        'tpuimagen',
                                        'tpuimagencircular',
                                        'estid'
                                    ]);

        $tpes = tpetipopermiso::get(['tpeid','tpenombre']);
        if (sizeof($tpes) > 0) {
            foreach ($tpes as $key => $tipo_permiso) {
                $pem = pempermisos::where('tpeid', $tipo_permiso['tpeid'])
                                                ->get(['pemid','pemnombre']);
                foreach ($pem as $item) {
                    $tup = tuptiposusuariospermisos::where('pemid', $item['pemid'])
                                                ->where('tpuid', $re_tpuid)
                                                ->first(['tupid']);
                    if ($tup) {
                        $array_tp[$key]['tipo_permiso'] = $tipo_permiso['tpenombre'];
                        $array_tp[$key]['seleccionado_todo'] = true;
                        $array_tp[$key]['permisos'][] = [
                                                            "pemid" => $item['pemid'],
                                                            "pemnombre" => $item['pemnombre'],
                                                            "seleccionado" => true
                                                        ];

                    }else{
                        $array_tp[$key]['tipo_permiso'] = $tipo_permiso['tpenombre'];
                        $array_tp[$key]['seleccionado_todo'] = true;
                        $array_tp[$key]['permisos'][] = [
                                                            "pemid" => $item['pemid'],
                                                            "pemnombre" => $item['pemnombre'],
                                                            "seleccionado" => false
                                                        ];
                    }
                }           
            }

            $respuesta      = true;
            $mensaje        = 'Los tipos de permisos se cargaron satisfactoriamente';
        }else{
            $respuesta      = false;
            $mensaje        = 'Los tipos de permisos no se cargaron satisfactoriamente';
        }
        $requestsalida = response()->json([
            "respuesta"    => $respuesta,
            "mensaje"      => $mensaje,
            "tipo_usuario" => $tpu,
            "datos"        => $array_tp
        ]);

        return $requestsalida;
    }
    
    // public function MostrarPermisosTiposUsuarios2(Request $request)
    // {
    //     $respuesta = false;
    //     $mensaje   = '';
    //     $arrayPermisosTipoUsuario = [];

    //     $tpuid = $request['tipo_usuario'];

    //     $permisosTipoUsuario = tuptiposusuariospermisos::join('pempermisos as pem', 'pem.pemid', 'tuptiposusuariospermisos.pemid')
    //                                     ->where('tpuid', $tpuid)
    //                                     ->get([
    //                                         'pem.pemid'
    //                                     ]);
        
    //     $permisos = pempermisos::get([
    //                                 'pemid',
    //                                 'pemslug'
    //                             ]);

    //     foreach($permisos as $key => $permiso)
    //     {
    //         $arrayPermisosTipoUsuario[$key]['pemid'] = $permiso['pemid'];
    //         $arrayPermisosTipoUsuario[$key]['pemslug'] = $permiso['pemslug'];
    //         $arrayPermisosTipoUsuario[$key]['seleccionado'] = false;             
            
    //         foreach ($permisosTipoUsuario as $permisoTU) {
    //             if ($permiso['pemid'] == $permisoTU['pemid']) {
    //                 $arrayPermisosTipoUsuario[$key]['seleccionado'] = true;
    //             }
    //         }
    //     }

    //     if(sizeof($arrayPermisosTipoUsuario) > 0){
    //         $respuesta      = true;
    //         $mensaje        = 'Los permisos del tipo de usuario se cargaron satisfactoriamente';
    //     }else{
    //         $respuesta      = false;
    //         $mensaje        = 'Los permisos del tipo de usuario no se cargaron satisfactoriamente';
    //     }

    //     $requestsalida = response()->json([
    //         "respuesta" => $respuesta,
    //         "mensaje"   => $mensaje,
    //         "permisos"  => $arrayPermisosTipoUsuario
    //     ]);

    //     return $requestsalida;
    // }
}
