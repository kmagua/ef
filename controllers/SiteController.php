<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\ContactForm;
use app\models\LoginForm;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
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

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

 public function actionLogin()
{
    if (!Yii::$app->user->isGuest) {
        return $this->redirect(['/backend/default']);
    }

    $this->layout = false;
    $model = new LoginForm();
    $session = Yii::$app->session;

    if ($model->load(Yii::$app->request->post())) {
        $submittedAnswer = Yii::$app->request->post('security_stamp');
        $expectedAnswer  = $session->get('security_stamp_answer');

        // ✅ Ensure captcha session exists
        if ($expectedAnswer === null) {
            Yii::$app->session->setFlash('error', 'Security Stamp expired. Please try again.');
            return $this->generateCaptcha($model);
        }

        // ✅ Validate captcha
        if (empty($submittedAnswer) || intval($submittedAnswer) !== intval($expectedAnswer)) {
            Yii::$app->session->setFlash('error_security_stamp', 'Incorrect Security Stamp. Please try again.');
            return $this->generateCaptcha($model);
        }

        try {
            if ($model->validate()) {
                $submittedOtp = Yii::$app->request->post('otp');
                $expectedOtp  = $session->get('login_otp');

                // ✅ Retrieve user email from DB
                $user = \app\models\User::findByUsername($model->username);
                if (!$user) {
                    Yii::$app->session->setFlash('error', 'User not found.');
                    return $this->generateCaptcha($model);
                }

                // ✅ If no OTP yet, generate and send one
                if ($expectedOtp === null) {
                    $otp = random_int(100000, 999999);
                    $session->set('login_otp', $otp);
                    $session->set('pending_login', $model->attributes);

                    // ✅ Send OTP via email
                    $sent = Yii::$app->mailer->compose()
                        ->setTo($user->email)
                        ->setSubject('Your Login OTP Code')
                        ->setHtmlBody("<p>Your OTP code is: <strong>{$otp}</strong></p>")
                        ->send();

                    if ($sent) {
                        Yii::$app->session->setFlash('info', 'An OTP has been sent to your registered email. Please enter it below.');
                    } else {
                        Yii::$app->session->setFlash('error', 'Failed to send OTP email. Please contact support.');
                    }

                    return $this->render('verify-otp', ['model' => $model]);
                }

                // ✅ Verify submitted OTP
                if ($submittedOtp != $expectedOtp) {
                    Yii::$app->session->setFlash('error', 'Invalid OTP. Please try again.');
                    return $this->render('verify-otp', ['model' => $model]);
                }

                // ✅ OTP verified, proceed to login
                $session->remove('login_otp');
                $session->remove('security_stamp_answer');

                if ($model->login()) {
                    $lastLogin = $user->last_login_date ?? null;
                    $user->last_login_date = date('Y-m-d H:i:s');
                    $user->save(false);

                    // ✅ Record login
                    Yii::$app->db->createCommand()->insert('user_login_activity', [
                        'user_id'       => $user->id,
                        'name'          => $user->user_names,
                        'email'         => $user->email,
                        'role'          => 'user',
                        'user_status'   => 'active',
                        'login_at'      => date('Y-m-d H:i:s'),
                        'login_ip'      => Yii::$app->request->userIP,
                        'user_agent'    => Yii::$app->request->userAgent,
                        'login_status'  => 'success',
                        'auth_method'   => 'otp',
                        'application'   => 'web',
                        'login_source'  => 'direct',
                        'session_id'    => $session->id,
                    ])->execute();

                    // ✅ Flash message
                    $username  = $user->user_names ?? $user->username;
                    $loginTime = date('l, F j, Y \a\t g:i A');
                    $message = "<div style='background:#28a745;color:#fff;padding:15px;border-radius:10px;'>
                                    🌟 Welcome, <strong>{$username}</strong>!<br>
                                    You logged in successfully on <em>{$loginTime}</em>.
                                </div>";
                    Yii::$app->session->setFlash('success', $message);
                    return $this->redirect(['/backend/default']);
                } else {
                    Yii::$app->session->setFlash('error', 'Login failed. Please try again.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Invalid username or password.');
            }
        } catch (\Throwable $e) {
            Yii::error("Login error: " . $e->getMessage(), __METHOD__);
            Yii::$app->session->setFlash('error', 'Unexpected error occurred. Please try again.');
        }

        return $this->generateCaptcha($model);
    }

    return $this->generateCaptcha($model);
}

public function actionVerifyOtp()
{
    $this->layout = false;

    if (Yii::$app->request->isPost) {
        $enteredOtp = Yii::$app->request->post('otp');
        $sessionOtp = Yii::$app->session->get('otp_code');
        $expiry     = Yii::$app->session->get('otp_expiry');
        $userId     = Yii::$app->session->get('pending_user_id');

        if (!$sessionOtp || time() > $expiry) {
            Yii::$app->session->setFlash('error', 'OTP expired. Please log in again.');
            return $this->redirect(['/backend/default/login']);
        }

        if ($enteredOtp == $sessionOtp && $userId) {
            $user = \app\models\User::findOne($userId);
            if ($user) {
                // ✅ Log the user in
                Yii::$app->user->login($user);

                // ✅ Clear session OTP
                Yii::$app->session->remove('otp_code');
                Yii::$app->session->remove('otp_expiry');
                Yii::$app->session->remove('pending_user_id');

                // ✅ Update last login timestamp
                $user->last_login_date = date('Y-m-d H:i:s');
                $user->save(false);

                // ✅ Prepare login activity data
                $activity = [
                    'user_id'         => $user->id,
                    'name'            => $user->user_names,
                    'other_names'     => null, // adjust if you have it in your user table
                    'designation'     => null, // adjust if applicable
                    'username'        => $user->username ?? $user->email,
                    'email'           => $user->email,
                    'role'            => 'user', // replace with real role if available
                    'user_status'     => $user->status == '1' ? 'active' : 'inactive',
                    'last_login'      => date('Y-m-d H:i:s'),
                    'login_count'     => new \yii\db\Expression('login_count + 1'),
                    'last_login_ip'   => Yii::$app->request->userIP,
                    'session_id'      => Yii::$app->session->id,
                    'login_ip'        => Yii::$app->request->userIP,
                    'user_agent'      => Yii::$app->request->userAgent,
                    'location'        => null, // you can resolve from IP with GeoIP
                    'country_code'    => null, // same as above
                    'timezone'        => date_default_timezone_get(),
                    'browser'         => null, // parse from user_agent if needed
                    'os'              => null, // parse from user_agent if needed
                    'device'          => null, // parse from user_agent if needed
                    'login_at'        => date('Y-m-d H:i:s'),
                    'logout_at'       => null,
                    'session_duration'=> null,
                    'login_status'    => 'success',
                    'auth_method'     => 'otp',
                    'reason'          => null,
                    'application'     => 'web',
                    'login_source'    => 'direct',
                    'risk_score'      => 0,
                    'attempt_count'   => 1,
                ];

                Yii::$app->db->createCommand()->insert('user_login_activity', $activity)->execute();

                Yii::$app->session->setFlash('success', "🎉 Welcome back, {$user->user_names}!");
                return $this->redirect(['/backend/default']);
            }
        } else {
            // failed OTP attempt log
            $activity = [
                'user_id'         => $userId,
                'name'            => null,
                'other_names'     => null,
                'designation'     => null,
                'username'        => null,
                'email'           => null,
                'role'            => null,
                'user_status'     => 'inactive',
                'last_login'      => null,
                'login_count'     => 0,
                'last_login_ip'   => Yii::$app->request->userIP,
                'session_id'      => Yii::$app->session->id,
                'login_ip'        => Yii::$app->request->userIP,
                'user_agent'      => Yii::$app->request->userAgent,
                'location'        => null,
                'country_code'    => null,
                'timezone'        => date_default_timezone_get(),
                'browser'         => null,
                'os'              => null,
                'device'          => null,
                'login_at'        => date('Y-m-d H:i:s'),
                'logout_at'       => null,
                'session_duration'=> null,
                'login_status'    => 'failed',
                'auth_method'     => 'otp',
                'reason'          => 'Invalid OTP',
                'application'     => 'web',
                'login_source'    => 'direct',
                'risk_score'      => 1,
                'attempt_count'   => 1,
            ];

            Yii::$app->db->createCommand()->insert('user_login_activity', $activity)->execute();

            Yii::$app->session->setFlash('error', 'Invalid OTP. Please try again.');
        }
    }

    return $this->render('verify-otp');
}



    public function actionGenerateSecurityStamp()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        Yii::$app->response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        Yii::$app->response->headers->set('Pragma', 'no-cache');

        $number1 = rand(1, 10);
        $number2 = rand(1, 20);
        $answer = $number1 + $number2;

        Yii::$app->session->set('security_stamp_answer', $answer);

        return [
            'question' => "$number1 + $number2",
            'answer' => $answer,
        ];
    }

    private function redirectUserByRole($role)
    {
        if (isset(Yii::$app->user->identity) && method_exists(Yii::$app->user->identity, 'inEqualizationFund') && Yii::$app->user->identity->inEqualizationFund()) {
            return $this->redirect(['/ef/ef-project/index']);
        }

        switch ($role) {
            case 'admin':
                return $this->redirect(['/admin/dashboard']);
            case 'equalization':
                return $this->redirect(['/ef/ef-project/index']);
            case 'igfr':
                return $this->redirect(['/igfrd']);
            case 'finance':
                return $this->redirect(['/finance/dashboard']);
            default:
                return $this->goHome();
        }
    }

    private function generateCaptcha($model)
    {
        $number1 = rand(1, 10);
        $number2 = rand(1, 20);
        Yii::$app->session->set('security_stamp_answer', $number1 + $number2);

        return $this->render('login', [
            'model'   => $model,
            'number1' => $number1,
            'number2' => $number2,
        ]);
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');
            return $this->refresh();
        }
        return $this->render('contact', ['model' => $model]);
    }

    public function actionEqualization()
    {
        return $this->render('equalization');
    }

    public function actionIgfrd()
    {
        return $this->render('igfrd');
    }

    public function actionAbout()
    {
        return $this->render('about');
    }
}
