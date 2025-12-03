<?php

namespace app\modules\ef\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\ef\models\EqualizationTwoDisbursement;

/**
 * EqualizationTwoDisbursementSearch represents the model behind the search form of `app\modules\ef\models\EqualizationTwoDisbursement`.
 */
class EqualizationTwoDisbursementSearch extends EqualizationTwoDisbursement
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['county'], 'safe'],
            [['approved_budget', 'total_disbursement'], 'number'],
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
        $query = EqualizationTwoDisbursement::find();

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
            'approved_budget' => $this->approved_budget,
            'total_disbursement' => $this->total_disbursement,
        ]);

        $query->andFilterWhere(['like', 'county', $this->county]);

        return $dataProvider;
    }
}
