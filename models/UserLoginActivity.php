<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_login_activity".
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $login_at
 * @property string|null $logout_at
 * @property string|null $login_status
 * @property string|null $auth_method
 * @property string|null $session_id
 * @property string|null $login_ip
 * @property string|null $user_agent
 * @property string|null $device
 * @property string|null $browser
 * @property string|null $os
 * @property string|null $location
 * @property string|null $country_code
 * @property string|null $timezone
 * @property string|null $application
 * @property string|null $login_source
 * @property int|null $risk_score
 * @property int|null $attempt_count
 * @property int|null $session_duration
 * @property string|null $reason
 *
 * @property User $user
 */
class UserLoginActivity extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'user_login_activity';
    }

    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'risk_score', 'attempt_count', 'session_duration'], 'integer'],
            [['login_at', 'logout_at'], 'safe'],
            [['login_status', 'auth_method', 'login_source'], 'string'],
            [['session_id'], 'string', 'max' => 255],
            [['login_ip'], 'string', 'max' => 45],
            [['user_agent'], 'string', 'max' => 512],
            [['device', 'browser', 'os', 'timezone', 'application'], 'string', 'max' => 100],
            [['location', 'reason'], 'string', 'max' => 255],
            [['country_code'], 'string', 'max' => 2],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'login_at' => 'Login Time',
            'logout_at' => 'Logout Time',
            'login_status' => 'Login Status',
            'auth_method' => 'Auth Method',
            'session_id' => 'Session ID',
            'login_ip' => 'Login IP',
            'user_agent' => 'User Agent',
            'device' => 'Device',
            'browser' => 'Browser',
            'os' => 'Operating System',
            'location' => 'Location',
            'country_code' => 'Country Code',
            'timezone' => 'Timezone',
            'application' => 'Application',
            'login_source' => 'Login Source',
            'risk_score' => 'Risk Score',
            'attempt_count' => 'Attempt Count',
            'session_duration' => 'Session Duration',
            'reason' => 'Reason',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
