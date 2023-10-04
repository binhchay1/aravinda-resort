<?php
namespace app\models;

use yii\db\ActiveRecord;

class ChItems extends ActiveRecord
{
	public static function tableName() {
		return '{{app_whoarewe_items}}';
	}
        public function getData()
	{
		return $this->hasMany(ChItemData::className(), ['item_id' => 'item_id']);
	}
        public function getSeotext()
	{
		return $this->hasOne(Seotext::className(), ['item_id' => 'item_id']);
	}
}
