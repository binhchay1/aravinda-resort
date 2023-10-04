   <? 
use yii\helpers\Html;
use yii\widgets\ActiveForm; 
?>
<?php $this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css', ['depends' => 'app\assets\AppAsset', 'position' => $this::POS_END]) ?>
<?php $this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.pack.js', ['depends' => 'app\assets\AppAsset', 'position' => $this::POS_END]) ?>


   <? use yii\easyii\modules\text\api\Text; 
   $photos = $theEntry->photosArray['summary'];
      $photosC1 = $photosC2 = $photosC3= [];
      foreach ($photos as $key => $value) {
        $k = $key +1;
        if($k % 3 == 1){
          $photosC1[] = $value;
        }
        if($k % 3 == 2){
          $photosC2[] = $value;
        }
        if($k % 3 == 0){
          $photosC3[] = $value;
        }
      }
   ?>
    <?php $this->registerCssFile(DIR . 'assets/css/form.css', ['depends' => 'app\assets\AppAsset', 'position' => $this::POS_END]) ?> 
    <? include '_inc_br.php'; ?>
    <div class="container">
        <div class="row text-cente no-gutters">
            <h1 class="text-uppercase col-12 text-center mt-0 mb-40 tt"><?=$theEntry->title;?></h1>
        </div>
        <div class="row no-gutters galleries flex-wrap">
            <div class="col-12 col-sm-12 col-lg-4 mx-0 mx-md-3">
                <? $n=0; ?>
                <? foreach ($photosC1 as $key => $value) :?>
                    <div class="w-100 fancybox" data-index = "<?=$n ?>">
                    <img alt="<?=$value->description; ?>"  src="<?=$value->image; ?>" class="focus-center w-100 img-zoom img-lazy <?=$key <= 1 ? 'lazyload' : '' ?>"/>
                    <p class="text text-center color-888 pt-30"><?=$value->model->caption; ?></p>
                </div>
                <? $n++; ?>
                <? endforeach; ?>
            </div> 
            <div class="col-12 col-sm-12 col-lg-4 mx-0 mx-md-3">
                <? foreach ($photosC2 as $key => $value) :?>
                    <div class="w-100 fancybox" data-index = "<?=$n ?>">
                    <img alt="<?=$value->description; ?>"  src="<?=$value->image; ?>" class="focus-center w-100 img-zoom img-lazy <?=$key <= 1 ? 'lazyload' : '' ?>"/>
                    <p class="text text-center color-888 pt-30"><?=$value->model->caption; ?></p>
                </div>
                <? $n++; ?>
                <? endforeach; ?>
            </div>  
            <div class="col-12 col-sm-12 col-lg-4 mx-0 mx-md-3">
                <? foreach ($photosC3 as $key => $value) :?>
                    <div class="w-100 fancybox" data-index = "<?=$n ?>">
                    <img alt="<?=$value->description; ?>"  src="<?=$value->image; ?>" class="focus-center w-100 img-zoom img-lazy <?=$key <= 1 ? 'lazyload' : '' ?>"/>
                    <p class="text text-center color-888 pt-30"><?=$value->model->caption; ?></p>
                </div>
                <? $n++; ?>
                <? endforeach; ?>
            </div>       
            
        </div>
        <?
            $photos = $theEntry->photosArray['galery'];
      $photosC1 = $photosC2 = $photosC3= [];
      foreach ($photos as $key => $value) {
        $k = $key +1;
        if($k % 3 == 1){
          $photosC1[] = $value;
        }
        if($k % 3 == 2){
          $photosC2[] = $value;
        }
        if($k % 3 == 0){
          $photosC3[] = $value;
        }
      }
   ?>
         ?>
        <div class="galleries-popup" id="galleries-popup" style="display: none;">
            <div class="swiper-container">
                    <!-- Additional required wrapper -->
                    <div class="swiper-wrapper">
                        <? foreach ($photosC1 as $key => $vimg) :?>
                             <!-- Slides -->
                        <div class="swiper-slide"><img alt="<?=$vimg->description?>" src="<?=$vimg->image ?>">
                        <p class="caption"><?=$vimg->model->caption ?></p>
                        </div>
                        <? endforeach; ?>
                        <? foreach ($photosC2 as $key => $vimg) :?>
                             <!-- Slides -->
                        <div class="swiper-slide"><img alt="<?=$vimg->description?>" src="<?=$vimg->image ?>">
                        <p class="caption"><?=$vimg->model->caption ?></p>
                        </div>
                        <? endforeach; ?>
                        <? foreach ($photosC3 as $key => $vimg) :?>
                             <!-- Slides -->
                        <div class="swiper-slide"><img alt="<?=$vimg->description?>" src="<?=$vimg->image ?>">
                        <p class="caption"><?=$vimg->model->caption ?></p>
                        </div>
                        <? endforeach; ?>
                    </div>
                    <!-- If we need pagination -->
                    <div class="swiper-pagination"></div>

                    <!-- If we need navigation buttons -->
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
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
$('div.fancybox').click(function(){
    $.fancybox({
        href: '#galleries-popup',
        centerOnScroll: true,
        padding: 1,
        width: 1024,
        height: 683, 
        autoSize: false
        });
    $(window).resize();
        mySwiper.update();
    var index = $(this).data('index');
    mySwiper.slideTo(index+1)
});

JS;
$this->registerJs($js);
$this->registerCss('
    .breadcrumb{
    padding-top: 100px;
    }
    .galleries .fancybox{
        
        margin-bottom: 30px;
        padding: 5px;
        
        box-shadow: 0px 1px 1px 2px #ededed;
        border-radius: 5px;
        text-align: center;
    }
    .galleries .col-lg-4{
        margin: 0 15px;
    }
    .galleries .col-lg-4:nth-of-type(3n+1){
        margin-left: 0;
    }
    .galleries .col-lg-4:nth-of-type(3n){
        margin-right: 0;
    }  
    .caption{
            position: absolute;
    width: 100%;
    z-index: 999999;
    bottom: 0;
    left: 0;
    background: rgba(255,255,255,0.7);
    margin: 0;
    padding: 10px 0;
    text-align: center;
    } 
    .swiper-container-horizontal>.swiper-pagination-bullets, .swiper-pagination-custom, .swiper-pagination-fraction{
        bottom: 50px;
    }
    @media(min-width: 1200px){
        .galleries .col-lg-4{
            max-width: calc((100% - 60px)/3);
        }
    }
');
?>