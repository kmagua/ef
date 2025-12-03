<?php

namespace app\modules\ef\controllers;

use app\modules\ef\models\EqualisationFundEntitlements;
use app\modules\ef\models\EqualisationFundEntitlementsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EqFundEntitlementsController implements the CRUD actions for EqualisationFundEntitlements model.
 */
class EqFundEntitlementsController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
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
     * Lists all EqualisationFundEntitlements models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new EqualisationFundEntitlementsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays analytics dashboard for EqualisationFundEntitlements models.
     *
     * @return string
     */
    public function actionAnalytics()
    {
        // Get all data for analytics
        $models = EqualisationFundEntitlements::find()->all();
        
        // Calculate aggregates
        $globalTotal = EqualisationFundEntitlements::find()->sum('ef_entitlement_ksh');
        $globalCount = EqualisationFundEntitlements::find()->count();
        $globalAudited = EqualisationFundEntitlements::find()->sum('audited_approved_revenue_ksh');
        $globalReflected = EqualisationFundEntitlements::find()->sum('amount_reflected_in_dora_ksh');
        $globalTransfers = EqualisationFundEntitlements::find()->sum('transfers_into_ef');
        $globalArrears = EqualisationFundEntitlements::find()->sum('arrears');

        // Get data by financial year
        $yearlyData = [];
        $years = EqualisationFundEntitlements::find()
            ->select('financial_year')
            ->distinct()
            ->orderBy('financial_year')
            ->column();

        foreach ($years as $year) {
            $yearData = EqualisationFundEntitlements::find()->where(['financial_year' => $year])->one();
            if ($yearData) {
                $yearlyData[] = [
                    'year' => $year,
                    'audited' => $yearData->audited_approved_revenue_ksh,
                    'entitlement' => $yearData->ef_entitlement_ksh,
                    'reflected' => $yearData->amount_reflected_in_dora_ksh,
                    'transfers' => $yearData->transfers_into_ef,
                    'arrears' => $yearData->arrears,
                ];
            }
        }

        return $this->render('analytics', [
            'models' => $models,
            'globalTotal' => $globalTotal,
            'globalCount' => $globalCount,
            'globalAudited' => $globalAudited,
            'globalReflected' => $globalReflected,
            'globalTransfers' => $globalTransfers,
            'globalArrears' => $globalArrears,
            'yearlyData' => $yearlyData,
        ]);
    }

    /**
     * Displays a single EqualisationFundEntitlements model.
     * @param int $id ID
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
     * Creates a new EqualisationFundEntitlements model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new EqualisationFundEntitlements();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EqualisationFundEntitlements model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing EqualisationFundEntitlements model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the EqualisationFundEntitlements model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return EqualisationFundEntitlements the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EqualisationFundEntitlements::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}