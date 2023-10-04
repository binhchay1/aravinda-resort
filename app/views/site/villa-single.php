<? use yii\easyii\modules\text\api\Text; ?>
    <? include '_inc_br.php'; ?>
    <div class="container">
        <div class="row text-center">
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
                <p class="price my-40 d-block w-100 w-700
                "><span class="mb-40"><?=Text::get('gia-phong');?>: </span><?=isset($theEntry->data->price) ? $theEntry->data->price : '' ?> <span> / </span><?=Text::get('roomnight');?></p>
                <p class="acre d-block w-100 mb-40"><span><?=Text::get('dien-tich');?>: </span><?=isset($theEntry->data->acre) ? $theEntry->data->acre : '' ?></p>
                <div class="description mb-130 mx-auto text-center w-700">
                    <?=$theEntry->description ?>
                </div>
                
                <div class="row options-room mx-auto mb-txt-130">
                    <h3 class="w-100 tt mt-0 mb-40 color-888"><?=Text::get('tien-nghi-phong');?></h3>
                    <? $options = \app\modules\libraries\api\Catalog::items(['where' => ['IN', 'item_id', $theEntry->data->options],
                        'orderBy' => [new \yii\db\Expression('FIELD (item_id, ' . implode(',',$theEntry->data->options) . ')')]
                ]); ?>
                    <? foreach($options as $vo) : ?>
                    <div class="col-12 col-sm-12 col-md-6 text-left ">
                        <img class="auto-size" src="<?=$vo->photosArray['icon'][0]->image ?>" alt="<?=$vo->photosArray['icon'][0]->description ?>">
                        <span class="tt-icon ml-3"><?=$vo->title?></span>
                    </div>

                    <? endforeach; ?>
                </div>
                <div class="row">
                </div>
                <div class="dkgia  w-700 mb-130">
                    <h3 class="tt mt-0  mb-40 color-888"><?=Text::get('dieu-kien-gia');?></h3>
                    <? if(Yii::$app->language  == 'en') : ?>
                    <?=isset($theEntry->data->checkprice) ? $theEntry->data->checkprice : '' ?>
                    <? endif; ?>
                    <? if(Yii::$app->language  == 'fr') : ?>
                    <?=isset($theEntry->data->checkpricefr) ? $theEntry->data->checkpricefr : '' ?>
                    <? endif; ?>
                    <? if(Yii::$app->language  == 'vi') : ?>
                    <?=isset($theEntry->data->checkpricevi) ? $theEntry->data->checkpricevi : '' ?>
                    <? endif; ?>
                </div>
                
            
        </div>
        
        
    </div>

    <div class="container-fluid form-booking mb-130" id="form-booking">
        <div class="container">
            <? include('_inc_booking_engine.php') ?>
        </div>
    </div>
    <div class="container">
        <div class="row others  text-left mb-130">
                <p class="tt mt-0 mb-40 w-100 text-center text-uppercase"><?=Text::get('loai-phong-khac');?></p>
                <? foreach($others as $ko => $vo) : ?>
                    <div class="col-12 col-sm-12 col-md-4 mb-40 mb-md-0">
                        <a class="opc-8" href="/<?=$vo->slug ?>">
                        <img class="w-100" alt="<?=$vo->photosArray['summary'][0]->description ?>" src="/timthumb.php?src=<?=$vo->photosArray['summary'][0]->image ?>&w=367&q=80&zc=0"/>
                        <p class="tt text-capitalize my-40"><?=$vo->title?></p>
                    </a>
                        <p class="summary mb-40 text-left"><?=$vo->model->summary?></p>
                        <a class="read-more" href="/<?=$vo->slug ?>"><?=Text::get('xem-them');?></a>
                    </div>
                <? endforeach; ?>
            </div>
    </div>

<?
$room = $theEntry->title;
 $js = <<<JS
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
    .price{
        font-size: 22px;
    }
    .price span{
        color: #888
    }
');
?>