<div class="body1_page3"></div>
<div class="circle_page3" style="color:{{$categoria['catcolor']}}">•</div>
<div class="box2_page" id="color_page3" style="color:{{$categoria['catcolor']}}" >Creciendo Juntos</div>
<div class="box2_page3">Actualización 5 de Agosto del 2021</div>
<div class="box4_page" id="color_page3" style="color:{{$categoria['catcolor']}}">{{$categoria['catnombre']}}</div>
<div class="box4_page3">
    
    


    @foreach($data as $posicionDat => $dat)

        @if($posicionDat == 0)
        <div class="Primera-Columna-Pdf-Promociones">
            <div class="Contenedor-Titulo-Canal-Pdf-Promociones">
                <div 
                    class="Titulo-Canal-Pdf-Promociones"
                    style="background:{{$categoria['catcolor']}}"
                >
                    <b>{{$dat['cannombre']}}</b>
                </div>
            </div>
            <table >
                @foreach($dat['promocionesOrdenadas'] as $promocion)
                <tr class="fila">
                    <th class="columna">
                            
                        @if($promocion['cspid'] == 0)
                        <div class="box_table"
                            id="box_table_color_page3"
                            style="border: 1px solid red"
                        >
                        
                            <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion.jpg'><br>
                            <div class="Txt-No-Hay-Promocion">No hay promoción</div>
                        </div>
                        @endif
                        @if($promocion['cspid'] != 0)
                        <div class="box_table"
                            id="box_table_color_page3"
                            style="border: 1px solid {{$categoria['catcolor']}}"
                        >
                        
                            <div class="big_text">
                                <div 
                                    class="text1" 
                                    id="text1_color_page3"
                                    style="color:{{$categoria['catcolor']}}"
                                >{{round($promocion['csptotalcombo'])}} Combos </div> 
                                <div 
                                    class="text2" 
                                    id="text2_color_page3"
                                    style="color:{{$categoria['catcolor']}}"
                                >Total de planchas: {{round($promocion['csptotalplancha'])}}</div> 
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
                @endforeach
            </table>
        </div>
        @endif

        @if($posicionDat == 1)
        <div class="Segunda-Columna-Pdf-Promociones" style="border-right: 1px dashed {{$categoria['catcolor']}}" >
            <div class="Contenedor-Titulo-Canal-Pdf-Promociones">
                <div 
                    class="Titulo-Canal-Pdf-Promociones"
                    style="background:{{$categoria['catcolor']}}"
                >
                    <b>{{$dat['cannombre']}}</b>
                </div>
            </div>
            <table >
                @foreach($dat['promocionesOrdenadas'] as $promocion)
                <tr class="fila">
                    <th class="columna">
                            
                        @if($promocion['cspid'] == 0)
                        <div class="box_table"
                            id="box_table_color_page3"
                            style="border: 1px solid red"
                        >
                        
                            <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion.jpg'><br>
                            <div class="Txt-No-Hay-Promocion">No hay promoción</div>
                        </div>
                        @endif
                        @if($promocion['cspid'] != 0)
                        <div class="box_table"
                            id="box_table_color_page3"
                            style="border: 1px solid {{$categoria['catcolor']}}"
                        >
                        
                            <div class="big_text">
                                <div 
                                    class="text1" 
                                    id="text1_color_page3"
                                    style="color:{{$categoria['catcolor']}}"
                                >{{round($promocion['csptotalcombo'])}} Combos </div> 
                                <div 
                                    class="text2" 
                                    id="text2_color_page3"
                                    style="color:{{$categoria['catcolor']}}"
                                >Total de planchas: {{round($promocion['csptotalplancha'])}}</div> 
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
                @endforeach
            </table>
        </div>
        @endif

        @if($posicionDat == 2)
        <div class="Tercera-Columna-Pdf-Promociones">
            <div class="Contenedor-Titulo-Canal-Pdf-Promociones">
                <div 
                    class="Titulo-Canal-Pdf-Promociones"
                    style="background:{{$categoria['catcolor']}}"
                >
                    <b>{{$dat['cannombre']}}</b>
                </div>
            </div>
            <table >
                @foreach($dat['promocionesOrdenadas'] as $promocion)
                <tr class="fila">
                    <th class="columna">
                            
                        @if($promocion['cspid'] == 0)
                        <div class="box_table"
                            id="box_table_color_page3"
                            style="border: 1px solid red"
                        >
                        
                            <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion.jpg'><br>
                            <div class="Txt-No-Hay-Promocion">No hay promoción</div>
                        </div>
                        @endif
                        @if($promocion['cspid'] != 0)
                        <div class="box_table"
                            id="box_table_color_page3"
                            style="border: 1px solid {{$categoria['catcolor']}}"
                        >
                        
                            <div class="big_text">
                                <div 
                                    class="text1" 
                                    id="text1_color_page3"
                                    style="color:{{$categoria['catcolor']}}"
                                >{{round($promocion['csptotalcombo'])}} Combos </div> 
                                <div 
                                    class="text2" 
                                    id="text2_color_page3"
                                    style="color:{{$categoria['catcolor']}}"
                                >Total de planchas: {{round($promocion['csptotalplancha'])}}</div> 
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
                @endforeach
            </table>
        </div>
        @endif

        @if($posicionDat == 3)
        <div class="Cuarta-Columna-Pdf-Promociones">
            <div class="Contenedor-Titulo-Canal-Pdf-Promociones">
                <div 
                    class="Titulo-Canal-Pdf-Promociones"
                    style="background:{{$categoria['catcolor']}}"
                >
                    <b>{{$dat['cannombre']}}</b>
                </div>
            </div>
            <table >
                @foreach($dat['promocionesOrdenadas'] as $promocion)
                <tr class="fila">
                    <th class="columna">
                            
                        @if($promocion['cspid'] == 0)
                        <div class="box_table"
                            id="box_table_color_page3"
                            style="border: 1px solid red"
                        >
                        
                            <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion.jpg'><br>
                            <div class="Txt-No-Hay-Promocion">No hay promoción</div>
                        </div>
                        @endif
                        @if($promocion['cspid'] != 0)
                        <div class="box_table"
                            id="box_table_color_page3"
                            style="border: 1px solid {{$categoria['catcolor']}}"
                        >
                        
                            <div class="big_text">
                                <div 
                                    class="text1" 
                                    id="text1_color_page3"
                                    style="color:{{$categoria['catcolor']}}"
                                >{{round($promocion['csptotalcombo'])}} Combos </div> 
                                <div 
                                    class="text2" 
                                    id="text2_color_page3"
                                    style="color:{{$categoria['catcolor']}}"
                                >Total de planchas: {{round($promocion['csptotalplancha'])}}</div> 
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
                @endforeach
            </table>
        </div>
        @endif
    @endforeach

    @if($categoria['catid'] == 1)
        <img class="body2_page" src='../public/images/pdf/Azul-15-15.jpg' alt="">
    @endif
    @if($categoria['catid'] == 2)
        <img class="body2_page" src='../public/images/Rojo-14-15-15.jpg' alt="">
    @endif
    @if($categoria['catid'] == 3)
        <img class="body2_page" src='../public/images/pdf/Lila-15-15.jpg' alt="">
    @endif
    @if($categoria['catid'] == 4)
        <img class="body2_page" src='../public/images/pdf/Verde-15-15.jpg' alt="">
    @endif
    @if($categoria['catid'] == 5)
        <img class="body2_page" src='../public/images/pdf/Morado-15-15.jpg' alt="">
    @endif
    @if($categoria['catid'] == 6)
        <img class="body2_page" src='../public/images/pdf/Celeste-15-15.jpg' alt="">
    @endif
</div>


