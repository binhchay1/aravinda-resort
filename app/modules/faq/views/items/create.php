<?php
$this->title = Yii::t('easyii/faq', 'Create item');
?>
<?= $this->render('_menu', ['category' => $category]) ?>
<?= $this->render('_form_create', ['model' => $model,
                'category' => $category,
                'dataForm' => $dataForm,
                'locations' => $locations,
                'notes' => $notes,
                'exclusives' => $exclusives	
                ]) ?>