<?php

namespace app\modules\ef\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\ef\models\Request;

/**
 * RequestSearch represents the model behind the search form of `app\modules\ef\models\Request`.
 */
class RequestSearch extends Request
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id'], 'integer'],
            [['county', 'sector', 'fiscal_year', 'status', 'date_requested'], 'safe'],
            [['amount_requested'], 'number'],
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
        $query = Request::find();

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
            'amount_requested' => $this->amount_requested,
            'user_id' => $this->user_id,
            'date_requested' => $this->date_requested,
        ]);

        $query->andFilterWhere(['like', 'county', $this->county])
            ->andFilterWhere(['like', 'sector', $this->sector])
            ->andFilterWhere(['like', 'fiscal_year', $this->fiscal_year])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
