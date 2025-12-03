<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
$web = Yii::getAlias('@web');
$login = yii\helpers\Url::to(['/backend/default/login']);
$backend = yii\helpers\Url::to(['/ef/default/dashboard']);
$base = yii\helpers\Url::base();

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?= $this->params['meta_description'] ?? '' ?>">
    <meta name="keywords" content="<?= $this->params['meta_keywords'] ?? '' ?>">
    <link rel="icon" type="image/x-icon" href="<?= Yii::getAlias('@web/favicon.ico') ?>">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Ubuntu:wght@500;700&display=swap"
        rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissible" style="background-color: #dff0d8; color: #3c763d; border-radius: 5px; border: 1px solid #d6e9c6; position: relative; padding: 15px; margin: 20px 0;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="position: absolute; top: 15px; right: 15px; color: #3c763d; opacity: 0.5;">&times;</button>
        <strong>Success!</strong> <?= Yii::$app->session->getFlash('success') ?>
    </div>
<?php endif; ?>

    
<?php $this->beginBody() ?>

    <div class="container-fluid sticky-top" style="background-color: #9E812B">
        <div class="container" >

            <nav class="navbar navbar-expand-lg navbar-dark p-0">

 <a href="<?= yii\helpers\Url::to(['/ef/default/dashboard']) ?>" class="navbar-brand" style="display: flex; align-items: center;">
   
    <span style="
        font-size: 19px; 
        font-family: 'Poppins', sans-serif !important; 
        color: #FFF; 
        text-shadow: 1px 1px 1px #000; 
        padding: 5px 10px; 
        background: linear-gradient(to right, #6db3f2, #1e69de); 
        border-radius: 4px; 
        display: inline-block; 
        box-shadow: 3px 3px 5px rgba(0,0,0,0.3); 
        transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
    ">
           FiscalBridge Portal
    </span>
</a>

<script>
    const element = document.querySelector('a.navbar-brand span');
    element.addEventListener('mouseover', () => {
        element.style.transform = 'translateY(-5px)';
        element.style.boxShadow = '5px 5px 7px rgba(0,0,0,0.5)';
        element.style.background = 'linear-gradient(to right, #1e69de, #6db3f2)';
    });
    element.addEventListener('mouseout', () => {
        element.style.transform = 'translateY(0)';
        element.style.boxShadow = '3px 3px 5px rgba(0,0,0,0.3)';
        element.style.background = 'linear-gradient(to right, #6db3f2, #1e69de)';
    });
</script>

                <button type="button" class="navbar-toggler ms-auto me-0" data-bs-toggle="collapse"
                    data-bs-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <br>
         
  <div class="navbar-nav">
    <a href="<?= yii\helpers\Url::to(['/ef/default/dashboard']) ?>" class="nav-item nav-link active">
      <i class="fas fa-home"></i> Home
    </a>
    
    <?php if (Yii::$app->user->isGuest): ?>
      <a href="<?= $login ?>" class="nav-item nav-link">
        <i class="fas fa-sign-in-alt"></i> Login
      </a>
    <?php else: ?>
      <a href="<?= $backend ?>" class="nav-item nav-link">
        <i class="fas fa-tachometer-alt"></i> Dashboard
      </a>
    <?php endif; ?>
  </div>
                
                
        <a href="#" class="nav-item nav-link" style="margin-left: auto; display: flex; align-items: center; text-decoration: none; color: #000;">
            <i class="fas fa-info-circle" style="margin-right: 8px; font-size: 18px;"></i>
            About Us
            <i class="fas fa-arrow-right" style="margin-left: 8px; font-size: 18px;"></i>
        </a>
    </div>

</div>

</div>

                </nav>
            </div>
    </div>
            
    <!-- Navbar End -->


	<?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <?= $content ?>
 
  
 <!-- Begin Footer -->
<footer class="container-fluid text-white py-4 footer-custom">
    <div class="container">
        <div class="row text-center">
            <!-- FiscalBridge Portal -->
            <div class="col-lg-4 col-md-6 mb-3 footer-box">
                <h5 class="fw-bold text-gold">FiscalBridge Information Management System</h5>
                <p class="small">
                    Enhancing fiscal transparency and strengthening intergovernmental financial collaboration.
                </p>
            </div>

            <!-- Vertical Separator -->
            <div class="col-lg-1 d-none d-lg-block footer-divider"></div>

            <!-- IGFR Department -->
            <div class="col-lg-3 col-md-6 mb-3 footer-box">
                <h5 class="fw-bold text-gold">IGFR Portal</h5>
                <ul class="list-unstyled small">
                    <li><i class="fa-solid fa-location-dot icon-gold"></i> Harambee Avenue, Treasury Building</li>
                    <li><i class="fa-solid fa-phone icon-gold"></i> 
                        <a href="tel:+254202252299" class="footer-link">+254 20 2252299</a>
                    </li>
                    <li><i class="fa-solid fa-envelope icon-gold"></i> 
                        <a href="mailto:intergovernmental@treasury.go.ke" class="footer-link">intergovernmental@treasury.go.ke</a>
                    </li>
                </ul>
            </div>

            <!-- Vertical Separator -->
            <div class="col-lg-1 d-none d-lg-block footer-divider"></div>

            <!-- Equalization Fund -->
            <div class="col-lg-3 col-md-6 footer-box">
                <h5 class="fw-bold text-gold">Equalization Fund Portal</h5>
                <ul class="list-unstyled small">
                    <li><i class="fa-solid fa-building icon-gold"></i> National Bank Building, 8th Floor</li>
                    <li><i class="fa-solid fa-envelope icon-gold"></i> 
                        <a href="mailto:info@equalizationfund.go.ke" class="footer-link">info@equalizationfund.go.ke</a>
                    </li>
                    <li><i class="fa-solid fa-phone icon-gold"></i> 
                        <a href="tel:+254112394023" class="footer-link">+254 112 394023</a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Footer Bottom -->
        <hr class="white-separator">
        <div class="text-center footer-text">
            &copy; <?= date('Y') ?> FiscalBridge Information Management System, All Rights Reserved.
        </div>
    </div>
</footer>
<!-- End Footer -->

<!-- Back to Top Button -->
<button id="backToTopBtn" title="Go to top">
    <i class="fa-solid fa-chevron-up"></i>
</button>

<!-- Footer Styling -->
<style>
    /* Footer Container */
    .footer-custom {
        position: relative;
        background: linear-gradient(to right, #008a8a, #6db1a8); 
        padding-top: 30px;
        padding-bottom: 15px;
        color: #fff;
        overflow: hidden;
    }

    /* Folded Corners */
    .footer-custom::before,
    .footer-custom::after {
        content: "";
        position: absolute;
        top: 0;
        width: 80px;
        height: 80px;
        background: #F9C74F;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        z-index: 1;
    }
    .footer-custom::before {
        left: 0;
        transform: translate(-50%, -50%) rotate(-45deg);
    }
    .footer-custom::after {
        right: 0;
        transform: translate(50%, -50%) rotate(45deg);
    }

    /* Footer Content */
    .footer-box {
        text-align: center;
    }

    /* Vertical Line Separators */
    .footer-divider {
        border-left: 2px solid rgba(255, 255, 255, 0.5);
        height: 100%;
        align-self: center;
    }

    /* Footer Text */
    .footer-text {
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 10px;
        text-shadow: 0 0 5px rgba(0, 0, 0, 0.7);
    }

    /* Footer Separator */
    .white-separator {
        border: none;
        background-color: #fff;
        height: 2px;
        margin: 10px 0;
        opacity: 0.8;
    }

    /* Gold Text & Icons */
    .text-gold {
        color: #F9C74F;
    }

    /* Footer Links */
    .footer-link {
        color: #F9C74F;
        text-decoration: none;
        transition: color 0.3s ease-in-out;
    }
    .footer-link:hover {
        color: #FFF;
        text-decoration: underline;
    }

    /* Back to Top Button */
    #backToTopBtn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #F9C74F;
        color: #1A4D2E;
        border: none;
        padding: 10px 15px;
        cursor: pointer;
        display: none;
        border-radius: 50%;
        z-index: 999;
        transition: background-color 0.3s ease-in-out;
    }
    #backToTopBtn:hover {
        background-color: #eab93e;
    }

    /* Responsive Design */
    @media (max-width: 767px) {
        .footer-custom {
            text-align: center;
        }
        .footer-box {
            margin-bottom: 15px;
        }
        .footer-divider {
            display: none;
        }
        .footer-custom::before,
        .footer-custom::after {
            width: 60px;
            height: 60px;
            transform: none;
        }
    }
</style>

<!-- Smooth Scroll to Top Script -->
<script>
    window.onscroll = function() { scrollFunction(); };

    function scrollFunction() {
        const backToTopBtn = document.getElementById("backToTopBtn");
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            backToTopBtn.style.display = "block";
        } else {
            backToTopBtn.style.display = "none";
        }
    }

    document.getElementById("backToTopBtn").addEventListener("click", function() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    });
</script>

<!-- Font Awesome Link -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
