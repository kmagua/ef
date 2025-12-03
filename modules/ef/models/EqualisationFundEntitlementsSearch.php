<?php

namespace app\modules\ef\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\ef\models\EqualisationFundEntitlements;

/**
 * EqualisationFundEntitlementsSearch represents the model behind the search form of `app\modules\ef\models\EqualisationFundEntitlements`.
 */
class EqualisationFundEntitlementsSearch extends EqualisationFundEntitlements
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['financial_year', 'base_year_most_recent_audited_revenue'], 'safe'],
            [['audited_approved_revenue_ksh', 'ef_entitlement_ksh', 'amount_reflected_in_dora_ksh', 'transfers_into_ef', 'arrears'], 'number'],
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
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = EqualisationFundEntitlements::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'audited_approved_revenue_ksh' => $this->audited_approved_revenue_ksh,
            'ef_entitlement_ksh' => $this->ef_entitlement_ksh,
            'amount_reflected_in_dora_ksh' => $this->amount_reflected_in_dora_ksh,
            'transfers_into_ef' => $this->transfers_into_ef,
            'arrears' => $this->arrears,
        ]);

        // Handle financial_year as an array if multiple values are selected
        if (is_array($this->financial_year)) {
            $query->andFilterWhere(['in', 'financial_year', $this->financial_year]);
        } else {
            $query->andFilterWhere(['like', 'financial_year', $this->financial_year]);
        }

        $query->andFilterWhere(['like', 'base_year_most_recent_audited_revenue', $this->base_year_most_recent_audited_revenue]);

        return $dataProvider;
    }
}