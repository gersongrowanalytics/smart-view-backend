<html>
<head>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">

</head>
<body>
<style type="text/css">
@import url(https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap);
</style>

    <div style="width: 100%;">
        <div style="width: 370px; margin-left: 25%">
            <div id="cabezaCorreo" style="width: 100%;">
                <img width="370px" height="212px" src="https://southcentralus1-mediap.svc.ms/transform/thumbnail?provider=spo&inputFormat=png&cs=fFNQTw&docid=https%3A%2F%2Fgrowanalyticscom.sharepoint.com%3A443%2F_api%2Fv2.0%2Fdrives%2Fb!SaJkXZ9JpEG99drP4BB2WslTkCQrO_JNnRYYJ732Iyr_tez6U7VrQ7CcgI2JyUjs%2Fitems%2F01ATJHHXWWIQNXKD4TGVBYUDOAYXYT2MOS%3Fversion%3DPublished&access_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJub25lIn0.eyJhdWQiOiIwMDAwMDAwMy0wMDAwLTBmZjEtY2UwMC0wMDAwMDAwMDAwMDAvZ3Jvd2FuYWx5dGljc2NvbS5zaGFyZXBvaW50LmNvbUAxZGY0Njg4Yy1iOTE1LTQxMDMtOThjMC0zYTM2OGZiMjkzZTgiLCJpc3MiOiIwMDAwMDAwMy0wMDAwLTBmZjEtY2UwMC0wMDAwMDAwMDAwMDAiLCJuYmYiOiIxNjAyMDkzNjAwIiwiZXhwIjoiMTYwMjExNTIwMCIsImVuZHBvaW50dXJsIjoiYlZGbk8wQ0o5UHBzMGlxMW82R3J3YlJ3ajcwY24vZFVzNS9jdUVmZ0NPST0iLCJlbmRwb2ludHVybExlbmd0aCI6IjEyMyIsImlzbG9vcGJhY2siOiJUcnVlIiwidmVyIjoiaGFzaGVkcHJvb2Z0b2tlbiIsInNpdGVpZCI6Ik5XUTJOR0V5TkRrdE5EazVaaTAwTVdFMExXSmtaalV0WkdGalptVXdNVEEzTmpWaCIsInNpZ25pbl9zdGF0ZSI6IltcImttc2lcIl0iLCJuYW1laWQiOiIwIy5mfG1lbWJlcnNoaXB8Z2Vyc29uLnZpbGNhQGdyb3ctYW5hbHl0aWNzLmNvbSIsIm5paSI6Im1pY3Jvc29mdC5zaGFyZXBvaW50IiwiaXN1c2VyIjoidHJ1ZSIsImNhY2hla2V5IjoiMGguZnxtZW1iZXJzaGlwfDEwMDMyMDAwZDU4MmQ1M2JAbGl2ZS5jb20iLCJ0dCI6IjAiLCJ1c2VQZXJzaXN0ZW50Q29va2llIjoiMyJ9.NDF6RDRjSmgybE5FVlNFZ0hqYUVRUlRDajdhdDlrYmwvMXR4WDFQQkpCND0&encodeFailures=1&srcWidth=&srcHeight=&width=1147&height=655&action=Access" alt="">
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
                            href="http://smartview.gavsistemas.com/"
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
                        src    = "https://southcentralus1-mediap.svc.ms/transform/thumbnail?provider=spo&inputFormat=png&cs=fFNQTw&docid=https%3A%2F%2Fgrowanalyticscom.sharepoint.com%3A443%2F_api%2Fv2.0%2Fdrives%2Fb!SaJkXZ9JpEG99drP4BB2WslTkCQrO_JNnRYYJ732Iyr_tez6U7VrQ7CcgI2JyUjs%2Fitems%2F01ATJHHXT3MZB5J7RYBZFJFDOMH6OMECCQ%3Fversion%3DPublished&access_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJub25lIn0.eyJhdWQiOiIwMDAwMDAwMy0wMDAwLTBmZjEtY2UwMC0wMDAwMDAwMDAwMDAvZ3Jvd2FuYWx5dGljc2NvbS5zaGFyZXBvaW50LmNvbUAxZGY0Njg4Yy1iOTE1LTQxMDMtOThjMC0zYTM2OGZiMjkzZTgiLCJpc3MiOiIwMDAwMDAwMy0wMDAwLTBmZjEtY2UwMC0wMDAwMDAwMDAwMDAiLCJuYmYiOiIxNjAyMDkzNjAwIiwiZXhwIjoiMTYwMjExNTIwMCIsImVuZHBvaW50dXJsIjoiYlZGbk8wQ0o5UHBzMGlxMW82R3J3YlJ3ajcwY24vZFVzNS9jdUVmZ0NPST0iLCJlbmRwb2ludHVybExlbmd0aCI6IjEyMyIsImlzbG9vcGJhY2siOiJUcnVlIiwidmVyIjoiaGFzaGVkcHJvb2Z0b2tlbiIsInNpdGVpZCI6Ik5XUTJOR0V5TkRrdE5EazVaaTAwTVdFMExXSmtaalV0WkdGalptVXdNVEEzTmpWaCIsInNpZ25pbl9zdGF0ZSI6IltcImttc2lcIl0iLCJuYW1laWQiOiIwIy5mfG1lbWJlcnNoaXB8Z2Vyc29uLnZpbGNhQGdyb3ctYW5hbHl0aWNzLmNvbSIsIm5paSI6Im1pY3Jvc29mdC5zaGFyZXBvaW50IiwiaXN1c2VyIjoidHJ1ZSIsImNhY2hla2V5IjoiMGguZnxtZW1iZXJzaGlwfDEwMDMyMDAwZDU4MmQ1M2JAbGl2ZS5jb20iLCJ0dCI6IjAiLCJ1c2VQZXJzaXN0ZW50Q29va2llIjoiMyJ9.NDF6RDRjSmgybE5FVlNFZ0hqYUVRUlRDajdhdDlrYmwvMXR4WDFQQkpCND0&encodeFailures=1&srcWidth=&srcHeight=&width=264&height=124&action=Preview" alt="">
                </div>
                    
            </div>
        </div>
    </div>
</bodY>
</html>