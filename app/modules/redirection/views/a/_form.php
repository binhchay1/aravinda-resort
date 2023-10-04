<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\easyii\widgets\Redactor;
use yii\easyii\widgets\SeoForm;
use wadeshuler\ckeditor\widgets\CKEditor;
?>
<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => true,
    'options' => ['class' => 'model-form']
]); ?>
<?= $form->field($model, 'source_url') ?>
<?= $form->field($model, 'target_url')?>
<?= $form->field($model, 'type')->dropDownList(['1' => 'Regex', '0' => 'String'],['prompt'=>'Select Option']); ?>

<?= Html::submitButton(Yii::t('easyii','Save'), ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>

<div class="amica-tony-guide" style="width: 750px; background-color: #cccccc; border-radius: 10px; padding: 10px; margin: 10px;">
    <ul>
        <li>Step 1: Source URL, It's the link you want to redirect, for example: /abc/def</li>
        <li>Step 2: Target URL, This is link, we want that customer wanna go to, for example: https://www.amica-travel.com/abc
        </li>
        <li>Step 3: Select Type
            <ul>
                <li>Option 1: Select Option (Default)</li>
                <li>Option 2: Regex</li>
                <li>Option 3: String</li>
            </ul>
        </li>
        <li>Step 4: Click on Save and wait</li>
        <li>## STEP 3 ##</li>
        <li>Use String, if you fill fields (Source URL and Target URL) with a URL.</li>
        <li>Use Regex, if you fill Source URL with a REGEX. WARNING, we must know to use REGEX (ask IT Team).</li>
        <li>Example for CREATE URL: <br>If you want to redirect all URL has type like as */form to https://www.amica-travel.com you can use regex like as <b>'\/form'</b> for SOURCE URL and <b>https://www.amica-travel.com</b> for TARGET URL</li>
        <li>Example for ## IMPORT URL ## <br><abbr title="SOURCE URL">actualites/fete-ao-dai-a-ho-chi-minh-ville-du-07-au-08-mars-2016</abbr>,<abbr title="TARGET URL">https://www.amica-travel.com/actualites</abbr>,<abbr title="TYPE URL">1</abbr></li>
    </ul>
</div>
