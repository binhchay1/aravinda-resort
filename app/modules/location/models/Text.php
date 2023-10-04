<?php
namespace app\modules\location\models;
use yii\behaviors\SluggableBehavior;


use Yii;
use yii\easyii\behaviors\CacheFlush;

class Text extends \yii\easyii\components\ActiveRecord
{
    const CACHE_KEY = 'easyii_text';

    public static function tableName()
    {
        return 'app_locations';
    }

    public function rules()
    {
        return [
            ['text_id', 'number', 'integerOnly' => true],
            ['text', 'required'],
            ['text', 'trim'],
            ['slug', 'match', 'pattern' => self::$SLUG_PATTERN, 'message' => Yii::t('easyii', 'Slug can contain only 0-9, a-z and "-" characters (max: 128).')],
            ['slug', 'default', 'value' => null],
            ['slug', 'unique']
        ];
    }

    public function attributeLabels()
    {
        return [
            'location' => Yii::t('easyii', 'Text'),
            'slug' => Yii::t('easyii', 'Slug'),
        ];
    }

    public function behaviors()
    {
        return [
            CacheFlush::className(),
            'sluggable' => [
                'class' => SluggableBehavior::className(),
                'attribute' => 'text',
                'ensureUnique' => true
            ]
        ];
    }

}