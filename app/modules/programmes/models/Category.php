<?php

namespace app\modules\programmes\models;
use yii\easyii\models\Photo;
use creocoder\nestedsets\NestedSetsBehavior;

class Category extends \navatech\language\db\ActiveRecordCategory

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

        'options' => 'Options Room'

    ];

   

    public static function tableName()

    {

        return 'app_programmes_categories';

    }
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['ml'] = [
            'class'      => \navatech\language\components\LanguageBehavior::className(),
            'attributes' => [
                'title',
                'content',
                'summary',
                'slug'
            ],
            'languageField' => 'lang_code',
            'translateClassName' => '\app\modules\programmes\models\CategoryLang',
            'translateTableName' => 'app_programmes_categories_translate',
            'translateForeignKey' => 'cat_id'
        ];
        return $behaviors;
    }


    public function rules(){
        return array_merge(parent::rules(), [
            [['summary','sub_title','summary_title'], 'safe']
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


    private function parseFields(){

        $this->fields = $this->fields !== '' ? json_decode($this->fields) : [];

    }

}