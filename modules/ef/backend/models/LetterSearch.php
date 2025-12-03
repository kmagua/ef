<?php

namespace app\modules\backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\backend\models\Letter;

/**
 * LetterSearch represents the model behind the search form of `app\modules\backend\models\Letter`.
 */
class LetterSearch extends Letter
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'from_county', 'entity_id', 'added_by'], 'integer'],
            [['title', 'letter', 'status', 'date_added'], 'safe'],
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
        $query = Letter::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        //if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            //return $dataProvider;
        //}

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'from_county' => $this->from_county,
            'entity_id' => $this->entity_id,
            'added_by' => $this->added_by,
            'date_added' => $this->date_added,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'letter', $this->letter])
            ->andFilterWhere(['like', 'status', $this->status]);
        $query->orderBy('id desc');
        return $dataProvider;
    }
}
