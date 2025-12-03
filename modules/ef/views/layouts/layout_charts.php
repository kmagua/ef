<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

app\modules\ef\assets\AppAsset::register($this);
$web = Yii::getAlias('@web');

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>


<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
  <head>
  <title>
        <?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/data.js"></script>
        <script src="https://code.highcharts.com/modules/drilldown.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/export-data.js"></script>
        <script src="https://code.highcharts.com/modules/accessibility.js"></script>
        <script src="https://code.highcharts.com/highcharts-more.js"></script>
  </head>
 
  <body style="color:#000">
      <?php $this->beginBody() ?>
    <div class="container-scroller">
      <!-- partial:partials/_sidebar.html -->
      
      <?= $this->render('_sidebar') ?>
      
      <!-- partial -->
      
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_navbar.html -->
        <?= $this->render('_topbar') ?>
        <!-- partial -->
        <div class="main-panel">
            <div class="content-wrapper">
                <?php if (!empty($this->params['breadcrumbs'])): ?>
                <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
                <?php endif ?>
                <?= Alert::widget() ?>
                <?= $content ?>
            </div>

          <!-- content-wrapper ends -->
          <!-- partial:partials/_footer.html -->

 <!-- Begin Footer -->
<footer class="container-fluid text-white py-4 footer-custom">
    <div class="container">
        <div class="row text-center">
            <!-- FiscalBridge Portal -->
            <div class="col-lg-4 col-md-6 mb-3 footer-box">
                <h5 class="fw-bold text-gold">FiscalBridge Information System</h5>
                <p class="small">
                    Enhancing fiscal transparency and strengthening intergovernmental financial collaboration.
                </p>
            </div>

            <!-- Vertical Separator -->
            <div class="col-lg-1 d-none d-lg-block footer-divider"></div>

            <!-- IGFR Department -->
            <div class="col-lg-3 col-md-6 mb-3 footer-box">
                <h5 class="fw-bold text-gold">IGFR Department</h5>
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
                <h5 class="fw-bold text-gold">Equalization Fund</h5>
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

<!-- End Footer -->
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <?php
yii\bootstrap5\Modal::begin(['headerOptions' => ['id' => 'modalHeader'], 
        'id' => 'igfrsystem-modal', 'size' => 'modal-lg', //'class' => 'modal',
    'closeButton' => ['id' => 'close-button', 'class' => 'close', 'data-dismiss' => 'modal',], //keeps from closing modal with esc key or by clicking out of the modal.
    // user must click cancel or X to close
    'options' => ['data-backdrop' => 'static', 'keyboard' => true, 'tabindex' => -1,//'class' => 'modal'
    ]]);
echo "<div id='modalContent'><div style='text-align:center'></div></div>";
echo '<div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close Dialog</button>
      </div>';
yii\bootstrap5\Modal::end();
?>
<?php
//$path = Yii::getAlias('@web');
$js = <<<JS
var basePathWeb = '$web'
$('#boardsys-modal').on('hidden.bs.modal', function () {
    if (typeof refresh_on_close === 'undefined') {
        //location.reload();
    }
})
        
$(document).ready(function() {
    $('a.active').removeClass('active').removeAttr('aria-current');
    //alert(location.pathname)
    console.log($('a[href="' + location.pathname + '"]').closest('a'))
    $('a[href="' + location.pathname + '"]').closest('a').addClass('active'); 
});
        
document.addEventListener('DOMContentLoaded', function(){
        /////// Prevent closing from click inside dropdown
        document.querySelectorAll('.dropdown-menu').forEach(function(element){
        	element.addEventListener('click', function (e) {
        		e.stopPropagation();
        	});
        })
    }); 
JS;

	
	// DOMContentLoaded  end

$this->registerJs($js, yii\web\View::POS_END, 'refresh_on_close_modal');
?>
    <!-- End custom js for this page -->
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>