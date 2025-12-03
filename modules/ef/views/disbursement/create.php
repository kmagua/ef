<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\Disbursement $model */

$this->title = 'New Disbursement';
$this->params['breadcrumbs'][] = ['label' => 'Disbursements', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="disbursement-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
