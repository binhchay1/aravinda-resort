<?php
use yii\easyii\widgets\Photos;

$this->title = Yii::t('easyii', 'Photos') . ' ' . $model->title;
?>

<?= $this->render('_menu', ['model' => $model]) ?>

<?= Photos::widget(['model' => $model])?>