<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use yii\data\Pagination;
use yii\helpers\Security;
use vendor\Mobile_Detect\Mobile_Detect;
use app\modules\whoarewe\api\Catalog;
use app\models\ContactForm;
use app\models\ContactFormMobile;
use app\models\Country;
use app\models\DevisForm;
use app\models\DevisFormMobile;
use app\models\NewsletterForm;
use app\models\Inquiry;
use app\models\Nlsub;
use Mailgun\Mailgun;
use app\models\ChItems;
use app\models\ChCategory;
use yii\easyii\modules\page\api\Page;
use app\helpers\Mailjet;

use yii\httpclient\Client;

class AmicaFrController extends Controller {
   
   public $destiMenu = [];
   public $ideesMenu = [];
   public $excluMenu = [];
   public $aproMenu = [];
   public $pageT = '';
   public $metaT = '';
   public $metaD = '';
   public $seoContent = '';
   public $metaK = '';
    public $metaFbT = '';
    public $metaFbD = '';
    public $metaFbImg = '';
   public $metaIndex = 1;
   public $metaFollow = 1;
   public $canonical = '';
   public $site;
   public $countTour = 0;
   public $countExcl = 0;
   public $envies = [];
   public $destination = false;
   public $exclusives = false;
   public $programes = false;
   public $aboutUs = false;
   public $id_inquiry = NULL;
   // breadcrumb
   public $root;
   public $entry;
   public $pagination = 0;
   public $iconBanner = [];
   public $notBannerRight = false;
    public $totalCount = 0;
    public $arr_option_filter_mobile = [];
    public $priceSeo;

    public function __construct($id, $module, $config = []) {
        parent::__construct($id, $module, $config);

     
        
      //  if (!$this->site) throw new HttpException(404, 'Web site could not be found.');
        if(URI)
            $this->redirectUrl(URI);

        // Original http referrer
        if (!Yii::$app->session->get('ref')) {
            Yii::$app->session->set('ref', Yii::$app->request->getReferrer());
        }
        
        $this->destiMenu = $this->getDestimenu();
        $this->ideesMenu = $this->getIdeesmenu();
        $this->excluMenu = $this->getExclumenu();
        $this->aproMenu = $this->getApromenu();
        
        // Detect mobile
        
       
        
        if (!defined('IS_MOBILE')) {
            $detect = new Mobile_Detect;
            
            //$detect = Yii::$app->mobileDetect;
            if ($detect->isMobile() && !$detect->isTablet()) {
                define('IS_MOBILE', true);
            } else {
                define('IS_MOBILE', false);
            }
        }

        // Mobile layout
        if (IS_MOBILE) {
            $this->layout = 'mobile';
        }
        //hotel menu
//        if (!IS_MOBILE) {
//            $this->hotelMenu = $this->getHotelmenu();
//        }
        
     
    }
	
	// xu ly devis
	 public static function allowedDomains()
    {
        return [
            // '*',                        // star allows all domains
            'https://my.amicatravel.com',
        ];
    }

    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors'  => [
                    // restrict access to domains:
                    'Origin'                           => static::allowedDomains(),
                    'Access-Control-Request-Method'    => ['POST'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age'           => 3600,                 // Cache (seconds)
                ],
            ],
        ];
    }
	
	// end process devis
	
    public function redirectUrl($uri){
        if(preg_match('/^secrets-ailleurs\/(.*?)\/(.*?)/', URI))
        {
            Yii::$app->response->redirect('https://www.amica-travel.com/'.explode('/', URI)[1].'/'.explode('/', URI)[0].'/'.explode('/', URI)[2], 301);
            Yii::$app->end();
        }
        
        $redirect = \app\modules\redirection\api\Page::get($uri);
        if ($redirect) {
            Yii::$app->response->redirect($redirect->model->target_url, 301);
            Yii::$app->end();
        }
        

        // REDIRECT BY REGEX
        $redirects = \app\modules\redirection\models\Page::find()->select(['source_url', 'target_url'])->where('type = 1')->asArray()->all();
        if (count($redirects) > 0) {
            foreach ($redirects as $key => $value) {
                $pattern = $value['source_url'];
                $targetUrl = $value['target_url'];
                if (preg_match($pattern, $uri)) {
                    Yii::$app->response->redirect($targetUrl, 301);
                    Yii::$app->end();
                }
            }
        }
    }

    public function actionChangeUrlExcl(){
        $theEntries = \app\modules\exclusives\models\Item::find()->all();
        foreach($theEntries as $key => $value){
            $model = \app\modules\exclusives\models\Item::findOne(['item_id' => $value->item_id]);
            $model-> slug = explode('/', $value->slug)[1].'/'.explode('/', $value->slug)[0].'/'.explode('/', $value->slug)[2];
            var_dump($model->save());
        }
        
    }

    public function getDestimenu() {
        $theMenu = \app\modules\destinations\models\Category::find()
                    ->where('depth IN (0,1)')
                    ->andWhere("slug NOT LIKE '%envies%'")
                   ->with(['photos']) 
                  //  ->orderBy('category_id')
                    ->all();
        return $theMenu;
    }

    public function actionMaps(){
        return $this->renderPartial('//page2016/maps/big-maps');
    }
    public function getIdeesmenu() {
        $theIdees = \app\modules\programmes\models\Category::find()
                    ->where(['depth'=>0])
                   ->with(['photos']) 
                    ->orderBy('order_num')
                    ->all();
        return $theIdees;
    }
    public function getExclumenu() {
        $theExclu = \app\modules\exclusives\models\Category::find()
                    ->where(['depth'=>0])
                   ->with(['photos']) 
                    ->orderBy('order_num')
                    ->all();
        return $theExclu;
    }
    public function getApromenu() {
		$arr = ['1', '36', '2','18', '20','11','5','4'];
        $theMenu = NULL;
        foreach ($arr as $v) {
            
        
        $theMenu[] = \app\modules\whoarewe\models\Category::find()
                    ->where(['depth'=>0])
                    ->andWhere(['category_id' => $v])
                   ->with(['photos']) 
                  //  ->orderBy('category_id')
                    ->one();
        }
        return $theMenu;
    }
    public function actions() {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'width' => 100,
                'height' => 34,
                'foreColor' => 0xa8a8a8,
                'minLength' => 4,
                'maxLength' => 4,
                'offset' => 2,
                'transparent' => true,
                
            ],
        ];
    }

    public function getSeo($theEntry = null){
       // $seo = $theEntry->model->seo;
        if($theEntry){
            $this->pageT = isset($theEntry->h1) ? $theEntry->h1 : '';
            $this->metaT = isset($theEntry->title) ? $theEntry->title : '';
            $this->metaD = isset($theEntry->description) ? $theEntry->description : '';
            $this->seoContent = isset($theEntry->seo_content) ? $theEntry->seo_content : '';
            $entry_socials = isset($theEntry->social) ? $entry_socials = $theEntry->social : $entry_socials = array();
            if(!empty($entry_socials)) {
                $socials = json_decode($theEntry->social, true);
                if(!empty($socials['title'])) {
                    $this->metaFbT = $socials['title'];
                } else {
                    $this->metaFbT = $this->metaT;
                }
                if(!empty($socials['description'])) {
                    $this->metaFbD = $socials['description'];
                }else {
                    $this->metaFbD = $this->metaD;
                }
                if(!empty($socials['fb_img'])) {
                    $this->metaFbImg = $socials['fb_img'];
                }
            } else {
                $this->metaFbT = $this->metaT;
                $this->metaFbD = $this->metaD;
            }
        }
    }

    protected function getDataFromMetaRobots() {
        $key= 'data-cache-meta-robots';
        $cache = Yii::$app->cache;

        // try retrieving $data from cache
        $data = $cache->get($key);

        if ($data === false) {
            // $data is not found in cache, calculate it from scratch
            $data = \app\modules\metarobot\models\Page::find()->where(['status' => '1'])->orderBy(['sort_order' => SORT_ASC])->asArray()->all();

            // store $data in cache so that it can be retrieved next time
            $cache->set($key, $data);

        }
        // $data is available here
        return $data;
    }

    public function getRootAboutUs(){
        $root = \yii\easyii\modules\page\api\Page::get(15);
        return $root;
    }

    public function beforeAction($action) {
        // if(!Yii::$app->session->get('login')  && Yii::$app->controller->action->id !='login'){
        //     $url = Yii::$app->request->url;
        //     return $this->redirect('/login?url='.$url);
        // }
        $this->enableCsrfValidation = false;

        $rootId = 0;
        if(strpos($this->action->id, 'destination') !== false){ 
            $this->destination = true;
            $rootId = 12;
        }
        if(strpos($this->action->id, 'exclusivites') !== false){
            $this->exclusives = true;
            $rootId = 14;
        } 
        if(strpos($this->action->id, 'voyage') !== false){
            $this->programes = true;
            $rootId = 13;
        }
        if(strpos($this->action->id, 'faq') !== false){
            //$this->faq = true;
            $rootId = 10;
        }
        if(strpos($this->action->id, 'about-us') !== false){
            $this->aboutUs = true;
            $rootId = 15;
        }
        if($rootId)
            $this->root = \yii\easyii\modules\page\api\Page::get($rootId);
        if(Yii::$app->session->get('projet'))
            $projet = Yii::$app->session->get('projet');
          else $projet = [
            'programes'=> ['select'=>[], 'view'=>[]],
            'exclusives' => ['select'=>[], 'view'=> []]
            ];
          Yii::$app->session->set('projet',$projet);

        // Run metarobots
        $regexMetaRobots = $this->getDataFromMetaRobots();
        if (!empty($regexMetaRobots)) {
            $uri = Yii::$app->request->getAbsoluteUrl();
            foreach ($regexMetaRobots as $key => $value) {
                $tmp = $value['url'];
                $index = $value['index'];
                $follow = $value['follow'];
                $pattern = '/' . $tmp . '/';
                if (preg_match($pattern, $uri)) {
                    $this->metaIndex = ($index);
                    $this->metaFollow = ($follow);
                    break;
                }
            }
        }

        return parent::beforeAction($action);
    }

    public function actionLogin(){

        if(Yii::$app->request->post()){
            if(Yii::$app->request->post('password')  == 'Amica27ntT'){
                Yii::$app->session->set('login', true);
                $url = isset(Yii::$app->request->getQueryParams()['url']) ? Yii::$app->request->getQueryParams()['url'] : '/';
                return $this->redirect($url);
            }
        }
        return $this->renderPartial('//page2016/login');
    }
    // TODO page cache

    public function actionError() {
        $this->metaT = 'Page non trouvée | Amica Travel';
        return $this->render('//page2016/error');
    }

    public function actionIndex()
    {
        Yii::$app->cache->flush();
        $theEntry = \yii\easyii\modules\page\api\Page::get(31);
        // var_dump($theEntry->photos[0]);exit;
        if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
        $iconBanner = [];
        foreach ($theEntry->photos as $key => $value) {
            if($value->type == 'icon-banner')
                $iconBanner[] = $value;
        }
        $this->iconBanner = $iconBanner;
        $this->getSeo($theEntry->model->seo);
        $this->countTour = count(\app\modules\programmes\api\Catalog::items(['pagination' => ['pageSize' => 0]]));
        
        if(!Yii::$app->cache->get('cache-home-all')){
            $desRoot = \app\modules\destinations\models\Category::find()->roots()->all();
        $exclusives = \app\modules\exclusives\models\Category::find()->roots()->all();
        
        // Temoignages
        $dataTemoignageItems = [];
        // Yii::$app->cache->delete('cache-testi-home');
        if(!Yii::$app->cache->get('cache-testi-home')) {
            $dataTemoignageSelected = \app\modules\modulepage\api\Catalog::get(11);
            if(isset($dataTemoignageSelected->data->temoignages)) {
                $dataTemoignageSelected = $dataTemoignageSelected->data->temoignages;
                $dataTemoignageItems = \app\modules\whoarewe\models\Item::find()->where(['in', 'item_id', $dataTemoignageSelected])->with(['photos'=> function (\yii\db\ActiveQuery $query) {
            $query->andWhere(['type' => 'summary']);
        }])->asArray()->all();
                $dataTemoignageItems = ArrayHelper::map($dataTemoignageItems, 'item_id', function($element){
                    $element['data'] = json_decode($element['data']);
                    return $element;
                });
                uksort($dataTemoignageItems, function($key1, $key2) use ($dataTemoignageSelected) {
                    return (array_search($key1, $dataTemoignageSelected) > array_search($key2, $dataTemoignageSelected));
                });
                Yii::$app->cache->set('cache-testi-home', $dataTemoignageItems);
            }
        } else
            $dataTemoignageItems = Yii::$app->cache->get('cache-testi-home');
        // var_dump($dataTemoignageItems[1384]);exit;
        // BLOGS
        $arrBlog = [];
        if(!Yii::$app->cache->get('cache-blog-home')) {
            $data = \app\modules\modulepage\api\Catalog::get(24);
            if(isset($data->data->blogs)) {
                $dataBlogSelected = $data->data->blogs;
                foreach ($dataBlogSelected as $keyBlog => $valueBlog) {
                    $arrBlog[$keyBlog] = $this->getDataPost($valueBlog);
                    $titleCategory = $this->getCategoryName($arrBlog[$keyBlog]['categories'][0])['name'];
                    $arrBlog[$keyBlog]['cat_name'] = $titleCategory;


                    $featuredMediaData = $this->getFeatureImage($arrBlog[$keyBlog]['featured_media'])['media_details']['sizes']['barouk_list-thumb'];
                    $arrBlog[$keyBlog]['alt_text'] = $this->getFeatureImage($arrBlog[$keyBlog]['featured_media'])['alt_text'];
                    $arrBlog[$keyBlog]['src'] = '/timthumb.php?src=' . $featuredMediaData['source_url'] . '&w=300&h=200&zc=1&q=80';
                }
                Yii::$app->cache->set('cache-blog-home', $arrBlog);
            }    
        } else{
            $arrBlog = Yii::$app->cache->get('cache-blog-home');
        }
        $aboutUs = \yii\easyii\modules\page\api\Page::get(15);
        // var_dump($aboutUs);exit;
        $aboutUsItems = \app\modules\whoarewe\models\Category::find()->where(['in', 'category_id', [36,37]])->with(['photos'=> function (\yii\db\ActiveQuery $query) {
            $query->andWhere(['type' => 'on-home']);
        }])->all();
        $entrySecret = \yii\easyii\modules\page\api\Page::get(14);
        $dataHome = [
            'desRoot' => $desRoot,
            'arrExclusives' => $exclusives,
            'arrTemoignages' => $dataTemoignageItems,
            'arrBlog' => $arrBlog,
            'theEntry' => $theEntry,
            'aboutUs' => $aboutUs,
            'aboutUsItems' => $aboutUsItems,
            'entrySecret' => $entrySecret
        ];
        Yii::$app->cache->set('cache-home-all', $dataHome); 
        } else{
            $dataHome = Yii::$app->cache->get('cache-home-all');
        }


        if(IS_MOBILE){
            $this->totalCount = $this->countTour;
            $this->arr_option_filter_mobile = [
                    'title_filter' => 'Filtrer votre recherche' ,
                    'namefilter' => 'filter_voyage',
                    'uri' =>$theEntry->slug,
                    'country' => 'all',
                    'type' => 'all',
                    'length' => 'all',
                ];
        }

        return $this->render(IS_MOBILE ? '//page2016/mobile/home' : '//page2016/home', $dataHome);
    }

    protected function getDataPost($id)
    {
        return $this->sendRequestToBlogAmica('https://blog.amica-travel.com/wp-json/wp/v2/posts/' . $id);
    }
    protected function getFeatureImage($id)
    {
        return $this->sendRequestToBlogAmica('https://blog.amica-travel.com/wp-json/wp/v2/media/' . $id);
    }


    protected function getCategoryName($id)
    {
        return $this->sendRequestToBlogAmica('https://blog.amica-travel.com/wp-json/wp/v2/categories/' . $id);
    }

    protected function sendRequestToBlogAmica($url)
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('get')
            ->setUrl($url)
            ->send();
        return $response->getData();
    }

  public function actionGetNumberExcl(){
        if(Yii::$app->request->isAjax){
        $type = Yii::$app->request->get('type');
        if(!$type || $type == 'all'){
            $filterType = [];
        } else $filterType = ['category_id' => explode('-',$type)];
        
        $country = Yii::$app->request->get('country');
        if(!$country || $country == 'all'){
            $filters = [];
        } else {
            if(strpos($country, '-') === false){
                $filters = ['countries' => $country];
            }
            else{
                $filters = ['countries' => ['IN',explode('-',$country)]];
            }
        }
        $exclusives = \app\modules\exclusives\api\Catalog::items([
            'where' => ['and',
                $filterType
                ],
            'filters' => $filters,
            'pagination' => ['pageSize' => 0]
            ]);
        return count($exclusives);
      }
    }

    public function actionGetNumberProg($countryP = ''){
          $type = Yii::$app->request->get('type');
        $typeNoChild = $typeChild = [];
        if(!$type || $type == 'all'){
            $filterType = [];
        } else {
            foreach (explode('-',$type) as $key => $value) {
                    if( $childrenType = \app\modules\programmes\models\Category::findOne($value)->children(1)->all())
                    $typeChild = ArrayHelper::getColumn($childrenType, 'category_id');
                    else {
                         $typeNoChild[] = intval($value);
                    }
            }
            $filterType = array_merge($typeChild, $typeNoChild);
        
        }
        $filterType = ['category_id' => $filterType];
        $country = Yii::$app->request->get('country');
        if($countryP) $country = $countryP;
        if(!$country || $country == 'all'){
            $filters = [];
        } else {
            if(strpos($country, '-') === false){
                $filters = ['countries' => $country];
            }
            else{
                $filters = ['countries' => ['IN',explode('-',$country)]];
            }
        }
        $length = Yii::$app->request->get('length');
        if($length == 'all'){
            $length = '';
        }
        if(strpos($length, '-') !== false){
              $arrLen = explode('-',$length);
              asort($arrLen);
              if($arrLen[0]==1) $arrLen[0] = 0;
              if(count($arrLen) == 4){
                $filterLen = ['between', 'days', $arrLen[0], end($arrLen)];
              }
              if(count($arrLen) == 3){
                $filterLen = ['or',
                ['between', 'days', $arrLen[0], $arrLen[1]],
                ['>=','days', end($arrLen)]  
                ];
              }
              if(count($arrLen) == 2){
                $filterLen = ['between', 'days', $arrLen[0], $arrLen[1]];
              }
        } else $filterLen = ['>=','days', $length];

        $voyage = \app\modules\programmes\api\Catalog::items([
            'where' => ['and',
                $filterType,
                $length ? $filterLen : []
                ],
            'filters' => $filters,
            'pagination' => ['pageSize' => 0]
            ]);
        return count($voyage);
    }

    public function actionGetNumberTesti(){
         //data search for testi
        //for tour Type
        $type = Yii::$app->request->get('type');
        $filterType = [];
        if($type && $type != 'all')
            $filterType = ['tTourTypes' => ['IN',explode(',',$type)]];
        //for tour themes
        $theme = Yii::$app->request->get('theme');
        $filterTheme = [];
        if($theme && $theme != 'all')
            $filterTheme = ['tTourThemes' => ['IN',explode(',',$theme)]];
        //for country
        $country = Yii::$app->request->get('country');
        $filterCountry = [];
        if($country && $country != 'all'){
            if(strpos($country, '-') === false){
                $filterCountry = ['countries' => ['IN', [$country]]];
            }
            else{
                foreach (explode('-',$country) as $key => $value) {
                    $filterCountry[] = $value;
                }
                $filterCountry = ['countries' => ['IN', $filterCountry]];
            }
        }
        $filter = [];
        $filter = $filter + $filterCountry + $filterTheme + $filterType;
        
        //get data category & items testi
        //process data testi
        $theTesti = \app\modules\whoarewe\api\Catalog::cat(13);
       
        $totalCountTesti = count($theTesti->items(['pagination' => ['pageSize'=>0],
            'filters' => $filter]));
        return $totalCountTesti;
    }
      
	public function actionMentionsLegalesAboutUs() {
          $theEntry = \app\modules\whoarewe\api\Catalog::cat(URI);
          $this->entry = $theEntry;
        
         if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvÃ©!');
         $this->getSeo($theEntry->model->seo);
        return $this->render(IS_MOBILE ? '//page2016/mobile/view-only-text' : '//page2016/mentions-legales', [
            'theEntry' => $theEntry,
            'root' => $this->getRootAboutUs()
        ]);
    }
    public function actionConditionsAboutUs() {
       //  $theEntry = Catalog::cat(22);
          $theEntry = \app\modules\whoarewe\models\Category::find()
                ->where(['category_id'=>35])
                ->with(['photos'])
                ->one();
        $this->entry =\app\modules\whoarewe\api\Catalog::cat(35);
         if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvÃ©!');
        $this->getSeo($theEntry->seo);
        if(IS_MOBILE) $theEntry = \app\modules\whoarewe\api\Catalog::cat(URI);
        return $this->render(IS_MOBILE ? '//page2016/mobile/view-only-text' : '//page2016/conditions', [
            'theEntry' => $theEntry,
            'root' => $this->getRootAboutUs()
        ]);
    }
    public function actionProposDeNousAboutUs() {

        $theEntry = Page::get(15);
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        $this->getSeo($theEntry->model->seo);
		$arr = ['1', '36', '37', '2','18', '20','11','5','4'];
        $theMenu = NULL;
        foreach ($arr as $v) {
            
        
        $theMenu[] = \app\modules\whoarewe\models\Category::find()
                    ->where(['depth'=>0])
                    ->andWhere(['category_id' => $v])
                    ->with(['photos']) 
                    ->one();
        }
		
        $theRaisons = \app\modules\whoarewe\api\Catalog::cat(2);
        
		$theRaisons_list = $theRaisons->items(['where'=>['category_id' => $theRaisons->model->category_id],'orderBy'=>'item_id']);
        return $this->render(IS_MOBILE ? '//page2016/mobile/a-propos-de-nous' : '//page2016/a-propos-de-nous', [
            'theEntry' => $theEntry,
            'theMenu' => $theMenu,
            'theRaisons' => $theRaisons,
            'theRaisons_list' => $theRaisons_list,
        ]);
    }
    public function actionRecrutementAboutUs() {
          $theEntry = \app\modules\whoarewe\models\Category::find()
                ->where(['category_id'=>22])
                ->with(['photos'])
                ->one();
        $this->entry =\app\modules\whoarewe\api\Catalog::cat(22);
         if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        $this->getSeo($theEntry->seo);
        if(IS_MOBILE) $theEntry = \app\modules\whoarewe\api\Catalog::cat(22);
        return $this->render(IS_MOBILE ? '//page2016/mobile/view-banner-text' : '//page2016/recrutement',[
            'theEntry' => $theEntry,
            'root' => $this->getRootAboutUs()
        ]);
    }
     public function actionRecrutementSingleAboutUs() {
        $theEntry = \app\modules\whoarewe\api\Catalog::get(URI);
        $this->entry = $theEntry;
         if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        $this->getSeo($theEntry->model->seo);
        $theEntries = \app\modules\whoarewe\api\Catalog::cat(SEG1)->items(['where' => ['status' => 1]]);
        
        if(IS_MOBILE)  $theEntries = \app\modules\whoarewe\api\Catalog::cat(SEG1)->items(['where' => ['!=', 'slug', URI]]);  
        else   $theEntries = \app\modules\whoarewe\api\Catalog::cat(SEG1)->items(['where' => ['status' => 1]]);
       return $this->render(IS_MOBILE ? '//page2016/mobile/recrutement-single' : '//page2016/recrutement-single',[
            'theEntry' => $theEntry,
            'theEntries' => $theEntries,
            'root' => $this->getRootAboutUs()
        ]);
    }
     public function actionFaq() {
         $theEntry = Page::get(10);
         
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        
        $this->getSeo($theEntry->model->seo);
        $theEntries = \app\modules\faq\models\Category::find()
            ->with(['photos'])
            ->orderBy('category_id')
            ->all();
        //var_dump($theEntries);exit;
        $list_item = \app\modules\faq\api\Catalog::items([
            'orderBy' => ['on_top_flag' => SORT_ASC, 'on_top' => SORT_ASC],
            'where' => ['>','on_top',0],
            //'filters' => $fil_countries,    
            'pagination' => ['pageSize' => 0]

        ]);
         //var_dump($list_item);exit;
        
         return $this->render(IS_MOBILE ? '//page2016/mobile/faq' : '//page2016/faq', [
            'theEntry' => $theEntry,
            'theEntries' => $theEntries,
            'list_item' => $list_item,
            
        ]);
    }
     public function actionFaqSingle() {
         
        $theRoot = ''; 
        $theParent = Page::get(10);
        
        $theEntry = \app\modules\faq\api\Catalog::cat(URI);
        $thechildren = $theEntry;
        //var_dump($thechildren);exit;
        if(!$theEntry){
           $theRoot = Page::get(10);  
           
            $thechildren = \app\modules\faq\api\Catalog::get(URI);
            $theEntry = \app\modules\faq\api\Catalog::cat($thechildren->category_id);
            //var_dump($thechildren->parents());exit;
        }
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        $this->entry = $thechildren;
        $this->getSeo($thechildren->model->seo);
        
        if(Yii::$app->request->post('slug') != NULL){
            $slug = Yii::$app->request->post('slug');
        }else{
            $slug = NULL;
        }
        
        $theItems = $theEntry->items([
            'where' => ['slug'=>$slug],
            'orderBy' => 'item_id'
            ]);
        //$theRaisons_list = $theRaisons->items(['where' => ['category_id' => $theRaisons->model->category_id], 'orderBy' => 'item_id']);
        //var_dump($theItem);exit;
        $theFive = \app\modules\faq\models\Category::find()
            ->where('category_id != "'.$theEntry->model->category_id.'"')    
            ->with(['photos'])
            ->orderBy('category_id')
            ->all();
        
    //var_dump($theFive);exit;
        
        return $this->render(IS_MOBILE ? '//page2016/mobile/faq-single' : '//page2016/faq-single',[
            'theRoot' => $theRoot,
            'theParent' => $theParent,
            'theEntry' => $theEntry,
            'thechildren' => $thechildren,
            'theFive' => $theFive,
            'theItems' => $theItems,
            
        ]);
    }
    public function actionFaqSearch(){
        
       
            
            $search = Yii::$app->request->post('search');
            //var_dump($search);exit;
            if($search != NULL){
//            $data = \app\modules\faq\models\Item::find()
//           // ->where(['type'=>'journey'])        
//            ->andFilterWhere(['like', 'question', '%'.$search.'%', false])
//            ->orderBy('on_top ASC')        
//            //->limit(10)
//            //->asArray()
//            ->all();
             $data = \app\modules\faq\api\Catalog::items([
                'orderBy' => ['on_top_flag' => SORT_ASC, 'on_top' => SORT_ASC],
                'where' => ['like','question','%'.$search.'%', false],
                //'filters' => $fil_countries,    
                'pagination' => ['pageSize' => 0]

            ]);
            }else{
                $data = NULL;
            }
    //var_dump($data);exit;
            $html = '';
            if(!$data) {$html = "<li>Pas de résultat. Tapez un autre mot-clé ou une autre question</li>";}
            else {
                foreach ($data as $key => $dt) {
                $html .= '<li><a href="'.DIR.$dt->slug.'">'.$dt->title.'</a></li>';
                }
            }
            echo $html;
        }
    
     public function actionRaisonsAboutUs() {
         $theEntry = \app\modules\whoarewe\api\Catalog::cat(URI);
        $this->entry = $theEntry;
         if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        $this->getSeo($theEntry->model->seo);
         $theEntries = Catalog::items(['where'=>['category_id'=>$theEntry->model->category_id],'orderBy'=>'item_id']);
          $theItem = Catalog::get(614);
         //var_dump($theItem);exit;
        $listModules = NULL;
		 
         $listModules_Exclu = NULL;
		if(isset($theItem->data->moduleexcl)){ 
        foreach ($theItem->data->moduleexcl as $v) {
		
            //foreach ($v as $value) {
				if($v != ''){
                $listModules[] = \app\modules\modulepage\models\Item::find()
                        ->where(['slug'=>$v])
                        ->with(['photos'])
                        ->one();
				}
           // }
        }
		}
        
       
        
      
       if($listModules != NULL){
        foreach ($listModules as $v) {
            
            foreach ($v->data->exclusives as $value) {
                $listModules_Exclu[] = \app\modules\exclusives\models\Item::find()
                        ->where(['slug'=>$value])
                        ->with(['photos'])
                        ->one();
            }
        }
		}
        
         
         
         return $this->render(IS_MOBILE ? '//page2016/mobile/10-raisons' : '//page2016/10-raisons', [
            'theEntry' => $theEntry,
            'theEntries' => $theEntries,
            'theItem' => $theItem,
            'listModules' => $listModules,
            'listModules_Exclu' => $listModules_Exclu,
            'root' => $this->getRootAboutUs()
        ]);
    }
    public function actionClubAmiAboutUs() {
         $theEntry = Catalog::cat(4);
         $this->entry = $theEntry;
         if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        $this->getSeo($theEntry->model->seo);
        return $this->render(IS_MOBILE ? '//page2016/mobile/club-ami' : '//page2016/club-ami', [
            'theEntry' => $theEntry,
            'root' => $this->getRootAboutUs()
        ]);
    }
    public function actionActualitesAboutUs() {
       // $theEntry = Catalog::cat(17);
        $theEntry = \app\modules\whoarewe\api\Catalog::cat(URI);
        if(Yii::$app->request->isAjax){
            $theEntries = $theEntry->items(['pagination' => ['pageSize' =>  8]]);
            return $this->renderPartial('//page2016/mobile/actualites', ['theEntries' => $theEntries
            ]);
        }
        $this->entry = $theEntry;
         if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
		 $this->getSeo($theEntry->model->seo);
        $countQuery = clone $theEntry;
        $pages = new Pagination([
            'totalCount' => count($countQuery->items(['pagination' => ['pageSize' => 0]])),
            'defaultPageSize' => 12,
            'forcePageParam' => false
        ]);
        $this->pagination = $pages->pageCount;
        $theEntries = $theEntry->items(['pagination' => ['pageSize' => Yii::$app->request->get('view') == 'all' ? 0 : 12]]);
        if(IS_MOBILE) $theEntries = $theEntry->items(['pagination' => ['pageSize' =>  8]]);
        return $this->render(IS_MOBILE ? '//page2016/mobile/actualites' : '//page2016/actualites', [
            'theEntry' => $theEntry,
            'theEntries' => $theEntries,
            'pages' => $pages,
            'root' => $this->getRootAboutUs()
        ]);
    }
    public function actionActualitesSingleAboutUs() {
         $theEntry = Catalog::get(SEG2);
         $this->entry = $theEntry;
         if (!$theEntry || $theEntry->cat->category_id !=17) throw new HttpException(404, 'Page ne pas trouvé!');

		$this->getSeo($theEntry->model->seo);
        
        $articles = ChItems::find()
                 ->where(['status' => 1,'category_id'=>$theEntry->cat->category_id])
                 ->orderBy('rand()')
                 ->limit(3)
                 ->asArray()
                 ->all();
         

        $this->getSeo($theEntry->model->seo);
        return $this->render(IS_MOBILE ? '//page2016/mobile/actualites-single' : '//page2016/actualites-single', [
            'theEntry' => $theEntry,
            'root' => $this->getRootAboutUs(),
            'articles' => $articles
        ]);
    }
   public function actionExclusivites()
    {
        $theEntry = Page::get(14);


        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        $this->getSeo($theEntry->model->seo);

        $this->countExcl = count(\app\modules\exclusives\api\Catalog::items([
            //'filters' => ['countries' => SEG1],
            'pagination' => ['pageSize' => 0]
        ]));
        $theSix = \app\modules\exclusives\models\Category::find()
            ->with(['photos'])
            ->orderBy('category_id')
            ->all();
          if(IS_MOBILE){
                $this->totalCount = $this->countExcl;
                $this->arr_option_filter_mobile = [
                        'title_filter' => $theEntry->model->seo->h1 ,
                        'namefilter' => 'filter_exclusivites',
                        'uri' =>$theEntry->slug,
                        'country' => 'all',
                        'type' => 'all',
                        'length' => 'all',
                    ];
            }    


        return $this->render(IS_MOBILE ? '//page2016/mobile/exclusivites' : '//page2016/exclusivites', [
            'theEntry' => $theEntry,
            'theSix' => $theSix,
        ]);
    }
	public function actionExclusivitesTypeCountry() {
       
        $theEntry = Page::get(14);
        
         if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
		 $theParent = Page::get(SEG1); 
		 $this->getSeo($theEntry->model->seo);
      
         $exclusives_country = \app\modules\exclusives\api\Catalog::items([
            'where' => ['status'=>1],
            'filters' => [SEG2],
           'pagination' => ['pageSize' => Yii::$app->request->get('view') == 'all' ? 0 : 12]
            ]);
          
         if (Yii::$app->request->isAjax) {
             if(Yii::$app->request->post()['type'] == 'excl'){
                return $this->renderPartial('//page2016/ajax/country-excl', ['theEntries' => $exclusives_country]);

            }
        } else{
             Yii::$app->session->set('countExcl', count(\app\modules\exclusives\api\Catalog::items([
             'where' => ['status'=>1],
            'filters' => [SEG2],
            'pagination' => ['pageSize' => 0 ]
            ])));
        }
        $pagi = new \yii\data\Pagination(['totalCount' => Yii::$app->session->get('countExcl'), 'pageSize'=>12]);
        $this->pagination = $pagi->pageCount;
       
         $theRaisons = \app\modules\whoarewe\api\Catalog::cat(2);
         $theRaisons_list = $theRaisons->items(['where'=>['category_id' => $theRaisons->model->category_id],'orderBy'=>'item_id']);
        
        return $this->render('//page2016/exclusivites-type-country',[
            'theEntry' => $theEntry,
            'theParent' => $theParent,
            'theEntries' => $exclusives_country,
            'theRaisons' => $theRaisons,
            'theRaisons_list' => $theRaisons_list,
            
        ]);
    }
     public function actionExclusivitesType()
    {
        $theEntry = \app\modules\exclusives\api\Catalog::cat(URI);
        $this->entry = $theEntry;
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');

        $this->countExcl = count(\app\modules\exclusives\api\Catalog::items([
            //'filters' => ['category_id' => $theEntry->model->category_id],
            'where' => ['category_id' => $theEntry->model->category_id],
            'pagination' => ['pageSize' => 0]
        ]));


        $theParent = Page::get(SEG1);
        $this->getSeo($theEntry->model->seo);
        
        // xy ly Filter Ajax

            $theEntries = $this->getAjaxFilterExclusive(['country'=>'','type'=> $theEntry->model->category_id])['voyage'];
            $totalCount = $this->getAjaxFilterExclusive(['country'=>'','type'=> $theEntry->model->category_id])['totalCount'];
            //$numberFilter = $this->getAjaxFilterExclusive();
            $numberFilter = $this->getAjaxFilterExclusive(['country'=>'','type'=> $theEntry->model->category_id]);



        $theRaisons = \app\modules\whoarewe\api\Catalog::cat(2);
        $theRaisons_list = $theRaisons->items(['where' => ['category_id' => $theRaisons->model->category_id], 'orderBy' => 'item_id']);
        if(IS_MOBILE){
                $this->totalCount = $totalCount;
                $this->arr_option_filter_mobile = [
                        'title_filter' => $theEntry->model->seo->h1 ,
                        'namefilter' => 'filter_exclusivites',
                        'uri' =>$theEntry->slug,
                        'country' => 'all',
                        'type' => $theEntry->model->category_id,
                        'length' => 'all',
                        'switch_link' => 'filter_type_active',
                    ];
            }    

        return $this->render(IS_MOBILE ? '//page2016/mobile/exclusivites-type' : '//page2016/exclusivites-type', [
            'theEntry' => $theEntry,
            'theParent' => $theParent,
            'theEntries' => $theEntries,
            'theRaisons' => $theRaisons,
            'theRaisons_list' => $theRaisons_list,
            'totalCount' => $totalCount,
            'numberFilter' => $numberFilter,
        ]);
    }
     public function actionExclusivitesSingle() {
         $theRoot = Page::get(SEG1); 
        
         
         //$theEntry = \app\modules\exclusives\api\Catalog::get(SEG3);
          $theEntry = \app\modules\exclusives\models\Item::find()
                ->where(['slug'=>URI,'status'=>1])
                ->with(['photos'])
                ->one();
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');        
         $this->entry =\app\modules\exclusives\api\Catalog::get(URI);
         $theParent = \app\modules\exclusives\api\Catalog::cat($theEntry->category_id);
         
         $this->getSeo($theEntry->seo);
          // add view to projet      
          if(Yii::$app->session->get('projet'))
            $projet = Yii::$app->session->get('projet');
          else $projet = [
            'programes'=> ['select'=>[], 'view'=>[]],
            'exclusives' => ['select'=>[], 'view'=> []]
            ];
          if(!in_array($theEntry->item_id, $projet['exclusives']['view']) && !in_array($theEntry->item_id, $projet['exclusives']['select']))
              $projet['exclusives']['view'][] = $theEntry->item_id;
          Yii::$app->session->set('projet',$projet);
         $theProgram = [];
         
         $pro = explode(',', $theEntry->programes);
         
         foreach ($pro as $p) {
             $item = \app\modules\programmes\api\Catalog::get($p);
             if($item)
                $theProgram[] = \app\modules\programmes\api\Catalog::get($p);
         }
        
        
         $allCountries = Country::find()->select(['code', 'name_fr'])->orderBy('name_fr')->asArray()->all();

        $model = new ContactForm;
        if (IS_MOBILE) {
            $model = new ContactFormMobile;
            $model->scenario = 'contactce_mobile';
        } else {
            $model->scenario = 'contactce';
        }
        

        $model->country = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';
        $model->countryCallingCode = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';

        if ($model->load($_POST) && $model->validate()) {
            
            $model->tourName = $theEntry->title;
            $model->tourUrl = Yii::$app->request->getAbsoluteUrl();
            
            if (IS_MOBILE) {
            $model->fname = preg_split("/\s+(?=\S*+$)/", $model->fullName)[0];
                $model->lname = '';
                if (isset(preg_split("/\s+(?=\S*+$)/", $model->fullName)[1])) {
                    $model->lname = preg_split("/\s+(?=\S*+$)/", $model->fullName)[1];
                }
            $data2 = <<<'TXT'
Tour name: {{ tourUrl : $tourUrl }}   
Votre nom: {{ prefix : $prefix }} {{ fullName : $fullName }}
Votre mail: {{ email : $email }}
Votre pays: {{ country : $country }}
Département, Votre ville: {{ region : $region }} {{ ville : $ville }}
Votre message: {{ message : $message }}
Souhaitez-vous recevoir une proposition de programme avec devis personnalisé sur d'autres régions du pays ?: {{ question : $question }}
Si vous êtes recommandé(e) par un ancien client d'Amica, merci de préciser son nom et prénom: {{ reference : $reference }}
TXT;
                 $data2 = str_replace([
                   '$tourUrl', '$prefix' ,'$fullName', '$email', '$message', '$question','$country', '$region', '$ville', '$reference'
                    ], [
                       '<a href="'.$model->tourUrl.'">'.$model->tourName.'</a>', 
                    $model->prefix, $model->fullName, $model->email, $model->message, $model->question, $model->country, $model->region, $model->ville, $model->reference
                    ], $data2);
                
                    $this->saveInquiry($model, 'Form-contactsa-mobile', $data2);
                } else {      
                $data2 = <<<'TXT'
Tour name: {{ tourUrl : $tourUrl }}   
Votre nom et prénom: {{ prefix : $prefix }} {{ fname : $fname }} { lname : $lname }}
Votre adresse mail: {{ email : $email }}
Votre pays: {{ country : $country }}
Département, Votre ville: {{ region : $region }} {{ ville : $ville }}
Votre message: {{ message : $message }}
Souhaitez-vous recevoir une proposition de programme avec devis personnalisé sur d'autres régions du pays ?: {{ question : $question }}
Si vous êtes recommandé(e) par un ancien client d'Amica, merci de préciser son nom et prénom: {{ reference : $reference }}
TXT;
                $data2 = str_replace([
                    '$tourUrl', '$prefix', '$fname', '$lname', '$email', '$country', '$region', '$ville', '$message', '$question', '$reference'
                    ], [
                    '<a href="'.$model->tourUrl.'">'.$model->tourName.'</a>', $model->prefix, $model->fname, $model->lname, $model->email, $model->country, $model->region, $model->ville, $model->message, $model->question, $model->reference
                    ], $data2);
                // Save db
                $this->saveInquiry($model, 'Form-contactsa', $data2);
                if($model->newsletter){
                    $data = [
                    'firstname' => $model->fname,
                    'lastname' => $model->lname,
                    'codecontry' => $model->country,
                    'source' => 'newsletter',
                    'sex' => $model->prefix,
                    'newsletter_insc' => date('d/m/Y'),
                    'statut' => 'prospect',
                    'birmanie' => false,
                    'vietnam' => false,
                    'cambodia' => false,
                    'laos' => false
                    ];
                    $this->addContactToMailjet($model->email, '1688900', $data);
                }
            }
             
            // Email me
            $this->notifyAmica('Contact from ' . $model->email, '//page2016/email_template', ['data2' => $data2]);
            $this->confirmAmica($model->email);
            // Redir
            return Yii::$app->response->redirect(DIR.'merci?from=contact_sa&id='.$this->id_inquiry);
        }

        return $this->render(IS_MOBILE ? '//page2016/mobile/exclusivites-single' : '//page2016/exclusivites-single',[
                    'model' => $model,
                    'allCountries' => $allCountries,
                    'theRoot' => $theRoot,
                    'theParent' => $theParent,
                    'theEntry' => $theEntry,
                    'theProgram' => $theProgram,
        ]);
    }
     public function actionEnvieDuMomentAboutUs() {
       // $theEntry = Catalog::cat(14);
        
        $theEntry = \app\modules\whoarewe\models\Category::find()
                ->where(['category_id'=>14])
                ->with(['photos'])
                ->one();
         $this->entry =\app\modules\whoarewe\api\Catalog::cat(14);       
         if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
		 $this->getSeo($theEntry->seo);
		 
         $theParent_Exclu = Page::get(14);
         $theSix_Exclu = \app\modules\exclusives\models\Category::find()
               // ->where(['category_id'=>14])
                ->with(['photos'])
                
                ->all();
         
        return $this->render('//page2016/envie-du-moment',[
            'theEntry'=> $theEntry,
            //'item' => $item,
            'theParent_Exclu' => $theParent_Exclu,
            'theSix_Exclu' => $theSix_Exclu,
            'root' => $this->getRootAboutUs()   
            
        ]);
    }
      public function actionEspacePresseAboutUs() {
       // $theEntry = Catalog::cat(5);
         $theEntry = \app\modules\whoarewe\models\Category::find()
                ->where(['category_id'=>5])
                ->with(['photos'])
                ->one();
         $this->entry =\app\modules\whoarewe\api\Catalog::cat(5);       

        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        $this->getSeo($theEntry->seo);
         $theEntries = Catalog::items(['where'=>['category_id'=>$theEntry->category_id],'orderBy'=>'item_id']);
        
         return $this->render(IS_MOBILE ? '//page2016/mobile/espace-presse' : '//page2016/espace-presse', [
            'theEntry' => $theEntry,
            'theEntries' => $theEntries,
            'root' => $this->getRootAboutUs()
        ]);
    }   
     public function actionDecouvrezLePaysAboutUs() {
        $theEntry = \app\modules\whoarewe\api\Catalog::cat(URI);
        $this->entry = $theEntry;
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        $this->getSeo($theEntry->model->seo);
        $theReport = \app\modules\whoarewe\api\Catalog::cat(23);
		$theEntries_m = Null;
        if(IS_MOBILE){
            $theEntries_m = \app\modules\whoarewe\api\Catalog::cat(23)->items();
        }
        $countQuery = clone $theReport;
        $theEntries = $theReport->items(['pagination' => ['pageSize' => 0]]);
        $pages = new Pagination([
            'totalCount' => count($countQuery->items(['pagination' => ['pageSize'=> 0]])),
            'defaultPageSize' => 3,
            // 'pageParam' => 'reportage',
            'params' => ['page' => Yii::$app->request->get('page')],
            'route' => 'explorateurs'
        ]);
          $this->pagination = $pages->pageCount;
         $theParent_Exclu = Page::get('secrets-ailleurs');
         $theSix_Exclu = \app\modules\exclusives\models\Category::find()
                ->with(['photos'])
                
                ->all();
//        if (Yii::$app->request->isAjax) {
//            if(Yii::$app->request->post()['type'] == 'deco'){
//                $locationAjax = \app\modules\destinations\api\Catalog::cat(SEG1.'/visiter');
//                return $this->renderPartial('//page2016/ajax/deco-ajax', [
//            'theEntries' => $theEntries,
//            'pages' => $pages,
//            ]);
//
//            }
//             
//        }
        return $this->render(IS_MOBILE ? '//page2016/mobile/decouvrez-le-pays' : '//page2016/decouvrez-le-pays',[
            'theEntry' => $theEntry,
            'theReport' => $theReport,
            'theEntries' => $theEntries,
            'pages' => $pages,
            'theParent_Exclu' => $theParent_Exclu,
            'theSix_Exclu' => $theSix_Exclu,
            'root' => $this->getRootAboutUs(),
			'theEntries_m' => $theEntries_m,
        ]);
    }
    
    public function actionReportagesAboutUs(){
        $theEntry = \app\modules\whoarewe\api\Catalog::cat(URI);
         $this->entry = $theEntry;
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        $this->getSeo($theEntry->model->seo);
        
        
        $countQuery = clone $theEntry;
        $totalCount = count($countQuery->items(['pagination' => ['pageSize' => 0]]));
       
        if(Yii::$app->request->get('see-more') != NULL){
            $pagesize = Yii::$app->request->get('see-more');
           
           
        }else{
            $pagesize = 6;
        }
        
        
        
        $theEntries = $theEntry->items(['pagination' => ['pageSize' => $pagesize]]);
        
         
        

        return $this->render(IS_MOBILE ? '//page2016/mobile/reportages' : '//page2016/reportages',[
            'theEntry' => $theEntry,
            'theEntries' => $theEntries,
            'root' => $this->getRootAboutUs(),
            'pagesize' => $pagesize,
            'totalCount' => $totalCount,
        ]);
    }

    public function actionDecouvrezLePaysSingleAboutUs() {
         $theEntry = Catalog::get(URI);
         $this->entry = $theEntry;
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        $this->getSeo($theEntry->model->seo);
        $theRand_thrre = Catalog::items([
            //'where'=>['category_id'=>$theEntry->cat->category_id],
            'where' => ['and', 'category_id = '.$theEntry->cat->category_id.'', ['not', ['item_id' => $theEntry->model->item_id]]],
            'orderBy'=>'rand()',
            'pagination'=>['pageSize'=>3]
            ]);
        
        return $this->render(IS_MOBILE ? '//page2016/mobile/decouvrez-le-pays-single' : '//page2016/decouvrez-le-pays-single',[
            'root' => $this->getRootAboutUs(),    
            'theEntry' => $theEntry,
            'theRand_thrre' => $theRand_thrre,
        ]);
    }
    
     public function actionFondationAboutUs() {
        $theEntry = \app\modules\whoarewe\models\Category::find()
                ->where(['category_id'=>20,'status'=>1])
                ->with(['photos'])
                ->one();
		if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');		
         $this->entry =\app\modules\whoarewe\api\Catalog::cat(20);
        
		$this->getSeo($theEntry->seo);
        $theItem = Catalog::get(635);
        
        $listModules = [];
        
        foreach ($theItem->data->modules as $v) {
                $listModules[] = \app\modules\modulepage\models\Item::find()
                        ->where(['slug'=>$v,'status'=>1])
                        ->with(['photos'])
                        ->one();
        }
       
        $listModules_Exclu = [];
        foreach ($listModules as $v) {
            
            foreach ($v->data->exclusives as $value) {
                $listModules_Exclu[] = \app\modules\exclusives\models\Item::find()
                        ->where(['item_id'=>$value,'status'=>1])
                        ->with(['photos'])
                        ->one();
            }
        }
        $theProjets = Catalog::cat(21);
        
        
        $theProjets_list = \app\modules\whoarewe\models\Category::find()
                 ->where(['status' => 1,'tree'=>$theProjets->tree, 'depth'=>2])  
                //->asArray()
                ->with(['photos'])
                ->orderBy('category_id')
                 ->all(); 
        $theLeft = Catalog::cat(26);
        
        $theLeft_list = \app\modules\whoarewe\models\Item::find()
                 ->where(['status' => 1,'category_id'=>$theLeft->model->category_id])  
                 ->with(['photos'])
                 ->orderBy('time DESC')
                // ->asArray()
                 ->all();  
        $theRight = Catalog::cat(27);
        $theRight_list = \app\modules\whoarewe\models\Item::find()
                 ->where(['status' => 1,'category_id'=>$theRight->model->category_id])  
                 ->with(['photos'])
                 ->orderBy('time DESC')
                // ->asArray()
                 ->all(); 
        
        return $this->render(IS_MOBILE ? '//page2016/mobile/fondation' : '//page2016/fondation',[
            'theEntry' => $theEntry,
            'theProjets' => $theProjets,
            'theProjets_list' => $theProjets_list,
            'theLeft' => $theLeft,
            'theLeft_list' => $theLeft_list,
            'theRight' => $theRight,
            'theRight_list' => $theRight_list,
            'theItem' => $theItem,
            'listModules' => $listModules,
            'listModules_Exclu' => $listModules_Exclu,
            'root' =>  $this->getRootAboutUs()
        ]);
    }


    public function actionProjetsAboutUs(){
        $theEntry = \app\modules\whoarewe\api\Catalog::cat(URI);
        $this->entry = $theEntry;
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        $this->getSeo($theEntry->model->seo);
    //$theEntries = $theEntry->children();
        if(Yii::$app->request->get('see-more') !== NULL){
            $pagesize = Yii::$app->request->get('see-more');
           // $visiter = $locations->items(['pagination' => ['pageSize' => $pagesize]]);
        }else{
            $pagesize = 8;
        }
        $theEntries = \app\modules\whoarewe\models\Category::find()
                 ->where(['status' => 1,'tree'=>$theEntry->tree, 'depth'=>2])  
                //->asArray()
                ->with(['photos'])
                ->orderBy('category_id,order_num')
                ->limit($pagesize)
                 ->all(); 
         $totalCount = count(\app\modules\whoarewe\models\Category::find()
                 ->where(['status' => 1,'tree'=>$theEntry->tree, 'depth'=>2])  
                //->asArray()
                ->with(['photos'])
                ->orderBy('category_id,order_num')
                
                 ->all());
        return $this->render(IS_MOBILE ? '//page2016/mobile/projets' : '//page2016/projets', [
            'theEntry' => $theEntry,
            'theEntries' => $theEntries,
            'pagesize' => $pagesize,
            'totalCount' => $totalCount,
            'root' => $this->getRootAboutUs()
            ]);
    }

    public function actionAssociationsAboutUs(){
        $theEntry = \app\modules\whoarewe\api\Catalog::cat(URI);
        $this->entry = $theEntry;
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        $this->getSeo($theEntry->model->seo);
//        if($children = $theEntry->children()){
//            $theEntries = $children; 
//        } else
        $queryCount = clone $theEntry;
        $totalCount = $queryCount->items(['pagination'=>['pageSize' => 0]]);
        if(Yii::$app->request->get('see-more') !== NULL){
            $pagesize = Yii::$app->request->get('see-more');
           // $visiter = $locations->items(['pagination' => ['pageSize' => $pagesize]]);
        }else{
            $pagesize = 8;
            
        }
        
            $theEntries = $theEntry->items(['pagination'=>['pageSize' => $pagesize]]);
        return $this->render('//page2016/associations', [
            'theEntry' => $theEntry,
            'theEntries' => $theEntries,
            'pagesize' => $pagesize,
            'totalCount' => $totalCount,
            'root' => $this->getRootAboutUs()
            ]);
    }
    
    public function actionFondationSingleAboutUs() {
        $root = Catalog::cat(20); 
        $theParent = Catalog::cat(SEG1);
        $theEntry = Catalog::get(URI);
		if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        $this->entry = $theEntry;
        //echo '<pre>';
       // var_dump($theEntry);exit;
        $theParent_entries = Catalog::cat($theEntry->category_id);
       // echo '<pre>';
       // var_dump($theParent_entries->model);exit;
        $theEntries = \app\modules\whoarewe\models\Item::find()
                 
                 ->where(['status' => 1,'category_id'=>$theEntry->category_id])  
                 ->andWhere('slug != "'.URI.'"')
                 ->with(['photos'])
                 ->orderBy('time DESC')
                 
                 ->all();  
        
       
         
        
		$this->getSeo($theEntry->model->seo);
		
        return $this->render('//page2016/fondation-single',[
            'theParent' => $theParent,
            'theEntry' => $theEntry,
            'theParent_entries' => $theParent_entries,
            'theEntries' => $theEntries,
            'root' => $this->getRootAboutUs(),
        ]);
    }
   public function actionFondationThongnongAboutUs() {
         $theEntry = \app\modules\whoarewe\api\Catalog::cat(URI);
         $this->entry = $theEntry;
       
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        $this->getSeo($theEntry->model->seo);
        $theEntry_info = \app\modules\whoarewe\models\Item::find()
                ->where(['slug'=>$theEntry->slug])
                ->with(['photos'])
                ->one();
        $theEntries = \app\modules\whoarewe\models\Item::find()
                ->where(['category_id' => $theEntry->model->category_id])
                ->andWhere('slug !="'.$theEntry->slug.'"')
                ->orderBy('time ASC')
                ->with(['photos'])
                ->all();

        $theList_tour;
        foreach ($theEntry_info->data->location as $v) {
            //var_dump($v);exit;
            $theList_tour = \app\modules\programmes\models\Item::find()
                    ->where("locations LIKE '%".$v."%'")
                    ->with(['photos'])
                    ->orderBy('rand()')
                    ->limit(3)
                    ->all();
        }
        $location = \app\modules\libraries\api\Catalog::items(['where'=>['in','category_id',[3, 4, 5, 6]],'pagination' => ['pageSize'=>0]]);
           
        $location = \yii\helpers\ArrayHelper::map($location, 'slug','title');
        
       
        
        return $this->render(IS_MOBILE ? '//page2016/mobile/fondation-thongnong' : '//page2016/fondation-thongnong',[
            'theEntry' => $theEntry,
            'theEntry_info' => $theEntry_info,
            'theEntries' => $theEntries,
            'theList_tour' => $theList_tour,
            'location' => $location,
            'root' => $this->getRootAboutUs()
        ]);
    }
     public function actionFondationThongnongSingleAboutUs() {
          $theEntry = \app\modules\whoarewe\api\Catalog::get(URI);
          $this->entry = $theEntry;
          if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
          $this->getSeo($theEntry->model->seo);
          $theList_tour;

          if($theEntry->data->location != NULL){
            foreach ($theEntry->data->location as $v) {


                $theList_tour = \app\modules\programmes\models\Item::find()
                ->where("locations LIKE '%".$v."%'")
                ->with(['photos'])
                ->orderBy('rand()')
                ->limit(3)
                ->all();
            }
        }else{
            $theList_tour = NULL;
        }
        $location = \app\modules\libraries\api\Catalog::items(['where'=>['in','category_id',[3, 4, 5, 6]],'pagination' => ['pageSize'=>0]]);


        $location = \yii\helpers\ArrayHelper::map($location, 'slug','title');  

        return $this->render('//page2016/fondation-thongnong-single',[
            'theEntry' => $theEntry,
            'theList_tour' => $theList_tour,
            'location' => $location,
            'root' => $this->getRootAboutUs()

            ]);
     }
    public function actionPortraitSingleAboutUs() {
        $theEntry = \app\modules\whoarewe\api\Catalog::get(URI);
        $this->entry = $theEntry;
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        $this->getSeo($theEntry->model->seo);
        $theEntries = \app\modules\whoarewe\api\Catalog::cat(SEG1)->items([
            'where' => [
                '!=','item_id', $theEntry->model->item_id
            ]    
            ]);
        return $this->render(IS_MOBILE ? '//page2016/mobile/portrait-single' : '//page2016/portrait-single',[
            'theEntry' => $theEntry,
            'theEntries' => $theEntries,
            'root' => $this->getRootAboutUs()
        ]);
    }
     public function actionTemoignageAboutUs() {
          //data search for testi
        //for tour Type
        if(Yii::$app->request->isAjax){
            $page = Yii::$app->request->get('page');
            $testis = \app\modules\whoarewe\api\Catalog::items([
            'where' => [
                'category_id' => 13
            ],
            'pagination' => ['pageSize' => 5, 'pageSizeLimit' => [$page, $page + 1]]
            ]);
            return $this->renderPartial(IS_MOBILE ? '//page2016/mobile/confiance' : '//page2016/temoignage',[
            'testis' => $testis,
            ]);
        }
        $type = Yii::$app->request->get('type');
        $filterType = [];
        if($type && $type != 'all')
            $filterType = ['tTourTypes' => $type];
        //for tour themes
        $theme = Yii::$app->request->get('theme');
        $filterTheme = [];
        if($theme && $theme != 'all')
            $filterTheme = ['tTourThemes' => $theme];
        //for country
        $country = Yii::$app->request->get('country');
        $filterCountry = [];
        if($country && $country != 'all'){
            if(strpos($country, '-') === false){
                $filterCountry = ['countries' => ['IN', [$country]]];
            }
            else{
                foreach (explode('-',$country) as $key => $value) {
                    $filterCountry[] = $value;
                }
                $filterCountry = ['countries' => ['IN', $filterCountry]];
            }
        }
        // $length = Yii::$app->request->get('length');
        // $filterLen = [];
        // if($length && $length != 'all'){
        //     if(strpos($length, '-') !== false){
        //         $filterLen = ['datediff(to, from)' => [explode('-',$length)[0], explode('-',$length)[1]]];
        //     } else $filterLen = ['datediff(to, from)' => [$length, 0]];
        // }
        $filter = [];
        $filter = $filter + $filterCountry + $filterTheme + $filterType;
        
//        if (Yii::$app->request->isAjax) {
//            if(Yii::$app->request->post()['type'] == 'port'){
//                $thePortrait  = \app\modules\whoarewe\api\Catalog::cat(12);
//                $queryPort = clone $thePortrait;
//                $queryFindLarge = clone $queryPort;
//                $topPortrait = $queryFindLarge->items(['filters' => [
//                        'islarge' => 'on'
//                    ]]);
//                $topPortrait = $topPortrait[0];
//                $portraits = $thePortrait->items([
//                    'pagination'=>['pageSize' => 3],
//                    'where' => ['!=','item_id', $topPortrait->model->item_id]
//                    ]);
//                $totalCountPort = count($queryPort->items(['pagination' => ['pageSize'=>0],
//                    'where' => ['!=','item_id', $topPortrait->model->item_id]
//                    ]));
//                $pagesPort = new Pagination([
//                    'totalCount' => $totalCountPort,
//                    'defaultPageSize' => 3
//                ]);
//                return $this->renderPartial('//page2016/ajax/port-ajax', [
//                        'portraits' => $portraits,
//                        'pagesPort' => $pagesPort
//                    ]);
//            }
//             if(Yii::$app->request->post()['type'] == 'testi'){
//                $theTesti = \app\modules\whoarewe\api\Catalog::cat(13);
//                $queryTesti = clone $theTesti;
//                $testis = $theTesti->items([
//                    'where' => [
//                        'category_id' => 13
//                    ],
//                    'pagination' => ['pageSize' => 5],
//                    'filters' => $filter
//                    ]);
//                
//                $totalCountTesti = count($queryTesti->items(['pagination' => ['pageSize'=>0],
//                    'filters' => $filter]));
//                $pageTesti = new Pagination([
//                    'totalCount' => $totalCountTesti,
//                    'defaultPageSize' => 5,
//                ]);
//
//                return $this->renderPartial('//page2016/ajax/testi-ajax', ['testis' => $testis,
//                    'pageTesti' => $pageTesti]);
//
//            }
//        }
        $theEntry = \app\modules\whoarewe\api\Catalog::cat(URI);
        $this->entry = $theEntry;
		$this->getSeo($theEntry->model->seo);
        //get data category & items portrain
        $thePortrait  = \app\modules\whoarewe\api\Catalog::cat(12);
        $queryPort = clone $thePortrait;
        $queryFindLarge = clone $queryPort;
        $topPortrait = $queryFindLarge->items(['filters' => [
                'islarge' => 'on'
            ]]);
        $topPortrait = $topPortrait[0];
        $portraits = $thePortrait->items([
            'pagination'=>['pageSize' => 0],
            'where' => ['!=','item_id', $topPortrait->model->item_id]
            ]);
        $totalCountPort = count($queryPort->items(['pagination' => ['pageSize'=>0],
            'where' => ['!=','item_id', $topPortrait->model->item_id]
            ]));
            
        $pagesPort = new Pagination([
            'totalCount' => $totalCountPort,
            'defaultPageSize' => 3
        ]);
        //get data category & items testi
        //process data testi
        $theTesti = \app\modules\whoarewe\api\Catalog::cat(13);
        $totalCountTesti = count($theTesti->items(['pagination' => ['pageSize'=>0],
            'filters' => $filter]));
        //$queryTesti = clone $theTesti;
        if(Yii::$app->request->get('see-more') != NULL){
            $pagesize = Yii::$app->request->get('see-more');
        }else{
            $pagesize = 5;
        }
        
        $testis = \app\modules\whoarewe\api\Catalog::items([
            'where' => [
                'category_id' => 13
            ],
            'pagination' => ['pageSize' => $pagesize],
            'filters' => $filter
            ]);
       

//        $pageTesti = new Pagination([
//            'totalCount' => $totalCountTesti,
//            'defaultPageSize' => 5,
//        ]);
//        $this->pagination = $pageTesti->pageCount;
        return $this->render(IS_MOBILE ? '//page2016/mobile/confiance' : '//page2016/temoignage',[
            'theEntry' => $theEntry,
            'thePortrait' => $thePortrait,
            'portraits' => $portraits,
            'topPortrait' => $topPortrait,
            'pagesPort' => $pagesPort,
            'theTesti' => $theTesti,
            'testis' => $testis,
            'pagesize' => $pagesize,
            'totalCountTesti' => $totalCountTesti,
            'root' => $this->getRootAboutUs()
        ]);
    }

    public function actionTemoignageTypeAboutUs(){
        $theEntry = \app\modules\whoarewe\api\Catalog::cat(URI);
        if(Yii::$app->request->isAjax && IS_MOBILE){
            $testis = $theEntry->items([
            'where' => [
                'category_id' => 13
            ],
            'pagination' => ['pageSize' => IS_MOBILE ? 8 : 5],
            ]);
            return $this->renderPartial(IS_MOBILE ? '//page2016/mobile/temoignage-type' : '//page2016/temoignage-type',[
            'testis' => $testis,
            ]);
        }
        $this->entry = $theEntry;
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        $this->getSeo($theEntry->model->seo);
        //process data testi
        $root = \yii\easyii\modules\page\api\Page::get(15);
        $queryTesti = clone $theEntry;
        $testis = $theEntry->items([
            'where' => [
                'category_id' => 13
            ],
            'pagination' => ['pageSize' => IS_MOBILE ? 8 : 5],
            ]);
        $totalCountTesti = count($queryTesti->items(['pagination' => ['pageSize'=>0]]));
        $pageTesti = new Pagination([
            'totalCount' => $totalCountTesti,
            'defaultPageSize' => IS_MOBILE ? 8 : 5,
            'forcePageParam' => false
        ]);
        $this->pagination = $pageTesti->pageCount;
        if (Yii::$app->request->isAjax) {
             if(Yii::$app->request->post()['type'] == 'testi'){
                return $this->renderPartial('//page2016/ajax/testi-ajax', ['testis' => $testis,
                    'pageTesti' => $pageTesti]);

            }
        }

         return $this->render(IS_MOBILE ? '//page2016/mobile/temoignage-type' : '//page2016/temoignage-type', [
            'theEntry' => $theEntry,
            'testis' => $testis,
            'pageTesti' => $pageTesti,
            'root' => $root
        ]);
    }

    public function actionPortraitAboutUs(){
        $theEntry = \app\modules\whoarewe\api\Catalog::cat(URI);
        if(Yii::$app->request->isAjax && IS_MOBILE){
            $page = Yii::$app->request->get('page');
            $pagesize = 8;
            $portraitsAjax = $theEntry->items([
                'pagination' => ['pageSize' => $pagesize]
            ]);
            return $this->renderPartial('//page2016/mobile/portrait', [
                'portraits' => $portraitsAjax,
                ]);
        }
        $this->entry = $theEntry;
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        $this->getSeo($theEntry->model->seo);
        $root = \yii\easyii\modules\page\api\Page::get(15);
        //get data category & items portrain
        $queryPort = clone $theEntry;
        $queryFindLarge = clone $queryPort;
        $topPortrait = $queryFindLarge->items(['filters' => [
                'islarge' => 'on'
            ]]);
        $topPortrait = $topPortrait[0];
        
        if(Yii::$app->request->get('see-more')){
            $pagesize = Yii::$app->request->get('see-more');
        }else{
            $pagesize = 3;
        }
        if(IS_MOBILE) $pagesize = 8;
        $portraits = $theEntry->items([
            'pagination'=>['pageSize' => $pagesize],
            'where' => ['!=','item_id', $topPortrait->model->item_id]
            ]);
        $totalCountPort = count($queryPort->items(['pagination' => ['pageSize'=>0],
            'where' => ['!=','item_id', $topPortrait->model->item_id]
            ]));
//        $pagesPort = new Pagination([
//            'totalCount' => $totalCountPort,
//            'defaultPageSize' => 3
//        ]);
//        $this->pagination = $pagesPort->pageCount;
//        if (Yii::$app->request->isAjax) {
//            if(Yii::$app->request->post()['type'] == 'port'){
//                return $this->renderPartial('//page2016/ajax/port-ajax', [
//                        'portraits' => $portraits,
//                        'pagesPort' => $pagesPort
//                    ]);
//            }
//        }
        return $this->render(IS_MOBILE ? '//page2016/mobile/portrait' : '//page2016/portrait',[
            'theEntry' => $theEntry,
            'portraits' => $portraits,
            'topPortrait' => $topPortrait,
            //'pagesPort' => $pagesPort,
            'totalCountPort' => $totalCountPort,
            'pagesize' => $pagesize,
            'root' => $root
        ]);
    }

    public function actionSearchTemoignageAboutUs() {
        $theEntry = \app\modules\whoarewe\api\Catalog::cat('temoignages');
        $this->entry = $theEntry;
          //data search for testi
        //for tour Type
         $type = Yii::$app->request->get('type');
        $filterType = [];
        if($type && $type != 'all')
            $filterType = ['tTourTypes' => ['IN',explode(',',$type)]];
        //for tour themes
        $theme = Yii::$app->request->get('theme');
        $filterTheme = [];
        if($theme && $theme != 'all')
            $filterTheme = ['tTourThemes' => ['IN',explode(',',$theme)]];
        //for country
        $country = Yii::$app->request->get('country');
        $filterCountry = [];
        if($country && $country != 'all'){
            if(strpos($country, '-') === false){
                $filterCountry = ['countries' => ['IN', [$country]]];
            }
            else{
                foreach (explode('-',$country) as $key => $value) {
                    $filterCountry[] = $value;
                }
                $filterCountry = ['countries' => ['IN', $filterCountry]];
            }
        }
        
        $filter = [];
        $filter = $filter + $filterCountry + $filterTheme + $filterType;
        
        if (Yii::$app->request->isAjax) {
            if(IS_MOBILE){
                $theTesti = \app\modules\whoarewe\api\Catalog::cat(13);
                $queryTesti = clone $theTesti;
                $testis = $theTesti->items([
                    'where' => [
                        'category_id' => 13
                    ],
                    'pagination' => ['pageSize' => IS_MOBILE ? 8 : 5],
                    'filters' => $filter
                    ]);
                return $this->renderPartial('//page2016/mobile/search-temoignage', ['testis' => $testis
                ]);
            }

            if(Yii::$app->request->post()['type'] == 'testi'){
                $theTesti = \app\modules\whoarewe\api\Catalog::cat(13);
                $queryTesti = clone $theTesti;
                $testis = $theTesti->items([
                    'where' => [
                        'category_id' => 13
                    ],
                    'pagination' => ['pageSize' => 5],
                    'filters' => $filter
                    ]);
                
                $totalCountTesti = count($queryTesti->items(['pagination' => ['pageSize'=>0],
                    'filters' => $filter]));
                $pageTesti = new Pagination([
                    'totalCount' => $totalCountTesti,
                    'pageSize' => 5,
                ]);
                return $this->renderPartial('//page2016/ajax/testi-ajax', ['testis' => $testis,
                    'pageTesti' => $pageTesti]);

            }
        }
        $theEntry = \yii\easyii\modules\page\api\Page::get(URI);
        $this->getSeo($theEntry->model->seo);
        
        //get data category & items testi
        //process data testi
        $theTesti = \app\modules\whoarewe\api\Catalog::cat(13);
        $queryTesti = clone $theTesti;
        $testis = $theTesti->items([
            'where' => [
                'category_id' => 13
            ],
            'pagination' => ['pageSize' => IS_MOBILE ? 8 : 5],
            'filters' => $filter
            ]);
        $totalCountTesti = count($queryTesti->items(['pagination' => ['pageSize'=>0],
            'filters' => $filter]));
        $pageTesti = new Pagination([
            'totalCount' => $totalCountTesti,
            'pageSize' => IS_MOBILE ? 8 : 5,
        ]);
        $this->pagination = $pageTesti->pageCount;
        return $this->render(IS_MOBILE ? '//page2016/mobile/search-temoignage' : '//page2016/search-temoignage',[
            'theEntry' => $theEntry,
            'theTesti' => $theTesti,
            'testis' => $testis,
            'pageTesti' => $pageTesti,
            'totalCountTesti' => $totalCountTesti
        ]);
    }
     public function actionTemoignageSingleAboutUs() {
        $theEntry = Catalog::get(SEG2);
        $this->entry = $theEntry;
         $allCountries = Country::find()->select(['code', 'name_fr'])->orderBy('name_fr')->asArray()->all();
         $allCountries = ArrayHelper::map($allCountries,'code','name_fr');
        
         if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
         $this->getSeo($theEntry->model->seo);
        return $this->render(IS_MOBILE ? '//page2016/mobile/temoignage-single' : '//page2016/temoignage-single',[
            'allCountries' => $allCountries,
            'theEntry' => $theEntry,
            'root' => $this->getRootAboutUs() 
        ]);
    }
    
     public function actionNosSecretAboutUs() {
        
        // $theEntry = Catalog::cat(19);
         $theEntry = \app\modules\whoarewe\api\Catalog::cat(URI);
         $this->entry = $theEntry;
         if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
		 $this->getSeo($theEntry->model->seo);
        // var_dump($theEntry->model);exit;
         
        $theItem = Catalog::get(634);
		  //echo '<pre>';
         //var_dump($theItem);exit;
        $listModules = [];
        if($theItem != NULL){
        foreach ($theItem->data->module as $v) {
            //foreach ($v as $value) {
                $listModules[] = \app\modules\modulepage\models\Item::find()
                        ->where(['slug'=>$v])
                        ->with(['photos'])
                        ->one();
           // }
        }
		}
        $listModules_Exclu = [];
        
       
       
        foreach ($listModules as $v) {
            
            foreach ($v->data->exclusives as $value) {
                $listModules_Exclu[] = \app\modules\exclusives\models\Item::find()
                        ->where(['item_id'=>$value])
                        ->with(['photos'])
                        ->one();
            }
        }
         
         return $this->render(IS_MOBILE ? '//page2016/mobile/nos-secret-dailleurs' : '//page2016/nos-secret-dailleurs',[
            'theEntry' => $theEntry,
            'theItem' => $theItem,
            'listModules' => $listModules,
            'listModules_Exclu' => $listModules_Exclu,
            'root' => $this->getRootAboutUs()
        ]);
    }
    public function actionResultNosSecretDailleursExclusivites()
    {

        $theEntry = \yii\easyii\modules\page\api\Page::get(URI);
        
        $this->entry = $theEntry;
        $this->getSeo($theEntry->model->seo);

        $process = $this->getAjaxFilterExclusive();
        
        $theEntries = $process['voyage'];
        $totalCount = $process['totalCount'];
        $numberFilter = $process;
        
        if(IS_MOBILE){
            $this->totalCount = $totalCount;
            $this->arr_option_filter_mobile = [
                    'title_filter' => $theEntry->model->seo->h1 ,
                    'namefilter' => 'filter_exclusivites',
                    'uri' =>$theEntry->slug,
                    'country' => 'all',
                    'type' => 'all',
                    'length' => 'all',
                ];
        }
        
        $theEntries = $this->getAjaxFilterExclusive()['voyage'];
        $totalCount = $this->getAjaxFilterExclusive()['totalCount'];
        $numberFilter = $this->getAjaxFilterExclusive();
        

        $dataLoc = \app\modules\libraries\models\Category::findOne(['slug' => 'locations'])->children()->with('items')->asArray()->all();
        $locationsLib = [];
        foreach ($dataLoc as $key => $value) {
            $locationsLib += ArrayHelper::map($value['items'], 'slug', 'title');
        }

        $theRaisons = \app\modules\whoarewe\api\Catalog::cat(2);
        $theRaisons_list = $theRaisons->items(['category_id' => $theRaisons->model->category_id]);

        return $this->render(IS_MOBILE ? '//page2016/mobile/result-nos-secret-dailleurs' : '//page2016/result-nos-secret-dailleurs', [
            'theEntry' => $theEntry,
            'theEntries' => $theEntries,
            'locationsLib' => $locationsLib,
            'theRaisons' => $theRaisons,
            'theRaisons_list' => $theRaisons_list,
            //'theEntry' => $theEntry,
            'totalCount' => $totalCount,
            'numberFilter' => $numberFilter,
        ]);
    }
    
     public function actionImprovisezAboutUs() {
       // $theEntry = Catalog::cat(18);
        $theEntry = \app\modules\whoarewe\api\Catalog::cat(URI);
        $this->entry = $theEntry;
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
		$this->getSeo($theEntry->model->seo);
        
        return $this->render(IS_MOBILE ? '//page2016/mobile/improvisez' : '//page2016/improvisez',[
            'theEntry' => $theEntry,
            'root' => $this->getRootAboutUs()
        ]);
    }
   
    public function actionQuiSommesNousAboutUs() {
        //$theEntry = Catalog::cat(1);
        $theEntry = \app\modules\whoarewe\models\Category::find()
                ->where(['category_id'=>1])
                ->with(['photos'])
                ->one();
        $this->entry =\app\modules\whoarewe\api\Catalog::cat(1);
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
		$this->getSeo($theEntry->seo);
         $theItem_1 = Catalog::get(1);
         $theItem_2 = Catalog::get(572);
		 $theItem_3 = Catalog::get(640);
        
         $theRaisons = \app\modules\whoarewe\api\Catalog::cat(2);
        
    $theRaisons_list = $theRaisons->items(['where'=>['category_id' => $theRaisons->model->category_id],'orderBy'=>'item_id']);
        return $this->render(IS_MOBILE ? '//page2016/mobile/qui-sommes-nous' : '//page2016/qui-sommes-nous',[
            'theEntry' => $theEntry,
            'theItem_1' => $theItem_1,
            'theItem_2' => $theItem_2,
			'theItem_3' => $theItem_3,
            'theRaisons' => $theRaisons,
            'theRaisons_list' => $theRaisons_list,
            'root' => $this->getRootAboutUs()
        ]);
    }

    public function actionOfficeAboutUs()
    {

        $theEntry = \app\modules\whoarewe\models\Category::find()
            ->where(['category_id' => 36])
            ->with(['photos'])
            ->one();
        $theEntries = NULL;
        if(IS_MOBILE){
            $theEntry = \app\modules\whoarewe\api\Catalog::cat(36);
            
            $theEntries = \app\modules\whoarewe\api\Catalog::items([
                //'where' => ['and', 'category_id = '.$theEntry->category_id.'', ['not', ['item_id' => $theEntry->model->item_id]]],
                'where' => ['category_id'=>$theEntry->model->category_id],
                'orderBy' =>'item_id ASC',
                'pagination' =>['pagesize'=>0],
            ]);
        }
        $this->entry = \app\modules\whoarewe\api\Catalog::cat(36);
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        
        if(IS_MOBILE){
            $this->getSeo($theEntry->model->seo);
        }else{
            $this->getSeo($theEntry->seo);
        }
        $theItem_1 = Catalog::get(1);
        $theItem_2 = Catalog::get(572);
        $theItem_3 = \app\modules\whoarewe\api\Catalog::cat(36);


        $theRaisons = \app\modules\whoarewe\api\Catalog::cat(2);

        $theRaisons_list = $theRaisons->items(['where' => ['category_id' => $theRaisons->model->category_id], 'orderBy' => 'item_id']);
        return $this->render(IS_MOBILE ? '//page2016/mobile/nos-bureaux' : '//page2016/nos-bureaux', [
            'theEntry' => $theEntry,
            'theItem_1' => $theItem_1,
            'theItem_2' => $theItem_2,
            'theItem_3' => $theItem_3,
            'theRaisons' => $theRaisons,
            'theRaisons_list' => $theRaisons_list,
            'theEntries' => $theEntries,
            'root' => $this->getRootAboutUs()
        ]);
    }

    public function actionOfficeSingleAboutUs() {

        $theEntry = \app\modules\whoarewe\api\Catalog::get(URI);
        $this->entry = $theEntry;       
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        $this->getSeo($theEntry->model->seo);
        
        $otherOffice = \app\modules\whoarewe\api\Catalog::items([
            'where' => ['and', 'category_id = '.$theEntry->category_id.'', ['not', ['item_id' => $theEntry->model->item_id]]],
            'orderBy' =>'rand()',
            'pagination' =>['pagesize'=>0],
        ]);
        
       
        

        return $this->render(IS_MOBILE ? '//page2016/mobile/nos-bureaux-single' : '//page2016/nos-bureaux-single',[
            'theEntry' => $theEntry,
            'otherOffice' => $otherOffice,

            'root' => $this->getRootAboutUs()
        ]);
    }
	public function actionNotreEquipeAboutUs() {

        if(IS_MOBILE){
            $theEntry = \app\modules\whoarewe\api\Catalog::cat(37);
        }else{
        
            $theEntry = \app\modules\whoarewe\models\Category::find()
                ->where(['category_id' => 37])
                ->with(['photos'])
                ->one();
        }
         $this->entry =\app\modules\whoarewe\api\Catalog::cat(37);       
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        $this->getSeo($this->entry->model->seo);
         
        
        return $this->render(IS_MOBILE ? '//page2016/mobile/notre-equipe' : '//page2016/notre-equipe',[
            'theEntry' => $theEntry,
            
            'root' => $this->getRootAboutUs()
        ]);
    }
	
     public function actionIdeesDeVoyage()
    {

        $theEntry = Page::get(13);

        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');

        $this->getSeo($theEntry->model->seo);

        $this->countTour = count(\app\modules\programmes\api\Catalog::items([
            //'filters' => ['countries' => SEG1],
            'pagination' => ['pageSize' => 0]
        ]));
       // $this->countTour = $this->getAjaxFilter()['totalCount'];
        // $theSeven = \app\modules\programmes\models\Category::tree();
        $theSeven = \app\modules\programmes\models\Category::find()
            ->where(['depth' => 0])
            ->with(['photos'])
            ->orderBy('order_num')
            ->all();
         if(IS_MOBILE){
            $this->totalCount = $this->countTour;
            $this->arr_option_filter_mobile = [
                    'title_filter' => $theEntry->model->seo->h1 ,
                    'namefilter' => 'filter_voyage',
                    'uri' =>$theEntry->slug,
                    'country' => 'all',
                    'type' => 'all',
                    'length' => 'all',
                ];
        }    
        return $this->render(IS_MOBILE ? '//page2016/mobile/idees-de-voyage' : '//page2016/idees-de-voyage', [
            'theEntry' => $theEntry,
            'theSeven' => $theSeven,
        ]);
    }
     
    public function actionIdeesDeVoyageType()
    {
        $theParent = Page::get(13);
        $theEntry = \app\modules\programmes\api\Catalog::cat(URI);
        $this->entry = $theEntry;
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');



        if ($theEntry->parents()) {
            return Yii::$app->runAction('amica-fr/idees-de-voyage-list-entre-ocean');
        }
        $this->getSeo($theEntry->model->seo);


        // xy ly Filter Ajax



            $theEntries = $this->getAjaxFilter(['country'=>'','type'=>$theEntry->model->category_id])['voyage'];
            $totalCount = $this->getAjaxFilter(['country'=>'','type'=>$theEntry->model->category_id])['totalCount'];
            $numberFilter = $this->getAjaxFilter(['country'=>'','type'=>$theEntry->model->category_id]);

        // end Filter
            if(IS_MOBILE){
                $this->totalCount = $totalCount;
                $this->arr_option_filter_mobile = [
                        'title_filter' => $theEntry->model->seo->h1 ,
                        'namefilter' => 'filter_voyage',
                        'uri' =>$theEntry->slug,
                        'country' => 'all',
                        'type' => $theEntry->model->category_id,
                        'length' => 'all',
                        'switch_link' => 'filter_type_active',
                    ];
            }
        $theRaisons = \app\modules\whoarewe\api\Catalog::cat('10-raisons-de-partir-avec-amica-travel');
        $theRaisons_list = $theRaisons->items(['where' => ['category_id' => $theRaisons->model->category_id], 'orderBy' => 'item_id']);


        return $this->render(IS_MOBILE ? '//page2016/mobile/idees-de-voyage-type' : '//page2016/idees-de-voyage-type', [
            'theParent' => $theParent,
            'theEntry' => $theEntry,
            'theEntries' => $theEntries,
            'theRaisons' => $theRaisons,
            'theRaisons_list' => $theRaisons_list,
            'totalCount' => $totalCount,
            'numberFilter' => $numberFilter,
        ]);
    }

    
    public function getAjaxFilter($input = []){
        
        
        if(Yii::$app->request->get('country') == NULL && Yii::$app->request->get('length') == Null && Yii::$app->request->get('type') == Null){
            

            if(!empty($input)){
                $category_id = ['category_id' => $input['type']];
                if(in_array($input['type'], [4,8,9,10,11])){
                   // $input['type'] = [8,9,10,11];
                    $category_id_total = ['category_id' => [8,9,10,11]];
                    //$category_id = ['category_id' => [8,9,10,11]];
                    $category_id = ['category_id' => $input['type']];
                }else{
                    $category_id_total = ['category_id' => $input['type']];
                    $category_id = ['category_id' => $input['type']];
                }

                if($input['country'] != NULL && in_array($input['type'], [4,8,9,10,11])){
                    $category_id = ['category_id' => [8,9,10,11]];
                }
                
                $fil_countries = ['countries'=>$input['country']];
                
            }else{
                $category_id = ['category_id' => ''];
                $category_id_total = ['category_id' => ''];
                $fil_countries = '';
            }

                
            
            $voyage_total = \app\modules\programmes\api\Catalog::items([
                    'where' => $category_id_total,
                    'filters' => $fil_countries,    
                    'pagination' => ['pageSize' => 0]

                ]);
            
            if(SEG1 == 'vietnam' || SEG1 == 'laos' || SEG1 == 'cambodge' || SEG1 == 'birmanie' || SEG1.'/'.SEG2 =='voyage/itineraire'){
                $voyage = \app\modules\programmes\api\Catalog::items([
                    'orderBy' => ['on_top_flag' => SORT_ASC, 'on_top' => SORT_ASC],
                    'where' => $category_id,
                    'filters' => $fil_countries,    
                    'pagination' => ['pageSize' => 8]

                ]);
            }else{
                $voyage = \app\modules\programmes\api\Catalog::items([
                  //  'orderBy' => ['on_top_flag' => SORT_ASC, 'on_top' => SORT_ASC],
                    'where' => $category_id,
                    'filters' => $fil_countries,    
                    'pagination' => ['pageSize' => 8]

                ]);
            }   
               
             
            $totalCount = count($voyage_total);
        
        // get number option filter
            // option destionation
                $countCountry = $this->getNumberFilterCountry($voyage_total);
                
            // option Length
                $countLength = $this->getNumberFilterLength($voyage_total);

            // option Type
            if(isset($input['country']) == ''){    
                 $totaltour_first = \app\modules\programmes\api\Catalog::items([
                   // 'where' => $category_id,
                   // 'filters' => $fil_countries,  
                    'pagination' => ['pageSize' => 0]
                    ]);
                
                $countType = $this->getNumberFilterTypeIdeel($totaltour_first); 
                 
                
            }else if(isset($input['country']) == SEG1){
                $totaltour_first = \app\modules\programmes\api\Catalog::items([
                   // 'where' => $category_id,
                    'filters' => $fil_countries,  
                    'pagination' => ['pageSize' => 0]
                    ]);
                
                $countType = $this->getNumberFilterTypeIdeel($totaltour_first); 
            }else{       
                $countType = $this->getNumberFilterTypeIdeel($voyage_total); 
                
            }            
        // end
        }else{
        //xu ly see more
        
        
        if(Yii::$app->request->get('see-more') !== NULL){
            $pagesize = Yii::$app->request->get('see-more');
        }else{
            $pagesize = 8;
        }
        
        // xu ly ajax type
        
        
        
        $type = Yii::$app->request->get('type');
        $typeNoChild = $typeChild = [];
        
        if(!$type || $type == 'all'){
            $filterType = [];
            
        } else {
            foreach (explode('-',$type) as $key => $value) {
               
                    if( $childrenType = \app\modules\programmes\models\Category::findOne($value)->children(1)->all()){
                        
                        $typeChild = ArrayHelper::getColumn($childrenType, 'category_id');
                        
                    }else {
                         $typeNoChild[] = intval($value);
                    }
            }
            $filterType = array_merge($typeChild, $typeNoChild);
            
        }
        $filterType = ['category_id' => $filterType];
        
         // xu ly ajax country
        
        $country = Yii::$app->request->get('country');
        
        if(!$country || $country == 'all' || count(explode('-', $country)) == 4){
            $filters = [];
        } else {
            if(strpos($country, '-') === false){
                $filters = ['countries' => $country];
            }
            else{
                $filters = ['countries' => ['IN',explode('-',$country)]];
            }
        }
        
          // xu ly ajax length
        
        $length = Yii::$app->request->get('length');
        if($length == 'all'){
            $length = '';
        }
        $filterLen = [];
        if(strpos($length, '-') !== false){
              $arrLen = explode('-',$length);
              asort($arrLen);
              if($arrLen[0]==1) $arrLen[0] = 0;
              if(count($arrLen) == 4){
                $filterLen = ['between', 'days', $arrLen[0], end($arrLen)];
              }
              if(count($arrLen) == 3){
                $filterLen = ['or',
                ['between', 'days', $arrLen[0], $arrLen[1]],
                ['>=','days', end($arrLen)]  
                ];
              }
              if(count($arrLen) == 2){
                $filterLen = ['between', 'days', $arrLen[0], $arrLen[1]];
              }
        } else {
            $filterLen = ['>=','days', $length];
        
        }
        
        if(SEG1 == 'vietnam' || SEG1 == 'laos' || SEG1 == 'cambodge' || SEG1 == 'birmanie' || SEG1.'/'.SEG2 =='voyage/itineraire'){
                 $voyage = \app\modules\programmes\api\Catalog::items([
                        'orderBy' => ['on_top_flag' => SORT_ASC, 'on_top' => SORT_ASC],
                        'where' => ['and',
                            $filterType,
                            $length ? $filterLen : []
                            ],
                        'filters' => $filters,
                        'pagination' => ['pageSize' => $pagesize]
                       // 'pagination' => ['pageSize' => 0]
                        ]);
            }else{
                $voyage = \app\modules\programmes\api\Catalog::items([
                 //   'orderBy' => ['on_top_flag' => SORT_ASC, 'on_top' => SORT_ASC],
                    'where' => ['and',
                        $filterType,
                        $length ? $filterLen : []
                        ],
                    'filters' => $filters,
                    'pagination' => ['pageSize' => $pagesize]
                   // 'pagination' => ['pageSize' => 0]
                    ]);
            }   
       
            
           
        // get number option filter
            $totaltour = \app\modules\programmes\api\Catalog::items([
                    'where' => ['and',
                        $filterType,
                        $length ? $filterLen : []
                        ],
                    'filters' => $filters,
                    'pagination' => ['pageSize' => 0]
                    ]);
        
            // option destionation
            if(Yii::$app->request->get('first') == 'destination' || Yii::$app->request->get('first') == 'length' || Yii::$app->request->get('first') == 'type'){
                $totaltour_first = \app\modules\programmes\api\Catalog::items([
                    'where' => ['and',
                        $filterType,
                        $length ? $filterLen : []
                        ],
                    //'filters' => $filters,
                    'pagination' => ['pageSize' => 0]
                    ]);
                
                $countCountry = $this->getNumberFilterCountry($totaltour_first);
                
                $totaltour_first = \app\modules\programmes\api\Catalog::items([
                    'where' => ['and',
                        $filterType,
                     //   $length ? $filterLen : []
                        ],
                    'filters' => $filters,
                    'pagination' => ['pageSize' => 0]
                    ]);
                
                $countLength = $this->getNumberFilterLength($totaltour_first);
               
                    
                $totaltour_first = \app\modules\programmes\api\Catalog::items([
                    'where' => ['and',
                     //   $filterType,
                        $length ? $filterLen : []
                        ],
                    'filters' => $filters,
                    'pagination' => ['pageSize' => 0]
                    ]);
                
                $countType = $this->getNumberFilterTypeIdeel($totaltour_first);
                
                
                 
            }else{
                
                $countCountry = $this->getNumberFilterCountry($totaltour);
                
                $countLength = $this->getNumberFilterLength($totaltour);   
                 
                $countType = $this->getNumberFilterTypeIdeel($totaltour);
                    
               
            }     
            // option Length
            
           

        // end
        
        
                $totalCount = count($totaltour);
                }
        
            return ['voyage'=>$voyage,
                    'totalCount'=>$totalCount,
                    'countCountry' => $countCountry,
                    'countLength' => $countLength,
                    'countType' => $countType
                    ];
       

    }

    // Xu ly thong so cua bo loc - FILTERS
        
    public function getNumberFilterCountry($input){
        $countCountry = ArrayHelper::getColumn(ArrayHelper::map(ArrayHelper::map($input, 'slug', 'model'), 'item_id', 'data'), 'countries');
                   
            $vietnam = 0;
            $laos = 0;
            $cambodge = 0;
            $birmanie = 0;
            foreach ($countCountry as $value) {
                foreach ($value as $v) {
                    if($v == 'vietnam'){
                        $vietnam++;
                    }
                    if($v == 'laos'){
                        $laos++;
                    }
                    if($v == 'cambodge'){
                        $cambodge++;
                    }
                    if($v == 'birmanie'){
                        $birmanie++;
                    }
                }
            }
             $countCountry = [
                'vietnam' => $vietnam,
                'laos' => $laos,
                'cambodge' => $cambodge,
                'birmanie' => $birmanie
            ];
        return $countCountry;    
    }
    public function getNumberFilterLength($input){
        $countLength = ArrayHelper::map(ArrayHelper::map($input, 'slug', 'model'), 'item_id','days');
        $day0_7 = 0;
        $day8_14 = 0;
        $day15 = 0;

        foreach ($countLength as $v) {
            if($v <= 7){
                $day0_7++;
            }else if($v >= 8 && $v <= 14){
                $day8_14++;
            }else{
                $day15++;
            }
        }
        $countLength = [
            '1-7' => $day0_7,
            '8-14' => $day8_14,
            '15' => $day15
        ];  
        return $countLength;
    }
    
    public function getNumberFilterType($input){
        $countType = array_count_values(ArrayHelper::map(ArrayHelper::map($input, 'slug', 'model'), 'item_id','category_id'));
        $countType_null = array(1 => 0, 2 => 0, 3 => 0, 4 => 0,5 => 0, 6 => 0);
        $countType = $countType + $countType_null;
        return $countType;    
    }
    public function getNumberFilterTypeIdeel($input){
        $countType = array_count_values(ArrayHelper::map(ArrayHelper::map($input, 'slug', 'model'), 'item_id','category_id'));
        $countType_null = array(1 => 0, 2 => 0, 3 => 0, 4 => 0,5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0);
        $countType = $countType + $countType_null;
        $countType[4] = $countType[8] + $countType[9] + $countType[10] + $countType[11];    
        return $countType;    
    }


    // End thong so.
    

    public function getAjaxFilterExclusive($input = []){
        
        
        if(Yii::$app->request->get('country') == NULL && Yii::$app->request->get('type') == Null){
            
            //var_dump($input);exit;
            if(!empty($input)){
              
                $category_id = ['category_id' => $input['type']];
                $fil_countries = ['countries'=>$input['country']];
            }else{
                $category_id = ['category_id' => ''];
                $fil_countries = ['countries'=>''];
            }
       
            
            $voyage_total = \app\modules\exclusives\api\Catalog::items([
                'where' => $category_id,
                'filters' => $fil_countries,
                'pagination' => ['pageSize' => 0]
            ]);
           // var_dump($voyage_total);exit;
            
            $voyage = \app\modules\exclusives\api\Catalog::items([
                'orderBy' => ['on_top_flag' => SORT_ASC, 'on_top' => SORT_ASC],
                'where' => $category_id,
                'filters' => $fil_countries,
                'pagination' => ['pageSize' => 8]
            ]);
            $totalCount = count($voyage_total);
             
           
             
             
        
        // get number option filter
            // option destionation
                $countCountry = $this->getNumberFilterCountry($voyage_total);
            // end option destination
            
            // option Type
                 
            if(URI == 'secrets-ailleurs/itineraire' || $input['country'] == ''){
                //echo 'ok';exit;
                 $totaltour_first = \app\modules\exclusives\api\Catalog::items([
                    
                    'pagination' => ['pageSize' => 0]
                    ]);
                 
                $countType = $this->getNumberFilterType($totaltour_first);
                
            }else if($input['type'] !== ''){
                $totaltour_first = \app\modules\exclusives\api\Catalog::items([
                    'filters' => ['countries'=>$input['country']],
                    'pagination' => ['pageSize' => 0]
                    ]);
                 $countType = $this->getNumberFilterType($totaltour_first);

            }else{     
                $countType = $this->getNumberFilterType($voyage_total);
            }            
        // end
        }else{ // Xy ly khi bat dau ton tai ajax get
            //xu ly see more
                if(Yii::$app->request->get('see-more') !== NULL){
                    $pagesize = Yii::$app->request->get('see-more');
                }else{
                    $pagesize = 8;
                }
            // xu ly ajax type
        
        
        
        $type = Yii::$app->request->get('type');
        $typeNoChild = $typeChild = [];
        
        if(!$type || $type == 'all'){
            $filterType = [];
            
        } else {
            foreach (explode('-',$type) as $key => $value) {
               
                    if( $childrenType = \app\modules\exclusives\models\Category::findOne($value)->children(1)->all()){
                        
                        $typeChild = ArrayHelper::getColumn($childrenType, 'category_id');
                        
                    }else {
                         $typeNoChild[] = intval($value);
                    }
            }
            $filterType = array_merge($typeChild, $typeNoChild);
            
        }
        $filterType = ['category_id' => $filterType];
        
         // xu ly ajax country
        
        $country = Yii::$app->request->get('country');
        if(!$country || $country == 'all' || count(explode('-', $country)) == 4){
            $filters = [];
        } else {
            if(strpos($country, '-') === false){
                $filters = ['countries' => $country];
            }
            else{
                $filters = ['countries' => ['IN',explode('-',$country)]];
            }
        }
        
          // xu ly ajax length
        
        $length = Yii::$app->request->get('length');
        if($length == 'all'){
            $length = '';
        }
        if(strpos($length, '-') !== false){
              $arrLen = explode('-',$length);
              asort($arrLen);
              if($arrLen[0]==1) $arrLen[0] = 0;
              if(count($arrLen) == 4){
                $filterLen = ['between', 'days', $arrLen[0], end($arrLen)];
              }
              if(count($arrLen) == 3){
                $filterLen = ['or',
                ['between', 'days', $arrLen[0], $arrLen[1]],
                ['>=','days', end($arrLen)]  
                ];
              }
              if(count($arrLen) == 2){
                $filterLen = ['between', 'days', $arrLen[0], $arrLen[1]];
              }
        } else {
            $filterLen = ['>=','days', $length];
        
        }
        

        $voyage = \app\modules\exclusives\api\Catalog::items([
            'orderBy' => ['on_top_flag' => SORT_ASC, 'on_top' => SORT_ASC],
            'where' => ['and',
                $filterType,
                $length ? $filterLen : []
                ],
            'filters' => $filters,
            'pagination' => ['pageSize' => $pagesize]
           
        ]);
           
        // get number option filter
            $totaltour = \app\modules\exclusives\api\Catalog::items([
                    'where' => ['and',
                        $filterType,
                        $length ? $filterLen : []
                        ],
                    'filters' => $filters,
                    'pagination' => ['pageSize' => 0]
                    ]);
        
            // option destionation
            
            if(Yii::$app->request->get('first') !== 'type'){
                if(Yii::$app->request->get('first') == 'destination'){
                    $totaltour_first = \app\modules\exclusives\api\Catalog::items([
                        'where' => ['and',
                            $filterType,

                            ],
                        'pagination' => ['pageSize' => 0]
                        ]);
                    $countCountry = $this->getNumberFilterCountry($totaltour_first);
                    
                    $totaltour_type = \app\modules\exclusives\api\Catalog::items([

                         'filters' => $filters,
                        'pagination' => ['pageSize' => 0]
                        ]);
                    
                    $countType = $this->getNumberFilterType($totaltour_type);

                }else{
                    
                    $countCountry = $this->getNumberFilterCountry($totaltour);

                }   
            }
            
            // end option destination
            
            // option Type
            if(Yii::$app->request->get('first') !== 'destination'){
                if(Yii::$app->request->get('first') == 'type'){

                    $totaltour_first = \app\modules\exclusives\api\Catalog::items([
                        'filters' => $filters,
                        'pagination' => ['pageSize' => 0]
                        ]);
                    $countType = $this->getNumberFilterType($totaltour_first);
                    
                    $totaltour_country = \app\modules\exclusives\api\Catalog::items([
                        'where' => ['and',
                            $filterType,

                            ],
                        'pagination' => ['pageSize' => 0]
                    ]);
                    
                    $countCountry = $this->getNumberFilterCountry($totaltour_country);

                }else{
                    
                    $countType = $this->getNumberFilterType($totaltour);

                }    
            }
        // end
        
        
                $totalCount = count($totaltour);
        }
        
            return ['voyage'=>$voyage,
                    'totalCount'=>$totalCount,
                    'countCountry' => $countCountry,
                    'countLength' => '',
                    'countType' => $countType,
                    ];
       

    }
    
    
  
    
    public function actionIdeesDeVoyageEntreOcean()
    {

        $theParent = Page::get(13);
        $theEntry = \app\modules\programmes\api\Catalog::cat(URI);
        $this->entry = $theEntry;

        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');


        $this->getSeo($theEntry->model->seo);

        $limi = 8;
        
         if(Yii::$app->request->get('type') !== NULL && Yii::$app->request->get('country') !== NULL && Yii::$app->request->get('length') !== NULL){
            $theEntries = $this->getAjaxFilter(['country'=>'','type'=>$theEntry->model->category_id])['voyage'];
           
            $totalCount = $this->getAjaxFilter(['country'=>'','type'=>$theEntry->model->category_id])['totalCount'];
            $numberFilter = $this->getAjaxFilter(['country'=>'','type'=>$theEntry->model->category_id]);
              
        }else{ 
            
            $theEntries = \app\modules\programmes\models\Category::find()
                ->where(['tree' => $theEntry->model->category_id, 'depth' => 1])
                ->limit($limi)
                ->all();
            $totalCount = $this->getAjaxFilter(['country'=>'','type'=>$theEntry->model->category_id])['totalCount'];
            $numberFilter = $this->getAjaxFilter(['country'=>'','type'=>$theEntry->model->category_id]);
        }

        $theRaisons = \app\modules\whoarewe\api\Catalog::cat(2);
        $theRaisons_list = $theRaisons->items(['where' => ['category_id' => $theRaisons->model->category_id], 'orderBy' => 'item_id']);
        if(IS_MOBILE){
                $this->totalCount = $totalCount;
                $this->arr_option_filter_mobile = [
                        'title_filter' => $theEntry->model->seo->h1 ,
                        'namefilter' => 'filter_voyage',
                        'uri' =>$theEntry->slug,
                        'country' => 'all',
                        'type' => $theEntry->model->category_id,
                        'length' => 'all',
                    ];
            }
        return $this->render(IS_MOBILE ? '//page2016/mobile/idees-de-voyage-entre-ocean' : '//page2016/idees-de-voyage-entre-ocean', [
            'theParent' => $theParent,
            'theEntry' => $theEntry,
            'theEntries' => $theEntries,
            'theRaisons' => $theRaisons,
            'theRaisons_list' => $theRaisons_list,
            'limi' => $limi,
            'totalCount' => $totalCount,
            'numberFilter' => $numberFilter,
        ]);
    }

    public function actionIdeesDeVoyageListEntreOcean()
    {

        $theRoot = Page::get(13);
        
        $theEntry = \app\modules\programmes\api\Catalog::cat(URI);
        
        $this->entry = $theEntry;
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');


        $this->countTour = count(\app\modules\programmes\api\Catalog::items([
            //'filters' => ['category_id' => $theEntry->model->category_id],
            'where' => ['category_id' => $theEntry->model->category_id, 'status' => 1],
            'pagination' => ['pageSize' => 0],

        ]));
         $this->getSeo($theEntry->model->seo);
       
            $process = $this->getAjaxFilter(['country'=>'', 'type'=>$theEntry->model->category_id]);
         
            $theEntries = $process['voyage'];
            $totalCount = $this->countTour;
            $numberFilter = $process;
                     
        if(IS_MOBILE){
                $this->totalCount = $totalCount;
                $this->arr_option_filter_mobile = [
                        'title_filter' => $theEntry->model->seo->h1 ,
                        'namefilter' => 'filter_voyage',
                        'uri' =>$theEntry->slug,
                        'country' => 'all',
                        'type' => 4,
                        'length' => 'all',
                    ];
            }
        $theRaisons = \app\modules\whoarewe\api\Catalog::cat(2);
        $theRaisons_list = $theRaisons->items(['where' => ['category_id' => $theRaisons->model->category_id], 'orderBy' => 'item_id']);

        return $this->render(IS_MOBILE ? '//page2016/mobile/idees-de-voyage-list-entre-ocean' : '//page2016/idees-de-voyage-list-entre-ocean', [
            'theRoot' => $theRoot,
            'theEntry' => $theEntry,
            'theEntries' => $theEntries,
            'theRaisons' => $theRaisons,
            'theRaisons_list' => $theRaisons_list,
            //'limi' => $limi,
            'totalCount' => $totalCount,
            'numberFilter' => $numberFilter,
        ]);
    }

    public function actionExtensionExclusivesTourSingle(){
        $theProgram = Yii::$app->session->get('the-programes-tours-single');
        return $this->renderPartial('//page2016/extension-exclusives-tour-single', ['theProgram' => $theProgram, 'location' => Yii::$app->session->get('location')]);
       
    }
   
    public function actionIdeesDeVoyageSingle() {
          $theEntry = \app\modules\programmes\api\Catalog::get(URI);
          if(Yii::$app->request->isAjax && IS_MOBILE){
            if(Yii::$app->request->post('type') == 'formules'){
                $theProgram = Yii::$app->session->get('the-programes-tours-single');
                $location = Yii::$app->session->get('location');  
                return $this->renderPartial('//page2016/mobile/idees-de-voyage-single',[
                    'theProgram' => $theProgram,
                    'location' => $location ,
                    'type' => 'formules'
                ]);
            }

            if(Yii::$app->request->post('type') == 'combien'){
                $infos_pratiques = Yii::$app->session->get('infos_pratiques');
                return $this->renderPartial('//page2016/mobile/idees-de-voyage-single',[
                    'infos_pratiques' => $infos_pratiques,
                    'type' => 'combien'
                ]);
            }
          }
          $this->entry = $theEntry;
           if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
            if(isset( $theEntry->data->price)){
                    $mainImg = 'https://www.amica-travel.com'.$theEntry->photosArray['banner'][0]->image;
                    $title = $theEntry->title;
                    $subTt = $theEntry->model->sub_title;
                    $catName  = $theEntry->cat->title;
                    $price = $theEntry->data->price;
                    preg_match('/([\d\.]+)(?:€|\&euro;)/', $price, $matches);
                    $price = isset($matches[1]) ? $matches[1] : '';

                    $jsSeoPrice = '
					<script type="application/ld+json">
					{
						"@context": "http://schema.org",
						"@type": "Product",
						"image": "{{mainImg}}",
						"name": "{{title}}",
						"description": "{{subTt}}",
						"category": "{{catName}}",
						"brand": "Amica Travel",
						"offers": {
							"@type": "AggregateOffer",
							"lowPrice": "{{price}}",
							"availability": "InStock",
							"priceCurrency": "EUR"
						}
					}
					</script>
                ';
                $jsSeoPrice = str_replace(['{{mainImg}}', '{{title}}', '{{subTt}}', '{{catName}}', '{{price}}'], [$mainImg, $title, $subTt, $catName, $price], $jsSeoPrice);
               $this->priceSeo = $jsSeoPrice;
            }
            if($theEntry->category_id == 7) $this->notBannerRight = true;
           $theRoot = Page::get(13); 
           $theParent = \app\modules\programmes\api\Catalog::cat($theEntry->category_id);
      
         $theParent_parent = \app\modules\programmes\models\Category::find()
                        ->where(['category_id'=>$theParent->tree])
                        ->one();
         if($theParent_parent->category_id == 4){
            
            return Yii::$app->runAction('amica-fr/idees-de-voyage-entre-ocean-single');
            
         }elseif($theParent_parent->category_id == 5){
            
            if(IS_MOBILE){
                    $view = '//page2016/mobile/idees-de-voyage-croisiere-single';
            }else{
                    $view = '//page2016/idees-de-voyage-croisiere-single';
            }
            
         }else{
             if(IS_MOBILE){
                    $view = '//page2016/mobile/idees-de-voyage-single';
                }else{
                    $view = '//page2016/idees-de-voyage-single';
                }
         }
         if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
         
         $this->getSeo($theEntry->model->seo);
         //add a view program to projet
         if(Yii::$app->session->get('projet'))
            $projet = Yii::$app->session->get('projet');
          else $projet = [
            'programes'=> ['select'=>[], 'view'=>[]],
            'exclusives' => ['select'=>[], 'view'=> []]
            ];
          if(!in_array($theEntry->model->item_id, $projet['programes']['view']) && !in_array($theEntry->model->item_id, $projet['programes']['select']))
              $projet['programes']['view'][] = $theEntry->model->item_id;
          Yii::$app->session->set('projet',$projet);
            
            $theProgram = [];
            
            $pro = '';
            if($theEntry->model->exclusives != ''){
                $pro = explode(',', $theEntry->model->exclusives);
            
                foreach ($pro as $p) {
                   $item = \app\modules\exclusives\api\Catalog::get($p);
                   if($item)
                    $theProgram[] = $item;
                }
            }
        $allCountries = Country::find()->select(['code', 'dial_code', 'name_fr'])->orderBy('name_fr')->asArray()->all();
        $allDialCodes = Country::find()->select(['code', 'CONCAT(name_fr, " (+", dial_code, ")") AS xcode'])->where('dial_code!=""')->orderBy('name_fr')->asArray()->all();

        if($theParent_parent->category_id == 5){
            
            if (IS_MOBILE) {
                $model = new ContactFormMobile;
                $model->scenario = 'contactce_mobile';
             } else {
                $model = new ContactForm;
                $model->scenario = 'contactce';
             }
            $model->country = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';
            $model->countryCallingCode = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';

            if ($model->load($_POST) && $model->validate()) {
                
                $model->tourName = $theEntry->title;
                $model->tourUrl = Yii::$app->request->getAbsoluteUrl();
                
                if (IS_MOBILE) {
            $model->fname = preg_split("/\s+(?=\S*+$)/", $model->fullName)[0];
                $model->lname = '';
                if (isset(preg_split("/\s+(?=\S*+$)/", $model->fullName)[1])) {
                    $model->lname = preg_split("/\s+(?=\S*+$)/", $model->fullName)[1];
                }
            $data2 = <<<'TXT'
Tour name: {{ tourUrl : $tourUrl }}   
Votre nom: {{ prefix : $prefix }} {{ fullName : $fullName }}
Votre mail: {{ email : $email }}
Votre pays: {{ country : $country }}
Département, Votre ville: {{ region : $region }} {{ ville : $ville }}
Votre message: {{ message : $message }}
Souhaitez-vous recevoir une proposition de programme avec devis personnalisé sur d'autres régions du pays ?: {{ question : $question }}
Si vous êtes recommandé(e) par un ancien client d'Amica, merci de préciser son nom et prénom: {{ reference : $reference }}
TXT;
                 $data2 = str_replace([
                   '$tourUrl', '$prefix' ,'$fullName', '$email', '$message', '$question','$country', '$region', '$ville', '$reference'
                    ], [
                       '<a href="'.$model->tourUrl.'">'.$model->tourName.'</a>', 
                    $model->prefix, $model->fullName, $model->email, $model->message, $model->question, $model->country, $model->region, $model->ville, $model->reference
                    ], $data2);
                
                    $this->saveInquiry($model, 'Form-contactbc-mobile', $data2);
                } else {      
                
                
                $data2 = <<<'TXT'
Tour name: {{ tourUrl : $tourUrl }}   
Votre nom et prénom: {{ prefix : $prefix }} {{ fname : $fname }} { lname : $lname }}
Votre adresse mail: {{ email : $email }}
Votre pays: {{ country : $country }}
Département, Votre ville: {{ region : $region }} {{ ville : $ville }}
Votre message: {{ message : $message }}
Souhaitez-vous recevoir une proposition de programme avec devis personnalisé sur d'autres régions du pays ?: {{ question : $question }}
Si vous êtes recommandé(e) par un ancien client d'Amica, merci de préciser son nom et prénom: {{ reference : $reference }}
TXT;
                    $data2 = str_replace([
                        '$tourUrl', '$prefix', '$fname', '$lname', '$email', '$country', '$region', '$ville', '$message', '$question', '$reference'
                        ], [
                        '<a href="'.$model->tourUrl.'">'.$model->tourName.'</a>', $model->prefix, $model->fname, $model->lname, $model->email, $model->country, $model->region, $model->ville, $model->message, $model->question, $model->reference
                        ], $data2);
                    // Save db
                    $this->saveInquiry($model, 'Form-contactbc', $data2);
                
                    if($model->newsletter){
                        $data = [
                                'firstname' => $model->fname,
                                'lastname' => $model->lname,
                                'codecontry' => $model->country,
                                'source' => 'newsletter',
                                'sex' => $model->prefix,
                                'newsletter_insc' => date('d/m/Y'),
                                'statut' => 'prospect',
                                'birmanie' => false,
                                'vietnam' => false,
                                'cambodia' => false,
                                'laos' => false
                            ];
                        $this->addContactToMailjet($model->email, '1688900', $data);
                    }
                }
                // Email me
                $this->notifyAmica('Contact from ' . $model->email, '//page2016/email_template', ['data2' => $data2]);
                $this->confirmAmica($model->email);
                // Redir
                return Yii::$app->response->redirect(DIR.'merci?from=contact_bc&id='.$this->id_inquiry);
            }
        }else{
        
        $model = new DevisForm;
        $model->scenario = 'booking';
        if (IS_MOBILE) {
            $model = new DevisFormMobile;
            $model->scenario = 'mobileBooking';
        }
       

        $model->country = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';
        $model->countryCallingCode = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';

        if ($model->load($_POST) && $model->validate()) {
            
                $model->tourName = $theEntry->title;
                $model->tourUrl = Yii::$app->request->getAbsoluteUrl();
                
               
            // Save db
         if (IS_MOBILE) {
                $model->fname = preg_split("/\s+(?=\S*+$)/", $model->fullName)[0];
                $model->lname = '';
                if (isset(preg_split("/\s+(?=\S*+$)/", $model->fullName)[1])) {
                    $model->lname = preg_split("/\s+(?=\S*+$)/", $model->fullName)[1];
                }
                if (!$model->has_date) {
                    $model->departureDate = null;
                    $model->tourLength = null;
                }
                $contact = '';
               if($model->contactEmail) $contact .= $model->email;
               if($model->contactEmail && $model->contactPhone) $contact .= ' ,';
               if($model->contactPhone) $contact .= $model->phone;
            $data2 = <<<'TXT'
Tour name: {{ tourUrl : $tourUrl }}  
Extension(s): {{ extension : $extension }}              
Votre nom & prénom: {{ fullName : $fullName }}
Votre Mail: {{ email : $email }}  
Votre pays: {{ country : $country }}
Département, Votre ville: {{ region : $region }} {{ ville : $ville }}              
Comment préférez-vous communiquer avec nous?: {{ contact : $contact }}
Date d’arrivée sur place: {{ departureDate : $departureDate }}
Durée: {{ tourLength : $tourLength }}
Destination(s): {{ countriesToVisit : $countriesToVisit }}
Votre message: {{ message : $message }}
Avez-vous déjà acheté votre (vos) billet(s) d’avion internationaux aller-retour ?: {{ howTicket : $howTicket }}
Budget par personne (budget total, incluant les vols): {{ budget : $budget }}
Le petit déjeuner est généralement déjà inclus dans le prix de l’hébergement. Souhaitez-vous d’autres repas ? : {{ lepetit : $lepetit }}
Vous partez: {{ typeGo : $typeGo }}
Adulte(s): {{ numberOfTravelers12 : $numberOfTravelers12 }}
Enfant(s): {{ numberOfTravelers2 : $numberOfTravelers2 }}
BéBé: {{ numberOfTravelers1 : $numberOfTravelers1 }}
Détails d'âges : {{ agesOfTravelers12 : $agesOfTravelers12 }}
Quel(s) types d'hébergement préférez-vous  pour ce voyage ?: {{ interest : $interest }}
Combien de chambres souhaitez-vous:
Chambre double avec un grand lit: {{ hotelRoomDbl : $hotelRoomDbl }}
Chambre double avec 2 petit lits: {{ hotelRoomTwn : $hotelRoomTwn }}
Chambre pour 3 personnes: {{ hotelRoomTrp : $hotelRoomTrp }}
Chambre individuelle: {{ hotelRoomSgl : $hotelRoomSgl }}
Vos centres d’intérêts :(plusieurs choix possibles) {{ tourThemes : $tourThemes }}
Pouvez-vous nous raconter votre dernier voyage long-courrier ? (destination, type de voyage, expériences, ce que vous avez aimé, ...) {{ howMessage : $howMessage }}
Vos loisirs et passe-temps préférés (ce que vous aimez, ce que vous n’aimez pas…) : {{ howHobby : $howHobby }}
Si vous êtes recommandé(e) par un ancien client d'Amica, merci de préciser son nom et prénom: {{ reference : $reference }}
TXT;
                 $data2 = str_replace([
                    '$tourUrl', '$extension', '$departureDate', '$tourLength', '$countriesToVisit', '$typeGo', '$numberOfTravelers12', '$numberOfTravelers2', '$interest', '$tourThemes', '$contact', '$fullName', '$email', '$message', '$agesOfTravelers12', '$hotelRoomDbl', '$hotelRoomTwn', '$hotelRoomTrp', '$hotelRoomSgl', '$budget', '$howTraveler', '$howMessage', '$howHobby', '$howTicket', '$job', '$lepetit', '$country', '$region', '$ville', '$numberOfTravelers1', '$reference'
                    ], [
                    '<a href="'.$model->tourUrl.'">'.$model->tourName.'</a>', implode(', ',(array)$model->listFormules), $model->departureDate, $model->tourLength, implode(', ' ,(array)$model->countriesToVisit), $model->typeGo, $model->numberOfTravelers12, $model->numberOfTravelers2, implode(', ', (array)$model->interest), implode(', ', (array)$model->tourThemes), $contact, $model->fullName, $model->email, $model->message,$model->agesOfTravelers12, $model->hotelRoomDbl, $model->hotelRoomTwn, $model->hotelRoomTrp, $model->hotelRoomSgl, $model->budget, $model->howTraveler, $model->howMessage, $model->howHobby, $model->howTicket, $model->job, $model->lepetit, $model->country, $model->region, $model->ville, $model->numberOfTravelers1, $model->reference
                    ], $data2);
            
                $this->saveInquiry($model, 'Form-booking-mobile', $data2);
            } else {   
         
          $data2 = <<<'TXT'
Vos coordonnées:
 
Tour name: {{ tourUrl : $tourUrl }}  
Extension(s): {{ extension : $extension }}
Votre nom et prénom: {{ prefix : $prefix }} {{ fname : $fname }} {{ lname : $lname }} 
Votre adresse mail: {{ email : $email }}
Votre pays: {{ country : $country }}
Département, Votre ville: {{ region : $region }} {{ ville : $ville }}

votre projet:

Date d'arrivée approximative: {{ departureDate : $departureDate }}
Date de retour: {{ deretourDate : $deretourDate }}
Durée du voyage: {{ tourLength : $tourLength }}
Qu'est ce qui vous a donné envie de choisir cette (ou ces) destination(s) ?: {{ whyCountry : $whyCountry }}
Avez-vous déjà acheté votre (vos) billet(s) d’avion internationaux aller-retour ?: {{ howTicket : $howTicket }}
Les participants: + de 12 ans {{ numberOfTravelers12 : $numberOfTravelers12 }} détails d'âges: {{ agesOfTravelers12 : $agesOfTravelers12 }}
- de 12 ans {{ numberOfTravelers2 : $numberOfTravelers2 }} 
- de 2 ans {{ numberOfTravelers0 : $numberOfTravelers0 }}
La forme physique des participants : {{ howTraveler : $howTraveler }}
Quel(s) type(s) d’hébergement aimeriez-vous pour ce voyage: {{ hotelTypes : $hotelTypes }}
Combien de chambres souhaitez-vous:
Chambre double avec un grand lit: {{ hotelRoomDbl : $hotelRoomDbl }}
Chambre double avec 2 petit lits: {{ hotelRoomTwn : $hotelRoomTwn }}
Chambre pour 3 personnes: {{ hotelRoomTrp : $hotelRoomTrp }}
Chambre individuelle: {{ hotelRoomSgl : $hotelRoomSgl }}
Repas: {{ mealsIncluded : $mealsIncluded }}
Budget par personne: {{ budget : $budget }}

pour mieux vous connaitre:

Décrivez votre projet, votre vision du voyage et de quelle façon vous souhaitez découvrir notre pays: {{ message : $message }}
Pouvez-vous nous raconter votre dernier voyage long-courrier ? (destination, type de voyage, expériences, ce que vous avez aimé, ...) {{ howMessage : $howMessage }}
Vos loisirs et passe-temps préférés (ce que vous aimez, ce que vous n’aimez pas…) : {{ howHobby : $howHobby }}
Votre (vos) professions ? : {{ job : $job }}
Nous vous rappelons gratuitement pour mieux comprendre votre projet: {{ callback : $callback }}

TXT;
               $datacallback = <<<'TXT'
Votre numéro de téléphone: {{ countryCallingCode : $CallingCode }} {{ phone : $phone }}
Date / heure pour le RDV: {{ callDate : $callDate }} {{ callTime : $callTime }}

TXT;
               $datalast = <<<'TXT'
Si vous êtes recommandé(e) par un ancien client d'Amica, merci de préciser son nom et prénom: {{ reference : $reference }}   
Newsletters: {{ newsletter : $newsletter }}    
TXT;

      if($model->callback == 'Oui'){
                $data2 .= $datacallback;
                $data2 .= $datalast;
                $data2 = str_replace([
                    '$tourUrl', '$extension', '$prefix', '$fname', '$lname', '$email', '$country', '$region', '$ville', '$departureDate', '$deretourDate', '$tourLength', '$numberOfTravelers12', '$numberOfTravelers2', '$numberOfTravelers0', '$agesOfTravelers12', '$message', '$hotelTypes', '$hotelRoomDbl', '$hotelRoomTwn', '$hotelRoomTrp', '$hotelRoomSgl', '$mealsIncluded', '$budget', '$callback', '$CallingCode', '$phone', '$callDate', '$callTime','$newsletter', '$whyCountry', '$howTraveler', '$howMessage', '$howHobby', '$howTicket', '$job', '$reference'
                    ], [
                    '<a href="'.$model->tourUrl.'">'.$model->tourName.'</a>', implode(', ',(array)$model->extension), $model->prefix, $model->fname, $model->lname, $model->email, $model->country, $model->region, $model->ville, $model->departureDate, $model->deretourDate, $model->tourLength, $model->numberOfTravelers12, $model->numberOfTravelers2, $model->numberOfTravelers0, $model->agesOfTravelers12, $model->message, implode(', ', (array)$model->hotelTypes), $model->hotelRoomDbl, $model->hotelRoomTwn, $model->hotelRoomTrp, $model->hotelRoomSgl, $model->mealsIncluded, $model->budget, $model->callback, $model->countryCallingCode, $model->phone, $model->callDate, $model->callTime, $model->newsletter == 1 ? 'Oui' : 'Non', $model->whyCountry, $model->howTraveler, $model->howMessage, $model->howHobby, $model->howTicket, $model->job, $model->reference
                    ], $data2);
                }else{
                    
                 $data2 .= $datalast;
                 
                 $data2 = str_replace([
                    '$tourUrl', '$extension', '$prefix', '$fname', '$lname', '$email', '$country', '$region', '$ville', '$departureDate', '$deretourDate', '$tourLength', '$numberOfTravelers12', '$numberOfTravelers2', '$numberOfTravelers0', '$agesOfTravelers12', '$message', '$hotelTypes', '$hotelRoomDbl', '$hotelRoomTwn', '$hotelRoomTrp', '$hotelRoomSgl', '$mealsIncluded', '$budget', '$callback', '$newsletter', '$whyCountry', '$howTraveler', '$howMessage', '$howHobby', '$howTicket', '$job', '$reference'
                    ], [
                    '<a href="'.$model->tourUrl.'">'.$model->tourName.'</a>', implode(', ',(array)$model->extension), $model->prefix, $model->fname, $model->lname, $model->email, $model->country, $model->region, $model->ville, $model->departureDate, $model->deretourDate, $model->tourLength, $model->numberOfTravelers12, $model->numberOfTravelers2, $model->numberOfTravelers0, $model->agesOfTravelers12, $model->message, implode(', ', (array)$model->hotelTypes), $model->hotelRoomDbl, $model->hotelRoomTwn, $model->hotelRoomTrp, $model->hotelRoomSgl, $model->mealsIncluded, $model->budget, $model->callback, $model->newsletter == 1 ? 'Oui' : 'Non', $model->whyCountry, $model->howTraveler, $model->howMessage, $model->howHobby, $model->howTicket, $model->job, $model->reference
                    ], $data2);
                }
         
                $this->saveInquiry($model, 'Form-booking', $data2);
                if($model->newsletter){
                    $data = [
                    'firstname' => $model->fname,
                    'lastname' => $model->lname,
                    'codecontry' => $model->country,
                    'source' => 'newsletter',
                    'sex' => $model->prefix,
                    'newsletter_insc' => date('d/m/Y'),
                    'statut' => 'prospect',
                    'birmanie' => false,
                    'vietnam' => false,
                    'cambodia' => false,
                    'laos' => false
                ];
            }
                
            $this->addContactToMailjet($model->email, '1690435', $data);
                }
                // If he subscribes to our newsletter
              //  if ($model->newsletter == 1) $this->saveNlsub($model, 'form_booking_1216');
            

            // Email me
            $this->notifyAmica('Devis from ' . $model->email, '//page2016/email_template', ['data2' => $data2]);
            $this->confirmAmica($model->email);
            // Redir
            return Yii::$app->response->redirect(DIR.'merci?from=booking&id='.$this->id_inquiry);
        }
        
        }
        $location = \app\modules\libraries\api\Catalog::items(['where'=>['in','category_id',[3, 4, 5, 6]],'pagination' => ['pageSize'=>0]]);
           
            $location = \yii\helpers\ArrayHelper::map($location, 'slug','title');
            $dataNotes = \app\modules\libraries\api\Catalog::items(['where' => ['category_id' => [14,15,16]], 'pagination' => ['pageSize' => 0]]);
            $listParent_exclusivites = \app\modules\exclusives\api\Catalog::cats();
            $listParent_exclusivites = \yii\helpers\ArrayHelper::map($listParent_exclusivites, 'category_id','title');
            
            $infos_pratiques = \app\modules\libraries\api\Catalog::items(['where'=>['category_id'=>18],'pagination' => ['pageSize'=>0],'orderBy'=>'item_id']);
        Yii::$app->session->set('the-programes-tours-single', $theProgram); 
        Yii::$app->session->set('location', $location);    
         Yii::$app->session->set('infos_pratiques', $infos_pratiques);
        return $this->render($view,[
                    'model' => $model,
                    'allCountries' => $allCountries,
                    'allDialCodes' => $allDialCodes,
                    'theEntry'=> $theEntry,
                    'theRoot'=> $theRoot,
                    'theParent_parent' => $theParent_parent,
                    'theParent'=> $theParent,
                    'theProgram' => $theProgram,
                    'listParent_exclusivites' => $listParent_exclusivites,
                    'location' => $location,
                    'notes' =>$dataNotes,
                    'data' =>$theEntry,
                    'infos_pratiques' => $infos_pratiques,
                    'destination' => $this->getBreadCrumb('destination'),
                    'breadcrumb_3' => $this->getBreadCrumb('breadcrumb-3'),
                    'breadcrumb_4' => $this->getBreadCrumb('breadcrumb-4')
        ]);
    }
    
    public function actionIdeesDeVoyageEntreOceanSingle() {
        
         $root = Page::get(13); 
         
         $theEntry = \app\modules\programmes\api\Catalog::get(URI);
         $this->entry = $theEntry;
         if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
         $this->getSeo($theEntry->model->seo);
         
            //$countries = \app\modules\destinations\models\Category::find()->roots()->all();
            $location = \app\modules\libraries\api\Catalog::items(['where'=>['in','category_id',[3, 4, 5, 6]],'pagination' => ['pageSize'=>0]]);
           
            $location = \yii\helpers\ArrayHelper::map($location, 'slug','title');
            
            $listParent_exclusivites = \app\modules\exclusives\api\Catalog::cats();
            $listParent_exclusivites = \yii\helpers\ArrayHelper::map($listParent_exclusivites, 'category_id','title');
            
            $theProgram = [];
            
            $pro = '';
            if($theEntry->model->exclusives != ''){
                $pro = explode(',', $theEntry->model->exclusives);
            
                foreach ($pro as $p) {
                   // $p = explode('/', $p);
                    $theProgram[] = \app\modules\exclusives\api\Catalog::get($p);
                }
            }
            
         
        
        $allCountries = Country::find()->select(['code', 'dial_code', 'name_fr'])->orderBy('name_fr')->asArray()->all();
        $allDialCodes = Country::find()->select(['code', 'CONCAT(name_fr, " (+", dial_code, ")") AS xcode'])->where('dial_code!=""')->orderBy('name_fr')->asArray()->all();

        $model = new ContactForm;
        if (IS_MOBILE) {
            $model = new ContactFormMobile;
            $model->scenario = 'contactce_mobile';
         } else {
                $model->scenario = 'contactce';
         }
       

        $model->country = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';
        $model->countryCallingCode = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';

        if ($model->load($_POST) && $model->validate()) {
            
            $model->tourName = $theEntry->title;
            $model->tourUrl = Yii::$app->request->getAbsoluteUrl();
            
            if (IS_MOBILE) {
                $model->fname = preg_split("/\s+(?=\S*+$)/", $model->fullName)[0];
                $model->lname = '';
                if (isset(preg_split("/\s+(?=\S*+$)/", $model->fullName)[1])) {
                    $model->lname = preg_split("/\s+(?=\S*+$)/", $model->fullName)[1];
                }
            
            $data2 = <<<'TXT'
Tour name: {{ tourUrl : $tourUrl }}   
Votre nom: {{ prefix : $prefix }} {{ fullName : $fullName }}
Votre mail: {{ email : $email }}
Votre pays: {{ country : $country }}
Département, Votre ville: {{ region : $region }} {{ ville : $ville }}
Votre message: {{ message : $message }}
Souhaitez-vous recevoir une proposition de programme avec devis personnalisé sur d'autres régions du pays ?: {{ question : $question }}
TXT;
                 $data2 = str_replace([
                   '$tourUrl', '$prefix' ,'$fullName', '$email', '$message', '$question','$country', '$region', '$ville'
                    ], [
                       '<a href="'.$model->tourUrl.'">'.$model->tourName.'</a>', 
                    $model->prefix, $model->fullName, $model->email, $model->message, $model->question, $model->country, $model->region, $model->ville
                    ], $data2);
                
                    $this->saveInquiry($model, 'Form-contactbc-mobile', $data2);
                } else {   

                $data2 = <<<'TXT'
Tour name: {{ tourUrl : $tourUrl }}   
Votre nom et prénom: {{ prefix : $prefix }} {{ fname : $fname }} { lname : $lname }}
Votre adresse mail: {{ email : $email }}
Votre pays: {{ country : $country }}
Département, Votre ville: {{ region : $region }} {{ ville : $ville }}
Votre message: {{ message : $message }}
Souhaitez-vous recevoir une proposition de programme avec devis personnalisé sur d'autres régions du pays ?: {{ question : $question }}
TXT;
                $data2 = str_replace([
                    '$tourUrl', '$prefix', '$fname', '$lname', '$email', '$country', '$region', '$ville', '$message', '$question',
                    ], [
                    '<a href="'.$model->tourUrl.'">'.$model->tourName.'</a>', $model->prefix, $model->fname, $model->lname, $model->email, $model->country, $model->region, $model->ville, $model->message, $model->question,
                    ], $data2);
                // Save db
                $this->saveInquiry($model, 'Form-contactbc', $data2);
                if($model->newsletter){
                    $data = [
                    'firstname' => $model->fname,
                    'lastname' => $model->lname,
                    'codecontry' => $model->country,
                    'source' => 'newsletter',
                    'sex' => $model->prefix,
                    'newsletter_insc' => date('d/m/Y'),
                    'statut' => 'prospect',
                    'birmanie' => false,
                    'vietnam' => false,
                    'cambodia' => false,
                    'laos' => false
                    ];
                    $this->addContactToMailjet($model->email, '1688900', $data);
                }
            }
            
            // Email me
            $this->notifyAmica('Contact from ' . $model->email, '//page2016/email_template', ['data2' => $data2]);
            $this->confirmAmica($model->email);
            // Redir
            return Yii::$app->response->redirect(DIR.'merci?from=contact_bc&id='.$this->id_inquiry);
        }
        return $this->render(IS_MOBILE ? '//page2016/mobile/idees-de-voyage-entre-ocean-single' : '//page2016/idees-de-voyage-entre-ocean-single',[
                    'model' => $model,
                    'allCountries' => $allCountries,
                    'allDialCodes' => $allDialCodes,
                    'root'=> $root,
                    'theEntry'=> $theEntry,
                    'location' => $location,
                    'theProgram' => $theProgram,
                    'listParent_exclusivites' => $listParent_exclusivites,
                    'infos_pratiques' => [],
                    'destination' => $this->getBreadCrumb('destination'),
                    'breadcrumb_3' => $this->getBreadCrumb('breadcrumb-3'),
                    'breadcrumb_4' => $this->getBreadCrumb('breadcrumb-4')
        ]);
    }
    
    public function actionRechercheIdeesDeVoyage()
    {
        $theEntry = \yii\easyii\modules\page\api\Page::get(URI);
        $this->entry = $theEntry;
        $this->getSeo($theEntry->model->seo);

        $voyage = $this->getAjaxFilter()['voyage'];
        $totalCount = $this->getAjaxFilter()['totalCount'];
        $numberFilter = $this->getAjaxFilter();
        if(IS_MOBILE){
            $this->totalCount = $totalCount;
            $this->arr_option_filter_mobile = [
                    'title_filter' => $theEntry->model->seo->h1 ,
                    'namefilter' => 'filter_voyage',
                    'uri' =>$theEntry->slug,
                    'country' => 'all',
                    'type' => 'all',


                    'length' => 'all',
                ];
        }
        $dataLoc = \app\modules\libraries\models\Category::findOne(['slug' => 'locations'])->children()->with('items')->asArray()->all();
        $locationsLib = [];
        foreach ($dataLoc as $key => $value) {
            $locationsLib += ArrayHelper::map($value['items'], 'slug', 'title');
        }
        $theRaisons = \app\modules\whoarewe\api\Catalog::cat(2);
        $theRaisons_list = $theRaisons->items(['category_id' => $theRaisons->model->category_id]);

        return $this->render(IS_MOBILE ? '//page2016/mobile/recherche-idees-de-voyage' : '//page2016/recherche-idees-de-voyage', [
            'theEntry' => $theEntry,
            'voyage' => $voyage,
            'totalCount' => $totalCount,
            'locationsLib' => $locationsLib,
            'theRaisons' => $theRaisons,
            'theRaisons_list' => $theRaisons_list,
            'numberFilter' => $numberFilter,
        ]);
    }



    public function actionDevisBooking(){
        $this->layout = 'main-form';
        if(IS_MOBILE){
            $this->layout = 'mobile-form';
        }
        $theEntry = \app\modules\programmes\api\Catalog::get(preg_replace('/\/form$/', '', URI));
        $this->entry = $theEntry;
        $this->getSeo($theEntry->model->seo);
        $parents = $theEntry->parents();
        if($parents){
            if($parents[0]->category_id == 5 || $parents[0]->category_id == 4){
                     return Yii::$app->runAction('amica-fr/contact-booking');
            }
        }

        if (Yii::$app->request->isAjax) {
        // select option extension
            if(Yii::$app->request->post()['type'] == 'selected'){
              $name = Yii::$app->request->post()['extension'];
              
              if(Yii::$app->session->get('data_extension'))
                $data_extension = Yii::$app->session->get('data_extension');
              else $data_extension = [];
              if(!in_array($name, $data_extension))
                  $data_extension[$name] = $name;
//              if(($key = array_search($progId, $projet['programes']['view'])) !== false) {
//                      unset($projet['programes']['view'][$key]);
//              }
              Yii::$app->session->set('data_extension',$data_extension);
              
            }
            //remove select option extension
            if(Yii::$app->request->post()['type'] == 'remove-selected'){
              $name = Yii::$app->request->post()['extension'];
              
              if(Yii::$app->session->get('data_extension'))
                $data_extension = Yii::$app->session->get('data_extension');
              //else $data_extension = [];
                unset($data_extension[$name]);

              Yii::$app->session->set('data_extension',$data_extension);
              
            }
        }
        
            

        
        
        $theProgram = [];
        $pro = '';
        if($theEntry->model->exclusives != ''){
            $pro = explode(',', $theEntry->model->exclusives);
            
            foreach ($pro as $p) {
                if(\app\modules\exclusives\api\Catalog::get($p))
                    $theProgram[] = \app\modules\exclusives\api\Catalog::get($p);
            }
        }
        $allCountries = Country::find()->select(['code', 'dial_code', 'name_fr'])->orderBy('name_fr')->asArray()->all();
        $allDialCodes = Country::find()->select(['code', 'CONCAT(name_fr, " (+", dial_code, ")") AS xcode'])->where('dial_code!=""')->orderBy('name_fr')->asArray()->all();
        $model = new DevisForm;
        $model->scenario = 'booking';
        if(IS_MOBILE){
            $model = new DevisFormMobile;
            $model->scenario = 'mobileBooking';
        }
        $model->extension = Yii::$app->session->get('data_extension');
        $model->country = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';
        $model->countryCallingCode = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';

        if ($model->load($_POST) && $model->validate()) {
            
                $model->tourName = $theEntry->title;
                $model->tourUrl = 'https://www.amica-travel.com/'.$theEntry->slug;
                if(IS_MOBILE){
                



                $model->fname = preg_split("/\s+(?=\S*+$)/", $model->fullName)[0];
                $model->lname = '';
                if (isset(preg_split("/\s+(?=\S*+$)/", $model->fullName)[1])) {
                    $model->lname = preg_split("/\s+(?=\S*+$)/", $model->fullName)[1];
                }
                if (!$model->has_date) {
                    $model->departureDate = null;
                    $model->tourLength = null;
                }
                $contact = '';
                if ($model->contactEmail) $contact .= $model->email;
                if ($model->contactEmail && $model->contactPhone) $contact .= ' ,';
                if ($model->contactPhone) $contact .= $model->phone;
                
                
$data2 = <<<'TXT'
Vos coordonnées:   
   
Tour name: {{ tourUrl : $tourUrl }}  
Extension(s): {{ extension : $extension }}
Votre nom et prénom: {{ prefix : $prefix }} {{ fullname : $fullName }}   
Votre Mail: {{ email : $email }} 
Date de naissance: {{ age : $age }}
Votre pays: {{ country : $country }}
Département, Votre ville: {{ region : $region }} {{ ville : $ville }}      

votre projet:

Comment préférez-vous communiquer avec nous?: {{ contact : $contact }}
Date d’arrivée sur place: {{ departureDate : $departureDate }}
Durée: {{ tourLength : $tourLength }}
Votre message: {{ message : $message }}
Avez-vous déjà acheté votre (vos) billet(s) d’avion internationaux aller-retour ?: {{ howTicket : $howTicket }}
Budget par personne (budget total, incluant les vols): {{ budget : $budget }}
Le petit déjeuner est généralement déjà inclus dans le prix de l’hébergement. Souhaitez-vous d’autres repas ? : {{ lepetit : $lepetit }}
Vous partez: {{ typeGo : $typeGo }}
Adulte(s): {{ numberOfTravelers12 : $numberOfTravelers12 }}
Enfant(s): {{ numberOfTravelers2 : $numberOfTravelers2 }}
BéBé: {{ numberOfTravelers1 : $numberOfTravelers1 }}
Détails d'âges : {{ agesOfTravelers12 : $agesOfTravelers12 }}
Quel(s) types d'hébergement préférez-vous  pour ce voyage ?: {{ interest : $interest }}
Combien de chambres souhaitez-vous:
Chambre double avec un grand lit: {{ hotelRoomDbl : $hotelRoomDbl }}
Chambre double avec 2 petit lits: {{ hotelRoomTwn : $hotelRoomTwn }}
Chambre pour 3 personnes: {{ hotelRoomTrp : $hotelRoomTrp }}
Chambre individuelle: {{ hotelRoomSgl : $hotelRoomSgl }}

Vos centres d’intérêts

Pouvez-vous nous raconter votre dernier voyage long-courrier ? (destination, type de voyage, expériences, ce que vous avez aimé, ...) {{ howMessage : $howMessage }}
Vos loisirs et passe-temps préférés (ce que vous aimez, ce que vous n’aimez pas…) : {{ howHobby : $howHobby }}
Si vous êtes recommandé(e) par un ancien client d'Amica, merci de préciser son nom et prénom: {{ reference : $reference }}
Newsletters: {{ newsletter : $newsletter }} 
TXT;
                $data2 = str_replace([
                    '$tourUrl', '$extension', '$prefix', '$departureDate', '$tourLength', '$typeGo', '$numberOfTravelers12', '$numberOfTravelers2', '$interest', '$contact', '$fullName', '$email', '$message', '$agesOfTravelers12', '$hotelRoomDbl', '$hotelRoomTwn', '$hotelRoomTrp', '$hotelRoomSgl', '$budget', '$howTraveler', '$howMessage', '$howHobby', '$howTicket', '$job', '$lepetit', '$country', '$region', '$ville', '$numberOfTravelers1', '$reference', '$newsletter', '$age'
                ], [
                    '<a href="' . $model->tourUrl . '">' . $model->tourName . '</a>', implode(', ', (array)$model->extension), $model->prefix ,$model->departureDate, $model->tourLength, $model->typeGo, $model->numberOfTravelers12, $model->numberOfTravelers2, implode(', ', (array)$model->interest), $contact, $model->fullName, $model->email, $model->message, $model->agesOfTravelers12, $model->hotelRoomDbl, $model->hotelRoomTwn, $model->hotelRoomTrp, $model->hotelRoomSgl, $model->budget, $model->howTraveler, $model->howMessage, $model->howHobby, $model->howTicket, $model->job, $model->lepetit, $model->country, $model->region, $model->ville, $model->numberOfTravelers1, $model->reference, $model->newsletter, $model->age
                ], $data2);
                
                
                $this->saveInquiry($model, 'Form-booking-mobile', $data2);
                if ($model->newsletter) {
                    $data = [
                        'fname' => $model->fname,
                        'lname' => $model->lname,
                        'codecontry' => $model->country,
                        'source' => 'newsletter',
                        'sex' => $model->prefix,
                        'newsletter_insc' => date('d/m/Y'),
                        'statut' => 'prospect',
                        'birmanie' => false,
                        'vietnam' => false,
                        'cambodia' => false,
                        'laos' => false
                    ];

                    $this->addContactToMailjet($model->email, '1690435', $data);
                }
                

            }else{
               
            // Save db
          $data2 = <<<'TXT'
Vos coordonnées:
 
Tour name: {{ tourUrl : $tourUrl }}  
Extension(s): {{ extension : $extension }}
Votre nom et prénom: {{ prefix : $prefix }} {{ fname : $fname }} {{ lname : $lname }} 
Votre adresse mail: {{ email : $email }} Date de naissance: {{ age : $age }}
Votre pays: {{ country : $country }}
Département, Votre ville: {{ region : $region }} {{ ville : $ville }}

votre projet:

Date d'arrivée approximative: {{ departureDate : $departureDate }}
Date de retour: {{ deretourDate : $deretourDate }}
Durée du voyage: {{ tourLength : $tourLength }}
Avez-vous déjà acheté votre (vos) billet(s) d’avion internationaux aller-retour ?: {{ howTicket : $howTicket }}
En savoir plus : {{ ticketDetail : $ticketDetail }}
Souhaitez-vous être accompagné pour l'achat de vos billets internationnaux ? {{ helpTicket : $helpTicket }}
Les participants: + de 12 ans {{ numberOfTravelers12 : $numberOfTravelers12 }} détails d'âges: {{ agesOfTravelers12 : $agesOfTravelers12 }}
- de 12 ans {{ numberOfTravelers2 : $numberOfTravelers2 }} 
- de 2 ans {{ numberOfTravelers0 : $numberOfTravelers0 }}
La forme physique des participants : {{ howTraveler : $howTraveler }}
Quel(s) type(s) d’hébergement aimeriez-vous pour ce voyage: {{ hotelTypes : $hotelTypes }}
Combien de chambres souhaitez-vous:
Chambre double avec un grand lit: {{ hotelRoomDbl : $hotelRoomDbl }}
Chambre double avec 2 petit lits: {{ hotelRoomTwn : $hotelRoomTwn }}
Chambre pour 3 personnes: {{ hotelRoomTrp : $hotelRoomTrp }}
Chambre individuelle: {{ hotelRoomSgl : $hotelRoomSgl }}
Repas: {{ mealsIncluded : $mealsIncluded }}
Budget par personne: {{ budget : $budget }}

pour mieux vous connaitre:

Décrivez votre projet, votre vision du voyage et de quelle façon vous souhaitez découvrir notre pays: {{ message : $message }}
Pouvez-vous nous raconter votre dernier voyage long-courrier ? (destination, type de voyage, expériences, ce que vous avez aimé, ...) {{ howMessage : $howMessage }}
Vos loisirs et passe-temps préférés (ce que vous aimez, ce que vous n’aimez pas…) : {{ howHobby : $howHobby }}
Nous vous rappelons gratuitement pour mieux comprendre votre projet: {{ callback : $callback }}

TXT;
               $datacallback = <<<'TXT'
Votre numéro de téléphone: {{ countryCallingCode : $CallingCode }} {{ phone : $phone }}
Date / heure pour le RDV: {{ callDate : $callDate }} {{ callTime : $callTime }}

TXT;
               $datalast = <<<'TXT'
Si vous êtes recommandé(e) par un ancien client d'Amica, merci de préciser son nom et prénom: {{ reference : $reference }}   
Newsletters: {{ newsletter : $newsletter }}    
TXT;

      if($model->callback == 'Oui'){
                $data2 .= $datacallback;
                $data2 .= $datalast;
                $data2 = str_replace([
                    '$tourUrl', '$extension', '$prefix', '$fname', '$lname', '$email', '$country', '$region', '$ville', '$departureDate', '$deretourDate', '$tourLength', '$numberOfTravelers12', '$numberOfTravelers2', '$numberOfTravelers0', '$agesOfTravelers12', '$message', '$hotelTypes', '$hotelRoomDbl', '$hotelRoomTwn', '$hotelRoomTrp', '$hotelRoomSgl', '$mealsIncluded', '$budget', '$callback', '$CallingCode', '$phone', '$callDate', '$callTime','$newsletter', '$whyCountry', '$howTraveler', '$howMessage', '$howHobby', '$howTicket', '$ticketDetail', '$helpTicket','$job', '$reference', '$age'
                    ], [
                    '<a href="'.$model->tourUrl.'">'.$model->tourName.'</a>', implode(', ',(array)$model->extension), $model->prefix, $model->fname, $model->lname, $model->email, $model->country, $model->region, $model->ville, $model->departureDate, $model->deretourDate, $model->tourLength, $model->numberOfTravelers12, $model->numberOfTravelers2, $model->numberOfTravelers0, $model->agesOfTravelers12, $model->message, implode(', ', (array)$model->hotelTypes), $model->hotelRoomDbl, $model->hotelRoomTwn, $model->hotelRoomTrp, $model->hotelRoomSgl, $model->mealsIncluded, $model->budget, $model->callback, $model->countryCallingCode, $model->phone, $model->callDate, $model->callTime, $model->newsletter == 1 ? 'Oui' : 'Non', $model->whyCountry, $model->howTraveler, $model->howMessage, $model->howHobby, $model->howTicket, $model->ticketDetail, $model->helpTicket, $model->job, $model->reference, $model->age
                    ], $data2);
                }else{
                    
                 $data2 .= $datalast;
                 
                 $data2 = str_replace([
                    '$tourUrl', '$extension', '$prefix', '$fname', '$lname', '$email', '$country', '$region', '$ville', '$departureDate', '$deretourDate', '$tourLength', '$numberOfTravelers12', '$numberOfTravelers2', '$numberOfTravelers0', '$agesOfTravelers12', '$message', '$hotelTypes', '$hotelRoomDbl', '$hotelRoomTwn', '$hotelRoomTrp', '$hotelRoomSgl', '$mealsIncluded', '$budget', '$callback', '$newsletter', '$whyCountry', '$howTraveler', '$howMessage', '$howHobby', '$howTicket', '$ticketDetail', '$helpTicket', '$job', '$reference', '$age'
                    ], [
                    '<a href="'.$model->tourUrl.'">'.$model->tourName.'</a>', implode(', ',(array)$model->extension), $model->prefix, $model->fname, $model->lname, $model->email, $model->country, $model->region, $model->ville, $model->departureDate, $model->deretourDate, $model->tourLength, $model->numberOfTravelers12, $model->numberOfTravelers2, $model->numberOfTravelers0, $model->agesOfTravelers12, $model->message, implode(', ', (array)$model->hotelTypes), $model->hotelRoomDbl, $model->hotelRoomTwn, $model->hotelRoomTrp, $model->hotelRoomSgl, $model->mealsIncluded, $model->budget, $model->callback, $model->newsletter == 1 ? 'Oui' : 'Non', $model->whyCountry, $model->howTraveler, $model->howMessage, $model->howHobby, $model->howTicket, $model->ticketDetail, $model->helpTicket, $model->job, $model->reference, $model->age
                    ], $data2);
                }
                        
                $this->saveInquiry($model, 'Form-booking', $data2);
                if($model->newsletter){
                    $data = [
                    'firstname' => $model->fname,
                    'lastname' => $model->lname,
                    'codecontry' => $model->country,
                    'source' => 'newsletter',
                    'sex' => $model->prefix,
                    'newsletter_insc' => date('d/m/Y'),
                    'statut' => 'prospect',
                    'birmanie' => false,
                    'vietnam' => false,
                    'cambodia' => false,
                    'laos' => false
                    ];

                    $this->addContactToMailjet($model->email, '1690435', $data);
                }
            }
            // Email me
            $this->notifyAmica('Devis from ' . $model->email, '//page2016/email_template', ['data2' => $data2]);
            $this->confirmAmica($model->email);
            // Redir
            return Yii::$app->response->redirect(DIR.'merci?from=booking&id='.$this->id_inquiry);
        }
        $location = \app\modules\libraries\api\Catalog::items(['where'=>['in','category_id',[3, 4, 5, 6]],'pagination' => ['pageSize'=>0]]);
           
        $location = \yii\helpers\ArrayHelper::map($location, 'slug','title');
        Yii::$app->session->set('the-programes-tours-single', $theProgram); 
        Yii::$app->session->set('location', $location);    
        return $this->render(IS_MOBILE ? '//page2016/mobile/devis-booking-mobile' : '//page2016/devis-booking',[
                    'model' => $model,
                    'allCountries' => $allCountries,
                    'allDialCodes' => $allDialCodes,
                    'theEntry'=> $theEntry,
                    'theProgram' => $theProgram,
        ]);
    }

//    public function actionContactBooking(){
//        $this->layout = 'main-form';
//        if (IS_MOBILE){
//            $this->layout = 'mobile-form';
//        }
//        $formName = 'Form-contactbc';
//        $from = 'contact_bc';
//        if(SEG2=='secrets-ailleurs'){
//            $theEntry = \app\modules\exclusives\api\Catalog::get(preg_replace('/\/form$/', '', URI));
//            $formName = 'Form-contactsa';
//            $from = 'contact_sa';
//        } else 
//            $theEntry = \app\modules\programmes\api\Catalog::get(preg_replace('/\/form$/', '', URI));
//        $this->entry = $theEntry;
//        $this->getSeo($theEntry->model->seo);
//        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
//        $allCountries = Country::find()->select(['code', 'dial_code', 'name_fr'])->orderBy('name_fr')->asArray()->all();
//        $allDialCodes = Country::find()->select(['code', 'CONCAT(name_fr, " (+", dial_code, ")") AS xcode'])->where('dial_code!=""')->orderBy('name_fr')->asArray()->all();
//
//        $model = new ContactForm;
//        $model->scenario = 'contactce';
//        $model->country = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';
//        $model->countryCallingCode = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';
//
//        if ($model->load($_POST) && $model->validate()) {
//            if(IS_MOBILE){
//                    $model->fname = preg_split("/\s+(?=\S*+$)/", $model->fullName)[0];
//                    $model->lname = '';
//                    if (isset(preg_split("/\s+(?=\S*+$)/", $model->fullName)[1])) {
//                        $model->lname = preg_split("/\s+(?=\S*+$)/", $model->fullName)[1];
//                    }
//$data2 =<<<'TXT'
//Tour name: {{ tourUrl : $tourUrl }}    
//Votre nom & prénom: {{ fullname : $fullName }}
//Votre adresse mail: {{ email : $email }}
//Votre pays: {{ country : $country }}
//Département, Votre ville: {{ region : $region }} {{ ville : $ville }}
//Votre message: {{ message : $message }}
//Souhaitez-vous recevoir une proposition de programme avec devis personnalisé sur d'autres régions du pays ?: {{ question : $question }}
//Si vous êtes recommandé(e) par un ancien client d'Amica, merci de préciser son nom et prénom: {{ reference : $reference }}  
//Newsletter: {{ newsletter : $newsletter }}
//TXT;
//                    $data2 = str_replace([
//                    '$tourUrl', '$prefix', '$fullName', '$email', '$country', '$region', '$ville','$message', '$reference', '$newsletter'
//                    ], [
//                    '<a href="https://www.amica-travel.com/' . $theEntry->slug . '">' . $theEntry->title . '</a>', $model->prefix, $model->fullName, $model->email, $model->country, $model->region, $model->ville, $model->message, $model->reference, $model->newsletter
//                    ], $data2);
//                    // Save db
//                    $this->saveInquiry($model, 'Form-contact-mobile', $data2);    
//                    
//                    if ($model->newsletter) {
//                $data = [
//                    'fname' => $model->fname,
//                    'lname' => $model->lname,
//                    'codecontry' => $model->country,
//                    'source' => 'newsletter',
//                    'sex' => $model->prefix,
//                    'newsletter_insc' => date('d/m/Y'),
//                    'statut' => 'prospect',
//                    'birmanie' => false,
//                    'vietnam' => false,
//                    'cambodia' => false,
//                    'laos' => false
//                ];
//
//                $this->addContactToMailjet($model->email, '1688900', $data);
//            }
//
//
//                } else {
//            $data2 = <<<'TXT'
//Tour name: {{ tourUrl : $tourUrl }}   
//Votre nom et prénom: {{ prefix : $prefix }} {{ fname : $fname }} {{ lname : $lname }}
//Votre adresse mail: {{ email : $email }}
//Votre pays: {{ country : $country }}
//Département, Votre ville: {{ region : $region }} {{ ville : $ville }}
//Votre message: {{ message : $message }}
//Souhaitez-vous recevoir une proposition de programme avec devis personnalisé sur d'autres régions du pays ?: {{ question : $question }}
//Si vous êtes recommandé(e) par un ancien client d'Amica, merci de préciser son nom et prénom: {{ reference : $reference }}
//TXT;
//                $data2 = str_replace([
//                    '$tourUrl', '$prefix', '$fname', '$lname', '$email', '$country', '$region', '$ville', '$message', '$question', '$reference'
//                    ], [
//                    '<a href="https://www.amica-travel.com/'.$theEntry->slug.'">'.$theEntry->title.'</a>', $model->prefix, $model->fname, $model->lname, $model->email, $model->country, $model->region, $model->ville, $model->message, $model->question, $model->reference
//                    ], $data2);
//                // Save db
//                $this->saveInquiry($model, $formName, $data2);
//            if($model->newsletter){
//                $data = [
//                        'firstname' => $model->fname,
//                        'lastname' => $model->lname,
//                        'codecontry' => $model->country,
//                        'source' => 'newsletter',
//                        'sex' => $model->prefix,
//                        'newsletter_insc' => date('d/m/Y'),
//                        'statut' => 'prospect',
//                        'birmanie' => false,
//                        'vietnam' => false,
//                        'cambodia' => false,
//                        'laos' => false
//                    ];
//                $this->addContactToMailjet($model->email, '1688900', $data);
//             }
//         }
//            // Email me
//            $this->notifyAmica('Contact from ' . $model->email, '//page2016/email_template', ['data2' => $data2]);
//            $this->confirmAmica($model->email);
//            // Redir
//
//            return Yii::$app->response->redirect(DIR.'merci?from='.$from.'&id='.$this->id_inquiry);
//        }
//        return $this->render(IS_MOBILE ? '//page2016/mobile/contact-booking-mobile' : '//page2016/contact-booking',[
//                    'model' => $model,
//                    'allCountries' => $allCountries,
//                    'allDialCodes' => $allDialCodes,
//                    'theEntry'=> $theEntry,
//        ]);
//    }
    
    public function actionContactBooking()
    {
        $this->layout = 'main-form';
        if (IS_MOBILE){
            $this->layout = 'mobile-form';
        }
        
        
        $formName = 'Form-contactbc';
        $from = 'contact_bc';
        if (SEG2 == 'secrets-ailleurs') {
            $theEntry = \app\modules\exclusives\api\Catalog::get(preg_replace('/\/form$/', '', URI));
            $formName = 'Form-contactsa';
            $from = 'contact_sa';
        } else
            $theEntry = \app\modules\programmes\api\Catalog::get(preg_replace('/\/form$/', '', URI));
        $this->entry = $theEntry;
        $this->getSeo($theEntry->model->seo);
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        $allCountries = Country::find()->select(['code', 'dial_code', 'name_fr'])->orderBy('name_fr')->asArray()->all();
        $allDialCodes = Country::find()->select(['code', 'CONCAT(name_fr, " (+", dial_code, ")") AS xcode'])->where('dial_code!=""')->orderBy('name_fr')->asArray()->all();

        $model = new ContactForm;
        $model->scenario = 'contactce';
        if(IS_MOBILE){
            $model = new ContactFormMobile;
            $model->scenario = 'contactce_mobile';
        }
        
        $model->country = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';
        $model->countryCallingCode = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';

        if ($model->load($_POST) && $model->validate()) {
            if(IS_MOBILE){
                    $model->fname = preg_split("/\s+(?=\S*+$)/", $model->fullName)[0];
                    $model->lname = '';
                    if (isset(preg_split("/\s+(?=\S*+$)/", $model->fullName)[1])) {
                        $model->lname = preg_split("/\s+(?=\S*+$)/", $model->fullName)[1];
                    }
$data2 =<<<'TXT'
Tour name: {{ tourUrl : $tourUrl }}    
Votre nom & prénom: {{ prefix : $prefix }} {{ fullname : $fullName }}
Votre adresse mail: {{ email : $email }}
Date de naissance: {{ age : $age }}
Votre pays: {{ country : $country }}
Département, Votre ville: {{ region : $region }} {{ ville : $ville }}
Votre message: {{ message : $message }}
Souhaitez-vous recevoir une proposition de programme avec devis personnalisé sur d'autres régions du pays ?: {{ question : $question }}
Si vous êtes recommandé(e) par un ancien client d'Amica, merci de préciser son nom et prénom: {{ reference : $reference }}  
Newsletter: {{ newsletter : $newsletter }}
TXT;
                    $data2 = str_replace([
                    '$tourUrl', '$prefix', '$fullName', '$email', '$country', '$region', '$ville','$message', '$reference', '$newsletter', '$age'
                    ], [
                    '<a href="https://www.amica-travel.com/' . $theEntry->slug . '">' . $theEntry->title . '</a>', $model->prefix, $model->fullName, $model->email, $model->country, $model->region, $model->ville, $model->message, $model->reference, $model->newsletter, $model->age
                    ], $data2);
                    // Save db
                    $this->saveInquiry($model, 'Form-contact-mobile', $data2);    
                    
                    if ($model->newsletter) {
                $data = [
                    'fname' => $model->fname,
                    'lname' => $model->lname,
                    'codecontry' => $model->country,
                    'source' => 'newsletter',
                    'sex' => $model->prefix,
                    'newsletter_insc' => date('d/m/Y'),
                    'statut' => 'prospect',
                    'birmanie' => false,
                    'vietnam' => false,
                    'cambodia' => false,
                    'laos' => false
                ];
                $this->addContactToMailjet($model->email, '1688900', $data);
            }
                } else {
            $data2 = <<<'TXT'
Tour name: {{ tourUrl : $tourUrl }}   
Votre nom et prénom: {{ prefix : $prefix }} {{ fname : $fname }} { lname : $lname }}
Votre adresse mail: {{ email : $email }} Date de naissance: {{ age : $age }}
Votre pays: {{ country : $country }}
Département, Votre ville: {{ region : $region }} {{ ville : $ville }}
Votre message: {{ message : $message }}
Souhaitez-vous recevoir une proposition de programme avec devis personnalisé sur d'autres régions du pays ?: {{ question : $question }}
Si vous êtes recommandé(e) par un ancien client d'Amica, merci de préciser son nom et prénom: {{ reference : $reference }}    
Newsletter: {{ newsletter : $newsletter }}
TXT;
            $data2 = str_replace([
                '$tourUrl', '$prefix', '$fname', '$lname', '$email', '$country', '$region', '$ville', '$message', '$question', '$reference', '$newsletter', '$age'
            ], [
                '<a href="https://www.amica-travel.com/' . $theEntry->slug . '">' . $theEntry->title . '</a>', $model->prefix, $model->fname, $model->lname, $model->email, $model->country, $model->region, $model->ville, $model->message, $model->question, $model->reference, $model->newsletter, $model->age
            ], $data2);
            // Save db
            $this->saveInquiry($model, $formName, $data2);
            if ($model->newsletter) {
                $data = [
                    'firstname' => $model->fname,
                    'lastname' => $model->lname,
                    'codecontry' => $model->country,
                    'source' => 'newsletter',
                    'sex' => $model->prefix,
                    'newsletter_insc' => date('d/m/Y'),
                    'statut' => 'prospect',
                    'birmanie' => false,
                    'vietnam' => false,
                    'cambodia' => false,
                    'laos' => false
                ];
                $this->addContactToMailjet($model->email, '1688900', $data);
            }
                }
            // Email me
            $this->notifyAmica('Contact from ' . $model->email, '//page2016/email_template', ['data2' => $data2]);
            $this->confirmAmica($model->email);
            // Redir

            return Yii::$app->response->redirect(DIR . 'merci?from=' . $from . '&id=' . $this->id_inquiry);
        }
        return $this->render(IS_MOBILE ? '//page2016/mobile/contact-booking-mobile' : '//page2016/contact-booking', [
            'model' => $model,
            'allCountries' => $allCountries,
            'allDialCodes' => $allDialCodes,
            'theEntry' => $theEntry,
        ]);
    }
    
    
      public function actionNosDestinations() {
        $theEntry = \yii\easyii\modules\page\api\Page::get('destinations');
        if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
        $this->getSeo($theEntry->model->seo);
        
      
        $countries = \app\modules\destinations\models\Category::find()->roots()->orderBy('order_num DESC')->all();
        
        // 3 tour secrets on top
        $modules_exclusive = \app\modules\modulepage\api\Catalog::get('decouvrir-nos-secrets-dailleurs'); // id 22
                    
        $voyage = \app\modules\programmes\models\Category::find()->roots()->orderBy('order_num ASC')->all();
        
        
        // BLOGS
        $arrBlog = array();
        if(!Yii::$app->cache->get('cache-blog-destinations-002')){
            if(isset(\app\modules\modulepage\api\Catalog::get('destinations/blog')->data->blogs)) {
                $dataBlogSelected = \app\modules\modulepage\api\Catalog::get('destinations/blog')->data->blogs;
                foreach ($dataBlogSelected as $keyBlog => $valueBlog) {
                    $arrBlog[] = $this->getDataPost($valueBlog);
                    
                    foreach ($arrBlog as $key2 => $value2) {
                        $categoryId = $value2['categories'][0];
                        $featuredMediaId = $value2['featured_media'];

                        $titleCategory = $this->getCategoryName($categoryId)['name'];
                        $arrBlog[$keyBlog]['cat_name'] = $titleCategory;
                        $arrBlog[$keyBlog]['alt_text'] = $this->getFeatureImage($arrBlog[$keyBlog]['featured_media'])['alt_text'];
                        $featuredMediaData = $this->getFeatureImage($featuredMediaId)['media_details']['sizes']['barouk_list-thumb'];
                        $arrBlog[$keyBlog]['src'] = '/timthumb.php?src=' . $featuredMediaData['source_url'] . '&w=300&h=200&zc=1&q=80';
                    }
                }
            }
            Yii::$app->cache->set('cache-blog-destinations-002', $arrBlog);
        }else{
            $arrBlog = Yii::$app->cache->get('cache-blog-destinations-002');
            
        }    

        return $this->render(IS_MOBILE ? '//page2016/mobile/nos-destinations' : '//page2016/nos-destinations', [
            'theEntry' => $theEntry,
            'countries' => $countries,
            'modules_exclusive' => $modules_exclusive,
            'voyage' => $voyage,
            'arrBlog' => $arrBlog,
                ]);
    }

    public function getMoreVisiterMobile($locations, $totalCount){
        $visiter = $locations->items(['pagination' => ['pageSize' => 4]]);
            $showMore = true;
            if(($totalCount/4) <= Yii::$app->request->get('page') ) $showMore = false;
            $html = '<div class="tour-item"><a data-ajax="false" href="{{slug}}"><img alt="{{description-img}}" src="/thumb/660/440/1/80{{image}}" data-srcset="/thumb/660/440/1/80{{image}}" data-sizes="auto" class="banner-img lazyload" /><div class="text-on-image"><h3 class="tt-title tt-fontsize-32 tt-latolatin-semibold tt-color-white tt-uppercase">{{title}}</h3><p class="tt-fontsize-28 tt-latolatin-regular tt-color-white">{{sub-title}}</p></div></a></div>';
            $content = '';
            foreach ($visiter as $key => $value) :
                $v = '';
                if(isset($value->photosArray['banner'])){
                    $v = $value->photosArray['banner'][0];
                } 
                $sumT =  $value->model->sub_title ? $value->model->sub_title : $value->title;
                $content .= str_replace(['{{slug}}', '{{description-img}}', '{{image}}', '{{sub-title}}', '{{title}}'], [DIR.$value->slug, $v->description, $v->image, $sumT, $value->model->title], $html);  
            endforeach;
            return \yii\helpers\Json::encode(['content' => $content, 'showMore' => $showMore ]);
    }

    public function actionNosDestinationsType() {

        $checkItem = \app\modules\destinations\api\Catalog::get(URI);
        if($checkItem){
            if(strpos($checkItem->cat->slug, 'visiter' ) !== false){
                return Yii::$app->runAction('amica-fr/nos-destinations-detaile');
            }
            if(strpos($checkItem->cat->slug, 'envies')){
                return Yii::$app->runAction('amica-fr/nos-destinations-envies');
            }
        }
        if (Yii::$app->request->isAjax) {
            if(Yii::$app->request->post()['type'] == 'des'){
                $locationAjax = \app\modules\destinations\api\Catalog::cat(SEG1.'/visiter');
                return $this->renderPartial('//page2016/ajax/des-ajax', ['locations' => $locationAjax, 'enviesLib' => $this->getEnviesLib()]);

            }
             if(Yii::$app->request->post()['type'] == 'prog'){
                 $programes = \app\modules\programmes\api\Catalog::items(['filters' => ['country' => 'Vietnam']]);
                return $this->renderPartial('//page2016/ajax/prog-ajax', ['programes' => $programes]);

            }
        }
        $theEntry = \app\modules\destinations\api\Catalog::cat(URI);
        $this->entry = $theEntry;
         // var_dump($theEntry->model->photos);exit;
        if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
        $this->getSeo($theEntry->model->seo);
        //get data info-pratiques VN
        $infos = \app\modules\destinations\models\Category::findOne(['slug'=>SEG1.'/informations-pratiques']);
        //lay cac muc lon info-pratic
        if($infos) {
            $infosChild = $infos->children()->all();
        } else $infosChild = [];
        //get data guide
        $guide = \app\modules\destinations\models\Category::findOne(['slug'=>SEG1.'/guide']);
        //lay cac muc lon guide
        $guideChild  = [];
        if($guide)
             $guideChild = $guide->children(1)->andWhere(['status' => 1])->all();
        // lay du lieu cua site a visiter VN
        $locations = \app\modules\destinations\api\Catalog::cat(SEG1.'/visiter');
         $pagi = new \yii\data\Pagination(['totalCount' => count($locations->items(['pagination' => ['pageSize' => 0]])), 'defaultPageSize' => 4]);
        if(SEG2 == 'visiter') $this->pagination = $pagi->pageCount;
        // lay du lieu cua cac exclusives VN
        //array tat ca cac dia danh o VN
        $locationVietnam = \app\modules\libraries\api\Catalog::cat(SEG1)->items();

        $locationVietnam = \yii\helpers\ArrayHelper::getColumn($locationVietnam, 'slug');
        // exclusives Viet Nam
        $exclusives = \app\modules\exclusives\api\Catalog::items(['filters' => ['locations' => ['IN', $locationVietnam]]]);
        //programes viet nam

        $programes = \app\modules\programmes\api\Catalog::items(['filters' => ['country' => ucfirst(SEG1)]]);
        Yii::$app->session->set('countVnProg', count($programes));
        $itemVoyage =  \app\modules\destinations\api\Catalog::get('idees-de-voyage-'.SEG2);

       
        return $this->render('//page2016/nos-destinations-type',
                [
                    'theEntry' => $theEntry,
                    'infos' => $infos,
                    'infosChild' => $infosChild,
                    'guide' => $guide,
                    'guideChild' => $guideChild,
                    'locations' => $locations, 
                    'exclusives' => $exclusives, 
                    'programes' => $programes,
                    'itemVoyage' => $itemVoyage,
                    'enviesLib' => $this->getEnviesLib()
                ]
            );
    }

    public function actionNosDestinationsCountry(){
        // Yii::$app->cache->flush();
        $theEntry = \app\modules\destinations\api\Catalog::cat(URI);
        if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
        $this->entry = $theEntry;
        $this->getSeo($theEntry->model->seo);
        $countVnTours =  $this->actionGetNumberProg(SEG1);
        $tourType = \app\modules\destinations\api\Catalog::cat(SEG1.'/itineraire');
        $infos = \app\modules\destinations\models\Category::findOne(['slug' => SEG1.'/informations-pratiques'])->children(1)->andWhere('on_top != "" AND on_top != "0"')->orderBy('on_top ASC')->all();
        $secretType = \app\modules\destinations\api\Catalog::cat(SEG1.'/secrets-ailleurs');

        $dataTemoignageItems = array();
        $arrBlog = array();

        // Temoignages
        if(!Yii::$app->cache->get('cache-testi-'.SEG1)) {
            $dataTemoignageSelected = \app\modules\modulepage\api\Catalog::get(SEG1.'/temoignage');
            if(isset($dataTemoignageSelected->data->temoignages)) {
                $dataTemoignageSelected = $dataTemoignageSelected->data->temoignages;
                $dataTemoignageItems = \app\modules\whoarewe\models\Item::find()->where(['in', 'item_id', $dataTemoignageSelected])->with(['photos'=> function (\yii\db\ActiveQuery $query) {
            $query->andWhere(['type' => 'summary']);
        }])->asArray()->all();
                $dataTemoignageItems = ArrayHelper::map($dataTemoignageItems, 'item_id', function($element){
                    $element['data'] = json_decode($element['data']);
                    return $element;
                });
                uksort($dataTemoignageItems, function($key1, $key2) use ($dataTemoignageSelected) {
                    return (array_search($key1, $dataTemoignageSelected) > array_search($key2, $dataTemoignageSelected));
                });
                Yii::$app->cache->set('cache-testi-'.SEG1, $dataTemoignageItems);
            }
        } else
            $dataTemoignageItems = Yii::$app->cache->get('cache-testi-'.SEG1);
        // BLOGS
        if(isset(\app\modules\destinations\api\Catalog::get(SEG1.'/blog')->data->moduleblog[0])) {
            $rs = \app\modules\modulepage\api\Catalog::get(\app\modules\destinations\api\Catalog::get(SEG1.'/blog')->data->moduleblog[0]);
            $dataBlogSelected = $rs->data->blogs;

            foreach ($dataBlogSelected as $keyBlog => $valueBlog) {
                $arrBlog[] = $this->getDataPost($valueBlog);
                foreach ($arrBlog as $key2 => $value2) {
                    $categoryId = $value2['categories'][0];
                    $featuredMediaId = $value2['featured_media'];

                    $titleCategory = $this->getCategoryName($categoryId)['name'];
                    $arrBlog[$keyBlog]['cat_name'] = $titleCategory;
                    $featuredMediaData = $this->getFeatureImage($featuredMediaId)['media_details']['sizes']['barouk_list-thumb'];
                    $arrBlog[$keyBlog]['src'] = '/timthumb.php?src=' . $featuredMediaData['source_url'] . '&w=300&h=200&zc=1&q=80';
                }
            }
        }
        if(IS_MOBILE){
                $this->totalCount = $countVnTours;
                $this->arr_option_filter_mobile = [
                        'title_filter' => $theEntry->model->seo->h1 ,
                        'namefilter' => 'filter_voyage',
                        'uri' =>$theEntry->slug,
                        'country' => SEG1,
                        'type' => 'all',
                        'length' => '',
                        'switch_link' => 'filter_type_active',
                    ];
            }
        return $this->render(IS_MOBILE ? '//page2016/mobile/nos-destinations-country' : '//page2016/nos-destinations-country-'.SEG1,
            [
                'theEntry' => $theEntry,
                'countVnTours' => $countVnTours,
                'tourType' => $tourType,
                'secretType' => $secretType,
                'arrTemoignages' => $dataTemoignageItems,
                'arrBlog' => $arrBlog,
                'infos' => $infos
            ]
        );
    }

    public function actionNosDestinationsGuideType() {
       
        $theEntry = \app\modules\destinations\api\Catalog::cat(URI);
        if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
        $this->entry = $theEntry;
        $this->getSeo($theEntry->model->seo);
        $guides = $theEntry->children();
        // var_dump($guides);exit;
        $envies = \app\modules\destinations\api\Catalog::cat(SEG1.'/envies')->items(['pagination' => ['pageSize' => 3]]);
        $progs = \app\modules\programmes\api\Catalog::items([
                'where' => SEG1 == 'birmanie' ? [] : ['>=', 'on_top', '1'],
                'orderBy' => 'on_top ASC',
                'filters' => ['countries' => SEG1],
                'pagination' => ['pageSize' => 6]
            ]);
        $countTours =  $this->actionGetNumberProg(SEG1);
         $arrBlog = [];
        if(!Yii::$app->cache->get('cache-blog-guide-'.SEG1)) {
            $data = \app\modules\modulepage\api\Catalog::get(SEG1.'/guide/blog');
            if(isset($data->data->blogs)) {
                $dataBlogSelected = $data->data->blogs;
                foreach ($dataBlogSelected as $keyBlog => $valueBlog) {
                    $arrBlog[$keyBlog] = $this->getDataPost($valueBlog);
                    $titleCategory = $this->getCategoryName($arrBlog[$keyBlog]['categories'][0])['name'];
                    $arrBlog[$keyBlog]['cat_name'] = $titleCategory;


                    $featuredMediaData = $this->getFeatureImage($arrBlog[$keyBlog]['featured_media'])['media_details']['sizes']['barouk_list-thumb'];
                    $arrBlog[$keyBlog]['src'] = '/timthumb.php?src=' . $featuredMediaData['source_url'] . '&w=299&h=199&zc=1&q=80';
                }
                Yii::$app->cache->set('cache-blog-guide-'.SEG1, $arrBlog);
            }    
        } else{
            $arrBlog = Yii::$app->cache->get('cache-blog-guide-'.SEG1);
        }
        return $this->render(IS_MOBILE ? '//page2016/mobile/nos-destinations-guide-type' : '//page2016/nos-destinations-guide-type',
                [
                    'theEntry' => $theEntry,
                    'guides' => $guides, 
                    'envies' => $envies,
                    'progs' => $progs,
                    'countTours' => $countTours,
                    'arrBlog' => $arrBlog
                ]
            );
    }

    public function actionNosDestinationsVisiter() {
         $locations = \app\modules\destinations\api\Catalog::cat(SEG1.'/visiter'); 
        $totalCount  = count($locations->items(['pagination' => ['pageSize' => 0]]));
        if(Yii::$app->request->isAjax){
            if(IS_MOBILE) {
                return $this->getMoreVisiterMobile($locations, $totalCount);
            }
            $pagesize = Yii::$app->request->get('see-more');
        }else{
            $pagesize = 8;
        }
        $allRoot =  \app\modules\destinations\api\Catalog::cat(SEG1.'/visiter')->items(['pagination' => ['pageSize' => 0]]);
        $theEntry = \app\modules\destinations\api\Catalog::cat(URI);
        if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
        $this->entry = $theEntry;
        $this->getSeo($theEntry->model->seo);
        //get data info-pratiques VN
        $infos = \app\modules\destinations\models\Category::findOne(['slug'=>SEG1.'/informations-pratiques']);
        // lay du lieu cua site a visiter VN
        $locations = \app\modules\destinations\api\Catalog::cat(SEG1.'/visiter'); 
        
        if(Yii::$app->request->get('see-more') !== NULL){
            $pagesize = Yii::$app->request->get('see-more');
           // $visiter = $locations->items(['pagination' => ['pageSize' => $pagesize]]);
        }else{
            $pagesize = 4;
        }
            $visiter = $locations->items(['pagination' => ['pageSize' => $pagesize]]);

        return $this->render(IS_MOBILE ? '//page2016/mobile/nos-destinations-visiter' : '//page2016/nos-destinations-visiter',
                [
                    'theEntry' => $theEntry,
                    'locations' => $locations, 
                    'enviesLib' => $this->getEnviesLib(),
                    'visiter' => $visiter,
                    'pagesize' => $pagesize,
                    'totalCount' => $totalCount
                ]
            );
    }

    public function actionNosDestinationsRecherche() {
        $theEntry = \app\modules\destinations\api\Catalog::cat(URI);
        $this->entry = $theEntry;
        if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
        $this->getSeo($theEntry->model->seo);
        $this->countTour = count(\app\modules\programmes\api\Catalog::items([
            'filters' => ['countries' => SEG1],
            'pagination' => ['pageSize' => 0]
            ]));
        //for tour Type
        $type = Yii::$app->request->get('type');
        $typeNoChild = $typeChild = [];
        if(!$type || $type == 'all'){
            $filterType = [];
        } else {
            foreach (explode('-',$type) as $key => $value) {
                    if( $childrenType = \app\modules\programmes\models\Category::findOne($value)->children(1)->all())
                    $typeChild = ArrayHelper::getColumn($childrenType, 'category_id');
                    else {
                         $typeNoChild[] = intval($value);
                    }
            }
            $filterType = array_merge($typeChild, $typeNoChild);
        
        }
        $filterType = ['category_id' => $filterType];
        //for tour length
         $length = Yii::$app->request->get('length');
        if($length == 'all'){
            $length = '';
        }
        if(strpos($length, '-') !== false){
              $arrLen = explode('-',$length);
              asort($arrLen);
              if($arrLen[0]==1) $arrLen[0] = 0;
              if(count($arrLen) == 4){
                $filterLen = ['between', 'days', $arrLen[0], end($arrLen)];
              }
              if(count($arrLen) == 3){
                $filterLen = ['or',
                ['between', 'days', $arrLen[0], $arrLen[1]],
                ['>=','days', end($arrLen)]  
                ];
              }
              if(count($arrLen) == 2){
                $filterLen = ['between', 'days', $arrLen[0], $arrLen[1]];
              }
        } else $filterLen = ['>=','days', $length];
        //for country
        $filters = ['countries' => SEG1];
        
        $voyage = \app\modules\programmes\api\Catalog::items([
            'orderBy' => ['on_top_flag' => SORT_ASC, 'on_top' => SORT_ASC],
            'where' => ['and',
                $filterType,
                $length ? $filterLen : []
                ],
            'filters' => $filters,
            'pagination' => ['pageSize' => Yii::$app->request->get('view') == 'all' ? 0 : 12]
            ]);
            $allVoyage = \app\modules\programmes\api\Catalog::items([
                'orderBy' => ['on_top_flag' => SORT_ASC, 'on_top' => SORT_ASC],
            'where' => ['and',
                $filterType,
                $length ? $filterLen : []
                ],
            'filters' => $filters,
            'pagination' => ['pageSize' => 0]
            ]);
            $totalPage = count($allVoyage);
            $pagi = new \yii\data\Pagination(['totalCount' => $totalPage, 'defaultPageSize'=>12 ]);
            $this->pagination = $pagi->pageCount;
            Yii::$app->session->set('countVnProg', $totalPage);
        if (Yii::$app->request->isAjax) {
            
             if(Yii::$app->request->post()['type'] == 'prog'){
                return $this->renderPartial('//page2016/ajax/itineraire-ajax', ['programes' => $voyage, 'totalPage' => $totalPage]);
            }
        }

        return $this->render('//page2016/recherche-itineraire',
            ['theEntry' => $theEntry, 
            'programes' => $voyage,
            'totalPage' => $totalPage,
            'allVoyage' => $allVoyage
            ]);
    }
    
    // cac trang destination Ideel de voyage, Fomules exclusive, Info
   public function actionNosDestinationsCountryInfo(){
        $theEntry = \app\modules\destinations\api\Catalog::cat(URI);
        $this->entry = $theEntry;
        // var_dump($theEntry->model->photos);exit;
        if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
        $this->getSeo($theEntry->model->seo);
      
        $infos_all_childrent = $theEntry->children();
     
        
        // 6 tour On Top
        
        if(SEG1 == 'birmanie'){
            $voyage = \app\modules\programmes\api\Catalog::items([
                    
                    'orderBy' => 'on_top ASC',
                    'filters' => ['countries' => SEG1],
                    'pagination' => ['pageSize' => 6]
                ]);
        }else{
            $voyage = \app\modules\programmes\api\Catalog::items([
                    'where' => ['>=', 'on_top', '1'],
                    'orderBy' => 'on_top ASC',
                    'filters' => ['countries' => SEG1],
                    'pagination' => ['pageSize' => 6]
                ]);
        }    
        
        
        $this->countTour = $this->getAjaxFilter(['country'=>SEG1,'type'=>'','length'=>''])['totalCount'];
        if (Yii::$app->request->isAjax  && !IS_MOBILE) {
           
            $this->countTour = $this->getAjaxFilter(['country'=>SEG1,'type'=>'','length'=>''])['totalCount'];
            return $this->countTour;
            
        }  
        
        // BLOGS
        $arrBlog = array();
        if(!Yii::$app->cache->get('cache-blog-'.SEG1)){
            if(isset(\app\modules\modulepage\api\Catalog::get(SEG1.'/informations-pratiques/blog')->data->blogs)) {
                $dataBlogSelected = \app\modules\modulepage\api\Catalog::get(SEG1.'/informations-pratiques/blog')->data->blogs;
                foreach ($dataBlogSelected as $keyBlog => $valueBlog) {
                    $arrBlog[] = $this->getDataPost($valueBlog);
                    foreach ($arrBlog as $key2 => $value2) {
                        $categoryId = $value2['categories'][0];
                        $featuredMediaId = $value2['featured_media'];

                        $titleCategory = $this->getCategoryName($categoryId)['name'];
                        $arrBlog[$keyBlog]['cat_name'] = $titleCategory;
                        $featuredMediaData = $this->getFeatureImage($featuredMediaId)['media_details']['sizes']['barouk_list-thumb'];
                        $arrBlog[$keyBlog]['src'] = '/timthumb.php?src=' . $featuredMediaData['source_url'] . '&w=300&h=200&zc=1&q=80';
                    }
                }
            }
            Yii::$app->cache->set('cache-blog-'.SEG1, $arrBlog);
        }else{
            $arrBlog = Yii::$app->cache->get('cache-blog-'.SEG1);
            
        }    
        
       // var_dump($arrBlog[0]);exit;
        
         return $this->render(IS_MOBILE ? '//page2016/mobile/nos-destinations-country-informations-pratiques' : '//page2016/nos-destinations-country-informations-pratiques',
            [
                'theEntry' => $theEntry,

                'voyage' => $voyage,
                'arrBlog' => $arrBlog,
                'infos_all_childrent' => $infos_all_childrent,
            ]);
    }
    public function actionNosDestinationsCountryIdeel(){
        $theEntry = \app\modules\destinations\api\Catalog::cat(URI);
        $this->entry = $theEntry;
        if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
        $this->getSeo($theEntry->model->seo);


            $theEntries = $this->getAjaxFilter(['country'=>SEG1,'type'=>''])['voyage'];
            $totalCount = $this->getAjaxFilter(['country'=>SEG1,'type'=>''])['totalCount'];
            
            $numberFilter = $this->getAjaxFilter(['country'=>SEG1,'type'=>'']);
            if(IS_MOBILE){
                $this->totalCount = $totalCount;
                $this->arr_option_filter_mobile = [
                        'title_filter' => $theEntry->model->seo->h1 ,
                        'namefilter' => 'filter_voyage',
                        'uri' =>$theEntry->slug,
                        'country' => SEG1,
                        'type' => 'all',
                        'length' => '',
                        'switch_link' => 'filter_type_active',
                    ];
            }



        return $this->render(IS_MOBILE ? '//page2016/mobile/nos-destinations-country-ideel' : '//page2016/nos-destinations-country-ideel',

            [
                'theEntry' => $theEntry,
                'theEntries' => $theEntries,
                'totalCount' => $totalCount,
                'numberFilter' => $numberFilter,
            ]);
    }
    
    public function actionNosDestinationsCountryIdeelType(){
        
        $theCountryType = \app\modules\destinations\api\Catalog::items([
            'where' =>['slug'=>URI],
            
        ]);
        if (!$theCountryType) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
        
         $theEntry = \app\modules\programmes\api\Catalog::cat('voyage/'.SEG3);
        
      //  $this->entry = $theEntry;
         $this->entry = $theCountryType[0];
        
       // $this->getSeo($theEntry->model->seo);
        $this->getSeo($theCountryType[0]->model->seo);
        
        
       

            $theEntries = $this->getAjaxFilter(['country'=>SEG1,'type'=>$theEntry->model->category_id])['voyage'];
            $totalCount = $this->getAjaxFilter(['country'=>SEG1,'type'=>$theEntry->model->category_id])['totalCount'];
            
            $numberFilter = $this->getAjaxFilter(['country'=>SEG1,'type'=>$theEntry->model->category_id]);
            if(IS_MOBILE){
                $this->totalCount = $totalCount;
                $this->arr_option_filter_mobile = [
                        'title_filter' => $theCountryType[0]->model->seo->h1 ,
                        'namefilter' => 'filter_voyage',
                        'uri' =>$theCountryType[0]->slug,
                        'country' => SEG1,
                        'type' => $theEntry->model->category_id,
                        'length' => '',
                        'switch_link' => 'filter_type_active',
                    ];
            }

        return $this->render(IS_MOBILE ? '//page2016/mobile/nos-destinations-country-ideel-type' : '//page2016/nos-destinations-country-ideel-type',
            [
                'theCountryType' => $theCountryType,
                'theEntry' => $theEntry,
                'theEntries' => $theEntries,
                'totalCount' => $totalCount,
                'numberFilter' => $numberFilter,
            ]);
    }
    
    public function actionNosDestinationsCountryExclusive(){
        $theEntry = \app\modules\destinations\api\Catalog::cat(URI);
        
        
        $this->entry = $theEntry;
        if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');

        $this->getSeo($theEntry->model->seo);


            $theEntries = $this->getAjaxFilterExclusive(['country'=>SEG1,'type'=>''])['voyage'];
            
            $totalCount = $this->getAjaxFilterExclusive(['country'=>SEG1,'type'=>''])['totalCount'];
            
            //$numberFilter = $this->getAjaxFilterExclusive();
            $numberFilter = $this->getAjaxFilterExclusive(['country'=>SEG1,'type'=>'']);
            if(IS_MOBILE){
                $this->totalCount = $totalCount;
                $this->arr_option_filter_mobile = [
                        'title_filter' => $theEntry->model->seo->h1 ,
                        'namefilter' => 'filter_exclusivites',
                        'uri' =>$theEntry->slug,
                        'country' => SEG1,
                        'type' => 'all',
                        'length' => '',
                        'switch_link' => 'filter_type_active',
                    ];
            }



       
        
        return $this->render(IS_MOBILE ? '//page2016/mobile/nos-destinations-country-exclusive' : '//page2016/nos-destinations-country-exclusive',

            [
                'theEntry' => $theEntry,
                'theEntries' => $theEntries,
                'totalCount' => $totalCount,
                'numberFilter' => $numberFilter,
            ]);
    }
    
     public function actionNosDestinationsCountryExclusiveType(){
        
        $theCountryType = \app\modules\destinations\api\Catalog::items([
            'where' =>['slug'=>URI],
            
        ]);
        if (!$theCountryType) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
        $theEntry = \app\modules\exclusives\api\Catalog::cat('secrets-ailleurs/'.SEG3);
        //$this->entry = $theEntry;
         $this->entry = $theCountryType[0];
        
        //$this->getSeo($theEntry->model->seo);
        $this->getSeo($theCountryType[0]->model->seo);
        
        
      
            $theEntries = $this->getAjaxFilterExclusive(['country'=>SEG1,'type'=>$theEntry->model->category_id])['voyage'];
            $totalCount = $this->getAjaxFilterExclusive(['country'=>SEG1,'type'=>$theEntry->model->category_id])['totalCount'];

           // $numberFilter = $this->getAjaxFilterExclusive();
            $numberFilter = $this->getAjaxFilterExclusive(['country'=>SEG1,'type'=>$theEntry->model->category_id]);

            if(IS_MOBILE){
                $this->totalCount = $totalCount;
                $this->arr_option_filter_mobile = [
                        'title_filter' => $theCountryType[0]->model->seo->h1 ,
                        'namefilter' => 'filter_exclusivites',
                        'uri' =>$theCountryType[0]->slug,
                        'country' => SEG1,
                        'type' => $theEntry->model->category_id,
                        'length' => '',
                        'switch_link' => 'filter_type_active',
                    ];
            }
       
        
        return $this->render(IS_MOBILE ? '//page2016/mobile/nos-destinations-country-exclusive-type' : '//page2016/nos-destinations-country-exclusive-type',
            [
                'theCountryType' => $theCountryType,
                'theEntry' => $theEntry,
                'theEntries' => $theEntries,
                'totalCount' => $totalCount,
                'numberFilter' => $numberFilter,
            ]);
    }
    
    // End destination Ideel de voyage, Fomules exclusive, Info
    
    
    

    public function getEnviesLib(){
         $dataEnvies = \app\modules\libraries\models\Category::findOne(['slug' => 'envies'])->children()->with('items')->asArray()->all();
            $enviesLib = [];
            foreach ($dataEnvies as $key => $value) {
                $enviesLib[$value['slug']] = ArrayHelper::map($value['items'], 'item_id', function ($element) {
                    return ['slug'=>$element['slug'], 'title' => $element['title']];
                });
            }
            return $enviesLib;
    }

     public function actionNosDestinationsEnvies() {
            $theEntry = \app\modules\destinations\api\Catalog::get(URI);
            $this->entry = $theEntry;
            if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
            $this->getSeo($theEntry->model->seo);
            $queryEnvies = \app\modules\destinations\api\Catalog::cat(SEG1.'/visiter');
            $queryCount = clone $queryEnvies;
            $enviesId = \app\modules\destinations\api\Catalog::get(URI)->model->item_id;
            $totalCount = count($queryCount->items(['filters' => ['envies' => $enviesId],
                'pagination' => ['pageSize' => 0]]));
            
            
            if(Yii::$app->request->get('see-more') !== NULL){
                $pagesize = Yii::$app->request->get('see-more');
                
            }else{
                $pagesize = 8;
            }
            $envies = \app\modules\destinations\api\Catalog::cat(SEG1.'/visiter')->items(['filters' => ['envies' => $enviesId],
                'pagination' => ['pageSize' =>  $pagesize]]);

         // Get Blogs
         $params = SEG1 . '-' . SEG2;
         $arrBlog = $this->setCacheForBlogs($theEntry, $params);

         // Set title for filter
         $titleBanner = 'le ' ;
         switch (SEG1) {
             case 'vietnam':
                 $titleBanner .= 'vietnam';
                 break;
             case 'cambodge':
                 $titleBanner .= 'cambodge';
                 break;
             case 'laos':
                 $titleBanner .= 'laos';
                 break;
             case 'birmanie':
                 $titleBanner = 'la birmanie';
                 break;
             default:
                 $theEntry = Page::get('destinations');
                 break;
         }

         $titleBanner .=  ' selon vos envies';

         return $this->render(IS_MOBILE ? '//page2016/mobile/nos-destinations-envies' : '//page2016/nos-destinations-list',
             [
                 'theEntry' => $theEntry,
                 'envies' => $envies,
                 'dataEnvies' => $this->getEnviesLib(),
                 'totalCount' => $totalCount,
                 'pagesize' => $pagesize,
                 'arrBlog' => $arrBlog,
                 'titleBanner' => $titleBanner
             ]
         );
    }

    public function actionNosDestinationsDetaile() {
        if (Yii::$app->request->isAjax) {
             if(Yii::$app->request->post()['type'] == 'prog'){
                return $this->renderPartial('//page2016/ajax/prog-des-ajax');
            }
        }
        $theEntry = \app\modules\destinations\api\Catalog::get(URI);
        $this->entry = $theEntry;
        // var_dump($theEntry->model->seo);exit;
        $this->getSeo($theEntry->model->seo);
        if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
        $allCountries = Country::find()->select(['code', 'dial_code', 'name_fr'])->orderBy('name_fr')->asArray()->all();
        $allDialCodes = Country::find()->select(['code', 'CONCAT(name_fr, " (+", dial_code, ")") AS xcode'])->where('dial_code!=""')->orderBy('name_fr')->asArray()->all();

        $model = new DevisForm;
        $model->scenario = 'booking';
        if (IS_MOBILE) {
            $model = new DevisFormMobile;
            $model->scenario = 'devis_mobile';
        }
     //   if (IS_MOBILE) {
     //       $model = new DevisFormMobile;
     //       $model->scenario = 'devis_mobile';
     //   }

        $model->country = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';
        $model->countryCallingCode = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';

        if ($model->load($_POST) && $model->validate()) {
            
                $model->tourName = 'tour test';
                $model->tourUrl = Yii::$app->request->getAbsoluteUrl();
                
                $model->fname = preg_split("/\s+(?=\S*+$)/", $model->fullName)[0];
                $model->lname = '';
                if (isset(preg_split("/\s+(?=\S*+$)/", $model->fullName)[1])) {
                    $model->lname = preg_split("/\s+(?=\S*+$)/", $model->fullName)[1];
                }
            // Save db
           if (IS_MOBILE) {
                
               if (!$model->has_date) {
                   $model->departureDate = null;
                   $model->tourLength = null;
               }
               $contact = '';
              if($model->contactEmail) $contact .= $model->email;
              if($model->contactEmail && $model->contactPhone) $contact .= ' ,';
              if($model->contactPhone) $contact .= $model->phone;
           $data2 = <<<'TXT'
Votre nom & pr?om: {{ fullName : $fullName }}
Votre Mail: {{ email : $email }}                
Comment pr??ez-vous communiquer avec nous?: {{ contact : $contact }}
Date d?arriv? sur place: {{ departureDate : $departureDate }}
Dur?: {{ tourLength : $tourLength }}
Destination(s): {{ countriesToVisit : $countriesToVisit }}
Qu'est ce qui vous a donn?envie de choisir cette (ou ces) destination(s) ?: {{ whyCountry : $whyCountry }}
Votre message: {{ message : $message }}
Avez-vous d??achet?votre (vos) billet(s) d?avion internationaux aller-retour ?: {{ howTicket : $howTicket }}
Budget par personne (budget total, incluant les vols): {{ budget : $budget }}
Le petit d?euner est g??alement d??inclus dans le prix de l?h?ergement. Souhaitez-vous d?autres repas ? : {{ lepetit : $lepetit }}
Vous partez: {{ typeGo : $typeGo }}
Adulte(s): {{ numberOfTravelers12 : $numberOfTravelers12 }}
Enfant(s): {{ numberOfTravelers2 : $numberOfTravelers2 }}
D?ails d'?es : {{ agesOfTravelers12 : $agesOfTravelers12 }}
Quel(s) types d'h?ergement pr??ez-vous  pour ce voyage ?: {{ interest : $interest }}
Combien de chambres souhaitez-vous:
Chambre double avec un grand lit: {{ hotelRoomDbl : $hotelRoomDbl }}
Chambre double avec 2 petit lits: {{ hotelRoomTwn : $hotelRoomTwn }}
Chambre pour 3 personnes: {{ hotelRoomTrp : $hotelRoomTrp }}
Chambre individuelle: {{ hotelRoomSgl : $hotelRoomSgl }}
Vos centres d?int??s :(plusieurs choix possibles) {{ tourThemes : $tourThemes }}
Pouvez-vous nous raconter votre dernier voyage long-courrier ? (destination, type de voyage, exp?iences, ce que vous avez aim? ...) {{ howMessage : $howMessage }}
Vos loisirs et passe-temps pr??? (ce que vous aimez, ce que vous n?aimez pas?) : {{ howHobby : $howHobby }}
TXT;
                $data2 = str_replace([
                   '$departureDate', '$tourLength', '$countriesToVisit', '$typeGo', '$numberOfTravelers12', '$numberOfTravelers2', '$interest', '$tourThemes', '$contact', '$fullName', '$email', '$message', '$agesOfTravelers12', '$hotelRoomDbl', '$hotelRoomTwn', '$hotelRoomTrp', '$hotelRoomSgl', '$budget', '$whyCountry', '$howTraveler', '$howMessage', '$howHobby', '$howTicket', '$job', '$lepetit'
                   ], [
                   $model->departureDate, $model->tourLength, implode(', ' ,(array)$model->countriesToVisit), $model->typeGo, $model->numberOfTravelers12, $model->numberOfTravelers2, implode(', ', (array)$model->interest), implode(', ', (array)$model->tourThemes), $contact, $model->fullName, $model->email, $model->message,$model->agesOfTravelers12, $model->hotelRoomDbl, $model->hotelRoomTwn, $model->hotelRoomTrp, $model->hotelRoomSgl, $model->budget, $model->whyCountry, $model->howTraveler, $model->howMessage, $model->howHobby, $model->howTicket, $model->job, $model->lepetit
                   ], $data2);
           
               $this->saveInquiry($model, 'fr_devis_m_140918', $data2);
           } else {
         
          $data2 = <<<'TXT'
Vos coordonn?s:
 
Tour name: {{ tourUrl : $tourUrl }}  
Extension(s): {{ extension : $extension }}
Votre nom et pr?om: {{ prefix : $prefix }} {{ fullName : $fullName }} 
Votre adresse mail: {{ email : $email }}
Votre pays: {{ country : $country }}
D?artement, Votre ville: {{ region : $region }} {{ ville : $ville }}

votre projet:

Date d'arriv? approximative: {{ departureDate : $departureDate }}
Date de retour: {{ deretourDate : $deretourDate }}
Dur? du voyage: {{ tourLength : $tourLength }}
Destinations: {{ countriesToVisit : $countriesToVisit }}
Qu'est ce qui vous a donn?envie de choisir cette (ou ces) destination(s) ?: {{ whyCountry : $whyCountry }}
Avez-vous d??achet?votre (vos) billet(s) d?avion internationaux aller-retour ?: {{ howTicket : $howTicket }}
Les participants: + de 12 ans {{ numberOfTravelers12 : $numberOfTravelers12 }} d?ails d'?es: {{ agesOfTravelers12 : $agesOfTravelers12 }}
- de 12 ans {{ numberOfTravelers2 : $numberOfTravelers2 }} 
- de 2 ans {{ numberOfTravelers0 : $numberOfTravelers0 }}
La forme physique des participants : {{ howTraveler : $howTraveler }}
Quel(s) type(s) d?h?ergement aimeriez-vous pour ce voyage: {{ hotelTypes : $hotelTypes }}
Combien de chambres souhaitez-vous:
Chambre double avec un grand lit: {{ hotelRoomDbl : $hotelRoomDbl }}
Chambre double avec 2 petit lits: {{ hotelRoomTwn : $hotelRoomTwn }}
Chambre pour 3 personnes: {{ hotelRoomTrp : $hotelRoomTrp }}
Chambre individuelle: {{ hotelRoomSgl : $hotelRoomSgl }}
Repas: {{ mealsIncluded : $mealsIncluded }}
Budget par personne: {{ budget : $budget }}

pour mieux vous connaitre:

D?rivez votre projet, votre vision du voyage et de quelle fa?n vous souhaitez d?ouvrir notre pays: {{ message : $message }}
Th?atiques: {{ tourThemes : $tourThemes }}
Pouvez-vous nous raconter votre dernier voyage long-courrier ? (destination, type de voyage, exp?iences, ce que vous avez aim? ...) {{ howMessage : $howMessage }}
Vos loisirs et passe-temps pr??? (ce que vous aimez, ce que vous n?aimez pas?) : {{ howHobby : $howHobby }}
Nous vous rappelons gratuitement pour mieux comprendre votre projet: {{ callback : $callback }}

TXT;
               $datacallback = <<<'TXT'
Votre num?o de t??hone: {{ countryCallingCode : $CallingCode }} {{ phone : $phone }}
Date / heure pour le RDV: {{ callDate : $callDate }} {{ callTime : $callTime }}

TXT;
               $datalast = <<<'TXT'
Newsletters: {{ newsletter : $newsletter }}    
TXT;
      if($model->callback == 'Oui'){
                $data2 .= $datacallback;
                $data2 .= $datalast;
                $data2 = str_replace([
                    '$tourUrl', '$extension', '$prefix', '$fullName', '$email', '$country', '$region', '$ville', '$departureDate', '$deretourDate', '$tourLength', '$countriesToVisit', '$numberOfTravelers12', '$numberOfTravelers2', '$numberOfTravelers0', '$agesOfTravelers12', '$message', '$tourThemes', '$hotelTypes', '$hotelRoomDbl', '$hotelRoomTwn', '$hotelRoomTrp', '$hotelRoomSgl', '$mealsIncluded', '$budget', '$callback', '$CallingCode', '$phone', '$callDate', '$callTime','$newsletter', '$whyCountry', '$howTraveler', '$howMessage', '$howHobby', '$howTicket'
                    ], [
                    '<a href="'.$model->tourUrl.'">'.$model->tourName.'</a>', implode(', ',(array)$model->extension), $model->prefix, $model->fullName, $model->email, $model->country, $model->region, $model->ville, $model->departureDate, $model->deretourDate, $model->tourLength, implode(', ', (array)$model->countriesToVisit), $model->numberOfTravelers12, $model->numberOfTravelers2, $model->numberOfTravelers0, $model->agesOfTravelers12, $model->message, implode(', ',(array)$model->tourThemes),  implode(', ', (array)$model->hotelTypes), $model->hotelRoomDbl, $model->hotelRoomTwn, $model->hotelRoomTrp, $model->hotelRoomSgl, $model->mealsIncluded, $model->budget, $model->callback, $model->countryCallingCode, $model->phone, $model->callDate, $model->callTime, $model->newsletter == 1 ? 'Oui' : 'Non', $model->whyCountry, $model->howTraveler, $model->howMessage, $model->howHobby, $model->howTicket
                    ], $data2);
                }else{
                    
                 $data2 .= $datalast;
                 
                 $data2 = str_replace([
                    '$tourUrl', '$extension', '$prefix', '$fullName', '$email', '$country', '$region', '$ville', '$departureDate', '$deretourDate', '$tourLength', '$countriesToVisit', '$numberOfTravelers12', '$numberOfTravelers2', '$numberOfTravelers0', '$agesOfTravelers12', '$message', '$tourThemes', '$hotelTypes', '$hotelRoomDbl', '$hotelRoomTwn', '$hotelRoomTrp', '$hotelRoomSgl', '$mealsIncluded', '$budget', '$callback', '$newsletter', '$whyCountry', '$howTraveler', '$howMessage', '$howHobby', '$howTicket', '$job'
                    ], [
                    '<a href="'.$model->tourUrl.'">'.$model->tourName.'</a>', implode(', ',(array)$model->extension), $model->prefix, $model->fullName, $model->email, $model->country, $model->region, $model->ville, $model->departureDate, $model->deretourDate, $model->tourLength, implode(', ', (array)$model->countriesToVisit), $model->numberOfTravelers12, $model->numberOfTravelers2, $model->numberOfTravelers0, $model->agesOfTravelers12, $model->message, implode(', ',(array)$model->tourThemes),  implode(', ', (array)$model->hotelTypes), $model->hotelRoomDbl, $model->hotelRoomTwn, $model->hotelRoomTrp, $model->hotelRoomSgl, $model->mealsIncluded, $model->budget, $model->callback, $model->newsletter == 1 ? 'Oui' : 'Non', $model->whyCountry, $model->howTraveler, $model->howMessage, $model->howHobby, $model->howTicket, $model->job
                    ], $data2);
                }
         
                $this->saveInquiry($model, 'form_booking_1216', $data2);
                // If he subscribes to our newsletter
              //  if ($model->newsletter == 1) $this->saveNlsub($model, 'form_booking_1216');
           }

            // Email me
            $this->notifyAmica('Devis from ' . $model->email, '//email_form_booking_1216', ['model' => $model]);
            $this->confirmAmica($model->email);
            // Redir
            return Yii::$app->response->redirect(DIR.'merci?from=booking&id='.$this->id_inquiry);
        }
        
        // 2 dia danh bat ky thuoc cung mot region
        //var_dump($theEntry);exit;
        $random_destination = \app\modules\destinations\api\Catalog::items([
            //'where' => ['not', ['slug' => URI]],
            'where' => ['and', 'category_id = '.$theEntry->category_id.'', ['not', ['slug' => URI]]],
            'orderBy' =>'rand()',
            'filters' => ['region'=>$theEntry->data->region[0]],
            'pagination' =>['pagesize'=>2],
        ]);
        
        
         // BLOGS
        $arrBlog = array();
        if(!Yii::$app->cache->get('cache-blog-lieu-002-'.SEG1)){
            if(isset(\app\modules\modulepage\api\Catalog::get('blog-'.SEG1)->data->blogs)) {
                $dataBlogSelected = \app\modules\modulepage\api\Catalog::get('blog-'.SEG1)->data->blogs;
                foreach ($dataBlogSelected as $keyBlog => $valueBlog) {
                    $arrBlog[] = $this->getDataPost($valueBlog);
                    
                    foreach ($arrBlog as $key2 => $value2) {
                        $categoryId = $value2['categories'][0];
                        $featuredMediaId = $value2['featured_media'];

                        $titleCategory = $this->getCategoryName($categoryId)['name'];
                        $arrBlog[$keyBlog]['cat_name'] = $titleCategory;
                        $arrBlog[$keyBlog]['alt_text'] = $this->getFeatureImage($arrBlog[$keyBlog]['featured_media'])['alt_text'];
                        $featuredMediaData = $this->getFeatureImage($featuredMediaId)['media_details']['sizes']['barouk_list-thumb'];
                        $arrBlog[$keyBlog]['src'] = '/timthumb.php?src=' . $featuredMediaData['source_url'] . '&w=211&h=141&zc=1&q=80';
                    }
                }
            }
            Yii::$app->cache->set('cache-blog-lieu-002-'.SEG1, $arrBlog);
        }else{
            $arrBlog = Yii::$app->cache->get('cache-blog-lieu-002-'.SEG1);
            
        }    
        
         
         
        
        
        return $this->render(IS_MOBILE ? '//page2016/mobile/nos-destinations-single' : '//page2016/nos-destinations-detaile',[
                'theEntry' => $theEntry, 
                'model' => $model,
                'allCountries' => $allCountries,
                'allDialCodes' => $allDialCodes,
                'arrBlog' => $arrBlog,
                'random_destination' => $random_destination,
            ]);
    }
        

    public function actionNosDestinationsDetaileInfos()
    {
        $theEntry = \app\modules\destinations\api\Catalog::cat(URI);
        
        $this->entry = $theEntry;
        if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
        $parents = \app\modules\destinations\models\Category::findOne(['slug' => URI])->parents()->all();
        
        $this->getSeo($theEntry->model->seo);
        $parent = \app\modules\destinations\models\Category::findOne(['slug' => SEG1 . '/' . SEG2]);
        
        //lay menus
        $children = [];
        if ($parent){
            $children = $parent->children(1)->andWhere(['status' => 1])->all();
        }    
        
        
        $top3 = $parent->children(1)->andWhere(['status' => 1])->andWhere(['!=', 'slug', URI])->orderBy('rand()')->limit(3)->all();
        
        //var_dump($children[1]);exit;
        
        if(SEG1 == 'birmanie'){
            $theEntries = \app\modules\programmes\api\Catalog::items([
                    
                    'orderBy' => 'on_top ASC',
                    'filters' => ['countries' => SEG1],
                    'pagination' => ['pageSize' => 4]
                ]);
        }else{
            $theEntries = \app\modules\programmes\api\Catalog::items([
                'where' => ['and', ['>=', 'on_top', '1'], ['!=', 'slug', URI]],
                'orderBy' => 'rand()',
                'filters' => ['countries' => SEG1],
                'pagination' => ['pageSize' => 4]
            ]); 
        }    
        
       
        
           return $this->render(IS_MOBILE ? '//page2016/mobile/nos-destinations-detaile-infos' : '//page2016/nos-destinations-detaile-infos', [
            'theEntry' => $theEntry, 
            'theEntries' => $theEntries,
            'parent' => $parent, 
            'children' => $children, 
            'parents' => $parents,
            'top3' => $top3,
                ]);
    }

    public function actionNosDestinationsDetaileInfosSingle()
    {
        $theEntry = \app\modules\destinations\api\Catalog::get(URI);
        //var_dump($theEntry);exit;
        $this->entry = $theEntry;
        if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
        $this->getSeo($theEntry->model->seo);
        $parent = \app\modules\destinations\models\Category::findOne(['slug' => SEG1 . '/' . SEG2]);
        
        //lay menus
        $children = [];
        if ($parent){
            $children = $parent->children(1)->andWhere(['status' => 1])->all();
        }    
        
        $top3 = \app\modules\destinations\api\Catalog::items([
                'where' => ['and', ['category_id'=> $theEntry->category_id], ['!=', 'slug', URI]],
                'orderBy' => 'rand()',
               
                'pagination' => ['pageSize' => 3]
            ]); 
        
        if(count($top3) < 2){
            $top3 = $parent->children(1)->andWhere(['status' => 1])->andWhere(['!=', 'slug', URI])->orderBy('rand()')->limit(3)->all();
        }
        //    var_dump($top3);exit;
        
        
        
       if(SEG1 == 'birmanie'){
            $theEntries = \app\modules\programmes\api\Catalog::items([
                    
                    'orderBy' => 'on_top ASC',
                    'filters' => ['countries' => SEG1],
                    'pagination' => ['pageSize' => 4]
                ]);
        }else{
            $theEntries = \app\modules\programmes\api\Catalog::items([
                'where' => ['and', ['>=', 'on_top', '1'], ['!=', 'slug', URI]],
                'orderBy' => 'rand()',
                'filters' => ['countries' => SEG1],
                'pagination' => ['pageSize' => 4]
            ]); 
        }    
       // return $this->render('//page2016/nos-destinations-detaile-infos-single', [
          return $this->render(IS_MOBILE ? '//page2016/mobile/nos-destinations-detaile-infos' : '//page2016/nos-destinations-detaile-infos', [
       
            'theEntry' => $theEntry,
            'parent' => $parent,
            'children' => $children,
            'top3' => $top3,
            'theEntries' => $theEntries,
                ]);
    }
    
     public function actionThanks() {
		 $theEntry = Page::get(24);
            $this->root = $theEntry;
         if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
		 $this->getSeo($theEntry->model->seo);
        //$this->metaT = 'Merci';
        $from = Yii::$app->request->get('from', 'contact');
        return $this->render(IS_MOBILE ? '//page2016/mobile/thanks' : '//page2016/thanks', [
			'theEntry'=> $theEntry,
			'from' => $from,
		]);
    }

    public function addContactToMailjet($email, $listID, $data=[]){
         $mj = new Mailjet('35d34aefe4ca059fc1dcc6329ae595e4', '52540d6e2c0b3108a0a810935731b11b');
                if(isset($data['sex'])){
                    $data['sex'] = str_replace('.', '', $data['sex']);
                    if($data['sex'] == 'Mlle')
                        $data['sex'] = 'MME';
                    $data['sex'] = strtoupper($data['sex']);
                }
                 $contact = array(
                     'Email'         =>  $email, 
                     'Properties' => $data
                 );
                 $params = array(
                    "method" => "POST",
                    "ID" => $listID,
                    "Action"    =>  "addforce"
                 );
                 $params = array_merge($params, $contact);
                 $result = $mj->contactslistManageContact($params);
    }

    public function actionNewsletter() {
		$theEntry = Page::get(29);
            $this->root = $theEntry;
         if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
		 
        if (Yii::$app->request->isAjax) {
            if(Yii::$app->request->post()['email']){
                $return = false;
                if(\yii\easyii\modules\subscribe\api\Subscribe::save(Yii::$app->request->post()['email']))
                    $return = true;
               $data = [
                    'source' => 'newsletter',
                    'newsletter_insc' => date('d/m/Y'),
                    'statut' => 'prospect',
                    'birmanie' => false,
                    'vietnam' => false,
                    'cambodia' => false,
                    'laos' => false
                ];
                $this->addContactToMailjet(Yii::$app->request->post()['email'], '1688900', $data);
                    return $return;
            }
        }
        $this->metaT = 'Abonnement newsletters';
        $this->metaD = 'Bénéficiez de nos informations de voyages (promotions, conseils, avis) en vous abonnant à notre newsletter. Nous vous aidons à réaliser votre voyage.';

        $allCountries = Country::find()->select(['code', 'name_fr'])->orderBy('name_fr')->asArray()->all();

        $model = new NewsletterForm;
        $model->scenario = 'newsletter';
        if ($model->load($_POST) && $model->validate()) {
            \yii\easyii\modules\subscribe\api\Subscribe::save( $model->email );
            
            // Redir
            return Yii::$app->response->redirect(DIR.'merci?from=newsletter');
        } else {
            return $this->render('//page2016/newsletter', [
						'theEntry'=> $theEntry,	
                        'model' => $model,
                        'allCountries' => $allCountries,
            ]);
        }
    }
    public function actionContact() {
        $this->layout = 'main-form';
        if(IS_MOBILE)
            $this->layout = 'mobile-form';
        $theEntry = Page::get(21);
        $this->root = $theEntry;
         if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
		 $this->getSeo($theEntry->model->seo);
		$allCountries = Country::find()->select(['code', 'name_fr'])->orderBy('name_fr')->asArray()->all();

         if (IS_MOBILE) {
            $model = new ContactFormMobile;
            $model->scenario = 'contact_mobile';
        } else {
            $model = new ContactForm;
            $model->scenario = 'contact';
        }
       

        $model->country = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';
        $model->countryCallingCode = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';

        if ($model->load($_POST) && $model->validate()) {

            $toEmail = 'Amica FR <inquiry-fr@amicatravel.com>';
            if($model->subjet == 'pdv' || $model->subjet == 'dec') {
                if(IS_MOBILE){
                    $model->fname = preg_split("/\s+(?=\S*+$)/", $model->fullName)[0];
                    $model->lname = '';
                    if (isset(preg_split("/\s+(?=\S*+$)/", $model->fullName)[1])) {
                        $model->lname = preg_split("/\s+(?=\S*+$)/", $model->fullName)[1];
                    }
$data2 =<<<'TXT'
Votre nom & prénom: {{ prefix : $prefix }} {{ fullname : $fullName }}
Votre adresse mail: {{ email : $email }}
Date de naissance: {{ age : $age }}
Votre pays: {{ country : $country }}
Département, Votre ville: {{ region : $region }} {{ ville : $ville }}
Votre message: {{ message : $message }}
Si vous êtes recommandé(e) par un ancien client d'Amica, merci de préciser son nom et prénom: {{ reference : $reference }}
TXT;
                    $data2 = str_replace([
                    '$prefix', '$fullName', '$email', '$country', '$region', '$ville','$message', '$reference', '$age'
                    ], [
                    $model->prefix, $model->fullName, $model->email, $model->country, $model->region, $model->ville, $model->message, $model->reference, $model->age
                    ], $data2);
                    // Save db
                    $this->saveInquiry($model, 'Form-contact-mobile', $data2);                     
                } else {
$data2 =<<<'TXT'
Votre nom et prénom: {{ prefix : $prefix }} {{ fname : $fname }} {{ lname : $lname }}
Votre adresse mail: {{ email : $email }} Date de naissance: {{ age : $age }}
Votre pays: {{ country : $country }}
Département, Votre ville: {{ region : $region }} {{ ville : $ville }}
Votre message: {{ message : $message }}
Si vous êtes recommandé(e) par un ancien client d'Amica, merci de préciser son nom et prénom: {{ reference : $reference }}
TXT;
                    $data2 = str_replace([
                    '$prefix', '$fname', '$lname', '$email', '$country', '$region', '$ville','$message', '$reference', '$age'
                    ], [
                    $model->prefix, $model->fname, $model->lname, $model->email, $model->country, $model->region, $model->ville, $model->message, $model->reference, $model->age
                    ], $data2);
                    // Save db
                        $this->saveInquiry($model, 'Form-contact', $data2);

                }

            } else{
$data2 =<<<'TXT'
    Votre nom et prénom: {{ prefix : $prefix }} {{ fname : $fname }} {{ lname : $lname }}
    Votre adresse mail: {{ email : $email }}
    Votre pays: {{ country : $country }}
    Département, Votre ville: {{ region : $region }} {{ ville : $ville }}
    Votre message: {{ message : $message }}
    Si vous êtes recommandé(e) par un ancien client d'Amica, merci de préciser son nom et prénom: {{ reference : $reference }}
TXT;
                    $data2 = str_replace([
                    '$prefix', '$fname', '$lname', '$email', '$country', '$region', '$ville','$message', '$reference'
                    ], [
                    $model->prefix, $model->fname, $model->lname, $model->email, $model->country, $model->region, $model->ville, $model->message, $model->reference
                    ], $data2);
                switch ($model->subjet) {
                    case 'pami':
                    $toEmail = 'service.clientele@amicatravel.com';
                    break;
                    case 'sc':
                    $toEmail = 'service.clientele@amicatravel.com';
                    break;
                    case 'hr':
                    $toEmail = 'hr@amica-travel.com';
                    break;
                    case 'mkt':
                    $toEmail = 'phuong.anh@amicatravel.com';
                    break;
                    case 'qd':
                    $toEmail = 'info@amica-travel.com';
                    break;
                }
            }
            if(!IS_MOBILE){
                if($model->newsletter){
                    $data = [
                    'firstname' => $model->fname,
                    'lastname' => $model->lname,
                    'codecontry' => $model->country,
                    'source' => 'newsletter',
                    'sex' => $model->prefix,
                    'newsletter_insc' => date('d/m/Y'),
                    'statut' => 'prospect',
                    'birmanie' => false,
                    'vietnam' => false,
                    'cambodia' => false,
                    'laos' => false
                    ];
                    $this->addContactToMailjet($model->email, '1688900', $data);
                }
            }
            
            // Email me
            $this->notifyAmica('[Contact] '.Yii::$app->params['subjet'][$model->subjet].' from ' . $model->email, '//page2016/email_template', ['data2' => $data2],  $toEmail);
            $this->confirmAmica($model->email);
            // Redir
            return Yii::$app->response->redirect(DIR.'merci?from=contact&id='.$this->id_inquiry);
        }


        return $this->render(IS_MOBILE ? '//page2016/mobile/contact_mobile' : '//page2016/contact', [
                    'theEntry' => $theEntry,
                    'model' => $model,
                    'allCountries' => $allCountries,
        ]);
    }
    
    public function actionRdv()
    {
        $this->layout = 'main-form';
        if(IS_MOBILE)
            $this->layout = 'mobile-form';
		$theEntry = Page::get(23);
        $this->root = $theEntry;
         if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
		 $this->getSeo($theEntry->model->seo);
		
        $allCountries = Country::find()->select(['code', 'dial_code', 'name_fr'])->orderBy('name_fr')->asArray()->all();
        $allDialCodes = Country::find()->select(['code', 'CONCAT(name_fr, " (+", dial_code, ")") AS xcode'])->where('dial_code!=""')->orderBy('name_fr')->asArray()->all();
      
        $model = new ContactForm;
        if (IS_MOBILE) {
             $model = new ContactFormMobile;
            $model->scenario = 'rdv_mobile';
        } else {
            $model->scenario = 'rdv';
        }
       

        $model->country = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';
        $model->countryCallingCode = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';

        if ($model->load($_POST) && $model->validate()) {
            if (IS_MOBILE) {
            $model->fname = preg_split("/\s+(?=\S*+$)/", $model->fullName)[0];
            $model->lname = '';
            if (isset(preg_split("/\s+(?=\S*+$)/", $model->fullName)[1])) {
                $model->lname = preg_split("/\s+(?=\S*+$)/", $model->fullName)[1];
            }
            
             $data2 = <<<'TXT'
Indicatif de pays: {{ countryCallingCode : $countryCallingCode }}
Votre numéro: {{ phone : $phone }}
Votre nom & prénom: {{ prefix : $prefix }} {{ fullName : $fullName }}
Votre mail: {{ email : $email }}
Date de naissance: {{ age : $age }}
Votre nationalité: {{ country : $country }}
Département, Votre ville: {{ region : $region }} {{ ville : $ville }}
Votre message: {{ message : $message }}
Si vous êtes recommandé(e) par un ancien client d'Amica, merci de préciser son nom et prénom: {{ reference : $reference }}
TXT;
                  $data2 = str_replace([
                    '$countryCallingCode', '$phone', '$prefix', '$fullName', '$email', '$message', '$country', '$region', '$ville', '$reference', '$age'
                    ], [
                    $model->countryCallingCode, $model->phone, $model->prefix, $model->fullName, $model->email, $model->message, $model->country, $model->region, $model->ville, $model->reference, $model->age
                    ], $data2);
            
                $this->saveInquiry($model, 'Form-rdv-mobile', $data2);
            } else {
            // Save db
            $data2 = <<<'TXT'
Votre nom et prénom: {{ prefix : $prefix }} {{ fname : $fname }} {{ lname : $lname }}
Votre adresse mail: {{ email : $email }}
Date de naissance: {{ age : $age }}
Votre nationalité: {{ country : $country }}
Département, Votre ville: {{ region : $region }} {{ ville : $ville }}
Votre numéro téléphone: {{ countryCallingCode : $CallingCode }} {{ phone : $phone }}
Date / heure pour le RDV: {{ callDate : $callDate }} {{ callTime : $callTime }}
Votre message: {{ message : $message }}
Si vous êtes recommandé(e) par un ancien client d'Amica, merci de préciser son nom et prénom: {{ reference : $reference }}
TXT;
                $data2 = str_replace([
                    '$prefix', '$fname', '$lname', '$email', '$country', '$region', '$ville', '$CallingCode', '$phone', '$callDate', '$callTime', '$message', '$reference', '$age'
                    ], [
                    $model->prefix, $model->fname, $model->lname, $model->email, $model->country, $model->region, $model->ville, $model->countryCallingCode, $model->phone, $model->callDate, $model->callTime, $model->message, $model->reference, $model->age
                    ], $data2);
            if($model->newsletter){
                $data = [
                        'firstname' => $model->fname,
                        'lastname' => $model->lname,
                        'codecontry' => $model->country,
                        'source' => 'newsletter',
                        'sex' => $model->prefix,
                        'newsletter_insc' => date('d/m/Y'),
                        'statut' => 'prospect',
                        'birmanie' => false,
                        'vietnam' => false,
                        'cambodia' => false,
                        'laos' => false
                    ];
                $this->addContactToMailjet($model->email, '1688900', $data);
             }
            $this->saveInquiry($model, 'Form-rdv', $data2);
            }
            // Email me
            $this->notifyAmica('RDV from ' . $model->email, '//page2016/email_template', ['data2' => $data2]);
            $this->confirmAmica($model->email);
            // Redir
            return Yii::$app->response->redirect(DIR.'merci?from=rdv&id='.$this->id_inquiry);
        }
            return $this->render(IS_MOBILE ? '//page2016/mobile/rdv_mobile' : '//page2016/rdv', [
                        'theEntry' => $theEntry,
                        'model' => $model,
                        'allCountries' => $allCountries,
                        'allDialCodes' => $allDialCodes,
            ]);
        
    }

    
    public function actionDevis() {
       // $this->metaT = 'Devis sur mesure pour un voyage sur mesure au Vietnam, Laos, Cambodge';
       // $this->metaD = 'Demandez un devis et votre conseiller personnel vous répondra sous 48h et vous aidera à construire le circuit le mieux adapté à votre demande.';
        $this->layout = 'main-form';
        if(IS_MOBILE)
            $this->layout = 'mobile-form';
		 $theEntry = Page::get(20);
        $this->root = $theEntry;
         if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
		 $this->getSeo($theEntry->model->seo);
		 
        $allCountries = Country::find()->select(['code', 'dial_code', 'name_fr'])->orderBy('name_fr')->asArray()->all();
        $allDialCodes = Country::find()->select(['code', 'CONCAT(name_fr, " (+", dial_code, ")") AS xcode'])->where('dial_code!=""')->orderBy('name_fr')->asArray()->all();

        $model = new DevisForm;
        $model->scenario = 'devis';
        if(IS_MOBILE) {
            $model = new DevisFormMobile;
            $model->scenario = 'devis_mobile';
        }
        

        $model->country = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';
        $model->countryCallingCode = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';

        if ($model->load($_POST) && $model->validate()) {
            
           if (IS_MOBILE) {
                $model->fname = preg_split("/\s+(?=\S*+$)/", $model->fullName)[0];
                $model->lname = '';
                if (isset(preg_split("/\s+(?=\S*+$)/", $model->fullName)[1])) {
                    $model->lname = preg_split("/\s+(?=\S*+$)/", $model->fullName)[1];
                }
                if (!$model->has_date) {
                    $model->departureDate = null;
                    $model->tourLength = null;
                }
                $contact = '';
               if($model->contactEmail) $contact .= $model->email;
               if($model->contactEmail && $model->contactPhone) $contact .= ' ,';
               if($model->contactPhone) $contact .= $model->phone;
            $data2 = <<<'TXT'
Votre nom & prénom: {{ prefix : $prefix }} {{ fname : $fullName }}
Votre Mail: {{ email : $email }}
Date de naissance: {{ age : $age }}
Votre pays: {{ country : $country }}
Département, Votre ville: {{ region : $region }} {{ ville : $ville }}              
Comment préférez-vous communiquer avec nous?: {{ contact : $contact }}
Date d’arrivée sur place: {{ departureDate : $departureDate }}
Durée: {{ tourLength : $tourLength }}
Destinations: {{ countriesToVisit : $countriesToVisit }} {{ multipays : $multipays }}
Votre message: {{ message : $message }}
Avez-vous déjà acheté votre (vos) billet(s) d’avion internationaux aller-retour ?: {{ howTicket : $howTicket }}

Budget par personne (budget total, incluant les vols): {{ budget : $budget }}
Le petit déjeuner est généralement déjà inclus dans le prix de l’hébergement. Souhaitez-vous d’autres repas ? : {{ lepetit : $lepetit }}
Vous partez: {{ typeGo : $typeGo }}
Adulte(s): {{ numberOfTravelers12 : $numberOfTravelers12 }}
Enfant(s): {{ numberOfTravelers2 : $numberOfTravelers2 }}
BéBé: {{ numberOfTravelers1 : $numberOfTravelers1 }}
Détails d'âges : {{ agesOfTravelers12 : $agesOfTravelers12 }}
Quel(s) types d'hébergement préférez-vous  pour ce voyage ?: {{ interest : $interest }}
Combien de chambres souhaitez-vous:
Chambre double avec un grand lit: {{ hotelRoomDbl : $hotelRoomDbl }}
Chambre double avec 2 petit lits: {{ hotelRoomTwn : $hotelRoomTwn }}
Chambre pour 3 personnes: {{ hotelRoomTrp : $hotelRoomTrp }}
Chambre individuelle: {{ hotelRoomSgl : $hotelRoomSgl }}
Vos centres d’intérêts :(plusieurs choix possibles) {{ tourThemes : $tourThemes }}
Pouvez-vous nous raconter votre dernier voyage long-courrier ? (destination, type de voyage, expériences, ce que vous avez aimé, ...) {{ howMessage : $howMessage }}
Vos loisirs et passe-temps préférés (ce que vous aimez, ce que vous n’aimez pas…) : {{ howHobby : $howHobby }}
Si vous êtes recommandé(e) par un ancien client d'Amica, merci de préciser son nom et prénom: {{ reference : $reference }}
TXT;
                 $data2 = str_replace([
                    '$departureDate', '$tourLength', '$countriesToVisit', '$multipays', '$typeGo', '$numberOfTravelers12', '$numberOfTravelers2', '$interest', '$tourThemes', '$contact', '$fullName', '$email', '$message', '$agesOfTravelers12', '$hotelRoomDbl', '$hotelRoomTwn', '$hotelRoomTrp', '$hotelRoomSgl', '$budget', '$howTraveler', '$howMessage', '$howHobby', '$howTicket', '$job', '$lepetit', '$country', '$region', '$ville', '$numberOfTravelers1', '$reference', '$prefix', '$age'
                    ], [
                    $model->departureDate, $model->tourLength, implode(', ' ,(array)$model->countriesToVisit), $model->multipays, $model->typeGo, $model->numberOfTravelers12, $model->numberOfTravelers2, implode(', ', (array)$model->interest), implode(', ', (array)$model->tourThemes), $contact, $model->fullName, $model->email, $model->message,$model->agesOfTravelers12, $model->hotelRoomDbl, $model->hotelRoomTwn, $model->hotelRoomTrp, $model->hotelRoomSgl, $model->budget, $model->howTraveler, $model->howMessage, $model->howHobby, $model->howTicket,  $model->job, $model->lepetit, $model->country, $model->region, $model->ville, $model->numberOfTravelers1, $model->reference, $model->prefix, $model->age
                    ], $data2);
            
                $this->saveInquiry($model, 'Form-devis-mobile', $data2);
            } else {    
                
            // Save db
          $data2 = <<<'TXT'
Vos coordonnées:
 
Votre nom et prénom: {{ prefix : $prefix }} {{ fname : $fname }} {{ lname : $lname }} 
Votre adresse mail: {{ email : $email }} Date de naissance: {{ age : $age }}
Votre pays: {{ country : $country }}
Département, Votre ville: {{ region : $region }} {{ ville : $ville }}

votre projet:

Date d'arrivée approximative: {{ departureDate : $departureDate }}
Date de retour: {{ deretourDate : $deretourDate }}
Durée du voyage: {{ tourLength : $tourLength }}
Destinations: {{ countriesToVisit : $countriesToVisit }} {{ multipays : $multipays }}
Avez-vous déjà acheté votre (vos) billet(s) d’avion internationaux aller-retour ?: {{ howTicket : $howTicket }}
En savoir plus : {{ ticketDetail : $ticketDetail }}
Souhaitez-vous être accompagné pour l'achat de vos billets internationnaux ? {{ helpTicket : $helpTicket }}

Les participants: + de 12 ans {{ numberOfTravelers12 : $numberOfTravelers12 }} détails d'âges: {{ agesOfTravelers12 : $agesOfTravelers12 }}
- de 12 ans {{ numberOfTravelers2 : $numberOfTravelers2 }} 
- de 2 ans {{ numberOfTravelers0 : $numberOfTravelers0 }}
La forme physique des participants : {{ howTraveler : $howTraveler }}
Quel(s) type(s) d’hébergement aimeriez-vous pour ce voyage: {{ hotelTypes : $hotelTypes }}
Combien de chambres souhaitez-vous:
Chambre double avec un grand lit: {{ hotelRoomDbl : $hotelRoomDbl }}
Chambre double avec 2 petit lits: {{ hotelRoomTwn : $hotelRoomTwn }}
Chambre pour 3 personnes: {{ hotelRoomTrp : $hotelRoomTrp }}
Chambre individuelle: {{ hotelRoomSgl : $hotelRoomSgl }}
Repas: {{ mealsIncluded : $mealsIncluded }}
Budget par personne: {{ budget : $budget }}

pour mieux vous connaitre:

Décrivez votre projet, votre vision du voyage et de quelle façon vous souhaitez découvrir notre pays: {{ message : $message }}
Thématiques: {{ tourThemes : $tourThemes }}
Pouvez-vous nous raconter votre dernier voyage long-courrier ? (destination, type de voyage, expériences, ce que vous avez aimé, ...) {{ howMessage : $howMessage }}
Vos loisirs et passe-temps préférés (ce que vous aimez, ce que vous n’aimez pas…) : {{ howHobby : $howHobby }}
Nous vous rappelons gratuitement pour mieux comprendre votre projet: {{ callback : $callback }}

TXT;
               $datacallback = <<<'TXT'
Votre numéro de téléphone: {{ countryCallingCode : $CallingCode }} {{ phone : $phone }}
Date / heure pour le RDV: {{ callDate : $callDate }} {{ callTime : $callTime }}

TXT;
               $datalast = <<<'TXT'
Si vous êtes recommandé(e) par un ancien client d'Amica, merci de préciser son nom et prénom: {{ reference : $reference }}   
Newsletters: {{ newsletter : $newsletter }}    
TXT;
               
        if($model->newsletter){
            $data = [
                    'firstname' => $model->fname,
                    'lastname' => $model->lname,
                    'codecontry' => $model->country,
                    'source' => 'newsletter',
                    'sex' => $model->prefix,
                    'newsletter_insc' => date('d/m/Y'),
                    'statut' => 'prospect',
                    'birmanie' => false,
                    'vietnam' => false,
                    'cambodia' => false,
                    'laos' => false
                ];
            $this->addContactToMailjet($model->email, '1688900', $data);
        }
        
      if($model->countriesToVisit != 'Multi-pays'){
          $model->multipays = '';
      }else{
          $model->multipays = ': '.$model->multipays;
      }  
        
      if($model->callback == 'Oui'){
                $data2 .= $datacallback;
                $data2 .= $datalast;
                $data2 = str_replace([
                   '$prefix', '$fname', '$lname', '$email', '$country', '$region', '$ville', '$departureDate', '$deretourDate', '$tourLength', '$countriesToVisit', '$multipays', '$numberOfTravelers12', '$numberOfTravelers2', '$numberOfTravelers0', '$agesOfTravelers12', '$message', '$tourThemes', '$hotelTypes', '$hotelRoomDbl', '$hotelRoomTwn', '$hotelRoomTrp', '$hotelRoomSgl', '$mealsIncluded', '$budget', '$callback', '$CallingCode', '$phone', '$callDate', '$callTime','$newsletter', '$whyCountry', '$howTraveler', '$howMessage', '$howHobby', '$howTicket', '$ticketDetail', '$helpTicket','$job', '$reference', '$age'
                    ], [
                    $model->prefix, $model->fname, $model->lname, $model->email, $model->country, $model->region, $model->ville, $model->departureDate, $model->deretourDate, $model->tourLength, implode(', ', (array)$model->countriesToVisit), $model->multipays, $model->numberOfTravelers12, $model->numberOfTravelers2, $model->numberOfTravelers0, $model->agesOfTravelers12, $model->message, implode(', ',(array)$model->tourThemes),  implode(', ', (array)$model->hotelTypes), $model->hotelRoomDbl, $model->hotelRoomTwn, $model->hotelRoomTrp, $model->hotelRoomSgl, $model->mealsIncluded, $model->budget, $model->callback, $model->countryCallingCode, $model->phone, $model->callDate, $model->callTime, $model->newsletter == 1 ? 'Oui' : 'Non', $model->whyCountry, $model->howTraveler, $model->howMessage, $model->howHobby, $model->howTicket, $model->ticketDetail, $model->helpTicket, $model->job, $model->reference, $model->age
                    ], $data2);
                }else{
                    
                 $data2 .= $datalast;
                 
                 $data2 = str_replace([
                    '$prefix', '$fname', '$lname', '$email', '$country', '$region', '$ville', '$departureDate', '$deretourDate', '$tourLength', '$countriesToVisit', '$multipays', '$numberOfTravelers12', '$numberOfTravelers2', '$numberOfTravelers0', '$agesOfTravelers12', '$message', '$tourThemes', '$hotelTypes', '$hotelRoomDbl', '$hotelRoomTwn', '$hotelRoomTrp', '$hotelRoomSgl', '$mealsIncluded', '$budget', '$callback', '$newsletter', '$whyCountry', '$howTraveler', '$howMessage', '$howHobby', '$howTicket', '$ticketDetail', '$helpTicket', '$job', '$reference', '$age'
                    ], [
                    $model->prefix, $model->fname, $model->lname, $model->email, $model->country, $model->region, $model->ville, $model->departureDate, $model->deretourDate, $model->tourLength, implode(', ', (array)$model->countriesToVisit), $model->multipays, $model->numberOfTravelers12, $model->numberOfTravelers2, $model->numberOfTravelers0, $model->agesOfTravelers12, $model->message, implode(', ',(array)$model->tourThemes),  implode(', ', (array)$model->hotelTypes), $model->hotelRoomDbl, $model->hotelRoomTwn, $model->hotelRoomTrp, $model->hotelRoomSgl, $model->mealsIncluded, $model->budget, $model->callback, $model->newsletter == 1 ? 'Oui' : 'Non', $model->whyCountry, $model->howTraveler, $model->howMessage, $model->howHobby, $model->howTicket, $model->ticketDetail, $model->helpTicket, $model->job, $model->reference, $model->age
                    ], $data2);
                }
               
                $this->saveInquiry($model, 'Form-devis', $data2);
                // If he subscribes to our newsletter
              //  if ($model->newsletter == 1) $this->saveNlsub($model, 'Form-devis');
            
            }     
            // Email me
            $this->notifyAmica('Devis from ' . $model->email, '//page2016/email_template', ['data2' => $data2]);
            $this->confirmAmica($model->email);
            // Redir
            return Yii::$app->response->redirect(DIR.'merci?from=devis&id='.$this->id_inquiry);
        }

        return $this->render(IS_MOBILE ? '//page2016/mobile/devis_mobile' : '//page2016/devis', [
					'theEntry' => $theEntry,
                    'model' => $model,
                    'allCountries' => $allCountries,
                    'allDialCodes' => $allDialCodes,
        ]);
    }
    public function actionVotreProjet() {
        $this->layout = 'main-form';
		$theEntry = Page::get(22);
        $this->root = $theEntry;
         if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
		 $this->getSeo($theEntry->model->seo);
       // $this->metaT = 'Devis sur mesure pour un voyage sur mesure au Vietnam, Laos, Cambodge';
       // $this->metaD = 'Demandez un devis et votre conseiller personnel vous répondra sous 48h et vous aidera à construire le circuit le mieux adapté à votre demande.';
if (Yii::$app->request->isAjax) {
            //add exclusives to Projet
            if(Yii::$app->request->post()['type'] == 'excl'){
              $exclId = Yii::$app->request->post()['tour_id'];
              if(Yii::$app->session->get('projet'))
                $projet = Yii::$app->session->get('projet');
              else $projet = [
                'programes'=> ['select'=>[], 'view'=>[]],
                'exclusives' => ['select'=>[], 'view'=> []]
                ];
              if(!in_array($exclId, $projet['exclusives']['select']))
                  $projet['exclusives']['select'][] = $exclId;
              if(($key = array_search($exclId, $projet['exclusives']['view'])) !== false) {
                      unset($projet['exclusives']['view'][$key]);
              }
              Yii::$app->session->set('projet',$projet);
            }
            //add programes to Projet
            if(Yii::$app->request->post()['type'] == 'prog'){
              $progId = Yii::$app->request->post()['tour_id'];
              if(Yii::$app->session->get('projet'))
                $projet = Yii::$app->session->get('projet');
              else $projet = [
                'programes'=> ['select'=>[], 'view'=>[]],
                'exclusives' => ['select'=>[], 'view'=> []]
                ];
              if(!in_array($progId, $projet['programes']['select']))
                  $projet['programes']['select'][] = $progId;
              if(($key = array_search($progId, $projet['programes']['view'])) !== false) {
                      unset($projet['programes']['view'][$key]);
              }
              Yii::$app->session->set('projet',$projet);
            }
            //remove exclusives from Projet
            if (Yii::$app->request->post()['type'] == 'remove-excl') {
                $exclId = Yii::$app->request->post()['remove_id'];
                if(Yii::$app->session->get('projet'))
                  $projet = Yii::$app->session->get('projet');
                else $projet = [
                  'programes'=> ['select'=>[], 'view'=>[]],
                  'exclusives' => ['select'=>[], 'view'=> []]
                  ];
                if(($key = array_search($exclId, $projet['exclusives']['select'])) !== false) {
                        unset($projet['exclusives']['select'][$key]);
                }
                Yii::$app->session->set('projet',$projet);
            }
             //remove program from Projet
            if (Yii::$app->request->post()['type'] == 'remove-prog') {
                $progId = Yii::$app->request->post()['remove_id'];
                if(Yii::$app->session->get('projet'))
                  $projet = Yii::$app->session->get('projet');
                else $projet = [
                  'programes'=> ['select'=>[], 'view'=>[]],
                  'exclusives' => ['select'=>[], 'view'=> []]
                  ];
                if(($key = array_search($progId, $projet['programes']['select'])) !== false) {
                        unset($projet['programes']['select'][$key]);
                }
                Yii::$app->session->set('projet',$projet);
            }
            $html =  $this->renderPartial('//page2016/ajax/votre-ajax');
            return json_encode(['prog'=>count($projet['programes']['select']), 'excl' => count($projet['exclusives']['select']), 'html' => $html]);
        }
        
        $allCountries = Country::find()->select(['code', 'dial_code', 'name_fr'])->orderBy('name_fr')->asArray()->all();
        $allDialCodes = Country::find()->select(['code', 'CONCAT(name_fr, " (+", dial_code, ")") AS xcode'])->where('dial_code!=""')->orderBy('name_fr')->asArray()->all();

        $model = new DevisForm;
        $model->scenario = 'devis';
        

        $model->country = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';
        $model->countryCallingCode = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : 'fr';

        if ($model->load($_POST) && $model->validate()) {
            $wishlist = Yii::$app->session->get('projet');
        $listProg = '0';
        if($wishlist['programes']['select']){
            $listProg = '<br>';
            $where = $wishlist['programes']['select'];
            // var_dump($where);exit;
            $progData= \app\modules\programmes\api\Catalog::items(['where' => ['IN', 'item_id',  $where]]);
            foreach ($progData as $key => $value) {
                $listProg .= '<a href="https://www.amica-travel.com/'.$value->slug.'">'.$value->title.'</a><br>';
            }
        }
        $listExcl = '0';
        if($wishlist['exclusives']['select']){
            $listExcl = '<br>';
            $where = $wishlist['programes']['select'];
            // var_dump($where);exit;
            $exclData= \app\modules\exclusives\api\Catalog::items(['where' => ['IN', 'item_id',  $where]]);
            foreach ($exclData as $key => $value) {
                $listExcl .= '<a href="https://www.amica-travel.com/'.$value->slug.'">'.$value->title.'</a><br>';
            }
        }       
               
            // Save db
            
         
           $data2 = <<<'TXT'
Vos coordonnées:

Voyage Selected: {{ voyage : $voyage }}
Exclusives Selected : {{ excl : $excl }} 
Votre Nom et Prénom: {{ prefix : $prefix }} {{ fname : $fname }} {{ lname : $lname }} 
Votre adresse mail: {{ email : $email }}
Votre pays: {{ country : $country }}
Département, Votre ville: {{ region : $region }} {{ ville : $ville }}

votre projet:

Date d'arrivée approximative: {{ departureDate : $departureDate }}
Date de retour: {{ deretourDate : $deretourDate }}
Durée du voyage: {{ tourLength : $tourLength }}
Destinations: {{ countriesToVisit : $countriesToVisit }} {{ multipays : $multipays }}
Avez-vous déjà acheté votre (vos) billet(s) d’avion internationaux aller-retour ?: {{ howTicket : $howTicket }}
En savoir plus : {{ ticketDetail : $ticketDetail }}
Souhaitez-vous être accompagné pour l'achat de vos billets internationnaux ? {{ helpTicket : $helpTicket }}
Les participants: + de 12 ans {{ numberOfTravelers12 : $numberOfTravelers12 }} Détails d'âges: {{ agesOfTravelers12 : $agesOfTravelers12 }}
- de 12 ans {{ numberOfTravelers2 : $numberOfTravelers2 }} 
- de 2 ans {{ numberOfTravelers0 : $numberOfTravelers0 }}
La forme physique des participants : {{ howTraveler : $howTraveler }}
Quel(s) type(s) d’hébergement aimeriez-vous pour ce voyage ? : {{ hotelTypes : $hotelTypes }}
Combien de chambres souhaitez-vous:
Chambre double avec un grand lit: {{ hotelRoomDbl : $hotelRoomDbl }}
Chambre double avec 2 petit lits: {{ hotelRoomTwn : $hotelRoomTwn }}
Chambre pour 3 personnes: {{ hotelRoomTrp : $hotelRoomTrp }}
Chambre individuelle: {{ hotelRoomSgl : $hotelRoomSgl }}
Repas: {{ mealsIncluded : $mealsIncluded }}
Budget par personne: {{ budget : $budget }}

pour mieux vous connaitre:

Décrivez votre projet, votre vision du voyage et de quelle façon vous souhaitez découvrir vous souhaitez découvrir la ou les destinations choisie(s): {{ message : $message }}

Thématiques: {{ tourThemes : $tourThemes }}
Pouvez-vous nous raconter votre dernier voyage long-courrier ? (destination, type de voyage, expériences, ce que vous avez aimé, ...) {{ howMessage : $howMessage }}
Vos loisirs et passe-temps préférés (ce que vous aimez, ce que vous n’aimez pas…) : {{ howHobby : $howHobby }}
Nous vous rappelons gratuitement pour mieux comprendre votre projet: {{ callback : $callback }}

TXT;
               $datacallback = <<<'TXT'
Votre numéro de téléphone: {{ countryCallingCode : $CallingCode }} {{ phone : $phone }}
Date / heure pour le RDV: {{ callDate : $callDate }} {{ callTime : $callTime }}

TXT;
               $datalast = <<<'TXT'
Si vous êtes recommandé(e) par un ancien client d'Amica, merci de préciser son nom et prénom: {{ reference : $reference }}   
Newsletters: {{ newsletter : $newsletter }}    
TXT;
       if($model->countriesToVisit != 'Multi-pays'){
          $model->multipays = '';
      }else{
          $model->multipays = ': '.$model->multipays;
      }         
               
      if($model->callback == 'Oui'){
                $data2 .= $datacallback;
                $data2 .= $datalast;
                $data2 = str_replace([
                   '$prefix', '$fname', '$lname', '$email', '$country', '$region', '$ville', '$departureDate', '$deretourDate', '$tourLength', '$countriesToVisit', '$multipays', '$numberOfTravelers12', '$numberOfTravelers2', '$numberOfTravelers0', '$agesOfTravelers12', '$message', '$tourThemes', '$hotelTypes', '$hotelRoomDbl', '$hotelRoomTwn', '$hotelRoomTrp', '$hotelRoomSgl', '$mealsIncluded', '$budget', '$callback', '$CallingCode', '$phone', '$callDate', '$callTime','$newsletter', '$whyCountry', '$howTraveler', '$howMessage', '$howHobby', '$howTicket', '$ticketDetail', '$helpTicket', '$job', '$voyage', '$excl', '$reference'
                    ], [
                    $model->prefix, $model->fname, $model->lname, $model->email, $model->country, $model->region, $model->ville, $model->departureDate, $model->deretourDate, $model->tourLength, implode(', ', (array)$model->countriesToVisit), $model->multipays, $model->numberOfTravelers12, $model->numberOfTravelers2, $model->numberOfTravelers0, $model->agesOfTravelers12, $model->message, implode(', ',(array)$model->tourThemes),  implode(', ', (array)$model->hotelTypes), $model->hotelRoomDbl, $model->hotelRoomTwn, $model->hotelRoomTrp, $model->hotelRoomSgl, $model->mealsIncluded, $model->budget, $model->callback, $model->countryCallingCode, $model->phone, $model->callDate, $model->callTime, $model->newsletter == 1 ? 'Oui' : 'Non', $model->whyCountry, $model->howTraveler, $model->howMessage, $model->howHobby, $model->howTicket, $model->ticketDetail, $model->helpTicket, $model->job, $listProg, $listExcl, $model->reference
                    ], $data2);
                }else{
                    
                 $data2 .= $datalast;
                 
                 $data2 = str_replace([
                    '$prefix', '$fname', '$lname', '$email', '$country', '$region', '$ville', '$departureDate', '$deretourDate', '$tourLength', '$countriesToVisit', '$multipays', '$numberOfTravelers12', '$numberOfTravelers2', '$numberOfTravelers0', '$agesOfTravelers12', '$message', '$tourThemes', '$hotelTypes', '$hotelRoomDbl', '$hotelRoomTwn', '$hotelRoomTrp', '$hotelRoomSgl', '$mealsIncluded', '$budget', '$callback', '$newsletter', '$whyCountry', '$howTraveler', '$howMessage', '$howHobby', '$howTicket', '$ticketDetail', '$helpTicket', '$job', '$voyage', '$excl', '$reference'
                    ], [
                    $model->prefix, $model->fname, $model->lname, $model->email, $model->country, $model->region, $model->ville, $model->departureDate, $model->deretourDate, $model->tourLength, implode(', ', (array)$model->countriesToVisit), $model->multipays, $model->numberOfTravelers12, $model->numberOfTravelers2, $model->numberOfTravelers0, $model->agesOfTravelers12, $model->message, implode(', ',(array)$model->tourThemes),  implode(', ', (array)$model->hotelTypes), $model->hotelRoomDbl, $model->hotelRoomTwn, $model->hotelRoomTrp, $model->hotelRoomSgl, $model->mealsIncluded, $model->budget, $model->callback, $model->newsletter == 1 ? 'Oui' : 'Non', $model->whyCountry, $model->howTraveler, $model->howMessage, $model->howHobby, $model->howTicket, $model->ticketDetail, $model->helpTicket, $model->job, $listProg, $listExcl, $model->reference
                    ], $data2);
                }
         
                $this->saveInquiry($model, 'Form-liste-envies', $data2);
                if($model->newsletter){
            $data = [
                    'firstname' => $model->fname,
                    'lastname' => $model->lname,
                    'codecontry' => $model->country,
                    'source' => 'newsletter',
                    'sex' => $model->prefix,
                    'newsletter_insc' => date('d/m/Y'),
                    'statut' => 'prospect',
                    'birmanie' => false,
                    'vietnam' => false,
                    'cambodia' => false,
                    'laos' => false
                ];
            $this->addContactToMailjet($model->email, '1688900', $data);
        }
                // If he subscribes to our newsletter
              //  if ($model->newsletter == 1) $this->saveNlsub($model, 'Form-devis');
            

            // Email me
            $this->notifyAmica('Devis from ' . $model->email, '//page2016/email_template', ['data2' => $data2]);
            $this->confirmAmica($model->email);
            // Redir
            return Yii::$app->response->redirect(DIR.'merci?from=liste-d-envies&id='.$this->id_inquiry);
        }

        return $this->render('//page2016/votre-projet', [
                    'model' => $model,
					'theEntry' => $theEntry,	
                    'allCountries' => $allCountries,
                    'allDialCodes' => $allDialCodes,
        ]);
    }
    
    public function actionQuandCommentCombienAboutUs(){
        $this->layout = 'main-quand-comment-combien';
        $this->metaIndex = 0;
        $this->metaFollow = 0;
        $theEntry = \yii\easyii\modules\page\api\Page::get('quand-comment-combien');
        if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
        $this->getSeo($theEntry->model->seo);
        $infos_pratiques = \app\modules\libraries\api\Catalog::items(['where'=>['category_id'=>18],'pagination' => ['pageSize'=>0],'orderBy'=>'item_id']);
        
        return $this->render('//page2016/quand-comment-combien', [
                    'theEntry' => $theEntry,
                    'infos_pratiques' => $infos_pratiques,	
                    
        ]);
    }

    

    public function saveInquiry($model, $form, $data2 = '') {
        // Do not save test
        if ($model->email == 'huan@huanh.com') return false;
        $inquiry = new Inquiry;
        $inquiry->ip = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : Yii::$app->request->getUserIP();
        $inquiry->ua = Yii::$app->request->getUserAgent();
        $inquiry->ref = Yii::$app->session->get('ref', '-');
        $inquiry->name = $model->fname . ' ' . $model->lname;
        $inquiry->email = $model->email;
        $inquiry->data = serialize($model->getAttributes(null, ['verificationCode']));
        if ($data2 != '') {
            $inquiry->data2 = $data2;
        }
        $inquiry->site_id = SITE_ID;
        $inquiry->form_id = 0;
        $inquiry->form_name = $form;
        $inquiry->created_at = NOW;
        $inquiry->updated_at = NOW;
        $inquiry->status = 'on';
        $inquiry->save();
		
		// get id_inquiry
		$this->id_inquiry = $inquiry->id;
    }
    
    public function saveNlsub($model, $form) {
        // Do not save test
        if ($model->email == 'huan@huanh.com') return false;
        $nlsub = new Nlsub;
        $nlsub->ip = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : Yii::$app->request->getUserIP();
        $nlsub->ua = Yii::$app->request->getUserAgent();
        $nlsub->ref = Yii::$app->session->get('ref', '-');
        $nlsub->prefix = $model->prefix;
        $nlsub->fname = $model->fname;
        $nlsub->lname = $model->lname;
        $nlsub->email = $model->email;
        $nlsub->country = $model->country;
        $nlsub->site_id = SITE_ID;
        $nlsub->form = $form;
        $nlsub->created_at = NOW;
        $nlsub->updated_at = NOW;
        $nlsub->status = 'on';
        $nlsub->save();
    }
    
     public function notifyAmica($subject = '', $template = '', $params = [], $email='Amica FR <inquiry-fr@amicatravel.com>') {
        $mgClient = new Mailgun(MAILGUN_API_KEY);
        $result = $mgClient->sendMessage(MAILGUN_API_DOMAIN, [
            'from' => 'Amica-FR <noreply-fr@amicatravel.com>',
            'to' =>  $email,
            'bcc' => ['Nguyen Linh. <nguyen.linh@amicatravel.com>', 'Info Arnaud. <arnaud.l@amicatravel.com>'],
             'subject' => $subject,
            'text' => '',
            'html' => $this->renderPartial($template, $params),
                ]
        );
    }

    public function getWatermarkimage($image)
    {

        //  $img = DIR.'upload/home/lolo-noir-cao-bang-vietnam.jpg';
        // var_dump(Yii::getAlias('@webroot'));exit;
        $wr = Yii::getAlias('@webroot');
        // $img = $wr.'/upload/home/baie-dalong-vietnam.jpg';
        $img = $wr . $image;
        $img_size = getimagesize($img);
        $w = $img_size[0] - 110;
        $h = $img_size[1] - 74;
        $img_w = $wr . '/assets/img/page2016/logo_watermark_new.png';
        // $img_w = DIR.'assets/img/page2016/watermark_logo.jpg';
        $src = Yii::$app->easyimage->getUrl($img, ['watermark' => ['image' => $img_w, 'offset_x' => $w, 'offset_y' => 7]], $absolute = false);
        //var_dump($src);exit;

        return str_replace('\\', '/', $src);


    }

    public function confirmAmica($email = '', $template = '//page2016/email_fr_confirm', $params = []) {
        $mgClient = new Mailgun(MAILGUN_API_KEY);
        
        $result = $mgClient->sendMessage(MAILGUN_API_DOMAIN, [
            'from' => 'Amica Travel <noreply-fr@amicatravel.com>',
            'to' => $email,
            'subject' => 'Votre demande a été envoyée',
            'text' => '',
            'html' => $this->renderPartial($template, $params),
                ]
        );
    }

    public function actionImsprint($id = 0, $code = 0) {
        $key = 'hu4n12bb';
        if ($id == 0) {
            die('NOT OK');
        }
        $handle = fopen('https://my.amicatravel.com/products/x/' . $id, 'r');
        //$handle = fopen('http://www.etourhome.com/products/x/' . $id, 'r');
        //$handle = fopen('http://www.w3schools.com/php/func_filesystem_fopen.asp', 'r');
        $data = stream_get_contents($handle);
        //$data = Security::decrypt($rawData, $key);

        $theProduct = unserialize($data);
        if ($code != md5($theProduct['created_at'])) {
            die('NOT OK');
        }
        $theProduct['createdBy']['fname'] = $this->vn_str_filter($theProduct['createdBy']['fname']);
        $theProduct['createdBy']['lname'] = $this->vn_str_filter($theProduct['createdBy']['lname']);
        $data = unserialize($data);
        
        
      //  var_dump($data['days'][0]['body']);exit;
       // echo "<pre>";
       // print_r($data['days'][0]['body']);
       // exit;

        return $this->renderPartial('//page2016/imsPrint', [
                    'theProduct' => $theProduct
        ]);
    }

    public function actionImsprintB2b($id = 0, $code = 0) {
        $key = 'hu4n12bb';
        if ($id == 0) {
            die('NOT OK');
        }

        $handle = fopen('https://my.amicatravel.com/products/x/' . $id, 'r');
        $data = stream_get_contents($handle);

        $theProduct = unserialize($data);
        if ($code != md5($theProduct['created_at'])) {
            die('NOT OK');
        }
        $theProduct['createdBy']['fname'] = $this->vn_str_filter($theProduct['createdBy']['fname']);
        $theProduct['createdBy']['lname'] = $this->vn_str_filter($theProduct['createdBy']['lname']);
        $data = unserialize($data);


        return $this->renderPartial('//page2016/imsPrintB2b', [
                    'theProduct' => $theProduct
        ]);
    }
     public function actionImsprintB2bEn($id = 0, $code = 0) {
        $key = 'hu4n12bb';
        if ($id == 0) {
            die('NOT OK');
        }

        $handle = fopen('https://my.amicatravel.com/products/x/' . $id, 'r');
        $data = stream_get_contents($handle);

        $theProduct = unserialize($data);
        if ($code != md5($theProduct['created_at'])) {
            die('NOT OK');
        }
        $theProduct['createdBy']['fname'] = $this->vn_str_filter($theProduct['createdBy']['fname']);
        $theProduct['createdBy']['lname'] = $this->vn_str_filter($theProduct['createdBy']['lname']);
        $data = unserialize($data);
       // print_r($data['conditions']);exit;
       //var_dump($data);exit;
        return $this->renderPartial('//page2016/imsPrintB2b_en', [
                    'theProduct' => $theProduct
        ]);
    }

    function vn_str_filter($str) {

        $unicode = array(
            'a' => 'á|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|�?|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|�?|õ|�?|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|�?|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => '�?|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => '�?',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => '�?|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|�?|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => '�?|Ỳ|Ỷ|Ỹ|Ỵ',
        );

        foreach ($unicode as $nonUnicode => $uni) {

            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }

        return $str;
    }

    public function actionRdvSurParis()
    {
        $this->layout = 'main-form';
        $theEntry = Page::get(32);
        $this->root = $theEntry;
        $this->entry = $theEntry;
        if (!$theEntry) throw new HttpException(404, 'Page ne pas trouvé!');
        $this->getSeo($theEntry->model->seo);
        $model = new ContactForm;
        $model->scenario = 'rdv-surparis';

        if ($model->load($_POST) && $model->validate()) {
            $dataEmail = <<<'TXT'
                            Votre nom et prénom: {{ prefix : $prefix }} {{ fname : $fname }} {{ lname : $lname }}
                            Votre adresse mail: {{ email : $email }} Date de naissance: {{ age : $age }}
                            Votre RDV souhaité sur Paris: {{ period : $period }}
                            Votre numéro téléphone: {{ phone : $phone }}
                            Date / heure pour le RDV: {{ callDate : $callDate }}
                            Votre message: {{ message : $message }}
                            Votre newsletter: {{ newsletter : $newsletter }}
                            Si vous êtes recommandé(e) par un ancien client d'Amica, merci de préciser son nom et prénom: {{ reference : $reference }}
TXT;
            $dataEmail = str_replace([
                '$prefix', '$fname', '$lname', '$email', '$period', '$phone', '$callDate', '$message', '$newsletter', '$reference', '$age'
            ], [
                $model->prefix, $model->fname, $model->lname, $model->email, $model->period, $model->phone, $model->callDate, $model->message, $model->newsletter, $model->reference, $model->age
            ], $dataEmail);

           $this->saveInquiry($model, 'Form-rdv-paris', $dataEmail);

            if ($model->newsletter) {
                $data = [
                    'firstname' => $model->fname,
                    'lastname' => $model->lname,
                    'codecontry' => $model->country,
                    'source' => 'newsletter',
                    'sex' => $model->prefix,
                    'newsletter_insc' => date('d/m/Y'),
                    'statut' => 'prospect',
                    'birmanie' => false,
                    'vietnam' => false,
                    'cambodia' => false,
                    'laos' => false
                ];
                $this->addContactToMailjet($model->email, '1688900', $data);
            }
            $this->notifyAmica('RDV sur Paris from ' . $model->email, '//page2016/email_template', ['data2' => $dataEmail]);
            $this->confirmAmica($model->email);
            return Yii::$app->response->redirect(DIR . 'merci?from=rdv-paris&id=' . $this->id_inquiry);
        }

        $listData = array();
        for ($i = 1; $i <= 20; $i++) {
            $key = $i . 'er arrondissement';
            $listData[$key] = $i . 'er arrondissement';
        }

        return $this->render(IS_MOBILE ? '//page2016/mobile/rdv_mobile' : '//page2016/rdv-sur-paris', [
            'theEntry' => $theEntry,
            'model' => $model,
            'listData' => $listData
        ]);
    }

    // Function get breadcrumb
    protected function getBreadCrumb($params = '') {
        $arr = array();
        $theEntry = array();

        switch ($params) {
            case 'destination':
                $theEntry = Page::get('destinations');
                break;
            case 'breadcrumb-3':
                $theEntry = \app\modules\destinations\api\Catalog::cat(SEG1);
                break;
            case 'breadcrumb-4':
                $theEntry = \app\modules\destinations\api\Catalog::cat(SEG1 . '/' . SEG2);
                break;
            default:
                $theEntry = Page::get('destinations');
                break;
        }

        $arr['title'] = $theEntry->model->seo->breadcrumb ?: '';
        $arr['url'] = $theEntry->model->slug ?: '#';
        return $arr;
    }

    protected function setCacheForBlogs($theEntry, $params) {

        $key= 'data-cache-' . $params . '-blogs';
        $cache = Yii::$app->cache;

        // try retrieving $data from cache
        $data = $cache->get($key);
        if (empty($data)) {
            $arrBlog = $dataBlogSelected = array();

            /* $dataBlogSelected get data default blog */
            if(!empty($theEntry->data->blogs)) {
                $dataBlogSelected = $theEntry->data->blogs;
            } else {
                $dataBlogSelected = ["13749", "13849", "13502"];
                switch (SEG1) {
                    case 'vietnam':
                        // LOCAL: 13
                        // DEMO SITE: 20
                        // ON SITE: 29
                        if(!empty(\app\modules\modulepage\api\Catalog::get(29)->data->blogs)) {
                            $dataBlogSelected = \app\modules\modulepage\api\Catalog::get(29)->data->blogs;
                        }
                        break;
                    case 'cambodge':
                        // LOCAL: 17
                        // DEMO SITE: 22
                        // ON SITE: 27
                        if(!empty(\app\modules\modulepage\api\Catalog::get(27)->data->blogs)) {
                            $dataBlogSelected = \app\modules\modulepage\api\Catalog::get(27)->data->blogs;
                        }
                        break;
                    case 'laos':
                        // LOCAL: 15
                        // DEMO SITE: 24
                        // ON SITE: 28
                        if(!empty(\app\modules\modulepage\api\Catalog::get(28)->data->blogs)) {
                            $dataBlogSelected = \app\modules\modulepage\api\Catalog::get(28)->data->blogs;
                        }
                        break;
                    case 'birmanie':
                        // LOCAL: 16
                        // DEMO SITE: 26
                        // ON SITE: 30
                        if(!empty(\app\modules\modulepage\api\Catalog::get(30)->data->blogs)) {
                            $dataBlogSelected = \app\modules\modulepage\api\Catalog::get(30)->data->blogs;
                        }
                        break;
                    default:
                        $dataBlogSelected = ["13749", "13849", "13502"];
                        break;
                }
            }

            /* $arrBlog contain data for frontend */
            foreach ($dataBlogSelected as $keyBlog => $valueBlog) {
                // Get all blogs
                $arrBlog[$keyBlog] = $this->getDataPost($valueBlog);
                $arrBlog[$keyBlog]['cat_name'] = $this->getCategoryName($arrBlog[$keyBlog]['categories'][0])['name'];
                $featuredMediaData = $this->getFeatureImage($arrBlog[$keyBlog]['featured_media']);
                if(isset($featuredMediaData['data']['status']) == 404) {
                    $arrBlog[$keyBlog]['alt_text'] = '';
                    $arrBlog[$keyBlog]['src'] = '';
                } else {
                    $featuredMediaData = $this->getFeatureImage($arrBlog[$keyBlog]['featured_media'])['media_details']['sizes']['barouk_list-thumb'];
                    $arrBlog[$keyBlog]['alt_text'] = $this->getFeatureImage($arrBlog[$keyBlog]['featured_media'])['alt_text'];
                    $arrBlog[$keyBlog]['src'] = '/timthumb.php?src=' . $featuredMediaData['source_url'] . '&w=300&h=200&zc=1&q=80';
                }
            }
            // $data is not found in cache, calculate it from scratch
            $data = $arrBlog;

            // store $data in cache so that it can be retrieved next time
            $cache->set($key, $data);
        }

        // $data is available here
        return $data;
    }

    protected function deleteAllCache(){
        Yii::$app->cache->flush();
    }

    public function actionForIms($id = 0)
    {
        $key = 'hu4n12bb';
        $result = '';
        $theInquiries = Yii::$app->db->createCommand('SELECT * FROM at_inquiries WHERE id>:id ORDER BY id LIMIT 10', [':id'=>$id])->queryAll();

        $str = serialize($theInquiries);
        $arr = str_split($str);
        foreach ($arr as $chr) {
            $result .= '-'.ord($chr);
        }
        echo $result;
    }
    public function actionAjaxResultMenu(){

        $key= 'data-cache-menu-021';

        $cache = Yii::$app->cache;

        // try retrieving $data from cache
        $cache->delete($key);
        $data = $cache->get($key);
        if(empty($data)){
            
            if(Yii::$app->request->post('flag') == 'ok'){
                $data = $this->renderPartial('//page2016/_inc_sub_menu');
                $cache->set($key, $data);
                //return $this->renderPartial('//page2016/_inc_sub_menu');
            }
        }
        
        return $data;
    }
    public function actionPromotionBasseSaisonAboutUs(){
        
        $this->metaIndex = 0;
        $this->metaFollow = 0;
        $theEntry = \yii\easyii\modules\page\api\Page::get('promotion-basse-saison');
        if (!$theEntry) throw new HttpException(404, 'Oops! Cette page n\'existe pas.');
        $this->getSeo($theEntry->model->seo);
        
        return $this->render(IS_MOBILE ? '//page2016/mobile/promotion-basse-saison' : '//page2016/promotion-basse-saison', [
                    'theEntry' => $theEntry,
        ]);
    }
}
