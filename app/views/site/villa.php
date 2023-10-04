<? use yii\easyii\modules\text\api\Text; ?>
    <? include '_inc_br.php'; ?>
    <div class="container">
        <div class="row text-center">
            <h1 class="text-uppercase col-12 text-center mt-0 mb-40 tt"><?=$theEntry->title;?></h1>
            <div class="summary mx-auto text-center mb-40"><?=$theEntry->model->content; ?></div>
                <? foreach($theEntry->items() as $k => $v) : ?>
                <div class="villa-item col-12 text-left col-sm-12 col-lg-6 p-md-3 pl-0 pl-sm-0 <?=$k%2 == 0 ? 'pl-md-0' : 'pr-md-0' ?>">
                    <a class="opc-8" href="/<?=$v->slug?>">
               <? if(isset($v->photosArray['summary'][0])) : 
                    $vimg = $v->photosArray['summary'][0];
                ?>

                        <!-- Slides -->
                        <img alt="<?=$vimg->description?>" src="/timthumb.php?src=<?=$vimg->image ?>&w=560&zc=0&q=80" class="w-100">
                        <? endif; ?>
                <h2 class="tt my-40 text-capitalize"><?=$v->title; ?></h2></a>
                <p class="summary mb-40 text-left"><?=$v->model->summary ?></p>
                <div class="row">
                    <? $options = \app\modules\libraries\api\Catalog::items(['where' => ['IN', 'item_id', $v->data->optionssum],
                        'orderBy' => [new \yii\db\Expression('FIELD (item_id, ' . implode(',',$v->data->optionssum) . ')')]]); ?>
                    <? foreach($options as $ko => $vo) : ?>
                    <? if($ko == 4) break; ?>
                    <div class="col-auto icon text-center"><img class="auto-size" src="<?=$vo->photosArray['icon'][0]->image ?>" alt="<?=$vo->photosArray['icon'][0]->description ?>">
                        <p class="tt-icon"><?=$vo->title?></p>
                    </div>

                    <? endforeach; ?>
                </div>
                <a class="mt-20 btn-basic d-inline-flex text-uppercase" href="/<?=$this->context->otherMenu['booking']->slug ?>?room=<?=$v->title?>"><?=Text::get('dat-phong');?></a><a class="read-more  ml-4" href="/<?=$v->slug?>"><?=Text::get('xem-them');?></a>
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
$this->registerCss('.villa-item .tt{ font-size: 22px;} .villa-item{margin-bottom: 130px;} .summary{ min-height: 66px;}');
?>