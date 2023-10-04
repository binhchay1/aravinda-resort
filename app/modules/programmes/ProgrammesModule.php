<?php
namespace app\modules\programmes;

class ProgrammesModule extends \yii\easyii\components\Module
{
    public $settings = [
        'categoryThumb' => true,
        'itemsInFolder' => false,

        'itemThumb' => true,
        'itemPhotos' => true,
        'itemDescription' => true,
        'itemSale' => true,
    ];

    public static $installConfig = [
        'title' => [
            'en' => 'Catalog',
            'ru' => 'Каталог',
        ],
        'icon' => 'list-alt',
        'order_num' => 100,
    ];
    public function __construct($id, $module = null, $config = []){
       
        \Yii::$app->session->set('moduleName', 'programmes');
         \Yii::$app->session->set('moduleUrl', 'voyage');
      parent::__construct($id, $module, $config);
    }
}