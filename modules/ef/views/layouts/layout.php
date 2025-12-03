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
  <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    

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
     
<footer class="container-fluid text-white py-4 footer-custom">
    <div class="container">
        <div class="row text-center">
            <!-- FiscalBridge Portal -->
            <div class="col-lg-4 col-md-6 mb-3 footer-box">
                <h5 class="fw-bold text-gold">FiscalBridge Information Management System</h5>
                <p class="small">Enhancing fiscal transparency and strengthening intergovernmental financial collaboration.</p>
            </div>

            <!-- Vertical Separator -->
            <div class="col-lg-1 d-none d-lg-block footer-divider"></div>

            <!-- IGFR Department -->
            <div class="col-lg-3 col-md-6 mb-3 footer-box">
                <h5 class="fw-bold text-gold">IGFR Portal</h5>
                <ul class="list-unstyled small">
                    <li><i class="fa-solid fa-location-dot icon-gold"></i> Harambee Avenue, Treasury Building</li>
                    <li><i class="fa-solid fa-phone icon-gold"></i> <a href="tel:+254202252299" class="footer-link">+254 20 2252299</a></li>
                    <li><i class="fa-solid fa-envelope icon-gold"></i> <a href="mailto:intergovernmental@treasury.go.ke" class="footer-link">intergovernmental@treasury.go.ke</a></li>
                </ul>
            </div>

            <!-- Vertical Separator -->
            <div class="col-lg-1 d-none d-lg-block footer-divider"></div>

            <!-- Equalization Fund -->
            <div class="col-lg-3 col-md-6 footer-box">
                <h5 class="fw-bold text-gold">Equalization Fund Portal</h5>
                <ul class="list-unstyled small">
                    <li><i class="fa-solid fa-building icon-gold"></i> National Bank Building, 8th Floor</li>
                    <li><i class="fa-solid fa-envelope icon-gold"></i> <a href="mailto:info@equalizationfund.go.ke" class="footer-link">info@equalizationfund.go.ke</a></li>
                    <li><i class="fa-solid fa-phone icon-gold"></i> <a href="tel:+254112394023" class="footer-link">+254 112 394023</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Footer Bottom (Full Width) -->
    <div class="container-fluid border-top border-light mt-3 pt-2 pb-2 bg-footer-bottom">
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

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

<!-- Styles -->
<style>
body {
    font-family: 'Poppins', sans-serif !important;
}

/* Footer Container */
.footer-custom {
    position: relative;
    background: linear-gradient(to right, #008A8A);
    padding: 20px 0 30px;
    color: #fff;
    overflow: hidden;
}

.footer-custom::before,
.footer-custom::after {
    content: "";
    position: absolute;
    top: 0;
    width: 50px;
    height: 50px;
    background: gold;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
}

.footer-custom::before {
    right: 0;
    transform: translate(50%, -50%) rotate(45deg);
}

.footer-custom::after {
    left: 0;
    transform: translate(-50%, -50%) rotate(-45deg);
}

.text-gold, .icon-gold {
    color: gold !important;
}

.footer-link {
    color: gold;
    text-decoration: none;
    transition: color 0.3s ease-in-out;
}

.footer-link:hover {
    color: #fff;
    text-decoration: underline;
}

#backToTopBtn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: gold;
    color: #008A8A;
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

.footer-text {
    font-size: 0.8rem;
    margin-top: 5px;
}

/* Responsive Fixes */
@media (max-width: 767px) {
    .footer-custom {
        text-align: center;
    }
    .footer-custom .row div {
        margin-bottom: 20px;
    }
    .footer-custom::before,
    .footer-custom::after {
        width: 40px;
        height: 40px;
    }
}
</style>

<!-- Back to Top Script -->
<script>
    window.onscroll = function() {
        document.getElementById("backToTopBtn").style.display = 
            (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) 
            ? "block" : "none";
    };

    document.getElementById("backToTopBtn").addEventListener("click", () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
</script>

      
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
