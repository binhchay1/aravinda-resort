<?php

use yii\easyii\helpers\Image;

use yii\helpers\Html;

use yii\helpers\Url;

use yii\widgets\ActiveForm;

use yii\easyii\widgets\SeoForm;
use wadeshuler\ckeditor\widgets\CKEditor;


$class = $this->context->categoryClass;

$settings = $this->context->module->settings;

?>

<?php $form = ActiveForm::begin([

    'enableAjaxValidation' => true,

    'options' => ['enctype' => 'multipart/form-data']

]); ?>

<?= $form->field($model, 'title') ?>

<?= $form->field($model, 'sub_title') ?>
<?= $form->field($model, 'summary')->textArea() ?>
<?= $form->field($model, 'on_top')->dropDownList(range(0, 30)) ?>


<?php if(!empty($parent)) : ?>

    <div class="form-group field-category-title required">

        <label for="category-parent" class="control-label"><?= Yii::t('easyii', 'Parent category') ?></label>

        <select class="form-control" id="category-parent" name="parent">

            <option value="" class="smooth"><?= Yii::t('easyii', 'No') ?></option>

            <?php foreach($class::find()->sort()->asArray()->all() as $node) : ?>

                <option

                    value="<?= $node['category_id'] ?>"

                    <?php if($parent == $node['category_id']) echo 'SELECTED' ?>

                    style="padding-left: <?= $node['depth']*20 ?>px;"

                ><?= $node['title'] ?></option>

            <?php endforeach; ?>

        </select>

    </div>

<?php endif; ?>



<?php if($settings['categoryThumb']) : ?>

    <?php if($model->image) : ?>

        <img src="<?= Image::thumb($model->image, 240) ?>">

        <a href="<?= Url::to(['/admin/'.$this->context->moduleName.'/a/clear-image', 'id' => $model->primaryKey]) ?>" class="text-danger confirm-delete" title="<?= Yii::t('easyii', 'Clear image')?>"><?= Yii::t('easyii', 'Clear image')?></a>

    <?php endif; ?>

    <?= $form->field($model, 'image')->fileInput() ?>
     <a id='get-parent' style='float: right;' class="btn btn-success" href="javascript:void(0)">Get Parent Slug</a>
    <?= $form->field($model, 'content')->widget(CKEditor::className()); ?>

<?php endif; ?>

<?php if(IS_ROOT || IS_SEO) : ?>

    <?= $form->field($model, 'slug') ?>

    <?= SeoForm::widget(['model' => $model]) ?>

<?php endif; ?>



<?= Html::submitButton(Yii::t('easyii', 'Save'), ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end(); ?>


<? 

$js = <<< JS

$('.ckeditor').each(function(){
    CKEDITOR.replace($(this).attr('id'));
})


$('#get-parent').click(function(){
    var slug = '$prSlug'+'/'+'$model->slug';
    $('#category-slug').val(slug);
}); 

JS;

$this->registerJs($js);