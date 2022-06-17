<html>

<head>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
</head>

<body>
    <style type="text/css">
        @import url(https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap);

    </style>

    <div style="width: 100%;">
        <center>
            <div style="width: 550px; border: 1px solid #E7F3FF;">
                <div id="piesCorreo"
                    style="background: #FFFFF; margin-top:30px; height: 40px; width: 550px; padding-top: 0px; position:relative">
                    <div id=""
                        style=" font-style: normal; font-weight: 900; font-size: 18px; line-height: 21px; color: #004FB8; padding-top:10px;">
                        Grow Analytics
                    </div>
                </div>
                <table style="padding-left: 30px; padding-right: 30px;">
                    <tr>
                        <td align="center" style=" width: 100000px;">
                            <div id=""
                                style=" font-style: normal; font-weight: bold; font-size: 15px; line-height: 18px; color: #004FB8; padding-top:15px">
                                Hemos detectado imagenes pendientes en la plataforma de<br> SmartView.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style=" width: 100000px;">
                            <div id=""
                                style=" font-style: normal; font-weight: normal; font-size: 13px; line-height: 18px; color: black; padding-top:10px">
                                <b>Cantidad de productos sin imagenes:</b> {{ $cantidad }}
                            </div>
                        </td>
                    </tr>
                    @foreach ($registros as $registro)
                        <tr>
                            <td align="center" style=" width: 100000px;">
                                <div id=""
                                    style=" font-style: normal; font-weight: normal; font-size: 13px; line-height: 18px; color: black; padding-top:1px">
                                    {{ $registro->pronombre }}
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    <tr>
                        <td align="center" style=" width: 100000px">
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
                        <td align="center" style=" width: 100000px">
                            <a id="btnPlataforma" href="https://smartview.grow-corporate.com/banco-imagen"
                                style="text-decoration: none; color: white; background: #FF8023; padding-top: 10px; padding-bottom: 10px; padding-left: 35px; padding-right: 35px; border-radius: 22px; margin-top: 50px;  font-style: normal; font-weight: bold; font-size: 10px; line-height: 12px;">Ir
                                a la plataforma</a>
                        </td>
                    </tr>
                </table>
                <div id="piesCorreo" style="background: #ECF1FA; margin-top:30px; height: 40px; width: 550px;">
                    <div id=""
                        style=" font-style: normal; font-weight: bold; font-size: 9px; line-height: 11px; color: #4157BD; padding-top:15px">
                        © <span id="anioactual"></span> GROW ANALYTICS</div>
                </div>
            </div>
        </center>

    </div>
</body>

</html>
