<?php
namespace app\modules\destinations\assets;

class FieldsAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@webroot/app/modules/whoarewe/media';
    public $css = [
        'css/fields.css',
    ];
    public $js = [
        'js/fields.js?v=1'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
