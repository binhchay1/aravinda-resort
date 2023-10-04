<?php

namespace app\models;

use yii\base\Model;
use yii\easyii\modules\text\api\Text;

class BookingForm extends Model {

    public $fullName;
    public $email;
    public $phone;
    public $dateCome;
    public $dateGo;
    public $typeRoom;
    public $numRoom;
    public $adults = '';
    public $child = '';
    public $ageChild;
    public $mesage;
    public $promo = '';

    public function rules() {
        return [
            [['mesage', 'child', 'promo'], 'safe'],
            ['email', 'email'],
            [['fullName', 'email', 'phone', 'dateCome', 'dateGo', 'typeRoom', 'numRoom', 'adults'], 'required'],
            [['adults', 'numRoom'], 'number', 'integerOnly'=>true, 'min'=>1],
            ['ageChild', 'required', 'when'=>function($model) {
                return $model->child != '';
            }]
            
        ];
    }

    public function attributeLabels() {
        return [
            'fullName' => Text::get('ho-ten'),
            'phone' => Text::get('phone'),
            'dateCome' => Text::get('ngay-den'),
            'dateGo' => Text::get('ngay-di'),
            'numRoom' => Text::get('so-phong'),
            'typeRoom' => Text::get('loai-phong'),
            'adults' => Text::get('ng-lon'),
            'child' => Text::get('tre-em'),
            'ageChild' => Text::get('tuoi-tre-em'),
            'mesage' => Text::get('loi-nhan'),
            'promo' => strip_tags(Text::get('promo'))
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
