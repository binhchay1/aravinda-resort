<?php
namespace app\models;

use yii\db\ActiveRecord;

//class Country extends MyActiveRecord
class City extends ActiveRecord
{

	public static function tableName() {
		return '{{%city}}';
	}

	public function attributeLabels() {
		return [
			
			
		];
	}

	public function zbehaviors() {
		return [
			'timestamp' => [
				'class' => 'yii\behaviors\AutoTimestamp',
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => array('create_at', 'update_at'),
					ActiveRecord::EVENT_BEFORE_UPDATE => 'update_at',
				],
			],
		];
	}

	public function rules() {
		return array(
			//array('rate_dt, currency1, currency1, rate, note', 'filter', 'filter' => 'trim'),
			//array('rate_dt, currency1, currency1, rate', 'required'),
		);
	}

}
