<? use yii\easyii\modules\text\api\Text; ?>
<?php $this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css', ['depends' => 'app\assets\AppAsset', 'position' => $this::POS_END]) ?>
<?php $this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.pack.js', ['depends' => 'app\assets\AppAsset', 'position' => $this::POS_END]) ?>
    <? include '_inc_br.php'; ?>
    <div class="container mb-130">
        <div class="row text-center">
            <h1 class="text-uppercase col-12 text-center mt-0 mb-40 tt"><?=$theEntry->title;?></h1>
            <div class="summary mx-auto text-center mb-40 d-flex justify-content-center align-items-center p-20">
                <span><?=Text::get('lien-he') ?></span>
                <div class="float-right ml-40 text-left">
                <?=$theEntry->model->content; ?>
                </div>    
            </div>
            <div class="row m-0 entry-body w-100">
                <? foreach($theEntry->children(1) as $k => $v) : ?>
                    <div class="col-12 col-sm-6 text-left pl-0 mb-40 pr-5">
                        <p class="tt"><?=$v->title ?> </p>
                        <? foreach($v->items as $ki => $vi) : ?>
                            <p class="sub-tt"><?=$vi->title ?> </p>
                            <div class="summary-content row m-0 mb-20 d-flex align-items-start">
                            <span class="w-455  d-inline-block mr-20"><?=$vi->summary ?>...</span>
                            <a class="fancybox m-0 float-right read-more d-block"  href="#popup-<?=$vi->item_id ?>"><?=Text::get('xem-them') ?></a>
                        </div>
                        <? endforeach; ?>
                        
                    </div>
                <? endforeach;?>
                <div class="popups" style="display: none;">
                    <? foreach($theEntry->children(1) as $k => $v) : ?>
                        <? foreach($v->items as $ki => $vi) : ?>
                        <div class="popup-td" id="popup-<?=$vi->item_id ?>">

                                    <p class="sub-tt"><?=$vi->title ?> </p>
                                    <div class="content-item">
                                        <?=$vi->description; ?>
                                    </div>
                        </div>
                         <? endforeach;?>
                    <? endforeach;?>
                </div>
            </div>
        </div>
        
        
    </div>

    

<? $js = <<<JS
$('.fancybox').fancybox({
    width: 800,
    autoSize: false,
    padding: 65
    });
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
$this->registerCss('.villa-item .tt{ font-size: 22px;} .villa-item{margin-bottom: 130px;}
.popup-td .tt, .entry-body .sub-tt{
    font: 15.5px Lora,serif;
    font-weight: bold;
    text-transform: none;
    color: #000;
}
.popup-td .sub-tt, .entry-body .tt{
    font: 22px Lora,serif;
    color: #5a8d8d;
    text-transform: none;
}
.summary{
    background: #5a8d8d;
    width: 100%;
    max-width: 100%;
    color: #fff;
}
.summary > span{
    font-size: 20px;
}
.summary .float-right p{
    margin: 0;

}
.summary-content .w-455{
    display: inline-block;
    width: 400px;
}');
?>