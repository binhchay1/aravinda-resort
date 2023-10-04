<?php
namespace app\modules\redirection\models;

use Yii;
use yii\easyii\behaviors\SeoBehavior;
use yii\easyii\models\Photo;

class Page extends \yii\easyii\components\ActiveRecord
{
    public $file_import;

    public static function tableName()
    {
        return 'app_redirections';
    }

    public function rules()
    {
        return [
            [['source_url', 'target_url'], 'required'],
            [['source_url', 'target_url'], 'trim'],
            [['file_import', 'type'], 'safe'],
            ['source_url', 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'source_url' => 'Source URL',
            'target_url' => 'Target URL',
            'type' => 'Type',
        ];
    }
}