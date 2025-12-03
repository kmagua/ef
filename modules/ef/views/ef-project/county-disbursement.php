<?php
use yii\helpers\Html;
use yii\data\Pagination;
use yii\widgets\LinkPager;

// Title and breadcrumbs
$this->title = 'County-Level Data Overview';
$this->params['breadcrumbs'][] = $this->title;

// Pagination Setup
$pageSize = 15;
$pagination = new Pagination([
    'totalCount' => count($groupedData),
    'defaultPageSize' => $pageSize,
]);
$pagedData = array_slice($groupedData, $pagination->offset, $pagination->limit);
?>

<style>
    :root {
        --primary-color: #008a8a;
        --secondary-color: #2C3E50;
        --accent-color: #E67E22;
        --danger-color: #E74C3C;
        --light-bg: #F8F9FA;
    }

    body {
        background-color: var(--secondary-color);
        color: #ECF0F1;
        font-family: 'Poppins', sans-serif;
    }

    .equalization-report {
        margin-top: 20px;
    }

    .table th {
        background: var(--primary-color);
        color: #fff !important;
        font-weight: 600;
        vertical-align: middle;
    }

    .table td {
        vertical-align: middle;
        padding: 0.75rem;
        font-size: 0.95rem;
    }

    .fw-bold {
        font-weight: 700;
    }

    .pagination .page-item .page-link {
        background-color: #34495E;
        color: #ECF0F1;
        border-radius: 5px;
        margin: 0 3px;
        border: none;
    }

    .pagination .page-item .page-link:hover {
        background-color: #1F2C38;
    }

    .pagination .page-item.active .page-link {
        background-color: var(--accent-color) !important;
        border-color: var(--accent-color);
        font-weight: bold;
    }

    .card {
        border: none;
        box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.3);
    }

    .text-primary {
        color: var(--accent-color) !important;
    }

    .text-danger {
        color: var(--danger-color) !important;
    }

    .bg-light {
        background-color: var(--light-bg) !important;
    }
</style>

<div class="card shadow">
    <div class="card-header" style="background-color: var(--primary-color); color: #fff;">
        📋 Project Allocation & Disbursement Summary
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center">
                <thead>
                    <tr>
                        <th>County</th>
                        <th>Constituency</th>
                        <th>Sector</th>
                        <th>Project Name</th>
                        <th>Total Allocation (A)</th>
                        <th>Disbursed Amount (B)</th>
                        <th>% Disbursed (B / A * 100)</th>
                        <th>Amount Due (A - B)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pagedData as $county => $details): ?>
                        <?php
                            $countyTotalAllocation = $details['total_allocation'];
                            $countyTotalDisbursed = $countyDisbursementData[$county] ?? 0;
                            $percentageDisbursed = ($countyTotalAllocation > 0) ? ($countyTotalDisbursed / $countyTotalAllocation) * 100 : 0;
                            $amountDue = $countyTotalAllocation - $countyTotalDisbursed;
                        ?>
                        <!-- Constituency Breakdown -->
                        <?php if (!empty($details['constituencies'])): ?>
                            <?php foreach ($details['constituencies'] as $constituency => $projects): ?>
                                <?php $constituencyPrinted = false; ?>
                                <?php foreach ($projects as $project): ?>
                                    <tr>
                                        <td></td>
                                        <?php if (!$constituencyPrinted): ?>
                                            <td rowspan="<?= count($projects) ?>" class="align-middle"><?= Html::encode($constituency) ?></td>
                                            <?php $constituencyPrinted = true; ?>
                                        <?php endif; ?>
                                        <td><?= Html::encode($project['sector']) ?></td>
                                        <td><?= Html::encode($project['project_name']) ?></td>
                                        <td class="text-end"><?= number_format($project['allocation'], 2) ?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <!-- County Totals -->
                        <tr class="fw-bold bg-light">
                            <td><?= Html::encode($county) ?></td>
                            <td colspan="2" class="text-center">Total for County</td>
                            <td></td>
                            <td class="text-end"><?= number_format($countyTotalAllocation, 2) ?></td>
                            <td class="text-end"><?= $countyTotalDisbursed > 0 ? number_format($countyTotalDisbursed, 2) : '' ?></td>
                            <td class="text-center fw-bold text-primary">
                                <?= $countyTotalDisbursed > 0 ? number_format($percentageDisbursed, 2) . '%' : '' ?>
                            </td>
                            <td class="text-end <?= ($amountDue < 0) ? 'text-danger fw-bold' : '' ?>">
                                <?= number_format($amountDue, 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Grand Totals -->
                    <tr class="fw-bold" style="background-color: var(--primary-color); color: #fff;">
                        <td colspan="4" class="text-center">Grand Total</td>
                        <td class="text-end"><?= number_format($grandTotalAllocation, 2) ?></td>
                        <td class="text-end"><?= number_format(array_sum($countyDisbursementData), 2) ?></td>
                        <td class="text-center">
                            <?= ($grandTotalAllocation > 0) ? number_format((array_sum($countyDisbursementData) / $grandTotalAllocation) * 100, 2) . '%' : '' ?>
                        </td>
                        <td class="text-end <?= ($grandTotalAllocation - array_sum($countyDisbursementData) < 0) ? 'text-danger fw-bold' : '' ?>">
                            <?= number_format($grandTotalAllocation - array_sum($countyDisbursementData), 2) ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            <?= LinkPager::widget([
                'pagination' => $pagination,
                'options' => ['class' => 'pagination'],
                'linkOptions' => ['class' => 'page-link'],
                'prevPageLabel' => '«',
                'nextPageLabel' => '»',
            ]) ?>
        </div>
    </div>
</div>
