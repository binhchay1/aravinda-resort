<?php
namespace app\modules\exclusives\controllers;

use Yii;
use yii\easyii\behaviors\StatusController;
use yii\web\UploadedFile;
use yii\helpers\Html;

use yii\easyii\components\Controller;
use app\modules\exclusives\models\Category;
use app\modules\exclusives\models\Item;
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
use app\modules\exclusives\api\Catalog;
use yii\easyii\models\SeoText;
use app\modules\location\models\Text;
use yii\helpers\FileHelper;

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

    public function actionTop($id)
    {
        $this->top($id);
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionCreate($id)
    {
        if(!($category = Category::findOne($id))){
            return $this->redirect(['/admin/'.$this->module->id]);
        }

        $model = new Item;
        $model->slug = $category->slug.'/';
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
                if($model->programes)
                    $model->programes = implode(',',$model->programes);
                $model->category_id = $category->primaryKey;
                $model->data = Yii::$app->request->post('Data');

                if (isset($_FILES) && $this->module->settings['itemThumb']) {
                    $model->image = UploadedFile::getInstance($model, 'image');
                    if ($model->image && $model->validate(['image'])) {
                        $model->image = Image::upload($model->image, 'exclusives');
                    } else {
                        $model->image = '';
                    }
                }
                if ($model->save()) {
                    $this->flash('success', Yii::t('easyii/exclusives', 'Item created'));
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
                'dataForm' => $this->generateForm($category->fields),
                'programes' => $this->getProgrames()
            ]);
        }
    }

    public function getProgrames(){
        $prog = Yii::$app->db->createCommand("SELECT item.item_id as 'id', item.title as 'text', cat.title as 'group' FROM app_programmes_items
 as item JOIN app_programmes_categories as cat ON cat.category_id = item.category_id
WHERE item.category_id
ORDER BY 'id', 'group'")->queryAll();
            foreach ($prog as $kl => $vl) {
                $programes[$vl['group']][$vl['id']] = $vl['text'];
            }
            return $programes;
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
                if(isset(Yii::$app->request->post('Item')['programes']))
                    $model->programes = implode(',',Yii::$app->request->post('Item')['programes']);
                else $model->programes = '';

               if(isset(Yii::$app->request->post('Data')['countries'])){
                $ct = Yii::$app->request->post('Data')['countries'];
                     if($ct){
                        $ct = implode(', ', $ct);
                        Yii::$app->request->post('Data')['countries'] = $ct;
                    }
                }
                if(isset(Yii::$app->request->post('Data')['locations'])){
                        Yii::$app->request->post('Data')['countries'] = implode(', ', Yii::$app->request->post('Data')['locations']);
                 }
                $request = Yii::$app->request->post('Data');

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
                'dataForm' => $this->generateForm($model->category->fields, $model->data),
                'programes' => $this->getProgrames(),
                'cat' => $cat
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
        } else {
            $this->error = Yii::t('easyii', 'Not found');
        }
        return $this->formatResponse(Yii::t('easyii/exclusives', 'Item deleted'));
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

    public function getDestinationData(){
        $dataDes = \app\modules\destinations\models\Category::find()->where(['slug' => ['vietnam/visiter', 'cambodge/visiter', 'laos/visiter', 'birmanie/visiter']])->with('items')->asArray()->all();
            $destinations = [];
            foreach ($dataDes as $key => $value) {
                $destinations[$value['sub_title']] = ArrayHelper::getColumn($value['items'], function($element){
                    return [$element['item_id'], $element['title'], $element['slug'], $element['status'], $element['summary_title']];
                });
            }
        return $destinations;
    }


    private function generateForm($fields, $data = null)
    {
        $result = '';
        foreach($fields as $field)
        {
            $value = !empty($data->{$field->name}) ? $data->{$field->name} : null;
            if ($field->type === 'string') {
                    if($field->name == 'city'){
               $result .= '<div class="form-group"><label>'. $field->title .'</label>'. Html::textarea("Data[{$field->name}]", $value, ['class' => 'form-control hidden', 'id' => $field->name]) .'</div>';
                    $result .= "<div class='chosen-city chosen-container chosen-container-multi'>
                        <ul class='chosen-choices' style='min-width: 500px;'>";
                    if(json_decode($value)) {
                        foreach (json_decode($value) as $kvl => $vvl) {
                        $result .= "<li data-id='$vvl->id' data-slug='$vvl->slug' data-status='$vvl->status' data-stitle='$vvl->stitle' class='search-choice'><span>$vvl->title</span><a class='search-choice-close'></a></li>";
                        }  
                    }
                     
                    $result .= "<li class='search-field'><input type='text' placeholder='Select Some Options' class=''></li>
                        </ul>
                        <div class='chosen-drop'>
                        <ul class='chosen-results'>";
                    foreach ($this->getDestinationData() as $kd => $vd) {
                        $result .= "<li class='group-result'>$kd</li>";
                        foreach ($vd as $kvd => $vvd) {
                            $result .= "<li class='active-result group-option' data-option-array-index='1' data-id='$vvd[0]' data-slug='$vvd[2]' data-status='$vvd[3]' data-stitle=\"$vvd[4]\" style=''>$vvd[1]</li>";
                        }
                    }
                           
                    $result .= "</ul></div>
                    </div>";
                } else 

                $result .= '<div class="form-group"><label>'. $field->title .'</label>'. Html::textarea("Data[{$field->name}]", $value, ['class' => 'form-control', 'id' => $field->name]) .'</div>';
            }
            elseif ($field->type === 'text') {
                $result .= '<div class="form-group"><label>'. $field->title .'</label>'. Html::textarea("Data[{$field->name}]", $value, ['class' => 'form-control ckeditor', 'id' => $field->name]) .'</div>';
            }
            elseif ($field->type === 'boolean') {
                $result .= '<div class="checkbox"><label>'. Html::checkbox("Data[{$field->name}]", $value, ['uncheck' => 0]) .' '. $field->title .'</label></div>';
            }
            elseif ($field->type === 'select') {
                $options = ['' => 'Select'];
                foreach($field->options as $option){
                    $options[$option] = $option;
                }
                $result .= '<div class="form-group"><label>'. $field->title .'</label><select name="Data['.$field->name.']" class="form-control">'. Html::renderSelectOptions($value, $options) .'</select></div>';
            }
            elseif ($field->type === 'selectTags') {
                $options = ['' => 'Select'];
                foreach($field->options as $option){
                    $options[$option] = $option;
                }
                $result .= '<div class="control-group">
                    <label for="select-beast">'.$field->title.'</label>
                   <select name="'."Data[{$field->name}][]".'" multiple class="form-control selectTags">'. Html::renderSelectOptions($value, $options) .'</select>
                </div>';
                
            }
             elseif ($field->type === 'selectLocations') {
                $locations = $this->getLibraries()['locations'];
                $result .= '<div class="control-group">
                    <label for="select-beast">'.$field->title.'</label>
                   <select name="'."Data[{$field->name}][]".'" multiple class="form-control selectTags">'. Html::renderSelectOptions($value, $locations) .'</select>
                </div>';
                
            }
            elseif ($field->type === 'countries') {
                $countries = $this->getLibraries()['countries'];

                $result .= '<div class="control-group">
                    <label for="select-beast">'.$field->title.'</label>
                   <select name="'."Data[{$field->name}][]".'"  class="form-control selectTags">'. Html::renderSelectOptions($value, [''=>'Select...']+$countries) .'</select>
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

    public function getLibraries(){
         $notes = \app\modules\libraries\api\Catalog::items(['where' => ['category_id' => \app\modules\libraries\api\Catalog::cat('notes')->model->category_id]]);
            $notes = ArrayHelper::map($notes, 'slug', 'title');
            $locations = [];
            $list = [];
            $catLocations = \app\modules\libraries\models\Category::findOne(['slug' => 'locations'])->leaves()->all();
            foreach ($catLocations as $kc => $vc) {
                $list[] = $vc['category_id'];
            }

            $list = implode(',', $list);
            $loc = Yii::$app->db->createCommand("SELECT item.slug as 'id', item.title as 'text', cat.title as 'group' FROM app_libraries_items
 as item JOIN app_libraries_categories as cat ON cat.category_id = item.category_id
WHERE item.category_id IN (".$list.")
ORDER BY 'id', 'group'")->queryAll();
            foreach ($loc as $kl => $vl) {
                $locations[$vl['group']][$vl['id']] = $vl['text'];
            }

             $countries = \app\modules\libraries\models\Category::findOne(['slug' => 'locations'])->leaves(1)->asArray()->all();
            $countries = ArrayHelper::map($countries, 'slug', 'title');
            // $locations = ArrayHelper::map($loc, 'id', 'text');
            return ['notes' => $notes,
                'locations' => $locations,
                'countries' =>  $countries
            ];
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