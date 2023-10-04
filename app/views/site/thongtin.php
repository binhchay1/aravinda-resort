     <? use yii\easyii\modules\text\api\Text;
    use yii\helpers\Markdown;
    ?>
    <? include '_inc_br.php'; ?>
    <div class="container">
        <div class="row text-cente no-gutters amthuc-row">
            <h1 class="text-uppercase col-12 text-center mt-0 mb-40 tt"><?=$theEntry->title;?></h1>
            
            <div class="w-100 mb-40">
            <iframe src="<?=Text::get('maps'); ?>" width="100%" frameborder="0" height="480"></iframe>
            </div>
            <div class="summary mx-auto text-center mb-130"><?=$theEntry->model->content; ?></div>

            <div class="row row-spacing">
                <p class="tt mt-0 mb-40 w-100 text-center"><?=strip_tags(Text::get('discover-more'))?></p>
                <? foreach($theEntry->children() as $k => $v) : ?>
                <? $vimg = $v->photosArray['summary'][0]; ?>
                <div class="mb-130 pb-0 text-left villa-item col-12 col-sm-12 col-md-6">
                <? if($vimg) : ?>
                <img alt="<?=$vimg->description?>" src="/timthumb.php?src=<?=$vimg->image ?>&w=560&zc=0&q=80" class="w-100">
                <? endif; ?>
                <h2 class="tt tt-use my-40"><?=$v->title; ?></h2>
                <p class="summary mb-40"><?=$v->summary ?></p>
                
                <? foreach ($v->items as $key => $value) :?>
                    <div class="row m-0">
                        <p class="w-100"><b><a class="opc-8 color-5a8" href="/<?=$value->slug?>"><?=$value->title; ?></b></a></p>
                        <p class="<?=$key == (count($v->items) - 1)? 'm-0' : '' ?>"><?=$value->summary; ?>
                            <br><a class="read-more mt-0" href="/<?=$value->slug?>"><?=Text::get('xem-them');?></a>
                        </p>
                        
                    </div>
                <? endforeach; ?>
            </div>
            <? endforeach; ?>
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
    .tt-use, .description h4 strong {
        font: 25px Lora,serif;
        color: #000;
        text-transform: none;
    }
    
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