<img class="body_page" src='../public/images/pdf/Indice.jpg' alt="">
<!-- <div class="Texto-Titulo-Indice-Promociones" >Índice</div> -->
<div class="Contenedor-Tarjetas-Categorias-Indice-Pdf">
    @foreach($categorias as $categoria)
        @if($categoria['catid'] == 1 && sizeof($categoria['canales']) > 0)
        <img class="Icono-Indice-Promociones-Pdf" src='../public/images/pdf/iconoindice/indicefamily.jpg' alt=""><br>
        @elseif($categoria['catid'] == 2 && sizeof($categoria['canales']) > 0)
        <img class="Icono-Indice-Promociones-Pdf" src='../public/images/pdf/iconoindice/indiceinfant.jpg' alt=""><br>
        @elseif($categoria['catid'] == 3 && sizeof($categoria['canales']) > 0)
        <img class="Icono-Indice-Promociones-Pdf" src='../public/images/pdf/iconoindice/indiceadult.jpg' alt=""><br>
        @elseif($categoria['catid'] == 4 && sizeof($categoria['canales']) > 0)
        <img class="Icono-Indice-Promociones-Pdf" src='../public/images/pdf/iconoindice/indicewipes.jpg' alt="">
        @elseif($categoria['catid'] == 5 && sizeof($categoria['canales']) > 0)
        <img class="Icono-Indice-Promociones-Pdf" src='../public/images/pdf/iconoindice/indicefem.jpg' alt=""><br>
        @elseif($categoria['catid'] == 6 && sizeof($categoria['canales']) > 0)
        <img class="Icono-Indice-Promociones-Pdf" src='../public/images/pdf/iconoindice/indicemulticategoria.jpg' alt=""><br>
        @endif
    @endforeach
</div>