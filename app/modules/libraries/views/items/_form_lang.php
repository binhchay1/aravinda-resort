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
<?= $form->field($model, 'description')->widget(CKEditor::className());?>
<?= Html::submitButton(Yii::t('easyii', 'Save'), ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end(); ?>
