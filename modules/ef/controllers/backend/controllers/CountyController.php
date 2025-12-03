<?php

namespace app\modules\backend\controllers;

use app\modules\backend\models\County;
use app\modules\backend\models\CountySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * CountyController implements the CRUD actions for County model.
 */
class CountyController extends Controller
{
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
     * Lists all County models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CountySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single County model.
     * @param int $CountyId County ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($CountyId)
    {
        return $this->render('view', [
            'model' => $this->findModel($CountyId),
        ]);
    }

    /**
     * Creates a new County model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new County();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'CountyId' => $model->CountyId]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing County model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $CountyId County ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($CountyId)
    {
        $model = $this->findModel($CountyId);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'CountyId' => $model->CountyId]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing County model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $CountyId County ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($CountyId)
    {
        $this->findModel($CountyId)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the County model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $CountyId County ID
     * @return County the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($CountyId)
    {
        if (($model = County::findOne(['CountyId' => $CountyId])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
