<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\EqualizationFundProject $model */

$this->title = 'Create Equalization Fund Project';
$this->params['breadcrumbs'][] = ['label' => 'Equalization Fund Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equalization-fund-project-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
