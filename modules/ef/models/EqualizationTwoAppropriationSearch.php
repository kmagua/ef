<?php

namespace app\modules\ef\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\ef\models\EqualizationTwoAppropriation;

/**
 * EqualizationTwoAppropriationSearch represents the model behind the search form of `app\modules\ef\models\EqualizationTwoAppropriation`.
 */
class EqualizationTwoAppropriationSearch extends EqualizationTwoAppropriation
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['county', 'constituency', 'ward', 'marginalised_areas', 'financial_year'], 'safe'],
            [['allocation_ksh'], 'number'],
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
        $query = EqualizationTwoAppropriation::find();

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
            'allocation_ksh' => $this->allocation_ksh,
        ]);

        $query->andFilterWhere(['like', 'county', $this->county])
            ->andFilterWhere(['like', 'constituency', $this->constituency])
            ->andFilterWhere(['like', 'ward', $this->ward])
            ->andFilterWhere(['like', 'marginalised_areas', $this->marginalised_areas])
            ->andFilterWhere(['like', 'financial_year', $this->financial_year]);

        return $dataProvider;
    }
}
