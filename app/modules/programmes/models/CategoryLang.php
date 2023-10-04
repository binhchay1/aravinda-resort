<?php

namespace app\modules\programmes\models;
use yii\easyii\models\Photo;
use yii\db\ActiveRecord;
use creocoder\nestedsets\NestedSetsBehavior;
use app\modules\programmes\models\Category;

class CategoryLang extends ActiveRecord

{

    public static function tableName()

    {

        return 'app_programmes_categories_translate';

    }



    public function rules(){
        return array_merge(parent::rules(), [
            [['title', 'content', 'slug', 'summary'], 'safe']
            ]);
    }

    public function behaviors()
    {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                'treeAttribute' => 'tree'
            ]
        ];
    }

    public function getCategory()

    {

        return $this->hasMany(Category::className(), ['category_id' => 'cat_id'])->sortDate();

    }

    public function children($depth = null)
    {
        $condition = [
            'and',
            ['>', $this->leftAttribute, $this->owner->getAttribute($this->leftAttribute)],
            ['<', $this->rightAttribute, $this->owner->getAttribute($this->rightAttribute)],
        ];

        if ($depth !== null) {
            $condition[] = ['<=', $this->depthAttribute, $this->owner->getAttribute($this->depthAttribute) + $depth];
        }

        $this->applyTreeAttributeCondition($condition);

        return $this->owner->find()->andWhere($condition)->addOrderBy([$this->leftAttribute => SORT_ASC]);
    }
}