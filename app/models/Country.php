<?php
namespace app\models;

use yii\db\ActiveRecord;

//class Country extends MyActiveRecord
class Country extends ActiveRecord
{

	public static function tableName() {
		return '{{%countries}}';
	}

	public function attributeLabels() {
		return [
			'code'=>'Country code',
			'dial_code'=>'Dial code',
			'name_en'=>'Name in English',
			'name_en'=>'Name in French',
			'name_vi'=>'Name in Vietnamese',
			'name_local'=>'Local name',
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
