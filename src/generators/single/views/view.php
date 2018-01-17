<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use kartik\detail\DetailView;
use kartik\helpers\Html;
use kartik\widgets\AlertBlock

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">
    <?= '<?=' ?>
    AlertBlock::widget([
        'useSessionFlash' => true,
        'type' => AlertBlock::TYPE_ALERT
    ]);
    ?>
    
    <?= "<?= " ?>DetailView::widget([
        'model' => $model,
        'striped' => true,
        'bordered' => true,
        'condensed' => true,
        'responsive' => true,
        'mode' => DetailView::MODE_VIEW,
        'enableEditMode' => false,
        'panel' => [
            'type' => 'primary',
            'heading' => Html::icon('glyphicon glyphicon-tag') . ' ' . Yii::t('app', '<?= $modelClass ?> information'),
        ],
        'attributes' => [
<?php
            if (($tableSchema = $generator->getTableSchema()) === false) {
                foreach ($generator->getColumnNames() as $name) {
                    echo "            '" . $name . "',\n";
                }
            } else {
                foreach ($generator->getTableSchema()->columns as $column) {
                    $format = $generator->generateColumnFormat($column);
                    echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                }
            }
            ?>
        ],
    ]) ?>

</div>
