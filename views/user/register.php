<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AspNetUsers */
/* @var $form yii\widgets\ActiveForm */
$this->title = "User Registration";
$web = Yii::getAlias("@web");
?>
<div class="" style="margin: auto; margin-top:2%; width:50% !important; ">
    <!-- /.login-logo -->
    <div class="login-box-body">
        <!-- <img src="../../Images/pomac logo(1).jpg" style="margin-left:auto; margin-right:auto;width:20% ;display:block"/>-->        
        <div style="text-align: center;">
            <h1>Scrap Metal Dealer Registration System</h1>
            <img src="<?= $web ?>/images/logo1.jpg"/>
            
            <p class="login-box-msg" style="color:darkgoldenrod; font-size:20px; font-weight:500;">Register Account</p>            
        </div>
        <div class="asp-net-users-form" style="min-width:450px !important;">

            <?php $form = ActiveForm::begin(['layout' => 'horizontal',
                    'fieldConfig' => [
                        'template' => "{label}\n<div class=\"col-lg-6\">{input}</div>\n<div class=\"col-lg-4\">{error}</div>",
                        'labelOptions' => ['class' => 'col-lg-4 control-label'],
                    ],
                ]); ?>

           <?= ''//$form->field($model, 'kra_pin_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'captcha')->widget(\yii\captcha\Captcha::classname(), [
        // configure additional widget properties here
    ]) ?>


            <div class="form-group">
                <?= Html::submitButton('Register Account', ['class' => 'btn btn-success']) ?> <?= Html::a("Login", ['site/login'], ['class' => 'btn btn-danger', 'style' =>'float:right; margin-right:100px']) ?>
            </div>
             
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

