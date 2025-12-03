<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserLoginActivitySearch extends UserLoginActivity
{
    public $user_name; // Virtual attribute

    public function rules()
    {
        return [
            [['user_id', 'risk_score', 'attempt_count'], 'integer'],
            [['login_status', 'auth_method', 'login_ip', 'browser', 'os', 'device', 'location', 'application', 'login_source', 'login_at', 'logout_at', 'reason', 'country_code', 'user_name'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // Bypass scenarios() from the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = UserLoginActivity::find()->joinWith('user');

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['login_at' => SORT_DESC]),
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'user_login_activity.user_id' => $this->user_id,
            'risk_score' => $this->risk_score,
            'attempt_count' => $this->attempt_count,
            'login_at' => $this->login_at,
            'logout_at' => $this->logout_at,
        ]);

        $query->andFilterWhere(['like', 'login_status', $this->login_status])
              ->andFilterWhere(['like', 'auth_method', $this->auth_method])
              ->andFilterWhere(['like', 'login_ip', $this->login_ip])
              ->andFilterWhere(['like', 'browser', $this->browser])
              ->andFilterWhere(['like', 'os', $this->os])
              ->andFilterWhere(['like', 'device', $this->device])
              ->andFilterWhere(['like', 'location', $this->location])
              ->andFilterWhere(['like', 'application', $this->application])
              ->andFilterWhere(['like', 'login_source', $this->login_source])
              ->andFilterWhere(['like', 'reason', $this->reason])
              ->andFilterWhere(['like', 'country_code', $this->country_code])
              ->andFilterWhere(['like', 'user.user_names', $this->user_name]);

        return $dataProvider;
    }
}
