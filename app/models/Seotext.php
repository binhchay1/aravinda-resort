<?php
namespace app\models;

use yii\db\ActiveRecord;

class Seotext extends ActiveRecord
{
	public static function tableName() {
		return '{{easyii_seotext}}';
	}
}
