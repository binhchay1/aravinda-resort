<?php
namespace app\modules\destinations\api;

use Yii;
use yii\easyii\components\API;
use yii\helpers\Html;
use yii\helpers\Url;

class ContentMobileObject extends \yii\easyii\components\ApiObject
{
    public $description;
    public $type;

}