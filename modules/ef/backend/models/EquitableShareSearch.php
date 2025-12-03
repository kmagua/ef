<?php

namespace app\modules\backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\backend\models\EquitableRevenueShare;

/**
 * EquitableShareSearch represents the model behind the search form of `app\modules\backend\models\EquitableRevenueShare`.
 */
class EquitableShareSearch extends EquitableRevenueShare
{
    public $cnt_name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {        
        return [
            [['id', 'county_id'], 'integer'],
            [['project_amt', 'actual_amt', 'balance_bf', 'osr_projected', 'osr_actual'], 'number'],
            [['fy', 'cnt_name'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = EquitableRevenueShare::find();
        $query->joinWith('county');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'county_id' => $this->county_id,
            'project_amt' => $this->project_amt,
            'actual_amt' => $this->actual_amt,
            'balance_bf' => $this->balance_bf,
            'osr_projected' => $this->osr_projected,
            'osr_actual' => $this->osr_actual,
        ]);

        $query->andFilterWhere(['like', 'fy', $this->fy])
            ->andFilterWhere(['like', 'county.CountyName', $this->cnt_name]);

        return $dataProvider;
    }
}
