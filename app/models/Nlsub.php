<?php
namespace app\models;
use yii\db\ActiveRecord;
class Nlsub extends ActiveRecord
{
	public static function tableName()
	{
		return '{{%nlsub}}';
	}
}
