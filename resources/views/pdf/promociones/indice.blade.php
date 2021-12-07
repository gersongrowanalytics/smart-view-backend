<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <link rel="stylesheet" href="{{asset('css/style.css')}}"> --}}
    {{-- <link rel="stylesheet" href="../css/style.css"> --}}
    <link rel="stylesheet" href="../resources/css/style.css">
</head>
<body>
    @if($posicion == 0)
        @include('pdf.promociones.caratula')
    @endif
    
    @include('pdf.promociones.titulocategoria')
    
</body>
</html>