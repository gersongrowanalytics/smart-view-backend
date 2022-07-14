<html>

<head>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <style type="text/css">
        @import url(https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap);
    table tr td 
    { 
        vertical-align: middle;
    }
    </style>

    <div style="width: 100%;">
        <center>
            <div style="width: 1050px;">
                <div style="background: #FFFFFF; margin-top:30px; height: 40px; width: 1050px; padding-top: 0px; position:relative;">
                    <div style="font-style: normal; font-weight: 500; font-size: 14px; line-height: 172.51%; letter-spacing: -0.051em; color: #1E1E1E">
                        Equipo de Creciendo Juntos,
                    </div>
                    <div style="font-style: normal; font-weight: 500; font-size: 14px; line-height: 172.51%; letter-spacing: -0.051em; color: #1E1E1E">
                        Se les está compartiendo un update de información que ha sido actualizado al {{$fechas}}; con ello, se les está brindando visibilidad de los <span style="font-weight: 700"> pendientes </span>
                    </div>
                    <div style="font-style: normal; font-weight: 700; font-size: 14px; line-height: 172.51%; letter-spacing: -0.051em; color: #1E1E1E">
                        correspondientes a cada área/usuario:
                    </div>
                </div>
                <table style="margin-bottom: 15px; margin-top: 50px">
                    <tr>
                        @foreach ($cuadros as $cuadro)
                            @if ($cuadro['arenombre'] == 'Trade Marketing')
                                <td style="padding-right: 52px">
                                    <table style="border: 2px solid; border-color:#FFFF00; border-radius: 8px; width: 166px; padding-left: 18px">
                                        <tr style="height: 50px;">
                                            <td align="center" style="width: 50%;font-style: normal; font-weight: 700; font-size: 14px; line-height: 19px; letter-spacing: -0.015em;">
                                                {{$cuadro['arenombre']}}
                                            </td>
                                            <td style="font-style: normal; font-weight: 700; font-size: 20px; line-height: 27px; letter-spacing: -0.015em; color: #000000">{{$cuadro['areporcentaje']}}</td>
                                        </tr>
                                    </table>
                                </td>
                            @endif
                            @if ($cuadro['arenombre'] == 'Data Analytics')
                                <td style="padding-right: 52px">
                                    <table style="border: 2px solid; border-color:#62F5A9; border-radius: 8px; width: 166px; padding-left: 18px">
                                        <tr style="height: 50px;">
                                            <td align="center" style="width: 50%;font-style: normal; font-weight: 700; font-size: 14px; line-height: 19px; letter-spacing: -0.015em;">
                                                {{$cuadro['arenombre']}}
                                            </td>
                                            <td style="font-style: normal; font-weight: 700; font-size: 20px; line-height: 27px; letter-spacing: -0.015em; color: #000000">{{$cuadro['areporcentaje']}}</td>
                                        </tr>
                                    </table>
                                </td>
                            @endif
                            @if ($cuadro['arenombre'] == 'Support Grow')
                                <td>
                                    <table style="border: 2px solid; border-color:#A6FFFF; border-radius: 8px; width: 166px; padding-left: 18px">
                                        <tr style="height: 50px;">
                                            <td align="center" style="width: 50%; font-style: normal; font-weight: 700; font-size: 14px; line-height: 19px; letter-spacing: -0.015em;">
                                                {{$cuadro['arenombre']}}
                                            </td>
                                            <td style="font-style: normal; font-weight: 700; font-size: 20px; line-height: 27px; letter-spacing: -0.015em; color: #000000">{{$cuadro['areporcentaje']}}</td>
                                        </tr>
                                    </table>
                                </td>
                            @endif
                        @endforeach
                    </tr>
                </table>
                <br>
                <table style="padding-left: 30px; padding-right: 30px; width: 950px; padding-top: 15px; border-collapse: collapse; margin-bottom: 20px;">
                    <tr style="color: #EDF0FA; height: 38px; padding: 0px; margin: 0px">
                        <td align="center" style=" color: white; background: #1EC0ED; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal; border-top-left-radius: 8px">Área</td>
                        <td align="left" style=" padding-left: 10px; color: white; background: #1EC0ED; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal">Base de datos</td>
                        <td align="center" style="color: white; background: #1EC0ED; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal">Responsable</td>
                        <td align="center" style="color: white; background: #1EC0ED; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal">Usuario</td>
                        <td align="center" style="color: white; background: #1EC0ED; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal">DeadLine</td>
                        <td align="center" style="color: white; background: #1EC0ED; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal">Fecha de Carga</td>
                        <td align="center" style="color: white; background: #1EC0ED; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal">Días de Retraso</td>
                        <td align="center" style="color: white; background: #1EC0ED; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal; border-top-right-radius: 8px">Status</td>
                    </tr>
                    @foreach ($datos as $dato)
                        @if ($dato['base_datos'] == 'Sell In Objetivo')
                            <tr style="color: #EDF0FA; padding: 0px; margin: 0px">
                                <td rowspan="11" align="center" style="border: 1px solid; color: black; background: white; border-color: #E5E5E5; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['area']}} </td>
                                <td align="left" style="vertical-align: middle; height: 27px ;border: 1px solid; padding-left: 10px; border-color: #E5E5E5 ; color: black; background: #EDF0FA; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['base_datos']}} </td>
                                <td align="center" style="vertical-align: middle;height: 27px ;border: 1px solid; border-color: #E5E5E5 ; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['responsable']}} </td>
                                <td align="center" style="vertical-align: middle;height: 27px ;border: 1px solid; border-color: #E5E5E5 ; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['usuario']}} </td>
                                <td align="center" style="vertical-align: middle;height: 27px ;border: 1px solid; border-color: #E5E5E5 ; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['deadline']}} </td>
                                <td align="center" style="vertical-align: middle;height: 27px ;border: 1px solid; border-color: #E5E5E5 ; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['fecha_carga']}} </td>
                                <td align="center" style="vertical-align: middle;height: 27px ;border: 1px solid; border-color: #E5E5E5 ; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> 
                                    <table>
                                        <tr>
                                            <td>{{$dato['dias_retraso']}} días </td>
                                            <td><div style="width: 10px; height: 10px; border-radius: 8px; background: red"></div></td>
                                        </tr>
                                    </table>
                                </td>
                                @if ($dato['status'] == 'Cargado')
                                    <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #EDF0FA; color: #2FDA36; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['status']}} </td>
                                @else
                                    <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #EDF0FA; color: red; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['status']}} </td>
                                @endif
                                
                            </tr>
                        @endif
                        @if ($dato['areaid'] == '1')
                            <tr style="color: #EDF0FA; padding: 0px; margin: 0px">
                                <td align="left" style="border: 1px solid; padding-left: 10px; border-color: #E5E5E5 ; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['base_datos']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['responsable']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['usuario']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['deadline']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['fecha_carga']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> 
                                    <table>
                                        <tr>
                                            <td>{{$dato['dias_retraso']}} días </td>
                                            <td><div style="width: 10px; height: 10px; border-radius: 8px; background: red"></div></td>
                                        </tr>
                                    </table>
                                </td>
                                @if ($dato['status'] == 'Cargado')
                                    <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #EDF0FA; color: #2FDA36; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['status']}} </td>
                                @else
                                    <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #EDF0FA; color: red; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['status']}} </td>
                                @endif
                            </tr>
                        @endif
                        @if ($dato['base_datos'] == 'Sell In Real')
                            <tr style="color: #EDF0FA; padding: 0px; margin: 0px">
                                <td rowspan="5" align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #ffffff; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['area']}} </td>
                                <td align="left" style="border: 1px solid; padding-left: 10px; border-color: #E5E5E5; background: #ffffff; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['base_datos']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #ffffff; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['responsable']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #ffffff; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['usuario']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #ffffff; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['deadline']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #ffffff; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['fecha_carga']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #ffffff; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal">
                                    <table>
                                        <tr>
                                            <td>{{$dato['dias_retraso']}} días </td>
                                            <td><div style="width: 10px; height: 10px; border-radius: 8px; background: red"></div></td>
                                        </tr>
                                    </table>
                                </td>
                                @if ($dato['status'] == 'Cargado')
                                    <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #ffffff; color: #2FDA36; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['status']}} </td>
                                @else
                                    <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #ffffff; color: red; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['status']}} </td>
                                @endif
                            </tr>
                        @endif
                        @if ($dato['areaid'] == '2')
                            <tr style="color: #EDF0FA; padding: 0px; margin: 0px">
                                <td align="left" style="border: 1px solid; padding-left: 10px; border-color: #E5E5E5 ; background: #ffffff; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['base_datos']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #ffffff; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['responsable']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #ffffff; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['usuario']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #ffffff; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['deadline']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #ffffff; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['fecha_carga']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #ffffff; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal">
                                    <table>
                                        <tr>
                                            <td>{{$dato['dias_retraso']}} días </td>
                                            <td><div style="width: 10px; height: 10px; border-radius: 8px; background: red"></div></td>
                                        </tr>
                                    </table> 
                                </td>
                                @if ($dato['status'] == 'Cargado')
                                    <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #ffffff; color: #2FDA36; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['status']}} </td>
                                @else
                                    <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #ffffff; color: red; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['status']}} </td>
                                @endif
                            </tr>
                        @endif
                        @if ($dato['base_datos'] == 'Sell Out Real DT')
                            <tr style="color: #EDF0FA; padding: 0px; margin: 0px">
                                <td rowspan="4" align="center" style="border: 1px solid; border-color: #E5E5E5; background: white; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['area']}} </td>
                                <td align="left" style="border: 1px solid; padding-left: 10px; border-color: #E5E5E5; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['base_datos']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['responsable']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['usuario']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['deadline']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['fecha_carga']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> 
                                    <table>
                                        <tr>
                                            <td>{{$dato['dias_retraso']}} días </td>
                                            <td><div style="width: 10px; height: 10px; border-radius: 8px; background: red"></div></td>
                                        </tr>
                                    </table>
                                </td>
                                @if ($dato['status'] == 'Cargado')
                                    <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #EDF0FA; color: #2FDA36; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['status']}} </td>
                                @else
                                    <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #EDF0FA; color: red; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['status']}} </td>
                                @endif
                            </tr>
                        @endif
                        @if ($dato['areaid'] == '3')
                            <tr style="color:#EDF0FA; padding: 0px; margin: 0px">
                                <td align="left" style="border: 1px solid; padding-left: 10px; border-color: #E5E5E5; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['base_datos']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['responsable']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['usuario']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['deadline']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['fecha_carga']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #EDF0FA; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> 
                                    <table>
                                        <tr>
                                            <td>{{$dato['dias_retraso']}} días </td>
                                            <td><div style="width: 10px; height: 10px; border-radius: 8px; background: red"></div></td>
                                        </tr>
                                    </table>
                                </td>
                                @if ($dato['status'] == 'Cargado')
                                    <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #EDF0FA; color: #2FDA36; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['status']}} </td>
                                @else
                                    <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #EDF0FA; color: red; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['status']}} </td>
                                @endif
                        @endif
                       
                    @endforeach
                    
                </table>
                <br>
                <div style="font-style: normal; font-weight: 500; font-size: 14px; line-height: 21.13px; letter-spacing: -0.051em; color: #1E1E1E" >
                    Les reiteramos nuestro apoyo en este proceso, y recordamos que, ante cualquier consulta o requerimiento, durante esta etapa, podrá ser</br>
                    canalizado al correo soporte@grow-analytics.com.pe .</br>
                    ¡Que tengan un excelente día!   
                </div>
                <div style="font-style: normal; font-weight: 700; font-size: 14px; line-height: 22.75px; letter-spacing: -0.051em; color: #1E1E1E">
                    Atte. Customer Services Prime </br>
                    (Información confidencial, prohibida su divulgación de acuerdo a la Ley Peruana)
                </div>
            </div>
        </center>
    </div>
</body>

</html>
