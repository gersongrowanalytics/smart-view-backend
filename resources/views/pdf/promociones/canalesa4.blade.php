<div class="body1_page3"></div>
<div class="circle_page3" style="color:{{$categoria['catcolor']}}">•</div>
<div class="box2_page" id="color_page3" style="color:{{$categoria['catcolor']}}" >Creciendo Juntos</div>
<div class="box2_page3">Actualización 5 de Agosto del 2021</div>
<div class="box4_page" id="color_page3" style="color:{{$categoria['catcolor']}}">{{$categoria['catnombre']}}</div>
<div class="box4_page3-A4">
    
    


    @foreach($data as $posicionDat => $dat)

        @if($posicionDat == 0)
        <div class="Primera-Columna-Pdf-Promociones">
            <div class="Contenedor-Titulo-Canal-Pdf-Promociones">
                <div 
                    class="Titulo-Canal-Pdf-Promociones-A4"
                    style="background:{{$categoria['catcolor']}}; opacity:{{$opacidadcanal}}"
                >
                    <b>{{$dat['cannombre']}}</b>
                </div>
            </div>
            <table >
                @foreach($dat['promocionesOrdenadas'] as $posicionPromocion => $promocion)
                    @if($posicionPromocion >= $desde && $posicionPromocion <= $hasta)
                        <tr class="fila">
                            <th class="columna">
                                    
                                @if($promocion['cspid'] == 0)
                                <div class="box_table-A4"
                                    id="box_table_color_page3"
                                    style="border: 1px solid red"
                                >
                                
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion.jpg'><br>
                                    <div class="Txt-No-Hay-Promocion">No hay promoción</div>
                                </div>
                                @endif
                                @if($promocion['cspid'] != 0)
                                <div class="box_table-A4"
                                    id="box_table_color_page3"
                                    style="border: 1px solid {{$categoria['catcolor']}}"
                                >
                                
                                    <div class="big_text-A4">
                                        <div 
                                            class="text1" 
                                            id="text1_color_page3"
                                            style="color:{{$categoria['catcolor']}}"
                                        >
                                            @if(isset($promocion['cspcantidadplancha']))
                                                {{round($promocion['cspcantidadplancha'])}} Planchas 
                                            @else 
                                                {{round($promocion['csptotalplancha'])}} Planchas
                                            @endif

                                        </div> 
                                        <div 
                                            class="text2" 
                                            id="text2_color_page3"
                                            style="color:{{$categoria['catcolor']}}"
                                        >
                                            @if(isset($promocion['cspcantidadcombo']))
                                                Total de Combos: {{round($promocion['cspcantidadcombo'])}}
                                            @else
                                                Total de Combos: {{round($promocion['csptotalcombo'])}}
                                            @endif
                                        </div> 
                                        <div class="text3">Sell In Bonificación</div>
                                    </div>
                                    <div class="box_table_img">
                                        <table>
                                            <tr>
                                                <th>
                                                    @foreach($promocion['productos'] as $posicionProducto => $producto)
                                                    @if($posicionProducto == 0)
                                                    <div class="img_products">
                                                        <div class="text_free_product1">Gratis</div>
                                                        <img 
                                                            class="img_product" 
                                                            src="{{$producto['proimagen']}}"
                                                            alt=""
                                                        >
                                                        <div class="text1_product1">{{$producto['prpproductoppt']}}</div>
                                                        <div class="text2_product1">{{$producto['prpcomprappt']}}</div>
                                                    </div>
                                                    @endif
                                                    @endforeach
                                                </th>
                                                <th>
                                                    @foreach($promocion['productosbonificados'] as $posicionProductoBonif => $productobonificado)
                                                    @if($posicionProductoBonif == 0)
                                                    <div class="img_products">
                                                        @if($promocion['cspgratis'] == 1)
                                                        <div class="text_free_product2">Gratis</div>
                                                        @else
                                                        <div class="text_free_product2_white" style="color:white">Gratis</div>
                                                        @endif
                                                        <img 
                                                            class="img_product" 
                                                            src="{{$productobonificado['prbimagen']}}" 
                                                            alt=""
                                                        >
                                                        <div class="text1_product2">{{$productobonificado['prbproductoppt']}}</div>
                                                        <div class="text2_product2">{{$productobonificado['prbcomprappt']}}</div>
                                                    </div>
                                                    @endif
                                                    @endforeach
                                                </th>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                @endif
                            </th>
                        </tr>    
                    @endif
                @endforeach
            </table>
        </div>
        @endif

        @if($posicionDat == 1)
        <div class="Segunda-Columna-Pdf-Promociones-A4" >
            <div class="Contenedor-Titulo-Canal-Pdf-Promociones">
                <div 
                    class="Titulo-Canal-Pdf-Promociones-A4"
                    style="background:{{$categoria['catcolor']}}; opacity:{{$opacidadcanal}}"
                >
                    <b>{{$dat['cannombre']}}</b>
                </div>
            </div>
            <table >
                @foreach($dat['promocionesOrdenadas'] as $posicionPromocion => $promocion)
                    @if($posicionPromocion >= $desde && $posicionPromocion <= $hasta)
                        <tr class="fila">
                            <th class="columna">
                                    
                                @if($promocion['cspid'] == 0)
                                <div class="box_table-A4"
                                    id="box_table_color_page3"
                                    style="border: 1px solid red"
                                >
                                
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion.jpg'><br>
                                    <div class="Txt-No-Hay-Promocion">No hay promoción</div>
                                </div>
                                @endif
                                @if($promocion['cspid'] != 0)
                                <div class="box_table-A4"
                                    id="box_table_color_page3"
                                    style="border: 1px solid {{$categoria['catcolor']}}"
                                >
                                
                                    <div class="big_text-A4">
                                        <div 
                                            class="text1" 
                                            id="text1_color_page3"
                                            style="color:{{$categoria['catcolor']}}"
                                        >
                                            @if(isset($promocion['cspcantidadplancha']))
                                                {{round($promocion['cspcantidadplancha'])}} Planchas 
                                            @else 
                                                {{round($promocion['csptotalplancha'])}} Planchas
                                            @endif

                                        </div> 
                                        <div 
                                            class="text2" 
                                            id="text2_color_page3"
                                            style="color:{{$categoria['catcolor']}}"
                                        >
                                            @if(isset($promocion['cspcantidadcombo']))
                                                Total de Combos: {{round($promocion['cspcantidadcombo'])}}
                                            @else
                                                Total de Combos: {{round($promocion['csptotalcombo'])}}
                                            @endif
                                        </div> 
                                        <div class="text3">Sell In Bonificación</div>
                                    </div>
                                    <div class="box_table_img">
                                        <table>
                                            <tr>
                                                <th>
                                                    @foreach($promocion['productos'] as $posicionProducto => $producto)
                                                    @if($posicionProducto == 0)
                                                    <div class="img_products">
                                                        <div class="text_free_product1">Gratis</div>
                                                        <img 
                                                            class="img_product" 
                                                            src="{{$producto['proimagen']}}"
                                                            alt=""
                                                        >
                                                        <div class="text1_product1">{{$producto['prpproductoppt']}}</div>
                                                        <div class="text2_product1">{{$producto['prpcomprappt']}}</div>
                                                    </div>
                                                    @endif
                                                    @endforeach
                                                </th>
                                                <th>
                                                    @foreach($promocion['productosbonificados'] as $posicionProductoBonif => $productobonificado)
                                                    @if($posicionProductoBonif == 0)
                                                    <div class="img_products">
                                                        @if($promocion['cspgratis'] == 1)
                                                        <div class="text_free_product2">Gratis</div>
                                                        @else
                                                        <div class="text_free_product2_white" style="color:white">Gratis</div>
                                                        @endif
                                                        <img 
                                                            class="img_product" 
                                                            src="{{$productobonificado['prbimagen']}}" 
                                                            alt=""
                                                        >
                                                        <div class="text1_product2">{{$productobonificado['prbproductoppt']}}</div>
                                                        <div class="text2_product2">{{$productobonificado['prbcomprappt']}}</div>
                                                    </div>
                                                    @endif
                                                    @endforeach
                                                </th>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                @endif
                            </th>
                        </tr>    
                    @endif
                @endforeach
            </table>
        </div>
        @endif

    @endforeach

    @if($categoria['catid'] == 1)
        <img class="body2_page-A4" src='../public/images/pdf/Azul-15-15.jpg' alt="">
    @endif
    @if($categoria['catid'] == 2)
        <img class="body2_page-A4" src='../public/images/Rojo-14-15-15.jpg' alt="">
    @endif
    @if($categoria['catid'] == 3)
        <img class="body2_page-A4" src='../public/images/pdf/Lila-15-15.jpg' alt="">
    @endif
    @if($categoria['catid'] == 4)
        <img class="body2_page-A4" src='../public/images/pdf/Verde-15-15.jpg' alt="">
    @endif
    @if($categoria['catid'] == 5)
        <img class="body2_page-A4" src='../public/images/pdf/Morado-15-15.jpg' alt="">
    @endif
    @if($categoria['catid'] == 6)
        <img class="body2_page-A4" src='../public/images/pdf/Celeste-15-15.jpg' alt="">
    @endif
</div>


