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

use yii\easyii\widgets\SeoForm;

$this->registerJsFile(DIR . 'assets/js/chosen/chosen.jquery.js', ['depends' => 'yii\easyii\assets\AdminAsset', 'position' => $this::POS_HEAD]); 

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





<?= $dataForm ?>

<?php if($settings['itemDescription']) : ?>

    <?= $form->field($model, 'description')->widget(CKEditor::className()); ?>

<?php endif; ?>



<?= $form->field($model, 'time')->widget(DateTimePicker::className()); ?>


<?= $form->field($model, 'slug') ?>
<?= SeoForm::widget(['model' => $model]) ?>
<?php if(IS_ROOT || IS_SEO) : ?>

    

<?php endif; ?>



<?= Html::submitButton(Yii::t('easyii', 'Save'), ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end(); ?>

<? 

$dir = DIR;

$js = <<<JS

$('.tagsInput').chosen();

$('.selectTags').chosen();
$('.ckeditor').each(function(){
    CKEDITOR.replace($(this).attr('id'));
})

// $('.datetimepicker').datetimepicker();

JS;

$this->registerJs($js);

$this->registerCss('#redactor-modal-box > div {

    margin-top: 200px !important;

}');

 ?>