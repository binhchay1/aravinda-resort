<?php
namespace app\modules\metarobot\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;
use yii\helpers\FileHelper;
use yii\easyii\components\Controller;
use app\modules\metarobot\models\Page;

class AController extends Controller
{
    public $rootActions = ['create', 'delete'];

    public function actionIndex()
    {
        $data = new ActiveDataProvider([
            'query' => Page::find()->desc()
        ]);
        return $this->render('index', [
            'data' => $data
        ]);
    }

    public function actionCreate($slug = null)
    {
        $model = new Page;

        if ($model->load(Yii::$app->request->post())) {
            if(Yii::$app->request->isAjax){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            else{
                if($model->save()){
                    $this->deleteCache();
                    $this->flash('success', Yii::t('easyii/metarobot', 'Page created'));
                    return $this->redirect(['/admin/'.$this->module->id]);
                }
                else{
                    $this->flash('error', Yii::t('easyii', 'Create error. {0}', $model->formatErrors()));
                    return $this->refresh();
                }
            }
        }
        else {
            if($slug) $model->slug = $slug;

            return $this->render('create', [
                'model' => $model
            ]);
        }
    }

    public function actionEdit($id)
    {
        $model = Page::findOne($id);

        if($model === null){
            $this->flash('error', Yii::t('easyii', 'Not found'));
            return $this->redirect(['/admin/'.$this->module->id]);
        }
        if ($model->load(Yii::$app->request->post())) {
            if(Yii::$app->request->isAjax){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            else{
                if($model->save()){
                    $this->deleteCache();
                    $this->flash('success', Yii::t('easyii/metarobot', 'Page updated'));
                }
                else{
                    $this->flash('error', Yii::t('easyii', 'Update error. {0}', $model->formatErrors()));
                }
                return $this->refresh();
            }
        }
        else {
            return $this->render('edit', [
                'model' => $model
            ]);
        }
    }

    protected function deleteCache() {
        $cache = Yii::$app->cache;
        $cache->delete('data-cache-meta-robots');
    }

    public function actionDelete($id)
    {
        if(($model = Page::findOne($id))){
            $model->delete();
            $this->deleteCache();
        } else {
            $this->error = Yii::t('easyii', 'Not found');
        }
        return $this->formatResponse(Yii::t('easyii/metarobot', 'Page deleted'));
    }

    public function actionPhotos($id)
    {
        $model = Page::findOne($id);
        if(!($model = Page::findOne($id))){
            return $this->redirect(['/admin/'.$this->module->id]);
        }

        return $this->render('photos', [
            'model' => $model,
        ]);
    }
}