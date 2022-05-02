<?php

namespace App\Http\Controllers\Sistema\Administrador\Usuarios\Reportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\usuusuarios;
use App\ussusuariossucursales;

class GenerarExcelUsuariosController extends Controller
{
    public function GenerarExcelUsuario (Request $request) 
    {      

        $respuesta = false;
        $mensaje = "";
        $arrayUsuarios = [];

        $usu = usuusuarios::join('perpersonas as per', 'per.perid', 'usuusuarios.perid')
                        ->join('tputiposusuarios as tpu', 'tpu.tpuid', 'usuusuarios.tpuid')
                        ->orderBy('usuusuarios.usuid', 'DESC')
                        ->where('usuusuarios.usuid', '!=', 1)
                        ->where('usuusuarios.tpuid', '!=', 1)
                        ->whereNotNull('usuusuario')
                        ->where('usuusuario','not like','%prueba%')
                        ->where('usuusuario','not like','%grow%')
                        ->where('usuusuario','not like','%eunice%')
                        ->where('usuusuario','not like','%gerson%')
                        ->where('usuusuario','not like','%usuario%')
                        ->get([
                            'usuusuarios.usuid',
                            'per.pernombrecompleto',
                            'tpu.tpunombre',
                            'usuusuarios.usuusuario'
                        ]);

        if($usu){
            foreach ($usu as $key => $usuario) {
                $uss = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                                ->where('ussusuariossucursales.usuid', $usuario->usuid)
                                                ->get(['ussusuariossucursales.ussid','suc.sucnombre']);

                if ($uss) {
                    $sucursalesUsuario = "";
                    foreach ($uss as $sucursal) {
                        $sucursalesUsuario .= $sucursal['sucnombre'].",";
                    }
                    // $usuario['distribuidoras'] = $sucursalesUsuario;
                    $arrayUsuarios[$key][0]['value'] = $usuario->pernombrecompleto;
                    $arrayUsuarios[$key][0]['style'] = array("fill" => array("fgColor" => array("rgb" => "FFDAEEF3")));
                    $arrayUsuarios[$key][1]['value'] = $usuario->tpunombre;
                    $arrayUsuarios[$key][1]['style'] = array("fill" => array("fgColor" => array("rgb" => "FFDAEEF3")));
                    $arrayUsuarios[$key][2]['value'] = $usuario->usuusuario;
                    $arrayUsuarios[$key][2]['style'] = array("fill" => array("fgColor" => array("rgb" => "FFDAEEF3")));
                    $arrayUsuarios[$key][3]['value'] = $sucursalesUsuario;
                    $arrayUsuarios[$key][3]['style'] = array("fill" => array("fgColor" => array("rgb" => "FFDAEEF3")));
                }   
            }

            $datos = [array(
                "columns" => [
                    [ 
                        "title" => "Nombres y Apellidos", 
                        "width" => array("wpx" => "223"),
                        "style" => array(
                            "font" => array("sz" => "12", "bold" => true, "color" => array("rgb" => "FFFFFFFF")),
                            "fill" => array("fgColor" => array("rgb" => "FF366092"))
                        )
                    ],
                    [ 
                        "title" => "Tipo Usuario", 
                        "width" => array("wpx"=>"193"),
                        "style" => array(
                            "font" => array("sz" => "12", "bold" => true, "color" => array("rgb" => "FFFFFFFF")),
                            "fill" => array("fgColor" => array("rgb" => "FF366092"))
                        )
                    ],
                    [ 
                        "title" => "Usuario", "width" => array("wpx"=>"322"),
                        "style" => array(
                            "font" => array("sz" => "12", "bold" => true, "color" => array("rgb" => "FFFFFFFF")),
                            "fill" => array("fgColor" => array("rgb" => "FF366092"))
                        )
                    ],
                    [ 
                        "title" => "Distribuidoras", "width" => array("wpx"=>"269"),
                        "style" => array(
                            "font" => array("sz" => "12", "bold" => true, "color" => array("rgb" => "FFFFFFFF")),
                            "fill" => array("fgColor" => array("rgb" => "FF366092"))
                        )
                    ]
                ],
                "data" => $arrayUsuarios
            )];

            $respuesta = true;
            $mensaje = "Se retorno los datos de usuarios con exito";
        }else{
            $respuesta = false;
            $mensaje = "Error al obtener los datos de usuarios";
        }
        
        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,    
            "datos"     => $datos
        ]);

        return $requestsalida;        
    }
}
