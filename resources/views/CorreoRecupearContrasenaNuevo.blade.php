<html>
<head>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">

</head>
<body>

    <div style="width: 100%; ">
        <center>
            <div style="width: 560px;">
                <div id="piesCorreo" style="background: #558CFF; margin-top:30px; height: 40px; width: 450px; padding-top: 0px; position:relative">
                    <table border="0" cellspacing="0" cellpadding="0" style="width: 100%; position:absolute;" >
                        <tr style="width: 100%;">
                            <td style="width: 12.5%; height:5px; background: #558CFF"></td>
                            <td style="width: 12.5%; height:5px; background: #4157BD"></td>
                            <td style="width: 12.5%; height:5px; background: #4761D1"></td>
                            <td style="width: 12.5%; height:5px; background: #4B68E2"></td>
                            <td style="width: 12.5%; height:5px; background: #558CFF"></td>
                            <td style="width: 12.5%; height:5px; background: #4157BD"></td>
                            <td style="width: 12.5%; height:5px; background: #4761D1"></td>
                            <td style="width: 12.5%; height:5px; background: #4B68E2"></td>
                        </tr>
                    </table>
                    <div 
                        id="" 
                        style=" font-style: normal; font-weight: 900; font-size: 18px; line-height: 21px; color: #FFFFFF; padding-top:10px;">
                        Grow Analytics
                    </div>
                </div>
                <table>
                    <tr>
                        <td align="center" style=" width: 100000px;" >
                            <div id="" style=" font-style: normal; font-weight: bold; font-size: 15px; line-height: 18px; color: #4D4D4D; padding-top:15px">
                            Hemos recibido una solicitud de <br>cambio de contraseña.
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style=" width: 100000px">
                            <div style="margin-left: 60px; margin-right: 60px; margin-bottom:10px; margin-top:20px">
                                <span style=" font-style: normal; font-weight: normal; font-size: 10px; line-height: 12px; color: #9C9B9B;">
                                    <!-- Para cualquier consulta, escríbenos a <span id="direccionCorreo" style="color: #70AAFF;">consultasxxxx@xxxx.com.pe</span> -->
                                    Puedes hacerlo a través del siguiente enlace:
                                </span><br/>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style=" width: 100000px">
                            <a 
                                id="btnPlataforma" 
                                href="http://localhost:3000/cambiar-contrasenia/{{$token}}"
                                style="text-decoration: none; color: white; background: #558CFF; padding-top: 10px; padding-bottom: 10px; padding-left: 35px; padding-right: 35px; border-radius: 22px; margin-top: 50px;  font-style: normal; font-weight: bold; font-size: 10px; line-height: 12px;"
                            >Cambiar Contraseña</a>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style=" width: 100000px">
                            <div style="margin-left: 10px; margin-right: 10px; margin-top:10px">
                                <span style=" font-style: normal; font-weight: normal; font-size: 10px; line-height: 12px; color: #9C9B9B;">
                                    En caso tengas problemas para cambiar tu contraseña, ingresar al siguiente enlace:<br> <a style="text-decoration: none; color: #558CFF;" href="http://localhost:3000/cambiar-contrasenia/{{$token}}" >http://localhost:3000/cambiar-contrasenia/{{$token}}</a>
                                </span>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style=" width: 100000px">
                            <div style="font-size: 10px">
                                
                            </div>
                        </td>
                    </tr>
                </table>
                <div id="piesCorreo" style="background: #ECF1FA; margin-top:30px; height: 40px; width: 450px;">
                    <div 
                        id="" 
                        style=" font-style: normal; font-weight: bold; font-size: 9px; line-height: 11px; color: #4157BD; padding-top:15px"
                    >
                        © <span id="anioactual"></span> GROW ANALYTICS</div>
                </div>
            </div>
        </center>
        
    </div>

    <script>
        var elem = document.getElementById('anioactual');
        var anio = new Date().getFullYear();
        elem.innerHTML = anio
    </script>
</bodY>
</html>