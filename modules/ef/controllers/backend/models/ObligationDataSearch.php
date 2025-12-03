<?php

namespace app\modules\backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\backend\models\ObligationData;

/**
 * ObligationDataSearch represents the model behind the search form of `app\modules\backend\models\ObligationData`.
 */
class ObligationDataSearch extends ObligationData
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'obligation_id'], 'integer'],
            [['fy'], 'safe'],
            [['amt'], 'number'],
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
        $query = ObligationData::find();

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
            'obligation_id' => $this->obligation_id,
            'amt' => $this->amt,
        ]);

        $query->andFilterWhere(['like', 'fy', $this->fy]);

        return $dataProvider;
    }
}
