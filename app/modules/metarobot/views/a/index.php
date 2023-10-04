<?php
use yii\helpers\Url;

$this->title = Yii::t('easyii/metarobot', 'Pages');

$module = $this->context->module->id;
?>

<?= $this->render('_menu') ?>

<?php if($data->count > 0) : ?>
    <table class="table table-hover">
        <thead>
            <tr>
                <th width="50">#</th>
                <th>URL</th>
                <th>Index</th>
                <th>Follow</th>
                <th>Sort order</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach($data->models as $item) : ?>
            <tr>
                <td><?= $item->primaryKey ?></td>
                <td><a href="<?= Url::to(['/admin/'.$module.'/a/edit', 'id' => $item->primaryKey]) ?>"><?= $item->url ?></a></td>
                <td><?= ($item->index) ? 'True' : 'False' ?></td>
                <td><?= ($item->follow) ? 'True' : 'False' ?></td>
                <td><?= ($item->sort_order) ? $item->sort_order : 0 ?></td>
                <td><?= ($item->status) ? 'Active' : 'Inactive' ?></td>
                <td><a href="<?= Url::to(['/admin/'.$module.'/a/delete', 'id' => $item->primaryKey]) ?>" class="glyphicon glyphicon-remove confirm-delete" title="<?= Yii::t('easyii', 'Delete item')?>"></a></td>
            </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
    <?= yii\widgets\LinkPager::widget([
        'pagination' => $data->pagination
    ]) ?>
<?php else : ?>
    <p><?= Yii::t('easyii', 'No records found') ?></p>
<?php endif; ?>