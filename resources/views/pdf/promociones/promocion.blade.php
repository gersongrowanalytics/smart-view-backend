<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../resources/css/style.css">
</head>
<body>

    @if($mostrarPdfA4 == true)
        @include('pdf.promociones.canalesa4')
    @else
        @include('pdf.promociones.canales')
    @endif
    

</body>
</html>