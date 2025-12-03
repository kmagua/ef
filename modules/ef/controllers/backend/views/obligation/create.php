<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\Obligation $model */

$this->title = 'New Obligation Type';
$this->params['breadcrumbs'][] = ['label' => 'Obligations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="obligation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
