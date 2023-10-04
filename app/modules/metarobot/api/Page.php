<?php
namespace app\modules\metarobot\api;

use Yii;
use app\modules\metarobot\models\Page as PageModel;
use yii\helpers\Html;


/**
 * Page module API
 * @package app\modules\metarobot\api
 *
 * @method static PageObject get(mixed $id_slug) Get page object by id or slug
 */

class Page extends \yii\easyii\components\API
{
    private $_photos;
    private $_pages = [];

    public function api_get($id_slug)
    {
        if(!isset($this->_pages[$id_slug])) {
            $this->_pages[$id_slug] = $this->findPage($id_slug);
        }
        return $this->_pages[$id_slug];
    }

    private function findPage($id_slug)
    {
        $page = PageModel::find()->where(['or', 'page_id=:id_slug', 'source_url=:id_slug'], [':id_slug' => $id_slug])->one();

        return $page ? new PageObject($page) : $this->notFound($id_slug);
    }

    private function notFound($id_slug)
    {
        return false;
    }
}