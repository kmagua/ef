<?php

namespace app\modules\backend\controllers;

use app\modules\backend\models\Fiscal;
use app\modules\backend\models\FiscalSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii;
/**
 * FiscalController implements the CRUD actions for Fiscal model.
 */
class FiscalController extends Controller
{
    
 
    public $cnt_name;  // ✅ Used for county name filtering
    public $countyid; 
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    //'only' => ['index', ],
                    'rules' => [
                        [
                            'actions' => ['create', 'update', 'view', 'index'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],                        
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Fiscal models.
     *
     * @return string
     */
public function actionIndex()
{
    $searchModel = new FiscalSearch();
    $queryParams = Yii::$app->request->queryParams;
    $dataProvider = $searchModel->search($queryParams);

    // Ensure search filters are applied properly
    if (!empty($queryParams['FiscalSearch']['countyid'])) {
        $selectedCounty = $queryParams['FiscalSearch']['countyid'];
    } else {
        $selectedCounty = null;
    }

    if (!empty($queryParams['FiscalSearch']['fy'])) {
        $selectedYear = $queryParams['FiscalSearch']['fy'];
    } else {
        $selectedYear = null;
    }

    // Grouped DataProvider for Financial Year Summary
    $groupedQuery = Fiscal::find()
        ->select(['fy', 'SUM(development_budgement + recurrent_budget) AS total_project_amt'])
        ->groupBy('fy')
        ->orderBy(['fy' => SORT_DESC]);

    $groupedProvider = new \yii\data\ArrayDataProvider([
        'allModels' => $groupedQuery->asArray()->all(),
        'pagination' => false,
    ]);

    return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'groupedProvider' => $groupedProvider, // Pass the grouped data
        'selectedCounty' => $selectedCounty,
        'selectedYear' => $selectedYear,
    ]);
}

    /**
     * Displays a single Fiscal model.
     * @param string $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Fiscal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Fiscal();
        $model->added_by = \Yii::$app->user->identity->id;

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['index']);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Fiscal model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Fiscal model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Fiscal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Fiscal the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Fiscal::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
