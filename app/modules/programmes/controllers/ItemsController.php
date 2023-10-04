<?php
namespace app\modules\programmes\controllers;

use Yii;
use yii\easyii\behaviors\StatusController;
use yii\web\UploadedFile;
use yii\helpers\Html;

use yii\easyii\components\Controller;
use app\modules\programmes\models\Category;
use app\modules\programmes\models\Item;
use app\modules\programmes\models\ItemLang;
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
use app\modules\programmes\api\Catalog;
use yii\easyii\models\SeoText;
use app\modules\location\models\Text;

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
               
                $model->category_id = $category->primaryKey;
                $model->data = Yii::$app->request->post('Data');

                
                if ($model->save()) {
                    $this->flash('success', Yii::t('easyii/programmes', 'Item created'));
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
                
            ]);
        }
    }

    public function getModulePage(){
        $modulePage = [];
        $mol = Yii::$app->db->createCommand("SELECT item.slug as 'id', item.title as 'text', cat.title as 'group' FROM app_modulepage_items
 as item JOIN app_modulepage_categories as cat ON cat.category_id = item.category_id
ORDER BY 'id', 'group'")->queryAll();
        foreach ($mol as $kl => $vl) {
            $modulePage[$vl['group']][$vl['id']] = $vl['text'];
        }
        return $modulePage;
    }

    public function getExclu(){
        $exl = Yii::$app->db->createCommand("SELECT item.item_id as 'id', item.title as 'text', cat.title as 'group' FROM app_exclusives_items
 as item JOIN app_exclusives_categories as cat ON cat.category_id = item.category_id
WHERE item.category_id
ORDER BY 'id', 'group'")->queryAll();
            foreach ($exl as $kl => $vl) {
                $exclusives[$vl['group']][$vl['id']] = $vl['text'];
            }
            return $exclusives;
    }

    public function getLibraries(){
        $optionsO = \app\modules\libraries\api\Catalog::cat(1)->items(['pagination'=> ['pageSize' => 0]]);
        $optionsO = ArrayHelper::map($optionsO, function($element){
            return $element->model->item_id;
        }, 'title');
        return ['options' => $optionsO ];
    }

    protected function getDataDestinations($catsID) {
        $result = array();
        $cats = $catsID ?: array(2, 8, 13, 18);
        $catsIDString = implode(",", $cats);

        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("
                SELECT category_id, title, item_id
                FROM app_destinations_items
                WHERE category_id IN (" . $catsIDString . ")");
        $result = $command->queryAll();

        return $result;
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
                        if(strpos('string|text|boolean|date', $field->type) === false){
                            $exts = json_decode(Yii::$app->request->post('exts-order-'.$field->name));
                            $request[$field->name] = $exts;
                        }
                        
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
                
                'cat' => $cat,
            ]);
        }
    }


    public function actionEditLang($id, $lang)
{
    if(!($model = Item::findOne($id))){
        return $this->redirect(['/admin/'.$this->module->id]);
    }
    if(!($modelLang = ItemLang::findOne(['item_id' => $id, 'lang_code' => $lang]))){
        $modelLang = new ItemLang;
    }
if ($modelLang->load(Yii::$app->request->post())) {
    if(Yii::$app->request->isAjax){
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ActiveForm::validate($model);
    }
    else {
        $modelLang->lang_code = $lang;
        $modelLang->item_id = $id;
        $modelLang->description = $modelLang->description;
        if ($modelLang->save()) {
            $this->flash('success', 'Item updated');
            return $this->redirect(['/admin/'.$this->module->id.'/items/edit-lang/'.$id.'/'.$lang]);
        } else {
            $this->flash('error', Yii::t('easyii', 'Update error. {0}', $model->formatErrors()));
            return $this->refresh();
        }
    }
}
else {
    return $this->render('edit_lang', [
        'model' => $model, 'modelLang' => $modelLang
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
        return $this->formatResponse(Yii::t('easyii/programmes', 'Item deleted'));
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
                if($field->name == 'itinerary'){
                    $result .= '<div class="form-group"><label>'. $field->title .'</label>'. Html::textarea("Data[{$field->name}]", $value, ['class' => 'form-control hidden', 'id' => $field->name]) .'</div>';
                    $result .= "<div class='chosen-itinerary chosen-container chosen-container-multi'>
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
                   <select name="'."Data[{$field->name}][]".'" multiple class="form-control selectTags">'. Html::renderSelectOptions($value, $countries) .'</select>
                </div>';
                
            }
            elseif ($field->type === 'options') {
                $options = $this->getLibraries()['options'];
               
                $result .= '<div class="control-group">
                    <label for="select-beast">'.$field->title.'</label>
                   <select name="'."Data[{$field->name}][]".'" multiple  class="form-control selectTags select-'.$field->name.'">'. Html::renderSelectOptions($value, $options) .'</select>
                   <input style="display: none;" class="exts-order" name="exts-order-'.$field->name.'" />
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
            
           elseif ($field->type === 'file') {
                $options = ['' => 'Select'];
               
                $result .= '<div class="form-group"><label>'. $field->title .'</label><select name="Data['.$field->name.']" class="form-control chosen">'. Html::renderSelectOptions($value, ['' => 'Select']+ $this->getFile()) .'</select></div>';
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
        }
        return $result;
    }

    public function actionUploadData($file){
                $dir = \Yii::getAlias('@webroot').'/uploads/'.\Yii::$app->session->get('uploadFolder');
                    if (!is_dir($dir)) {
                        mkdir($dir);
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
            $seo->class = 'app\modules\programmes\models\Item';
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
            $seo->class = 'app\modules\programmes\models\Item';
            $seo->h1 = $dt['title'];
            $seo->title = $dt['meta_t'];
            $seo->keywords = $dt['meta_k'];
            $seo->description = $dt['meta_d'];
            $seo->save();
            var_dump($seo->save());
        }
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