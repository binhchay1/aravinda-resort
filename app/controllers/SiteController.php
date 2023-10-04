<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use yii\data\Pagination;
use yii\helpers\Security;
use yii\httpclient\Client;
use app\modules\programmes\api\Catalog;
use app\modules\programmes\models\Category;
use app\modules\programmes\models\CategoryLang;
use yii\easyii\modules\page\api\Page;
use yii\easyii\modules\page\models\PageLang;
use app\modules\programmes\models\ItemLang;
use yii\easyii\modules\text\api\Text;
use app\models\BookingForm;
use Mailgun\Mailgun;

class SiteController extends Controller {
	public $page;
	public $entry;
   public $menu;
   public $otherMenu;
	public function __construct($id, $module, $config = []) {
        parent::__construct($id, $module, $config);

        Yii::$app->language = 'en';
        if($cat = CategoryLang::findOne(['slug'=>URI])){
            Yii::$app->language = $cat->lang_code;
        } 
        if($item = ItemLang::findOne(['slug'=>URI])){
            Yii::$app->language = $item->lang_code;
        } 
        if(SEG1=='vi'){
            Yii::$app->language = 'vi';
        }
        if(SEG1=='fr'){
            Yii::$app->language = 'fr';
        }
        if(!SEG1) Yii::$app->language = 'en';
        $this->page = \yii\easyii\modules\page\api\Page::get(37);
        $menu = Category::find()->roots()->all();
        $menu = ArrayHelper::map($menu, function($e){
            return $e->category_id;
        }, function($e){
            return $e;
        });
        $menu[14] = Catalog::cat(14);
        $this->menu = $menu;
        $otherMenu = [];
        $otherMenu['gallery'] = Catalog::get('gallery');
        $otherMenu['contact'] = Catalog::get('contact');
        $otherMenu['booking'] = Catalog::get('booking-engine');
        $otherMenu['tuyendung'] = Catalog::cat('careers');
        $otherMenu['policy'] = Catalog::get('privacy-policy');
        $this->otherMenu = $otherMenu;
   }

   public function actionIndex(){
         $theEntry = Page::get('/');
         $useMod = \app\modules\modulepage\api\Catalog::get(1);
         $dataUse = \app\modules\programmes\api\Catalog::items(['where'=>['in','item_id', $useMod->data->items],'pagination' => ['pageSize'=>0]]);
         $this->entry = $theEntry;
         $desMod = \app\modules\modulepage\api\Catalog::get(4);
         $dataDes= \app\modules\programmes\api\Catalog::items(['where'=>['in','item_id', $desMod->data->items],'pagination' => ['pageSize'=>0]]);

         $gasMod = \app\modules\modulepage\api\Catalog::get(5);
          $dataGas= \app\modules\programmes\api\Catalog::items(['where'=>['in','item_id', $gasMod->data->items],'pagination' => ['pageSize'=>0],
          'orderBy' => [new \yii\db\Expression('FIELD (item_id, ' . implode(',',$gasMod->data->items) . ')')]
       ]);
   		return $this->render('home', [
            'dataUse' => $dataUse,
            'dataDes' => $dataDes,
            'dataGas' => $dataGas,
            'theEntry' => $theEntry
         ]);
   }

   public function actionVilla(){
   		$theEntry = Catalog::cat(11);
   		if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
   		$this->entry = $theEntry;
   		return $this->render('villa', [
   			'theEntry' => $theEntry
   		]);
   }

   public function actionVillaSingle(){
   		$theEntry = Catalog::get(URI);
         if($langData = ItemLang::findOne(['slug' => URI])){
            $theEntry = Catalog::get($langData->item_id);
         }
   		if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
   		$this->entry = $theEntry;
         $others = Catalog::items([
            'where' => ['and', 'category_id = '.$theEntry->cat->category_id.'', ['not', ['item_id' => $theEntry->model->item_id]]],
            'orderBy'=>'rand()',
            'pagination'=>['pageSize'=>3]
            ]);
   		return $this->render('villa-single', [
   			'theEntry' => $theEntry,
            'others' => $others
   		]);

   }
    public function actionAmthuc(){
   		$theEntry = Catalog::cat(12);
   		if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
   		$this->entry = $theEntry;
   		return $this->render('amthuc', [
   			'theEntry' => $theEntry
   		]);
   }
   public function actionAmthucSingle(){
   		$theEntry = Catalog::get(URI);
         if($langData = ItemLang::findOne(['slug' => URI])){
            $theEntry = Catalog::get($langData->item_id);
         }
   		if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
   		$this->entry = $theEntry;
   		$others = Catalog::items([
            'where' => ['and', 'category_id = '.$theEntry->cat->category_id.'', ['not', ['item_id' => $theEntry->model->item_id]]],
            'orderBy'=>'rand()',
            'pagination'=>['pageSize'=>3]
            ]);
   		return $this->render('amthuc-single', [
   			'theEntry' => $theEntry, 
   			'others' => $others
   		]);
   }
   public function actionTrainghiem(){
   		$theEntry = Catalog::cat(13);
   		if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
   		$this->entry = $theEntry;
   		return $this->render('trainghiem', [
   			'theEntry' => $theEntry
   		]);
   }
   public function actionTrainghiemSingle(){
   		$theEntry = Catalog::get(URI);
         if($langData = ItemLang::findOne(['slug' => URI])){
            $theEntry = Catalog::get($langData->item_id);
         }
   		if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
   		$this->entry = $theEntry;
   		$others = Catalog::items([
            'where' => ['and', 'category_id = '.$theEntry->cat->category_id.'', ['not', ['item_id' => $theEntry->model->item_id]]],
            'orderBy'=>'rand()',
            'pagination'=>['pageSize'=>3]
            ]);
   		return $this->render('trainghiem-single', [
   			'theEntry' => $theEntry, 
   			'others' => $others
   		]);
   }
   public function actionAboutUs(){
   		$theEntry = Catalog::cat(10);
   		if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
   		$this->entry = $theEntry;
   		return $this->render('about-us', [
   			'theEntry' => $theEntry
   		]);
   }
   public function actionPromotion(){
   		$theEntry = Catalog::cat(14);
   		if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
   		$this->entry = $theEntry;
   		return $this->render('promotion', [
   			'theEntry' => $theEntry
   		]);
   }
   public function actionPromotionSingle(){
   		$theEntry = Catalog::cat(URI);
       if($langData = CategoryLang::findOne(['slug' => URI])){
            $theEntry = Catalog::cat($langData->cat_id);
         }
   		if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
   		$this->entry = $theEntry;
      if($langData){
        $parent = Category::findOne(['category_id' => $theEntry->model->category_id])->parents(1)->one();
        $others = Category::findOne($parent->category_id)->children(1)->andWhere(['<>','category_id', $theEntry->model->category_id])->all();
      }
   		
      else
      $others = Category::findOne(['slug' => SEG1])->children(1)->andWhere(['<>','slug', URI])->all();  
   		return $this->render('promotion-single', [
   			'theEntry' => $theEntry, 
   			'others' => $others
   		]);
   }
   public function actionThongtin(){
   		$theEntry = Catalog::cat(15);
   		if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
   		$this->entry = $theEntry;
   		return $this->render('thongtin', [
   			'theEntry' => $theEntry
   		]);
   }

   public function actionBooking(){
      $theEntry = Catalog::get(URI);
         if($langData = ItemLang::findOne(['slug' => URI])){
            $theEntry = Catalog::get($langData->item_id);
         }
      if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
      $this->entry = $theEntry;
      $typeRooms = Catalog::cat(11)->items();
      $typeRooms = ArrayHelper::map($typeRooms, 'title', 'title');
      $promos = Catalog::cat(14)->items();
      $promos = ArrayHelper::map($promos, 'title', 'title');
      array_unshift($promos, '');
      $model = new BookingForm;
      if ($model->load($_POST) && $model->validate()) {
          $data2 = <<<'TXT'
FullName: $fullName
Email: $email
Phone: $phone
Check in: $dateCome
Check out: $dateGo
Type Room: $typeRoom
Number Room: $numRoom
Adults: $adults
Children: $child
Age Children: $ageChild
Promotion: $promo
Message:   $mesage
TXT;
                  $data2 = str_replace([
                    '$fullName', '$email', '$phone', '$dateCome', '$dateGo', '$typeRoom', '$numRoom', '$adults', '$child', '$ageChild', '$promo', '$mesage'
                    ], [
                    $model->fullName, $model->email, $model->phone, $model->dateCome, $model->dateGo, $model->typeRoom, $model->numRoom, $model->adults, $model->child, $model->ageChild, $model->promo,$model->mesage
                    ], $data2);
            
                $this->notifyEmail('Reservation from ' . $model->email,  $data2);
                return Yii::$app->response->redirect(DIR.'thank-you');
      }
     return $this->render('booking', [
        'theEntry' => $theEntry, 
        'model' => $model,
        'typeRooms' => $typeRooms,
        'promos' => $promos
      ]);
   }

   public function actionContact(){
      $theEntry = Catalog::get(URI);
         if($langData = ItemLang::findOne(['slug' => URI])){
            $theEntry = Catalog::get($langData->item_id);
         }
      if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
      $this->entry = $theEntry;
      $model = new \app\models\ContactForm;
      if ($model->load($_POST) && $model->validate()) {
          $data2 = <<<'TXT'
FullName: $fullName
Email: $email
Phone: $phone
Message:   $mesage
TXT;
                  $data2 = str_replace([
                    '$fullName', '$email', '$phone', '$mesage'
                    ], [
                    $model->fullName, $model->email, $model->phone, $model->mesage
                    ], $data2);
            
                $this->notifyEmail('Contact from ' . $model->email.': '.$model->subject,  $data2);
                return Yii::$app->response->redirect(DIR.'thank-you');
      }
     return $this->render('contact', [
        'theEntry' => $theEntry, 
        'model' => $model
      ]);
   }

   public function actionThank(){
      $theEntry = Catalog::get(URI);
         if($langData = ItemLang::findOne(['slug' => URI])){
            $theEntry = Catalog::get($langData->item_id);
         }
      if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
      $this->entry = $theEntry;
      return $this->render('thank', [
        'theEntry' => $theEntry, 
      ]);
   }


   public function actionGallery(){
      $theEntry = Catalog::get(URI);
         if($langData = ItemLang::findOne(['slug' => URI])){
            $theEntry = Catalog::get($langData->item_id);
         }
      if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
      $this->entry = $theEntry;
      return $this->render('gallery', [
        'theEntry' => $theEntry, 
      ]);
   }

   public function notifyEmail($subject = '', $template = '', $email='reservation@aravindaresort.com') {
        $mgClient = new Mailgun(MAILGUN_API_KEY);
        $result = $mgClient->sendMessage(MAILGUN_API_DOMAIN, [
            'from' => 'info@aravindaresort.com',
            'to' =>  ['sales@aravindaresort.com', 'info@aravindaresort.com', 'ngocanh.hoa@aravindaresort.com'],
             'subject' => $subject,
            'text' => $template,
                ]
        );
    }

     public function actionError(){

      return $this->render('thank', [
        'theEntry' => $theEntry, 
      ]);
   }

   public function actionCarrier(){
      $theEntry = Catalog::cat(URI);
      if($langData = CategoryLang::findOne(['slug' => URI])){
            $theEntry = Catalog::cat($langData->cat_id);
         }
      if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
      $this->entry = $theEntry;
      return $this->render('tuyendung', [
        'theEntry' => $theEntry
      ]);
   }

   public function actionEntry(){
      $theEntry = Catalog::get(URI);
       if($langData = ItemLang::findOne(['slug' => URI])){
            $theEntry = Catalog::get($langData->item_id);
         }
        if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
        $this->entry = $theEntry;
        return $this->render('entry', [
          'theEntry' => $theEntry
        ]);
   }
   public function actionEntryOther(){
      $theEntry = Catalog::get(URI);
       if($langData = ItemLang::findOne(['slug' => URI])){
            $theEntry = Catalog::get($langData->item_id);
         }
        if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
        $this->entry = $theEntry;
        $others = Catalog::items([
            'where' => ['and', 'category_id = '.$theEntry->cat->category_id.'', ['not', ['item_id' => $theEntry->model->item_id]]],
            'orderBy'=>'rand()',
            'pagination'=>['pageSize'=>3]
            ]);
        return $this->render('entry-other', [
          'theEntry' => $theEntry,
          'others' => $others
        ]);
   }

   public function actionChangeLang($lang = 'en'){
      Yii::$app->language = $lang;
      $uri = Yii::$app->request->get('uri');
      if(!$uri || $uri == 'en' || $uri == 'vi' || $uri == 'fr'){
        if($lang == 'en') $lang = '';
        Yii::$app->response->redirect('/'.$lang, 301)->send();
            Yii::$app->end();
      }
      $catLang = CategoryLang::findOne(['slug' => $uri]);

      if($lang == 'en'){
        $catLang = CategoryLang::findOne(['slug' => $uri]);
        if($catLang){
          $catLink = Catalog::cat($catLang->cat_id);
            Yii::$app->response->redirect('/'.$catLink->slug, 301)->send();
            Yii::$app->end();
        }
        $itemLang = ItemLang::findOne(['slug' => $uri]);
        if($itemLang){
          $itemLink = Catalog::get($itemLang->item_id);
            Yii::$app->response->redirect('/'.$itemLink->slug, 301)->send();
            Yii::$app->end();
        }
      } else{
        $catLang = CategoryLang::findOne(['slug' => $uri]);
        $catLangEn = Catalog::cat($uri);
        if($catLang || $catLangEn){
            if($catLang)
            $catLink = CategoryLang::findOne(['lang_code' => $lang, 'cat_id' => $catLang->cat_id]);
            else $catLink = CategoryLang::findOne(['lang_code' => $lang, 'cat_id' => $catLangEn->model->category_id]);
            Yii::$app->response->redirect('/'.$catLink->slug, 301)->send();
            Yii::$app->end();
        }
        $itemLang = ItemLang::findOne(['slug' => $uri]);
        $itemLangEn = Catalog::get($uri);
        if($itemLang || $itemLangEn){
          if($itemLang)
          $itemLink = ItemLang::findOne(['lang_code' => $lang, 'item_id' => $itemLang->item_id]);
          else 
            $itemLink = ItemLang::findOne(['lang_code' => $lang, 'item_id' => $itemLangEn->model->item_id]);
            Yii::$app->response->redirect('/'.$itemLink->slug, 301)->send();
            Yii::$app->end();
        }


      }
      
      
      
   }

   public function actionBookingEngine(){
      $theEntry = Page::get(URI);
      $this->entry = $theEntry;
      return $this->render('booking-engine');
   }
}