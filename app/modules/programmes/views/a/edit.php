<?php
$this->title = Yii::t('easyii', 'Edit category');
?>
<?= $this->render('_menu') ?>
<? echo $this->render('_submenu', ['model' => $model], $this->context); ?>
<? if($this->context->action->id == 'edit-lang')
	 $this->render('_form_lang', ['model' => $model]);  else 
	 $this->render('_form', ['model' => $model]); ?>