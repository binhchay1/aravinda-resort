<?php
use yii\helpers\Url;

$action = $this->context->action->id;
$module = $this->context->module->id;
?>
<?php if(IS_ROOT) : ?>
    <ul class="nav nav-tabs">
        <li <?= ($action === 'edit') ? 'class="active"' : '' ?>><a href="<?= Url::to(['/admin/'.$module.'/a/edit', 'id' => $model->primaryKey]) ?>"><?= Yii::t('easyii', 'Edit') ?></a></li>
        <li <?= ($action === 'fields') ? 'class="active"' : '' ?>><a href="<?= Url::to(['/admin/'.$module.'/a/fields', 'id' => $model->primaryKey]) ?>"><span class="glyphicon glyphicon-cog"></span> Fields</a></li>
        <li <?= ($action === 'photos') ? 'class="active"' : '' ?>><a href="<?= Url::to(['/admin/'.$module.'/a/photos', 'id' => $model->primaryKey]) ?>"><span class="glyphicon glyphicon-camera"></span> <?= Yii::t('easyii', 'Photos') ?></a></li>
        <li <?= ($action === 'photos-mobile') ? 'class="active"' : '' ?>><a href="<?= Url::to(['/admin/'.$module.'/a/photos-mobile', 'id' => $model->primaryKey]) ?>"><span class="glyphicon glyphicon-camera"></span> Photos Mobile</a></li>
        <li <?= ($action === 'contents-mobile') ? 'class="active"' : '' ?>><a href="<?= Url::to(['/admin/'.$module.'/a/contents-mobile', 'id' => $model->primaryKey]) ?>"><span class="glyphicon glyphicon-pencil"></span> Contents Mobile</a></li>
    </ul>
    <br>
<?php endif;?>