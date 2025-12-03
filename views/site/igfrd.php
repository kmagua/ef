<?php
$this->title = 'IGFR Portal';
$web = Yii::getAlias('@web');
use yii\bootstrap5\Html;
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.5.0/js/bootstrap.min.js"></script>

<style>
/* GLOBAL STYLES */
body {
    font-family: 'Poppins', sans-serif;
    background: #f9fafb;
    color: #333;
    margin: 0;
    padding: 0;
}

/* HERO SECTION */
.hero-banner {
    text-align: center;
    padding: 2rem 1rem;
    border-radius: 20px;
    background: linear-gradient(135deg, #7C4102, #FFD700);
    color: #fff;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    margin: 2rem auto;
    max-width: 1100px;
}
.hero-banner h1 {
    font-size: 2rem;
    font-weight: 800;
    letter-spacing: 0.5px;
    margin: 0;
    text-shadow: 1px 2px 8px rgba(0,0,0,0.3);
}

/* TWO-COLUMN LAYOUT */
.two-column {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
    justify-content: center;
    max-width: 1100px;
    margin: 0 auto 3rem;
}
.two-column .card {
    flex: 1 1 450px;
    min-width: 300px;
}

/* ABOUT CARD */
.about-card {
    display: flex;
    align-items: center;
    gap: 2rem;
    padding: 2rem;
    background: #ffffff;
    border-radius: 25px;
    border: 1px solid #eee;
    box-shadow: 0 12px 28px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}
.about-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 40px rgba(0,0,0,0.12);
}

/* LOGO CONTAINER */
.about-card .image-container {
    flex: 0 0 220px; 
    height: 140px;   
    border-radius: 20px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f9f9f9;
    box-shadow: inset 0 0 12px rgba(0,0,0,0.05);
    padding: 15px; 
}
.about-card .image-container img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

/* ABOUT CONTENT */
.about-card .content h2 {
    font-weight: 800;
    font-size: 1.6rem;
    color: #7C4102;
    margin-bottom: 0.8rem;
}
.about-card .content p {
    font-size: 1rem;
    line-height: 1.6;
    color: #555;
}

/* DOCUMENTS CARD */
.documents-card {
    background: #ffffff;
    border-radius: 25px;
    border: 1px solid #eee;
    box-shadow: 0 12px 28px rgba(0,0,0,0.08);
    padding: 2rem 1.5rem;
}
.documents-card h2 {
    color: #7C4102;
    font-weight: 800;
    margin-bottom: 1.5rem;
    text-align: center;
}
.documents-card .document-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.7rem 1rem;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    border-radius: 10px;
    transition: background 0.3s ease, transform 0.2s ease;
}
.documents-card .document-item:hover {
    background: #fff8e6;
    transform: translateX(4px);
}
.documents-card .document-item i {
    color: #7C4102;
    margin-right: 0.8rem;
    font-size: 1.1rem;
}
.documents-card .document-badge {
    background: linear-gradient(135deg, #7C4102, #FFD700);
    color: #fff;
    font-weight: 600;
    padding: 0.35rem 0.9rem;
    border-radius: 50px;
    font-size: 0.8rem;
    box-shadow: 0 3px 8px rgba(0,0,0,0.15);
}

/* RESPONSIVE */
@media (max-width: 992px) {
    .two-column { flex-direction: column; gap: 1.5rem; }
    .about-card { flex-direction: column; align-items: center; text-align: center; padding: 1.5rem; }
    .about-card .image-container { margin-bottom: 1rem; width: 200px; height: 130px; }
    .about-card .content h2 { font-size: 1.4rem; }
}
@media (max-width: 576px) {
    .hero-banner h1 { font-size: 1.5rem; }
    .about-card .image-container { width: 170px; height: 110px; }
    .about-card .content h2 { font-size: 1.2rem; }
    .about-card .content p { font-size: 0.9rem; }
}
</style>

<!-- HERO -->
<div class="hero-banner">
    <h1>Interlink at a Click</h1>
</div>

<!-- TWO-COLUMN SECTION -->
<div class="two-column">

    <!-- ABOUT CARD -->
    <div class="card about-card">
        <div class="image-container">
            <img src="<?= $web ?>/igfr_front/img/IGFRDMuimi.png" alt="IGFRD Portal Logo">
        </div>
        <div class="content d-flex flex-column justify-content-center">
            <h2>About IGFR Department</h2>
            <p>
                The Intergovernmental Fiscal Relations Department acts as the interface between the national and county governments.
                It coordinates public finance management, oversees fund disbursement and revenue sharing, and builds capacity.
                As the focal point within the National Treasury, it ensures seamless collaboration and harmony in public financial
                management between National and County Governments, in line with the Constitution of Kenya.
            </p>
        </div>
    </div>

    <!-- DOCUMENTS CARD -->
    <div class="card documents-card">
        <h2>Key Documents Prepared by the Department</h2>
        <div class="document-item" data-bs-toggle="modal" data-bs-target="#docModal1">
            <span><i class="fas fa-file-alt"></i> Division of Revenue Act</span>
            <span class="document-badge">Info</span>
        </div>
        <div class="document-item" data-bs-toggle="modal" data-bs-target="#docModal2">
            <span><i class="fas fa-file-alt"></i> County Allocation of Revenue Act</span>
            <span class="document-badge">Info</span>
        </div>
        <div class="document-item" data-bs-toggle="modal" data-bs-target="#docModal3">
            <span><i class="fas fa-file-alt"></i> County Governments Additional Allocation Act</span>
            <span class="document-badge">Info</span>
        </div>
        <div class="document-item" data-bs-toggle="modal" data-bs-target="#docModal4">
            <span><i class="fas fa-file-alt"></i> Public Statements on Payments to County Governments</span>
            <span class="document-badge">Info</span>
        </div>
    </div>

</div>

<!-- MODALS -->
<?php for ($i=1; $i<=4; $i++): ?>
<div class="modal fade" id="docModal<?= $i ?>" tabindex="-1" aria-labelledby="docModalLabel<?= $i ?>" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:15px;">
      <div class="modal-header">
        <h5 class="modal-title" id="docModalLabel<?= $i ?>">Document <?= $i ?> Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Detailed information about Documents <?= $i ?>...
      </div>
    </div>
  </div>
</div>
<?php endfor; ?>
