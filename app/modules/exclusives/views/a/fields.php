<?php
use yii\helpers\Html;
use app\modules\exclusives\assets\FieldsAsset;
use app\modules\exclusives\models\Category;
$this->title = Yii::t('easyii/exclusives', 'Category fields');
$this->registerAssetBundle(FieldsAsset::className());

$this->registerJs('
var fieldTemplate = \'\
    <tr>\
        <td>'. Html::input('text', null, null, ['class' => 'form-control field-name']) .'</td>\
        <td>'. Html::input('text', null, null, ['class' => 'form-control field-title']) .'</td>\
        <td>\
            <select class="form-control field-type">'.str_replace("\n", "", Html::renderSelectOptions('', Category::$fieldTypes)).'</select>\
        </td>\
        <td><textarea class="form-control field-options" placeholder="'.Yii::t('easyii/exclusives', 'Type options with `comma` as delimiter').'" style="display: none;"></textarea></td>\
        <td class="text-right">\
            <div class="btn-group btn-group-sm" role="group">\
                <a href="#" class="btn btn-default move-up" title="'. Yii::t('easyii', 'Move up') .'"><span class="glyphicon glyphicon-arrow-up"></span></a>\
                <a href="#" class="btn btn-default move-down" title="'. Yii::t('easyii', 'Move down') .'"><span class="glyphicon glyphicon-arrow-down"></span></a>\
                <a href="#" class="btn btn-default color-red delete-field" title="'. Yii::t('easyii', 'Delete item') .'"><span class="glyphicon glyphicon-remove"></span></a>\
            </div>\
        </td>\
    </tr>\';
', \yii\web\View::POS_HEAD);

?>
<?= $this->render('@easyii/views/category/_menu') ?>
<?= $this->render('_submenu', ['model' => $model]) ?>
<br>

<?= Html::button('<i class="glyphicon glyphicon-plus font-12"></i> '.Yii::t('easyii/exclusives', 'Add field'), ['class' => 'btn btn-default', 'id' => 'addField']) ?>

<table id="categoryFields" class="table table-hover">
    <thead>
        <th>Name</th>
        <th><?= Yii::t('easyii', 'Title') ?></th>
        <th><?= Yii::t('easyii/exclusives', 'Type') ?></th>
        <th><?= Yii::t('easyii/exclusives', 'Options') ?></th>
        <th width="120"></th>
    </thead>
    <tbody>
    <?php foreach($model->fields as $field) : ?>
        <tr>
            <td><?= Html::input('text', null, $field->name, ['class' => 'form-control field-name']) ?></td>
            <td><?= Html::input('text', null, $field->title, ['class' => 'form-control field-title']) ?></td>
            <td>
                <select class="form-control field-type">
                    <?= Html::renderSelectOptions($field->type, Category::$fieldTypes) ?>
                </select>
            </td>
            <td>
                <textarea class="form-control field-options" placeholder="<?= Yii::t('easyii/exclusives', 'Type options with `comma` as delimiter') ?>" <?= !$field->options ? 'style="display: none;"' : '' ?> ><?= is_array($field->options) ? implode(',', $field->options) : '' ?></textarea>
            </td>
            <td class="text-right">
                <div class="btn-group btn-group-sm" role="group">
                    <a href="#" class="btn btn-default move-up" title="<?= Yii::t('easyii', 'Move up') ?>"><span class="glyphicon glyphicon-arrow-up"></span></a>
                    <a href="#" class="btn btn-default move-down" title="<?= Yii::t('easyii', 'Move down') ?>"><span class="glyphicon glyphicon-arrow-down"></span></a>
                    <a href="#" class="btn btn-default color-red delete-field" title="<?= Yii::t('easyii', 'Delete item') ?>"><span class="glyphicon glyphicon-remove"></span></a>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?= Html::button('<i class="glyphicon glyphicon-ok"></i> '.Yii::t('easyii/exclusives', 'Save fields'), ['class' => 'btn btn-primary', 'id' => 'saveCategoryBtn']) ?>
