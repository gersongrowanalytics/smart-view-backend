<html>

<head>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <style type="text/css">
        @import url(https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap);

    </style>

    <div style="width: 100%;">
        <center>
            <div style="width: 1050px; border: 1px solid #E7F3FF;">
                <div style="background: #FFFFF; margin-top:30px; height: 40px; width: 1050px; padding-top: 0px; position:relative; margin-bottom: 5px">
                    <div style=" font-style: normal; font-weight: 700; font-size: 18px; line-height: 21px; color: #004FB8; padding-top:10px;">
                        Status Creciendo Juntos
                    </div>
                </div>
                <table style="padding-left: 30px; padding-right: 30px; width: 950px; padding-top: 15px; border-collapse: collapse; margin-bottom: 20px">
                    <tr style="color: #edf0fab3; height: 40px;">
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
                            <tr style="color: #edf0fab3;">
                                <td rowspan="11" align="center" style="border: 1px solid; color: black; background: white; border-color: #E5E5E5; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['area']}} </td>
                                <td align="left" style="border: 1px solid; padding-left: 10px; border-color: #E5E5E5 ; color: black; background: #edf0fab3; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['base_datos']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['responsable']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['usuario']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['deadline']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['fecha_carga']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> 
                                    <table>
                                        <tr>
                                            <td>{{$dato['dias_retraso']}} días </td>
                                            <td><div style="width: 10px; height: 10px; border-radius: 8px; background: red"></div></td>
                                        </tr>
                                    </table>
                                </td>
                                @if ($dato['status'] == 'Cargado')
                                    <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #edf0fab3; color: #2FDA36; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['status']}} </td>
                                @else
                                    <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #edf0fab3; color: red; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['status']}} </td>
                                @endif
                                
                            </tr>
                        @endif
                        @if ($dato['areaid'] == '1')
                            <tr style="color: #edf0fab3">
                                {{-- <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; color: black"> {{$dato['area']}} </td> --}}
                                <td align="left" style="border: 1px solid; padding-left: 10px; border-color: #E5E5E5 ; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['base_datos']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['responsable']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['usuario']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['deadline']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['fecha_carga']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> 
                                    <table>
                                        <tr>
                                            <td>{{$dato['dias_retraso']}} días </td>
                                            <td><div style="width: 10px; height: 10px; border-radius: 8px; background: red"></div></td>
                                        </tr>
                                    </table>
                                </td>
                                @if ($dato['status'] == 'Cargado')
                                    <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #edf0fab3; color: #2FDA36; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['status']}} </td>
                                @else
                                    <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #edf0fab3; color: red; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['status']}} </td>
                                @endif
                            </tr>
                        @endif
                        @if ($dato['base_datos'] == 'Sell In Real')
                            <tr style="color: #edf0fab3">
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
                            <tr style="color: #edf0fab3">
                                {{-- <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; color: black"> {{$dato['area']}} </td> --}}
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
                            <tr style="color: #edf0fab3">
                                <td rowspan="4" align="center" style="border: 1px solid; border-color: #E5E5E5; background: white; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['area']}} </td>
                                <td align="left" style="border: 1px solid; padding-left: 10px; border-color: #E5E5E5; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['base_datos']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['responsable']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['usuario']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['deadline']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['fecha_carga']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> 
                                    <table>
                                        <tr>
                                            <td>{{$dato['dias_retraso']}} días </td>
                                            <td><div style="width: 10px; height: 10px; border-radius: 8px; background: red"></div></td>
                                        </tr>
                                    </table>
                                </td>
                                @if ($dato['status'] == 'Cargado')
                                    <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #edf0fab3; color: #2FDA36; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['status']}} </td>
                                @else
                                    <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #edf0fab3; color: red; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['status']}} </td>
                                @endif
                            </tr>
                        @endif
                        @if ($dato['areaid'] == '3')
                            <tr style="color:#edf0fab3">
                                {{-- <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; color: black"> {{$dato['area']}} </td> --}}
                                <td align="left" style="border: 1px solid; padding-left: 10px; border-color: #E5E5E5; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['base_datos']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['responsable']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['usuario']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['deadline']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['fecha_carga']}} </td>
                                <td align="center" style="border: 1px solid; border-color: #E5E5E5; background: #edf0fab3; color: black; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> 
                                    <table>
                                        <tr>
                                            <td>{{$dato['dias_retraso']}} días </td>
                                            <td><div style="width: 10px; height: 10px; border-radius: 8px; background: red"></div></td>
                                        </tr>
                                    </table>
                                </td>
                                @if ($dato['status'] == 'Cargado')
                                    <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #edf0fab3; color: #2FDA36; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['status']}} </td>
                                @else
                                    <td align="center" style="border: 1px solid; border-color: #E5E5E5 ; background: #edf0fab3; color: red; font-weight: 400; font-size: 12px; line-height: 16px; font-style: normal"> {{$dato['status']}} </td>
                                @endif
                        @endif
                       
                    @endforeach
                    
                    <tr>
                        <td colspan="8" align="center" style=" width: 100000px">
                            <div style="margin-left: 60px; margin-right: 60px; margin-bottom:30px; margin-top:20px">
                                <span
                                    style=" font-style: normal; font-weight: normal; font-size: 10px; line-height: 12px; color: #9C9B9B;">
                                    <!-- Para cualquier consulta, escríbenos a <span id="direccionCorreo" style="color: #70AAFF;">consultasxxxx@xxxx.com.pe</span> -->
                                    Puedes revisar tus status en el siguiente enlace:
                                </span><br />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" style=" width: 100000px">
                            <a id="btnPlataforma" href="https://smartview.grow-corporate.com/"
                                style="text-decoration: none; color: white; background: #FF8023; padding-top: 10px; padding-bottom: 10px; padding-left: 35px; padding-right: 35px; border-radius: 22px; margin-top: 50px;  font-style: normal; font-weight: bold; font-size: 10px; line-height: 12px;">Ir
                                a la plataforma</a>
                        </td>
                    </tr>
                </table>
                <div id="piesCorreo" style="background: #ECF1FA; margin-top:30px; height: 40px; width: 1050px;">
                    <div id=""
                        style=" font-style: normal; font-weight: bold; font-size: 9px; line-height: 11px; color: #4157BD; padding-top:15px">
                        © <span id="anioactual"></span> GROW ANALYTICS</div>
                </div>
            </div>
        </center>
    </div>
</body>

</html>
