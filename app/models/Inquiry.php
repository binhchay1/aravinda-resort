<?php
namespace app\models;

use yii\db\ActiveRecord;

class Inquiry extends ActiveRecord
{
	public static function tableName()
	{
		return '{{%inquiries}}';
	}
}
