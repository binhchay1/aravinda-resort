<?php
namespace app\modules\modulepage;

class ModulepageModule extends \yii\easyii\components\Module
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
       
        \Yii::$app->session->set('moduleName', 'modulepage');
      parent::__construct($id, $module, $config);
    }
}