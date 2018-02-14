<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();
echo "<?php\n";
?>
use kartik\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use dadinugroho\ajaxcrud\CrudAsset; 
use dadinugroho\ajaxcrud\BulkButtonWidget;
use kartik\alert\AlertBlock;

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = ['label' => Yii::t('app', '<?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">
    <div id="ajaxCrudDatatable">
        <?="<?="?>GridView::widget([
            'id'=>'crud-datatable',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax'=>true,
            'columns' => require(__DIR__.'/_columns.php'),
            'options' => [
                'style' => 'overflow: auto; word-wrap: break-word;'
            ],
            'pager' => [
                'options' => ['class' => 'pagination pagination-sm'],
                'hideOnSinglePage' => true,
                'lastPageLabel' => '>>',
                'firstPageLabel' => '<<',
                'nextPageLabel' => '>',
                'prevPageLabel' => '<',
            ],
            'floatHeader' => true,
            'floatHeaderOptions' => ['top' => '70'],
            'perfectScrollbar' => true,
            'toolbar'=> [
                ['content'=>
                    Html::a(Html::icon('glyphicon glyphicon-plus'), ['create'],
                    ['role'=>'modal-remote','title'=><?= $generator->generateString('Create new ' . strtolower($generator->modelClass)) ?>,'class'=>'btn btn-default']).
                    Html::a(Html::icon('glyphicon glyphicon-repeat'), [''],
                    ['data-pjax'=>1, 'class'=>'btn btn-default', 'title'=> <?= $generator->generateString('Reset grid') ?>]).
                    '{toggleData}'.
                    '{export}'
                ],
            ],          
            'striped' => true,
            'bordered' => true,
            'condensed' => true,
            'responsive' => true,          
            'panel' => [
                'type' => 'primary', 
                'heading' => Html::icon('glyphicon glyphicon-list') . ' ' . Yii::t('app', '<?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?> listing'),
                'before' => AlertBlock::widget([
                    'useSessionFlash' => true,
                    'type' => AlertBlock::TYPE_ALERT
                ]),
                'after'=>BulkButtonWidget::widget([
                            'buttons'=>Html::a(Html::icon('glyphicon glyphicon-trash') . Yii::t('app', ' Delete all'),
                                ['bulk-delete'] ,
                                [
                                    'class'=>'btn btn-danger btn-xs',
                                    'role'=>'modal-remote-bulk',
                                    'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                                    'data-request-method'=>'post',
                                    'data-confirm-title' => Html::icon('glyphicon glyphicon-warning-sign white') . Yii::t('app', ' Bulk delete <?= strtolower($generator->modelClass) ?>s'),
                                    'data-confirm-message' => Yii::t('app', 'Are you sure you want to delete these <?= strtolower($generator->modelClass) ?>s?'),
                                    'data-confirm-ok' => Html::icon('glyphicon glyphicon-ok') . Yii::t('app', ' Yes'),
                                    'data-confirm-cancel' => Html::icon('glyphicon glyphicon-remove') . Yii::t('app', ' No'),
                                ]),
                        ]).                        
                        '<div class="clearfix"></div>',
            ]
        ])<?="?>\n"?>
    </div>
</div>
<?="<?php Modal::begin([
    'id'=>'ajaxCrudModal',
    'footer'=>'', // always need it for jquery plugin
])?>" . "\n"?>
<?='<?php Modal::end(); ?>'?>

<?= '<?php' ?> $this->registerJs(
        "$.fn.modal.Constructor.prototype.enforceFocus = $.noop;
        $('#ajaxCrudModal').on('shown.bs.modal', function () {
        $('#<?= strtolower($generator->modelClass) ?>-code').focus();
        });"
);?>
