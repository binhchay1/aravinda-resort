<?php
namespace app\modules\redirection;

use Yii;

class RedirectionModule extends \yii\easyii\components\Module
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
       
        \Yii::$app->session->set('moduleName', 'redirection');
      parent::__construct($id, $module, $config);
    }
}