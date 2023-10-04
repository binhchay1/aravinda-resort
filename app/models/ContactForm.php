<?php

namespace app\models;

use yii\base\Model;
use yii\easyii\modules\text\api\Text;

class ContactForm extends Model {

    public $fullName;
    public $email;
    public $phone;
    public $subject;
    public $mesage;

    public function rules() {
        return [
            [['mesage'], 'safe'],
            ['email', 'email'],
            [['fullName', 'email', 'phone', 'subject'], 'required'],

            
        ];
    }

    public function attributeLabels() {
        return [
            'fullName' => Text::get('ho-ten'),
            'phone' => Text::get('phone'),
            'subject' => Text::get('tieu-de'),
            'mesage' => Text::get('loi-nhan')
        ];
    }

    // public function scenarios() {
    //     return [
    //         'contact' => ['prefix', 'fname', 'lname', 'email', 'age', 'country', 'message', 'region', 'ville','subjet', 'newsletter', 'reference', 'reCaptcha'],
    //         'send-wishlist' => ['prefix', 'email', 'age', 'verificationCode', 'message', 'receiveName', 'fullName'],
    //         'contactce' => ['prefix', 'fname', 'lname','email', 'age', 'country', 'message', 'question','region','ville', 'newsletter', 'reference'],
    //         'contactce_mobile' => ['prefix', 'fullName', 'email', 'age', 'message', 'verificationCode','question', 'reference'],
    //         'rdv_mobile' => ['prefix', 'fullName', 'email', 'age', 'message', 'verificationCode', 'countryCallingCode', 'phone', 'reference'],
    //         'rdv' => ['prefix', 'fname', 'lname', 'email', 'age', 'country', 'region', 'ville', 'countryCallingCode', 'phone', 'callDate', 'callTime', 'message', 'newsletter', 'reference'],
    //         'rdv-surparis' => ['prefix', 'fname', 'lname', 'email', 'age', 'period', 'phone', 'callDate', 'message', 'newsletter', 'reference'],
    //     ];
    // }

}
