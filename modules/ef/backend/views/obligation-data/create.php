<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\ObligationData $model */

$this->title = 'New Obligation Data Record';
$this->params['breadcrumbs'][] = ['label' => 'Obligation Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="obligation-data-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
