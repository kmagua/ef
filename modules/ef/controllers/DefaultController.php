<?php

namespace app\modules\ef\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\LoginForm;
use yii\filters\VerbFilter;
use app\models\Utility;
use hisorange\BrowserDetect\Parser as Browser;
use GeoIp2\Database\Reader; 
/**
 * Default controller for the `ef` module
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
                        'actions' => ['logout','login'
                            , 'dashboard', 'index','verify-otp', 'error', 'equitable-chart',
                            'charts', 'equitable-byregion-chart','main-chart', 'total-revenue-vs-actual-revenue', 
                            'target-osr-vs-actual', 'budgetary-analysis', 'pending-bills','top-ten-counties','pending-by-financial-year'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                      [
                    'actions' => ['login-activity'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
                    [
                        'actions' => ['login', 'verify-otp','error'],
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
    if (!Yii::$app->user->isGuest) {
        return $this->redirect(['/ef/default']);
    }

    $this->layout = false;
    $model = new LoginForm();
    
    // Generate security stamp on initial load
    if (!Yii::$app->request->isPost) {
        $this->generateSecurityStamp();
    }

    if (Yii::$app->request->isPost) {
        try {
            // Load POST data
            if ($model->load(Yii::$app->request->post())) {
                // Validate the model
                if (!$model->validate()) {
                    // Show validation errors
                    $errors = [];
                    foreach ($model->getErrors() as $attribute => $errorMessages) {
                        $errors[] = implode(', ', $errorMessages);
                    }
                    Yii::$app->session->setFlash('error', '❌ ' . implode(' ', $errors));
                    Yii::error("Login validation failed: " . implode(' | ', $errors), __METHOD__);
                    $this->generateSecurityStamp(); // Regenerate security stamp
                    return $this->render('login', ['model' => $model]);
                }
                
                // Validation passed, proceed with login
                $user = $model->getUser();

                if ($user) {
                    // Debug: Log user details
                    Yii::info("Login attempt - User ID: {$user->id}, Email: " . ($user->email ?? 'NULL') . ", Status: " . ($user->status ?? 'NULL'), __METHOD__);
                    
                    // 🚫 Check if user has email
                    if (empty($user->email)) {
                        Yii::$app->session->setFlash('error', '⚠️ Your account does not have an email address. Please contact support.');
                        Yii::error("User {$user->id} has no email address", __METHOD__);
                        return $this->refresh();
                    }
                    
                    // 🚫 Block inactive accounts
                    // Status is ENUM('1', '0', '2') - check as string '1' or integer 1
                    $userStatus = (string)$user->status;
                    if ($userStatus !== '1') {
                        Yii::$app->session->setFlash('error', '⚠️ Your account is inactive. Contact support. Status: ' . ($user->status ?? 'NULL'));
                        Yii::error("Login blocked - User ID: {$user->id}, Status: {$userStatus}", __METHOD__);
                        return $this->refresh();
                    }

                    // 🚫 Rate-limit login attempts
                    $cacheKey       = "login_attempts_{$user->id}";
                    $failedAttempts = (int)(Yii::$app->cache->get($cacheKey) ?? 0);

                    if ($failedAttempts >= 5) {
                        Yii::$app->session->setFlash('error', '🚫 Too many failed attempts. Try again later.');
                        return $this->refresh();
                    }

                    // ✅ Generate OTP (6 digits)
                    $otp       = sprintf("%06d", random_int(100000, 999999));
                    $expiresAt = date('Y-m-d H:i:s', time() + 300);

                    // ✅ Save in session (works both local + prod)
                    Yii::$app->session->set('pending_user_id', $user->id);
                    Yii::$app->session->set('otp_code', $otp);
                    Yii::$app->session->set('otp_expiry', time() + 300);

                    // ✅ Save OTP in DB (safe for strict mode)
                    try {
                        Yii::info("Attempting to insert OTP: user_id={$user->id}, otp_code={$otp}, expires_at={$expiresAt}", __METHOD__);
                        
                        $result = Yii::$app->db->createCommand()->insert('user_otp', [
                            'user_id'    => (int)$user->id,
                            'otp_code'   => $otp,
                            'expires_at' => $expiresAt,
                            'is_used'    => 0,
                            'created_at' => new \yii\db\Expression('NOW()')
                        ])->execute();
                        
                        Yii::info("✅ OTP saved to database successfully: {$otp} for user ID: {$user->id}, Rows affected: {$result}", __METHOD__);
                    } catch (\Throwable $dbError) {
                        $errorMsg = "❌ Failed to save OTP to database: " . $dbError->getMessage();
                        if (method_exists($dbError, 'getTraceAsString')) {
                            $errorMsg .= " | Trace: " . substr($dbError->getTraceAsString(), 0, 500);
                        }
                        Yii::error($errorMsg, __METHOD__);
                        
                        // Log to file for debugging
                        $logFile = Yii::getAlias('@runtime/logs/otp_error.log');
                        file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $errorMsg . "\n", FILE_APPEND);
                        
                        // Continue anyway - OTP is in session, so login can still work
                    }

                    // ✅ Send OTP email
                    Yii::info("Attempting to send OTP to: {$user->email}", __METHOD__);
                    $emailSent = $this->sendOtpEmail($user, $otp);
                    Yii::info("OTP email send result: " . ($emailSent ? 'SUCCESS' : 'FAILED'), __METHOD__);
                    
                    // For development: if email fails, show OTP on screen
                    if (!$emailSent) {
                        if (YII_DEBUG || YII_ENV_DEV) {
                            // In development, show OTP in flash message for testing
                            Yii::$app->session->setFlash(
                                'info',
                                "⚠️ Email not configured. Your OTP is: <strong style='font-size:24px;color:#006a71;'>{$otp}</strong> (This will expire in 5 minutes)"
                            );
                            // Still redirect to OTP page so user can enter it
                        } else {
                            // In production, show error and don't proceed
                            Yii::$app->session->setFlash(
                                'error',
                                '⚠️ Could not send OTP email. Please contact support or try again.'
                            );
                            return $this->refresh();
                        }
                    } else {
                        // Email sent successfully
                        Yii::$app->session->setFlash('info', '✅ OTP has been sent to your email. Please check your inbox.');
                    }

                    // 📝 Log login attempt
                    try {
                        Yii::$app->db->createCommand()->insert('user_login_activity', [
                            'user_id'      => (int)$user->id,
                            'username'     => $user->username ?? $user->email,
                            'email'        => $user->email,
                            'login_status' => 'pending_otp',
                            'auth_method'  => 'password+otp',
                            'login_ip'     => Yii::$app->request->userIP,
                            'user_agent'   => Yii::$app->request->userAgent,
                            'session_id'   => Yii::$app->session->id,
                            'login_at'     => new \yii\db\Expression('NOW()'),
                        ])->execute();
                    } catch (\Throwable $logError) {
                        Yii::error("Failed to log login attempt: " . $logError->getMessage(), __METHOD__);
                    }

                    // ✅ Redirect to OTP verification
                    Yii::info("Redirecting to OTP verification for user ID: {$user->id}, Email: {$user->email}", __METHOD__);
                    return $this->redirect(['/ef/default/verify-otp']);
                }
            }

            // ❌ Invalid credentials → track attempts
            if ($model->username) {
                $user = \app\models\User::findByUsername($model->username);
                if ($user) {
                    $cacheKey       = "login_attempts_{$user->id}";
                    $failedAttempts = (int)(Yii::$app->cache->get($cacheKey) ?? 0);
                    Yii::$app->cache->set($cacheKey, $failedAttempts + 1, 600); // expire after 10 minutes
                }
            }

                    // This should not be reached if validation passed above
                    $errors = [];
                    foreach ($model->getErrors() as $attribute => $errorMessages) {
                        $errors[] = implode(', ', $errorMessages);
                    }
                    if (!empty($errors)) {
                        Yii::$app->session->setFlash('error', '❌ ' . implode(' ', $errors));
                    } else {
                        Yii::$app->session->setFlash('error', '❌ Invalid username or password. Please try again.');
                    }
        } catch (\Throwable $e) {
            Yii::error("Login error: " . $e->getMessage(), __METHOD__);
            Yii::$app->session->setFlash('error', '⚠️ System temporarily unavailable. Please try again.');
        }
        
        // Regenerate security stamp after failed attempt
        $this->generateSecurityStamp();
    }

    // Generate security stamp on initial load
    if (!Yii::$app->request->isPost) {
        $this->generateSecurityStamp();
    }

    return $this->render('login', ['model' => $model]);
}

private function sendOtpEmail($user, $otp)
{
    try {
        $loginTime = date('l, F j, Y \a\t g:i A');
        $ipAddress = Yii::$app->request->getUserIP();
        $year      = date('Y');
        $username  = \yii\helpers\Html::encode($user->user_names ?? $user->username);

        // ✅ Dynamic subject
        $subject = "🔐 OTP for {$username} – {$loginTime}";
        
        // Get from address from config or use default
        $fromEmail = Yii::$app->params['supportEmail'] ?? Yii::$app->params['adminEmail'] ?? 'no-reply@ict.go.ke';
        $fromName = 'FiscalBridge Portal';

        $sent = Yii::$app->mailer->compose()
            ->setFrom([$fromEmail => $fromName])
            ->setTo($user->email)
            ->setSubject($subject)
            ->setHtmlBody("
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <style>
                    body {
                        font-family: 'Poppins', Arial, sans-serif;
                        background-color: #f4f6f8;
                        margin: 0;
                        padding: 0;
                        color: #2c3e50;
                    }
                    .container {
                        max-width: 600px;
                        margin: 30px auto;
                        background: #ffffff;
                        border-radius: 12px;
                        box-shadow: 0 6px 18px rgba(0,0,0,0.1);
                        overflow: hidden;
                    }
                    .header {
                        background: linear-gradient(135deg, #008080, #20b2aa);
                        color: #fff;
                        text-align: center;
                        padding: 25px 20px;
                        font-size: 20px;
                        font-weight: 600;
                        letter-spacing: 0.5px;
                    }
                    .content {
                        padding: 30px;
                    }
                    h2 {
                        font-size: 22px;
                        margin-bottom: 15px;
                        color: #008080;
                        text-align: center;
                    }
                    p {
                        font-size: 15px;
                        line-height: 1.6;
                        margin: 10px 0;
                    }
                    .otp-box {
                        display: block;
                        margin: 25px auto;
                        padding: 18px 40px;
                        font-size: 30px;
                        font-weight: bold;
                        color: #fff;
                        background: linear-gradient(135deg, #008080, #006666);
                        border-radius: 12px;
                        letter-spacing: 6px;
                        text-align: center;
                        box-shadow: 0 4px 15px rgba(0,0,0,0.25);
                        width: fit-content;
                    }
                    .meta {
                        margin-top: 20px;
                        font-size: 14px;
                        color: #444;
                        background: #f9f9f9;
                        padding: 12px;
                        border-radius: 8px;
                        border: 1px solid #eee;
                        line-height: 1.5;
                    }
                    .warning {
                        margin-top: 25px;
                        padding: 14px;
                        background: #fff8e1;
                        border-left: 5px solid #f39c12;
                        color: #7a5c00;
                        font-size: 14px;
                        border-radius: 6px;
                    }
                    .footer {
                        font-size: 13px;
                        color: #7f8c8d;
                        margin-top: 30px;
                        text-align: center;
                        border-top: 1px solid #ecf0f1;
                        padding: 15px;
                        background: #fafafa;
                    }
                    @media (max-width: 600px) {
                        .content { padding: 20px; }
                        .otp-box { font-size: 26px; padding: 15px 30px; }
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>FiscalBridge Portal – Secure Login</div>
                    <div class='content'>
                        <h2>🔐 Login Verification</h2>
                        <p>Hello <strong>{$username}</strong>,</p>
                        <p>Your one-time password (OTP) for accessing your account is:</p>

                        <div class='otp-box'>{$otp}</div>

                        <p>This code will <strong>expire in 5 minutes</strong>. Please use it immediately to complete your login.</p>

                        <div class='meta'>
                            🕒 Login attempt: {$loginTime}<br>
                            🌐 IP Address: {$ipAddress}
                        </div>

                        <div class='warning'>
                            ⚠️ If you did not attempt to log in, please ignore this email or contact support immediately.
                        </div>

                        <div class='footer'>
                            &copy; {$year} FiscalBridge Portal. All rights reserved.<br>
                            This is an automated message – please do not reply.
                        </div>
                    </div>
                </div>
            </body>
            </html>
        ")
        ->send();
        
        if ($sent) {
            Yii::info("OTP email sent successfully to {$user->email}", __METHOD__);
            return true;
        } else {
            Yii::error("OTP email failed to send to {$user->email} - mailer returned false", __METHOD__);
            return false;
        }
    } catch (\Throwable $e) {
        Yii::error("Failed to send OTP email to {$user->email}: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString(), __METHOD__);
        return false;
    }
}

  
public function actionVerifyOtp()
{
    $this->layout = false;

    if (Yii::$app->request->isPost) {
        $enteredOtp = trim(Yii::$app->request->post('otp'));
        $sessionOtp = Yii::$app->session->get('otp_code');
        $expiry     = Yii::$app->session->get('otp_expiry');
        $userId     = Yii::$app->session->get('pending_user_id');

        // ❌ No OTP in session or expired
        if (!$sessionOtp || time() > $expiry) {
            $this->clearOtpSession();

            if ($userId) {
                try {
                    Yii::$app->db->createCommand()
                        ->update('user_otp', ['is_used' => 2], [
                            'user_id'  => $userId,
                            'otp_code' => $sessionOtp
                        ])
                        ->execute();
                } catch (\Throwable $e) {
                    Yii::error("Failed to mark OTP expired: " . $e->getMessage(), __METHOD__);
                }
            }

            Yii::$app->session->removeAllFlashes();
            $message = "
                <div style='
                    background: linear-gradient(135deg,#ff4d4d,#b30000);
                    color: #fff; font-family:Poppins,sans-serif;
                    padding:20px;border-radius:10px;
                    box-shadow:0 4px 15px rgba(0,0,0,0.2);
                    font-size:14px;line-height:1.6;'>
                    ⏳ <strong>OTP Expired</strong><br>
                    Your one-time password has expired. Please log in again to request a new code.
                </div>";
            Yii::$app->session->setFlash('error', $message);

            return $this->redirect(['/ef/default/login']);
        }

        // ✅ OTP success
        if ($enteredOtp === $sessionOtp && $userId) {
            $user = \app\models\User::findOne($userId);

            if ($user && (string)$user->status === '1') {
                Yii::$app->user->login($user);
                $this->clearOtpSession();

                try {
                    Yii::$app->db->createCommand()
                        ->update('user_otp', ['is_used' => 1], [
                            'user_id'  => $user->id,
                            'otp_code' => $sessionOtp
                        ])
                        ->execute();
                } catch (\Throwable $e) {
                    Yii::error("Failed to mark OTP used: " . $e->getMessage(), __METHOD__);
                }

                $user->last_login_date = date('Y-m-d H:i:s');
                $user->save(false);

                $this->logLoginActivity($user, 'success', 'otp');

                $username  = $user->user_names ?? $user->username;
                $loginTime = date('l, F j, Y \a\t g:i A');
                $ip        = Yii::$app->request->getUserIP();

                $message = "
                    <div style='
                        background: linear-gradient(135deg,#28a745,#1e7e34);
                        color: #fff;font-family:Poppins,sans-serif;
                        padding:20px;border-radius:10px;
                        box-shadow:0 4px 15px rgba(0,0,0,0.2);
                        font-size:14px;line-height:1.6;'>
                        🎉 <strong>Welcome back, {$username}!</strong><br>
                        ✅ Your OTP has been successfully verified.<br>
                        🕒 <em>Verified at:</em> {$loginTime}<br>
                        🌐 <em>IP Address:</em> {$ip}
                    </div>";
                Yii::$app->session->removeAllFlashes();
                Yii::$app->session->setFlash('success', $message);

                return $this->redirect(['/ef/default']);
            }

            Yii::$app->session->removeAllFlashes();
            $message = "
                <div style='
                    background: linear-gradient(135deg,#ffcc00,#cc9900);
                    color:#000;font-family:Poppins,sans-serif;
                    padding:20px;border-radius:10px;
                    box-shadow:0 4px 15px rgba(0,0,0,0.2);
                    font-size:14px;line-height:1.6;'>
                    ⚠️ <strong>Account Inactive</strong><br>
                    Your account is currently inactive. Please contact support for assistance.
                </div>";
            Yii::$app->session->setFlash('warning', $message);

            return $this->redirect(['/ef/default/login']);
        }

        // ❌ OTP failed
        $this->logFailedOtp($userId);

        Yii::$app->session->removeAllFlashes();
        $message = "
            <div style='
                background: linear-gradient(135deg,#ff4d4d,#b30000);
                color:#fff;font-family:Poppins,sans-serif;
                padding:20px;border-radius:10px;
                box-shadow:0 4px 15px rgba(0,0,0,0.2);
                font-size:14px;line-height:1.6;'>
                ❌ <strong>Invalid OTP</strong><br>
                The code you entered is incorrect. Please try again.
            </div>";
        Yii::$app->session->setFlash('error', $message);
    }

    return $this->render('verify-otp');
}

private function clearOtpSession()
{
    Yii::$app->session->remove('otp_code');
    Yii::$app->session->remove('otp_expiry');
    Yii::$app->session->remove('pending_user_id');
}
public function actionLoginActivity()
{
    $searchModel = new \app\models\UserLoginActivitySearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    // 🔥 Top 5 most active users
    $topUsers = \app\models\UserLoginActivity::find()
        ->select(['user_id', 'COUNT(*) AS login_count'])
        ->where(['login_status' => 'success'])
        ->groupBy('user_id')
        ->orderBy(['login_count' => SORT_DESC])
        ->limit(5)
        ->with('user')
        ->asArray()
        ->all();

    return $this->render('login-activity', [
        'searchModel'  => $searchModel,
        'dataProvider' => $dataProvider,
        'topUsers'     => $topUsers,
    ]);
}


/**
 * Log login activity with browser/device/GeoIP detection + risk score
 */
private function logLoginActivity($user, $status = 'success', $method = 'otp', $reason = null)
{
    $ip       = Yii::$app->request->userIP;
    $ua       = Yii::$app->request->userAgent;
    $timezone = date_default_timezone_get();

    // Defaults
    $browser = $os = $device = 'unknown';
    $isBot = $isMobile = $isTablet = $isDesktop = false;
    $country = $city = $countryCode = null;

    // 🔎 Browser / Device detection (safe wrapper)
    try {
        if (class_exists(\hisorange\BrowserDetect\Parser::class)) {
            $browser   = \hisorange\BrowserDetect\Parser::browserName() . ' ' . \hisorange\BrowserDetect\Parser::browserVersion();
            $os        = \hisorange\BrowserDetect\Parser::platformName() . ' ' . \hisorange\BrowserDetect\Parser::platformVersion();
            $device    = \hisorange\BrowserDetect\Parser::deviceFamily();
            $isBot     = \hisorange\BrowserDetect\Parser::isBot();
            $isMobile  = \hisorange\BrowserDetect\Parser::isMobile();
            $isTablet  = \hisorange\BrowserDetect\Parser::isTablet();
            $isDesktop = \hisorange\BrowserDetect\Parser::isDesktop();
        }
    } catch (\Throwable $e) {
        Yii::warning("BrowserDetect failed: " . $e->getMessage(), __METHOD__);
    }

    // 🌍 GeoIP lookup (safe)
    try {
        $geoDb = Yii::getAlias('@app/runtime/GeoLite2-City.mmdb');
        if (file_exists($geoDb)) {
            $reader = new \GeoIp2\Database\Reader($geoDb);
            $record = $reader->city($ip);

            $country     = $record->country->name;
            $city        = $record->city->name ?? null;
            $countryCode = $record->country->isoCode;
            $timezone    = $record->location->timeZone ?? $timezone;
        }
    } catch (\Throwable $e) {
        Yii::warning("GeoIP lookup failed for IP {$ip}: " . $e->getMessage(), __METHOD__);
    }

    // 🛡️ Risk Score
    $risk = 0;
    if ($status === 'failed') {
        $risk += 2;
    }
    if ($method === 'password' && $isMobile) {
        $risk += 1;
    }
    if ($countryCode && !in_array($countryCode, ['KE', 'US'])) { // whitelist
        $risk += 2;
    }
    if ($isBot) {
        $risk += 5;
    }

    // ✅ Insert into DB
    try {
        Yii::$app->db->createCommand()->insert('user_login_activity', [
            'user_id'        => $user->id ?? null,
            'name'           => $user->user_names ?? null,
            'username'       => $user->username ?? $user->email,
            'email'          => $user->email ?? null,
            'role'           => $user->role ?? 'user',
            'user_status'    => $status === 'success' ? 'active' : 'inactive',
            'last_login'     => date('Y-m-d H:i:s'),
            'login_status'   => $status,
            'auth_method'    => $method,
            'session_id'     => Yii::$app->session->id,
            'login_ip'       => $ip,
            'last_login_ip'  => $ip,
            'user_agent'     => $ua,
            'device'         => $device,
            'browser'        => $browser,
            'os'             => $os,
            'location'       => $city ? "{$city}, {$country}" : $country,
            'country_code'   => $countryCode,
            'timezone'       => $timezone,
            'application'    => 'web',
            'login_source'   => 'direct',
            'risk_score'     => $risk,
            'attempt_count'  => 1,
            'reason'         => $reason,
            'login_at'       => date('Y-m-d H:i:s'),
        ])->execute();
    } catch (\Throwable $e) {
        Yii::error("Failed to insert login activity: " . $e->getMessage(), __METHOD__);
    }
}


/**
 * Helper: Log failed OTP
 */
private function logFailedOtp($userId)
{
    $activity = [
        'user_id'       => $userId,
        'user_status'   => 'unknown',
        'login_at'      => date('Y-m-d H:i:s'),
        'last_login_ip' => Yii::$app->request->userIP,
        'session_id'    => Yii::$app->session->id,
        'user_agent'    => Yii::$app->request->userAgent,
        'timezone'      => date_default_timezone_get(),
        'login_status'  => 'failed',
        'auth_method'   => 'otp',
        'reason'        => 'Invalid OTP',
        'application'   => 'web',
        'login_source'  => 'direct',
        'risk_score'    => 1,
        'attempt_count' => 1,
    ];

    try {
        Yii::$app->db->createCommand()->insert('user_login_activity', $activity)->execute();
    } catch (\Throwable $e) {
        Yii::error("Failed to insert failed login activity: " . $e->getMessage(), __METHOD__);
    }
}


public function actionResendOtp()
{
    $userId = Yii::$app->session->get('pending_user_id');

    if (!$userId) {
        Yii::$app->session->setFlash('error', 'Your session expired. Please log in again.');
        return $this->redirect(['/ef/default/login']);
    }

    $user = \app\models\User::findOne($userId);
    if (!$user) {
        Yii::$app->session->setFlash('error', 'User not found. Please log in again.');
        return $this->redirect(['/ef/default/login']);
    }

    // ✅ Generate secure 6-digit OTP
    $otp = sprintf("%06d", random_int(100000, 999999));
    $expiresAt = date('Y-m-d H:i:s', time() + 300);

    // Save OTP in session
    Yii::$app->session->set('otp_code', $otp);
    Yii::$app->session->set('otp_expiry', time() + 300);

    // ✅ Invalidate old OTPs and insert new one in `user_otp` table
    Yii::$app->db->createCommand()
        ->update('user_otp', ['is_used' => 2], ['user_id' => $user->id, 'is_used' => 0])
        ->execute();

    Yii::$app->db->createCommand()
        ->insert('user_otp', [
            'user_id'    => $user->id,
            'otp_code'   => $otp,
            'expires_at' => $expiresAt,
            'is_used'    => 0,
            'created_at' => new \yii\db\Expression('NOW()')
        ])
        ->execute();

    // ✅ Send OTP email
    try {
        $sent = Yii::$app->mailer->compose()
            ->setTo($user->email)
            ->setFrom([Yii::$app->params['supportEmail'] => 'FiscalBridge Portal'])
            ->setSubject('Your New OTP Code')
            ->setHtmlBody("
                <div style='
                    background: linear-gradient(135deg,#006a71,#00a0a9);
                    color:#fff; padding:20px; font-family:Poppins,sans-serif;
                    border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15);'>
                    
                    <h3>🔐 New OTP Requested</h3>
                    <p>Hello <strong>{$user->user_names}</strong>,</p>
                    <p>Your new one-time password (OTP) is:</p>
                    <h2 style='text-align:center; letter-spacing:4px;'>{$otp}</h2>
                    <p>This code will expire in <strong>5 minutes</strong>.</p>
                    
                    <p style='font-size:13px;opacity:0.8;'>If this wasn’t you, please secure your account immediately.</p>
                </div>
            ")
            ->send();

        if ($sent) {
            Yii::$app->session->setFlash('success', '✅ A new OTP has been sent to your email.');
        } else {
            Yii::$app->session->setFlash('error', '❌ Failed to send OTP. Please try again.');
        }
    } catch (\Throwable $mailError) {
        Yii::error("Resend OTP mailer error: " . $mailError->getMessage(), __METHOD__);
        Yii::$app->session->setFlash('error', '⚠️ Unexpected error while sending OTP.');
    }

    return $this->redirect(['/ef/default/verify-otp']);
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
public function actionGenerateSecurityStamp()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    return $this->generateSecurityStamp(true); // Return the question + answer
}
protected function generateSecurityStamp(bool $return = false): ?array
{
    $num1 = rand(1, 10);
    $num2 = rand(1, 20);
    $answer = $num1 + $num2;
    $question = "{$num1} + {$num2}";

    Yii::$app->session->set('security_stamp_answer', $answer);
    Yii::$app->session->set('security_stamp_question', $question);

    Yii::debug("New Security Stamp Generated: {$question} = {$answer}", __METHOD__);

    return $return ? [
        'question' => $question,
        'answer'   => $answer,
    ] : null;
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
        Yii::$app->db->createCommand()->update('user_login_activity', [
            'logout_at' => new \yii\db\Expression('NOW()'),
            'session_duration' => new \yii\db\Expression('TIMESTAMPDIFF(SECOND, login_at, NOW())'),
        ], ['session_id' => Yii::$app->session->id])->execute();

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
        $models= \app\modules\ef\models\EquitableRevenueShare::findBySql($sql)->all();
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
public function actionEqualization()
{
    $this->layout = 'layout_charts'; // or any layout you prefer
    return $this->render('equalization');
}


public function actionIgfrd()
{
    return $this->render('igfrd'); // will look for views/site/igfrd.php
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
    
    /**
 * Dashboard action - displays summary statistics and analytics for both 1st and 2nd Marginalization Policies
 * @return string
 */
public function actionDashboard()
{
    $this->layout = 'layout';
    
    // 1st Marginalization Policy Data
    // Total Counties
    $firstPolicyCounties = \Yii::$app->db->createCommand("
        SELECT COUNT(DISTINCT county) as total_counties
        FROM equalization_fund_project
        WHERE county IS NOT NULL AND county != ''
    ")->queryOne();
    $firstPolicyTotalCounties = (int)($firstPolicyCounties['total_counties'] ?? 0);
    
    // Total Allocation
    $firstPolicyAllocation = \Yii::$app->db->createCommand("
        SELECT COALESCE(SUM(ef_allocation), 0) as total_allocation
        FROM equalization_fund_allocation
        WHERE ef_allocation IS NOT NULL
    ")->queryOne();
    $firstPolicyTotalAllocation = (float)($firstPolicyAllocation['total_allocation'] ?? 0);
    
    // Total Disbursement
    $firstPolicyDisbursement = \Yii::$app->db->createCommand("
        SELECT COALESCE(SUM(amount_disbursed), 0) as total_disbursement
        FROM equalization_fund_disbursement
        WHERE amount_disbursed IS NOT NULL
    ")->queryOne();
    $firstPolicyTotalDisbursement = (float)($firstPolicyDisbursement['total_disbursement'] ?? 0);
    
    // 1st Policy Analytics
    // Yearly Allocation Trend
    $firstPolicyYearlyAllocation = \Yii::$app->db->createCommand("
        SELECT financial_year, COALESCE(SUM(ef_allocation), 0) as allocation
        FROM equalization_fund_allocation
        GROUP BY financial_year
        ORDER BY financial_year
    ")->queryAll();
    
    // Sector Distribution
    $firstPolicySectorDistribution = \Yii::$app->db->createCommand("
        SELECT sector, COALESCE(SUM(budget_2018_19), 0) as total_budget
        FROM equalization_fund_project
        WHERE sector IS NOT NULL
        GROUP BY sector
        ORDER BY total_budget DESC
    ")->queryAll();
    
    // Top 5 Counties by Allocation
    $firstPolicyTopCounties = \Yii::$app->db->createCommand("
        SELECT county, COALESCE(SUM(budget_2018_19), 0) as total_allocation
        FROM equalization_fund_project
        WHERE county IS NOT NULL
        GROUP BY county
        ORDER BY total_allocation DESC
        LIMIT 5
    ")->queryAll();
    
    // Absorption Rate by County
    $firstPolicyAbsorptionByCounty = \Yii::$app->db->createCommand("
        SELECT d.county, 
               COALESCE(SUM(d.amount_disbursed), 0) as disbursed,
               COALESCE(SUM(p.budget_2018_19), 0) as allocated,
               CASE 
                 WHEN COALESCE(SUM(p.budget_2018_19), 0) > 0 
                 THEN (COALESCE(SUM(d.amount_disbursed), 0) / COALESCE(SUM(p.budget_2018_19), 0)) * 100 
                 ELSE 0 
               END as absorption_rate
        FROM equalization_fund_disbursement d
        LEFT JOIN equalization_fund_project p ON d.county = p.county
        GROUP BY d.county
        ORDER BY absorption_rate DESC
    ")->queryAll();
    
    // Project Completion Status
    $firstPolicyCompletionStatus = \Yii::$app->db->createCommand("
        SELECT 
               SUM(CASE WHEN percent_completion = 100 THEN 1 ELSE 0 END) as completed,
               SUM(CASE WHEN percent_completion > 0 AND percent_completion < 100 THEN 1 ELSE 0 END) as in_progress,
               SUM(CASE WHEN percent_completion = 0 OR percent_completion IS NULL THEN 1 ELSE 0 END) as not_started
        FROM equalization_fund_project
    ")->queryOne();
    
    // 2nd Marginalization Policy Data
    // Total Counties
    $secondPolicyCounties = \Yii::$app->db->createCommand("
        SELECT COUNT(DISTINCT county) as total_counties
        FROM eq2_appropriation
        WHERE county IS NOT NULL AND county != ''
    ")->queryOne();
    $secondPolicyTotalCounties = (int)($secondPolicyCounties['total_counties'] ?? 0);
    
    // Total Allocation
    $secondPolicyAllocation = \Yii::$app->db->createCommand("
        SELECT COALESCE(SUM(allocation_ksh), 0) as total_allocation
        FROM eq2_appropriation
        WHERE allocation_ksh IS NOT NULL
    ")->queryOne();
    $secondPolicyTotalAllocation = (float)($secondPolicyAllocation['total_allocation'] ?? 0);
    
    // Total Disbursement
    $secondPolicyDisbursement = \Yii::$app->db->createCommand("
        SELECT COALESCE(SUM(total_disbursement), 0) as total_disbursement
        FROM eq2_disbursement
        WHERE total_disbursement IS NOT NULL
    ")->queryOne();
    $secondPolicyTotalDisbursement = (float)($secondPolicyDisbursement['total_disbursement'] ?? 0);
    
    // 2nd Policy Analytics
    // Yearly Allocation Trend
    $secondPolicyYearlyAllocation = \Yii::$app->db->createCommand("
        SELECT financial_year, COALESCE(SUM(allocation_ksh), 0) as allocation
        FROM eq2_appropriation
        GROUP BY financial_year
        ORDER BY financial_year
    ")->queryAll();
    
    // Sector Distribution
    $secondPolicySectorDistribution = \Yii::$app->db->createCommand("
        SELECT sector, COALESCE(SUM(project_budget), 0) as total_budget
        FROM eq2_projects
        WHERE sector IS NOT NULL
        GROUP BY sector
        ORDER BY total_budget DESC
    ")->queryAll();
    
    // Top 5 Counties by Allocation
    $secondPolicyTopCounties = \Yii::$app->db->createCommand("
        SELECT county, COALESCE(SUM(allocation_ksh), 0) as total_allocation
        FROM eq2_appropriation
        WHERE county IS NOT NULL
        GROUP BY county
        ORDER BY total_allocation DESC
        LIMIT 5
    ")->queryAll();
    
    // Absorption Rate by County
    $secondPolicyAbsorptionByCounty = \Yii::$app->db->createCommand("
        SELECT d.county, 
               COALESCE(SUM(d.total_disbursement), 0) as disbursed,
               COALESCE(SUM(a.allocation_ksh), 0) as allocated,
               CASE 
                 WHEN COALESCE(SUM(a.allocation_ksh), 0) > 0 
                 THEN (COALESCE(SUM(d.total_disbursement), 0) / COALESCE(SUM(a.allocation_ksh), 0)) * 100 
                 ELSE 0 
               END as absorption_rate
        FROM eq2_disbursement d
        LEFT JOIN eq2_appropriation a ON d.county = a.county
        GROUP BY d.county
        ORDER BY absorption_rate DESC
    ")->queryAll();
    
    // Project Distribution by Financial Year
    $secondPolicyProjectsByYear = \Yii::$app->db->createCommand("
        SELECT financial_year, COUNT(*) as project_count
        FROM eq2_projects
        GROUP BY financial_year
        ORDER BY financial_year
    ")->queryAll();
    
    // Comparison Metrics
    $comparisonData = [
        'allocation_difference' => $secondPolicyTotalAllocation - $firstPolicyTotalAllocation,
        'disbursement_difference' => $secondPolicyTotalDisbursement - $firstPolicyTotalDisbursement,
        'first_policy_absorption_rate' => $firstPolicyTotalAllocation > 0 ? 
            ($firstPolicyTotalDisbursement / $firstPolicyTotalAllocation) * 100 : 0,
        'second_policy_absorption_rate' => $secondPolicyTotalAllocation > 0 ? 
            ($secondPolicyTotalDisbursement / $secondPolicyTotalAllocation) * 100 : 0,
    ];
    
    // Map View Data - Get projects with coordinates
    $mapProjects = \app\modules\ef\models\EqualizationTwoProjects::find()
        ->where(['not', ['latitude' => null]])
        ->andWhere(['not', ['longitude' => null]])
        ->all();
    
    // Prepare project data for JavaScript
    $mapProjectsData = [];
    foreach ($mapProjects as $project) {
        $mapProjectsData[] = [
            'id' => $project->id,
            'name' => $project->project_description ?: 'Unnamed Project',
            'county' => $project->county ?: 'N/A',
            'constituency' => $project->constituency ?: 'N/A',
            'ward' => $project->ward ?: 'N/A',
            'marginalised_area' => $project->marginalised_area ?: 'N/A',
            'sector' => $project->sector ?: 'N/A',
            'budget' => $project->project_budget ? number_format($project->project_budget, 2) : '0.00',
            'latitude' => (float)$project->latitude,
            'longitude' => (float)$project->longitude,
        ];
    }
    
    return $this->render('dashboard', [
        // 1st Policy Data
        'firstPolicyTotalCounties' => $firstPolicyTotalCounties,
        'firstPolicyTotalAllocation' => $firstPolicyTotalAllocation,
        'firstPolicyTotalDisbursement' => $firstPolicyTotalDisbursement,
        'firstPolicyYearlyAllocation' => $firstPolicyYearlyAllocation,
        'firstPolicySectorDistribution' => $firstPolicySectorDistribution,
        'firstPolicyTopCounties' => $firstPolicyTopCounties,
        'firstPolicyAbsorptionByCounty' => $firstPolicyAbsorptionByCounty,
        'firstPolicyCompletionStatus' => $firstPolicyCompletionStatus,
        
        // 2nd Policy Data
        'secondPolicyTotalCounties' => $secondPolicyTotalCounties,
        'secondPolicyTotalAllocation' => $secondPolicyTotalAllocation,
        'secondPolicyTotalDisbursement' => $secondPolicyTotalDisbursement,
        'secondPolicyYearlyAllocation' => $secondPolicyYearlyAllocation,
        'secondPolicySectorDistribution' => $secondPolicySectorDistribution,
        'secondPolicyTopCounties' => $secondPolicyTopCounties,
        'secondPolicyAbsorptionByCounty' => $secondPolicyAbsorptionByCounty,
        'secondPolicyProjectsByYear' => $secondPolicyProjectsByYear,
        
        // Comparison Data
        'comparisonData' => $comparisonData,
        
        // Map View Data
        'mapProjects' => $mapProjects,
        'mapProjectsData' => $mapProjectsData,
    ]);
}
}