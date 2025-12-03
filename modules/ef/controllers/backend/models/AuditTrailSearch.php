<?php

namespace app\modules\backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\backend\models\AuditTrail;

/**
 * AuditTrailSearch represents the model behind the search form of `backend\models\AuditTrail`.
 */
class AuditTrailSearch extends AuditTrail
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'model_id', 'user_id', 'change_no'], 'integer'],
            [['old_value', 'new_value', 'model', 'field', 'comments', 'action', 'stamp'], 'safe'],
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
        $query = AuditTrail::find();

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
            'model_id' => $this->model_id,
            'user_id' => $this->user_id,
            'change_no' => $this->change_no,
        ]);

        $query->andFilterWhere(['like', 'old_value', $this->old_value])
            ->andFilterWhere(['like', 'new_value', $this->new_value])
            ->andFilterWhere(['like', 'model', $this->model])
            ->andFilterWhere(['like', 'field', $this->field])
            ->andFilterWhere(['like', 'comments', $this->comments])
            ->andFilterWhere(['like', 'action', $this->action])
            ->andFilterWhere(['like', 'stamp', $this->stamp]);
        $query->orderBy('id desc'); 
        return $dataProvider;
    }
}
