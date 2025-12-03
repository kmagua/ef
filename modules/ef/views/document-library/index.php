<?php

use app\modules\backend\models\DocumentLibrary;
use app\modules\backend\models\DocumentType;
use app\modules\backend\models\FinancialYear;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\DocumentLibrarySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Equalization Fund Document Library';
$dataProvider->pagination->pageSize = 15;
$this->params['breadcrumbs'][] = $this->title;

// Calculate analytics
$totalDocuments = DocumentLibrary::find()->where(['category' => 'equalization_fund'])->count();
$totalDocumentTypes = DocumentType::find()->count();
$recentUploads = DocumentLibrary::find()
    ->where(['category' => 'equalization_fund'])
    ->orderBy(['upload_date' => SORT_DESC])
    ->limit(5)
    ->count();
$documentsThisMonth = DocumentLibrary::find()
    ->where(['category' => 'equalization_fund'])
    ->andWhere(['>=', 'upload_date', date('Y-m-01')])
    ->count();

// Documents by type
$documentsByType = DocumentLibrary::find()
    ->select(['document_type', 'COUNT(*) as count'])
    ->where(['category' => 'equalization_fund'])
    ->groupBy('document_type')
    ->asArray()
    ->all();

// Documents by financial year
$documentsByYear = DocumentLibrary::find()
    ->select(['financial_year', 'COUNT(*) as count'])
    ->where(['category' => 'equalization_fund'])
    ->groupBy('financial_year')
    ->asArray()
    ->all();

$this->registerCssFile('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
?>

<style>
    :root {
        --primary-color: #008a8a;
        --primary-dark: #006666;
        --primary-light: #e0f7fa;
        --accent-color: #ffc107;
        --success-color: #28a745;
        --danger-color: #dc3545;
        --warning-color: #ff9800;
        --info-color: #17a2b8;
        --text-dark: #333;
        --text-light: #666;
        --bg-light: #f8f9fa;
        --border-color: #dee2e6;
        --shadow: 0 2px 10px rgba(0,0,0,0.08);
        --shadow-hover: 0 4px 20px rgba(0,0,0,0.12);
    }

    body {
        font-family: 'Poppins', sans-serif !important;
        background: var(--bg-light);
    }

    .document-library-wrapper {
        padding: 20px;
        background: #fff;
        border-radius: 12px;
        box-shadow: var(--shadow);
        margin-bottom: 20px;
    }

    /* Header Section */
    .page-header {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: #fff;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: var(--shadow);
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }

    .page-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 10px 0;
        position: relative;
        z-index: 1;
    }

    .page-header p {
        margin: 0;
        opacity: 0.9;
        font-size: 1rem;
        position: relative;
        z-index: 1;
    }

    /* Analytics Cards */
    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .analytics-card {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        border-left: 4px solid var(--primary-color);
        position: relative;
        overflow: hidden;
    }

    .analytics-card::before {
        content: '';
        position: absolute;
        top: -20px;
        right: -20px;
        width: 80px;
        height: 80px;
        background: var(--primary-light);
        border-radius: 50%;
        opacity: 0.3;
    }

    .analytics-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }

    .analytics-card.primary { border-left-color: var(--primary-color); }
    .analytics-card.success { border-left-color: var(--success-color); }
    .analytics-card.warning { border-left-color: var(--warning-color); }
    .analytics-card.info { border-left-color: var(--info-color); }

    .analytics-card .card-icon {
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 2.5rem;
        opacity: 0.2;
        color: var(--primary-color);
    }

    .analytics-card.primary .card-icon { color: var(--primary-color); }
    .analytics-card.success .card-icon { color: var(--success-color); }
    .analytics-card.warning .card-icon { color: var(--warning-color); }
    .analytics-card.info .card-icon { color: var(--info-color); }

    .analytics-card .card-label {
        font-size: 0.9rem;
        color: var(--text-light);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
        font-weight: 500;
    }

    .analytics-card .card-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 5px;
    }

    .analytics-card .card-subtext {
        font-size: 0.85rem;
        color: var(--text-light);
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }

    .btn-modern {
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: var(--shadow);
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
    }

    .btn-primary-modern {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: #fff;
    }

    .btn-primary-modern:hover {
        background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
        color: #fff;
    }

    /* GridView Styling */
    .table-responsive {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: var(--shadow);
    }

    .table thead th {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: #fff !important;
        border: none;
        padding: 15px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .table thead th a {
        color: #fff !important;
        text-decoration: none;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: var(--primary-light);
        transform: scale(1.01);
    }

    .table tbody td {
        padding: 15px;
        border-color: var(--border-color);
        vertical-align: middle;
    }

    .table tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    /* Badge Styling */
    .badge-modern {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-primary-modern {
        background: var(--primary-light);
        color: var(--primary-color);
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 25px;
        gap: 5px;
    }

    .pagination li a,
    .pagination li span {
        padding: 10px 15px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        color: var(--primary-color);
        transition: all 0.3s ease;
    }

    .pagination li.active span {
        background: var(--primary-color);
        color: #fff;
        border-color: var(--primary-color);
    }

    .pagination li a:hover {
        background: var(--primary-light);
        border-color: var(--primary-color);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .analytics-grid {
            grid-template-columns: 1fr;
        }
        .action-buttons {
            flex-direction: column;
        }
        .btn-modern {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="document-library-wrapper">
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-folder-open"></i> <?= Html::encode($this->title) ?></h1>
        <p>Manage and access all Equalization Fund documents in one place</p>
    </div>

    <!-- Analytics Cards -->
    <div class="analytics-grid">
        <div class="analytics-card primary">
            <i class="fas fa-file-alt card-icon"></i>
            <div class="card-label">Total Documents</div>
            <div class="card-value"><?= number_format($totalDocuments) ?></div>
            <div class="card-subtext">All Equalization Fund documents</div>
        </div>

        <div class="analytics-card success">
            <i class="fas fa-upload card-icon"></i>
            <div class="card-label">This Month</div>
            <div class="card-value"><?= number_format($documentsThisMonth) ?></div>
            <div class="card-subtext">Documents uploaded this month</div>
        </div>

        <div class="analytics-card warning">
            <i class="fas fa-tags card-icon"></i>
            <div class="card-label">Document Types</div>
            <div class="card-value"><?= number_format($totalDocumentTypes) ?></div>
            <div class="card-subtext">Available document categories</div>
        </div>

        <div class="analytics-card info">
            <i class="fas fa-clock card-icon"></i>
            <div class="card-label">Recent Uploads</div>
            <div class="card-value"><?= number_format($recentUploads) ?></div>
            <div class="card-subtext">Latest 5 documents</div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <?= Html::a('<i class="fas fa-plus-circle"></i> Upload New Document', ['create'], [
            'class' => 'btn btn-modern btn-primary-modern'
        ]) ?>
        <?= Html::a('<i class="fas fa-list"></i> View All Types', ['/ef/default/card'], [
            'class' => 'btn btn-modern',
            'style' => 'background: #fff; color: var(--primary-color); border: 2px solid var(--primary-color);'
        ]) ?>
    </div>

    <!-- Documents Table -->
    <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table table-striped table-hover'],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'document_name',
                    'label' => 'Document Name',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Html::a(
                            Html::encode($model->document_name),
                            ['view', 'id' => $model->id],
                            ['style' => 'color: var(--primary-color); font-weight: 500;']
                        );
                    },
                ],
                [
                    'attribute' => 'document_type',
                    'label' => 'Document Type',
                    'value' => function ($model) {
                        return $model->documentType ? $model->documentType->document_type : 'N/A';
                    },
                    'filter' => \yii\helpers\ArrayHelper::map(
                        DocumentType::find()->all(),
                        'id',
                        'document_type'
                    ),
                ],
                [
                    'attribute' => 'financial_year',
                    'label' => 'Financial Year',
                    'value' => function ($model) {
                        return $model->financialYear ? $model->financialYear->financial_year : 'N/A';
                    },
                    'filter' => \yii\helpers\ArrayHelper::map(
                        FinancialYear::find()->all(),
                        'id',
                        'financial_year'
                    ),
                ],
                [
                    'label' => 'Document',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->fileLink();
                    }
                ],
                [
                    'attribute' => 'uploadedBy.user_names',
                    'label' => 'Uploaded By',
                    'value' => function ($model) {
                        return $model->uploadedBy ? $model->uploadedBy->user_names : 'Unknown';
                    },
                ],
                [
                    'attribute' => 'upload_date',
                    'label' => 'Upload Date',
                    'format' => ['date', 'php:d M Y'],
                ],
                [
                    'class' => ActionColumn::className(),
                    'header' => 'Actions',
                    'headerOptions' => ['style' => 'color: #fff;'],
                    'contentOptions' => ['style' => 'white-space: nowrap;'],
                    'template' => '{view} {update} {delete}',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Html::a('<i class="fas fa-eye"></i>', $url, [
                                'class' => 'btn btn-sm btn-info',
                                'title' => 'View',
                                'style' => 'margin-right: 5px;'
                            ]);
                        },
                        'update' => function ($url, $model) {
                            return Html::a('<i class="fas fa-edit"></i>', $url, [
                                'class' => 'btn btn-sm btn-warning',
                                'title' => 'Update',
                                'style' => 'margin-right: 5px;'
                            ]);
                        },
                        'delete' => function ($url, $model) {
                            return Html::a('<i class="fas fa-trash"></i>', $url, [
                                'class' => 'btn btn-sm btn-danger',
                                'title' => 'Delete',
                                'data-confirm' => 'Are you sure you want to delete this item?',
                                'data-method' => 'post',
                            ]);
                        },
                    ],
                    'urlCreator' => function ($action, $model, $key, $index) {
                        return Url::toRoute([$action, 'id' => $model->id]);
                    }
                ],
            ],
        ]); ?>
    </div>
</div>
