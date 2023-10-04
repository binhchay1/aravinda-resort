    <? use yii\easyii\modules\text\api\Text; ?>
    <? include '_inc_br.php'; ?>
    <div class="container">
        <div class="row text-cente no-gutters amthuc-row">
            <h1 class="text-uppercase col-12 text-center mt-0 mb-40 tt"><?=$theEntry->title;?></h1>
            <div class="summary mx-auto text-center mb-40"><?=$theEntry->model->content; ?></div>
                <? foreach($theEntry->items() as $k => $v) : ?>
                <? if($k == 2) break;?>
                <? $vimg = $v->photosArray['summary'][1]; ?>
                <div class="mb-130 pb-0 text-left villa-item col-12 col-sm-12 col-lg-<?=$k < 2 ? 6 : 4 ?> px-3 <?=$k%2 == 0 && $k < 2 ? 'pl-md-0' : 'pr-md-0' ?> ">
                <a class="opc-8" href="/<?=$v->slug?>">
                <img alt="<?=$vimg->description?>" src="/timthumb.php?src=<?=$vimg->image ?>&w=560&zc=0&q=80" class="w-100">
                <h2 class="tt my-40 text-capitalize"><?=$v->title; ?></h2></a>
                <p class="summary mb-40"><?=$v->model->summary ?></p>
                
                <a class="mt-20 btn-basic" href="/<?=$v->slug?>"><?=Text::get('xem-them');?></a>
            </div>
            <? endforeach; ?>
        </div>
        <div class="row text-cente no-gutters amthuc-row-3">
            <p class="tt mt-0 mb-40 w-100 text-center"><?=Text::get('phong-vi-khac');?></p>
            <? foreach($theEntry->items() as $k => $v) : ?>
                <? if($k < 2) continue;?>
                <? $vimg = $v->photosArray['summary'][0]; ?>
                <div class="mb-130 pb-0 text-left villa-item col-12 col-sm-12 col-lg-4">
                    <a class="opc-8" href="/<?=$v->slug?>">
                <img alt="<?=$vimg->description?>" src="/timthumb.php?src=<?=$vimg->image ?>&w=560&zc=0&q=80" class="w-100">
                <h2 class="tt my-40 text-capitalize"><?=$v->title; ?></h2></a>
                <p class="summary mb-40 text-left"><?=$v->model->summary ?></p>
                
                <a class="mt-20 btn-basic" href="/<?=$v->slug?>"><?=Text::get('xem-them');?></a>
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
    @media (min-width: 960px){
        .amthuc-row-3 .col-lg-4{
        margin-right: 20px;
        max-width: calc((100% - 40px)/3);
        }
        .amthuc-row-3 .col-lg-4:last-of-type{
            margin: 0;
        }
    }
    
');
?>