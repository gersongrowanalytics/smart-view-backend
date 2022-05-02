<?php

namespace App\Http\Controllers\Sistema\Administrador\Usuarios\Reportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\audauditorias;

class GenerarExcelAuditoriaLoginController extends Controller
{
    public function GenerarReporteAuditoriaLogin(Request $request)
    {
        $respuesta      = false;
        $mensaje        = "";
        $arrayAuditoria = [];

        $re_fechaInicio = $request['re_fechaInicio'];
        $re_fechaFinal  = $request['re_fechaFinal'];
        // $re_fechaInicio = "2021-07-01";
        // $re_fechaFinal  = "2021-07-30";

        $aud = audauditorias::join('usuusuarios as usu', 'usu.usuid','audauditorias.usuid')
                        ->join('perpersonas as per', 'per.perid', 'usu.perid')
                        ->join('tputiposusuarios as tpu', 'tpu.tpuid', 'usu.tpuid')
                        ->orderBy('audauditorias.audid', 'DESC')
                        ->where('audauditorias.audaccion', "LOGIN")
                        ->where('usu.usuid', '!=', 1)
                        ->where('usu.tpuid', '!=', 1)
                        ->whereBetween('audauditorias.created_at', [$re_fechaInicio, $re_fechaFinal])
                        ->get([
                            'per.pernombrecompleto',
                            'usu.usuusuario',
                            'tpu.tpunombre',
                            'audauditorias.created_at'
                        ]);
        if($aud){
            foreach ($aud as $key =>$auditoria) {
                $arrayAuditoria[$key][0]['value'] = $auditoria->pernombrecompleto;
                $arrayAuditoria[$key][0]['style'] = array("fill" => array("fgColor" => array("rgb" => "FFDAEEF3")));
                $arrayAuditoria[$key][1]['value'] = $auditoria->usuusuario;
                $arrayAuditoria[$key][1]['style'] = array("fill" => array("fgColor" => array("rgb" => "FFDAEEF3")));
                $arrayAuditoria[$key][2]['value'] = $auditoria->tpunombre;
                $arrayAuditoria[$key][2]['style'] = array("fill" => array("fgColor" => array("rgb" => "FFDAEEF3")));
                $arrayAuditoria[$key][3]['value'] = $auditoria->created_at;
                $arrayAuditoria[$key][3]['style'] = array("fill" => array("fgColor" => array("rgb" => "FFDAEEF3")));
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
                        "title" => "Usuario",
                        "width" => array("wpx" => "322"),
                        "style" => array(
                            "font" => array("sz" => "12", "bold" => true, "color" => array("rgb" => "FFFFFFFF")),
                            "fill" => array("fgColor" => array("rgb" => "FF366092"))
                        )
                    ],
                    [ 
                        "title" => "Tipo de Usuario",
                        "width" => array("wpx" => "193"),
                        "style" => array(
                            "font" => array("sz" => "12", "bold" => true, "color" => array("rgb" => "FFFFFFFF")),
                            "fill" => array("fgColor" => array("rgb" => "FF366092"))
                        )
                    ],
                    [ 
                        "title" => "Fecha de Creación",
                        "width" => array("wpx" => "150"),
                        "style" => array(
                            "font" => array("sz" => "12", "bold" => true, "color" => array("rgb" => "FFFFFFFF")),
                            "fill" => array("fgColor" => array("rgb" => "FF366092"))
                        )
                    ]
                ],
                "data" => $arrayAuditoria
            )];
            $respuesta = true;
            $mensaje = "Se retorno los datos de auditoria del login con exito";
        }else{
            $respuesta = false;
            $mensaje = "Error al obtener los datos de auditoria de login";
        }
        
        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            "datos"     => $datos
        ]);

        return $requestsalida;    
    }
}
