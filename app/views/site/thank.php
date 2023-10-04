<? use yii\helpers\Html;
use yii\widgets\ActiveForm; 
 use yii\easyii\modules\text\api\Text; ?>
    <div class="container">
        <div class="row entry-body d-flex justify-content-between mt-130 mb-60 ">
            <? if($theEntry->photosArray['summary']) : 
                $vimg = $theEntry->photosArray['summary'][0];
            ?>
                <img class="col-12 col-sm p-0 focus-center" alt="<?=$vimg->description; ?>" src="<?=$vimg->image; ?>">
            <? endif; ?>
            <div class="text float-right col-12 col-sm d-flex align-items-center justify-content-center text-center pt-40">
                <div><?=$theEntry->description; ?></div>
                <div class="social text-center text-md-right text-lg-center">
                <a class="float-left float-md-none fb" target="_blank" href="https://www.facebook.com/aravindaresortninhbinh/">fb</a>
                <a href="https://www.pinterest.com/aravinda_resort_ninh_binh" target="_blank" class="pin">pin</a>
                <a class="float-right float-md-none insta" target="_blank" href="https://www.instagram.com/aravindaresort.ninhbinh/">insta</a>
            </div>
            </div>
        </div>
    </div>
<? 
$css = '.entry-body{
    max-width: 900px;
    margin-left: auto;
    margin-right: auto;
}
.entry-body .text{
    background: #5d8f8f;
    color: #fff;
    font-size: 20px;
    position: relative;
    flex-wrap: wrap;
}
.entry-body .text p{
    font-size: 20px;
}
.entry-body .text:after{
    background: url(/assets/img/bg-footer.png) center center no-repeat #5d8f8f;
    content: "";
    width: 100%;
    height: 100%;
    opacity: 0.3;
    position: absolute;
    top: 0;
    left: 0;
    z-index: 0;
}
.social{
	z-index: 99;
}
.social > a {
    display: inline-block;
    width: 50px;
    height: 50px;
    text-indent: 9999rem;
    background: url(/assets/img/social-icon.png) left top no-repeat transparent;
    margin-right: 20px;
    overflow: hidden;
}
.social > a.pin {
    background-position-x: -65px;
}
.social > a.insta {
    background-position-x: -132px;
    margin-right: 0;
}
'; 
$this->registerCss($css);
?>
