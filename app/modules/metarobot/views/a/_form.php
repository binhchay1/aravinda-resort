<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\easyii\widgets\Redactor;
use yii\easyii\widgets\SeoForm;
use wadeshuler\ckeditor\widgets\CKEditor;
?>
<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => true,
    'options' => ['class' => 'model-form']
]); ?>
<?= $form->field($model, 'url') ?>
<?= $form->field($model, 'index')->dropDownList(['0' => 'False', '1' => 'True', ],['prompt'=>'Select Option']); ?>
<?= $form->field($model, 'follow')->dropDownList(['0' => 'False', '1' => 'True', ],['prompt'=>'Select Option']); ?>
<?= $form->field($model, 'sort_order')->dropDownList(range(0, 100)); ?>
<?= $form->field($model, 'status')->dropDownList(['0' => 'Inactive', '1' => 'Active', ],['prompt'=>'Select Option']); ?>

<?= Html::submitButton(Yii::t('easyii','Save'), ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>