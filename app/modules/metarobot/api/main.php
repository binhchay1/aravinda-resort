<?php
use yii\helpers\FileHelper;
use yii\helpers\Html;
use app\assets\AppAsset;
use yii\helpers\Markdown;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="fr" class="no-js"> <!--<![endif]-->
    <head>
        <?php $this->registerMetaTag(['charset' => 'utf-8']) ?> 
        <?php $this->registerMetaTag(['http-equiv' => 'X-UA-Compatible', 'content' => 'IE=edge']) ?>
		<?php $this->registerMetaTag(['http-equiv' => 'content-language', 'content' => 'fr']) ?>
        <? if(Yii::$app->request->get('page') || (SEG1 == 'devis') || (SEG1 == 'votre-liste-envies') || (SEG1 == 'merci' || (SEG1 == 'mentions-legales') || (SEG1 == 'conditions-generales-de-vente'))) 
                $this->registerMetaTag(['name' => 'ROBOTS', 'content' => 'NOINDEX, FOLLOW']);
            else    
                $this->registerMetaTag(['name' => 'ROBOTS', 'content' => 'INDEX, FOLLOW']);
        ?>
        <?php $this->registerMetaTag(['name' => 'google-site-verification', 'content' => '5RPgaIZ9TROjN3QeaK_d7YwlSzL8O0GPZRIqVfYVZ-k']); ?>
        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
		
       <link rel="icon" href="/favicon.ico?v=1" type="image/x-icon" />
		
        <!-- BEGIN SEO -->
        <? $this->registerLinkTag(['rel' => 'canonical', 'href' => Yii::$app->urlManager->createAbsoluteUrl(URI)]);?>
       <? $this->registerLinkTag(['rel' => 'alternate', 'href' => Yii::$app->urlManager->createAbsoluteUrl(URI), 'hreflang' => 'x-default']);?>
        <? if($this->context->pagination) : ?>
            <? $numPage = Yii::$app->request->get('page') ? Yii::$app->request->get('page') : 1; ?>
            <? if($numPage > 1) : ?>
            <link rel="prev" href="<?=Yii::$app->urlManager->createAbsoluteUrl(URI)?><?=$numPage != 2 ? '?page='.($numPage-1) : '' ?>" />
            <? endif;?>
             <? if($numPage < $this->context->pagination) : ?>
            <link rel="next" href="<?=Yii::$app->urlManager->createAbsoluteUrl(URI)?>?page=<?=$numPage+1?>" />
            <? endif;?>
        <? endif ?>
        <?php $this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0' ]); ?>
        <?php $this->registerLinkTag(['rel' => 'publisher', 'href' => 'https://plus.google.com/+AmicatravelFR']);?>
        
        <?php $this->registerMetaTag(['name' => 'msvalidate.01', 'content' => '64C6247A095AEDF078755244B0562B56' ]); ?>
        <link rel="publisher" href="https://plus.google.com/+AmicatravelFR" >
        
        <meta name="msvalidate.01" content="64C6247A095AEDF078755244B0562B56" />
        <!-- END SEO -->

        <?php
       $this->registerMetaTag(['name' => 'description', 'content' => $this->context->metaD]);
        // $this->registerMetaTag(['name' => 'title', 'content' => $this->context->metaT]);
         $this->registerMetaTag(['name' => 'keywords', 'content' => '']);
        ?>
        <title><?= Html::encode($this->title = $this->context->metaT) ?></title>
        <?php $this->head() ?>
        <?= Html::csrfMetaTags() ?>
        <!-- TWITTER -->
        <? 
        $mainImg = $summaryImg = '/assets/img/page2016/logo_amica_travel.png';
        if($this->context->entry){
            if(isset($this->context->entry->model->photos)){
                foreach ($this->context->entry->model->photos as $key => $value) {
                    if($value->type == 'banner'){
                        $mainImg = $value->image;
                    }
                    if($value->type == 'summary'){
                        $summaryImg = $value->image;
                    }
                }
            }
        }
        ?>
        <meta name="twitter:card" content="<?=Yii::$app->urlManager->getHostInfo(). $summaryImg ?>">
        <meta name="twitter:site" content="@amicatravel">
        <meta name="twitter:title" content="<?=$this->context->metaT?>">
        <meta name="twitter:description" content="<?=$this->context->metaD?>">
        <meta name="twitter:image" content="<?=Yii::$app->urlManager->getHostInfo(). $mainImg ?>">
        <!-- FACEBOOK -->
        
        
        
        <meta property="og:title" content="<?=$this->context->metaT?>" />
        <meta property="og:url" content="<?=Yii::$app->urlManager->createAbsoluteUrl(URI)?>" />
        <meta property="og:type" content="website" />
        <meta property="og:description" content="<?=$this->context->metaD?>" />
        <meta property="og:image" content="<?=Yii::$app->urlManager->getHostInfo(). $mainImg ?>" />
        <meta property="og:site_name" content="Amica Travel" />
        <meta property="og:locale:alternate" content="fr" />
         <!-- Page hiding snippet (recommended) -->
 <style>.async-hide { opacity: 0 !important} </style>
<script>(function(a,s,y,n,c,h,i,d,e){s.className+=' '+y;h.start=1*new Date;
h.end=i=function(){s.className=s.className.replace(RegExp(' ?'+y),'')};
(a[n]=a[n]||[]).hide=h;setTimeout(function(){i();h.end=null},c);h.timeout=c;
})(window,document.documentElement,'async-hide','dataLayer',4000,
{'GTM-P3JTNZ6':true});</script>

         <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TCX7426');</script>
<!-- End Google Tag Manager -->
        
    </head>

    <body>
       <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TCX7426"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
        <?php $this->beginBody() ?>
        
       <!-- Nav Responsive - Banner-->
            <?php include_once 'nav_banner_responsive.php'; ?>
       <!-- end Nav - Banner - Responsive-->
        
        <a class="btn-contactez-nous" href="<?=DIR?>nous-contacter">Contactez - Nous</a>
        <?php if(SEG1 == ''){ ?>
<!-- Full Page Image Background Carousel Header -->
    <header id="myCarousel" class="carousel slide carousel-fade">
        <!-- Indicators -->
<!--      <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1"></li>
            <li data-target="#myCarousel" data-slide-to="2"></li>
        </ol>-->

        <!-- Wrapper for Slides -->
        <div class="carousel-inner">
            <?   $home = \yii\easyii\modules\page\api\Page::get(31); 
            if(!empty($home->photos)) :  ?>
                <? foreach ($home->photos as $key => $value) : ?>
                    <? if($value->type == 'banner') : ?>
                    <div class="item item-<?=$key ?> <?=$key==0 ? 'active' : '' ?>">
                        <div class="fill" style="background-image:url('<?=$value->image?>');"></div>
                        <div class="carousel-caption">
                             <h2><?=str_replace('|', '<br>', preg_replace('/<p\b[^>]*>(.*?)<\/p>/i', '$1', Markdown::process($value->model->caption)));?></h2>
                            <a href="<?= $value->description?>">En savoir plus</a>
                        </div>
                    </div>
                    <?  endif;?>
                <? endforeach; ?>
            <? endif;?>
        </div>

        <!-- Controls -->
        <a class="left carousel-control" href="#myCarousel" data-slide="prev">
            <span class="icon-prev"></span>
        </a>
        <a class="right carousel-control" href="#myCarousel" data-slide="next">
            <span class="icon-next"></span>
        </a>
        
        <div class="area-logo-group-btn">
            <div class="logo">
                <a href="<?=DIR?>">
                    <img alt="Amica Travel" src="<?=DIR?>assets/img/page2016/logo.png">
                </a>    
            </div>
            <div class="group-btn">
                <h1>Agence locale, spécialiste des voyages sur mesure : <span>Vietnam, Laos, Cambodge & Birmanie</span>
					
				</h1>
                <ul>
                    <li class="btn-devis"><a href="<?=DIR.'devis'?>">DEVIS sur mesure</a></li>
                    <li class="btn-rdv"><a href="<?=DIR.'rdv-telephonique'?>">RAPPEL gratuit</a></li>
                    <li class="btn-menu"><a href="javascript:void(0)">menu</a>               
                    </li>
                </ul>
            </div>
            <div class="list-menu">
                 <ul class="btn-items">
                     <li><a href="<?=DIR.'destinations'?>">NOS DESTINATIONS</a></li>
                     <li><a href="<?=DIR.'secrets-ailleurs'?>">NOS SECRETS D’AILLEURS</a></li>
                     <li><a href="<?=DIR.'voyage'?>">IDÉES DE VOYAGE</a></li>
                     <li><a href="<?=DIR.'a-propos-de-nous'?>">À PROPOS DE NOUS</a></li>
               </ul>
            </div>
        </div>
        
        <div class="area-search-menu">
                 
                    <div class="group-search">
                        <p>BOUGEZ SELON VOTRE ENVIE</p>
                        <form class="search-excl-form search-form">
                            <div class="cs-select destination search-destination">
                                <span class="cs-placeholder active">Destination</span>
                                    <div class="cs-options" style="display: none;">
                                            <ul>
                                                <li data-option="" data-value="vietnam">Vietnam</li>
                                                <li data-option="" data-value="laos">Laos</li>
                                                <li data-option="" data-value="cambodge">Cambodge</li>
                                                <li data-option="" data-value="birmanie">Birmanie</li>
                                            </ul>
                                    </div>
                                    <div class="list-option">
                                            <ul>
                                                <li data-value="vietnam">Vietnam<span></span></li>
                                                <li data-value="laos">Laos<span></span></li>
                                                <li data-value="cambodge">Cambodge<span></span></li>
                                                <li data-value="birmanie">Birmanie<span></span></li>
                                            </ul>    
                                        </div>
                            </div>
                            <div class="cs-select search-envies search-type une-envie">
                                <span class="cs-placeholder active">Une envie</span>
                                    <div class="cs-options" style="display: none;">
                                        <ul>
                                            <? foreach ($type = \app\modules\exclusives\models\Category::find()->roots()->all() as $key => $value) : ?>
                                               <li data-option="" data-value="<?=$value->category_id ?>"><?=$value->title ?></li>
                                            <? endforeach ?>
                                            </ul>
                                            
                                    </div>
                                    <div class="list-option">
                                        <ul>
                                         <? foreach ($type as $key => $value) : ?>
                                               <li data-value="<?=$value->category_id ?>"><?=$value->title ?><span></span></li>
                                            <? endforeach ?>
            
                                        </ul>    
                                    </div>
                          
                            </div>
                            <div class="cs-select submit">
                                submit
                                <span id="count-tour-search"><?=$this->context->countExcl ?></span>
                            </div>
                        </form>
                    </div>    
                 <div class="group-menu">
                     <div class="items item-1">
                         <a href="<?=DIR.'secrets-ailleurs'?>">
                             <img class="img-lazy" alt="Amica Travel" src="" data-src="<?=DIR?>assets/img/page2016/secret-ailleurs.jpg">
                             <div class="entry-title">
                                <p><span>NOS<br> SECRETS</span> <br>d’ailleurs</p>
                            </div>   
                         </a>
                         
                     </div>
                     <div class="items item-2">
                         <a href="<?=DIR.'voyage-avec-amica-travel'?>">
                             <img class="img-lazy" alt="Amica Travel" src="" data-src="<?=DIR?>assets/img/page2016/voyage-selon-envies.jpg">
                             <div class="entry-title">
                                <p><span>Le voyage</span><br> SUR MESURE</p>
                            </div>   
                         </a>
                         
                     </div>
                     <div class="items item-3">
                         <a href="<?= DIR.'tourisme-solidaire'?>">
                             <img class="img-lazy" alt="Amica Travel" src="" data-src="<?=DIR?>assets/img/page2016/voyage-solidaire.jpg">
                             <div class="entry-title">
                                <p>Le voyage <br><span>SOLIDAIRE</span></p>
                             </div> 
                         </a>
                            
                     </div>
                     <div class="items item-4">
                         <a href="<?= DIR.'devis'?>">
                             <img class="img-lazy" alt="Amica Travel" src="" data-src="<?=DIR?>assets/img/page2016/voyage-sur-mesure.jpg">
                             <div class="entry-title">
                                <p>Le voyage <br>au gré des <br><span>ENVIES</span></p>
                             </div>  
                         </a>
                          
                     </div>
                 </div>
                 
             </div>    
        

</header>
        <?php }?>
        
       <div class="text-sologan">
            <p>Agence locale, spécialiste des voyages sur mesure : <span>Vietnam, Laos, Cambodge & Birmanie</span>
				<a href="<?=DIR?>devis" class="link-btn">Demande de devis</a>
			</p>
        </div>
             
       
        <div class="area-btn-list-menu">
            <div class="group-list">
                <div class="btn-logo">
                    <a href="<?=DIR?>">
                        <img alt='Amica Travel' src="<?= DIR?>assets/img/page2016/logo_amica_travel.png">
                    </a>    
                </div>
                <ul class="group-btn">
                    <li><a class="btn-mn sub-mn-1 <?=$this->context->destination ? 's-active' : '' ?>" data-name="sub-mn-1" href="<?=DIR.'destinations'?>">NOS DESTINATIONS</a></li>
                    <li><a class="btn-mn sub-mn-2 <?=$this->context->exclusives ? 's-active' : '' ?>" data-name="sub-mn-2" href="<?=DIR.'secrets-ailleurs'?>">NOS SECRETS D’AILLEURS</a></li>
                    <li><a class="btn-mn sub-mn-3 <?=$this->context->programes ? 's-active' : '' ?>" data-name="sub-mn-3" href="<?=DIR.'voyage'?>">IDÉES DE VOYAGE </a></li>
                    <li><a class="btn-mn sub-mn-4  <?=$this->context->aboutUs ? 's-active' : '' ?>" data-name="sub-mn-4" href="<?=DIR.'a-propos-de-nous'?>">À PROPOS DE NOUS</a></li>
                </ul>
                <div class="votre-project">
                    <a href="<?=DIR.'votre-liste-envies'?>">votre <br>liste d’envies</a>
                    <div class="number">
                    <span id="numb-tour" class="count-tour <?= isset(\Yii::$app->session['projet']) && (count(Yii::$app->session['projet']['programes']['select']) + count(Yii::$app->session['projet']['exclusives']['select'])) > 0 ? 'active' : '' ?>"><?= count(Yii::$app->session['projet']['programes']['select']) + count(Yii::$app->session['projet']['exclusives']['select']) ?></span>
                    </div>
                </div>  
            </div>  
            
            <?php include_once '_inc_sub_menu.php';?>
           
            
        </div> 

        <!-- Content -->
        <?=$content?>
        <!-- End content-->
        <? 
        $classAreaMap = 'general';
        if($this->context->destination && SEG1 != 'destinations') {
            $classAreaMap = SEG1;
        }?>
        <div class="footer-bg <?=$this->context->destination ? $classAreaMap : '' ?>" style="background-image: url(/assets/img/page2016/bg_footer_<?=$classAreaMap ?>.jpg?v=1)">
        <div class="area-map <?=$this->context->destination ? $classAreaMap : '' ?>">
            <div class="map">
<!--                <img class="img-fix img-left-fixed" src="<?=DIR?>assets/img/page2016/img-left-fixed.png">
                <img class="img-fix img-right-fixed" src="<?=DIR?>assets/img/page2016/img-right-fixed.png">-->
                <div class="col col-1">
                    <p class="bureaux">nos bureaux</p>
                    <ul class="redirect-map">
                        <li name="info-office-hanoi" class="active" data-position="place_id:ChIJqYHx-rmrNTERsZ9R-AY9vnk"><a href="javascript:void(0)">Hanoi</a></li>
                        <li name="info-office-saigon" data-position="place_id:ChIJ_TFAPTgvdTERseX-VwRjWRM"><a href="javascript:void(0)">Saigon</a></li>
                        <li name="info-office-siem" data-position="place_id:ChIJkyp6TzgXEDERdpFeDUjYQGU"><a href="javascript:void(0)">Siem Reap</a></li>
                        <li name="info-office-luong" data-position="Ban Pakham Luang Prabang Laos"><a href="javascript:void(0)">Luang Prabang</a></li>
                    </ul>
                </div>
                <div class="col col-2">
                   <iframe id="get-map" class="img-lazy" width="600" height="200" frameborder="0" style="border:0" src=" " data-src="https://www.google.com/maps/embed/v1/place?q=place_id:ChIJqYHx-rmrNTERsZ9R-AY9vnk&key=AIzaSyAHEAW4xHCcY8aG90qhqusQwNNPvWLPwI8" allowfullscreen></iframe> 
                   
                   
                </div>
                <div class="col col-3">
                    <div class="info-office info-office-hanoi active" itemscope itemtype="http://schema.org/Organization">
                        <span itemprop="name">Amica Travel</span>
                     <p itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"> Building NIKKO, 2ème étage, 
                      <span itemprop="streetAddress">27 Rue Nguyen Truong To, Ba Dinh</span>,
                      <span itemprop="addressLocality">Hanoi, Vietnam</span>
                        </p>
                     <p>
                      Téléphone : <span itemprop="telephone">(+84) 46273 44 55</span>
                     </p>
                     <p>Email : <span itemprop="email">info@amica-travel.com</span></p>
                    </div>
                    <div class="info-office info-office-saigon" itemscope itemtype="http://schema.org/Organization">
                     <span itemprop="name">Amica Travel</span>
                     <p itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">Building Resco, 5è étage, 
                      <span itemprop="streetAddress">94-96 rue Nguyen Du, 1è Disctrict, </span>,
                      <span itemprop="addressLocality">Ho Chi Minh Ville, Vietnam</span>
                        </p>
                     <p>
                      Téléphone : <span itemprop="telephone">(+84) 8 6685 4079</span>
                     </p>
                     <p>Email : <span itemprop="email">info@amica-travel.com</span></p>
                    </div>
                     <div class="info-office info-office-siem" itemscope itemtype="http://schema.org/Organization">
                     <span itemprop="name">Amica Travel</span>
                     <p itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">Borey Angkor Palace, Building B49 & B50,
                      <span itemprop="streetAddress">Phum Kruos, Sangkat Svay Dangkum, </span>,
                      <span itemprop="addressLocality">Siem Reap, Cambodge</span>
                        </p>
                     <p>
                      Téléphone : <span itemprop="telephone">(+85) 5 6396 6139</span>
                     </p>
                     <p>Email : <span itemprop="email">info@amica-travel.com</span></p>
                    </div>
                      <div class="info-office info-office-luong" itemscope itemtype="http://schema.org/Organization">
                     <span itemprop="name">Amica Travel</span>
                     <p itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">Ban Pakham, Unit 05 Maison
                      <span itemprop="streetAddress">No 64 Souvannabanlang, </span>,
                      <span itemprop="addressLocality">Luang Prabang, Laos</span>
                        </p>
                     <p>
                      Téléphone : <span itemprop="telephone">(+85) 6 7121 2275</span>
                     </p>
                     <p>Email : <span itemprop="email">info@amica-travel.com</span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="area-footer">
            <div class="group-info background img-lazy">
                <div class="row-1">
                    <div class="col col-1">
                        <p class="text-title">newsletter</p>  
                        <p>Restons en contact pour de dernières offres et actualités !</p>
                          <form id="newsletter-form">
                            <input class="email" type="text" value="" placeholder="Votre adresse mail" name="email" />
                            <p class="error-email">
                                Le format de votre email n'est pas valide.
                            </p>
                            <a class='submit-email'>Valider</a>
                        </form>
                         <h5>NOS DESTINATIONS</h5>
                        <p><a href="<?=DIR?>vietnam">Vietnam</a></p>
                        <p><a href="<?=DIR?>laos">Laos</a></p>
                        <p><a href="<?=DIR?>cambodge">Cambodge</a></p>
                        <p><a href="<?=DIR?>birmanie">Birmanie</a></p>
                        <p><a href="<?=DIR?>recrutement">RECRUTEMENT</a></p>
                        <p><a href="<?=DIR?>actualites">ACTUALITÉS</a></p>
                    </div>
                    <div class="col col-2">
                        <p class="text-title">hotline</p>  
                        <h4>Besoin d’aide ?</h4>
                        <p>Nous répondons à vos questions</p>
                        
                        <h5>en France</h5>
                        <p>du lundi au vendredi (09h-12h & 14h-18h)<br><span>(+33) 6 19 08 15 72 ou (+33) 6 28 22 72 86</span></p>
                        
                        <h5>au vietnam</h5>
                        <p>du lundi au vendredi (8h-17h30) :  <br><span>(+84) 984 56 66 76</span></p>
                    </div> 
                    <div class="col col-3">
                        <p class="text-title">REJOIGNEZ-NOUS !</p>
                        <ul>
                            <li class="facebook"><a target="_blank" href="https://www.facebook.com/amicatravel/">facebook</a></li>
                            <li class="twitter"><a target="_blank" href="https://twitter.com/AmicaTravel">twitter</a></li>
                            <li class="insta"><a target="_blank" href="https://www.instagram.com/amicatravel/">instagram</a></li>
                            <li class="youtube"><a target="_blank" href="http://www.youtube.com/c/AmicaTravelAgency">youtube</a></li>
                            
                            <li class="pinter"><a target="_blank" href="https://pinterest.com/amicatravel/">pinterest</a></li>
                            
                        </ul>
                        <p class="text-title"><a target="_blank" href="https://blog.amica-travel.com">VISITEZ NOTRE BLOG</a></p>
                        <p class="text-title"><a href="<?=DIR.'club-ami-amica'?>">CLUB AMI AMICA</a></p>
                        <p class="text-title">NOS GUIDES DESTINATIONS</p>
                        <div class="book">
                            <a href="<?=DIR?>uploads/files/amica-travel-voyage-au-vietnam.pdf" class="book-vietnam"><img src="/upload/book-vietnam.jpg?v=1"></a>
                            <a href="<?=DIR?>uploads/files/amica-travel-voyage-au-laos.pdf" class="book-laos"><img src="/upload/book-laos.jpg?v=1"></a>
                            <a href="<?=DIR?>uploads/files/amica-travel-voyage-au-cambodge.pdf" class="book-cambodge"><img src="/upload/book-cambodge.jpg?v=1"></a>
                        </div>
                       
                    </div> 
                </div>   
                <div class="row-2">
                    <ul>
                        <li><a href="<?=DIR?>mentions-legales">Mentions légales</a><span>|</span></li>
                        <li><a href="<?=DIR?>conditions-generales-de-vente">Conditions générales de vente</a><span>|</span></li>
                        
                    </ul>
                </div>
            </div>
            <div class="copyright">
                 <div id="ft-r4" style="text-align: center; padding-top: 3px;" vocab="http://schema.org/" typeof="website">
                <img style="height:15px" src="/assets/img/stars_ok.png">
                <img style="height:15px" src="/assets/img/stars_ok.png">
                <img style="height:15px" src="/assets/img/stars_ok.png">
                <img style="height:15px" src="/assets/img/stars_ok.png">
                <img style="height:15px" src="/assets/img/stars_ok.png">

                <div class="text-rating" property="aggregateRating" typeof="AggregateRating" style="display: inline-block;">
                <span style="font-weight:bold;" property="ratingValue">4.5</span>
                / <span style="font-weight:bold;">5</span>
                Calculé sur <span style="font-weight:bold;" property="ratingCount">139</span>
                avis <span property="name"><a href="https://www.facebook.com/amicatravel/" alt="Facebook Amica Travel">Facebook</a></span>
                <meta property="bestRating" content="5">
                <meta property="worstRating" content="1">
                </div>
                <span class="copytext">© Amica Travel 2017</span>
                </div>
            </div>
        </div>    
        </div>
        <span style="display: none;" id="back-to-top" title="Back to top" class="show">
            <img alt="amica travel" src="<?=DIR?>assets/img/page2016/back-to-top.png">
        </span> 

        <?php $this->endBody() ?>    
       
    </body>
</html>
<?php $this->endPage() ?>
