<html>

<head>
    {{-- <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"> --}}
    <link href="http://fonts.cdnfonts.com/css/segoe-ui-4" rel="stylesheet">
</head>

<body>
    <style type="text/css">
        /* @import url(https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap); */
         @import url('http://fonts.cdnfonts.com/css/segoe-ui-4');
    table tr td 
    { 
        vertical-align: middle;
    }
    </style>

        <table style="width: 100%; background: #F1F7FF">
            <tr>
                <td align="center">
                    <div style="width: 1050px; background: #F1F7FF">
                        <br>
                        <div style="font-style: normal; font-weight: 500; font-size: 11px; letter-spacing: -0.051em; color: #1E1E1E; margin-bottom: 2px">
                            Equipo de Creciendo Juntos,
                        </div>
                        <div style="font-style: normal; font-weight: 500; font-size: 11px; letter-spacing: -0.051em; color: #1E1E1E; margin-bottom: 11px">
                            Se les está compartiendo un update de información que ha sido actualizado al {{$fechas}}; con ello, se les está brindando visibilidad de los <span style="font-weight: 700"> pendientes correspondientes a cada área/usuario:</span>
                        </div>
                        <div style="font-style: normal; font-weight: 700; font-size: 11px; letter-spacing: -0.051em; color: #3646C3">
                            Nota: Ya estamos día 20 y está pendiente cargar los objetivos
                        </div>
                        <br>
                        <table style="margin-bottom: 15px; width: 950px;">
                            <tr style="width: 950px">
                                @foreach ($cuadros as $cuadro)
                                    @if ($cuadro['arenombre'] == 'Trade Marketing')
                                        <td style="padding-right: 10px">
                                            <table style="background: #1E1E1E; width: 100%">
                                                <tr style="height: 50px;">
                                                    <td align="left" style="font-style: normal; font-weight: 700; font-size: 14px; line-height: 19px; letter-spacing: -0.015em; color: #FFFFFF; padding-left: 27px">
                                                        {{$cuadro['arenombre']}}
                                                    </td>
                                                    <td style="font-style: normal; font-weight: 700; font-size: 20px; line-height: 27px; letter-spacing: -0.015em; color: #FFFF00; padding-left: 10px">
                                                        {{$cuadro['areporcentaje']}}%
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    @endif
                                    @if ($cuadro['arenombre'] == 'Data Analytics')
                                        <td style="padding-right: 10px">
                                            <table style="background: #1E1E1E; width: 100%">
                                                <tr style="height: 50px;">
                                                    <td align="left" style="font-style: normal; font-weight: 700; font-size: 14px; line-height: 19px; letter-spacing: -0.015em; color: #FFFFFF; padding-left: 27px">
                                                        {{$cuadro['arenombre']}}
                                                    </td>
                                                    <td style="font-style: normal; font-weight: 700; font-size: 20px; line-height: 27px; letter-spacing: -0.015em; color: #FFFF00; padding-left: 10px">
                                                        {{$cuadro['areporcentaje']}}%
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    @endif
                                    @if ($cuadro['arenombre'] == 'Support Grow')
                                        <td style="padding-right: 0px">
                                            <table style="background: #1E1E1E; width: 100%">
                                                <tr style="height: 50px;">
                                                    <td align="left" style="font-style: normal; font-weight: 700; font-size: 14px; line-height: 19px; letter-spacing: -0.015em; color: #FFFFFF; padding-left: 27px">
                                                        {{$cuadro['arenombre']}}
                                                    </td>
                                                    <td style="font-style: normal; font-weight: 700; font-size: 20px; line-height: 27px; letter-spacing: -0.015em; color: #FFFF00; padding-left: 10px">
                                                        {{$cuadro['areporcentaje']}}%
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    @endif
                                @endforeach
                            </tr>
                        </table>
                        <br>
                        <table border="0" cellspacing="0" cellpadding="0" style="padding-left: 30px; padding-right: 30px; width: 950px; padding-top: 15px; border-collapse: collapse; margin-bottom: 20px;">
                            <tr style="color: #1E1E1E; height: 38px; padding: 0px; margin: 0px">
                                <td align="center" style=" color: #1E1E1E; background: #FFFF00; font-weight: 700; font-size: 14px; line-height: 16px; font-style: normal">Área</td>
                                <td align="left" style=" padding-left: 10px; color: #1E1E1E; background: #FFFF00; font-weight: 700; font-size: 14px; line-height: 16px; font-style: normal">Base de datos</td>
                                <td align="center" style="color: #1E1E1E; background: #FFFF00;; font-weight: 700; font-size: 14px; line-height: 16px; font-style: normal">Responsable</td>
                                <td align="center" style="color: #1E1E1E; background: #FFFF00; font-weight: 700; font-size: 14px; line-height: 16px; font-style: normal">Usuario</td>
                                <td align="center" style="color: #1E1E1E; background: #FFFF00; font-weight: 700; font-size: 14px; line-height: 16px; font-style: normal">DeadLine</td>
                                <td align="center" style="color: #1E1E1E; background: #FFFF00; font-weight: 700; font-size: 14px; line-height: 16px; font-style: normal">Fecha de Carga</td>
                                <td align="center" style="color: #1E1E1E; background: #FFFF00; font-weight: 700; font-size: 14px; line-height: 16px; font-style: normal">Días de Retraso</td>
                                <td align="center" style="color: #1E1E1E; background: #FFFF00; font-weight: 700; font-size: 14px; line-height: 16px; font-style: normal">Status</td>
                            </tr>
                            @foreach ($datos as $dato)
                                @if ($dato['base_datos'] == 'Sell In Objetivo')
                                    <tr style="padding: 0px; margin: 0px">
                                        <td rowspan="9" align="center" style="border-top: 1px solid; border-left: 1px solid; border-right: 1px solid; color: #1E1E1E; background: #FFFFFF; border-color: #E5E5E5; font-weight: 700; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['area']}} </td>
                                        <td align="left" style=" border-top: 1px solid; padding-left: 10px; border-color: #E5E5E5 ; color: black; background: #FFFFFF; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['base_datos']}} </td>
                                        <td align="left" style=" border-top: 1px solid; border-color: #E5E5E5 ; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['responsable']}} </td>
                                        <td align="left" style=" border-top: 1px solid; border-color: #E5E5E5 ; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['usuario']}} </td>
                                        <td align="center" style=" border-top: 1px solid; border-color: #E5E5E5 ; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['deadline']}} </td>
                                        <td align="center" style=" border-top: 1px solid; border-color: #E5E5E5 ; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['fecha_carga']}} </td>
                                        <td align="center" style=" border-top: 1px solid; border-color: #E5E5E5 ; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> 
                                            <table>
                                                <tr>
                                                    <td style="background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal">{{$dato['dias_retraso']}} días </td>
                                                    @if ($dato['dias_retraso'] == '0')
                                                        <td><div style="width: 10px; height: 10px; border-radius: 8px; background: #2FDA36"></div></td>
                                                    @else
                                                        <td><div style="width: 10px; height: 10px; border-radius: 8px; background: red"></div></td>
                                                    @endif
                                                </tr>
                                            </table>
                                        </td>
                                        @if ($dato['status'] == 'Cargado')
                                            <td align="left" style="border-top: 1px solid; border-color: #E5E5E5; background: #FFFFFF; color: #2FDA36; font-weight: bold; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['status']}} </td>
                                        @else
                                            <td align="left" style="border-top: 1px solid; border-color: #E5E5E5; background: #FFFFFF; color: red; font-weight: bold; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['status']}} </td>
                                        @endif
                                    </tr>
                                @endif
                                @if ( $dato['base_datos'] != 'Sell In Objetivo' && $dato['areaid'] == '1')
                                    <tr style="padding: 0px; margin: 0px">
                                        <td align="left" style="border: 1px solid; padding-left: 10px; border-color: #FFFFFF ; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['base_datos']}} </td>
                                        <td align="left" style="border: 1px solid; border-color: #FFFFFF ; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['responsable']}} </td>
                                        <td align="left" style="border: 1px solid; border-color: #FFFFFF ; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['usuario']}} </td>
                                        <td align="center" style="border: 1px solid; border-color: #FFFFFF ; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['deadline']}} </td>
                                        <td align="center" style="border: 1px solid; border-color: #FFFFFF ; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['fecha_carga']}} </td>
                                        <td align="center" style="border: 1px solid; border-color: #FFFFFF ; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> 
                                            <table>
                                                <tr>
                                                    <td style="background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal">{{$dato['dias_retraso']}} días </td>
                                                    @if ($dato['dias_retraso'] == '0')
                                                        <td><div style="width: 10px; height: 10px; border-radius: 8px; background: #2FDA36"></div></td>
                                                    @else
                                                        <td><div style="width: 10px; height: 10px; border-radius: 8px; background: red"></div></td>
                                                    @endif
                                                </tr>
                                            </table>
                                        </td>
                                        @if ($dato['status'] == 'Cargado')
                                            <td align="left" style="background: #FFFFFF; color: #2FDA36; font-weight: bold; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['status']}} </td>
                                        @else
                                            <td align="left" style="background: #FFFFFF; color: red; font-weight: bold; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['status']}} </td>
                                        @endif
                                    </tr>
                                @endif
                                @if ($dato['base_datos'] == 'Sell In Real')
                                    <tr style="padding: 0px; margin: 0px; border-top: 1.5px solid; border-color: #E5E5E5;">
                                        <td rowspan="4" align="center" style="border-top: 1px solid; border-left: 1px solid; border-right: 1px solid; color: #1E1E1E; background: #FFFFFF; border-color: #E5E5E5; font-weight: 700; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['area']}} </td>
                                        <td align="left" style="border-top: 1px solid; padding-left: 10px; border-color: #E5E5E5; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['base_datos']}} </td>
                                        <td align="left" style="border-top: 1px solid; border-color: #E5E5E5 ; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['responsable']}} </td>
                                        <td align="left" style="border-top: 1px solid; border-color: #E5E5E5 ; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['usuario']}} </td>
                                        <td align="center" style="border-top: 1px solid; border-color: #E5E5E5 ; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['deadline']}} </td>
                                        <td align="center" style="border-top: 1px solid; border-color: #E5E5E5 ; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['fecha_carga']}} </td>
                                        <td align="center" style="border-top: 1px solid; border-color: #E5E5E5 ; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal">
                                            <table>
                                                <tr>
                                                    <td style="background: #FFFFFF ; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal" >{{$dato['dias_retraso']}} días </td>
                                                    @if ($dato['dias_retraso'] == '0')
                                                        <td><div style="width: 10px; height: 10px; border-radius: 8px; background: #2FDA36"></div></td>
                                                    @else
                                                        <td><div style="width: 10px; height: 10px; border-radius: 8px; background: red"></div></td>
                                                    @endif
                                                </tr>
                                            </table>
                                        </td>
                                        @if ($dato['status'] == 'Cargado')
                                            <td align="left" style="border-top: 1px solid; border-color: #E5E5E5 ; background: #FFFFFF; color: #2FDA36; font-weight: bold; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['status']}} </td>
                                        @else
                                            <td align="left" style="border-top: 1px solid; border-color: #E5E5E5 ; background: #FFFFFF; color: red; font-weight: bold; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['status']}} </td>
                                        @endif
                                    </tr>
                                @endif
                                @if ($dato['base_datos'] != 'Sell In Real' && $dato['areaid'] == '2')
                                    <tr style="color: #EDF0FA; padding: 0px; margin: 0px">
                                        <td align="left" style="border: 1px solid; padding-left: 10px; border-color: #FFFFFF ; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['base_datos']}} </td>
                                        <td align="left" style="border: 1px solid; border-color: #FFFFFF; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['responsable']}} </td>
                                        <td align="left" style="border: 1px solid; border-color: #FFFFFF; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['usuario']}} </td>
                                        <td align="center" style="border: 1px solid; border-color: #FFFFFF; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['deadline']}} </td>
                                        <td align="center" style="border: 1px solid; border-color: #FFFFFF; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['fecha_carga']}} </td>
                                        <td align="center" style="border: 1px solid; border-color: #FFFFFF; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal">
                                            <table>
                                                <tr>
                                                    <td style="background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal" >{{$dato['dias_retraso']}} días </td>
                                                    @if ($dato['dias_retraso'] == '0')
                                                        <td><div style="width: 10px; height: 10px; border-radius: 8px; background: #2FDA36"></div></td>
                                                    @else
                                                        <td><div style="width: 10px; height: 10px; border-radius: 8px; background: red"></div></td>
                                                    @endif
                                                </tr>
                                            </table> 
                                        </td>
                                        @if ($dato['status'] == 'Cargado')
                                            <td align="left" style="background: #FFFFFF; color: #2FDA36; font-weight: bold; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['status']}} </td>
                                        @else
                                            <td align="left" style="background: #FFFFFF; color: red; font-weight: bold; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['status']}} </td>
                                        @endif
                                    </tr>
                                @endif
                                @if ($dato['base_datos'] == 'Sell Out Real DT')
                                    <tr style="padding: 0px; margin: 0px; border-top: 1.5px solid; border-color: #E5E5E5;">
                                        <td rowspan="3" align="center" style="border-top: 1px solid; border-left: 1px solid; border-right: 1px solid; color: #1E1E1E; background: #FFFFFF; border-color: #E5E5E5; font-weight: 700; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['area']}} </td>
                                        <td align="left" style="border-top: 1px solid; padding-left: 10px; border-color: #E5E5E5; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['base_datos']}} </td>
                                        <td align="left" style="border-top: 1px solid; border-color: #E5E5E5; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['responsable']}} </td>
                                        <td align="left" style="border-top: 1px solid; border-color: #E5E5E5; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['usuario']}} </td>
                                        <td align="center" style="border-top: 1px solid; border-color: #E5E5E5; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['deadline']}} </td>
                                        <td align="center" style="border-top: 1px solid; border-color: #E5E5E5; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['fecha_carga']}} </td>
                                        <td align="center" style="border-top: 1px solid; border-color: #E5E5E5; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> 
                                            <table>
                                                <tr>
                                                    <td style="background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal" >{{$dato['dias_retraso']}} días </td>
                                                    @if ($dato['dias_retraso'] == '0')
                                                        <td><div style="width: 10px; height: 10px; border-radius: 8px; background: #2FDA36"></div></td>
                                                    @else
                                                        <td><div style="width: 10px; height: 10px; border-radius: 8px; background: red"></div></td>
                                                    @endif
                                                </tr>
                                            </table>
                                        </td>
                                        @if ($dato['status'] == 'Cargado')
                                            <td align="left" style="border-top: 1px solid; border-color: #E5E5E5 ; background: #FFFFFF; color: #2FDA36; font-weight: bold; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['status']}} </td>
                                        @else
                                            <td align="left" style="border-top: 1px solid; border-color: #E5E5E5 ; background: #FFFFFF; color: red; font-weight: bold; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['status']}} </td>
                                        @endif
                                    </tr>
                                @endif
                                @if ($dato['base_datos'] != 'Sell Out Real DT' && $dato['areaid'] == '3')
                                    <tr style="color:#EDF0FA; padding: 0px; margin: 0px">
                                        <td align="left" style="border: 1px solid; padding-left: 10px; border-color: #FFFFFF; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['base_datos']}} </td>
                                        <td align="left" style="border: 1px solid; border-color: #FFFFFF; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['responsable']}} </td>
                                        <td align="left" style="border: 1px solid; border-color: #FFFFFF; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['usuario']}} </td>
                                        <td align="center" style="border: 1px solid; border-color: #FFFFFF; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['deadline']}} </td>
                                        <td align="center" style="border: 1px solid; border-color: #FFFFFF; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['fecha_carga']}} </td>
                                        <td align="center" style="border: 1px solid; border-color: #FFFFFF; background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> 
                                            <table>
                                                <tr>
                                                    <td style="background: #FFFFFF; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal" >{{$dato['dias_retraso']}} días </td>
                                                    @if ($dato['dias_retraso'] == '0')
                                                        <td><div style="width: 10px; height: 10px; border-radius: 8px; background: #2FDA36"></div></td>
                                                    @else
                                                        <td><div style="width: 10px; height: 10px; border-radius: 8px; background: red"></div></td>
                                                    @endif
                                                </tr>
                                            </table>
                                        </td>
                                        @if ($dato['status'] == 'Cargado')
                                            <td align="left" style="background: #FFFFFF; color: #2FDA36; font-weight: bold; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['status']}} </td>
                                        @else
                                            <td align="left" style="background: #FFFFFF; color: red; font-weight: bold; font-size: 12px; line-height: 16px; font-style: normal; padding-left: 10px;"> {{$dato['status']}} </td>
                                        @endif
                                    </tr>
                                @endif
                            @endforeach
                        </table>
                        <br>
                        <div style="font-style: normal; font-weight: 500; font-size: 11px; letter-spacing: -0.051em; color: #1E1E1E; margin-bottom: 2px">
                            Les reiteramos nuestro apoyo en este proceso, y recordamos que, ante cualquier consulta o requerimiento, durante esta etapa, podrá ser
                        </div>
                        <div style="font-style: normal; font-weight: 500; font-size: 11px; letter-spacing: -0.051em; color: #1E1E1E; margin-bottom: 2px">
                            canalizado al correo soporte@grow-analytics.com.pe .
                        </div>
                        <div style="font-style: normal; font-weight: 500; font-size: 11px; letter-spacing: -0.051em; color: #1E1E1E; margin-bottom: 2px">
                            ¡Que tengan un excelente día!   
                        </div>
                        <div style="font-style: normal; font-weight: 700; font-size: 11px; letter-spacing: -0.051em; color: #1E1E1E; margin-bottom: 2px">
                            Atte. Customer Services Prime </br>
                        </div>
                        <div style="font-style: normal; font-weight: 700; font-size: 11px; letter-spacing: -0.051em; color: #1E1E1E; margin-bottom: 2px">
                            (Información confidencial, prohibida su divulgación de acuerdo a la Ley Peruana)
                        </div>
                        <br>
                    </div>
                </td>
            </tr>
        </table>
</body>

</html>
