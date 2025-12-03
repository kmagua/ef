<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "assigned_user_role".
 *
 * @property int $id
 * @property int $user_id
 * @property int $role_id
 *
 * @property User $user
 * @property AuthRole $role
 */
class AssignedUserRole extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'assigned_user_role'; // 👈 make sure this matches your DB table
    }

    public function rules()
    {
        return [
            [['user_id', 'role_id'], 'required'],
            [['user_id', 'role_id'], 'integer'],
            [['user_id', 'role_id'], 'unique', 'targetAttribute' => ['user_id', 'role_id']],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getRole()
    {
        return $this->hasOne(AuthRole::class, ['id' => 'role_id']);
    }
}
