<? use yii\easyii\modules\text\api\Text; ?>
    <? include '_inc_br.php'; ?>
    <div class="container mb-130">
        <div class="row text-center mb-130 px-4">
            <h1 class="text-uppercase col-12 text-center mt-0 mb-40 tt"><?=$theEntry->title;?></h1>
            <div class="w-700 text-left">
                <?=$theEntry->description;?>
            </div>   
        </div>
        <div class="row others justify-content-center">
            <p class="tt mt-0 mb-40 w-100 text-center"><?=Text::get('xem-them');?></p>
            <? foreach($others as $ko => $vo) : ?>
                <div class="col-12 col-sm-12 col-md-4 text-left <?=$ko != count($others) -1 ? 'mb-80' : 'mb-0'?> mb-sm-0">
                    <a class="opc-8" href="/<?=$vo->slug?>">
                    <?
                $vimg = ''; 
                if(isset($vo->photosArray['summary'][0])) $vimg = $vo->photosArray['summary'][0]; ?>
                    <img class="w-100" alt="<?=$vimg ? $vimg->description : ''?>" src="/timthumb.php?src=<?=$vimg ? $vimg->image : ''?>&w=367&q=80&zc=0"/>
                    <p class="mt-40  tt text-capitalize mb-40"><?=$vo->title?></p>
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
    .price{
        font-size: 22px;
    }
    .price span{
        color: #888
    }
');
?>