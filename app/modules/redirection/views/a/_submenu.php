<?php
use yii\helpers\Url;

$action = $this->context->action->id;
$module = $this->context->module->id;
?>
<?php if(IS_ROOT) : ?>
    <ul class="nav nav-tabs">
    </ul>
    <br>
<?php endif;?>