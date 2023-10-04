<?php

$params = require(__DIR__ . '/params.php');

$basePath =  dirname(__DIR__);
$webroot = dirname($basePath);

$config = [
    'id' => 'app',
    'basePath' => $basePath,
    'bootstrap' => ['multiLanguage' => [
           'class' => '\navatech\language\Component',
        ]
      ],
   // 'language' => 'en-US',
    'language' => 'en',
    'runtimePath' => $webroot . '/runtime',
    'vendorPath' => $webroot . '/vendor',
    'modules' => [
        'redactor' => [
            'class' => 'yii\redactor\RedactorModule',
        ],
        'markdown' => [
            'class' => 'kartik\markdown\Module',
        ],
        'ckeditor' => [
            'class' => 'wadeshuler\ckeditor\Module',
            'preset' => 'full',    // default: basic - options: basic, standard, standard-all, full, full-all
            'customCdn' => '/assets/plugins/ckeditor/ckeditor.js?v=3', 
            'uploadDir' => '@webroot/uploads',    // must be file path (required when using filebrowser*BrowseUrl below)
            'uploadUrl' => '@web/uploads',
            'widgetClientOptions' => [
                'filebrowserBrowseUrl' => '/ckeditor/default/file-browse',
                'filebrowserUploadUrl' => '/ckeditor/default/file-upload',
                'filebrowserImageBrowseUrl' => '/ckeditor/default/image-browse',
                'filebrowserImageUploadUrl' => '/ckeditor/default/image-upload',
            ]
        ],
        'gridview' => [
            'class' => '\kartik\grid\Module',
        ],
        'language' => [
        'class'    => '\navatech\language\Module',
        /*TODO uncommented if you want to custom view*/
        //'viewPath' => '@app/vendor/navatech/yii2-multi-language/src/views',
        /*TODO uncommented if you want to change suffix of translated table / model.
        should be one word, lowercase only.*/
        //'suffix' => 'translate',
        ],
    ],
    'components' => [
        'multiLanguage' => [
           'class' => '\navatech\language\Component',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'pw84HBitmjLhg7eDJztj',
        ],
        'easyimage' => [
            'class' => 'yiicod\easyimage\EasyImage',
            'webrootAlias' => '@webroot',
            'cachePath' => '/upload/watermark_gallery_new/',
            //'cachePath' => '/'.URI.'/',
            'imageOptions' => [
                'quality' => 100,
                
            ],
        ],
        'reCaptcha' =>[
                    'name' => 'reCaptcha',
                    'class' => 'himiklab\yii2\recaptcha\ReCaptcha',
                    'siteKey' => '6Ld9iUwUAAAAADv0INyr4yJMq9SAu7EvujcwuhNR',
                    'secret' => '6Ld9iUwUAAAAAH_6hPeKP9QL_bRUQPz4adVN3iKt',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        // 'errorHandler' => [
        //     // 'errorAction' => 'site/error',
        // ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
        ],
        'urlManager' => [
            'rules' => [
               '<controller:\w+>/view/<slug:[\w-]+>' => '<controller>/view',
               '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
               '<controller:\w+>/cat/<slug:[\w-]+>' => '<controller>/cat',
               '<x:|vi|fr>' => 'site/index',
               '<x:rooms-villas|chambres-villas|phong-va-villa>' => 'site/villa',
               '<x:rooms-villas|chambres-villas|phong-va-villa>/<y>' => 'site/villa-single',
               '<x:gastronomy|gastronomie|am-thuc>' => 'site/amthuc',
               '<x:gastronomy|gastronomie|am-thuc>/<y>' => 'site/amthuc-single',
               '<x:experiences|trai-nghiem|les-experiences>' => 'site/trainghiem',
               '<x:experiences|trai-nghiem|les-experiences>/<y>' => 'site/trainghiem-single',
               '<x:about-us|ve-chung-toi|a-propos>' => 'site/about-us',
               '<x:promotions|uu-dai|les-promotions>' => 'site/promotion',
               '<x:promotions|uu-dai|les-promotions>/<y>' => 'site/promotion-single',
               '<x:useful-information|thong-tin-huu-ich|infos-pratiques>' => 'site/thongtin',
               '<x:useful-information|thong-tin-huu-ich|infos-pratiques>/<y>' => 'site/entry-other',
               '<x:reservation|reserver|dat-phong>' => 'site/booking',
               '<x:thank-you|cam-on|merci>' => 'site/thank',
               '<x:lien-he|contact|nous-contacter>' => 'site/contact',
               '<x:gallery|thu-vien|galerie>' => 'site/gallery',
               '<x:careers|co-hoi-nghe-nghiep|recrutement>' => 'site/carrier',
               '<x:privacy-policy|politique-de-confidentialie|dieu-khoan-bao-mat>' => 'site/entry',
               'change-language/<lang:en|fr|vi>' => 'site/change-lang',
               '<x:sitemap>' => 'site/entry',
               'booking-engine' => 'site/booking-engine'
            ],
        ],
        'assetManager' => [
            // uncomment the following line if you want to auto update your assets (unix hosting only)
            //'linkAssets' => true,
            'bundles' => [
                'yii\web\JqueryAsset' => [
                   'js' => ['https://code.jquery.com/jquery-3.3.1.min.js']
                ],
                'yii\bootstrap\BootstrapAsset' => [
                   // 'css' => ['/assets/plugins/bootstrap4/bootstrap.min.css'],
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                   // 'js' => ['/assets/plugins/bootstrap4/bootstrap.min.js'],
                ],
            ],
            'forceCopy' => true
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['components']['assetManager']['forceCopy'] = true;
   // configuration adjustments for 'dev' environment
   // $config['bootstrap'][] = 'debug';
   // $config['modules']['debug'] = 'yii\debug\Module';

   // $config['bootstrap'][] = 'gii';
   // $config['modules']['gii'] = 'yii\gii\Module';
   
   // $config['components']['db']['enableSchemaCache'] = false;
}

return array_merge_recursive($config, require($webroot . '/vendor/noumo/easyii/config/easyii.php'));