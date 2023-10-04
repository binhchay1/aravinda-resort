<?php
namespace app\modules\metarobot;

use Yii;

class MetarobotModule extends \yii\easyii\components\Module
{
    public static $installConfig = [
        'title' => [
            'en' => 'Pages',
            'ru' => 'Страницы',
        ],
        'icon' => 'file',
        'order_num' => 50,
    ];
    public function __construct($id, $module = null, $config = []){
       
        \Yii::$app->session->set('moduleName', 'metarobot');
      parent::__construct($id, $module, $config);
    }
}