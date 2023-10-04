<?php
namespace app\modules\faq\controllers;

use Yii;
use yii\easyii\components\CategoryController;
use app\modules\faq\models\Category;
use yii\easyii\behaviors\SortableModel;
use yii\widgets\ActiveForm;
use yii\web\UploadedFile;
use yii\helpers\Html;
use yii\easyii\helpers\Image;

class AController extends CategoryController
{
    public $categoryClass = 'app\modules\faq\models\Category';
    public $moduleName = 'faq';

    public $rootActions = ['fields'];
   
    public function actionFields($id)
    {
        if(!($model = Category::findOne($id))){
            return $this->redirect(['/admin/'.$this->module->id]);
        }

        if (Yii::$app->request->post('save'))
        {
            $fields = Yii::$app->request->post('Field') ?: [];
            $result = [];

            foreach($fields as $field){
                $temp = json_decode($field);
                if( $temp === null && json_last_error() !== JSON_ERROR_NONE ||
                    empty($temp->name) ||
                    empty($temp->title) ||
                    empty($temp->type) ||
                    !($temp->name = trim($temp->name)) ||
                    !($temp->title = trim($temp->title)) ||
                    !array_key_exists($temp->type, Category::$fieldTypes)
                ){
                    continue;
                }
                $options = '';
                if($temp->type == 'select' || $temp->type == 'checkbox' || $temp->type == 'selectTags'){
                    if(empty($temp->options) || !($temp->options = trim($temp->options))){
                        continue;
                    }
                    $options = [];
                    foreach(explode(',', $temp->options) as $option){
                        $options[] = trim($option);
                    }
                }

                $result[] = [
                    'name' => \yii\helpers\Inflector::slug($temp->name),
                    'title' => $temp->title,
                    'type' => $temp->type,
                    'options' => $options
                ];
            }

            $model->fields = $result;

            if($model->save()){
                $ids = [];
                foreach($model->children()->all() as $child){
                    $ids[] = $child->primaryKey;
                }
                if(count($ids)){
                    Category::updateAll(['fields' => json_encode($model->fields)], ['in', 'category_id', $ids]);
                }

                $this->flash('success', Yii::t('easyii/faq', 'Category updated'));
            }
            else{
                $this->flash('error', Yii::t('easyii','Update error. {0}', $model->formatErrors()));
            }
            return $this->refresh();
        }
        else {
            return $this->render('fields', [
                'model' => $model
            ]);
        }
    }

    public function actionEdit($id)
    {
         if(!($model = Category::findOne($id))){
            return $this->redirect(['/admin/'.$this->module->id]);
        }
        //get parent slug
        $parentSlug = Category::findOne($id)->parents(1)->select('slug')->one();
        $prSlug = '';
        if($parentSlug)
            $prSlug = $parentSlug->slug.'/';
        else $prSlug = Yii::$app->session->get('moduleUrl').'/';
        $path = explode("/", $model->slug);
        Yii::$app->session->set('uploadFolder', Yii::$app->session->get('moduleName').'/'.end($path));
        if ($model->load(Yii::$app->request->post())) {
            if(Yii::$app->request->isAjax){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            else {
                $dir = Yii::getAlias('@webroot').'/uploads/';
                if (isset($_FILES) && $this->module->settings['itemThumb']) {
                    $model->image = UploadedFile::getInstance($model, 'image');
                    if ($model->image && $model->validate(['image'])) {
                        $model->image = Image::upload($model->image, $dir.'photos');
                    } else {
                        $model->image = $model->oldAttributes['image'];
                    }
                }
                $model->content = Yii::$app->request->post()['Category']['content'];
                if ($model->save()) {
                    $this->flash('success', 'Category updated');
                    return $this->redirect(['/admin/'.$this->module->id.'/a/edit', 'id' => $model->primaryKey]);
                } else {
                    $this->flash('error', Yii::t('easyii', 'Update error. {0}', $model->formatErrors()));
                    return $this->refresh();
                }
            }
        }
        else {
            return $this->render('@easyii/views/category/edit', [
                'model' => $model,'prSlug' => $prSlug
            ]);
        }
    }

    public function actionCreate($parent = null)
    {
        $class = $this->categoryClass;
        $model = new $class;
        $slug = '';
        if($parent){
            $cat = Category::findOne($parent);
            $slug = $cat->slug.'/';
        }
        $model->slug = $slug;
        if ($model->load(Yii::$app->request->post())) {
            if(Yii::$app->request->isAjax){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            else{
                if(isset($_FILES) && $this->module->settings['categoryThumb']){
                    $model->image = UploadedFile::getInstance($model, 'image');
                    if($model->image && $model->validate(['image'])){
                        $model->image = Image::upload($model->image, $this->moduleName);
                    } else {
                        $model->image = '';
                    }
                }

                $model->status = $class::STATUS_ON;
                $parent = (int)Yii::$app->request->post('parent', null);
                if($parent > 0 && ($parentCategory = $class::findOne($parent))){
                    $model->order_num = $parentCategory->order_num;
                    $model->appendTo($parentCategory);
                } else {
                    $model->attachBehavior('sortable', SortableModel::className());
                    $model->makeRoot();
                }

                if(!$model->hasErrors()){
                    $this->flash('success', Yii::t('easyii', 'Category created'));
                    return $this->redirect(['/admin/'.$this->moduleName, 'id' => $model->primaryKey]);
                }
                else{
                    $this->flash('error', Yii::t('easyii', 'Create error. {0}', $model->formatErrors()));
                    return $this->refresh();
                }
            }
        }
        else {
            return $this->render('@easyii/views/category/create', [
                'model' => $model,
                'parent' => $parent
            ]);
        }
    }


    public function actionPhotos($id)
    {
        $model = Category::findOne($id);
        if(!($model = Category::findOne($id))){
            return $this->redirect(['/admin/'.$this->module->id]);
        }

        return $this->render('photos', [
            'model' => $model, 'moduleName' => $this->moduleName
        ]);
    }

    public function actionPhotosMobile($id)
    {
        $model = Category::findOne($id);
        if(!$model){
            return $this->redirect(['/admin/'.$this->module->id]);
        }

        return $this->render('photos_mobile', [
            'model' => $model,
        ]);
    }
   
}