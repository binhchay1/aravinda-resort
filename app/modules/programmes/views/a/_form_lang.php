<?php
use yii\easyii\helpers\Image;

use yii\helpers\Html;

use yii\helpers\Url;

use yii\widgets\ActiveForm;

use yii\easyii\widgets\SeoForm;
use wadeshuler\ckeditor\widgets\CKEditor;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => true
]); ?>
<?= $form->field($model, 'title') ?>
<?= $form->field($model, 'summary') ?>
<?= $form->field($model, 'content')->widget(CKEditor::className());?>
<?= $form->field($model, 'slug') ?>
<?= Html::submitButton(Yii::t('easyii', 'Save'), ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end(); ?>
