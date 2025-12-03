<?php

namespace app\modules\ef\controllers;

use app\modules\backend\models\DocumentLibrary;
use app\modules\backend\models\DocumentLibrarySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * DocumentLibraryController implements the CRUD actions for DocumentLibrary model.
 */
class DocumentLibraryController extends Controller
{
    /**
     * Set layout for EF module
     */
    public function init()
    {
        parent::init();
        if ($this->module && $this->module->id === 'ef') {
            $this->layout = '@app/modules/ef/views/layouts/layout'; // EF-specific layout with sidebar
        } elseif ($this->module && $this->module->id === 'backend') {
            $this->layout = '@app/modules/backend/views/layouts/main'; // Backend/IGFR-specific layout
        }
    }

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
                            'actions' => ['create', 'update', 'view', 'index', 'card'],
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
     * Lists all DocumentLibrary models.
     *
     * @return string
     */
public function actionIndex($tyid = null)
{
    // Instantiate search model and handle filtering
    $searchModel = new DocumentLibrarySearch();
    if (!empty($tyid)) {
        $searchModel->document_type = $tyid;
    }

    $dataProvider = $searchModel->search($this->request->queryParams);

    // Automatic filtering based on active module
    switch (\Yii::$app->controller->module->id) {
        case 'backend':
            $dataProvider->query->andWhere(['category' => 'igfr']);
            break;
        case 'ef':
            $dataProvider->query->andWhere(['category' => 'equalization_fund']);
            break;
        default:
            $dataProvider->query->andWhere(['category' => 'general']);
    }

    return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]);
}


    /**
     * Displays a single DocumentLibrary model.
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
public function actionPortalDocuments()
{
    return $this->render('portal-documents');
}


    /**
     * Creates a new DocumentLibrary model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new DocumentLibrary();
        $model->upload_date = date('Y-m-d');
        $model->uploaded_by = \Yii::$app->user->identity->id;
        // Automatically set category to equalization_fund for EF portal
        $model->category = 'equalization_fund';

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                // Ensure category is always equalization_fund for EF portal
                $model->category = 'equalization_fund';
                if ($model->saveWithFile()) {
                    \Yii::$app->session->setFlash('success', 'Document uploaded successfully!');
                    return $this->redirect(['/ef/default/card']);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DocumentLibrary model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->saveWithFile()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DocumentLibrary model.
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
     * Finds the DocumentLibrary model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return DocumentLibrary the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DocumentLibrary::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionCard()
    {
        return $this->render('document_types_view');
    }
}
