<div class="body1_page3"></div>
    @if($categoria['catid'] == 1)
        <img class="body2_page" src='../public/images/pdf/fondofamily.jpg' alt="">
    @endif
    @if($categoria['catid'] == 2)
        <img class="body2_page" src='../public/images/pdf/fondoinfant.jpg' alt="">
    @endif
    @if($categoria['catid'] == 3)
        <img class="body2_page" src='../public/images/pdf/fondoadult.jpg' alt="">
    @endif
    @if($categoria['catid'] == 4)
        <img class="body2_page" src='../public/images/pdf/fondowipes.jpg' alt="">
    @endif
    @if($categoria['catid'] == 5)
        <img class="body2_page" src='../public/images/pdf/fondofem.jpg' alt="">
    @endif
    @if($categoria['catid'] == 6)
        <img class="body2_page" src='../public/images/pdf/fondomulticategoria.jpg' alt="">
    @endif


<!-- @if($categoria['catid'] == 1)
<img class="Img-Icono-Categoria-Pdf" src='../public/images/pdf/iconos/icofamily.jpg'>
@elseif($categoria['catid'] == 2)
<img class="Img-Icono-Categoria-Pdf" src='../public/images/pdf/iconos/icoinfant.jpg'>
@elseif($categoria['catid'] == 3)
<img class="Img-Icono-Categoria-Pdf" src='../public/images/pdf/iconos/icoadult.jpg'>
@elseif($categoria['catid'] == 4)
<img class="Img-Icono-Categoria-Pdf" src='../public/images/pdf/iconos/icowipes.jpg'>
@elseif($categoria['catid'] == 5)
<img class="Img-Icono-Categoria-Pdf" src='../public/images/pdf/iconos/icofem.jpg'>
@elseif($categoria['catid'] == 6)
<img class="Img-Icono-Categoria-Pdf" src='../public/images/pdf/iconos/icomulticategoria.jpg'>
@endif
 -->


<!-- <div class="box2_page" id="color_page3" style="color:{{$categoria['catcolor']}}" >{{$categoria['catnombre']}}</div> -->

<div class="Contenedor-izquierda-pdf">
    <span class="circle_page3" style="color:{{$categoria['catcolor']}}">•</span>
    <span class="box4_page" style="color:{{$categoria['catcolor']}}"><b>{{$sucursal}}<b></span>
</div>
<!-- <div class="box2_page3">Agosto del 2021</div> -->
<div class="box2_page3">{{$fechaPromocion}}</div>
<div class="box4_page3">
    
    


    @foreach($data as $posicionDat => $dat)

        @if($posicionDat == 0)
        <div class="Primera-Columna-Pdf-Promociones">
            <div class="Contenedor-Titulo-Canal-Pdf-Promociones">
                <div 
                    class="Titulo-Canal-Pdf-Promociones"
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
                                <div class="box_table"
                                    id="box_table_color_page3"
                                    style="border: 1px solid {{$categoria['catcolor']}}"
                                >
                                
                                    @if($categoria['catid'] == 1)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/family.jpg'><br>
                                    @elseif($categoria['catid'] == 2)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/infant.jpg'><br>
                                    @elseif($categoria['catid'] == 3)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/adult.jpg'><br>
                                    @elseif($categoria['catid'] == 4)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/wipes.jpg'><br>
                                    @elseif($categoria['catid'] == 5)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/fem.jpg'><br>
                                    @elseif($categoria['catid'] == 6)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/multicategoria.jpg'><br>
                                    @endif
                                    <div class="Txt-No-Hay-Promocion" style="color: {{$categoria['catcolor']}}"  >No hay promoción</div>
                                </div>
                                @endif
                                @if($promocion['cspid'] != 0)
                                <div class="box_table"
                                    id="box_table_color_page3"
                                    style="border: 1px solid {{$categoria['catcolor']}}"
                                >
                                    <div class="Fecha-Expiracion-Promocion-Pdf" style="background:{{$categoria['catcolor']}}">
                                        <div class="Primera-Mitad-Fecha-Expiracion-Promocion-Pdf">
                                            @if($categoria['catid'] == 1)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/calfamily.jpg'>
                                            @elseif($categoria['catid'] == 2)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/calinfant.jpg'>
                                            @elseif($categoria['catid'] == 3)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/caladult.jpg'>
                                            @elseif($categoria['catid'] == 4)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/calwipes.jpg'>
                                            @elseif($categoria['catid'] == 5)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/calfem.jpg'>
                                            @elseif($categoria['catid'] == 6)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/calmulticategoria.jpg'>
                                            @endif
                                        </div>
                                        <div class="Segunda-Mitad-Fecha-Expiracion-Promocion-Pdf">
                                            <div class="Texto-Inicio-Fecha-Expiracion-Pdf">
                                                @if(isset($promocion['fechainicio']))
                                                Ini {{$promocion['fechainicio']}}
                                                @else
                                                01/12
                                                @endif
                                                -
                                                Fin
                                                @if(isset($promocion['fechafinal']))
                                                {{$promocion['fechafinal']}}
                                                @else
                                                30/12
                                                @endif
                                            </div>
                                            <!-- <div class="Texto-Fin-Fecha-Expiracion-Pdf">
                                                @if(isset($promocion['fechafinal']))
                                                {{$promocion['fechafinal']}}
                                                @else
                                                30/12
                                                @endif
                                            </div> -->
                                        </div>

                                        <!-- <div class="Tercera-Mitad-Fecha-Expiracion-Promocion-Pdf">
                                            <div class="Texto-Fin-Fecha-Expiracion-Pdf">
                                                @if(isset($promocion['fechafinal']))
                                                {{$promocion['fechafinal']}}
                                                @else
                                                30/12
                                                @endif
                                            </div>
                                        </div> -->
                                    </div>
                                
                                    <div class="big_text">
                                        <div 
                                            class="text1" 
                                            id="text1_color_page3"
                                            style="color: transparent"
                                        >
                                        <!-- style="color:{{$categoria['catcolor']}}" -->
                                            a
                                            <!-- @if(isset($promocion['cspcantidadplancha']))
                                                {{ number_format(round($promocion['cspcantidadplancha'])) }} Planchas 
                                            @else
                                                {{ number_format(round($promocion['csptotalplancha'])) }} Planchas

                                            @endif -->

                                        </div> 
                                        <div 
                                            class="text2" 
                                            id="text2_color_page3"
                                            style="color: transparent"
                                        >
                                        <!-- style="color:{{$categoria['catcolor']}}" -->
                                            a
                                            <!-- @if(isset($promocion['cspcantidadcombo']))
                                                Total de Combos: {{ number_format(round($promocion['cspcantidadcombo'])) }}
                                            @else
                                                Total de Combos: {{ number_format(round($promocion['csptotalcombo'])) }}
                                            @endif -->
                                        </div> 
                                        <div class="text3">Sell Out</div>
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
                                                        <!-- <div class="text2_product1">{{$producto['prosku']}}</div> -->
                                                    </div>
                                                    @endif
                                                    @endforeach
                                                </th>
                                                <th>
                                                    @foreach($promocion['productosbonificados'] as $posicionProductoBonif => $productobonificado)
                                                    @if($posicionProductoBonif == 0)
                                                    <div class="img_products" style="margin-right:20px">
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
                                                        <!-- <div class="text2_product2">{{$productobonificado['prosku']}}</div> -->
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
        <div class="Segunda-Columna-Pdf-Promociones" >
            <div class="Contenedor-Titulo-Canal-Pdf-Promociones">
                <div 
                    class="Titulo-Canal-Pdf-Promociones"
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
                                <div class="box_table"
                                    id="box_table_color_page3"
                                    style="border: 1px solid {{$categoria['catcolor']}}"
                                >
                                
                                    
                                    @if($categoria['catid'] == 1)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/family.jpg'><br>
                                    @elseif($categoria['catid'] == 2)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/infant.jpg'><br>
                                    @elseif($categoria['catid'] == 3)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/adult.jpg'><br>
                                    @elseif($categoria['catid'] == 4)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/wipes.jpg'><br>
                                    @elseif($categoria['catid'] == 5)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/fem.jpg'><br>
                                    @elseif($categoria['catid'] == 6)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/multicategoria.jpg'><br>
                                    @endif

                                    <div class="Txt-No-Hay-Promocion" style="color: {{$categoria['catcolor']}}">No hay promoción</div>
                                </div>
                                @endif
                                @if($promocion['cspid'] != 0)
                                <div class="box_table"
                                    id="box_table_color_page3"
                                    style="border: 1px solid {{$categoria['catcolor']}}"
                                >
                                    <div class="Fecha-Expiracion-Promocion-Pdf" style="background:{{$categoria['catcolor']}}">
                                        <div class="Primera-Mitad-Fecha-Expiracion-Promocion-Pdf">
                                            @if($categoria['catid'] == 1)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/calfamily.jpg'>
                                            @elseif($categoria['catid'] == 2)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/calinfant.jpg'>
                                            @elseif($categoria['catid'] == 3)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/caladult.jpg'>
                                            @elseif($categoria['catid'] == 4)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/calwipes.jpg'>
                                            @elseif($categoria['catid'] == 5)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/calfem.jpg'>
                                            @elseif($categoria['catid'] == 6)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/calmulticategoria.jpg'>
                                            @endif
                                            
                                        </div>
                                        <div class="Segunda-Mitad-Fecha-Expiracion-Promocion-Pdf">
                                            <div class="Texto-Inicio-Fecha-Expiracion-Pdf">
                                                @if(isset($promocion['fechainicio']))
                                                Ini {{$promocion['fechainicio']}}
                                                @else
                                                01/12
                                                @endif
                                                -
                                                Fin
                                                @if(isset($promocion['fechafinal']))
                                                {{$promocion['fechafinal']}}
                                                @else
                                                30/12
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                
                                    <div class="big_text">
                                        <div 
                                            class="text1" 
                                            id="text1_color_page3"
                                            style="color: transparent"
                                        >
                                            a
                                            <!-- @if(isset($promocion['cspcantidadplancha']))
                                                {{ number_format(round($promocion['cspcantidadplancha'])) }} Planchas 
                                            @else
                                                {{ number_format(round($promocion['csptotalplancha'])) }} Planchas

                                            @endif -->

                                        </div> 
                                        <div 
                                            class="text2" 
                                            id="text2_color_page3"
                                            style="color: transparent"
                                        >
                                            a
                                            <!-- @if(isset($promocion['cspcantidadcombo']))
                                                Total de Combos: {{ number_format(round($promocion['cspcantidadcombo'])) }}
                                            @else
                                                Total de Combos: {{ number_format(round($promocion['csptotalcombo'])) }}
                                            @endif -->
                                        </div> 
                                        <div class="text3">Sell Out</div>
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
                                                        <!-- <div class="text2_product1">{{$producto['prosku']}}</div> -->
                                                    </div>
                                                    @endif
                                                    @endforeach
                                                </th>
                                                <th>
                                                    @foreach($promocion['productosbonificados'] as $posicionProductoBonif => $productobonificado)
                                                    @if($posicionProductoBonif == 0)
                                                    <div class="img_products" style="margin-right:20px">
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
                                                        <!-- <div class="text2_product2">{{$productobonificado['prosku']}}</div> -->
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

        @if($posicionDat == 2)
        <div class="Tercera-Columna-Pdf-Promociones">
            <div class="Contenedor-Titulo-Canal-Pdf-Promociones">
                <div 
                    class="Titulo-Canal-Pdf-Promociones"
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
                                <div class="box_table"
                                    id="box_table_color_page3"
                                    style="border: 1px solid {{$categoria['catcolor']}}"
                                >
                                
                                    @if($categoria['catid'] == 1)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/family.jpg'><br>
                                    @elseif($categoria['catid'] == 2)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/infant.jpg'><br>
                                    @elseif($categoria['catid'] == 3)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/adult.jpg'><br>
                                    @elseif($categoria['catid'] == 4)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/wipes.jpg'><br>
                                    @elseif($categoria['catid'] == 5)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/fem.jpg'><br>
                                    @elseif($categoria['catid'] == 6)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/multicategoria.jpg'><br>
                                    @endif
                                    <div class="Txt-No-Hay-Promocion" style="color: {{$categoria['catcolor']}}" >No hay promoción</div>
                                </div>
                                @endif
                                @if($promocion['cspid'] != 0)
                                <div class="box_table"
                                    id="box_table_color_page3"
                                    style="border: 1px solid {{$categoria['catcolor']}}"
                                >
                                    <div class="Fecha-Expiracion-Promocion-Pdf" style="background:{{$categoria['catcolor']}}">
                                        <div class="Primera-Mitad-Fecha-Expiracion-Promocion-Pdf">
                                            @if($categoria['catid'] == 1)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/calfamily.jpg'>
                                            @elseif($categoria['catid'] == 2)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/calinfant.jpg'>
                                            @elseif($categoria['catid'] == 3)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/caladult.jpg'>
                                            @elseif($categoria['catid'] == 4)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/calwipes.jpg'>
                                            @elseif($categoria['catid'] == 5)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/calfem.jpg'>
                                            @elseif($categoria['catid'] == 6)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/calmulticategoria.jpg'>
                                            @endif
                                        </div>
                                        <div class="Segunda-Mitad-Fecha-Expiracion-Promocion-Pdf">
                                            <div class="Texto-Inicio-Fecha-Expiracion-Pdf">
                                                @if(isset($promocion['fechainicio']))
                                                Ini {{$promocion['fechainicio']}}
                                                @else
                                                01/12
                                                @endif
                                                -
                                                Fin
                                                @if(isset($promocion['fechafinal']))
                                                {{$promocion['fechafinal']}}
                                                @else
                                                30/12
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="big_text">
                                        <div 
                                            class="text1" 
                                            id="text1_color_page3"
                                            style="color: transparent"
                                        >
                                            a
                                            <!-- @if(isset($promocion['cspcantidadplancha']))
                                                {{ number_format(round($promocion['cspcantidadplancha'])) }} Planchas 
                                            @else
                                                {{ number_format(round($promocion['csptotalplancha'])) }} Planchas

                                            @endif -->

                                        </div> 
                                        <div 
                                            class="text2" 
                                            id="text2_color_page3"
                                            style="color: transparent"
                                        >
                                            a
                                            <!-- @if(isset($promocion['cspcantidadcombo']))
                                                Total de Combos: {{ number_format(round($promocion['cspcantidadcombo'])) }}
                                            @else
                                                Total de Combos: {{ number_format(round($promocion['csptotalcombo'])) }}
                                            @endif -->
                                        </div> 
                                        <div class="text3">Sell Out</div>
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
                                                        <!-- <div class="text2_product1">{{$producto['prosku']}}</div> -->
                                                    </div>
                                                    @endif
                                                    @endforeach
                                                </th>
                                                <th>
                                                    @foreach($promocion['productosbonificados'] as $posicionProductoBonif => $productobonificado)
                                                    @if($posicionProductoBonif == 0)
                                                    <div class="img_products" style="margin-right:20px">
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
                                                        <!-- <div class="text2_product2">{{$productobonificado['prosku']}}</div> -->
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

        @if($posicionDat == 3)
        <div class="Cuarta-Columna-Pdf-Promociones">
            <div class="Contenedor-Titulo-Canal-Pdf-Promociones">
                <div 
                    class="Titulo-Canal-Pdf-Promociones"
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
                                <div class="box_table"
                                    id="box_table_color_page3"
                                    style="border: 1px solid {{$categoria['catcolor']}}"
                                >
                                
                                    @if($categoria['catid'] == 1)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/family.jpg'><br>
                                    @elseif($categoria['catid'] == 2)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/infant.jpg'><br>
                                    @elseif($categoria['catid'] == 3)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/adult.jpg'><br>
                                    @elseif($categoria['catid'] == 4)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/wipes.jpg'><br>
                                    @elseif($categoria['catid'] == 5)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/fem.jpg'><br>
                                    @elseif($categoria['catid'] == 6)
                                    <img class="img-no-hay-promocion" src='../public/images/pdf/nohaypromocion/multicategoria.jpg'><br>
                                    @endif
                                    <div class="Txt-No-Hay-Promocion" style="color: {{$categoria['catcolor']}}">No hay promoción</div>
                                </div>
                                @endif
                                @if($promocion['cspid'] != 0)
                                <div class="box_table"
                                    id="box_table_color_page3"
                                    style="border: 1px solid {{$categoria['catcolor']}}"
                                >
                                    <div class="Fecha-Expiracion-Promocion-Pdf" style="background:{{$categoria['catcolor']}}">
                                        <div class="Primera-Mitad-Fecha-Expiracion-Promocion-Pdf">
                                            @if($categoria['catid'] == 1)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/calfamily.jpg'>
                                            @elseif($categoria['catid'] == 2)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/calinfant.jpg'>
                                            @elseif($categoria['catid'] == 3)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/caladult.jpg'>
                                            @elseif($categoria['catid'] == 4)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/calwipes.jpg'>
                                            @elseif($categoria['catid'] == 5)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/calfem.jpg'>
                                            @elseif($categoria['catid'] == 6)
                                            <img class="img-calendario-expiracion-pdf" src='../public/images/pdf/nohaypromocion/calmulticategoria.jpg'>
                                            @endif
                                        </div>
                                        <div class="Segunda-Mitad-Fecha-Expiracion-Promocion-Pdf">
                                            <div class="Texto-Inicio-Fecha-Expiracion-Pdf">
                                                @if(isset($promocion['fechainicio']))
                                                Ini {{$promocion['fechainicio']}}
                                                @else
                                                01/12
                                                @endif
                                                -
                                                Fin
                                                @if(isset($promocion['fechafinal']))
                                                {{$promocion['fechafinal']}}
                                                @else
                                                30/12
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="big_text">
                                        <div 
                                            class="text1" 
                                            id="text1_color_page3"
                                            style="color: transparent"
                                        >
                                            a
                                            <!-- @if(isset($promocion['cspcantidadplancha']))
                                                {{ number_format(round($promocion['cspcantidadplancha'])) }} Planchas 
                                            @else
                                                {{ number_format(round($promocion['csptotalplancha'])) }} Planchas

                                            @endif -->

                                        </div> 
                                        <div 
                                            class="text2" 
                                            id="text2_color_page3"
                                            style="color: transparent"
                                        >
                                            a
                                            <!-- @if(isset($promocion['cspcantidadcombo']))
                                                Total de Combos: {{ number_format(round($promocion['cspcantidadcombo'])) }}
                                            @else
                                                Total de Combos: {{ number_format(round($promocion['csptotalcombo'])) }}
                                            @endif -->
                                        </div> 
                                        <div class="text3">Sell Out</div>
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
                                                        <!-- <div class="text2_product1">{{$producto['prosku']}}</div> -->
                                                    </div>
                                                    @endif
                                                    @endforeach
                                                </th>
                                                <th>
                                                    @foreach($promocion['productosbonificados'] as $posicionProductoBonif => $productobonificado)
                                                    @if($posicionProductoBonif == 0)
                                                    <div class="img_products" style="margin-right:20px">
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
                                                        <!-- <div class="text2_product2">{{$productobonificado['prosku']}}</div> -->
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

    <!-- <div class="Texto-Numero-Pagina-Pdf" style="color:{{$categoria['catcolor']}}" > -->
        
        @if(strlen($pagina+1) == 1)
        <div class="Texto-Numero-Pagina-Pdf" style="color:{{$categoria['catcolor']}}" >
            {{$pagina+1}}
        </div>
        @else
        <div class="Texto-Numeros-Pagina-Pdf" style="color:{{$categoria['catcolor']}}" >
            {{$pagina+1}}
        </div>
        @endif
    <!-- </div> -->
</div>


