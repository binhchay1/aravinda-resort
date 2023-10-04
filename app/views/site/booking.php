<? 
use yii\helpers\Html;
use yii\widgets\ActiveForm; 
?>
   <? use yii\easyii\modules\text\api\Text; ?>
   <?php $this->registerCssFile(DIR . 'assets/css/form.css', ['depends' => 'app\assets\AppAsset', 'position' => $this::POS_END]) ?>
    <? include '_inc_br.php'; ?>
    <div class="container">
        <h1 class="text-uppercase col-12 text-center mt-0 mb-40 tt"><?=$theEntry->title;?></h1>
        <div class="row text-center  mb-60">
            
            <div class="col-12 col-sm-12 col-lg-auto info-container order-last order-sm-last order-lg-first">
                <div class="float-left text-left">
                    <h3 class="text-uppercase  my-40 tt color-88">Aravinda Resort</h3>
                    <div class="info">
                        <p><?=Text::get('dia-chi');?></p>
                        <p>Email : info@aravindaresort.com</p>
                        <p><?=Text::get('dien-thoai');?></p>
                    </div>
                    <h3 class="text-uppercase  my-40 tt  color-88"><?=Text::get('office-hn');?></h3>
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
        <div class="row p-0">
        <?= $form->field($model, 'dateCome', [
                        'options'=>['class'=>'col-12 col-sm-12 col-lg-6 pl-md-0'],
                        'inputOptions'=>['class'=>'date-go']
                        ])->label(Text::get('ngay-den').'*')->textInput(['value' => Yii::$app->request->get('in')]); ?>
        <?= $form->field($model, 'dateGo', [
                        'options'=>['class'=>'col-12 col-sm-12 col-lg-6  pr-md-0'],
                        'inputOptions'=>['class'=>'date-come']
                        ])->label(Text::get('ngay-di').'*')->textInput(['value' => Yii::$app->request->get('out')]); ?>
        </div>
        <div class="row  p-0">
        <?= $form->field($model, 'typeRoom', [
                        'options'=>['class'=>'col-12 col-sm-12 col-lg-6  pl-md-0']
                        ])->label(Text::get('loai-phong').'*')->dropDownList($typeRooms,['value' => Yii::$app->request->get('room')]); ?>
        <?= $form->field($model, 'numRoom', [
                        'options'=>['class'=>'col-12 col-sm-12 col-lg-6  pr-md-0']
                        ])->label(Text::get('so-phong').'*'); ?>
        </div>
        <div class="row  p-0">
        <?= $form->field($model, 'adults', [
                        'options'=>['class'=>'col-12 col-sm-12 col-lg-3  pl-md-0']
                        ])->label(Text::get('ng-lon').'*')->dropDownList(range(0, 60), ['value' => Yii::$app->request->get('adult'), 'options' => ['prompt' => '']]); ?>
        <?= $form->field($model, 'promo', [
                        'options'=>['class'=>'col-12 col-sm-12 col-lg-3']
                        ])->label(strip_tags(Text::get('promo')))->dropDownList($promos, ['value' => Yii::$app->request->get('promo'), 'options' => ['prompt' => '']]); ?>                
        <?= $form->field($model, 'child', [
                        'options'=>['class'=>'col-12 col-sm-6 col-lg-3']
                        ])->dropDownList(range(0, 60), ['value' => Yii::$app->request->get('children')]); ?>
        <?= $form->field($model, 'ageChild', [
                        'options'=>['class'=>'col-12 col-sm-6 col-lg-3  pr-md-0']
                        ])->label(Text::get('tuoi-tre-em').'*')->dropDownList(range(0, 60)); ?>
        </div>
        <?= $form->field($model, 'mesage', [
                        'inputOptions'=>['class'=>'w-100', 'rows'=>4]
                        ])->label(Text::get('loi-nhan'))->textArea(); ?>
        <div class="form-group row d-flex justify-content-end mb-0">
            <div class="col-12 col-sm-auto col-lg-auto p-0">
                <?= Html::submitButton(Text::get('gui'), ['class' => 'submit ']) ?>
            </div>
        </div>                
        <? ActiveForm::end() ?>
                </div>
            </div>
                
        </div>
    </div>
