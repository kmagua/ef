<?php
$this->title = 'IGFRD Documents Per Type';
$this->params['breadcrumbs'][] = $this->title;
$document_types = \app\modules\backend\models\DocumentType::find()->all();
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
<style>
    /* General Styles */
    .container {
        padding: 20px;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }

    .row {
        margin-top: 20px;
    }

    /* Card styles */
    .card {
        border-radius: 10px; /* Slightly smaller radius */
        background-color: #ffffff;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1); /* Lighter shadow for a cleaner look */
        transition: all 0.3s ease-in-out;
        margin: 5px; /* Added a small margin around cards */
    }

    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15); /* More subtle hover effect */
        background-color: #e8f5e9;
    }

    .card-body {
        padding: 15px; /* Reduced padding */
        color: #333;
    }

    .card i {
        color: #5c67f2;
        margin-bottom: 5px; /* Reduced space under the icon */
    }

    .card h6 {
        font-size: 1.2rem; /* Smaller heading */
        font-weight: 700;
        margin-top: 5px; /* Reduced margin */
    }

    .card p {
        color: #555;
        font-size: 1rem; /* Smaller paragraph text */
    }

    a.no-hover-effect {
        color: inherit;
        text-decoration: none;
    }

    a.no-hover-effect:hover {
        text-decoration: none;
    }

    /* Responsive Layout Adjustments */
    @media (max-width: 768px) {
        .col-sm-12 {
            margin-bottom: 15px; /* Reduce margin between cards on smaller screens */
        }

        .card {
            padding: 10px; /* Reduced padding for smaller screens */
        }
    }

    /* Title Styles */
    .title {
        text-align: center;
        color: #333;
        font-size: 2.5rem; /* Smaller title for a more elegant look */
        font-weight: 700;
        margin-bottom: 20px; /* Reduced bottom margin */
        background-image: linear-gradient(to right, #4facfe 0%, #00f2fe 100%);
        padding: 10px; /* Reduced padding */
        border-radius: 8px; /* Slightly smaller border radius */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>
<div class="container">
    <div class="title">IGFRD Documents Per Type</div>
    <div class="row">
        <?php $cnt = 0; $iconsIndex = 0;
        foreach ($document_types as $dt) {
            $documents_no = app\modules\backend\models\DocumentLibrary::getAllDocumentsByType($dt->id);
            $url = yii\helpers\Url::to(['/backend/document-library/index', 'tyid' => $dt->id], true);
            $iconClass = 'fas fa-file-alt'; // Placeholder for dynamic icon class based on $dt ?>
            <div class="col-sm-12 col-md-4 col-lg-3 m-0">
                <a href="<?= $url ?>" class="no-hover-effect">
                    <div class="card border-0 rounded-xs pt-0 h-100">
                        <div class="card-body text-center">
                            <i class="<?= $iconClass ?>" style="font-size: 48px;"></i>
                            <h6 class="mt-2 mb-2"><?= $dt->document_type ?></h6>
                            <p class="card-text">Total Documents: <strong><?= $documents_no ?></strong></p>
                        </div>
                    </div>
                </a>
            </div>
            <?php } ?>
    </div>
</div>
