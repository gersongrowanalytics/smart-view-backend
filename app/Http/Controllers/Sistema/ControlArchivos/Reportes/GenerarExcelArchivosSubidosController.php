<?php

namespace App\Http\Controllers\Sistema\ControlArchivos\Reportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\carcargasarchivos;

class GenerarExcelArchivosSubidosController extends Controller
{
    public function GenerarExcelArchivosSubidos () 
    {
        $respuesta = false;
        $mensaje = "";
        $arrayArchivosSubidos = [];

        $car = carcargasarchivos::join('usuusuarios as usu', 'usu.usuid', 'carcargasarchivos.usuid')
                        ->join('tputiposusuarios as tpu', 'tpu.tpuid', 'usu.tpuid')
                        ->join('tcatiposcargasarchivos as tca', 'tca.tcaid', 'carcargasarchivos.tcaid')
                        ->join('perpersonas as per', 'per.perid', 'usu.perid')
                        // ->join('paupaisesusuarios as pau', 'pau.usuid', 'usu.usuid')
                        ->orderBy('usu.usuid', 'DESC')
                        ->where('usu.usuid', '!=', 1)
                        ->where('usu.tpuid', '!=', 1)
                        ->whereNotNull('usu.usuusuario')
                        ->where('usu.usuusuario','not like','%prueba%')
                        ->where('usu.usuusuario','not like','%grow%')
                        ->where('usu.usuusuario','not like','%eunice%')
                        ->where('usu.usuusuario','not like','%gerson%')
                        ->where('usu.usuusuario','not like','%usuario%')
                        ->get([
                            'carcargasarchivos.carid',
                            'carcargasarchivos.carnombrearchivo',
                            'carcargasarchivos.carurl',
                            'tca.tcanombre',
                            'per.pernombrecompleto',
                            'usu.usuusuario',
                            'carcargasarchivos.created_at'
                        ]);

        // dd($car[0]);

        if($car){
            foreach ($car as $key => $archivo) {
                // $uss = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                //                                 ->where('ussusuariossucursales.usuid', $usuario->usuid)
                //                                 ->get(['ussusuariossucursales.ussid','suc.sucnombre']);

                // if ($uss) {
                //     $paisesUsuario = "";
                //     foreach ($uss as $sucursal) {
                //         $paisesUsuario .= $sucursal['sucnombre'].",";
                //     }
                    
                    $arrayArchivosSubidos[$key][0]['value'] = $archivo->carnombrearchivo;
                    $arrayArchivosSubidos[$key][0]['style'] = array("fill" => array("fgColor" => array("rgb" => "FFDAEEF3")));
                    $arrayArchivosSubidos[$key][1]['value'] = $archivo->carurl;
                    $arrayArchivosSubidos[$key][1]['style'] = array("fill" => array("fgColor" => array("rgb" => "FFDAEEF3")));
                    $arrayArchivosSubidos[$key][2]['value'] = $archivo->tcanombre;
                    $arrayArchivosSubidos[$key][2]['style'] = array("fill" => array("fgColor" => array("rgb" => "FFDAEEF3")));
                    $arrayArchivosSubidos[$key][3]['value'] = $archivo->pernombrecompleto;
                    $arrayArchivosSubidos[$key][3]['style'] = array("fill" => array("fgColor" => array("rgb" => "FFDAEEF3")));
                    $arrayArchivosSubidos[$key][4]['value'] = $archivo->usuusuario;
                    $arrayArchivosSubidos[$key][4]['style'] = array("fill" => array("fgColor" => array("rgb" => "FFDAEEF3")));
                    $arrayArchivosSubidos[$key][5]['value'] = $archivo->created_at;
                    $arrayArchivosSubidos[$key][5]['style'] = array("fill" => array("fgColor" => array("rgb" => "FFDAEEF3")));
                    // $arrayArchivosSubidos[$key][3]['value'] = $paisesUsuario;
                    // $arrayArchivosSubidos[$key][3]['style'] = array("fill" => array("fgColor" => array("rgb" => "FFDAEEF3")));
                // }   
            }

            $datos = [array(
                "columns" => [
                    [ 
                        "title" => "Nombre del archivo", 
                        "width" => array("wpx" => "223"),
                        "style" => array(
                            "font" => array("sz" => "12", "bold" => true, "color" => array("rgb" => "FFFFFFFF")),
                            "fill" => array("fgColor" => array("rgb" => "FF366092"))
                        )
                    ],
                    [ 
                        "title" => "URL del archivo", 
                        "width" => array("wpx"=>"193"),
                        "style" => array(
                            "font" => array("sz" => "12", "bold" => true, "color" => array("rgb" => "FFFFFFFF")),
                            "fill" => array("fgColor" => array("rgb" => "FF366092"))
                        )
                    ],
                    [ 
                        "title" => "Tipo de carga del archivo", 
                        "width" => array("wpx"=>"322"),
                        "style" => array(
                            "font" => array("sz" => "12", "bold" => true, "color" => array("rgb" => "FFFFFFFF")),
                            "fill" => array("fgColor" => array("rgb" => "FF366092"))
                        )
                    ],
                    [ 
                        "title" => "Apellidos y nombres del usuario", 
                        "width" => array("wpx"=>"269"),
                        "style" => array(
                            "font" => array("sz" => "12", "bold" => true, "color" => array("rgb" => "FFFFFFFF")),
                            "fill" => array("fgColor" => array("rgb" => "FF366092"))
                        )
                    ],
                    [ 
                        "title" => "Usuario", 
                        "width" => array("wpx"=>"269"),
                        "style" => array(
                            "font" => array("sz" => "12", "bold" => true, "color" => array("rgb" => "FFFFFFFF")),
                            "fill" => array("fgColor" => array("rgb" => "FF366092"))
                        )
                    ],
                    [ 
                        "title" => "Fecha de creación", 
                        "width" => array("wpx"=>"269"),
                        "style" => array(
                            "font" => array("sz" => "12", "bold" => true, "color" => array("rgb" => "FFFFFFFF")),
                            "fill" => array("fgColor" => array("rgb" => "FF366092"))
                        )
                    ]
                ],
                "data" => $arrayArchivosSubidos
            )];

            $respuesta = true;
            $mensaje = "Se retorno los datos de la carga de archivos con exito";
        }else{
            $respuesta = false;
            $mensaje = "Error al obtener los datos de carga de archivos";
        }
        
        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,    
            "datos"     => $datos
        ]);

        return $requestsalida;        
    }
}
