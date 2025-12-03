<?php

namespace app\modules\ef\controllers;

use app\modules\ef\models\Allocation;
use app\modules\ef\models\AllocationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii;
use Dompdf\Dompdf;
use Dompdf\Options;
/**
 * AllocationController implements the CRUD actions for Allocation model.
 */
class AllocationController extends Controller
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
                            'actions' => ['create','generate-report','view','update', 'index'],
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
     * Lists all Allocation models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AllocationSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Allocation model.
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
     * Creates a new Allocation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Allocation();

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
     * Updates an existing Allocation model.
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

public function actionGenerateReport()
{
    // Fetch allocation data from the database
    $allocations = Allocation::find()->asArray()->all();

    if (!$allocations) {
        Yii::$app->session->setFlash('error', 'No allocation data found.');
        return $this->redirect(['index']);
    }

    // Process data for financial year comparison
    $groupedData = [];
    $totalAuditedRevenues = 0;
    $totalEFAllocation = 0;
    $totalEFEntitlement = 0;
    $totalAmountDora = 0;

    foreach ($allocations as $allocation) {
        $fy = $allocation['financial_year'];
        if (!isset($groupedData[$fy])) {
            $groupedData[$fy] = [
                'audited_revenues' => 0,
                'ef_allocation' => 0,
                'ef_entitlement' => 0,
                'amount_reflected_dora' => 0,
            ];
        }

        $groupedData[$fy]['audited_revenues'] += $allocation['audited_revenues'];
        $groupedData[$fy]['ef_allocation'] += $allocation['ef_allocation'];
        $groupedData[$fy]['ef_entitlement'] += $allocation['ef_entitlement'];
        $groupedData[$fy]['amount_reflected_dora'] += $allocation['amount_reflected_dora'];

        $totalAuditedRevenues += $allocation['audited_revenues'];
        $totalEFAllocation += $allocation['ef_allocation'];
        $totalEFEntitlement += $allocation['ef_entitlement'];
        $totalAmountDora += $allocation['amount_reflected_dora'];
    }

    // Load Logo
    $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
    $logoUrl = file_exists($imagePath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($imagePath)) : '';

    // Generate HTML content for PDF
    $html = '<style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        th { background-color: #1b5e20; color: white; font-weight: bold; }
        .header { text-align: center; }
        .signature { margin-top: 30px; font-size: 14px; text-align: left; }
    </style>';

    // Header with Logo
    $html .= '<div class="header">';
    if ($logoUrl) {
        $html .= "<img src='{$logoUrl}' style='width:150px;'/><br>";
    }
    $html .= '<h2 style="text-align:center;">Equalization Fund Allocations Report</h2>';
    $html .= '<p style="text-align:center; font-size:10px; color:gray;">Generated by FiscalBridge Portal - ICTS - JKM, CISA</p>';
    $html .= '</div>';

    // Summary Section
    $html .= '<h3 style="text-align:center; background-color:#1b5e20; color:white; padding:10px;">Overall Summary</h3>';
    $html .= '<table border="1" width="100%" cellspacing="0" cellpadding="5">
                <tr>
                    <th>Total Audited Revenues</th>
                    <th>Total EF Allocation</th>
                    <th>Total EF Entitlement</th>
                    <th>Total Amount (DORA)</th>
                </tr>
                <tr>
                    <td>' . number_format($totalAuditedRevenues, 2) . '</td>
                    <td>' . number_format($totalEFAllocation, 2) . '</td>
                    <td>' . number_format($totalEFEntitlement, 2) . '</td>
                    <td>' . number_format($totalAmountDora, 2) . '</td>
                </tr>
              </table>';

    // Detailed Breakdown Section
    $html .= '<h3 style="text-align:center;">Breakdown by Financial Year</h3>';
    $html .= '<table border="1" width="100%" cellspacing="0" cellpadding="5">
                <thead>
                    <tr>
                        <th>Financial Year</th>
                        <th>Audited Revenues</th>
                        <th>EF Allocation</th>
                        <th>EF Entitlement</th>
                        <th>Amount (DORA)</th>
                    </tr>
                </thead>
                <tbody>';
    foreach ($groupedData as $year => $values) {
        $html .= "<tr>
                    <td>{$year}</td>
                    <td>" . number_format($values['audited_revenues'], 2) . "</td>
                    <td>" . number_format($values['ef_allocation'], 2) . "</td>
                    <td>" . number_format($values['ef_entitlement'], 2) . "</td>
                    <td>" . number_format($values['amount_reflected_dora'], 2) . "</td>
                  </tr>";
    }
    $html .= '</tbody></table>';
 // Signature section
    $html .= '
    <div class="signature">
        <p><strong>Signed By:</strong></p>
        <p>__________________________</p>
        <p><strong>Mr. Guyo Boru</strong></p>
        <p>Chief Executive Officer</p>
        <p>Date: ' . date('F d, Y') . '</p>
    </div>';
    
    // Generate PDF using Dompdf
    $options = new \Dompdf\Options();
    $options->set('defaultFont', 'Arial');
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    // Stream the PDF to the browser for direct download
    return Yii::$app->response->sendContentAsFile(
        $dompdf->output(),
        'Allocations_Report.pdf',
        ['mimeType' => 'application/pdf']
    );
}



    /**
     * Deletes an existing Allocation model.
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
     * Finds the Allocation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Allocation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Allocation::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
