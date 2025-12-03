<?php

namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\LoginForm;
use yii\filters\VerbFilter;
use app\models\Utility;

/**
 * Default controller for the `backend` module
 */
class DefaultController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                //'only' => ['index', ],
                'rules' => [
                    [
                        'actions' => ['logout', 'index', 'error', 'equitable-chart',
                            'charts', 'equitable-byregion-chart','main-chart', 'total-revenue-vs-actual-revenue', 
                            'target-osr-vs-actual', 'budgetary-analysis', 'pending-bills','top-ten-counties','pending-by-financial-year'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'tesehdeetme' : null,
            ],
        ];
    }
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = 'layout_charts';
        return $this->render('index');
    }
    
    /**
     * Login action.
     *
     * @return Response|string
     */
public function actionLogin()
    {
        // If user is already logged in, redirect based on role.
        if (!Yii::$app->user->isGuest) {
            return $this->redirectUserByRole(Yii::$app->user->identity->role ?? 'default');
        }

        $this->layout = false;
        $model = new LoginForm();

        if (Yii::$app->request->isPost) {
            $submittedAnswer = Yii::$app->request->post('security_stamp');
            $expectedAnswer = Yii::$app->session->get('security_stamp_answer');

            // Ensure a captcha exists in case of session timeout or other issues.
            if ($expectedAnswer === null) {
                $this->generateSecurityStamp();
                $expectedAnswer = Yii::$app->session->get('security_stamp_answer');
            }

            Yii::debug("Submitted Security Stamp: $submittedAnswer", __METHOD__);
            Yii::debug("Expected Security Stamp: $expectedAnswer", __METHOD__);

            // Validate captcha before proceeding
            if (!$this->validateSecurityStamp($submittedAnswer)) {
                Yii::$app->session->setFlash('error', 'Incorrect Security Stamp. Please try again.');
                $this->logLoginAttempt(Yii::$app->request->post('username') ?? 'Unknown', 'Captcha Failure');
                return $this->renderLoginWithCaptcha($model);
            }

            try {
                if ($model->load(Yii::$app->request->post()) && $model->login()) {
                    $username = Yii::$app->user->identity->username;
                    Yii::$app->session->setFlash('success', "Welcome, $username! You've entered the FiscalBridge Portal.");
                    Yii::$app->session->remove('security_stamp_answer'); // Clear captcha on success
                    $this->logLoginAttempt($username, 'Success');

                    return $this->redirectUserByRole(Yii::$app->user->identity->role ?? 'default');
                } else {
                    Yii::$app->session->setFlash('error', 'Invalid username or password. Please try again.');
                    $this->logLoginAttempt($model->username ?? 'Unknown', 'Failure');
                }
            } catch (\Throwable $e) {
                Yii::error("Login error: " . $e->getMessage(), __METHOD__);
                Yii::$app->session->setFlash('error', 'An unexpected error occurred. Please try again.');
                $this->logLoginAttempt($model->username ?? 'Unknown', 'Exception');
            }
        }

        // For GET requests or after errors, generate a new captcha.
        $this->generateSecurityStamp();
        return $this->render('login', [
            'model' => $model,
            'captchaChallenge' => Yii::$app->session->get('security_stamp_question')
        ]);
    }

    /**
     * Redirects user based on role.
     *
     * @param string $role
     * @return \yii\web\Response
     */
    private function redirectUserByRole($role)
    {
        Yii::debug("Redirecting user based on role: " . $role, __METHOD__);

        if (Yii::$app->user->identity->inEqualizationFund()) {
            return $this->redirect(['/ef/ef-project/index']);
        }

        $routes = [
            'admin'         => ['/admin/dashboard'],
            'equalization'  => ['/ef/ef-project/index'],
            'igfr'          => ['/igfrd'],
            'finance'       => ['/finance/dashboard']
        ];

        return isset($routes[$role]) ? $this->redirect($routes[$role]) : $this->goHome();
    }

    /**
     * Logs a login attempt with the username and status.
     *
     * @param string $username
     * @param string $status
     */
    private function logLoginAttempt($username, $status)
    {
        Yii::info("Login attempt for user '{$username}' with status '{$status}'", __METHOD__);
        // You can extend this to log to a file or database table.
    }

    /**
     * Generates and stores a new security stamp (Math Captcha) along with its challenge.
     */
    protected function generateSecurityStamp()
    {
        $num1 = rand(1, 10);
        $num2 = rand(1, 20);
        $answer = (string)($num1 + $num2);
        $question = "What is $num1 + $num2?";

        Yii::$app->session->set('security_stamp_answer', $answer);
        Yii::$app->session->set('security_stamp_question', $question);
        Yii::debug("New Security Stamp Generated: $question = $answer", __METHOD__);
    }

    /**
     * Validates the submitted security stamp answer.
     *
     * @param string|null $submittedAnswer
     * @return bool
     */
    protected function validateSecurityStamp($submittedAnswer)
    {
        $expectedAnswer = Yii::$app->session->get('security_stamp_answer');

        Yii::debug("Submitted Security Stamp: $submittedAnswer", __METHOD__);
        Yii::debug("Expected Security Stamp: $expectedAnswer", __METHOD__);

        return !empty($submittedAnswer) && trim($submittedAnswer) === trim($expectedAnswer);
    }

    /**
     * Renders the login page with a refreshed captcha.
     *
     * @param LoginForm $model
     * @return string
     */
    protected function renderLoginWithCaptcha($model)
    {
        $this->generateSecurityStamp();
        return $this->render('login', [
            'model' => $model,
            'captchaChallenge' => Yii::$app->session->get('security_stamp_question')
        ]);
    }




    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
    
    public function actionCharts()
    {
        $this->layout = 'layout_charts';
        return $this->render('charts');
    }
    
    public function actionEquitableChart()
    {
        $sql = "SELECT fy, SUM(project_amt) as project_amt FROM equitable_revenue_share GROUP by fy ";
        $models= \app\modules\backend\models\EquitableRevenueShare::findBySql($sql)->all();
        $data=array();
        foreach($models as $model){
           $data[]=array('name'=>$model->fy,'y'=>doubleval($model->project_amt));
        }

        return json_encode($data);
    }
    
    public function actionEquitableByregionChart()
    {
        $sql = "SELECT regions.region_name, sum(project_amt) as amount
                FROM equitable_revenue_share join county on county.CountyId=equitable_revenue_share.county_id 
                join regions on regions.RegionId=county.RegionId group by region_name";
        $models= \Yii::$app->db->createCommand($sql)->queryAll();
        $data=array();
        foreach($models as $model){
           $data[]=array('name'=>$model['region_name'],'y'=>doubleval($model['amount']));
        }

        return json_encode($data);
    }
    
    public function actionMainChart($fy=0, $cnt_id = 0)
    {
       
       //$data=$request->all();
       $fy=$fy;
       $CountyId=$cnt_id;

        if($CountyId==0)
        {

           if($fy==0)
           {
             $models= \Yii::$app->db->createCommand("SELECT  sum(development_budgement+recurrent_budget) as TotalBudget,
                     sum(development_budgement) as development_budgement, sum(recurrent_budget) as 
                     recurrent_budget,sum(development_expenditure) as development_expenditure,
                     sum(recurrent_expenditure) as recurrent_expenditure,
                     sum(recurrent_expenditure+ development_expenditure) as TotalExpenditure,
                     sum(pending_bills) as PendingBills,
                     sum(personal_emoluments)as personal_emoluments,
                     sum(actual_osr) as own_source,sum(recurrent_expenditure) as  recurrent_expenditure,
                     sum(development_expenditure) as development_expenditure,
                     sum(recurrent_expenditure+development_expenditure) as total_expenditure,
                     sum(pending_bills) as pending_bills,sum(personal_emoluments) as personal_emoluments  FROM fiscal ")->queryAll();

           }else{
             $models=\Yii::$app->db->createCommand("SELECT  sum(development_budgement+recurrent_budget) as TotalBudget, "
                     . "sum(development_budgement) as development_budgement, "
                     . "sum(recurrent_budget) as recurrent_budget,sum(development_expenditure) as development_expenditure,"
                     . "sum(recurrent_expenditure) as recurrent_expenditure,sum(recurrent_expenditure+ development_expenditure) "
                     . "as TotalExpenditure,sum(pending_bills) as PendingBills,sum(personal_emoluments)as personal_emoluments,"
                     . "sum(actual_osr) as own_source,sum(recurrent_expenditure) as  recurrent_expenditure,"
                     . "sum(development_expenditure) as development_expenditure,"
                     . "sum(recurrent_expenditure+development_expenditure) as total_expenditure,sum(pending_bills) as pending_bills,"
                     . "sum(personal_emoluments) as personal_emoluments  FROM fiscal where fy=:yr ",[':yr' =>$fy])->queryAll();
           }
        }else{
             if($fy==0)
             {
              $models=\Yii::$app->db->createCommand("SELECT  sum(development_budgement+recurrent_budget) as TotalBudget, sum(development_budgement) as development_budgement, sum(recurrent_budget) as recurrent_budget,sum(development_expenditure) as development_expenditure,sum(recurrent_expenditure) as recurrent_expenditure,sum(recurrent_expenditure+ development_expenditure) as TotalExpenditure,sum(pending_bills) as PendingBills,sum(personal_emoluments)as personal_emoluments,sum(actual_osr) as own_source ,sum(recurrent_expenditure) as  recurrent_expenditure,sum(development_expenditure) as development_expenditure,sum(recurrent_expenditure+development_expenditure) as total_expenditure,sum(pending_bills) as pending_bills,sum(personal_emoluments) as personal_emoluments FROM fiscal where countyid=:countyid  ",[':countyid' => $CountyId])->queryAll();

             }else{
              $models=\Yii::$app->db->createCommand("SELECT  sum(development_budgement+recurrent_budget) as TotalBudget, sum(development_budgement) as development_budgement, sum(recurrent_budget) as recurrent_budget,sum(development_expenditure) as development_expenditure,sum(recurrent_expenditure) as recurrent_expenditure,sum(recurrent_expenditure+ development_expenditure) as TotalExpenditure,sum(pending_bills) as PendingBills,sum(personal_emoluments)as personal_emoluments,sum(actual_osr) as own_source,sum(recurrent_expenditure) as  recurrent_expenditure,sum(development_expenditure) as development_expenditure,sum(recurrent_expenditure+development_expenditure) as total_expenditure ,sum(pending_bills) as pending_bills,sum(personal_emoluments) as personal_emoluments FROM fiscal where countyid=:countyid and fy=:fy  ",[':countyid' => $CountyId, ':fy' =>$fy])->queryAll();

             }
          //logic for Specific County
        }

        $data=array(
            "DevelopmentBudget"=> Utility::custom_number_format($models[0]['development_budgement']),
            "RecurrentBudget"=>Utility::custom_number_format($models[0]['recurrent_budget']),
            "TotalBudget"=>Utility::custom_number_format($models[0]['TotalBudget']),
            "TotalOwnSource"=>Utility::custom_number_format($models[0]['own_source']),
            "TotalExpenditure"=>Utility::custom_number_format($models[0]['total_expenditure']),
            "DevelopmentExpenditure"=>Utility::custom_number_format($models[0]['development_expenditure']),
            "RecurrentExpenditure"=>Utility::custom_number_format($models[0]['recurrent_expenditure']),
            "TotalPendingBills"=>Utility::custom_number_format($models[0]['pending_bills']),
            "PersonalEmoluments"=>Utility::custom_number_format($models[0]['personal_emoluments']),
        );
        return json_encode($data);
    }
    
    
    public function actionTotalRevenueVsActualRevenue($fy=0, $cnt_id = 0)
    {

        $models = \Yii::$app->db->createCommand("SELECT fy, sum(total_revenue) as totalrevenue,"
                . "sum(actual_revenue) as actual_revenue FROM fiscal WHERE 1 group by fy;  ")->queryAll();
        $actuals=array();
        $totals=array();
        $categories=array();
       
        foreach($models as $model)
        {
            $categories[]=$model['fy'];
             $actuals[]=floatval($model['totalrevenue']);
             $totals[]=floatval($model['actual_revenue']);
        }
        $small_data[]=array("name"=>"Total Revenue","data"=>$totals);
        $small_data[]=array("name"=>"Actual Revenue","data"=>$actuals);
        $big_array=array("categories"=>$categories,'dataseries'=>$small_data);
        return json_encode($big_array);
    }


    public function actionTargetOsrVsActual($fy=0, $cnt_id = 0)
    {
      
        $models= \Yii::$app->db->createCommand("SELECT fy,sum(target_osr) as target,sum(actual_osr) as actual_osr "
                . "FROM fiscal WHERE 1 group by fy  ") ->queryAll();
        $actuals=array();
        $totals=array();
        $categories=array();
       
        foreach($models as $model)
        {
            $categories[]=$model['fy'];
            $actuals[]=floatval($model['target']);
            $totals[]=floatval($model['actual_osr']);
        }
        $small_data[]=array("name"=>"Target OSR","data"=>$totals);
        $small_data[]=array("name"=>"Actual OSR","data"=>$actuals);
        $big_array=array("categories"=>$categories,'dataseries'=>$small_data);
        return json_encode($big_array);
    }
    
    /*** START HERE*/
    
    public function actionBudgetaryAnalysis($fy=0, $cnt_id = 0, $type=1)
    {
        $data= ['CountyId' => $cnt_id, 'FY' =>$fy];
        if($type==1)
        {
          $models=$this->getDevVsRecurrentBudgetAnalysis($data);
           $data['models']=$models;
           return $this->renderAjax('devvsrecurent', ['data' =>$data]);
        }else if($type==2){
          $models=$this->getDevVsRecurrentExpenditureAnalysis($data);
           $data['models']=$models;
           return $this->renderAjax('devvsrecurent_expenditure',['data' =>$data]);
        }else if($type==3){
          $models=$this->getDevelopmentAnalysis($data);
           $data['models']=$models;
           return $this->renderAjax('development_analysis',['data' =>$data]);

        }else{
           $models=$this->getDevelopmentAnalysis($data);
           $data['models']=$models;
           return $this->renderAjax('expenditure_analysis',['data' =>$data]);
        }
    }


    public function getDevVsRecurrentBudgetAnalysis($data)
    {
        if($data['CountyId']==0)
        {
          //For All Counties
            if($data['FY']==0)
            {
               $models=\Yii::$app->db->createCommand("SELECT fy,sum(development_budgement) as development_budgement,"
                       . "sum(recurrent_budget) as recurrent_budget,sum(development_budgement+recurrent_budget) as total,"
                       . "round((sum(development_budgement)/sum(development_budgement+recurrent_budget)*100),2) as DevRation,"
                       . "round((sum(recurrent_budget)/sum(development_budgement+recurrent_budget)*100),2) as RecRation "
                       . "FROM fiscal WHERE 1 group by fy ")->queryAll();
            }else{
               $models=\Yii::$app->db->createCommand("SELECT fy,sum(development_budgement) as development_budgement,"
                       . "sum(recurrent_budget) as recurrent_budget,sum(development_budgement+recurrent_budget) as total,"
                       . "round((sum(development_budgement)/sum(development_budgement+recurrent_budget)*100),2) as DevRation,"
                       . "round((sum(recurrent_budget)/sum(development_budgement+recurrent_budget)*100),2) as RecRation "
                       . "FROM fiscal WHERE fy=:fy group by fy ",[':fy' => $data['FY']])->queryAll();

            }
        }else{
            if($data['FY']==0)
            {
               $models=\Yii::$app->db->createCommand("SELECT fy,sum(development_budgement) as development_budgement,"
                       . "sum(recurrent_budget) as recurrent_budget,sum(development_budgement+recurrent_budget) as total,"
                       . "round((sum(development_budgement)/sum(development_budgement+recurrent_budget)*100),2) as DevRation,"
                       . "round((sum(recurrent_budget)/sum(development_budgement+recurrent_budget)*100),2) as RecRation "
                       . "FROM fiscal WHERE countyid=:countyid group by fy  order by fy asc",[':countyid' =>$data['CountyId']])->queryAll();
            }else{
              $models=\Yii::$app->db->createCommand("SELECT fy,sum(development_budgement) as development_budgement,"
                      . "sum(recurrent_budget) as recurrent_budget,sum(development_budgement+recurrent_budget) as total,"
                      . "round((sum(development_budgement)/sum(development_budgement+recurrent_budget)*100),2) as DevRation,"
                      . "round((sum(recurrent_budget)/sum(development_budgement+recurrent_budget)*100),2) as RecRation "
                      . "FROM fiscal WHERE countyid=:countyid and fy=:fy group by fy  order by fy asc",
                      [':countyid' => $data['CountyId'], ':fy' =>$data['FY']])->queryAll();
            }
          //logic fpor Asingle County
        }

        return $models;
    }

    public function getDevVsRecurrentExpenditureAnalysis($data)
    {

       if($data['CountyId']==0)
        {
          //For All Counties
            if($data['FY']==0)
            {
               $models=\Yii::$app->db->createCommand("SELECT fy,sum(recurrent_expenditure) as recurrent_expenditure,"
                       . "sum(development_expenditure) as development_expenditure,"
                       . "sum(recurrent_expenditure+development_expenditure) as total,"
                       . "round((sum(recurrent_expenditure)/sum(recurrent_expenditure+development_expenditure)*100),2) as  "
                       . "RecRation,"
                       . "round((sum(development_expenditure)/sum(recurrent_expenditure+development_expenditure)*100),2) as  "
                       . "DevRation "
                       . "FROM fiscal WHERE 1 group by fy order by fy asc ")->queryAll();
            }else{

              $models=\Yii::$app->db->createCommand("SELECT fy,sum(recurrent_expenditure) as recurrent_expenditure,"
                      . "sum(development_expenditure) as development_expenditure,sum(recurrent_expenditure+development_expenditure) "
                      . "as total,round((sum(recurrent_expenditure)/sum(recurrent_expenditure+development_expenditure)*100),2) "
                      . "as  RecRation,round((sum(development_expenditure)/sum(recurrent_expenditure+development_expenditure)*100),2) "
                      . "as  DevRation FROM fiscal WHERE fy=:fy group by fy order by fy asc ",[':fy' => $data['FY']])->queryAll();

            }
        }else{
            if($data['FY']==0)
            {
               $models=\Yii::$app->db->createCommand("SELECT fy,sum(recurrent_expenditure) as recurrent_expenditure,"
                       . "sum(development_expenditure) as development_expenditure,"
                       . "sum(recurrent_expenditure+development_expenditure) as total,"
                       . "round((sum(recurrent_expenditure)/sum(recurrent_expenditure+development_expenditure)*100),2) "
                       . "as  RecRation,round((sum(development_expenditure)/sum(recurrent_expenditure+development_expenditure)*100),2) "
                       . "as  DevRation FROM fiscal WHERE  countyid=:countyid group by fy order by fy asc ",[':countyid' => $data['CountyId']])
                       ->queryAll();
        }else{
              $models=\Yii::$app->db->createCommand("SELECT fy,sum(recurrent_expenditure) as recurrent_expenditure,"
                      . "sum(development_expenditure) as development_expenditure,"
                      . "sum(recurrent_expenditure+development_expenditure) as total,"
                      . "round((sum(recurrent_expenditure)/sum(recurrent_expenditure+development_expenditure)*100),2) as  "
                      . "RecRation,round((sum(development_expenditure)/sum(recurrent_expenditure+development_expenditure)*100),2) "
                      . "as  DevRation FROM fiscal WHERE fy=:fy and countyid=:countyid group by fy "
                      . "order by fy asc ",[':fy' =>$data['FY'],':countyid' =>$data['CountyId']])->queryAll();
            }


          //logic fpor Asingle County
        }

        return $models;


    }



    public function getDevelopmentAnalysis($data)
    {
       if($data['CountyId']==0)
        {
          //For All Counties
            if($data['FY']==0)
            {
               $models=\Yii::$app->db->createCommand("SELECT fy,sum(recurrent_budget) recurrent_budget,"
                       . "sum(recurrent_expenditure) as recurrent_expenditure,"
                       . "sum(recurrent_budget-recurrent_expenditure) as balance "
                       . "FROM `fiscal` WHERE 1 group by fy order by fy asc")->queryAll();
            }else{
              $models=\Yii::$app->db->createCommand("SELECT fy,sum(recurrent_budget) recurrent_budget,"
                      . "sum(recurrent_expenditure) as recurrent_expenditure,"
                      . "sum(recurrent_budget-recurrent_expenditure) as balance "
                      . "FROM `fiscal` WHERE fy=:fy group by fy order by fy asc",[':fy' => $data['FY']])->queryAll();
            }
        }else{
            if($data['FY']==0)
            {
               $models=\Yii::$app->db->createCommand("SELECT fy,sum(recurrent_budget) recurrent_budget,"
                       . "sum(recurrent_expenditure) as recurrent_expenditure,"
                       . "sum(recurrent_budget-recurrent_expenditure) as balance FROM `fiscal` "
                       . "WHERE countyid=:countyid group by fy order by fy asc",[':countyid' =>$data['CountyId']])->queryAll();
            }else{
              $models=\Yii::$app->db->createCommand("SELECT fy,sum(recurrent_budget) recurrent_budget,"
                      . "sum(recurrent_expenditure) as recurrent_expenditure,"
                      . "sum(recurrent_budget-recurrent_expenditure) as balance "
                      . "FROM `fiscal` WHERE fy=:fy and countyid=:countyid group by fy order by fy asc",
                      [':fy' => $data['FY'], ':countyid' => $data['CountyId']])->queryAll();
            }
          //logic fpor Asingle County
        }
        return $models;
    }

    public function  actionTopTenCounties()
    {


       $models=\Yii::$app->db->createCommand("SELECT county.CountyName,sum(pending_bills) as amount FROM `fiscal`
                join county on county.CountyId=fiscal.countyid
               
                WHERE 1 GROUP by CountyName order by amount desc limit 10")->queryAll();


    $i=1;

    foreach($models as $model)
    { 
        
         echo '<tr>
              <td>'.$i.'</td>
              <td>'.$model['CountyName'].'</td>
              <td  style="text-align:right">'.number_format($model['amount'],2).'</td></tr>';
               $i++;
    }




    }


    public function actionPendingByFinancialYear()
    {
      
  $models=\Yii::$app->db->createCommand("SELECT fy,sum(pending_bills) as pending_bills FROM `fiscal` WHERE 1 group by fy ")->queryAll();
  print_r($models);
  exit();
      


    }


    public function actionPendingBills($fy=0, $cnt_id = 0, $type=1)
    {
        //\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON; 
        if($cnt_id ==0){
          if($fy ==0){
            $models=\Yii::$app->db->createCommand("SELECT fiscal.countyid,regions.region_name,  county.CountyName,
                sum(pending_bills) as amount FROM `fiscal`
                join county on county.CountyId=fiscal.countyid
                join regions on regions.RegionId=county.RegionId
                WHERE 1 GROUP by countyid,CountyName,region_name")->queryAll();

          }else{
            $models=\Yii::$app->db->createCommand("SELECT fiscal.countyid,regions.region_name,  county.CountyName,sum(pending_bills) as amount FROM `fiscal`
            join county on county.CountyId=fiscal.countyid
            join regions on regions.RegionId=county.RegionId
            WHERE fy=:fy GROUP by countyid,CountyName,region_name",[':fy' =>$fy])->queryAll();

          }
         }else{
            if($fy==0){
                $models=\Yii::$app->db->createCommand("SELECT fiscal.countyid,regions.region_name,  
                  county.CountyName,sum(pending_bills) as amount FROM `fiscal`
                  join county on county.CountyId=fiscal.countyid
                  join regions on regions.RegionId=county.RegionId
                  WHERE 1 GROUP by countyid,CountyName,region_name")->queryAll();

            }else{
                $models=\Yii::$app->db->createCommand("SELECT fiscal.countyid,regions.region_name,  county.CountyName,
                  sum(pending_bills) as amount FROM `fiscal`
                  join county on county.CountyId=fiscal.countyid
                  join regions on regions.RegionId=county.RegionId
                  WHERE fy=:fy GROUP by countyid,CountyName,region_name",[':fy' =>$fy])->queryAll();
            }
        }
        $regions=array_unique(array_column($models, 'region_name'));
        
        $big_data=array();
        foreach($regions as $region){
            $region_counties=$this->search_revisions($models,$region,'region_name');
            $region_data=array();
            foreach($region_counties as $model){
              $region_data[]=array("name"=>$model->CountyName,'value'=>floatval($model->amount));
            }
            $big_data[]=array("name"=>$region,"data"=>$region_data);
        }
        return json_encode($big_data);
    }
    
    
    public static function search_revisions($dataArray, $search_value, $key_to_search) {
        // This function will search the revisions for a certain value
        // related to the associative key you are looking for.

            if(is_array($dataArray)&&sizeof($dataArray)>0)
            {
                $keys = array();
                foreach ($dataArray as $key => $cur_value) {
                    $cur_value = (object) $cur_value;
                    if ($cur_value->$key_to_search == $search_value) {
                        $keys[] =$cur_value;
                    }
                }
                return $keys;

            }else{
              return array();
            }

       
    }
}
