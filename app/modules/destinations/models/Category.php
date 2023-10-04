<?php

namespace app\modules\destinations\models;
use yii\easyii\models\Photo;
use yii\easyii\models\ContentMobile;

class Category extends \yii\easyii\components\CategoryModel

{

    static $fieldTypes = [

        'string' => 'String',

        'text' => 'Text',

        'boolean' => 'Boolean',

        'select' => 'Select',

        'selectTags' => 'Select Tags',

        'checkbox' => 'Checkbox',

        'tags' => 'Tags',

        'tagsDate' => 'Tags Datetime',

        'file' => 'File',

        'date' => 'Datetime',

        'tourTypes' => 'Tour Types',

        'tTourThemes' => 'Tour Themes',

        'tTourJours' => 'Tour Jours',

        'selectLocations' => 'Locations',

         'modulepage' => 'Modules Page',
         'envies' => 'Envies',

        'blogs' => 'Blogs'

    ];

   

    public static function tableName()

    {

        return 'app_destinations_categories';

    }

    public function rules(){
        return array_merge(parent::rules(), [
            [['summary','sub_title','summary_title','on_top'], 'safe']
            ]);

    }

    public function beforeSave($insert)

    {

        if (parent::beforeSave($insert)) {

            if($insert && ($parent = $this->parents(1)->one())){

                $this->fields = $parent->fields;

            }



            if(!$this->fields || !is_array($this->fields)){

                $this->fields = [];

            }

            $this->fields = json_encode($this->fields);



            return true;

        } else {

            return false;

        }

    }



    public function afterSave($insert, $attributes)

    {

        parent::afterSave($insert, $attributes);

        $this->parseFields();

    }



    public function afterFind()

    {

        parent::afterFind();

        $this->parseFields();

    }



    public function getItems()

    {

         return $this->hasMany(Item::className(), ['category_id' => 'category_id'])->sortDate();

    }



    public function afterDelete()

    {

        parent::afterDelete();



        foreach($this->getItems()->all() as $item){

            $item->delete();

        }

    }

    public function getPhotos()
    {
        return $this->hasMany(Photo::className(), ['item_id' => 'category_id'])->where(['class' => self::className()])->desc();
    }

    private function parseFields(){

        $this->fields = $this->fields !== '' ? json_decode($this->fields) : [];

    }

}