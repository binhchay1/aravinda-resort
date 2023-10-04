       <? use yii\easyii\modules\text\api\Text; ?>
    <? include '_inc_br.php'; ?>
    <div class="container mb-130">
        <div class="row text-center  mb-130">
            <h1 class="text-uppercase col-12 text-center mt-0 mb-40 tt"><?=$theEntry->title;?></h1>
                
                <div class="swiper-container">
                    <!-- Additional required wrapper -->
                    <div class="swiper-wrapper">
                    	<? if(!empty($theEntries = $theEntry->items())) : ?>
                    	<? foreach($theEntries as $k => $v) : ?>
                        <? if(isset($v->photosArray['banner'])) : ?>
                        	<? $vimg =  $v->photosArray['banner'][0]; ?>
                        <!-- Slides -->
                        <div class="swiper-slide"><img alt="<?=$vimg->description?>" src="/timthumb.php?src=<?=$vimg->image ?>&w=1140&zc=0&q=80" class="w-100">
                        <p class="tt mt-40 mb-40 w-100 text-center no-uppercase"><?=$v->title;?></p>

                        <div class="mt-40"><?=$v->description; ?></div>

                        </div>
                        <? endif; ?>
                    	<? endforeach; ?>

                    <? endif; ?>
                    </div>
                    <!-- If we need pagination -->
                    <!-- <div class="swiper-pagination"></div> -->

                    <!-- If we need navigation buttons -->
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
                <div class="description mt-40 mx-auto w-700">
                    <?=$theEntry->model->content ?>
                </div>
        </div>
        <div class="row others justify-content-center">
            <p class="tt mt-0 mb-40 w-100 text-center"><?=Text::get('uu-dai-khac');?></p>
            <? foreach($others as $ko => $vo) : ?>
                <div class="col-12 col-sm-12 col-md-4 text-left">
                    <a class="opc-8" href="/<?=$vo->slug?>">
                    <? if(!empty($vo->photosArray['summary'])) : ?>
                    <img class="w-100" alt="<?=$vo->photosArray['summary'][0]->description ?>" src="/timthumb.php?src=<?=$vo->photosArray['summary'][0]->image ?>&w=367&q=80&zc=0"/>
                	<? endif; ?>
                    <p class="tt text-capitalize my-40"><?=$vo->title?></p>
                </a>
                    <p class="summary mb-40 text-left"><?=$vo->summary?></p>
                    <a class="mt-20 btn-basic d-inline-flex text-uppercase" href="/<?=$this->context->otherMenu['booking']->slug ?>?promo=<?=$vo->title?>"><?=Text::get('dat-phong');?></a>
                    <a class="read-more  ml-4" href="/<?=$vo->slug?>"><?=Text::get('xem-them');?></a>
                </div>
            <? endforeach; ?>
        </div>
        
        
    </div>

    

<? $js = <<<JS
$('.view-down').click(function(){
	$('html, body').animate({
        scrollTop: $(".form-booking").offset().top - 90
    }, 700);
		// $("body").scrollTop($(".form-booking"), 500); 
});
$(document).ready(function () {
    //initialize swiper when document ready
    var mySwiper = new Swiper ('.swiper-container', {
      // Optional parameters
      loop: false,
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
    })
  });
JS;
$this->registerJs($js);
$this->registerCss('
    .options-room{
        max-width: 700px;
    }
    .swiper-button-next, .swiper-button-prev{
    	top: 127px;
    }
    .no-uppercase{
            text-transform: none;
    }
    .swiper-button-next.swiper-button-disabled, .swiper-button-prev.swiper-button-disabled{
        display: none;
    }
');
?>