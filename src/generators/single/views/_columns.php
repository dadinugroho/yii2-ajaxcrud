<?php

use yii\helpers\Url;
use kartik\helpers\Html;
use yii\bootstrap\Modal;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$modelClass = StringHelper::basename($generator->modelClass);
$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();
$actionParams = $generator->generateActionParams();

echo "<?php\n";

?>
use yii\helpers\Url;

return [
    [
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    <?php
    $count = 0;
    foreach ($generator->getColumnNames() as $name) {   
        if ($name=='id'||$name=='created_at'||$name=='updated_at'){
            echo "    // [\n";
            echo "        // 'class'=>'\kartik\grid\DataColumn',\n";
            echo "        // 'attribute'=>'" . $name . "',\n";
            echo "    // ],\n";
        } else if ('status' == $name) {
            echo "    [\n";
            echo "        'class'=>'\kartik\grid\BooleanColumn',\n";
            echo "        'attribute'=>'" . $name . "',\n";
            echo "        'format' => 'raw',\n";
            echo "        'width' => '80px',\n";
            echo "        'vAlign' => 'middle',\n";
            echo "        'trueLabel' =>  Yii::t('app', 'Active'),\n";
            echo "        'falseLabel' =>  Yii::t('app', 'Inactive'),\n";
            echo "    ],\n";
        } else {
            echo "    [\n";
            echo "        'class'=>'\kartik\grid\DataColumn',\n";
            echo "        'attribute'=>'" . $name . "',\n";
            echo "        'width' => '100px',\n";
            echo "        'vAlign' => 'middle',\n";
            echo "        'hAlign' => 'center',\n";
            echo "    ],\n";
        }
    }
    ?>
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'<?=substr($actionParams,1)?>'=>$key]);
        },
        'viewOptions'=>['role'=>'modal-remote','title'=>Yii::t('app', 'View'),'data-toggle'=>'tooltip'],
        'updateOptions'=>['role'=>'modal-remote','title'=>Yii::t('app', 'Update'), 'data-toggle'=>'tooltip'],
        'deleteOptions'=>['role'=>'modal-remote','title'=>Yii::t('app', 'Delete'), 
                          'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                          'data-request-method'=>'post',
                          'data-toggle'=>'tooltip',
                          'data-confirm-title' => Html::icon('glyphicon glyphicon-warning-sign white') . <?= $generator->generateString(' Delete ' . strtolower($modelClass)) ?>,
                          'data-confirm-message' => <?= $generator->generateString('Are you sure you want to delete this ' . strtolower($modelClass) . '?') ?>,
                          'data-confirm-ok' => Html::icon('glyphicon glyphicon-ok') . Yii::t('app', ' Yes'),
                          'data-confirm-cancel' => Html::icon('glyphicon glyphicon-remove') . Yii::t('app', ' No')],
    ],

];   