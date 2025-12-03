<?php

namespace app\modules\backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\backend\models\LetterAction;

/**
 * LetterActionSearch represents the model behind the search form of `app\modules\backend\models\LetterAction`.
 */
class LetterActionSearch extends LetterAction
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'assigned_to', 'action_by', 'letter_id'], 'integer'],
            [['action_name', 'comment', 'file_upload', 'date_actioned'], 'safe'],
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
        $query = LetterAction::find();

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
            'assigned_to' => $this->assigned_to,
            'action_by' => $this->action_by,
            'letter_id' => $this->letter_id,
            'date_actioned' => $this->date_actioned,
        ]);

        $query->andFilterWhere(['like', 'action_name', $this->action_name])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'file_upload', $this->file_upload]);

        return $dataProvider;
    }
}
