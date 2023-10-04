<?php
use yii\easyii\widgets\ContentsMobile;

$this->title = 'Contents Mobile ' . $model->title;
?>

<?= $this->render('_submenu', ['model' => $model]) ?>

<?= ContentsMobile::widget(['model' => $model])?>