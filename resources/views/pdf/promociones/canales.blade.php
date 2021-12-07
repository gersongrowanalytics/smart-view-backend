<div class="body1_page3"></div>
<div class="circle_page3">•</div>
<div class="box2_page" id="color_page3">Creciendo Juntos</div>
<div class="box2_page3">Actualización 5 de Agosto del 2021</div>
<div class="box4_page" id="color_page3">Infant</div>
<div class="box4_page3">
    
    <img class="body2_page" src='../public/images/Rojo-14-15-15.jpg' alt="">


    @foreach($data as $posicionDat => $dat)

        @if($posicionDat == 0)
        <div class="Primera-Columna-Pdf-Promociones">
            <div class="Contenedor-Titulo-Canal-Pdf-Promociones">
                <div class="Titulo-Canal-Pdf-Promociones">
                    <b>{{$dat['cannombre']}}</b>
                </div>
            </div>
            <table >
                <tr class="fila">
                    <th class="columna">
                            <div class="box_table"
                            id="box_table_color_page3">
                            <div class="big_text">
                                <div class="text1" id="text1_color_page3">50 Combos </div> 
                                <div class="text2" id="text2_color_page3">Total de planchas: 300</div> 
                                <div class="text3">Sell In Bonificación</div>
                            </div>
                            <div class="box_table_img">
                                <table>
                                    <tr>
                                        <th>
                                            <div class="img_products">
                                                <div class="text_free_product1">Gratis</div>
                                                <img class="img_product" src="https://pre-back.leadsmartview.com/Sistema/promociones/IMAGENES/PRODUCTOSNUEVO/iE3g1-2021-08-31-30226606.png" alt="">
                                                <div class="text1_product1">11 HAF x120</div>
                                                <div class="text2_product1">Por 9 plancha(s)</div>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="img_products">
                                                <div class="text_free_product2">Gratis</div>
                                                <img 
                                                    class="img_product" 
                                                    src="https://pre-back.leadsmartview.com/Sistema/promociones/IMAGENES/PRODUCTOSNUEVO/iE3g1-2021-08-31-30226606.png" 
                                                    alt=""
                                                >
                                                <div class="text1_product2">11 HAF x120</div>
                                                <div class="text2_product2">Por 9 plancha(s)</div>
                                            </div>
                                        </th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </th>
                </tr>
            </table>

        </div>
        @endif

        @if($posicionDat == 1)
        <div class="Segunda-Columna-Pdf-Promociones"></div>
        @endif

        @if($posicionDat == 2)
        <div class="Tercera-Columna-Pdf-Promociones"></div>
        @endif

        @if($posicionDat == 3)
        <div class="Cuarta-Columna-Pdf-Promociones"></div>
        @endif
    @endforeach

</div>


