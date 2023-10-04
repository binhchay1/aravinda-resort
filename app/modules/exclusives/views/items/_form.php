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

<div class="control-group">
    <label for="select-beast">Programes</label>
   <select name="Item[programes][]" multiple class="form-control selectTags"><?=Html::renderSelectOptions(explode(',',$model->programes), $programes)?></select>
</div>
<?= $form->field($model, 'on_projet')->dropDownList(array_combine(range(0, 10), range(0, 10)),['prompt' => 'Select...', 'class' => 'chosen', 'style' => 'margin-top: 20px;'])->label("Display on projet");  ?>
<?= $dataForm ?>

<?php if($settings['itemDescription']) : ?>

    <?= $form->field($model, 'description')->widget(CKEditor::className()); ?>

<?php endif; ?>


<?= $form->field($model, 'on_top')->dropDownList(range(0, 30)) ?>
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

$js = <<<JS

$('.tagsInput').chosen();

$('.selectTags').chosen();
$('.ckeditor').each(function(){
    CKEDITOR.replace($(this).attr('id'));
})
$('#get-parent').click(function(){
    var slug = '$cat->slug'+'/'+'$model->slug';
    $('#item-slug').val(slug);
}); 
//config for chosen-city
$('.chosen-city').click(function(event){
    event.stopPropagation();
    $(this).addClass('chosen-with-drop chosen-container-active');
    $(this).find('.search-field input').trigger('click');
});
$(window).click(function() {
        $('.chosen-city').removeClass('chosen-with-drop chosen-container-active');
});
$('.chosen-city .chosen-results li').mouseover(function(){
    $('.chosen-city .chosen-results li').removeClass('highlighted');
    $(this).addClass('highlighted');
})
$('.chosen-city .chosen-results li').click(function(){
    var chose = [];
    $('.chosen-city .search-field').before('<li data-id="'+$(this).data('id')+'" data-slug="'+$(this).data('slug')+'"  data-stitle="'+$(this).data('stitle')+'" data-status="'+$(this).data('status')+'" class="search-choice"><span>'+$(this).text()+'</span><a class="search-choice-close"></a></li>');
    $('.chosen-city .search-choice').each(function(){
        chose.push({'id' : $(this).data('id'), 'title' : $(this).text(), 'slug' : $(this).data('slug'), 'status': $(this).data('status'), 'stitle' : $(this).data('stitle')});
    });
    $('#city').val(JSON.stringify(chose));
    $('.chosen-city .search-field input').val('');
})
$('.chosen-city .search-field input').on('keydown',function(event){
    if ( event.which == 13 ) {
        event.preventDefault();
        $('.chosen-city .chosen-results li.group-option.highlighted').trigger('click');
        return false;
    }
    if ( event.which == 40 ) {
        var target = $('.chosen-city .chosen-results li.group-option.highlighted:not(.hidden)');
        target.removeClass('highlighted');
        target.nextAll().not('.hidden').first().addClass('highlighted');
        return false;
    }
    if ( event.which == 38 ) {
        var target = $('.chosen-city .chosen-results li.group-option.highlighted:not(.hidden)');
        target.removeClass('highlighted');
        target.prevAll().not('.hidden').first().addClass('highlighted');
        return false;
    }
    var target = $(this);
    $('.chosen-city .chosen-results li').removeClass('hidden').removeClass('highlighted');
    $('.chosen-city .chosen-results li').each(function(){
        if (removeSpecial($(this).text().toLowerCase()).indexOf(target.val()) == -1) {
           $(this).addClass('hidden');
        }
    });
    $('.chosen-city .chosen-results li:not(.hidden)').first().addClass('highlighted');
})

$('.chosen-city .chosen-choices ').on('click', '.search-choice-close', function(){
    $(this).parent().remove();
    var chose = [];
     $('.chosen-city .search-choice').each(function(){
        chose.push({'id' : $(this).data('id'), 'title' : $(this).text(), 'slug' : $(this).data('slug'), 'status': $(this).data('status'), 'stitle' : $(this).data('stitle')});
    });
    $('#city').val(JSON.stringify(chose));
});

$('.chosen-city .chosen-choices').on('click', '.search-choice span', function(){
    var text = $(this).text();
    var stt = $(this).parent().data('stitle');
    console.log(stt);
    $(this).text('('+text+')');
    if(stt)
        $(this).parent().data('stitle', '('+stt+')');
    var chose = [];
    $('.chosen-city .search-choice').each(function(){
        chose.push({'id' : $(this).data('id'), 'title' : $(this).text(), 'slug' : $(this).data('slug'), 'status': $(this).data('status'), 'stitle' : $(this).data('stitle')});
    });
    $('#city').val(JSON.stringify(chose));
});


function removeSpecial(str = ''){
       str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,'a');
       str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ.+/g,"e");
       str = str.replace(/ì|í|ị|ỉ|ĩ/g,"i");
       str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ.+/g,"o");
       str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u");
       str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y");
       str = str.replace("'","");
       str = str.replace(/đ/g,"d");
       
       /* tìm và thay thế các kí tự đặc biệt trong chuỗi sang kí tự - */ 
       //str= str.replace(/-+-/g,"-"); //thay thế 2- thành 1- 
       //str= str.replace(/^\-+|\-+$/g,""); 
       
       return str;
}
JS;

$this->registerJs($js);

$this->registerCss('#redactor-modal-box > div {

    margin-top: 200px !important;

}');


 ?>