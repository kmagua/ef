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
    $query->joinWith('county'); // Joins the county table

    $dataProvider = new ActiveDataProvider([
        'query' => $query,
    ]);

    $this->load($params);

    if (!$this->validate()) {
        return $dataProvider;
    }

    // **🔹 Fix: Specify the table for `countyid` to avoid ambiguity**
    $query->andFilterWhere([
        'fiscal.id' => $this->id,
        'fiscal.countyid' => $this->countyid, // ✅ Prefixed with `fiscal.`
        'fiscal.development_budgement' => $this->development_budgement,
        'fiscal.recurrent_budget' => $this->recurrent_budget,
        'fiscal.total_revenue' => $this->total_revenue,
        'fiscal.actual_revenue' => $this->actual_revenue,
        'fiscal.recurrent_expenditure' => $this->recurrent_expenditure,
        'fiscal.development_expenditure' => $this->development_expenditure,
        'fiscal.target_osr' => $this->target_osr,
        'fiscal.actual_osr' => $this->actual_osr,
        'fiscal.personal_emoluments' => $this->personal_emoluments,
        'fiscal.pending_bills' => $this->pending_bills,
        'fiscal.date_created' => $this->date_created,
        'fiscal.added_by' => $this->added_by,
    ]);

    $query->andFilterWhere(['like', 'fiscal.fy', $this->fy])
          ->andFilterWhere(['like', 'county.CountyName', $this->cnt_name]);

    return $dataProvider;
}
}