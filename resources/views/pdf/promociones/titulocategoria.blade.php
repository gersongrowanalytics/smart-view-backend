

@if ($data['catid'] == 1)
<img class="body_page" src='../public/images/pdf/Azul-portada-16.jpg' alt="">
<img class="img_body_page" src="../public/images/pdf/family.trazo-39.jpg" alt="">
@endif

@if ($data['catid'] == 2)
<img class="body_page" src='../public/images/pdf/Rojo-portada-16.jpg' alt="">
<img class="img_body_page" src="../public/images/pdf/infant.trazo-39.jpg" alt="">
@endif

@if ($data['catid'] == 3)
<img class="body_page" src='../public/images/pdf/Lila-portada-16.jpg' alt="">
<img class="img_body_page" src="../public/images/pdf/Adult.trazo-39.jpg" alt="">
@endif

@if ($data['catid'] == 4)
<img class="body_page" src='../public/images/pdf/Verde-portada-16.jpg' alt="">
<img class="img_body_page" src="../public/images/pdf/wipes.trazo-39.jpg" alt="">
@endif

@if ($data['catid'] == 5)
<img class="body_page" src='../public/images/pdf/Morado-portada-16.jpg' alt="">
<img class="img_body_page" src="../public/images/pdf/fem.trazo-39.jpg" alt="">
@endif

@if ($data['catid'] == 6)
<img class="body_page" src='../public/images/pdf/Celeste-portada-16.jpg' alt="">
<img class="img_body_page" src="../public/images/pdf/multicategoria.trazo-39.jpg" alt="">
@endif


<div class="box1_page">{{$data['catnombre']}}</div>