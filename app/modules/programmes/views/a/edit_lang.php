<?php
$this->title = Yii::t('easyii', 'Edit category');
?>
<?= $this->render('_menu') ?>
<? echo $this->render('_submenu', ['model' => $model], $this->context); ?>
<? echo  $this->render('_form_lang', ['model' => $modelLang]);
