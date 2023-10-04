    <? use yii\easyii\modules\text\api\Text;
    use yii\helpers\Markdown;
    ?>
    <? include '_inc_br.php'; ?>
    <div class="container">
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
                <div class="description mt-40">
                    <?=$theEntry->model->content ?>
                </div>
        </div>
        <div class="my-stotry mb-130 d-sm-flex-wrap d-lg-flex w-100"  id="concept">
            <? $story = $theEntry->items()[0]; ?>
            <? if(isset($story->photosArray['summary'])) : 
                $vimg = $story->photosArray['summary'][0];
                ?>
                <img class="focus-center" alt="<?=$vimg->description ?>" src="<?=$vimg->image ?>">
            <? endif; ?>
            <div class="story pb-0 pt-sm-40 pt-md-0 px-md-40">
                <p class="tt mb-20 text-uppercase color-5a8d8d"><?=$story->title;?></p>
                <?=$story->description; ?>
            </div>
            
       </div>
       <div class="services-tn row row-spacing mb-50 text-center">
            <?=Markdown::process(Text::get('tiennghi-dichvu'));?>
            <? foreach($theEntry->items() as $ki => $vi) : ?>
            <? if($ki==0 || $ki == 3) continue; ?>
            <div class="col-12 col-sm col-md-6 text-left mb-80">
                <? $vimg = '';
                if(isset($vi->photosArray['summary'])) $vimg = $vi->photosArray['summary'][0]; ?>
                <img class="w-100" alt="<?=$vimg ? $vimg->description : '' ?>" src="/timthumb.php?src=<?=$vimg ? $vimg->image : '';?>&w=560&q=80&zc=0">
                <h3 class="tt my-40"><?=$vi->title ?></h3>
                <div class="row">
                    <? $options = \app\modules\libraries\api\Catalog::items(['where' => ['IN', 'item_id', $vi->data->options],
                         'orderBy' => [new \yii\db\Expression('FIELD (item_id, ' . implode(',',$vi->data->options) . ')')]
                ]); ?>
                    <? foreach($options as $ko => $vo) : ?>
                    <? if(isset($vo->photosArray['icon'])) : ?>
                    <div class="col-auto icon text-center"><img class="auto-size" src="<?=$vo->photosArray['icon'][0]->image ?>" alt="<?=$vo->photosArray['icon'][0]->description ?>">
                        <p class="tt-icon"><?=$vo->title?></p>
                    </div>
                    <? endif; ?>
                    <? endforeach; ?>
                </div>
            </div>
            <? endforeach; ?>
       </div>
       <div class="des-ninhbinh mb-130 d-sm-flex-wrap d-lg-flex w-100" id="destination">
            <? $des = $theEntry->items()[3]; ?>
            <div class="des">
                <p class="tt mb-20 text-uppercase text-white"><?=$des->title; ?></p>
                <p class="summary"><?=$des->description ?></p>
            </div>
             <? if(isset($des->photosArray['summary'])) : 
                $vimg = $des->photosArray['summary'][0];
                ?>
                <img alt="<?=$vimg->description ?>" src="<?=$vimg->image ?>">
            <? endif; ?>
       </div>
    </div>
    <div class="main-adress container-fluid row p-0 no-guitters mb-130">
        <div class="col-12 col-md-6 adress p-0">
            <div class="float-right pr-3">
            <p class="tt"><?=strip_tags(Text::get('tim-chung-toi')); ?></p>
            <div class="info">
                <p><?=Text::get('dia-chi'); ?></p>
                <p>Email : info@aravindaresort.com</p>
                <p><?=Text::get('dien-thoai'); ?></p>
            </div>
            <a href="/contact" class="btn-basic d-inline-flex"><?=Text::get('lien-he'); ?></a>
            </div>
        </div>
        <div class="col-12 col-md-6 map-nb google-maps  p-0">
            <iframe src="<?=Text::get('maps'); ?>" width="100%" frameborder="0" height="480"></iframe>
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
$css = <<<CSS
.main-adress .float-right{
    background: url(/assets/img/bg-footer.png) center 20px no-repeat transparent;
}
.des-ninhbinh{
    background: #5b8382;
    color: #fff;
}
.des-ninhbinh .des{
    padding: 40px;
}
.adress{
    background: #60544c;
}
.adress .float-right{
    max-width: 540px;
    text-align: center; 
    color: #fff;
    padding: 125px 0 75px;
}
.services-tn{
    overflow-x: hidden;
}
.services-tn > h2{
    font: 25px Lora,serif;
    color: #5a8d8d;
    text-transform: uppercase;
    width: 100%;
    text-align: center;
    margin-bottom: 30px;
	margin-top: 0;
}
.services-tn > p{
    max-width: 700px;
    margin-left: auto;
     margin-right: auto;
     margin-bottom: 35px;
}
p a{
    color: #60544c;
}
@media(max-width: 767px){
    .google-maps iframe{
        position: relative;
        height: calc(100vh/2) !important;
    }
}
CSS;
$this->registerCss($css);
?>