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

$this->registerJsFile(DIR . 'assets/js/selectize.js', ['depends' => 'yii\easyii\assets\AdminAsset', 'position' => $this::POS_END]); 

$this->registerCssFile(DIR . 'assets/js/selectize.bootstrap3.css', ['depends' => 'yii\easyii\assets\AdminAsset', 'position' => $this::POS_END]); 

$settings = $this->context->module->settings;

$module = $this->context->module->id;

?>



<?php $form = ActiveForm::begin([

    'options' => ['enctype' => 'multipart/form-data', 'class' => 'model-form']

]); ?>

<?= $form->field($model, 'title') ?>

<?= $form->field($model, 'sub_title') ?>
<?= $form->field($model, 'summary')->textArea() ?>

<?php if($settings['itemThumb']) : ?>

    <?php if($model->image) : ?>

        <img src="<?= Image::thumb($model->image, 240) ?>">

        <a href="<?= Url::to(['/admin/'.$module.'/items/clear-image', 'id' => $model->primaryKey]) ?>" class="text-danger confirm-delete" title="<?= Yii::t('easyii', 'Clear image')?>"><?= Yii::t('easyii', 'Clear image')?></a>

    <?php endif; ?>
<?php endif; ?>



<?= $dataForm ?>

<?php if($settings['itemDescription']) : ?>

    <?= $form->field($model, 'description')->widget(CKEditor::className()); ?>

<?php endif; ?>



<?= $form->field($model, 'time')->widget(DateTimePicker::className()); ?>



<?php if(IS_ROOT) : ?>

    <?= $form->field($model, 'slug') ?>

    <?= SeoForm::widget(['model' => $model]) ?>

<?php endif; ?>


<?= Html::submitButton(Yii::t('easyii', 'Save'), ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end(); ?>

<? 
$dir = DIR;

$js = <<<JS
$('form button[type=submit]').click(function(){
    return false;
    });


$('.tagsInput').selectize({

    delimiter: ',',

    persist: false,

    enableDuplicate: true,

    create: function(input) {

        return {

            value: input,

            text: input

        }

    },

});

$('.selectTags').selectize({

    plugins: ['remove_button']

});
$('.ckeditor').each(function(){
    CKEDITOR.replace($(this).attr('id'));
})


JS;

$this->registerJs($js);

$this->registerCss('#redactor-modal-box > div {

    margin-top: 200px !important;

}');


 ?>