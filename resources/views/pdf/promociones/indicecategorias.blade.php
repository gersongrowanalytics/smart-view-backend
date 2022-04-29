<img class="body_page" src='../public/images/pdf/Indice.jpg' alt="">
<!-- <div class="Texto-Titulo-Indice-Promociones" >Índice</div> -->


@if($cantidadCategorias == 1)
<div class="Contenedor-Tarjetas-Categorias-Indice-Pdf" style="margin-top: 410px;">
@elseif($cantidadCategorias == 2)
<div class="Contenedor-Tarjetas-Categorias-Indice-Pdf" style="margin-top: 350px;">
@elseif($cantidadCategorias == 3)
<div class="Contenedor-Tarjetas-Categorias-Indice-Pdf" style="margin-top: 280px;">
@elseif($cantidadCategorias == 4)
<div class="Contenedor-Tarjetas-Categorias-Indice-Pdf" style="margin-top: 220px;">
@elseif($cantidadCategorias == 5)
<div class="Contenedor-Tarjetas-Categorias-Indice-Pdf" style="margin-top: 150px;">
@elseif($cantidadCategorias == 6)
<div class="Contenedor-Tarjetas-Categorias-Indice-Pdf" style="margin-top: 90px;">
@endif

    @foreach($categorias as $categoria)
        @if($categoria['catid'] == 1 && sizeof($categoria['canales']) > 0)
        <img class="Icono-Indice-Promociones-Pdf" src='../public/images/pdf/iconoindice/indicefamily.jpg' alt=""><br>
        @elseif($categoria['catid'] == 2 && sizeof($categoria['canales']) > 0)
        <img class="Icono-Indice-Promociones-Pdf" src='../public/images/pdf/iconoindice/indiceinfant.jpg' alt=""><br>

        @elseif(isset($categoria['canales']) && $categoria['catid'] == 3)
            @if($categoria['catid'] == 3 && sizeof($categoria['canales']) > 0)
            <img class="Icono-Indice-Promociones-Pdf" src='../public/images/pdf/iconoindice/indiceadult.jpg' alt=""><br>
            @endif

        @elseif($categoria['catid'] == 4 && sizeof($categoria['canales']) > 0)
        <img class="Icono-Indice-Promociones-Pdf" src='../public/images/pdf/iconoindice/indicewipes.jpg' alt="">
        @elseif($categoria['catid'] == 5 && sizeof($categoria['canales']) > 0)
        <img class="Icono-Indice-Promociones-Pdf" src='../public/images/pdf/iconoindice/indicefem.jpg' alt=""><br>

        @elseif(isset($categoria['canales']) && $categoria['catid'] == 6)
            @if($categoria['catid'] == 6 && sizeof($categoria['canales']) > 0)
            <img class="Icono-Indice-Promociones-Pdf" src='../public/images/pdf/iconoindice/indicemulticategoria.jpg' alt=""><br>
            @endif
        @endif
    @endforeach
</div>