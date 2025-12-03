<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->first_name . ' ' .$model->last_name;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= /* Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])*/ 
        "" ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'email:email',
            'first_name',
            'last_name',
            'status',
            //'role',
            [
                'label' => 'Role(s)',
                'value' => function($data){
                    $roles = $data->getRoleNames();
                    return implode(', ', $roles);
                }
            ],
            [
                'label' => 'MCDA',
                'value' => function($data){
                    return $data->getCounty();
                }
            ],
            'date_created',
            'last_updated',
            /*[
                'label' => 'Companies associated with user',
                'value' => function() use ($model){
                    $companies = '';
                    foreach ($model->companyProfiles as $cp){
                        $companies .= $cp->company_name .='<br>';
                    }
                    return $companies;
                },
                'format' => 'html'
            ],                        
            [
                'label' => 'Applications associated with user',
                'value' => function() use ($model){
                    $application = '';
                    foreach ($model->applications as $app){
                        $application .= Html::a($app->company->company_name .' -- ' . $app->type_of_license, 
                            ['ápplication/view', 'id'=>$app->id]) .'<br>';
                    }
                    return $application;
                },
                'format' => 'html'
            ]*/
        ],
    ])
    ?> 

</div>
