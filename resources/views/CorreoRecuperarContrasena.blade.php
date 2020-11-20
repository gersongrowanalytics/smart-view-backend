<html>
<head>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">

</head>
<body>

    <div style="width: 100%;">
        <div style="width: 370px; margin-left: 25%">
            <table>
                <tr>
                    <td align="center" style=" width: 100000px">
                        <img 
                            width="159px" 
                            height="153px" 
                            src="{{ env('APP_URL') }}/Sistema/abs/correo/mercaderista.png" alt="">
                    </td>
                </tr>
                <tr>
                    <td align="center" style=" width: 100000px;" >
                        <div id="" style="font-family: Roboto; font-style: normal; font-weight: bold; font-size: 15px; line-height: 18px; color: #4D4D4D;">
                            ¡Recuperaste tu contraseña<br/>con éxito!
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <td align="center" style="width: 100000px; padding-top:9px">
                        <div style=" font-family: Roboto; font-style: normal; font-weight: bold; font-size: 10px; line-height: 12px; color: #9C9B9B;" >
                            Usuario
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="center" style=" width: 100000px">
                        <div style=" font-family: Roboto; font-style: normal; font-weight: normal; font-size: 10px; line-height: 12px; color: #4D4D4D;" >
                            {{$usuario}}
                        </div>
                    </td>
                </tr>

                <tr>
                    <td align="center" style="width: 100000px; padding-top:9px">
                        <div style=" font-family: Roboto; font-style: normal; font-weight: bold; font-size: 10px; line-height: 12px; color: #9C9B9B;" >
                            contraseña
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="center" style=" width: 100000px">
                        <div style=" font-family: Roboto; font-style: normal; font-weight: normal; font-size: 10px; line-height: 12px; color: #4D4D4D;" >
                            {{$contrasena}}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="center" style=" width: 100000px">
                        <div style="margin-left: 60px; margin-right: 60px; margin-bottom:30px; margin-top:20px">
                            <span style="font-family: Roboto; font-style: normal; font-weight: normal; font-size: 10px; line-height: 12px; color: #9C9B9B;">
                                Para cualquier consulta, escríbenos a <span id="direccionCorreo" style="color: #70AAFF;">consultasxxxx@xxxx.com.pe</span>
                            </span><br/>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="center" style=" width: 100000px">
                        <a 
                            id="btnPlataforma" 
                            href="http://leadsmartview.com/"
                            style="text-decoration: none; color: white; background: #558CFF; padding-top: 10px; padding-bottom: 10px; padding-left: 35px; padding-right: 35px; border-radius: 22px; margin-top: 50px; font-family: Roboto; font-style: normal; font-weight: bold; font-size: 10px; line-height: 12px;"
                        >IR A PLATAFORMA</a>
                    </td>
                </tr>
            </table>
            <div id="piesCorreo" style="background: #ECF1FA; margin-top:30px; height: 40px; width: 370px; padding-left:20px">
                <div id="" style="float:left; width: 50%; font-family: Roboto; font-style: normal; font-weight: bold; font-size: 9px; line-height: 11px; color: #4157BD; padding-top:15px">@ Lead Smart View 2020</div>
                <div style="width: 50%; float: right">
                    <img 
                        width  = "85px";
                        height = "40px";
                        style  = "float: right;" 
                        src    = "{{ env('APP_URL') }}/Sistema/abs/correo/logo.png" alt="">
                </div>
                    
            </div>
        </div>
    </div>
</bodY>
</html>