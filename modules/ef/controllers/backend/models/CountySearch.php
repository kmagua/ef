<?php

namespace app\modules\backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\backend\models\County;

/**
 * CountySearch represents the model behind the search form of `app\modules\backend\models\County`.
 */
class CountySearch extends County
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['CountyId', 'RegionId', 'population'], 'integer'],
            [['CountyName', 'CountyCode'], 'safe'],
            [['size', 'poverty_index', 'gcp'], 'number'],
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
        $query = County::find();

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
            'CountyId' => $this->CountyId,
            'RegionId' => $this->RegionId,
            'size' => $this->size,
            'population' => $this->population,
            'poverty_index' => $this->poverty_index,
            'gcp' => $this->gcp,
        ]);

        $query->andFilterWhere(['like', 'CountyName', $this->CountyName])
            ->andFilterWhere(['like', 'CountyCode', $this->CountyCode]);

        return $dataProvider;
    }
}
