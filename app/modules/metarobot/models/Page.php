<?php
namespace app\modules\metarobot\models;

use Yii;
use yii\easyii\behaviors\SeoBehavior;
use yii\easyii\models\Photo;

class Page extends \yii\easyii\components\ActiveRecord
{
    public static function tableName()
    {
        return 'app_metarobots';
    }

    public function rules()
    {
        return [
            [['url', 'index', 'follow', 'status', 'sort_order'], 'required'],
            [['url'], 'trim'],
            ['url', 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'url' => 'Please enter string regex. Example "\/confiance$"',
            'index' => 'Index',
            'follow' => 'Follow',
            'status' => 'Status',
            'sort_order' => 'Sort order',
        ];
    }
}