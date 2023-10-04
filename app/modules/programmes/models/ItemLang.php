<?php

namespace app\modules\programmes\models;
use yii\easyii\models\Photo;
use yii\db\ActiveRecord;

class ItemLang extends ActiveRecord

{

    public static function tableName()

    {

        return 'app_programmes_items_translate';

    }



    public function rules(){
        return [
            [['title', 'description', 'slug', 'summary'], 'safe']
            ];
    }
}