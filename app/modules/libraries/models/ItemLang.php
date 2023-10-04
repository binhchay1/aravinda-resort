<?php

namespace app\modules\libraries\models;
use yii\easyii\models\Photo;
use yii\db\ActiveRecord;

class ItemLang extends ActiveRecord

{

    public static function tableName()

    {

        return 'app_libraries_items_translate';

    }



    public function rules(){
        return [
            [['title', 'description'], 'safe']
            ];
    }
}