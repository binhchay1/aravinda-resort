<?php
/* @var $this \yii\web\View */
/* @var $content string */
use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\easyii\modules\text\api\Text;
use yii\helpers\Markdown;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1.0, user-scalable=0">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="p:domain_verify" content="f99c8ec6f024220be45a1e0c9a52a4ca"/>
    <meta name="facebook-domain-verification" content="giruwbbu4wlg3e09n2dgjvslqf3hye" />
    <meta name="facebook-domain-verification" content="ef2ldmqap816kwozwt9rw00k54jny0" />

    <?php $this->registerCsrfMetaTags() ?>
    <title><?=isset($this->context->entry->title) ? $this->context->entry->title : '' ?></title>
    <?php $this->head() ?>

</head>

<body id="page-top">
<?php $this->beginBody() ?>
    <nav id="mainNav" class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <a class="ml-4 ml-sm-4 ml-lg-0 pt-0 d-block d-lg-none navbar-brand page-scroll" href="/<?=Yii::$app->language !='en' ?  Yii::$app->language : '';?>"><img class="logo" alt="" src="/assets/img/logo-bg-white.png"></a>

                
                <button type="button" class="float-right navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span> Menu <i class="fa fa-bars"></i>
                </button>
                <a class="float-right  mr-4 mr-sm-4 ml-lg-0 d-flex d-lg-none btn-booking navbar-brand page-scroll" href="/"><?=Text::get('dat-phong'); ?></a>
                <a class=" d-none d-lg-inline-block navbar-brand page-scroll" href="/<?=Yii::$app->language !='en' ?  Yii::$app->language : '';?>"><img class="logo" alt="" src="/assets/img/logo.png"></a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right nav-row-1">
                        <li>
                            <a class="page-scroll" href="/<?=$this->context->otherMenu['gallery']->slug ?>"><?=$this->context->otherMenu['gallery']->title ?></a>
                        </li>
                        <li class="mr-6 text-center" style="margin-right: 9rem;">
                            <a class="page-scroll" href="/<?=$this->context->otherMenu['contact']->slug ?>"><?=$this->context->otherMenu['contact']->title ?></a>
                        </li>
                        <li class="dropdown language">
                            <a class="language-btn dropdown-toggle " data-toggle="dropdown" href="/<?=Yii::$app->language !='en' ?  Yii::$app->language : '';?>"><?=ucfirst(Yii::$app->language)?></a>
                            <span class="caret"></span></a>
                              <ul class="dropdown-menu">
                                  <li class="<?=Yii::$app->language == 'en' ? 'd-none' : '' ?>"><a class="px-4 d-flex justify-content-between" href="/change-language/en?uri=<?=URI?>">English <img src="/assets/img/lang-en-icon.png"/></a></li>
                                  <li class="<?=Yii::$app->language == 'fr' ? 'd-none' : '' ?>"><a class="px-4 d-flex justify-content-between" href="/change-language/fr?uri=<?=URI?>">France <img src="/assets/img/lang-fr-icon.png"/></a></li>
                                  <li class="<?=Yii::$app->language == 'vi' ? 'd-none' : '' ?>"><a class="px-4 d-flex justify-content-between" href="/change-language/vi?uri=<?=URI?>">Vietnam <img src="/assets/img/lang-vn-icon.png"/></a></li>
                            </ul>
                        </li>
                    </ul>
                <ul class="nav navbar-nav navbar-right nav-row-2">
                    <? foreach ($this->context->menu as $km => $vm) :?>
                    <? if($km == 16 ) continue; ?>
                    <li class="dropdown <?=SEG1 == $vm->slug ? 'hover' : '' ?>">
                        <a class="page-scroll dropdown-toggle" data-target="/<?=$vm->slug;?>"   href="/<?=$vm->slug;?>"><?=$vm->title;?><? if($km != 10 && $km != 15) : ?>
                        <span class="arrow d-inline-flex d-sm-inline-flex d-lg-none"><img src="/assets/img/dropdown-icon-arrow.png"></span> <? endif; ?></a>
                        <? if($km != 10 && $km != 15 && $km != 14) : ?>
                        <ul class="dropdown-menu">
                        <? foreach ($vm->items as $ki => $vi) :?>
                           <li><a href="/<?=$vi->slug;?>"><?=$vi->title;?></a></li>
                        <? endforeach; ?>
                          
                        </ul>
                        <? endif; ?>
                        <? if($km == 14) : ?>
                        <ul class="dropdown-menu">
                        <? foreach ($vm->children() as $ki => $vi) :?>
                           <li><a href="/<?=$vi->slug;?>"><?=$vi->title;?></a></li>
                        <? endforeach; ?>
                          
                        </ul>
                        <? endif; ?>
                    </li>
                    <? endforeach; ?>
                    <li class="dropdown d-block d-sm-block d-lg-none">
                        <a class="page-scroll" href="/<?=$this->context->otherMenu['gallery']->slug ?>"><?=$this->context->otherMenu['gallery']->title ?></a>
                    </li>
                    <li class="dropdown d-block d-sm-block d-lg-none li-contact">
                        <a class="page-scroll" href="/<?=$this->context->otherMenu['contact']->slug ?>"><?=$this->context->otherMenu['contact']->title ?></a>
                    </li>
                    <li class="d-inline-block d-sm-inline-block d-lg-none bot-nav-mobile-li">
                        <ul class="menu-language float-left">
                                  <li class="<?=Yii::$app->language == 'en' ? 'd-none' : '' ?>"><a class="d-flex justify-content-center align-items-center" href="/change-language/en?uri=<?=URI?>"><img src="/assets/img/lang-en-icon.png"/> English</a></li>
                                  <li class="<?=Yii::$app->language == 'fr' ? 'd-none' : '' ?>"><a class="d-flex justify-content-center align-items-center" href="/change-language/fr?uri=<?=URI?>"><img src="/assets/img/lang-fr-icon.png"/> France</a></li>
                                  <li class="<?=Yii::$app->language == 'vi' ? 'd-none' : '' ?>"><a class="d-flex justify-content-center align-items-center" href="/change-language/vi?uri=<?=URI?>"><img src="/assets/img/lang-vn-icon.png"/> Vietnam</a></li>
                            </ul>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>
    <? if(Yii::$app->controller->action->id == 'index') : ?>
    <header>
        <div class="swiper-container swiper-container-header">
                    <!-- Additional required wrapper -->
                    <div class="swiper-wrapper">
                        <? foreach($this->context->page->photosArray['banner'] as $k => $p) : ?>
                        <div class="item swiper-slide">
                            <div class="header-content-inner" style="background-image: url(<?=$p->image ?>)">
                                <p class="content-item"><?=strip_tags(Markdown::process($p->model->caption), '<strong>') ?></p>
                            </div>
                        </div>
                    <? endforeach; ?>
                    </div>
                    <!-- If we need navigation buttons -->
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
            </div>

        
        <p class="view-down"><?=Text::get('keo-xuong'); ?></p>
    </header>
    <? else: ?>
    <div class="banner container-fluid p-0">
        <? $banner = ''; 
            if(isset($this->context->entry->photosArray['banner'])) $banner = $this->context->entry->photosArray['banner'][0];
        ?>
        <img class="w-100 focus-center" alt="<?=$banner ? $banner->description : '' ?>" src="<?=$banner ? $banner->image : '' ?>">
    </div>
    <? endif; ?>
    <main>
        <?= $content ?>
    </main>
    <div class="container-fluid footer mt-0 footer p-md-0">
        <div class="container px-0 mt-3">
    <footer>
        <div class="row no-gutters mx-md-3 mx-0">
        <div class="col-12 col-lg-4 col-md-6">
            <h3 class="text-uppercase mt-md-40 my-40 tt">Aravinda Resort</h3>
            <div class="info">
                <p><?=Text::get('dia-chi');?></p>
                <p>Email : info@aravindaresort.com</p>
                <p><?=Text::get('dien-thoai');?></p>
            </div>
            <a class="e-brochure">E-brochure</a>
        </div>
        <div class="col-12  col-lg-4 col-md-6 text-center text-md-right text-lg-center">
            <h3 class="tt mt-40 mt-md-40 mb-40 text-left text-md-right text-lg-center"><?=strip_tags(Text::get('tim-chung-toi'));?></h3> 
            <div class="social text-center text-md-right text-lg-center">
                <a class="float-left float-md-none fb"  target="_blank" href="https://www.facebook.com/aravindaresortninhbinh/">fb</a>
                <a  href="https://www.pinterest.com/aravinda_resort_ninh_binh" target="_blank" class="pin">pin</a>
                <a class="float-right float-md-none insta"  target="_blank" href="https://www.instagram.com/aravindaresort.ninhbinh/">insta</a>
            </div>
            <div class="weather mt-40 text-left text-md-right text-lg-center d-flex align-items-center justify-content-center">
                 <?php
$url = "http://api.openweathermap.org/data/2.5/weather?id=1571968&lang=en&units=metric&appid=46b18e7a85428a689c226f44b7b86d68";
$contents = file_get_contents($url);
$clima=json_decode($contents);
$temp =$clima->main->temp;
$icon=$clima->weather[0]->icon.".png";
//how get today date time PHP :P
$today = date("F j, Y, g:i a");
$cityname = $clima->name; 
?>
                <span class="sun pl-0"><? echo "<img src='http://openweathermap.org/img/w/" . $icon ."'/ >"; ?><?=intval($temp);?> °C</span>
                <span class="time">
                    <? date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                       echo $dateObject = date('h:i A');  
                    ?>
                    </span>
            </div>
        </div>
        <div class="newsletter col-12 col-md-12 col-lg-4">
            <h3 class="tt  mt-40 mt-md-40 mb-40"><?=strip_tags(Text::get('newsletter'));?></h3>   
            <p><?=Text::get('newsletter-text');?></p>
            <form action="" class="newsletter-form">
                <div class="col-12 col-md-6 col-lg-12 float-md-left pr-md-4 px-0 px-lg-0">
                    <input type="text" class="text" placeholder="<?=Text::get('ten');?>">
                </div>
                <div class="col-12 col-md-6 col-lg-12 float-md-right  pl-md-4 px-0 px-lg-0">
                    <input type="text" class="email  " placeholder="Email">
                </div>
                <button class="submit col-12 col-lg-auto"><?=Text::get('dang-ky');?></button>
            </form>
        </div>
        </div>
    </footer>
</div>
<div class="row fixed-footer mt-20">
    <div class="container align-middle align-items-center d-md-flex px-md-0">
            <div class="d-none d-sm-none d-md-block col-12 col-md-3 pull-left pl-0 pr-0">
                © Aravinda resort 2018
            </div>
            <div class=" col-12 col-md-9 pull-left pull-md-right text-left text-md-right about-us pr-0 pl-0">
                <a href="/<?=$this->context->otherMenu['tuyendung']->slug ?>"><?=$this->context->otherMenu['tuyendung']->title ?></a><span>/</span>
                <a href="#"><?=Text::get('so-do');?></a><span>/</span>
                <a href="/<?=$this->context->otherMenu['policy']->slug ?>"><?=$this->context->otherMenu['policy']->title ?></a>
            </div>
            <div class="d-block d-sm-block d-md-none col-12 col-md-3 pull-left pl-0 pr-0 mt-30">
                © Aravinda resort 2018
            </div>
        </div>
    </div>
</div>

<?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>

