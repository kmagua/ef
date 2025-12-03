<?php

namespace app\modules\ef\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/site.css',
        'igfr_template/assets/vendors/mdi/css/materialdesignicons.min.css',
        'igfr_template/assets/vendors/css/vendor.bundle.base.css',
        'igfr_template/assets/vendors/jvectormap/jquery-jvectormap.css',
        'igfr_template/assets/vendors/flag-icon-css/css/flag-icon.min.css',
        'igfr_template/assets/vendors/owl-carousel-2/owl.carousel.min.css',
        'igfr_template/assets/vendors/owl-carousel-2/owl.theme.default.min.css',
        'igfr_template/assets/css/style.css',
        // Avoid duplicate includes (mdi was repeated)
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/fontawesome.min.css',
    ];

    public $js = [
        'igfr_template/assets/vendors/js/vendor.bundle.base.js',
        'igfr_template/assets/vendors/chart.js/Chart.min.js',
        'igfr_template/assets/vendors/progressbar.js/progressbar.min.js',
        'igfr_template/assets/vendors/jvectormap/jquery-jvectormap.min.js',
        'igfr_template/assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js',
        'igfr_template/assets/vendors/owl-carousel-2/owl.carousel.min.js',
        'igfr_template/assets/js/off-canvas.js',
        'igfr_template/assets/js/hoverable-collapse.js',
        'igfr_template/assets/js/misc.js',
        'igfr_template/assets/js/settings.js',
        'igfr_template/assets/js/todolist.js',
        'igfr_template/assets/js/dashboard.js',
        'js/general_js.js',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/fontawesome.min.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
        'yii\bootstrap5\BootstrapPluginAsset', // ✅ Needed for dropdowns, modals, collapse, etc
    ];
}
