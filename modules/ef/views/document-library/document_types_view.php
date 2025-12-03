<?php
use yii\helpers\Html;
use yii\helpers\Url;

// Page Title
$this->title = 'Equalization Fund Documents';
$this->params['breadcrumbs'][] = $this->title;

// Only Equalization Fund for EF module
$mainCategories = [
    'equalization_fund' => 'Equalization Fund'
];

// Fetch all document types (make sure this uses EF module models)
$document_types = \app\modules\ef\models\DocumentType::find()->all();
?>

<!-- Google Fonts: Poppins -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

<style>
    .row { margin-top: 20px; }
    .card {
        border-radius: 10px;
        background-color: #ffffff;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease-in-out;
        margin: 10px;
    }
    .card:hover { transform: translateY(-5px); }

    .card i { color: #5c67f2; margin-bottom: 5px; }
    .card h6 { font-size: 1.2rem; font-weight: 700; margin-top: 5px; }
    .card p { color: #555; font-size: 1rem; }
    a.no-hover-effect { color: inherit; text-decoration: none; }
    a.no-hover-effect:hover { text-decoration: none; }
    .title {
        text-align: center;
        color: #333;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        background-image: linear-gradient(to right, #00c6ff, #0072ff);
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .category-title {
        margin-top: 30px;
        color: #7C4102;
        font-size: 1.8rem;
        font-weight: bold;
    }
</style>

<div class="container">
    <div class="title"> Equalization Fund Document Library </div>

    <?php foreach ($mainCategories as $category_key => $category_label): ?>
        <h2 class="category-title"><?= Html::encode($category_label) ?></h2>
        <div class="row">
            <?php foreach ($document_types as $dt): ?>
                <?php
                // Count documents in EF category
                $documents_no = \app\modules\ef\models\DocumentLibrary::find()
                    ->where(['document_type' => $dt->id, 'category' => $category_key])
                    ->count();

                if ($documents_no > 0):
                    // AUTO detect EF module URL dynamically
                    $url = Url::to([
                        '/' . Yii::$app->controller->module->id . '/document-library/index',
                        'tyid' => $dt->id,
                        'category' => $category_key
                    ]);
                    $iconClass = 'fas fa-file-alt'; // Customize per type if needed
                ?>
                <div class="col-sm-12 col-md-4 col-lg-3 m-0">
                    <a href="<?= $url ?>" class="no-hover-effect">
                        <div class="card border-0 rounded-xs pt-0 h-100">
                            <div class="card-body text-center">
                                <i class="<?= $iconClass ?>" style="font-size: 48px;"></i>
                                <h6 class="mt-2 mb-2"><?= Html::encode($dt->document_type) ?></h6>
                                <p class="card-text">Total Documents: <strong><?= $documents_no ?></strong></p>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>
