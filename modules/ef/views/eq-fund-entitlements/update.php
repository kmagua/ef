<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\EqualisationFundEntitlements $model */

$this->title = 'Update Equalisation Fund Entitlements: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Equalisation Fund Entitlements', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="equalisation-fund-entitlements-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
