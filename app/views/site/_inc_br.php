<? use yii\easyii\modules\text\api\Text; ?>
<div class="container-fluid">
        <div class="container text-center">
            <div class="breadcrumb text-center" itemscope itemtype="http://schema.org/BreadcrumbList">
			  <div class="item home d-inline-block" itemprop="itemListElement" itemscope
			      itemtype="http://schema.org/ListItem">
			    <a itemprop="item" href="/<?=Yii::$app->language !='en' ?  Yii::$app->language : '';?>"><span itemprop="name"><?=Text::get('trang-chu') ?></span></a> <span> / </span> 
			    <meta itemprop="position" content="1" />
			  </div>
			  
			  	<? if(isset($this->context->entry)) : ?>
					<? if($parents = $this->context->entry->parents()) : ?>
						<? foreach($parents as $kpr => $vpr) : ?>
						<? if($vpr->category_id == 16 || !$vpr->status) break; ?>
						<div class="item  d-inline-block" itemprop="itemListElement" itemscope
						      itemtype="http://schema.org/ListItem">
						    <a  itemprop="item" href="<?=DIR.$vpr->slug ?>"><span itemprop="name"><?=$vpr->seo['breadcrumb'] ? $vpr->seo['breadcrumb'] : $vpr->title?></span></a> <span> / </span> 
						    <meta itemprop="position" content="<?=$kpr+3 ?>" />
						  </div>
						<? endforeach; ?>
					<? endif;?>
				<div class="item  d-inline-block link-current" itemprop="itemListElement" itemscope
			      itemtype="http://schema.org/ListItem">
			    <span  itemprop="item" ><span itemprop="name"><?= isset($this->context->entry->model->seo->breadcrumb) && $this->context->entry->model->seo->breadcrumb ? $this->context->entry->model->seo->breadcrumb : str_replace('|', '', $this->context->entry->title)?></span></span>
			    <meta itemprop="position" content="<?=count($parents) + 3 ?>" />
			  </div>
				<? endif;?>
			</div>

        </div>
</div>

