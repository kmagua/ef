<?php
$this->title = 'EF Backend Dashboard';
?>

<style>
    .backend-index {
        padding: 50px 0;
    }
    .dashboard-header {
        background: linear-gradient(90deg, #008a8a, #006666);
        color: #fff;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .dashboard-header h1 {
        font-weight: 700;
    }
    .dashboard-actions .btn {
        min-width: 200px;
        margin: 10px;
        transition: all 0.3s ease;
    }
    .dashboard-actions .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .stats-card {
        background: #ffffff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: 0.3s ease;
    }
    .stats-card:hover {
        transform: translateY(-3px);
    }
</style>

<div class="backend-index container">

    <!-- Header -->
    <div class="dashboard-header text-center">
        <h1><?= $this->title ?></h1>
        <p>Welcome to the Equalization Fund Backend Module</p>
    </div>

    <!-- Stats Section -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stats-card text-center">
                <h5>Total Projects</h5>
                <h2 class="text-success">24</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card text-center">
                <h5>Pending Approvals</h5>
                <h2 class="text-warning">5</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card text-center">
                <h5>Completed</h5>
                <h2 class="text-primary">19</h2>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-center dashboard-actions flex-wrap">
        <a href="<?= yii\helpers\Url::to(['/ef/ef-project/index']); ?>" class="btn btn-success">
            <i class="bi bi-folder"></i> Manage EF Projects
        </a>
        <a href="<?= yii\helpers\Url::to(['/ef/reports/index']); ?>" class="btn btn-info">
            <i class="bi bi-graph-up"></i> Reports & Analytics
        </a>
        <a href="<?= yii\helpers\Url::to(['/ef/settings']); ?>" class="btn btn-secondary">
            <i class="bi bi-gear"></i> Settings
        </a>
    </div>

</div>
