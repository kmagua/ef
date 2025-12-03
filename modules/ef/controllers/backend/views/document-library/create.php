<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\DocumentLibrary $model */

$this->title = 'Create Document Library';
$this->params['breadcrumbs'][] = ['label' => 'Document Library', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-library-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
