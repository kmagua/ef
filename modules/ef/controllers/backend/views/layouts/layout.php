<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

app\modules\backend\assets\AppAsset::register($this);
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
         !-- Begin Footer -->
<footer class="container-fluid text-white py-5 footer-custom">
    <div class="container">
        <!-- Existing Footer Row -->
        <div class="row">
          <!-- FiscalBridge Portal -->
<div class="col-lg-4 col-md-6 mb-4" 
     style="background: linear-gradient(135deg, #1e1e2f 0%, #2a2a3d 100%); 
            padding: 1.5rem; 
            border-radius: 10px; 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15); 
            text-align: center;">
    
    <h5 class="fw-bold" 
        style="color: #daa520; 
               font-size: 1.25rem; 
               margin-bottom: 0.5rem;">
        FiscalBridge Portal
    </h5>
    
    <p class="small text-light" 
       style="font-size: 0.875rem; 
              line-height: 1.4;">
        Enhancing fiscal transparency and strengthening intergovernmental financial collaboration.
    </p>
</div>


            <!-- IGFR Department -->
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="fw-bold text-gold">IGFR Department</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-map-marker-alt me-2 icon-gold"></i> Harambee Avenue, Treasury Building</li>
                    <li>
                        <i class="fas fa-phone-alt me-2 icon-gold"></i>
                        <a href="tel:+254202252299" class="footer-link">+254 20 2252299</a>
                    </li>
                    <li>
                        <i class="fas fa-envelope me-2 icon-gold"></i>
                        <a href="mailto:intergovernmental@treasury.go.ke" class="footer-link">
                            intergovernmental@treasury.go.ke
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Equalization Fund -->
            <div class="col-lg-4 col-md-6">
                <h5 class="fw-bold text-gold">Equalization Fund</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-building me-2 icon-gold"></i> National Bank Building, 8th Floor</li>
                    <li>
                        <i class="fas fa-envelope me-2 icon-gold"></i>
                        <a href="mailto:info@equalizationfund.go.ke" class="footer-link">
                            info@equalizationfund.go.ke
                        </a>
                    </li>
                    <li>
                        <i class="fas fa-phone-alt me-2 icon-gold"></i>
                        <a href="tel:+254112394023" class="footer-link">+254 112 394023</a>
                    </li>
                </ul>
            </div>
        </div>

  

     <!-- Another Decorative Separator -->
<hr class="white-separator">


<style>
.white-separator {
  border: none !important;
  background-color: #fff !important;
  color: #fff !important;
  height: 3px !important;
  margin: 1rem 0 !important;
  opacity: 1 !important;
}

 
</style>
 
        </div>


   <div class="text-center footer-text">
    &copy; <?= date('Y') ?> IGFR Department & Equalization Fund, All Rights Reserved.
</div>

<style>
.footer-text {
  color: #fff; 
  text-shadow: 0 0 5px rgba(0, 0, 0, 0.7); /* Glow-like shadow */
  font-size: 0.9rem;                       /* Adjust as needed */
  font-weight: 600;                        /* Slightly bolder */
  margin-top: 1rem;                        /* Spacing from the line above */
}
</style>

    </div>
</footer>
<!-- End Footer -->

<!-- (Optional) Back to Top Button -->
<button id="backToTopBtn" title="Go to top">
    <i class="fas fa-chevron-up"></i>
</button>

<!-- Footer & Extra Features Styling -->
<style>
    
    .white-thick-separator {
  border: 0;                   /* Remove any default styling */
  border-top: 4px solid #ffff;  /* White color, 4px thickness */
  margin-top: 1rem;            /* Adjust spacing as desired */
}

 
    .footer-custom {
        position: relative;
        background: linear-gradient(to right, #008a8a); 
        padding-top: 50px;
        padding-bottom: 30px;
        color: #fff;
        overflow: hidden; /* Hide corners that go out of bounds */
    }

    /* Folded-corner effect on both top corners */
    .footer-custom::before {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: #F9C74F; /* Gold corner (right) */
        transform: translate(50%, -50%) rotate(45deg);
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        z-index: 1;
    }
    .footer-custom::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100px;
        height: 100px;
        background: #F9C74F; /* Gold corner (left) */
        transform: translate(-50%, -50%) rotate(-45deg);
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        z-index: 1;
    }

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