<?php
namespace app\modules\location;

class LocationModule extends \yii\easyii\components\Module
{
    public static $installConfig = [
        'title' => [
            'en' => 'Text blocks',
            'ru' => 'Текстовые блоки',
        ],
        'icon' => 'font',
        'order_num' => 20,
    ];
}