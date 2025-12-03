<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string $county */

// Calculate the total of all amounts in the data provider
$total = 0;
foreach ($dataProvider->models as $model) {
    $total += $model->amount_disbursed;
}

$this->title = 'Disbursements by Sector for  ' . $county;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="disbursement-index">

    <!-- Heading -->
    <h3 style="
        color: #fff !important;
        background: linear-gradient(135deg, #008a8a, #00aaaa) !important;
        padding: 20px;
        border-radius: 8px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        font-size: 1.75rem;
        letter-spacing: 1.2px;
        margin-bottom: 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        text-transform: uppercase;
        text-align: center;
    ">
        <?= Html::encode($this->title) ?>
    </h3>

    <p>
        <!-- Back to Summaries button -->
        <?= Html::a(
            'Back to Summaries',
            ['summaries'],
            [
                'class' => 'btn btn-danger',
                'style' => 'float:right; margin-bottom:10px;',
            ]
        ) ?>
    </p>


    <!-- Table Container -->
    <div class="table-responsive advanced-table-container">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            // 'filterModel' => $searchModel,
            
            // Enable the table footer to show totals
            'showFooter' => true,
            // Apply custom classes to the table
            'tableOptions' => ['class' => 'table advanced-table'],
            // Style the footer row
            'footerRowOptions' => ['style' => 'background-color: #f2fafa; font-weight: bold;'],
            
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'sector',
                [
                    'attribute' => 'amount_disbursed',
                    'headerOptions' => ['style' => 'text-align: right;'],
                    'contentOptions' => ['class' => 'text-end'],
                    'footerOptions' => ['class' => 'text-end'],
                    'content' => function ($data) {
                        return 'KES. ' . number_format($data->amount_disbursed, 2);
                    },
                    // Display total in the footer cell
                    'footer' => 'KES. ' . number_format($total, 2),
                ],
            ],
        ]); ?>
    </div>
</div>

<?php
// Register advanced CSS
$this->registerCss("
    /* Container styling */
    .disbursement-index {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        margin-top: 20px;
    }

    /* Table container with rounded corners and shadow */
    .advanced-table-container {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        background-color: #fff;
        margin-top: 10px;
    }

    /* Advanced table styling */
    .advanced-table {
        margin: 0;
        border-collapse: separate;
        width: 100%;
        border-spacing: 0;
        border-radius: 8px;
        overflow: hidden;
    }
    
    /* White table header */
    .advanced-table thead {
        background: #fff !important;
        color: #333 !important;
        text-transform: uppercase;
    }
    .advanced-table thead th {
        padding: 12px;
        font-weight: bold;
        border-bottom: 2px solid #e0e0e0;
    }

    /* Body cells */
    .advanced-table td {
        padding: 12px;
        vertical-align: middle;
        border-bottom: 1px solid #e0e0e0;
    }

    /* Zebra striping */
    .advanced-table tbody tr:nth-child(odd) {
        background-color: #f2fafa;
    }

    /* Hover effect */
    .advanced-table tbody tr:hover {
        background-color: #d9f0f0;
        transition: background 0.3s ease-in-out;
    }

    /* Button styling */
    .btn-danger {
        font-weight: bold;
        background: linear-gradient(135deg, #ff5f5f, #ff9999) !important;
        color: #fff !important;
        border: none !important;
        transition: all 0.3s ease-in-out;
    }
    .btn-danger:hover {
        filter: brightness(1.1);
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
");
?>
