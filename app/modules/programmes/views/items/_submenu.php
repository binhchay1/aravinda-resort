<?php
use yii\helpers\Url;

$action = $this->context->action->id;
$module = $this->context->module->id;
?>

<ul class="nav nav-tabs">
    <li <?= ($action === 'edit') ? 'class="active"' : '' ?>><a href="<?= Url::to(['/admin/'.$module.'/items/edit', 'id' => $model->primaryKey]) ?>"><?= Yii::t('easyii', 'Edit') ?></a></li>
     

      <? foreach(Yii::$app->params['lang'] as $item) : ?>
     <li <?= ($action === 'edit-lang' && SEG6 == $item[1]) ? 'class="active"' : '' ?>><a href="<?= Url::to(['/admin/'.$module.'/items/edit-lang/'.$model->primaryKey.'/'.$item[1]]) ?>">Edit <?=$item[2]?></a></li>
 <? endforeach; ?>
    <li <?= ($action === 'photos') ? 'class="active"' : '' ?>><a href="<?= Url::to(['/admin/'.$module.'/items/photos', 'id' => $model->primaryKey]) ?>"><span class="glyphicon glyphicon-camera"></span> <?= Yii::t('easyii', 'Photos') ?></a></li>
</ul>
<br>