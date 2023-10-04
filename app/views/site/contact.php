   <? 
use yii\helpers\Html;
use yii\widgets\ActiveForm; 
?>
   <? use yii\easyii\modules\text\api\Text; ?>
    <?php $this->registerCssFile(DIR . 'assets/css/form.css', ['depends' => 'app\assets\AppAsset', 'position' => $this::POS_END]) ?> 
    <? include '_inc_br.php'; ?>
    <div class="container">
        <div class="row text-cente no-gutters amthuc-row">
            <h1 class="text-uppercase col-12 text-center mt-0 mb-40 tt"><?=$theEntry->title;?></h1>
            <div class="w-100">
            <iframe src="https://www.google.com/maps/d/u/0/embed?mid=13odHBdx3PQy8uBGFVqdZH7WdicaeydY-" width="100%" frameborder="0" height="480"></iframe>
            </div>
        </div>
        <div class="row text-center  mb-60">
            
            <div class="col-12 col-sm-12 col-lg-auto info-container order-last order-sm-last order-lg-first">
                <div class="float-left text-left">
                    <h3 class="text-uppercase  my-40 tt color-88">Aravinda Resort</h3>
                    <div class="info">
                        <p><?=Text::get('dia-chi');?></p>
                        <p>Email : info@aravindaresort.com</p>
                        <p><?=Text::get('dien-thoai');?></p>
                    </div>
                    <h3 class="text-uppercase  my-40 tt color-88"><?=Text::get('office-hn');?></h3>
                    <div class="info">
                        <p><?=Text::get('vp-hn-text');?></p>
                    </div>
                </div>
            </div>
            <div class="form-container col-12 col-sm-12 col-lg-auto text-left order-first order-sm-first order-lg-last">
                <div class="description w-700 mt-40 text-left">
                    <?=$theEntry->description ?>
                </div>
                <div class="form mt-20">
        <? $form = ActiveForm::begin([
            'id' => 'booking-form',
            'options' => ['class' => 'form-horizontal  form-ar'],
        ]); ?>
        <?= $form->field($model, 'fullName')->label(Text::get('ho-ten').'*'); ?>
        <?= $form->field($model, 'email')->label('Email*'); ; ?>
        <?= $form->field($model, 'phone')->label(Text::get('phone').'*'); ?>
        <?= $form->field($model, 'subject')->label(Text::get('tieu-de').'*'); ?>
        <?= $form->field($model, 'mesage', [
                        'inputOptions'=>['class'=>'w-100', 'rows'=>4]
                        ])->label(Text::get('loi-nhan'))->textArea(); ?>
        <div class="form-group row d-flex justify-content-end mb-0">
            <div class="col-12 col-sm-3 col-lg-3 p-0 text-right">
                <?= Html::submitButton(Text::get('gui'), ['class' => 'submit ']) ?>
            </div>
        </div>                
        <? ActiveForm::end() ?>
                </div>
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