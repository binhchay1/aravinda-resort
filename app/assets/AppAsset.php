<?php
namespace app\assets;

class AppAsset extends \yii\web\AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $sourcePath = '@app/media';
    public $css = [
		'https://fonts.googleapis.com/css?family=Lora|Montserrat',
        'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css',
        'assets/css/bootstrap-grid.css',
        'assets/css/style-bootstrap4.css',
        'assets/theme/font-awesome.css',
        'https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800',
        'https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic,900,900italic',
        'assets/theme/magnific-popup.css',
        'https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/css/swiper.min.css',
        'assets/theme/creative.css',
        'assets/css/main.css',
        'assets/css/home.css',
    ];
    public $js = [
        'https://momentjs.com/downloads/moment.min.js',
        'https://code.jquery.com/ui/1.12.1/jquery-ui.js',
        'https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js',
        'assets/theme/scrollreveal.js',
        'assets/theme/jquery.magnific-popup.js',
        '//cdnjs.cloudflare.com/ajax/libs/jquery.touchswipe/1.6.4/jquery.touchSwipe.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/js/swiper.min.js',
        'assets/theme/creative.js',
        'assets/js/main.js',
        'https://widget.siteminder.com/ibe.min.js'
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'yii\web\YiiAsset'
    ];
     public function init() {
        parent::init();
    }
}
