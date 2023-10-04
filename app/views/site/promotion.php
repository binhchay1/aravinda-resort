       <? use yii\easyii\modules\text\api\Text; ?>
    <? include '_inc_br.php'; ?>
    <div class="container">
        <div class="row text-cente no-gutters amthuc-row">
            <h1 class="text-uppercase col-12 text-center mt-0 mb-40 tt"><?=$theEntry->title;?></h1>
                <? foreach($theEntry->children() as $k => $v) : ?>
                
                <div class="mb-130 pb-0 text-left villa-item col-12 col-sm-12 col-lg-<?=$k < 2 ? 6 : 4 ?> px-md-3 px-0 <?=$k%2 == 0 && $k < 2 ? 'pl-md-0' : 'pr-md-0' ?> ">
                    <a class="opc-8" href="/<?=$v->slug?>">
                <? if(!empty($v->photosArray['summary'])) : ?>
                <? $vimg = $v->photosArray['summary'][0]; ?>        
                <img alt="<?=$vimg->description?>" src="/timthumb.php?src=<?=$vimg->image ?>&w=560&zc=0&q=80" class="w-100">
                <? endif; ?>
                <a class="opc-8" href="/<?=$v->slug?>"><h2 class="tt my-40 text-capitalize"><?=$v->title; ?></h2></a>
                <p class="summary mb-40 text-left"><?=$v->summary ?></p>
                <a class="mt-20 btn-basic d-inline-flex text-uppercase" href="/<?=$this->context->otherMenu['booking']->slug ?>?promo=<?=$v->title?>"><?=Text::get('dat-phong');?></a>
                    <a class="read-more  ml-4" href="/<?=$v->slug?>"><?=Text::get('xem-them');?></a>
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