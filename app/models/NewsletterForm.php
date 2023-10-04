<?php

namespace app\models;

use yii\base\Model;

class NewsletterForm extends Model
{

	public $prefix;
	public $fname;
	public $lname;
	public $email;
        public $confirm_email;
        public $country;
	public $verificationCode;
        public $fullName;

        public function rules()
	{
		return [
			[['prefix', 'fname', 'lname', 'country', 'email', 'confirm_email'], 'filter', 'filter'=>'trim'],
			[['email' ,'confirm_email' ], 'filter', 'filter'=>'strtolower'],
			[['country', 'email', 'confirm_email'], 'filter', 'filter'=>'strtolower'],
			[['fname', 'lname', 'fullName','email', 'confirm_email'], 'required'],
			[['email', 'confirm_email'], 'email'],
                        //[['email'],'compare','compareAttribute'=>'confirm_email'],
			[['verificationCode'], 'captcha', 'captchaAction' => 'amica-fr/captcha'],
		];
	}

	public function attributeLabels()
	{
		return [
			'prefix'=>'Votre civilité',
			'fname'=>'Votre Nom',
			'lname'=>'Votre Prénom',
			'email'=>'Votre adresse é-mail',
                        'confirm_email'=>'Confirmez votre adresse é-mail',
			'verificationCode' => 'Code anti-spam',
                        'fullName' => 'Votre Nom & prénom',
		];
	}
         public function scenarios() {
        return [
            'newsletter' => ['prefix', 'fullName', 'email', 'country', 'verificationCode'],
          ];
    }
}
