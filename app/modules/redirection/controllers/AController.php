<?php
namespace app\modules\redirection\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\widgets\ActiveForm;
use yii\easyii\components\Controller;
use app\modules\redirection\models\Page;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

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
                    $this->flash('success', Yii::t('easyii/redirection', 'Page created'));
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
                    $this->flash('success', Yii::t('easyii/redirection', 'Page updated'));
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

    public function actionDelete($id)
    {
        if(($model = Page::findOne($id))){
            $model->delete();
        } else {
            $this->error = Yii::t('easyii', 'Not found');
        }
        return $this->formatResponse(Yii::t('easyii/redirection', 'Page deleted'));
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

    public function actionImport($slug = null)
    {
        $model = new Page;

        if (Yii::$app->request->post()) {
            $uploader = UploadedFile::getInstance($model, 'file_import');
            if ( $uploader )
            {
                $time = time();

                // create folder csv at root
                FileHelper::createDirectory(BASE_PATH . '/upload/csv');
                $uploader->saveAs('upload/csv/' .$time. '.' . $uploader->extension);
                $uploader = 'upload/csv/' .$time. '.' . $uploader->extension;

                $handle = fopen($uploader, "r");
                $messError = '';

                while (($fileop = fgetcsv($handle, 1000, ",")) !== false)
                {
                    if(!empty($fileop[0])) {
                        $source_url = $fileop[0];
                    }else {
                        $source_url = '';
                    }

                    if(!empty($fileop[1])) {
                        $target_url = $fileop[1];
                    }else {
                        $target_url = '';
                    }

                    if(!empty($fileop[2])) {
                        $type = $fileop[2];
                    } else {
                        $type = '';
                    }

                    $checker = $model::find()
                        ->where( [ 'source_url' => $source_url ] )
                        ->exists();

                    if(!$checker) {
                        $sql = "INSERT INTO app_redirections(source_url, target_url, type) VALUES ('$source_url', '$target_url', '$type')";
                        Yii::$app->db->createCommand($sql)->execute();
                    } else {
                        $messError .= $source_url . '<br/>';
                    }
                }

                if(!empty($messError)) {
                    $messError .= 'Exits!';
                }
            }

            $model->save();
            if(!empty($messError)) {
                $this->flash('danger', Yii::t('easyii/redirection', $messError));

            }
            $this->flash('success', Yii::t('easyii/redirection', 'Data upload successfully'));
            return $this->redirect(['/admin/'.$this->module->id]);
        } else {
            return $this->render('import', [
                'model' => $model,
            ]);
        }
    }
}