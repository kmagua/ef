<?php

namespace app\modules\backend\controllers;

use app\modules\backend\models\EquitableRevenueShare;
use app\modules\backend\models\EquitableShareSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Dompdf\Dompdf;
use yii\helpers\Html;
use Dompdf\Options;
use yii;
/**
 * EquitableRevenueController implements the CRUD actions for EquitableRevenueShare model.
 */
class EquitableRevenueController extends Controller
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
                            'actions' => ['create', 'update', 'view','report', 'index'],
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
     * Lists all EquitableRevenueShare models.
     *
     * @return string
     */
  public function actionIndex()
{
    $searchModel = new EquitableShareSearch();
    $queryParams = Yii::$app->request->queryParams;

    // Initialize default values for filters
    $selectedCounty = $queryParams['EquitableShareSearch']['county_id'] ?? 'All';
    $selectedYear = $queryParams['EquitableShareSearch']['fy'] ?? 'All';

    // Remove filters if "All" is selected
    if ($selectedCounty === 'All') {
        unset($queryParams['EquitableShareSearch']['county_id']);
    }

    if ($selectedYear === 'All') {
        unset($queryParams['EquitableShareSearch']['fy']);
    }

    // Search using the modified query parameters
    $dataProvider = $searchModel->search($queryParams);

    return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'selectedCounty' => $selectedCounty,
        'selectedYear' => $selectedYear,
    ]);
}

    /**
     * Displays a single EquitableRevenueShare model.
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
     * Creates a new EquitableRevenueShare model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new EquitableRevenueShare();

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

public function actionReport()
{
    $searchModel = new EquitableShareSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $records = $dataProvider->getModels();

    // Capture selected filters
    $selectedCounty = Yii::$app->request->get('county_id', 'All');
    $selectedYear = Yii::$app->request->get('fy', 'All');

    // Filter data manually
    if ($selectedCounty !== 'All') {
        $records = array_filter($records, fn($record) => $record->county_id == $selectedCounty);
    }
    if ($selectedYear !== 'All') {
        $records = array_filter($records, fn($record) => $record->fy == $selectedYear);
    }

    // Calculate Totals
    $totalProjectAmt = array_sum(array_map(fn($record) => $record->project_amt, $records));
    $totalActualAmt = array_sum(array_map(fn($record) => $record->actual_amt ?? 0, $records));

    // Load Logo Path (Ensure it's publicly accessible)
    $logoUrl = Yii::getAlias('@web') . '/uploads/reportlogo.jpg';

    // Initialize Dompdf
    $options = new Options();
    $options->set('defaultFont', 'Poppins');
    $dompdf = new Dompdf($options);

    // Build the HTML
    $html = "
    <html>
    <head>
        <style>
            body { font-family: 'Poppins', sans-serif; }
            h2 { text-align: center; color: #228B22; }
            h3 { text-align: center; background: #DAA520; color: white; padding: 10px; border-radius: 5px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
            th { background: #228B22; color: white; text-transform: uppercase; }
            tr:nth-child(even) { background: #f8f9fa; }
            tr:hover { background: #e9ecef; }
            .footer { margin-top: 20px; text-align: center; font-size: 14px; color: #555; }
            .signature { margin-top: 30px; text-align: right; font-size: 12px; font-weight: bold; }
            .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
            .header img { width: 120px; }
        </style>
    </head>
    <body>
        <!-- Report Header -->
        <div class='header'>
            <img src='{$logoUrl}' alt='Report Logo'>
            <h2>County Equitable Revenue Report</h2>
        </div>

        <h3>Filters: County - " . ($selectedCounty === 'All' ? 'All Counties' : Html::encode($selectedCounty)) . ", Year - " . ($selectedYear === 'All' ? 'All Years' : Html::encode($selectedYear)) . "</h3>
        <hr>

        <!-- Report Table -->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>County</th>
                    <th>Financial Year</th>
                    <th>Projected Amount (KES)</th>
                    <th>Actual Amount (KES)</th>
                </tr>
            </thead>
            <tbody>";

    // Table Data
    $count = 1;
    foreach ($records as $record) {
        $html .= "
            <tr>
                <td>{$count}</td>
                <td>" . Html::encode($record->county->CountyName) . "</td>
                <td>" . Html::encode($record->fy) . "</td>
                <td>" . number_format($record->project_amt, 2) . "</td>
                <td>" . number_format($record->actual_amt ?? 0, 2) . "</td>
            </tr>";
        $count++;
    }

    // Append Total Row
    $html .= "
            <tr style='background:#DAA520; color:white; font-weight:bold;'>
                <td colspan='3'>Total</td>
                <td>" . number_format($totalProjectAmt, 2) . "</td>
                <td>" . number_format($totalActualAmt, 2) . "</td>
            </tr>
            </tbody>
        </table>

        <!-- Signature -->
        <div class='signature'>
            <p>Prepared by: <strong>JKM, CISA</strong></p>
            <p>IGFR Portal</p>
            <p>Date: " . date('Y-m-d') . "</p>
        </div>

        <div class='footer'>Generated on " . date('Y-m-d H:i:s') . "</div>
    </body>
    </html>";

    // Load and Render PDF
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    // Stream the PDF for download
    $dompdf->stream("Equitable_Revenue_Report.pdf", ["Attachment" => true]); 
    exit;
}


    /**
     * Updates an existing EquitableRevenueShare model.
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
     * Deletes an existing EquitableRevenueShare model.
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
     * Finds the EquitableRevenueShare model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return EquitableRevenueShare the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquitableRevenueShare::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
