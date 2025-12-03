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
$backend = yii\helpers\Url::to(['/backend/default']);
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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* ======================================= */
        /* Custom Styles for Mobile Responsiveness */
        /* ======================================= */

        /* General Mobile-Friendly Navbar */
        @media (max-width: 991px) {
            .navbar {
                padding: 10px 15px;
            }

            .navbar-brand {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .navbar-brand img {
                max-width: 150px;
                height: auto;
                margin-right: 10px;
            }

            .navbar-toggler {
                border: none;
                outline: none;
                box-shadow: none;
                background: rgba(255, 255, 255, 0.1);
                padding: 8px 12px;
                border-radius: 5px;
            }

            .navbar-toggler:focus, .navbar-toggler:hover {
                background: rgba(255, 255, 255, 0.2);
            }

            .navbar-toggler-icon {
                width: 25px;
                height: 25px;
            }

            .navbar-collapse {
                background: #008a8a; /* Solid color for better readability */
                border-radius: 8px;
                padding: 10px;
                margin-top: 8px;
            }

            .navbar-nav {
                text-align: center;
                width: 100%;
            }

            .nav-item {
                margin-bottom: 8px;
            }

            .nav-item.nav-link {
                display: block;
                width: 100%;
                padding: 12px 15px;
                background: #0d6efd; /* Use a solid color for consistency */
                border-radius: 5px;
                color: white !important;
                font-size: 16px;
                font-weight: bold;
                transition: all 0.3s ease-in-out;
            }

            .nav-item.nav-link:hover {
                transform: scale(1.03); /* Smaller scale effect for mobile */
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            }

            .nav-item.nav-link.active {
                box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.3);
            }

            .nav-item.nav-link i {
                margin-right: 6px;
            }

            .navbar-nav {
                display: flex;
                flex-direction: column;
                align-items: center;
            }
        }

        /* Responsive Branding */
        @media (max-width: 576px) {
            .portal-name {
                display: none; /* Hide the long text on extra-small screens */
            }
        }
        
        /* Desktop Navbar Links */
        .nav-item.nav-link {
            background: #7C4102;
            color: #fff !important;
            margin: 0 5px;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease-in-out;
            display: flex;
            align-items: center;
            text-decoration: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        }

        .nav-item.nav-link:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }

        .nav-item.nav-link.active {
            box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.3);
            cursor: default;
        }

        .nav-item.nav-link i {
            margin-right: 6px;
        }

        /* Footer Styles */
        .footer-custom {
            position: relative;
            background: linear-gradient(to right, #008a8a, #6db1a8);
            padding-top: 30px;
            padding-bottom: 15px;
            color: #fff;
            overflow: hidden;
            font-size: 0.95rem;
        }

        .footer-link {
            color: #f5c85c;
            text-decoration: none;
            transition: color 0.3s ease-in-out;
        }

        .footer-link:hover {
            text-decoration: underline;
            color: #fff;
        }

        .text-gold {
            color: #f5c85c;
        }

        .vertical-flag-separator {
            position: absolute;
            top: 0;
            right: -10px;
            width: 6px;
            height: 100%;
            border-radius: 3px;
            background: linear-gradient(
                to bottom,
                #000000 0%, #000000 32%,
                #ffffff 32%, #ffffff 34%,
                #ff0000 34%, #ff0000 66%,
                #ffffff 66%, #ffffff 68%,
                #006600 68%, #006600 100%
            );
        }

        .footer-box {
            text-align: center;
        }

        .white-separator {
            border: none;
            background-color: #fff;
            height: 2px;
            margin: 10px 0;
            opacity: 0.8;
        }

        .footer-text {
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 10px;
            text-shadow: 0 0 5px rgba(0, 0, 0, 0.7);
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
    </style>
</head>

<body>
<?php $this->beginBody() ?>
<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" style="background-color: #dff0d8; color: #3c763d; border-radius: 5px; border: 1px solid #d6e9c6;">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-hidden="true"></button>
        <strong>Success!</strong> <?= Yii::$app->session->getFlash('success') ?>
    </div>
<?php endif; ?>

<div class="container-fluid sticky-top" style="background-color: #008a8a;">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark p-0">
            <a href="<?= $web ?>" class="navbar-brand d-flex align-items-center">
                <img src="<?= $web ?>/igfr_front/img/nt.png"
                     alt="IGFR Portal Logo"
                     class="img-fluid"
                     style="
                         max-width: 180px;
                         height: auto;
                         margin-right: 12px;
                         border-radius: 12px;
                         box-shadow: 0 2px 6px rgba(0,0,0,0.15);
                         transition: transform 0.2s ease-in-out;
                     ">
                <span class="portal-name" style="
                    font-family: 'Poppins', sans-serif;
                    font-weight: 600;
                    font-size: 18px;
                    color: #fff;
                    text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
                    white-space: nowrap;
                ">
                    FiscalBridge Information Management System
                </span>
            </a>

            <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarCollapse">
    <div class="navbar-nav ms-auto">
        <a href="<?= Yii::$app->homeUrl ?>" class="nav-item nav-link active">
            <i class="fas fa-home"></i> Home
        </a>
    </div>
</div>

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
            </div>
        </nav>
    </div>
</div>

<?php if (!empty($this->params['breadcrumbs'])): ?>
    <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
<?php endif ?>
<?= Alert::widget() ?>
<?= $content ?>

<footer class="container-fluid text-white py-4 footer-custom">
    <div class="container">
        <div class="row text-center text-md-start align-items-start position-relative">
            <div class="col-lg-4 col-md-6 mb-3 footer-box position-relative">
                <h5 class="fw-bold text-gold">FiscalBridge Information Management</h5>
                <p class="small">
                    Enhancing fiscal transparency and strengthening intergovernmental financial collaboration.
                </p>
                <div class="vertical-flag-separator d-none d-lg-block"></div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3 footer-box position-relative">
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
                <div class="vertical-flag-separator d-none d-lg-block"></div>
            </div>

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

        <hr class="white-separator mt-4">
        <div class="text-center footer-text">
            &copy; <?= date('Y') ?> FiscalBridge Information Management System, All Rights Reserved.
        </div>
    </div>
</footer>

<button id="backToTopBtn" title="Go to top">
    <i class="fa-solid fa-chevron-up"></i>
</button>

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

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>