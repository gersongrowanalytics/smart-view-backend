<?php

namespace App\Http\Controllers\Sistema\Administrador\Usuarios;

use App\Http\Controllers\Controller;
use App\usuusuarios;
use App\paupaisesusuarios;
use App\ussusuariossucursales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MostrarUsuariosController extends Controller
{
    public function MostrarUsuarios(Request $request)
    {
        $array_usuarios = [];

        $respuesta      = false;
        $mensaje        = '';

        $usutoken = $request->header('api_token');
        $re_tipoUsuario = $request['re_tipoUsuario'];

        $usuarios = usuusuarios::join('perpersonas as per', 'per.perid', 'usuusuarios.perid')
                                ->join('tputiposusuarios as tpu', 'tpu.tpuid', 'usuusuarios.tpuid')
                                ->leftJoin('zonzonas as zon', 'zon.zonid','usuusuarios.zonid')
                                ->join('estestados as est', 'est.estid','usuusuarios.estid')
                                ->where(function ($query) use($re_tipoUsuario) {
                                    foreach($re_tipoUsuario as $tu){
                                        if(isset($tu['seleccionado'])){
                                            if($tu['seleccionado'] == true){
                                                $query->orwhere('usuusuarios.tpuid', $tu['tpuid']);
                                            }
                                        }
                                    }
                                })
                                ->orderBy('usuusuarios.created_at', 'DESC')
                                ->paginate(10);
                                // ->get([
                                //     'usuusuarios.usuid',
                                //     'per.pernombrecompleto',
                                //     'per.pernombre',
                                //     'per.perapellidopaterno',
                                //     'usuusuarios.usucorreo',
                                //     'usuusuarios.usucontrasena',
                                //     'tpu.tpunombre',
                                //     'zon.zonnombre',                                    
                                //     'est.estnombre',
                                // ]);
        
       
        
        // foreach ($usuarios as $key => $usuario) {
        //     $paises = paupaisesusuarios::join('paipaises as pai','pai.paiid','paupaisesusuarios.paiid')
        //                                 ->where('paupaisesusuarios.usuid', $usuario['usuid'])
        //                                 ->get([
        //                                     'pai.paiid',
        //                                     'pai.painombre',
        //                                     'pai.paiicono',
        //                                     'pai.paiiconocircular',
        //                                     'pai.paiiconomas',
        //                                     'pai.estid'
        //                                 ]);

        //     $uss = ussusuariossucursales::where('usuid', $usuario['usuid'])
        //                                 ->get();
            
        //     $usuarios[$key]['paises'] = $paises;

        //     $usuarios[$key]['uss'] = $uss;
        // }

        if(sizeof($usuarios) > 0){
            $respuesta      = true;
            $mensaje        = 'Los usuarios se cargaron satisfactoriamente';
        }else{
            $respuesta      = false;
            $mensaje        = 'Los usuarios no se cargaron satisfactoriamente';
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $usuarios
        ]);

        return $requestsalida;
    }
}
