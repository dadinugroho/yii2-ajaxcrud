<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>
use kartik\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin(); ?>

    <div class="row">
        <span class="col-md-4">
            
        </span>
        <span class="col-md-8">
            
        </span>
    </div>
<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes)) {
        if ('status' == $attribute) {
            echo '    <?= Html::label($model->getAttributeLabel(\'' . $attribute . '\'), $model->getAttributeLabel(\'' . $attribute . "')) ?>\n";
            echo '    <?= $form->field($model, \'' . $attribute . '\')->radioButtonGroup([1 => ' . $generator->generateString('Active') . ', 0 => ' . $generator->generateString('Inactive') . '], [' .
                      '\'class\' => \'btn-group btn-group-justified\', \'itemOptions\' => [\'labelOptions\' => [\'class\' => \'btn btn-default\']]])->label(false); ?>' . "\n\n";
        } else {
            echo "    <?= " . $generator->generateActiveField($attribute) . " ?>\n\n";
        } 
    }
} ?>  
	<?='<?php if (!Yii::$app->request->isAjax){ ?>'."\n"?>
	  	<div class="form-group">
	        <?= "<?= " ?>Html::submitButton($model->isNewRecord ? <?= $generator->generateString('Create') ?> : <?= $generator->generateString('Update') ?>, ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?="<?php } ?>\n"?>

    <?= "<?php " ?>ActiveForm::end(); ?>
    
</div>

<script text="javascript">
    $('form').on('keydown', function (e) {
        if (13 == e.which) {
            $('#btn-submit').click();
        }
    });
</script>