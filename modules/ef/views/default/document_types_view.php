<?php
use yii\helpers\Url;
use app\modules\backend\models\DocumentType;
use app\modules\backend\models\DocumentLibrary;
use yii\helpers\Html;


$this->title = 'Equalization Fund Document Library';
$this->params['breadcrumbs'][] = $this->title;

// Only show Equalization Fund documents for EF portal
$mainCategories = [
    'equalization_fund' => 'Equalization Fund Documents'
];

// Fetch all document types
$document_types = DocumentType::find()->all();

// Track a global total
$globalTotal = 0;
?>

<!-- If not already in your main layout, load Bootstrap CSS (example):
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
-->

<style>
/* Base Page Styling - Ensure it fits within the layout */
.document-library-wrapper {
    width: 100%;
    max-width: 100%;
    padding: 0;
    margin: 0;
}

/* Page Header */
.library-header {
    text-align: center;
    margin-bottom: 25px;
    padding: 0 10px;
}
.library-header h1 {
    font-size: 1.6rem;
    font-weight: 700;
    margin-bottom: 10px;
    color: #333;
}
.library-header p {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 0;
}

/* Search & Global Total */
.search-bar {
    margin-bottom: 20px;
    padding: 0 10px;
}
.search-bar .form-label {
    font-weight: 600;
    margin-bottom: 8px;
}
#globalTotal {
    font-weight: 700;
    color: #006666;
    font-size: 1.1rem;
}

/* Category Block */
.category-block {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    margin-bottom: 20px;
    overflow: hidden;
    width: 100%;
}
.category-block-header {
    background: linear-gradient(to right, #008a8a, #00b0b0);
    color: #fff;
    padding: 12px 15px;
    border-radius: 8px 8px 0 0;
    font-weight: 600;
    font-size: 1rem;
    position: relative;
}
.category-block-header small {
    font-size: 0.85rem;
    margin-left: 10px;
    color: #f0f0f0;
}
.toggle-btn {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255,255,255,0.2);
    border: none;
    border-radius: 4px;
    color: #fff;
    cursor: pointer;
    font-size: 0.9rem;
    padding: 4px 8px;
    transition: background 0.3s ease;
}
.toggle-btn:hover {
    background: rgba(255,255,255,0.4);
}

/* Collapsible Body */
.category-block-body {
    padding: 15px;
}

/* Document Card */
.doc-card {
    background-color: #ffffff;
    border-radius: 6px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
    height: 100%;
    border: none;
    animation: fadeIn 0.5s ease-in-out;
}
.doc-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 12px rgba(0,0,0,0.1);
}
.doc-card-body {
    text-align: center;
    padding: 18px 15px;
}
.doc-card-body i {
    font-size: 36px;
    color: #008a8a;
    margin-bottom: 8px;
}
.doc-card-body h6 {
    font-size: 0.95rem;
    font-weight: 700;
    margin-bottom: 5px;
    color: #333;
    line-height: 1.3;
}
.doc-card-body p {
    font-size: 0.85rem;
    color: #555;
    margin-bottom: 0;
}

/* Fade In Keyframes */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(5px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .library-header h1 {
        font-size: 1.4rem;
        padding: 15px;
    }
    .search-bar .col-sm-6 {
        margin-bottom: 15px;
    }
    .doc-card-body {
        padding: 15px 10px;
    }
    .doc-card-body i {
        font-size: 30px;
    }
}

/* Button styling */
.btn-success {
    white-space: nowrap;
    font-size: 0.9rem;
    padding: 8px 16px;
}
</style>

<div class="document-library-wrapper">
    <!-- Page Header -->
    <div class="library-header">
        
        
           <!-- Heading -->
    <h1 style="
        color: #fff !important;
        background: linear-gradient(135deg, #008a8a, #00aaaa) !important;
        padding: 18px 20px;
        border-radius: 8px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        font-size: 1.5rem;
        letter-spacing: 1px;
        margin-bottom: 15px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        text-transform: uppercase;
        text-align: center;
    ">
        <?= Html::encode($this->title) ?>
    </h1>
        <p class="text-muted">Manage and access all Equalization Fund documents. Upload new documents to share with your team.</p>
    </div>

    <!-- Search & Global Total Row -->
    <div class="row search-bar mb-4">
        <div class="col-sm-6 mb-2">
            <label for="docSearchInput" class="form-label">Search Documents</label>
            <input type="text" id="docSearchInput" class="form-control"
                   onkeyup="filterDocuments()"
                   placeholder="Filter by document type...">
        </div>
        <div class="col-sm-6 d-flex align-items-end justify-content-end flex-wrap gap-2">
            <div class="me-3">
                <small class="text-muted me-2">Total Documents:</small>
                <span id="globalTotal">0</span>
            </div>
            <div>
                <?= Html::a('<i class="fas fa-plus-circle"></i> Upload New Document', 
                    ['/ef/document-library/create'], 
                    ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <!-- Categories Loop -->
    <?php foreach ($mainCategories as $catKey => $catLabel): ?>
        <?php
            // Count docs in this entire category
            $categoryTotal = DocumentLibrary::find()
                ->where(['category' => $catKey])
                ->count();
        ?>
        <div class="category-block mb-3 doc-category-container"
             data-doc-category="<?= strtolower($catLabel) ?>">
            <!-- Category Header -->
            <div class="category-block-header">
                <?= $catLabel ?>
                <?php if ($categoryTotal > 0): ?>
                    <small>(<?= $categoryTotal ?> docs)</small>
                <?php else: ?>
                    <small>(No docs)</small>
                <?php endif; ?>

                <!-- Collapse Button -->
                <button class="toggle-btn"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#cat-<?= $catKey ?>-body"
                        aria-expanded="true"
                        aria-controls="cat-<?= $catKey ?>-body">
                    <i class="fas fa-angle-up"></i>
                </button>
            </div>

            <!-- Collapsible Body -->
            <div id="cat-<?= $catKey ?>-body" class="collapse show">
                <div class="category-block-body">
                    <?php if ($categoryTotal == 0): ?>
                        <p class="text-muted fst-italic mb-0">
                            No documents available in this category.
                        </p>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($document_types as $dt): ?>
                                <?php
                                    // Count docs for this type in current category
                                    $docsCount = DocumentLibrary::find()
                                        ->where(['document_type' => $dt->id, 'category' => $catKey])
                                        ->count();

                                    if ($docsCount > 0):
                                        $globalTotal += $docsCount;
                                        $url = Url::to([
                                            '/ef/document-library/index',
                                            'tyid'    => $dt->id,
                                            'category'=> $catKey
                                        ], true);
                                ?>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3 doc-card-container"
                                     data-doc-info="<?= strtolower($dt->document_type . ' ' . $catLabel) ?>">
                                    <a href="<?= $url ?>" style="text-decoration:none; color:inherit;">
                                        <div class="doc-card h-100">
                                            <div class="doc-card-body">
                                                <i class="fas fa-file-alt"></i>
                                                <h6><?= $dt->document_type ?></h6>
                                                <p>Total Docs: <strong><?= $docsCount ?></strong></p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div><!-- /.row -->
                    <?php endif; ?>
                </div><!-- /.category-block-body -->
            </div><!-- /#cat-<?= $catKey ?>-body -->
        </div><!-- /.category-block -->
    <?php endforeach; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show the global total from PHP
    document.getElementById('globalTotal').textContent = <?= $globalTotal ?>;
});

// Client-Side Filter
function filterDocuments() {
    const input = document.getElementById("docSearchInput").value.toLowerCase();
    const docCards = document.querySelectorAll(".doc-card-container");
    const catBlocks = document.querySelectorAll(".doc-category-container");

    docCards.forEach(card => {
        const docInfo = card.getAttribute("data-doc-info");
        if (docInfo.includes(input)) {
            card.style.display = "block";
        } else {
            card.style.display = "none";
        }
    });

    // Hide category block if all doc-cards are hidden
    catBlocks.forEach(block => {
        const childCards = block.querySelectorAll(".doc-card-container");
        let hasVisible = false;
        childCards.forEach(c => {
            if (c.style.display !== "none") {
                hasVisible = true;
            }
        });
        block.style.display = hasVisible ? "block" : "none";
    });
}
</script>

<!-- If not in your main layout, also load Bootstrap JS for collapse toggles:
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
-->
