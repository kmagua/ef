<?php

namespace app\modules\backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\backend\models\Fiscal;

/**
 * FiscalSearch represents the model behind the search form of `app\modules\backend\models\Fiscal`.
 */
class FiscalSearch extends Fiscal
{
    public $cnt_name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'countyid', 'added_by'], 'integer'],
            [['fy', 'date_created', 'cnt_name'], 'safe'],
            [['development_budgement', 'recurrent_budget', 'total_revenue', 'actual_revenue', 'recurrent_expenditure', 'development_expenditure', 'target_osr', 'actual_osr', 'personal_emoluments', 'pending_bills'], 'number'],
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
        $query = Fiscal::find();
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
            'countyid' => $this->countyid,
            'development_budgement' => $this->development_budgement,
            'recurrent_budget' => $this->recurrent_budget,
            'total_revenue' => $this->total_revenue,
            'actual_revenue' => $this->actual_revenue,
            'recurrent_expenditure' => $this->recurrent_expenditure,
            'development_expenditure' => $this->development_expenditure,
            'target_osr' => $this->target_osr,
            'actual_osr' => $this->actual_osr,
            'personal_emoluments' => $this->personal_emoluments,
            'pending_bills' => $this->pending_bills,
            'date_created' => $this->date_created,
            'added_by' => $this->added_by,
        ]);

        $query->andFilterWhere(['like', 'fy', $this->fy])
            ->andFilterWhere(['like', 'county.CountyName', $this->cnt_name]);

        return $dataProvider;
    }
}
