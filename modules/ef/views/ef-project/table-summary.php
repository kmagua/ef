<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
?>

<style>
    .card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }
    .card-header {
        background: linear-gradient(90deg, #198754, #157347);
        padding: 1.1rem 1.4rem;
        border-bottom: none;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }
    .table {
        border: 1px solid #dee2e6;
        margin-bottom: 0;
    }
    .table th {
        background: #198754;
        color: #ffff !important;
        font-weight: 500;
        vertical-align: middle;
    }
    .table td {
        vertical-align: middle;
        padding: 0.75rem;
        font-size: 0.95rem;
    }
    .table .fw-bold {
        font-weight: 600;
    }
    .table .bg-light {
        background-color: #f8f9fa !important;
    }
    .pagination .page-link:hover {
        background-color: #198754;
        color: #fff;
    }
    .alert {
        font-size: 1rem;
        border: 1px solid #c6c8ca;
        background: #f0f0f0;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
    .text-wrap {
        word-wrap: break-word;
    }
    .shadow {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    }
    .text-end {
        text-align: right !important;
    }
    h5.mb-0 {
        font-weight: 800;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>
<br>
<div class="container-fluid py-4">
    <div class="card shadow">
        <br><!-- comment -->
        <br>
        <div class="card-header text-white fw-bold">
            <h5 class="mb-0 text-white">📋 County Projects/Sector Allocation Vs. Disbursement Summary</h5>
        </div>
        <div class="card-body table-responsive">

            <table class="table table-bordered text-center align-middle">
                <thead>
                    <tr>
                        <th>County</th>
                        <th>Constituency</th>
                        <th>Sector</th>
                        <th>Project Name</th>
                        <th>Total Allocation (A)</th>
                        <th>Disbursed Amount (B)</th>
                        <th>% Disbursed</th>
                        <th>Amount Due (A - B)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($groupedData as $county => $countyDetails): ?>
                        <?php foreach ($countyDetails['constituencies'] as $constituency => $projects): ?>
                            <?php foreach ($projects as $project): ?>
                                <tr>
                                    <td><?= Html::encode($project['county']) ?></td>
                                    <td><?= Html::encode($project['constituency']) ?></td>
                                    <td><?= Html::encode($project['sector']) ?></td>
                                    <td class="text-wrap" style="max-width: 250px;"><?= Html::encode($project['project_name']) ?></td>
                                    <td class="text-end"><?= number_format($project['allocation'], 2) ?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>

                        <!-- County Totals -->
                        <?php 
                            $allocation = $countyDetails['total_allocation'];
                            $disbursed = $countyDetails['total_disbursed'] ?? 0;
                            $percentage = ($allocation > 0) ? ($disbursed / $allocation) * 100 : 0;
                            $due = $allocation - $disbursed;
                        ?>
                        <tr class="fw-bold bg-light">
                            <td><?= Html::encode($county) ?></td>
                            <td colspan="2" class="text-center">Total for County</td>
                            <td></td>
                            <td class="text-end"><?= number_format($allocation, 2) ?></td>
                            <td class="text-end"><?= number_format($disbursed, 2) ?></td>
                            <td><?= number_format($percentage, 2) ?>%</td>
                            <td class="text-end <?= ($due < 0) ? 'text-danger fw-bold' : '' ?>"><?= number_format($due, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Grand Total -->
            <div class="mt-4">
                <div class="alert alert-secondary fw-bold">
                    Grand Total Allocation: <?= number_format($grandTotalAllocation, 2) ?> | 
                    Grand Total Disbursed: <?= number_format($grandTotalDisbursed, 2) ?> |
                    % Disbursed: <?= ($grandTotalAllocation > 0) ? number_format(($grandTotalDisbursed / $grandTotalAllocation) * 100, 2) . '%' : '0%' ?>
                </div>
            </div>

            <div class="d-flex justify-content-center my-3">
                <?= LinkPager::widget([
                    'pagination' => $dataProvider->pagination,
                    'options' => ['class' => 'pagination pagination-lg'],
                    'linkOptions' => ['class' => 'page-link'],
                    'prevPageLabel' => '«',
                    'nextPageLabel' => '»',
                ]) ?>
            </div>

        </div>
    </div>
</div>
