<?php
$this->title = 'Create item';
?>
<?= $this->render('_menu', ['category' => $category]) ?>
<?= $this->render('_form_create', ['model' => $model,
                'category' => $category,
                'dataForm' => $dataForm,
                ]) ?>