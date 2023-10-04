<?php
$this->title = Yii::t('easyii', 'Create category');
?>
<?= $this->render('_menu') ?>
<?= $this->render('_form_create', ['model' => $model, 'parent' => $parent]) ?>