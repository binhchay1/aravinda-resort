<?php

use yii\easyii\helpers\Image;

use yii\easyii\widgets\DateTimePicker;

use yii\helpers\Html;

use yii\helpers\Url;

use yii\widgets\ActiveForm;

use yii\easyii\widgets\TagsInput;

use dosamigos\fileupload\FileUpload;

use kartik\file\FileInput;

use yii\helpers\Json;
use wadeshuler\ckeditor\widgets\CKEditor;

use yii\easyii\widgets\Redactor;

use yii\easyii\widgets\SeoForm;

$this->registerJsFile(DIR . 'assets/js/chosen/chosen.jquery.js', ['depends' => 'yii\easyii\assets\AdminAsset', 'position' => $this::POS_HEAD]);
$this->registerJsFile(DIR . 'assets/plugins/chosen.order.jquery.min.js', ['depends' => 'yii\easyii\assets\AdminAsset', 'position' => $this::POS_HEAD]);
$this->registerCssFile(DIR . 'assets/js/chosen/chosen.min.css', ['depends' => 'yii\easyii\assets\AdminAsset', 'position' => $this::POS_HEAD]);

$settings = $this->context->module->settings;

$module = $this->context->module->id;
?>



<?php $form = ActiveForm::begin([

    'options' => ['enctype' => 'multipart/form-data', 'class' => 'model-form']

]); ?>

<?= $form->field($model, 'title') ?>

<?= $form->field($model, 'sub_title') ?>
<?= $form->field($model, 'summary')->textArea() ?>
<?= $form->field($model, 'spirit')->textArea() ?>
<?php if($settings['itemThumb']) : ?>

    <?php if($model->image) : ?>

        <img src="<?= Image::thumb($model->image, 240) ?>">

        <a href="<?= Url::to(['/admin/'.$module.'/items/clear-image', 'id' => $model->primaryKey]) ?>" class="text-danger confirm-delete" title="<?= Yii::t('easyii', 'Clear image')?>"><?= Yii::t('easyii', 'Clear image')?></a>

    <?php endif; ?>

<?php endif; ?>


<?php if($settings['itemDescription']) : ?>

    <?= $form->field($model, 'description')->widget(CKEditor::className()); ?>

<?php endif; ?>
<?= $dataForm ?>


<?= $form->field($model, 'time')->widget(DateTimePicker::className()); ?>


<?= SeoForm::widget(['model' => $model]) ?>
<?php if(IS_ROOT || IS_SEO) : ?>

    <?= $form->field($model, 'slug') ?>
    <a id='get-parent' style='float: right;' class="btn btn-success" href="javascript:void(0)">Get Parent Slug</a>
    

<?php endif; ?>



<?= Html::submitButton(Yii::t('easyii', 'Save'), ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end(); ?>

<?
$dir = DIR;
$excl = array();
if(isset($model->data->options)) {
    $options = $model->data->options;
    $options = implode(',',$options);
} else{
  $options = '';
}
if(isset($model->data->optionssum)) {
    $optionssum = $model->data->optionssum;
    $optionssum = implode(',',$optionssum);
} else{
  $optionssum = '';
}


$js = <<<JS

$(function(){
    $('.select-options').setSelectionOrder('$options'.split(','));
    $('.select-optionssum').setSelectionOrder('$optionssum'.split(','));
});

$('#item-title').change(function(){
    var slug = removeSpecial($(this).val().toLowerCase()).replace(/ +/g,'-').replace(/[0-9]/g,'').replace(/[^a-z0-9-_]/g,'').trim();
    var oldSlug = $('#item-slug').val();
    $('#item-slug').val(oldSlug + slug);
}); 

function removeSpecial(str = ''){
       str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,'a');
       str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ.+/g,"e");
       str = str.replace(/ì|í|ị|ỉ|ĩ/g,"i");
       str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ.+/g,"o");
       str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u");
       str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y");
       str = str.replace(/đ/g,"d");
       
       /* tìm và thay thế các kí tự đặc biệt trong chuỗi sang kí tự - */ 
       //str= str.replace(/-+-/g,"-"); //thay thế 2- thành 1- 
       //str= str.replace(/^\-+|\-+$/g,""); 
       
       return str;
}
$('button.btn-primary').click(function(){
    // Object-oriented flavor, example for jQuery plugin
    $('.selectTags').each(function(){
      var selection = $(this).getSelectionOrder();
      var json = JSON.stringify(selection);
      $(this).parent().find('.exts-order').val(json);
    });
});

$('.chosen').chosen();
$('.tagsInput').chosen();
$('.selectTags').chosen();
$('.ckeditor').each(function(){
    CKEDITOR.replace($(this).attr('id'));
})
JS;

$this->registerJs($js);

$this->registerCss('#redactor-modal-box > div {
    margin-top: 200px !important;
}
.chosen-itinerary .chosen-results li:hover{
    
}
');


 ?>
 