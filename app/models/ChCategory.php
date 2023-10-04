<?php
namespace app\models;

use yii\db\ActiveRecord;

class ChCategory extends ActiveRecord
{
	public static function tableName() {
		return '{{app_whoarewe_categories}}';
	}
}
