<?php
ini_set('display_errors', 1);
error_reporting(-1);
define('MAILGUN_API_KEY', 'b456bb9fa933563c9ae2e536096e298e-2416cf28-dc0d0e31');
define('MAILGUN_API_DOMAIN', 'aravinda-resort.com');
define('NOW', date('Y-m-d H:i:s'));
define('SITE_ID', 1);
define('DIR', '/');
define('BASE_PATH',__DIR__.'/');
define('YII_ENV_DEV', true);

// URI, SEGS, SEGn
$_REQUEST_URI = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'); define('URI', DIR == '/' ? $_REQUEST_URI : substr($_REQUEST_URI, strlen(trim(DIR, '/').'/'))); $_URI_SEGMENTS = explode('/', URI); define('SEGS', empty($_URI_SEGMENTS) ? 0 : count($_URI_SEGMENTS)); for ($i = 1; $i <= 9; $i ++) define('SEG'.$i, isset($_URI_SEGMENTS[$i - 1]) ? $_URI_SEGMENTS[$i - 1] : '');

if ($_REQUEST_URI != '' && substr($_SERVER['REQUEST_URI'], -1) == '/') {
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: /' . trim($_REQUEST_URI, '/'));
    exit;
}
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ .'/vendor/autoload.php');
require(__DIR__ .'/vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/app/config/web.php');

(new yii\web\Application($config))->run();
