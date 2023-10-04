<? use yii\easyii\modules\text\api\Text; ?>

    <div class="container">
        <div class="row text-center">
            <h1 class="text-uppercase col-12 text-center mt-60 mb-40 tt"><?=strip_tags($theEntry->text);?></h1>
            <div class="swiper-container swiper-container1">
                    <!-- Additional required wrapper -->
                    <div class="swiper-wrapper">
                        <? foreach ($dataDes as $kd => $vd) : ?>
                            <div class="swiper-slide">
                            <? if(isset($vd->photosArray['on-home'])) :
                                    $img = $vd->photosArray['on-home'][0];
                             ?>
                            <img class="focus-center" alt="<?=$img->description ?>" src="<?=$img->image ?>">
                                <? endif; ?>
                        </div>
                        <? endforeach; ?>
                    </div>
                    <!-- If we need pagination -->
                    <div class="swiper-pagination"></div>

                    <!-- If we need navigation buttons -->
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
            </div>
           
        </div>
        <div class="row mb-130">
            <ul class="nav nav-pills centered col-12 my-40 swiper-pagination1">
                <? foreach ($dataDes as $kd => $vd) : ?>
                    <li  class="<?=!$kd ? 'active' : '' ?>"  data-target="#carousel-<?=$kd+1 ?>" data-slide-to="<?=$kd ?>"><a href="#des-<?=$vd->model->item_id?>" data-toggle="tab"><?=$vd->title ?></a></li>
                <? endforeach; ?>

            </ul>
            <div class="tab-content justify-content-center mb-0">
                <? foreach ($dataDes as $kd => $vd) : ?>
                    <div class="tab-pane <?=!$kd ? 'active' : '' ?> fade in col-12 col-lg-9 text-center mx-auto float-lg-none" id="des-<?=$vd->model->item_id?>"><?=$vd->model->summary ?><a class="read-more d-block mt-40" href="/<?=$this->context->menu[10]->slug ?>#<?=!$kd ? 'concept' : 'destination' ?>"><?=Text::get('xem-them');?></a></div>
                <? endforeach; ?>
            </div>

        </div>
        <div class="row room mt-0 mb-130">
            <? $room = $this->context->menu[11]; ?>
            <h3 class="text-uppercase col-12 text-center tt mt-0 mb-40"><?=$room->title;?></h3>
            <div class="swiper-container swiper-container2">
                    <!-- Additional required wrapper -->
                    <div class="swiper-wrapper">
                        <? foreach($room->items as $kit => $vit) : ?>
                            <div class="swiper-slide">
                            <? if(isset($vit->photosArray['galery'])) : 
                            $vimg = $vit->photosArray['galery'][0];
                            ?>
                            <img class="focus-center" alt="<?=$vimg->description ?>" src="<?=$vimg->image ?>">
                                <? endif; ?>
                        </div>
                        <? endforeach; ?>
                    </div>
                    <!-- If we need pagination -->
                    <div class="swiper-pagination"></div>

                    <!-- If we need navigation buttons -->
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
            </div>
            <ul class="nav nav-pills tab-room-villa centered scroll col-12 my-40 swiper-pagination2">
                <? foreach($room->items as $kit => $vit) : ?>
                <li class="<?=!$kit ? 'active' : '' ?>" data-target="#carousel-<?=$kit+1 ?>" data-slide-to="<?=$kit+1 ?>"><a href="#room-<?=$vit->item_id ?>" data-toggle="tab" ><?=$vit->title ?></a><span><?=$vit->data->acre ?></span></li>
                <? endforeach; ?>
               
            </ul>
            <div class="tab-content col-12">
                <? foreach($room->items as $kit => $vit) : ?>
               <div class="tab-pane <?=!$kit ? 'active' : '' ?> fade in col-12 col-lg-9 mx-auto text-center float-none" id="room-<?=$vit->item_id?>"><?=$vit->summary ?>
               <a href="/<?=$vit->slug ?>" class="read-more mt-40 d-block"><?=Text::get('xem-them');?></a></div>
                <? endforeach; ?>
            </div>
                
        </div>
        <div class="exp mt-0 mb-130">
            <? $tn =  $this->context->menu[13];?>
            <h2 class="text-uppercase col-12  text-center mt-0 mb-40 tt"><?=$tn->title?></h2>
            <div class="row scroll">
                <? foreach ($dataGas as $ktn => $vtn) :?>
                   <div class="col-11 col-md-11 col-lg-4 pr-4 pr-md-0">
                        <a href="/<?=$vtn->slug ?>">
                        <? $vimg = '';
                        if(isset($vtn->photosArray['summary'])) : 
                            $vimg =  $vtn->photosArray['summary'][0];?>

                        <img alt="<?=$vimg->description?>" class="focus-center" src="/timthumb.php?src=<?=$vimg->image?>&w=367&q=80&zc=0">
                        <? endif; ?>
                        <p class="row-1 my-40"><?=$vtn->title; ?></p>
                        </a>
                        <p class="row-2 mb-40"><?=$vtn->model->summary ?></p>
                       <a href="/<?=$vtn->slug ?>" class="read-more mt-40 d-block"><?=Text::get('xem-them');?></a>
                    </div>
                <? endforeach; ?>
                
               
            </div>
        </div>
        <div class="row promo mt-0 mb-50">
            <? $pr = $this->context->menu[14];?>
            <h3 class="text-uppercase text-center col-12 tt mt-0 mb-40"><?=$pr->title;?></h3>
            <? foreach ($pr->children() as $ktn => $vtn) :?>
            <div class="col-12 col-lg-6 mb-80 ">
                <a href="/<?=$vtn->slug ?>">
                <? $vimg = '';
                        if(isset($vtn->photosArray['summary'])) : 
                            $vimg =  $vtn->photosArray['summary'][0];?>

                        <img alt="<?=$vimg->description?>" class="focus-center" src="/timthumb.php?src=<?=$vimg->image?>&w=560&q=80&zc=0">
                <? endif; ?>
                <p class="row-1 my-40"><?=$vtn->title; ?></p>
            </a>
                <p class="row-2 mb-40 pr-5"><?=$vtn->summary ?></p>
                
             
                <a class="read-more  ml-4" href="/<?=$vtn->slug?>"><?=Text::get('xem-them');?></a>
            </div>
            <? endforeach; ?>
        </div>
    </div>

    <div class="container-fluid mt-0 p-0 mb-130" style="overflow: hidden;">
        <div class="row mt-0 mt-0 ">
            <div class="swiper-container swiper-container3 swiper-container-basic  location-slide">
                    <!-- Additional required wrapper -->
                    <div class="swiper-wrapper ">
                        <? foreach ($dataUse as $kdu => $vdu) :?>
                            <div class="swiper-slide item">
                            <? if(isset($vdu->photosArray['on-home'])) : 
                            $vimg = $vdu->photosArray['on-home'][0];
                            ?>
                            <img class="focus-center" alt="<?=$vimg->description ?>" src="<?=$vimg->image ?>">
                                <? endif; ?>
                            <div class="text">
                                <p class="location"><?=$vdu->title; ?></p>
                                <p class="detail"><?=$vdu->model->summary; ?></p>
                                <a href="/<?=$vdu->slug ?>" class="read-more"><?=Text::get('xem-them');?></a>
                            </div>    
                        </div>
                        <? endforeach; ?>
                    </div>
                    <!-- If we need pagination -->
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-pagination"></div>
            </div>
            
        </div>
    </div>

<? $js = <<<JS
$('.view-down').click(function(){
	$('html, body').animate({
        scrollTop: $(".form-booking").offset().top - 90
    }, 700);
		// $("body").scrollTop($(".form-booking"), 500); 
});
$('#carousel-2 .carousel-indicators li').click(function(){
	var index = $(this).index();
	$('.tab-room-villa > li:eq('+index+') a').trigger('click');
	});
//initialize swiper when document ready
var mySwiperHeader = new Swiper ('.swiper-container-header', {
  // Optional parameters
  loop: true,
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
  
});  
var mySwiper1 = new Swiper ('.swiper-container1', {
  // Optional parameters
  loop: true,
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
   pagination: {
    el: '.swiper-pagination',
    clickable: true,
  },
}); 
mySwiper1.on('slideChange', function () {
    $('.swiper-pagination1 li:eq('+mySwiper1.realIndex+') a').trigger('click');
}); 
$('.swiper-pagination1 li').click(function(){
    $('.swiper-container1 .swiper-pagination span:eq('+$(this).index()+')').trigger('click');
})

var mySwiper2 = new Swiper ('.swiper-container2', {
  // Optional parameters
  loop: true,
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
   pagination: {
    el: '.swiper-pagination',
    clickable: true,
  },
}); 
mySwiper2.on('slideChange', function () {
    $('.swiper-pagination2 li:eq('+mySwiper2.realIndex+') a').trigger('click');
}); 
$('.swiper-pagination2 li').click(function(){
    $('.swiper-container2 .swiper-pagination span:eq('+$(this).index()+')').trigger('click');
    var left = $(this).position().left;
    $('.swiper-pagination2').scrollLeft(left);
})
var mySwiper3 = new Swiper ('.swiper-container3', {
  // Optional parameters
  loop: true,
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
   pagination: {
    el: '.swiper-pagination',
    clickable: true,
  },
}); 

JS;
$this->registerJs($js);
?>
