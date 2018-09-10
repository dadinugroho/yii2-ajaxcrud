<?php
/**
 * This is the template for generating a CRUD controller class file.
 */

use yii\helpers\StringHelper;
use yii\db\ActiveRecordInterface;


/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}

/* @var $class ActiveRecordInterface */
$class = $generator->modelClass;
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use Yii;
use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if (!empty($generator->searchModelClass)): ?>
use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php else: ?>
use yii\data\ActiveDataProvider;
<?php endif; ?>
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use kartik\helpers\Html;
use yii\db\Query;
use yii\db\IntegrityException;
use yii\db\Exception;

/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 */
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'ghost-access' => [
                'class' => 'webvimark\modules\UserManagement\components\GhostAccessControl',
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all <?= $modelClass ?> models.
     * @return mixed
     */
    public function actionIndex()
    {    
       <?php if (!empty($generator->searchModelClass)): ?>
 $searchModel = new <?= isset($searchModelAlias) ? $searchModelAlias : $searchModelClass ?>();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
<?php else: ?>
        $dataProvider = new ActiveDataProvider([
            'query' => <?= $modelClass ?>::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
<?php endif; ?>
    }


    /**
     * Displays a single <?= $modelClass ?> model.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionView(<?= $actionParams ?>)
    {   
        $request = Yii::$app->request;
        
        $model = $this->findModel(<?= $actionParams ?>);
        
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> Html::icon('glyphicon glyphicon-info-sign white') . ' ' . Yii::t('app', '<?= $modelClass ?>') . ' ' .  $model->name,
                    'content'=>$this->renderAjax('view', ['model' => $model]),
                    'footer' => Html::button(Html::icon('glyphicon glyphicon-remove'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                    Html::a(Html::icon('glyphicon glyphicon-pencil') . ' ' . Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-warning', 'role' => 'modal-remote']) .
                    Html::a(Html::icon('glyphicon glyphicon-plus') . ' ' . Yii::t('app', 'Create more'), ['create'], ['class' => 'btn btn-success', 'role' => 'modal-remote']) .
                    Html::a(Html::icon('glyphicon glyphicon-refresh'), ['view', 'id' => $model->id], ['id' => 'btn_ref', 'class' => 'btn btn-default', 'role' => 'modal-remote'])
                ];    
        } else {
            return $this->render('view', ['model' => $model]);
        }
    }

    /**
     * Creates a new <?= $modelClass ?> model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new <?= $modelClass ?>();  

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            if ($model->load($request->post()) && $model->save()) {
                Yii::$app->getSession()->setFlash('success', $model->name . ' ' . <?= $generator->generateString('is successfully created.') ?>);

                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => Html::icon('glyphicon glyphicon-info-sign white') . ' ' . Yii::t('app', '<?= $modelClass ?>') . ' ' . $model->name,
                    'content' => $this->renderAjax('view', ['model' => $model]),
                    'footer' => Html::button(Html::icon('glyphicon glyphicon-remove'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                    Html::a(Html::icon('glyphicon glyphicon-pencil') . ' ' . Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-warning', 'role' => 'modal-remote']) .
                    Html::a(Html::icon('glyphicon glyphicon-plus') . ' ' . Yii::t('app', 'Create more'), ['create'], ['class' => 'btn btn-success', 'role' => 'modal-remote']) .
                    Html::a(Html::icon('glyphicon glyphicon-refresh'), ['view', 'id' => $model->id], ['id' => 'btn_ref', 'class' => 'btn btn-default', 'role' => 'modal-remote'])
                ];
            }

            return [
                'title' => Html::icon('glyphicon glyphicon-plus-sign white') . ' ' . Yii::t('app', 'Create new <?= strtolower($modelClass) ?>'),
                'content' => $this->renderAjax('create', ['model' => $model]),
                'footer' => Html::button(Html::icon('glyphicon glyphicon-remove') . ' ' . Yii::t('app', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                Html::button(Html::icon('glyphicon glyphicon-ok') . ' ' . Yii::t('app', 'Create'), ['id' => 'btn-submit', 'class' => 'btn btn-success', 'type' => 'submit'])
            ];
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                Yii::$app->getSession()->setFlash('success', $model->name . ' ' . <?= $generator->generateString('is successfully created.') ?>);
                return $this->redirect(['view', <?= $urlParams ?>]);
            } else {
                return $this->render('create', ['model' => $model]);
            }
        }
       
    }

    /**
     * Updates an existing <?= $modelClass ?> model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionUpdate(<?= $actionParams ?>)
    {
        $request = Yii::$app->request;
        $model = $this->findModel(<?= $actionParams ?>); 

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            if ($model->load($request->post()) && $model->save()) {
                Yii::$app->getSession()->setFlash('success', $model->name . ' ' . <?= $generator->generateString('is successfully updated.') ?>);
                
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => Html::icon('glyphicon glyphicon-info-sign white') . ' ' . Yii::t('app', '<?= $modelClass ?>') . ' ' . $model->name,
                    'content' => $this->renderAjax('view', ['model' => $model]),
                    'footer' => Html::button(Html::icon('glyphicon glyphicon-remove'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                    Html::a(Html::icon('glyphicon glyphicon-pencil') . Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-warning', 'role' => 'modal-remote']) .
                    Html::a(Html::icon('glyphicon glyphicon-plus') . Yii::t('app', 'Create more'), ['create'], ['class' => 'btn btn-success', 'role' => 'modal-remote']) .
                    Html::a(Html::icon('glyphicon glyphicon-refresh'), ['view', 'id' => $model->id], ['id' => 'btn_ref', 'class' => 'btn btn-default', 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Html::icon('glyphicon glyphicon-info-sign white') . ' ' . Yii::t('app', 'Update <?= strtolower($modelClass) ?>') . ' ' . $model->name,
                    'content' => $this->renderAjax('update', ['model' => $model]),
                    'footer' => Html::button(Html::icon('glyphicon glyphicon-remove') . ' ' . Yii::t('app', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal']) .
                    Html::button(Html::icon('glyphicon glyphicon-ok') . ' ' . Yii::t('app', 'Save'), ['id' => 'btn-submit', 'class' => 'btn btn-success', 'type' => 'submit'])
                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                Yii::$app->getSession()->setFlash('success', $model->name . ' ' . <?= $generator->generateString('is successfully updated.') ?>);
                return $this->redirect(['view', <?= $urlParams ?>]);
            } else {
                return $this->render('update', ['model' => $model]);
            }
        }
    }

    /**
     * Delete an existing <?= $modelClass ?> model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionDelete(<?= $actionParams ?>)
    {
        $request = Yii::$app->request;
        $model = $this->findModel(<?= $actionParams ?>);
        $name = $model->name;

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $model->delete();

            $transaction->commit();

            Yii::$app->getSession()->setFlash('success', $name . ' ' . Yii::t('app', 'is successfully deleted.'));

            if ($request->isAjax) {
                /*
                 *   Process for ajax request
                 */
                return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
            } else {
                /*
                 *   Process for non-ajax request
                 */
                return $this->redirect(['index']);
            }
        } catch (IntegrityException $e) {
            $transaction->rollBack();
            return [
                'title' => Html::icon('glyphicon glyphicon-exclamation-sign white') . ' ' . Yii::t('app', 'Delete <?= strtolower($modelClass) ?>'),
                'size' => 'normal',
                'backgroundHeader' => 'btn-danger',
                'content' => Html::tag('span', 'Integrity error! ' . $e->getMessage(), ['class' => 'text-danger']),
                'footer' => Html::button(Html::icon('glyphicon glyphicon-remove') . ' ' . Yii::t('app', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal'])
            ];
        } catch (Exception $e) {
            $transaction->rollBack();
            return [
                'title' => Html::icon('glyphicon glyphicon-exclamation-sign white') . ' ' . Yii::t('app', 'Delete <?= strtolower($modelClass) ?>'),
                'size' => 'normal',
                'backgroundHeader' => 'btn-danger',
                'content' => Html::tag('span', 'Exception error! ' . $e->getMessage, ['class' => 'text-danger']),
                'footer' => Html::button(Html::icon('glyphicon glyphicon-remove') . ' ' . Yii::t('app', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal'])
            ];
        }
    }

     /**
     * Delete multiple existing <?= $modelClass ?> model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionBulkDelete()
    {        
        $request = Yii::$app->request;
        $pks = explode(',', $request->post( 'pks' )); // Array or selected records primary keys
        
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            foreach ( $pks as $pk ) {
                $model = $this->findModel($pk);
                $model->delete();
            }

            $transaction->commit();
        
            Yii::$app->getSession()->setFlash('success', <?= $generator->generateString($modelClass . 's are successfully deleted.') ?>);

            if($request->isAjax){
                /*
                *   Process for ajax request
                */
                return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
            }else{
                /*
                *   Process for non-ajax request
                */
                return $this->redirect(['index']);
            }
        } catch (IntegrityException $e) {
            $transaction->rollBack();
            return [
                'title' => Html::icon('glyphicon glyphicon-exclamation-sign white') . ' ' . Yii::t('app', 'Delete <?= strtolower($modelClass) ?>s'),
                'size' => 'normal',
                'backgroundHeader' => 'btn-danger',
                'content' => Html::tag('span', 'Integrity error! ' . $e->getMessage(), ['class' => 'text-danger']),
                'footer' => Html::button(Html::icon('glyphicon glyphicon-remove') . ' ' . Yii::t('app', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal'])
            ];
        } catch (Exception $e) {
            $transaction->rollBack();
            return [
                'title' => Html::icon('glyphicon glyphicon-exclamation-sign white') . ' ' . Yii::t('app', 'Delete <?= strtolower($modelClass) ?>s'),
                'size' => 'normal',
                'backgroundHeader' => 'btn-danger',
                'content' => Html::tag('span', 'Exception error! ' . $e->getMessage(), ['class' => 'text-danger']),
                'footer' => Html::button(Html::icon('glyphicon glyphicon-remove') . ' ' . Yii::t('app', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal'])
            ];
        }
    }

    /**
     * Finds the <?= $modelClass ?> model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return <?=                   $modelClass ?> the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(<?= $actionParams ?>)
    {
<?php
if (count($pks) === 1) {
    $condition = '$id';
} else {
    $condition = [];
    foreach ($pks as $pk) {
        $condition[] = "'$pk' => \$$pk";
    }
    $condition = '[' . implode(', ', $condition) . ']';
}
?>
        if (($model = <?= $modelClass ?>::findOne(<?= $condition ?>)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
     * Shows list of <?= strtolower($modelClass) . 's' ?>
     * @param type $q
     * @param type $id
     * @return type
     */
    public function action<?= $modelClass . 's' ?>List($q = null, $id = null) {
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];

        if (!empty($q)) {
            $query = new Query;
            $query->select('id, name AS text')
                    ->from(<?= $modelClass ?>::tableName())
                    ->where(['like', 'name', $q])
                    ->andWhere(['status' => <?= $modelClass ?>::ACTIVE])
                    ->limit(20);

            $command = $query->createCommand();
            $items = $command->queryAll();
            $out['results'] = array_values($items);
        } else if ($id > 0) {
            $item = <?= $modelClass ?>::find($id);
            $out['results'] = ['id' => $id, 'text' => $item->name];
        }

        return $out;
    }
}
