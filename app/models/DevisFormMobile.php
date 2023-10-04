<?php

namespace app\models;

use yii\base\Model;

class DevisFormMobile extends Model {

    public $has_date = 1;
    public $tourName = null;
    public $tourUrl = null;
    public $typeGo = null;
    public $interest = null;
    public $contactEmail = 1;
    public $contactPhone = 0;
    public $fullName = null;
    public $prefix;
    public $fname;
    public $lname;
    public $email;
    public $age;
    public $departureDate;
    public $tourLength;
   // public $countriesToVisit = [];
    public $countriesToVisit = 'Vietnam';
    public $numberOfTravelers12;
    public $numberOfTravelers2;
    public $numberOfTravelers1;
    public $message;
    public $tourThemes;
    public $phone='';
    public $country;
    public $countryCallingCode;
    
    public $verificationCode;
    public $verificationImgCode;
	public $agesOfTravelers12;
    public $hotelRoomDbl;
    public $hotelRoomTwn;
    public $hotelRoomTrp;
    public $hotelRoomSgl;
	public $budget;
    // question
    public $whyCountry;
    public $howTraveler;
    public $howMessage;
    public $howHobby;
    public $howTicket;
    public $job;
	public $lepetit;

     public $region;
    public $ville;

    public $listFormules;
    public $multipays;
    public $reference;
    public $newsletter = '1';
    public $extension = '';
    public function rules() {
        return [
            [['tourName', 'tourUrl', 'tourThemes', 'verificationImgCode', 'prefix',  'budget','hotelRoomDbl','hotelRoomTwn','hotelRoomTrp','hotelRoomSgl', 'whyCountry', 'howTraveler', 'howMessage', 'howHobby', 'contactEmail', 'howTicket', 'job', 'lepetit', 'numberOfTravelers1', 'verificationCode'], 'safe'],
            //[['countriesToVisit'],'required','on'=>'devis_mobile'],
            [['email'], 'filter', 'filter' => 'strtolower'],
            [['email'], 'email'],
            [['fullName', 'email', 'age','numberOfTravelers12', 'message', 'agesOfTravelers12', 'region', 'ville'], 'required'],
            [['departureDate'], 'required'],
            [['tourLength'],'integer', 'min' => 1],
            [['phone'], 'required', 'when' => function ($model) {
            return $model->contactPhone == 1;
        }, 'whenClient' => "function (attribute, value) {
                    return $('#devisformmobile-contactphone').is(':checked') == true;
                }", 'on' => ['mobileTour', 'devis_mobile']],
             
            [['multipays'], 'required', 'when' => function ($model) {
                            
                            return $model->countriesToVisit == 'Multi-pays';
                        }, 'whenClient' => "function (attribute, value) {
                                    return $('#devisformmobile-countriestovisit .ui-radio:last-of-type input[type=radio]').is(':checked') == true;
                                }",'message' => 'Multi-pays requis'],     
                
            ['numberOfTravelers12', 'number', 'integerOnly' => true, 'min' => 1],
            // ['verificationCode', 'captcha', 'captchaAction' => 'amica-fr/captcha'],
        ];
    }

    public function attributeLabels() {
        return [
            'prefix' => 'Votre civilité',
            'fname' => 'Votre nom',
            'lname' => 'Votre prénom',
            'email' => 'Votre adresse mail',
            'age' => 'Date de naissance',
            'departureDate' => 'Date d\'arrivée approximative',
            'tourLength' => 'Durée du voyage',
            'numberOfTravelers12' => 'Nombre de voyageurs + de 12 ans',
            'numberOfTravelers2' => 'Nombre de voyageurs - de 12 ans',
            'message' => 'Votre message',
            'countriesToVisit' => 'Pays à visiter',
            'tourThemes' => 'Thématiques',
            'phone' => 'Numéro de téléphone',
            'verificationCode' => 'Code anti-spam',
            'fullName' => 'Votre nom',
			'agesOfTravelers12' => "Détails d'âges",
            'budget' => 'Budget par personne',
            'hotelRoomDbl' => 'Nombre de chambres doubles',
            'hotelRoomTwn' => 'Nombre de chambres twin',
            'hotelRoomTrp' => 'Nombre de chambres pour 3 personnes',
            'hotelRoomSgl' => 'Nombre de chambres individuelle',
            'region' => 'Département',
            'ville' => 'Votre ville'
        ];
    }
    
    public function scenarios() {
        return [
            'mobileBooking' => ['typeGo', 'numberOfTravelers12', 'numberOfTravelers2', 'interest', 'phone', 'fullName', 'email', 'age', 'message', 'has_date', 'tourLength', 'contactPhone', 'departureDate', 'agesOfTravelers12','hotelRoomDbl', 'hotelRoomTwn','hotelRoomTrp','hotelRoomSgl','budget', 'whyCountry', 'howTraveler', 'howMessage', 'howHobby', 'contactEmail', 'howTicket', 'job', 'lepetit', 'lepetit', 'region', 'ville', 'numberOfTravelers1', 'reference','newsletter', 'extension', 'country'],
            'devis_mobile' => ['typeGo', 'numberOfTravelers12', 'numberOfTravelers2', 'interest', 'phone', 'fullName', 'email', 'age', 'message', 'countriesToVisit', 'multipays', 'tourThemes', 'tourLength', 'contactPhone', 'departureDate', 'has_date', 'agesOfTravelers12','hotelRoomDbl', 'hotelRoomTwn','hotelRoomTrp','hotelRoomSgl','budget', 'whyCountry', 'howTraveler', 'howMessage', 'howHobby', 'contactEmail', 'howTicket', 'job', 'lepetit', 'region', 'ville', 'numberOfTravelers1', 'reference','newsletter'],
            'landing' => ['numberOfTravelers12', 'numberOfTravelers2', 'phone', 'fullName', 'email', 'age', 'message', 'verificationCode', 'contactPhone', 'departureDate', 'agesOfTravelers12'],
            ];
    }

}
