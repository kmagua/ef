<?php

namespace app\modules\ef\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\ef\models\EqualizationTwoProjects;

/**
 * EqualizationTwoProjectsSearch represents the model behind the search form of `app\modules\ef\models\EqualizationTwoProjects`.
 */
class EqualizationTwoProjectsSearch extends EqualizationTwoProjects
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['county', 'constituency', 'ward', 'marginalised_area', 'project_description', 'sector', 'financial_year'], 'safe'],
            [['project_budget'], 'number'],
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
        $query = EqualizationTwoProjects::find();

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
            'project_budget' => $this->project_budget,
        ]);

        $query->andFilterWhere(['like', 'county', $this->county])
            ->andFilterWhere(['like', 'constituency', $this->constituency])
            ->andFilterWhere(['like', 'ward', $this->ward])
            ->andFilterWhere(['like', 'marginalised_area', $this->marginalised_area])
            ->andFilterWhere(['like', 'project_description', $this->project_description])
            ->andFilterWhere(['like', 'sector', $this->sector])
            ->andFilterWhere(['like', 'financial_year', $this->financial_year]);

        return $dataProvider;
    }
}
