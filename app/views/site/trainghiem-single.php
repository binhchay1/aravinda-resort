   <? use yii\easyii\modules\text\api\Text; ?>
    <? include '_inc_br.php'; ?>
    <div class="container mb-130">
        <div class="row text-center  mb-130">
            <h1 class="text-uppercase col-12 text-center mt-0 mb-40 tt"><?=$theEntry->title;?></h1>
                
                <div class="swiper-container">
                    <!-- Additional required wrapper -->
                    <div class="swiper-wrapper">
                        <? foreach($theEntry->photosArray['galery'] as $vimg) : ?>
                        <!-- Slides -->
                        <div class="swiper-slide"><img alt="<?=$vimg->description?>" src="/timthumb.php?src=<?=$vimg->image ?>&w=1140&zc=0&q=80" class="w-100"></div>
                        <? endforeach; ?>
                    </div>
                    <!-- If we need pagination -->
                    <div class="swiper-pagination"></div>

                    <!-- If we need navigation buttons -->
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
                <div class="description w-700 mt-40">
                    <?=$theEntry->description ?>
                </div>
        </div>
        <div class="row others">
            <p class="tt mt-0 mb-40 w-100 text-center"><?=Text::get('phong-vi-khac');?></p>
            <? foreach($others as $ko => $vo) : ?>
                <div class="col-12 col-sm-12 col-md-4 text-left <?=$ko != count($others) -1 ? 'mb-80' : 'mb-0'?> mb-sm-0">
                    <?
                $vimg = ''; 
                if(isset($vo->photosArray['summary'][0])) $vimg = $vo->photosArray['summary'][0]; ?>
                    <a class="opc-8" href="/<?=$vo->slug?>">
                    <img class="w-100" alt="<?=$vimg ? $vimg->description : ''?>" src="/timthumb.php?src=<?=$vimg ? $vimg->image : ''?>&w=367&q=80&zc=0"/>
                    <p class="tt text-capitalize my-40"><?=$vo->title?></p>
                </a>
                    <p class="summary mb-40 text-left"><?=$vo->model->summary?></p>
                    <a class="read-more" href="/<?=$vo->slug ?>"><?=Text::get('xem-them');?></a>
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
      loop: true,
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
');
?>