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
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => [
            ['label' => 'Home', 'url' => ['/site/index']],
            ['label' => 'About', 'url' => ['/site/about']],
            ['label' => 'Contact', 'url' => ['/site/contact']],
            Yii::$app->user->isGuest
                ? ['label' => 'Login', 'url' => ['/site/login']]
                : '<li class="nav-item">'
                    . Html::beginForm(['/backend/default/logout'])
                    . Html::submitButton(
                        'Logout (' . Yii::$app->user->identity->username . ')',
                        ['class' => 'nav-link btn btn-link logout']
                    )
                    . Html::endForm()
                    . '</li>'
        ]
    ]);
    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

 <!-- Begin Footer -->
<footer class="container-fluid text-white py-4 footer-custom">
    <div class="container">
        <div class="row text-center">
            <!-- FiscalBridge Portal -->
            <div class="col-lg-4 col-md-6 mb-3 footer-box">
                <h5 class="fw-bold text-gold"> FiscalBridge Information Management System</h5>
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


<!-- (Optional) Back to Top Button -->
<button id="backToTopBtn" title="Go to top">
    <i class="fas fa-chevron-up"></i>
</button>

<!-- Footer & Extra Features Styling -->
<style>
 

    /* Gold Text & Icons */
    .text-gold {
        color: #F9C74F;
        position: relative; /* Keep it above folded corners */
        z-index: 2;
    }
    .icon-gold {
        color: #F9C74F;
        position: relative;
        z-index: 2;
    }

    /* Buttons and Links in Gold */
    .btn-gold {
        background-color: #F9C74F;
        color: #1A4D2E;
        border: none;
        transition: background-color 0.3s ease-in-out;
    }
    .btn-gold:hover {
        background-color: #eab93e; /* Slightly darker gold on hover */
        color: #fff;
    }

    /* Footer Links */
    .footer-link {
        color: #F9C74F;
        text-decoration: none;
        transition: color 0.3s ease-in-out;
        position: relative;
        z-index: 2;
    }
    .footer-link:hover {
        color: #FFF;
        text-decoration: underline;
    }

    /* Gold Border for Separation */
    .border-gold {
        border-top: 1px solid rgba(249, 199, 79, 0.6);
        position: relative;
        z-index: 2;
    }

    /* Thicker separator for a more pronounced style */
    .thick-separator {
        border-top-width: 2px;
    }

    /* Optional Feature Row with folds at bottom corners */
    .folded-feature {
        position: relative;
        background: #2F7E52; /* Slightly darker or lighter shade of green for contrast */
        z-index: 2;
        margin: 30px 0;
        border-radius: 5px;
        overflow: hidden;
    }
    .folded-feature::before,
    .folded-feature::after {
        content: "";
        position: absolute;
        bottom: 0;
        width: 60px;
        height: 60px;
        background: #F9C74F;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        z-index: 1;
    }
    .folded-feature::before {
        left: 0;
        transform: translate(-30%, 30%) rotate(-45deg);
    }
    .folded-feature::after {
        right: 0;
        transform: translate(30%, 30%) rotate(45deg);
    }

    /*
     * Disclaimers Row with folds at top corners
     * to visually differentiate from the main footer background
     */
    .disclaimers-folded {
        position: relative;
        background: #2F7E52; /* Another green or your preference */
        border-radius: 5px;
        margin: 30px 0;
        overflow: hidden;
        z-index: 2;
    }
    .disclaimers-folded::before,
    .disclaimers-folded::after {
        content: "";
        position: absolute;
        top: 0;
        width: 60px;
        height: 60px;
        background: #F9C74F;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        z-index: 1;
    }
    .disclaimers-folded::before {
        left: 0;
        transform: translate(-30%, -30%) rotate(45deg);
    }
    .disclaimers-folded::after {
        right: 0;
        transform: translate(30%, -30%) rotate(-45deg);
    }

    /* Disclaimers list style */
    .disclaimers-list .separator {
        margin: 0 8px;
        font-weight: bold;
    }

    /* (Optional) Back to Top Button */
    #backToTopBtn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #F9C74F;
        color: #1A4D2E;
        border: none;
        padding: 10px 15px;
        cursor: pointer;
        display: none; /* Hidden by default, shown on scroll */
        border-radius: 50%;
        z-index: 999;
    }
    #backToTopBtn:hover {
        background-color: #eab93e; /* Slightly darker gold */
    }

    /* Responsive Fixes */
    @media (max-width: 767px) {
        .footer-custom {
            text-align: center;
        }
        .footer-custom .row div {
            margin-bottom: 20px;
        }
        .folded-feature, .disclaimers-folded {
            margin: 20px 0;
        }
        .folded-feature::before,
        .folded-feature::after,
        .disclaimers-folded::before,
        .disclaimers-folded::after {
            width: 40px;
            height: 40px;
            transform: none; /* Remove complicated transforms for smaller screens if desired */
        }
    }
</style>

<!-- Smooth Scroll to Top Script -->
<script>
    // Show the button after scrolling 20px
    window.onscroll = function() {
        scrollFunction();
    };

    function scrollFunction() {
        const backToTopBtn = document.getElementById("backToTopBtn");
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            backToTopBtn.style.display = "block";
        } else {
            backToTopBtn.style.display = "none";
        }
    }

    // When the user clicks on the button, scroll to the top
    function topFunction() {
        document.body.scrollTop = 0;  
        document.documentElement.scrollTop = 0; 
    }

    document.getElementById("backToTopBtn").addEventListener("click", topFunction);
</script>

<!-- End Footer -->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
