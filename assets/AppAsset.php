<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'igfr_front/lib/animate/animate.min.css',
        'igfr_front/lib/owlcarousel/assets/owl.carousel.min.css',
        'igfr_front/css/bootstrap.min.css',
     
    ];
    public $js = [
        'igfr_front/lib/wow/wow.min.js',
        'igfr_front/lib/easing/easing.min.js',
        'igfr_front/lib/waypoints/waypoints.min.js',
        'igfr_front/lib/counterup/counterup.min.js',
        'igfr_front/lib/owlcarousel/owl.carousel.min.js',
        'igfr_front/js/main.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}
