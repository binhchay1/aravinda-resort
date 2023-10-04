<?php
namespace app\modules\modulepage\controllers;

use Yii;
use yii\easyii\behaviors\StatusController;
use yii\web\UploadedFile;
use yii\helpers\Html;

use yii\easyii\components\Controller;
use app\modules\modulepage\models\Category;
use app\modules\modulepage\models\Item;
use yii\easyii\helpers\Image;
use yii\easyii\behaviors\SortableDateController;
use yii\widgets\ActiveForm;
use dosamigos\selectize\SelectizeTextInput;
// use yii\easyii\widgets\DateTimePicker;
use yii\helpers\ArrayHelper;
use kartik\file\FileInput;
use yii\helpers\Url;
use yii\helpers\Json;
use kartik\date\DatePicker;
use dosamigos\selectize\SelectizeDropDownList;
use app\modules\modulepage\api\Catalog;
use yii\easyii\models\SeoText;
use yii\helpers\FileHelper;
use yii\httpclient\Client;

class ItemsController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => SortableDateController::className(),
                'model' => Item::className(),
            ],
            [
                'class' => StatusController::className(),
                'model' => Item::className()
            ]
        ];
    }

    public function actionIndex($id)
    {
        if(!($model = Category::findOne($id))){
            return $this->redirect(['/admin/'.$this->module->id]);
        }

        return $this->render('index', [
            'model' => $model
        ]);
    }


    public function actionCreate($id)
    {
        if(!($category = Category::findOne($id))){
            return $this->redirect(['/admin/'.$this->module->id]);
        }

        $model = new Item;

        if ($model->load(Yii::$app->request->post())) {
            if(Yii::$app->request->isAjax){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            else {
                if(isset(Yii::$app->request->post('Data')['countries'])){
                $ct = Yii::$app->request->post('Data')['countries'];
                     if($ct){
                        $ct = implode(', ', $ct);
                        Yii::$app->request->post('Data')['countries'] = $ct;
                    }
                }
                $model->category_id = $category->primaryKey;
                $model->data = Yii::$app->request->post('Data');

                if (isset($_FILES) && $this->module->settings['itemThumb']) {
                    $model->image = UploadedFile::getInstance($model, 'image');
                    if ($model->image && $model->validate(['image'])) {
                        $model->image = Image::upload($model->image, 'modulepage');
                    } else {
                        $model->image = '';
                    }
                }
                if ($model->save()) {
                    $this->flash('success', Yii::t('easyii/modulepage', 'Item created'));
                    return $this->redirect(['/admin/'.$this->module->id.'/items/edit/', 'id' => $model->primaryKey]);
                } else {
                    $this->flash('error', Yii::t('easyii', 'Create error. {0}', $model->formatErrors()));
                    return $this->refresh();
                }
            }
        }
        else {
            return $this->render('create', [
                'model' => $model,
                'category' => $category,
                'dataForm' => $this->generateForm($category->fields)
            ]);
        }
    }

    public function getModulesPage(){
        
        $programes = \app\modules\programmes\api\Catalog::items(['pagination' => ['pageSize' => 0], 'where' => ['status' => 1]]);
        $programes = ArrayHelper::map($programes, function ($element) {
            return $element->model->item_id;
        }, 'title');
        return [
            'programes' => $programes,
        ];
    }

    protected function getDataFromBlogAmica()
    {
        $dataPosts = array();
        $dataPosts['posts'] = $this->getDataAllPostFromBlogAmica();

        return $dataPosts;
    }

    protected function getDataAllPostFromBlogAmica()
    {
        $key_cache = 'data-cache-all-blog-modified-from-2016';
        $cache = Yii::$app->cache;

        // try retrieving $data from cache
        $data = $cache->get($key_cache);

        if (empty($data)) {
            $hostName = 'https://blog.amica-travel.com';
            $arrData = array();
            // $data is not found in cache, calculate it from scratch
            $url1 = $hostName . '/wp-json/wp/v2/posts?per_page=90&page=1&post_modified_gmt=2016-01-01T00:00:01';
            $data1 = $this->sendRequestToBlogAmica($url1);
            foreach ($data1 as $key => $value) {
                if(isset($value['id'])) {
                    $arrData[$value['id']] = $value['title']['rendered'];
                }
            }

            $url1 = $hostName . '/wp-json/wp/v2/posts?per_page=90&page=1&post_modified_gmt=2016-01-01T00:00:01';
            $data1 = $this->sendRequestToBlogAmica($url1);
            foreach ($data1 as $key => $value) {
                if(isset($value['id'])) {
                    $arrData[$value['id']] = $value['title']['rendered'];
                }
            }

            $url1 = $hostName . '/wp-json/wp/v2/posts?per_page=90&page=2&post_modified_gmt=2016-01-01T00:00:01';
            $data1 = $this->sendRequestToBlogAmica($url1);
            foreach ($data1 as $key => $value) {
                if(isset($value['id'])) {
                    $arrData[$value['id']] = $value['title']['rendered'];
                }
            }

            $url1 = $hostName . '/wp-json/wp/v2/posts?per_page=90&page=3&post_modified_gmt=2016-01-01T00:00:01';
            $data1 = $this->sendRequestToBlogAmica($url1);
            foreach ($data1 as $key => $value) {
                if(isset($value['id'])) {
                    $arrData[$value['id']] = $value['title']['rendered'];
                }
            }

            $url1 = $hostName . '/wp-json/wp/v2/posts?per_page=90&page=4&post_modified_gmt=2016-01-01T00:00:01';
            $data1 = $this->sendRequestToBlogAmica($url1);
            foreach ($data1 as $key => $value) {
                if(isset($value['id'])) {
                    $arrData[$value['id']] = $value['title']['rendered'];
                }
            }

            $url1 = $hostName . '/wp-json/wp/v2/posts?per_page=90&page=5&post_modified_gmt=2016-01-01T00:00:01';
            $data1 = $this->sendRequestToBlogAmica($url1);
            foreach ($data1 as $key => $value) {
                if(isset($value['id'])) {
                    $arrData[$value['id']] = $value['title']['rendered'];
                }
            }

            $data = $arrData;

            // store $data in cache so that it can be retrieved next time
            $cache->set($key_cache, $data);
        }
        // $data is available here
        return $data;
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

    public function actionEdit($id)
    {
        if(!($model = Item::findOne($id))){
            return $this->redirect(['/admin/'.$this->module->id]);
        }
        $cat = Category::findOne($model->category_id);
       $path = explode("/", $model->slug);
        Yii::$app->session->set('uploadFolder', Yii::$app->session->get('moduleName').'/'.end($path));
        if ($model->load(Yii::$app->request->post())) {
             $file = UploadedFile::getInstanceByName('file');
            if(Yii::$app->request->isAjax){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            else {

                $request = Yii::$app->request->post('Data');

                // Save destinations with order
                foreach($cat->fields as $field){
                    $exts = json_decode(Yii::$app->request->post('exts-order-'.$field->type));
                    $request = Yii::$app->request->post('Data');
                    $request[$field->type] = $exts;
                }
                $dir = Yii::getAlias('@webroot').'/uploads/';
                if (isset($_FILES)) {
                    foreach($_FILES as $key=> $file){
                        if($file['tmp_name']){
                            $url = $this->actionUploadData($file);
                            $request[$key] = $url;
                        } else{
                            if(isset($model->data->$key))
                            $request[$key] = $model->data->$key;
                        }
                    }
                }
                $model->data = $request;
                if ($model->save()) {
                    $this->deleteCacheBlogs();
                    $this->flash('success', 'Item updated');
                    return $this->redirect(['/admin/'.$this->module->id.'/items/edit', 'id' => $model->primaryKey]);
                } else {
                    $this->flash('error', Yii::t('easyii', 'Update error. {0}', $model->formatErrors()));
                    return $this->refresh();
                }
            }
        }
        else {
            return $this->render('edit', [
                'model' => $model,
                'dataForm' => $this->generateForm($model->category->fields, $model->data)
            ]);
        }
    }

    public function actionPhotos($id)
    {
        if(!($model = Item::findOne($id))){
            return $this->redirect(['/admin/'.$this->module->id]);
        }

        return $this->render('photos', [
            'model' => $model,
        ]);
    }

    public function actionClearImage($id)
    {
        $model = Item::findOne($id);

        if($model === null){
            $this->flash('error', Yii::t('easyii', 'Not found'));
        }
        elseif($model->image){
            $model->image = '';
            if($model->update()){
                @unlink(Yii::getAlias('@webroot').$model->image);
                $this->flash('success', Yii::t('easyii', 'Image cleared'));
            } else {
                $this->flash('error', Yii::t('easyii', 'Update error. {0}', $model->formatErrors()));
            }
        }
        return $this->back();
    }

    public function actionDelete($id)
    {
        if(($model = Item::findOne($id))){
            $model->delete();
            $this->deleteCacheBlogs();
        } else {
            $this->error = Yii::t('easyii', 'Not found');
        }
        return $this->formatResponse(Yii::t('easyii/modulepage', 'Item deleted'));
    }

    public function actionUp($id, $category_id)
    {
        return $this->move($id, 'up', ['category_id' => $category_id]);
    }

    public function actionDown($id, $category_id)
    {
        return $this->move($id, 'down', ['category_id' => $category_id]);
    }

    public function actionOn($id)
    {
        return $this->changeStatus($id, Item::STATUS_ON);
    }

    public function actionOff($id)
    {
        return $this->changeStatus($id, Item::STATUS_OFF);
    }

    private function generateForm($fields, $data = null)
    {
        $result = '';
        foreach($fields as $field)
        {
            $value = !empty($data->{$field->name}) ? $data->{$field->name} : null;
            if ($field->type === 'string') {
                $result .= '<div class="form-group"><label>'. $field->title .'</label>'. Html::input('text', "Data[{$field->name}]", $value, ['class' => 'form-control']) .'</div>';
            }
            elseif ($field->type === 'text') {
                $result .= '<div class="form-group"><label>'. $field->title .'</label>'. Html::textarea("Data[{$field->name}]", $value, ['class' => 'form-control redactor']) .'</div>';
            }
            elseif ($field->type === 'boolean') {
                $result .= '<div class="checkbox"><label>'. Html::checkbox("Data[{$field->name}]", $value, ['uncheck' => 0]) .' '. $field->title .'</label></div>';
            }
            elseif ($field->type === 'select') {
                $options = ['' => Yii::t('easyii/tours', 'Select')];
                foreach($field->options as $option){
                    $options[$option] = $option;
                }
                $result .= '<div class="form-group"><label>'. $field->title .'</label><select name="Data['.$field->name.']" class="form-control">'. Html::renderSelectOptions($value, $options) .'</select></div>';
            }
            elseif ($field->type === 'selectTags') {
                // $options = ['' => Yii::t('easyii/tours', 'Select')];
                // foreach($field->options as $option){
                //     $options[$option] = $option;
                // }
                $result .= '<div class="control-group">
                    <label for="select-beast">'.$field->title.'</label>
                   <select name="'."Data[{$field->name}][]".'" multiple class="form-control selectTags">'. Html::renderSelectOptions($value, Yii::$app->params['tsDestinationList']) .'</select>
                </div>';

            }
            elseif ($field->type === 'exclusives') {
                $result .= '<div class="control-group">
                    <label for="select-beast">'.$field->title.'</label>
                   <select id="list-exts" name="'."Data[{$field->name}][]".'" multiple class="form-control selectTags">'. Html::renderSelectOptions($value, $this->getModulesPage()['exclusives']) .'</select>
                </div>';

            }
            elseif ($field->type === 'items') {
                $options = $this->getModulesPage()['programes'];
               
                $result .= '<div class="control-group">
                    <label for="select-beast">'.$field->title.'</label>
                   <select name="'."Data[{$field->name}][]".'" multiple  class="form-control selectTags select-items">'. Html::renderSelectOptions($value, $options) .'</select>
                   <input style="display: none;" class="exts-order" name="exts-order-items" />
                </div>';
                
            }
            elseif ($field->type === 'blogs') {
                $result .= '<div class="control-group">
                    <label for="select-beast">' . $field->title . '</label>
                   <select id="list-exts" name="' . "Data[{$field->name}][]" . '" multiple class="form-control selectTags">' . Html::renderSelectOptions($value, $this->getModulesPage()['blogs']['posts']) . '</select>
                </div>';
            }
            elseif ($field->type === 'temoignages') {
                $result .= '<div class="control-group">
                    <label for="select-beast">' . $field->title . '</label>
                    <select id="list-exts" name="' . "Data[{$field->name}][]" . '" multiple class="form-control selectTags">' . Html::renderSelectOptions($value, $this->getModulesPage()['temoignages']) . '</select>
                </div>';
            }
            elseif ($field->type === 'destinations') {

                $result .= '<div class="control-group">
                    <label for="select-beast">'.$field->title.'</label>
                   <select name="'."Data[{$field->name}][]".'" multiple class="form-control selectTags">'. Html::renderSelectOptions($value, $this->getModulesPage()['destinations']) .'</select>
                </div>';

            }
            elseif ($field->type === 'checkbox') {
                $options = '';
                foreach($field->options as $option){
                    $checked = $value && in_array($option, $value);
                    $options .= '<br><label>'. Html::checkbox("Data[{$field->name}][]", $checked, ['value' => $option]) .' '. $option .'</label>';
                }
                $result .= '<div class="checkbox well well-sm"><b>'. $field->title .'</b>'. $options .'</div>';
            }
            elseif ($field->type === 'tags') {
                $result .= '<div class="form-group"><label>'. $field->title .'</label>'. Html::input('text', "Data[{$field->name}]", $value, ['class' => 'form-control tagsInput']) .'</div>';
            }
            elseif ($field->type === 'tagsDate') {
                $result .= '<div class="form-group"><label>'. $field->title .'</label>'. Html::input('text', "Data[{$field->name}]", $value, ['class' => 'form-control tagsInput datetimepicker']) .'</div>';

            }
            elseif ($field->type === 'hotel') {
                $hotelSlug = Catalog::cat('hotels-list')->model;
                $options = Category::find()->where(['depth' => 1])->andWhere('lft > '.$hotelSlug->lft.' and rgt < '.$hotelSlug->rgt)->orderBy('lft ASC')->asArray()->all();
                $options = ArrayHelper::map($options, 'slug', 'title');
                $options = ArrayHelper::merge([''=> 'Select'], $options);
                $result .= '<div class="form-group"><label>'. $field->title .'</label><select name="Data['.$field->name.']" class="form-control">'. Html::renderSelectOptions($value, $options) .'</select></div>';
            }
            elseif ($field->type === 'extension') {
                $extensionSlug = Catalog::cat('extensions-list')->model;
                $options = Category::find()->where(['depth' => 1])->andWhere('lft > '.$extensionSlug->lft.' and rgt < '.$extensionSlug->rgt)->orderBy('lft ASC')->asArray()->all();
                $options = ArrayHelper::map($options, 'slug', 'title');
                $options = ArrayHelper::merge([''=> 'Select'], $options);
                $result .= '<div class="form-group"><label>'. $field->title .'</label><select name="Data['.$field->name.']" class="form-control">'. Html::renderSelectOptions($value, $options) .'</select></div>';
            }
            elseif ($field->type === 'file') {
                $nameFile = $field->name;
                $initialPreview = '';
                $initialCaption  = false;
                $showPreview = false;
                if(isset($data->$nameFile)){
                    $initialPreview = $data->$nameFile;
                    $initialCaption = $data->$nameFile;
                    $showPreview = true;
                }
                $result .= '<div class="form-group field-item-image"><label>'. $field->title .'</label>'.
                FileInput::widget([
                'name' => $nameFile,
                'pluginOptions' => [
                   'uploadUrl' => Url::to(['/admin/whoarewe/items/upload-data']),
                    'showUpload' => false,
                    'showPreview' => $showPreview,
                    'initialPreview'=>[
                        $initialPreview,
                    ],
                    'previewFileType' => 'any',
                    'initialPreviewAsData'=>true,
                    'initialCaption'=>$initialCaption,
                ]
            ]).
                '</div>';
            }
            elseif($field->type === 'date') {
                $result .= '<label>'.$field->title .'</label>'.
                DatePicker::widget([
                    'name' => $field->name,
                    'value' => date('d-m-Y', strtotime('+2 days')),
                    'options' => ['placeholder' => 'Select issue date ...'],
                    'pluginOptions' => [
                        'format' => 'dd-mm-yyyy',
                        'todayHighlight' => true
                    ]
                ]);
            }
            elseif (in_array($field->type, ['tourTypes', 'tTourThemes', 'tTourJours'])) {
                switch ($field->type) {
                    case 'tourTypes':
                        $options = Yii::$app->params['tTourTypes'];
                        break;
                    case 'tTourThemes':
                        $options = Yii::$app->params['tTourThemes'];
                        break;
                    case 'tTourJours':
                        $options = Yii::$app->params['tTourJours'];
                        break;
                };
                $options = ['' => 'Select'] + $options;
                $result .= '<div class="form-group"><label>'. $field->title .'</label><select name="Data['.$field->name.']" class="form-control">'. Html::renderSelectOptions($value, $options) .'</select></div>';
            }
        }
        return $result;
    }

     public function actionUploadData($file){
                $dir = \Yii::getAlias('@webroot').'/uploads/'.\Yii::$app->session->get('uploadFolder');
                    if (!is_dir($dir)) {
                        FileHelper::createDirectory($dir);
                    }
                    if ($file) {
                        $uid = uniqid(time(), true);
                        $fileName = $file['name'];
                        if(strpos($file['name'], '.pdf') === false){
                            $fileName = explode('.', $file['name'])[0].'-'.$uid . '.' . explode('.', $file['name'])[1];
                        }

                        $filePath = $dir .DIRECTORY_SEPARATOR. $fileName;
                        move_uploaded_file($file['tmp_name'],  $filePath);
                        return Yii::getAlias('@web').'/uploads/'.\Yii::$app->session->get('uploadFolder').'/'.$fileName ;
                    }
                    return '';
    }

    public function actionImageDelete($name)
    {
        $directory = \Yii::getAlias('@webroot/uploads/photos') . DIRECTORY_SEPARATOR;
        if (is_file($directory . DIRECTORY_SEPARATOR . $name)) {
            unlink($directory . DIRECTORY_SEPARATOR . $name);
        }
        $files = FileHelper::findFiles($directory);
        $output = [];
        foreach ($files as $file){
            $path = '/img/temp/' . Yii::$app->session->id . DIRECTORY_SEPARATOR . basename($file);
            $output['files'][] = [
                'name' => basename($file),
                'size' => filesize($file),
                "url" => $path,
                "thumbnailUrl" => $path,
                "deleteUrl" => 'image-delete?name=' . basename($file),
                "deleteType" => "POST"
            ];
        }
        return Json::encode($output);
    }

     public function actionPhotosMobile($id)
    {
        $model = Item::findOne($id);
        if(!$model){
            return $this->redirect(['/admin/'.$this->module->id]);
        }

        return $this->render('photos_mobile', [
            'model' => $model,
        ]);
    }

     public function actionLoadData(){
        $data = Yii::$app->db2->createCommand("SELECT * FROM at_ch_testi WHERE status = 'on' and id > 273")->queryAll();
        foreach ($data as $key => $dt) {
            $model = new Item;
            $list = [];
            if($dt['visited_countries']){
                $list['countries'] = explode(',',$dt['visited_countries']);
            }
            if($dt['type_of_tour']){
                $list['ttourtypes'] = $dt['type_of_tour'];
            }
            if($dt['idea_of_tour']){
                $list['ttourthemes'] = $dt['idea_of_tour'];
            }
            $list['nameclient'] = $dt['client_name'];
            $list['emailclient'] = $dt['client_email'];
            $list['phoneclient'] = $dt['client_phone'];
            $list['countryclient'] = $dt['country_code'];
            $list['cityclient'] = $dt['city'];
            $list['regionclient'] = $dt['region'];
            $list['from'] = $dt['visited_from'];
            $list['to'] = $dt['visited_until'];
            $model->category_id = 13;
            $model->title = 13;
            $model->title = $dt['title'];
            $model->sub_title = '';
            $model->summary = $dt['summary'];
            $model->description = $dt['body'];
            $model->data = $list;
            $model->image = $dt['image'];
            $model->slug = $dt['url_title'];
            $model->time = strtotime($dt['created_at']);
            $model->status = 1;
            var_dump($model->save());
            if($model->save()) $seo_id = $model->item_id; else return false;
            $seo = new SeoText;
            $seo->item_id = $seo_id;
            $seo->class = 'app\modules\modulepage\models\Item';
            $seo->h1 = $dt['title'];
            $seo->title = $dt['meta_t'];
            $seo->keywords = $dt['meta_k'];
            $seo->description = $dt['meta_d'];
            $seo->save();
            var_dump($seo->save());
        }
    }
     public function actionLoadDataNews(){
        $data = Yii::$app->db2->createCommand("SELECT * FROM at_ch_news WHERE status = 'on'")->queryAll();
        foreach ($data as $key => $dt) {
            $model = new Item;
            $list = [];
            $list['image'] = $dt['image'];
            $model->category_id = 17;
            $model->title = $dt['title'];
            $model->sub_title = '';
            $model->summary = $dt['summary'];
            $model->data = $list;
            $model->description = $dt['body'];
            $model->image = $dt['image'];
            $model->slug = $dt['url_title'];
            $model->time = strtotime($dt['created_at']);
            $model->status = 1;
            var_dump($model->save());
            if($model->save()) $seo_id = $model->item_id; else return false;
            $seo = new SeoText;
            $seo->item_id = $seo_id;
            $seo->class = 'app\modules\modulepage\models\Item';
            $seo->h1 = $dt['title'];
            $seo->title = $dt['meta_t'];
            $seo->keywords = $dt['meta_k'];
            $seo->description = $dt['meta_d'];
            $seo->save();
            var_dump($seo->save());
        }
    }

    protected function deleteCacheBlogs(){
        Yii::$app->cache->flush();
    }

    public function actionDeleteCache() {
        $this->deleteCacheBlogs();
        return $this->formatResponse(Yii::t('easyii/modulepage', 'Flush cached'));
    }

    public function actionContentsMobile($id)
    {
        $model = Item::findOne($id);
        if(!($model = Item::findOne($id))){
            return $this->redirect(['/admin/'.$this->module->id]);
        }

        return $this->render('contents_mobile', [
            'model' => $model,
        ]);
    }
}