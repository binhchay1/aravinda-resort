<?php
use yii\helpers\Url;

$action = $this->context->action->id;
$module = $this->context->module->id;
$this->registerJsFile(DIR . 'assets/js/chosen/chosen.jquery.js', ['depends' => 'yii\easyii\assets\AdminAsset', 'position' => $this::POS_HEAD]); 

$this->registerCssFile(DIR . 'assets/js/chosen/chosen.min.css', ['depends' => 'yii\easyii\assets\AdminAsset', 'position' => $this::POS_HEAD]); 
?>
<ul class="nav nav-pills">
    <?php if($action === 'index') : ?>
        <li><a href="<?= Url::to(['/admin/'.$module]) ?>"><i class="glyphicon glyphicon-chevron-left font-12"></i> <?= Yii::t('easyii', 'Categories') ?></a></li>
    <?php endif; ?>
    <li <?= ($action === 'index') ? 'class="active"' : '' ?>><a href="<?= Url::to(['/admin/'.$module.'/items', 'id' => $category->primaryKey]) ?>"><?php if($action !== 'index') echo '<i class="glyphicon glyphicon-chevron-left font-12"></i> ' ?><?= $category->title ?></a></li>
    <li <?= ($action === 'create') ? 'class="active"' : '' ?>><a href="<?= Url::to(['/admin/'.$module.'/items/create', 'id' => $category->primaryKey]) ?>"><?= Yii::t('easyii', 'Add') ?></a></li>
     <? 
    if($action === 'index') {
        $catsMenu = \yii\helpers\ArrayHelper::map($category->items, 'item_id', 'title'); 
        
        echo \yii\helpers\Html::dropDownList('quick_search', null, $catsMenu,[
            'class' => 'chosen',
            'data-placeholder' => 'Search...',
            'id' => 'quick_search',
            'multiple' => 'multiple',
            'data-classname' =>  $this->context->module->id,
            'style' => 'float: right;'
            ]);
        } ?>
</ul>
<br/>
<?
$js = <<<JS
$('.chosen').chosen();
$('#quick_search').on('change', function(evt, params) {
    var partLink = '/items/edit/';
    window.location = '/admin/'+$(this).data('classname')+partLink+params.selected;
    return false;
});
$('.chosen-choices input').keyup(function(){
    if(!$(this).val()){
        $('.chosen-drop .chosen-results').hide();
        return false;
    }
    $('.chosen-drop .chosen-results').show();
})        
JS;

$this->registerJs($js);
$this->registerCss('#quick_search_chosen{float: right; margin-top: 5px;}');