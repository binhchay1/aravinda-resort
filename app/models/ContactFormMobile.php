<?php

namespace app\models;

use yii\base\Model;

class ContactFormMobile extends Model {

    public $tz = 'Europe/France';
    public $tourName = null;
    public $tourUrl = null;
    public $fullName = null; //mobile
    public $receiveName;
    public $prefix;
    
    public $fname;
    public $lname;
    public $email;
    public $confirm_email;
    public $age;
    public $country;
    public $message;
    public $countryCallingCode;
    public $phone;
    public $callDate;
    public $callTime;
    public $verificationCode;
    public $verificationImgCode; //mobile
    public $region;
    public $ville;
    public $question;
    public $subjet;
    public $newsletter = '1';
    public $reference;

    public function rules() {
        return [
            [['tourName', 'tourUrl', 'verificationImgCode','question', 'newsletter'], 'safe'],
            [['prefix', 'fname', 'lname', 'email', 'confirm_email', 'country', 'message', 'countryCallingCode', 'phone', 'callDate', 'callTime', 'receiveName','region','ville'], 'filter', 'filter' => 'trim'],
            [['country', 'email', 'confirm_email'], 'filter', 'filter' => 'strtolower'],
            [['prefix', 'fname', 'lname', 'email', 'confirm_email', 'age', 'fullName', 'message', 'phone', 'countryCallingCode', 'region', 'ville','verificationCode', 'subjet'], 'required','message' => '{attribute} ne peut pas être vide.'],
            [['message', 'subjet'], 'required', 'on' => ['contact', 'rdv', 'contactce', 'contact_mobile', 'rdv_mobile']],
            [['countryCallingCode', 'phone', 'callDate', 'callTime'], 'required', 'on' => 'rdv'],
            [['email', ' confirm_email'], 'email'],
            [['confirm_email'], 'compare', 'compareAttribute' => 'email'],
             // [['verificationCode'], 'captcha', 'captchaAction' => 'amica-fr/captcha'],
        ];
    }

    public function attributeLabels() {
        $email = 'Votre adresse e-mail';
        if ($this->scenario == 'send-wishlist') {
            $email = 'Mail du destinataire';
        }
        return [
            'prefix' => 'Votre civilité',
            'fname' => 'Votre Prénom',
            'lname' => 'Votre Nom',
            'email' => $email,
            'confirm_email' => 'Confirmez votre adresse é-mail',
            'age' => 'Date de naissance',
            'country' => 'Votre pays de résidence',
            'countryCallingCode' => 'Indicatif de pays',
            'phone' => 'Votre numéro de téléphone',
            'callTime' => 'Heure pour recevoir nos appels',
            'callDate' => 'Date pour recevoir nos appels',
            'message' => 'Votre message',
            'verificationCode' => 'Code anti-spam',
            'fullName' => 'Votre nom',
            'subjet' => 'Sujet',
            'region' => 'Département',
            'ville' => 'Votre ville',
            'newsletter'=>'Inscription à la newsletter',
        ];
    }

    public function scenarios() {
        return [
            'contact' => ['prefix', 'fname', 'lname', 'email', 'age', 'country', 'message', 'verificationCode', 'region', 'ville','subjet', 'newsletter', 'reference'],
            'send-wishlist' => ['prefix', 'email', 'age', 'verificationCode', 'message', 'receiveName', 'fullName'],
            'contactce' => ['prefix', 'fname', 'lname','email', 'age', 'country', 'message', 'verificationCode', 'question','region','ville', 'newsletter', 'reference'],
            'contact_mobile' => ['prefix', 'fullName', 'email', 'age', 'message', 'question', 'subjet', 'country', 'region','ville', 'reference','newsletter'],
            'contactce_mobile' => ['prefix', 'fullName', 'email', 'age', 'message', 'question', 'country', 'region','ville', 'reference','newsletter'],
            'rdv_mobile' => ['prefix', 'fullName', 'email', 'message', 'age', 'countryCallingCode', 'phone', 'region', 'ville', 'reference','newsletter'],
            'rdv' => ['prefix', 'fname', 'lname', 'email', 'country', 'age', 'region', 'ville', 'countryCallingCode', 'phone', 'callDate', 'callTime', 'verificationCode', 'newsletter', 'reference'],
        ];
    }

}
