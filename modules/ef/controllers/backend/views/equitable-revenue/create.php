<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\EquitableRevenueShare $model */

$this->title = 'New Record - Equitable Share';
$this->params['breadcrumbs'][] = ['label' => 'Equitable Revenue Shares', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equitable-revenue-share-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
