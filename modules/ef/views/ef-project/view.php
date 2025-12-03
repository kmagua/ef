<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\bootstrap5\Modal;
use yii\web\View;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\EqualizationFundProject $model */

$this->title = Html::encode($model->project_name);
$this->params['breadcrumbs'][] = ['label' => 'Equalization Fund Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

// Register Poppins + Animate.css for subtle animations
$this->registerCssFile('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap', ['rel' => 'stylesheet']);
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css');

// Custom advanced styling
$this->registerCss("
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #eef1f6;
    }
    .project-container {
        max-width: 950px;
        margin: auto;
    }
    .card-header {
        background: linear-gradient(45deg, #00695c, #004d40);
        color: white;
        padding: 25px;
        text-align: center;
        font-size: 1.75rem;
        font-weight: 600;
        border-radius: 12px 12px 0 0;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }
    .breadcrumb {
        position: sticky;
        top: 10px;
        z-index: 1000;
        background: #fff !important;
        border: 1px solid #e0e0e0;
    }
    .btn-icon {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.3s ease;
    }
    .btn-icon:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .progress {
        height: 28px;
        font-weight: bold;
        border-radius: 7px;
        transition: width 0.6s ease-in-out;
        background-color: #f1f1f1;
    }
    .progress-bar {
        font-weight: bold;
        line-height: 28px;
        font-size: 0.95rem;
    }
    .badge-funding {
        font-size: 14px;
        padding: 8px 14px;
        font-weight: bold;
        border-radius: 20px;
        transition: background 0.3s ease;
    }
    .badge-funding:hover {
        background: #004d40 !important;
        color: #fff;
    }
    .section-divider {
        border-top: 2px dashed #bdbdbd;
        margin: 30px 0;
    }
    .modal-header {
        background-color: #d32f2f;
        color: white;
    }
");

?>

<div class="equalization-fund-project-view container mt-4 project-container">

    <!-- BREADCRUMBS -->
    <nav aria-label="breadcrumb" class="animate__animated animate__fadeInDown">
        <ol class="breadcrumb bg-white p-2 shadow-sm rounded">
            <li class="breadcrumb-item"><a href="<?= Url::to(['index']) ?>" class="text-success">Projects</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= Html::encode($this->title) ?></li>
        </ol>
    </nav>

    <!-- PROJECT DETAILS CARD -->
    <div class="card shadow-lg border-0 animate__animated animate__fadeInUp">

        <!-- CARD HEADER -->
        <div class="card-header">
            <?= Html::encode($model->project_name) ?>
        </div>

        <!-- CARD BODY -->
        <div class="card-body">
            <!-- Action Buttons -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="btn-group" role="group" aria-label="Actions">
                    <?= Html::a('<i class="bi bi-pencil-square"></i> Update', ['update', 'id' => $model->id], [
                        'class' => 'btn btn-primary btn-icon',
                        'title' => 'Edit project',
                        'data-bs-toggle' => 'tooltip'
                    ]) ?>
                    <button type="button" class="btn btn-danger btn-icon" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete project" data-bs-toggle="tooltip">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>
                <?= Html::a('<i class="bi bi-arrow-left"></i> Back', ['index'], [
                    'class' => 'btn btn-outline-secondary btn-icon',
                    'title' => 'Back to list',
                    'data-bs-toggle' => 'tooltip'
                ]) ?>
            </div>

            <!-- Divider -->
            <div class="section-divider"></div>

            <!-- PROJECT DETAILS TABLE -->
            <div class="table-responsive">
                <?= DetailView::widget([
                    'model' => $model,
                    'options' => ['class' => 'table table-bordered table-hover table-striped'],
                    'attributes' => [
                        'id',
                        [
                            'attribute' => 'county',
                            'contentOptions' => ['class' => 'fw-bold text-primary'],
                        ],
                        [
                            'attribute' => 'constituency',
                            'contentOptions' => ['class' => 'fw-bold text-info'],
                        ],
                        [
                            'attribute' => 'sector',
                            'contentOptions' => ['class' => 'fw-bold text-success'],
                        ],
                        [
                            'attribute' => 'budget_2018_19',
                            'label' => 'Budget (2018-19)',
                            'value' => '$' . number_format($model->budget_2018_19, 2),
                            'contentOptions' => ['class' => 'fw-bold text-end text-primary'],
                        ],
                        [
                            'attribute' => 'percent_completion',
                            'label' => 'Completion Status',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $completion = $model->percent_completion;
                                $color = $completion < 50 ? 'bg-danger' : ($completion < 75 ? 'bg-warning' : 'bg-success');
                                return "<div class='progress' aria-valuemin='0' aria-valuemax='100' aria-valuenow='{$completion}'>
                                            <div class='progress-bar $color' role='progressbar' style='width: {$completion}%;'>
                                                {$completion}%
                                            </div>
                                        </div>";
                            },
                        ],
                        [
                            'attribute' => 'funding_source',
                            'format' => 'raw',
                            'value' => '<span class="badge bg-primary badge-funding">' . Html::encode($model->funding_source) . '</span>',
                            'contentOptions' => ['class' => 'text-center'],
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>

<!-- DELETE CONFIRMATION MODAL -->
<?php Modal::begin([
    'id' => 'deleteModal',
    'title' => '<h5 class="modal-title text-white">Confirm Deletion</h5>',
    'options' => ['class' => 'modal fade'],
    'footer' => Html::a('Cancel', '#', ['class' => 'btn btn-secondary', 'data-bs-dismiss' => 'modal']) .
                Html::a('Delete', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => ['method' => 'post'],
                ]),
]); ?>
<p>Are you sure you want to delete this project? This action cannot be undone.</p>
<?php Modal::end(); ?>

<?php
// Enable Bootstrap tooltips
$this->registerJs("var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle=\"tooltip\"]')); tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl); });");
?>
