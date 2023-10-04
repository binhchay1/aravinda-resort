<?php
use yii\easyii\widgets\PhotosMobile;

$this->title = Yii::t('easyii', 'Photos Mobile') . ' ' . $model->title;
?>

<?= $this->render('_submenu', ['model' => $model]) ?>

<?= PhotosMobile::widget(['model' => $model])?>