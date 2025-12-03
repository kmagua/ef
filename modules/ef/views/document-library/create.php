<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\DocumentLibrary $model */

$this->title = 'Upload New Document';
$this->params['breadcrumbs'][] = ['label' => 'Document Library', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
?>

<style>
    :root {
        --primary-color: #008a8a;
        --primary-dark: #006666;
        --primary-light: #e0f7fa;
        --success-color: #28a745;
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

    .document-library-create {
        padding: 20px;
    }

    .create-wrapper {
        background: #fff;
        border-radius: 12px;
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    /* Header Section */
    .page-header {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: #fff;
        padding: 30px;
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

    /* Form Container */
    .form-container {
        padding: 30px;
    }

    /* Info Box */
    .info-box {
        background: var(--primary-light);
        border-left: 4px solid var(--primary-color);
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 25px;
    }

    .info-box i {
        color: var(--primary-color);
        margin-right: 10px;
    }

    .info-box p {
        margin: 0;
        color: var(--text-dark);
        font-size: 0.95rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .document-library-create {
            padding: 10px;
        }
        .form-container {
            padding: 20px;
        }
        .page-header h1 {
            font-size: 1.5rem;
        }
    }
</style>

<div class="document-library-create">
    <div class="create-wrapper">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-cloud-upload-alt"></i> <?= Html::encode($this->title) ?></h1>
            <p>Upload a new document to the Equalization Fund Document Library</p>
        </div>

        <!-- Form Container -->
        <div class="form-container">
            <!-- Info Box -->
            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                <p><strong>Note:</strong> The document will be automatically categorized under "Equalization Fund" and will be visible to all authorized users.</p>
            </div>

            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
