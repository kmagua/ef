<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\EqualizationTwoAppropriation $model */

$this->title = 'Create Equalization Two Appropriation';
$this->params['breadcrumbs'][] = ['label' => 'Equalization Two Appropriations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equalization-two-appropriation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
