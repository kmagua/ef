<?php

namespace app\modules\backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\backend\models\ExternalEntity;

/**
 * ExternalEntitySearch represents the model behind the search form of `app\modules\backend\models\ExternalEntity`.
 */
class ExternalEntitySearch extends ExternalEntity
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'parent_mda', 'added_by'], 'integer'],
            [['entity_name', 'type', 'po_box', 'physical_address', 'date_added'], 'safe'],
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
        $query = ExternalEntity::find();

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
            'parent_mda' => $this->parent_mda,
            'added_by' => $this->added_by,
            'date_added' => $this->date_added,
        ]);

        $query->andFilterWhere(['like', 'entity_name', $this->entity_name])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'po_box', $this->po_box])
            ->andFilterWhere(['like', 'physical_address', $this->physical_address]);

        return $dataProvider;
    }
}
