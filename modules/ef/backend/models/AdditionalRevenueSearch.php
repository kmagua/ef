<?php

namespace app\modules\backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\backend\models\AdditionalRevenue;

/**
 * AdditionalRevenueSearch represents the model behind the search form of `app\modules\backend\models\AdditionalRevenue`.
 */
class AdditionalRevenueSearch extends AdditionalRevenue
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'county_id', 'project_id', 'allocation_code'], 'integer'],
            [['fy'], 'safe'],
            [['project_amt', 'actual_amt'], 'number'],
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
        $query = AdditionalRevenue::find();

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
            'project_id' => $this->project_id,
            'project_amt' => $this->project_amt,
            'actual_amt' => $this->actual_amt,
            'allocation_code' => $this->allocation_code,
        ]);

        $query->andFilterWhere(['like', 'fy', $this->fy]);

        return $dataProvider;
    }
}
