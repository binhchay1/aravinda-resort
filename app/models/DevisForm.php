<?php

namespace app\models;

use yii\base\Model;

class DevisForm extends Model
{

	public $tourName = null;
	public $tourUrl = null;

	public $prefix;
	public $fname;
	public $lname;
	public $email;
        public $confirm_email;
        public $age;
        public $country;
	public $city;
	public $departureDate;
        public $deretourDate;
	public $tourLength;
	public $countriesToVisit = 'Vietnam';
	// question
	public $whyCountry;

	public $agesOfTravelers12;
	public $numberOfTravelers12;
	public $numberOfTravelers2;
	public $numberOfTravelers0;
	// question
	public $howTraveler;
	public $message;
	// question
	public $howMessage;
	public $budget;
	public $tourThemes;
	//question
	public $howHobby;
	public $hotelTypes;
	public $hotelRoomDbl;
	public $hotelRoomTwn;
	public $hotelRoomTrp;
	public $hotelRoomSgl;
	public $mealsIncluded = 'Non';

	public $callback = 'Non';
	public $callTime = '';
	public $callDate = '';
	public $countryCallingCode = '';
	public $phone = '';

	public $newsletter = '1';
	public $verificationCode;
	//question
	public $howTicket;
	public $job;
        public $region;
        public $ville;
        public $fullName;
        public $extension = '';
    public $ticketDetail;
    public $helpTicket;
    public $multipays;
    public $reference;

        public function isderetourDateGreater($attribute, $params) {
           
           
            if( strtotime($this->deretourDate) < strtotime($this->departureDate) ) {
                $this->addError('deretourDate', 'Date de retour must be greater than Date d\'arrivée approximative .');
            }
        }
	public function rules() {
           
		return [
			['tourName, tourUrl, whyCountry, howTraveler, howMessage, howHobby, howTicket, job, ticketDetail, helpTicket', 'safe'],
			['prefix, fullName, fname, lname, country, email,confirm_email ,phone, callTime, callDate, message', 'filter', 'filter'=>'trim'],
			['email , confirm_email', 'filter', 'filter'=>'strtolower'],
			[['email' , 'confirm_email'], 'email'],
			[['prefix', 
'fname', 
'lname', 
'email', 
'confirm_email',
'country', 
'city', 
//'departureDate', 
//'deretourDate',
'tourLength', 

'numberOfTravelers12', 
'numberOfTravelers2', 
'numberOfTravelers0', 
//'agesOfTravelers12',
'message',

'hotelRoomDbl', 
'hotelRoomTwn', 
'hotelRoomTrp', 
'hotelRoomSgl', 
'mealsIncluded', 
//'countriesToVisit',
'callback', 

'newsletter', 
//'verificationCode',
//'region',
//'ville',
'fullName', 
'age',
], 'required', 'message' => '{attribute} ne peut être vide.'],

//[['fname'],'required','message'=>'Merci d\'indiquer votre prénom'],
//[['lname'],'required','message'=>'Merci d\'indiquer votre nom de famille'],
//[['email'],'required','message'=>'Merci de nous donner votre adresse mail'],
[['region'],'required','message'=>'Département requis'],
[['ville'],'required','message'=>'Ville requise'],
[['departureDate'],'required','message'=>'Date d\'arrivée requise'],
[['deretourDate'],'required','message'=>'Date de retour requise'],
[['countriesToVisit'],'required','message'=>'Destination(s) requise(s)'],
[['agesOfTravelers12'],'required','message'=>'Âge des participants requis'],
//[['message'],'required','message'=>'Merci de préciser votre projet de voyage'],
[['verificationCode'],'required','message'=>'Merci de remplir le code anti-spam'],


                        ['deretourDate','isderetourDateGreater'],
                   
                       // ['deretourDate,departureDate','date','format'=>'dd/MM/yyyy'],
                        // ['deretourDate','compare','compareAttribute'=>'departureDate','operator'=>'>' , 'message'=>'{attribute} must be greater than "{compareValue}".'],
                        //['email','compare','compareAttribute'=>'confirm_email'],
			['callback', 'boolean', 'trueValue'=>'Oui', 'falseValue'=>'Non'],
						[['callTime', 'countryCallingCode'], 'required', 'when' => function ($model) {
                            return $model->callback == 'Oui';
                        }, 'whenClient' => "function (attribute, value) {
                                    return $('#devisform-callback label:first-of-type input[type=radio]').is(':checked') == true;
                                }",'message' => '{attribute} ne peut pas être vide.'],
                                
                        [[ 'callDate'], 'required', 'when' => function ($model) {
                            return $model->callback == 'Oui';
                        }, 'whenClient' => "function (attribute, value) {
                                    return $('#devisform-callback label:first-of-type input[type=radio]').is(':checked') == true;
                                }",'message' => 'Date de rendez-vous requise'],
                          
                        [['phone'], 'required', 'when' => function ($model) {
                            return $model->callback == 'Oui';
                        }, 'whenClient' => "function (attribute, value) {
                                    return $('#devisform-callback label:first-of-type input[type=radio]').is(':checked') == true;
                                }",'message' => 'Votre numéro de téléphone ne peut être vide'],      
                                
                        [['multipays'], 'required', 'when' => function ($model) {
                            
                            return $model->countriesToVisit == 'Multi-pays';
                        }, 'whenClient' => "function (attribute, value) {
                                    return $('#devisform-countriestovisit label:last-of-type input[type=radio]').is(':checked') == true;
                                }",'message' => 'Multi-pays requis'],         
                                
          //	[['callTime', 'callDate', 'countryCallingCode', 'phone'], 'required'],
			['newsletter', 'boolean'],
			['budget, countriesToVisit, tourThemes, hotelTypes', 'safe'],
			[['tourLength'], 'number', 'integerOnly'=>true, 'min'=>1, 'tooSmall'=>'Durée requise'],
			[['numberOfTravelers12'], 'number', 'integerOnly'=>true, 'min'=>1, 'tooSmall'=>'Nombre des participants requis'],   
                        [['age'], 'number', 'integerOnly'=>true],
			[['verificationCode'], 'captcha', 'captchaAction' => 'amica-fr/captcha'],
		];
	}
        
	public function attributeLabels()
	{
		return [
			'prefix'=>'Votre civilité',
			'fname'=>'Votre prénom',
			'lname'=>'Votre nom de famille',
			'email'=>'Votre adresse mail',
                        'confirm_email'=>'Confirmez votre adresse mail',
                        'age' => 'Date de naissance',
			'country'=>'Votre pays de résidence',
			'city'=>'Votre ville de résidence',
			'departureDate'=>'Date d\'arrivée approximative',
                        'deretourDate'=>'Date de retour',
			'tourLength'=>'Durée du voyage',
			'countriesToVisit'=>'Destination(s)',
			'agesOfTravelers12'=>'Age des voyageurs',
			'numberOfTravelers12'=>'Nombre de voyageurs + de 12 ans',
			'numberOfTravelers2'=>'Nombre de voyageurs - de 12 ans',
			'numberOfTravelers0'=>'Nombre de voyageurs - de 2 ans',
			'message'=>'Votre projet de voyage',
			'budget'=>'Budget par personne',
			'tourThemes'=>'Thématiques',
			'hotelTypes'=>'Types d’hébergement',
			'hotelRoomDbl'=>'Nombre de chambres doubles',
			'hotelRoomTwn'=>'Nombre de chambres twin',
			'hotelRoomTrp'=>'Nombre de chambres pour 3 personnes',
			'hotelRoomSgl'=>'Nombre de chambres individuelle',
			'mealsIncluded'=>'Autres repas',
			'callback'=>'RDV téléphonique',
			'callTime'=>'Heure pour recevoir nos appels',
			'callDate'=>'Date pour recevoir nos appels',
			'countryCallingCode'=>'Indicatif de pays',
			'phone'=>'Votre numéro de téléphone',
			'newsletter'=>'Inscription à la newsletter',
			'verificationCode'=>'Code anti-spam',
                        'region' => 'Département',
                        'ville' => 'Votre ville',
                        'fullName' => 'Votre Nom & prénom',
						'job' => 'Votre (vos) professions ?',
		];
	}

	public function beforeValidate()
	{
		if ($this->callback == 'Non') {
			$this->setAttributes([
				'callDate'=>'none',
				'callTime'=>'none',
				'countryCallingCode'=>'none',
				'phone'=>'none',
			]);
		}
		return true;
	}

	public function afterValidate()
	{
		if ($this->callback == 'Non') {
			$this->setAttributes([
				'callDate'=>'',
				'callTime'=>'',
				'countryCallingCode'=>'',
				'phone'=>'',
			]);
		}
		return true;
	}
	
	 public function scenarios()
    {
        return [
        'landing' => [
        'prefix',
        'fname',
        'lname',
        'email',
        'confirm_email',    
        'country',
        'city',
        'departureDate',
        'agesOfTravelers12',
        'numberOfTravelers12',
        'numberOfTravelers2',
        'numberOfTravelers0',
        'message',

        'callback',
        'callTime',
        'callDate',
        'countryCallingCode',
        'phone',
        'newsletter',
        'verificationCode'],
            
        'default'=>['tourName', 'tourUrl', 'prefix', 'fname', 'lname', 'email','confirm_email', 'country', 'city', 'departureDate','deretourDate', 'tourLength', 'countriesToVisit', 'multipays', 'agesOfTravelers12', 'numberOfTravelers12', 'numberOfTravelers2',
        'numberOfTravelers0',
        'message',
        'budget',
        'tourThemes',
        'hotelTypes',
        'hotelRoomDbl',
        'hotelRoomTwn',
        'hotelRoomTrp',
        'hotelRoomSgl',
        'mealsIncluded' ,
        'callback' ,
        'callTime',
        'callDate',
        'countryCallingCode',
        'phone',
        'newsletter',
        'verificationCode',

        'whyCountry',
        'howMessage',
        'howTraveler',
        'howHobby',
        'howTicket',
        'job',
        'region',
        'ville',  
        'fullName',   
        'ticketDetail',
        'helpTicket',
        'reference',  
        'age',    
	 ],
            
	 'booking'=> ['tourName', 'tourUrl', 'extension','prefix', 'fname', 'lname', 'email', 'country', 'region', 'ville', 'departureDate','deretourDate', 'agesOfTravelers12', 'numberOfTravelers12', 'numberOfTravelers2', 'tourLength',
        'numberOfTravelers0',
        'message',
        'budget',
        'hotelTypes',
        'hotelRoomDbl',
        'hotelRoomTwn',
        'hotelRoomTrp',
        'hotelRoomSgl',
        'mealsIncluded' ,
        'callback' ,
        'callTime',
        'callDate',
        'countryCallingCode',
        'phone',
        'newsletter',
        //'verificationCode',
        'whyCountry',
        'howMessage',
        'howTraveler',
        'howHobby',
        'howTicket',
		'job',
        'ticketDetail',
        'helpTicket',
        'reference',  
        'age',     
	 ],
            
             'devis'=> ['prefix', 'fname', 'lname', 'email', 'country', 'region', 'ville', 'departureDate','deretourDate', 'agesOfTravelers12', 'numberOfTravelers12', 'numberOfTravelers2','countriesToVisit', 'multipays', 'tourLength', 'tourThemes',
        'numberOfTravelers0',
        'message',
        'budget',
        'hotelTypes',
        'hotelRoomDbl',
        'hotelRoomTwn',
        'hotelRoomTrp',
        'hotelRoomSgl',
        'mealsIncluded' ,
        'callback' ,
        'callTime',
        'callDate',
        'countryCallingCode',
        'phone',
        'newsletter',
        //'verificationCode',
        'whyCountry',
        'howMessage',
        'howTraveler',
        'howHobby',
        'howTicket',
		'job',
        'ticketDetail',
        'helpTicket',
        'reference',      
        'age',         
	 ]
        ];
    }
    
}
