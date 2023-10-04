<?php
namespace app\models;

use yii\db\ActiveRecord;

class ChItemData extends ActiveRecord
{
	public static function tableName() {
		return '{{app_whoarewe_item_data}}';
	}
}
