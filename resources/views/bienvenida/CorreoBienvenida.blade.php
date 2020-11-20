<html>
<head>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">

</head>
<body>
<style type="text/css">
@import url(https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap);
</style>

    <div style="width: 100%;">
        <Center>
            <div style="width: 370px; ">
                <div id="cabezaCorreo" style="width: 100%;">
                    <img 
                        width="370px" 
                        height="212px" 
                        src="{{ env('APP_URL') }}/Sistema/abs/correo/mercaderista.png" alt="">
                </div>
                <table>
                    <tr>
                        <td align="center" style=" width: 100000px" >
                            <div id="tituloSaludo" style="font-family: Roboto; color:#558CFF; padding-top: 10px; padding-bottom: 8px; font-style: normal; font-weight: bold; font-size: 15px; line-height: 18px;">
                                Hola, {{$nombre}}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style=" width: 100000px">
                            <div id="txtNegrita" style="padding-bottom: 26px; font-family: Roboto; font-style: normal; font-weight: bold; font-size: 10px; line-height: 12px; color: #4D4D4D;">
                                ¡Se creó con éxito tu cuenta!
                            </div>
                        </td>
                    </tr>
                    
                    <tr>
                        <td align="center" style=" width: 100000px">
                            <div style="padding-bottom: 4px; font-family: Roboto; font-style: normal; font-weight: bold; font-size: 10px; line-height: 12px; color: #9C9B9B;" >
                                Usuario
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style=" width: 100000px">
                            <div style="padding-bottom: 15px; font-family: Roboto; font-style: normal; font-weight: normal; font-size: 10px; line-height: 12px; color: #4D4D4D;" >
                                {{$usuario}}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style=" width: 100000px">
                            <div style="padding-bottom: 4px; font-family: Roboto; font-style: normal; font-weight: bold; font-size: 10px; line-height: 12px; color: #9C9B9B;" >
                                Contraseña
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style=" width: 100000px">
                            <div style="padding-bottom: 22px; font-family: Roboto; font-style: normal; font-weight: normal; font-size: 10px; line-height: 12px; color: #4D4D4D;" >
                                {{$contrasena}}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style=" width: 100000px">
                            <div style="margin-left: 60px; margin-right: 60px; margin-bottom:30px">
                                <span style="font-family: Roboto; font-style: normal; font-weight: normal; font-size: 10px; line-height: 12px; color: #9C9B9B;">
                                    Te registraste con la siguiente dirección: 
                                    <span id="direccionCorreo" style="color: #70AAFF;">{{$correo}}</span> 
                                    Por favor, no respondas a este correo electrónico.<br/><br/>
                                    Para cualquier consuta, escríbenos a consultas@gmail.com<br/>
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
                <div id="piesCorreo" style="background: #ECF1FA; margin-top:30px; height: 40px; width: 370px;">
                    <div 
                        id="" 
                        style="float:left; font-family: Roboto; font-style: normal; font-weight: bold; font-size: 9px; line-height: 11px; color: #4157BD; padding-top:15px; padding-left:20px">
                        @ Lead Smart View 2020</div>
                    <div style="width: 50%; float: right">
                        <img 
                            width  = "85px";
                            height = "40px";
                            style  = "float: right;" 
                            src="{{ env('APP_URL') }}/Sistema/abs/correo/logo.png" 
                            alt="">
                    </div>
                        
                </div>
            </div>
        </Center>
    </div>
</bodY>
</html>