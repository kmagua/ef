<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\EqualisationFundEntitlements $model */

$this->title = 'Create Equalisation Fund Entitlements';
$this->params['breadcrumbs'][] = ['label' => 'Equalisation Fund Entitlements', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equalisation-fund-entitlements-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
