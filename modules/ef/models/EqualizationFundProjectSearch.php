<?php

namespace app\modules\ef\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class EqualizationFundProjectSearch extends EqualizationFundProject
{
    /**
     * @var int Minimum completion percentage for filtering
     */
    public $min_completion;
    
    /**
     * @var int Maximum completion percentage for filtering
     */
    public $max_completion;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'percent_completion', 'min_completion', 'max_completion'], 'integer'],
            [['county', 'constituency', 'sector', 'funding_source', 'project_name'], 'safe'],
            [['budget_2018_19', 'contract_sum'], 'number'],
            // Custom validation for completion range
            [['min_completion', 'max_completion'], 'integer', 'min' => 0, 'max' => 100],
            ['min_completion', 'compare', 'compareAttribute' => 'max_completion', 'operator' => '<=', 'type' => 'number', 'message' => 'Min completion must be less than or equal to max completion.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // Bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'min_completion' => 'Min Completion (%)',
            'max_completion' => 'Max Completion (%)',
        ]);
    }

    /**
     * Search function with multi-select support and completion range filtering.
     */
    public function search($params)
    {
        $query = EqualizationFundProject::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => ['pageSize' => 50],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // Uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // Numeric fields
        $query->andFilterWhere([
            'id' => $this->id,
            'budget_2018_19' => $this->budget_2018_19,
            'contract_sum' => $this->contract_sum,
            'percent_completion' => $this->percent_completion,
        ]);

        /* ---------------------------------------------------------
            MULTI-SELECT FILTERING (Select2 Compatible)
        ---------------------------------------------------------- */

        // County
        if (!empty($this->county)) {
            $query->andFilterWhere(['IN', 'county', (array)$this->county]);
        }

        // Constituency
        if (!empty($this->constituency)) {
            $query->andFilterWhere(['IN', 'constituency', (array)$this->constituency]);
        }

        // Sector
        if (!empty($this->sector)) {
            $query->andFilterWhere(['IN', 'sector', (array)$this->sector]);
        }

        // Funding source
        if (!empty($this->funding_source)) {
            $query->andFilterWhere(['IN', 'funding_source', (array)$this->funding_source]);
        }

        // Text search: Project name
        $query->andFilterWhere(['like', 'project_name', $this->project_name]);
        
        /* ---------------------------------------------------------
            COMPLETION RANGE FILTERING
        ---------------------------------------------------------- */
        
        // Filter by completion percentage range
        if ($this->min_completion !== null && $this->min_completion !== '') {
            $query->andWhere(['>=', 'percent_completion', $this->min_completion]);
        }
        
        if ($this->max_completion !== null && $this->max_completion !== '') {
            $query->andWhere(['<=', 'percent_completion', $this->max_completion]);
        }

        return $dataProvider;
    }
    
    /**
     * Custom search for marginalized areas reports
     * @param array $params
     * @param array $marginalizedCounties
     * @return ActiveDataProvider
     */
    public function searchMarginalized($params, $marginalizedCounties)
    {
        $query = EqualizationFundProject::find()->where(['IN', 'county', $marginalizedCounties]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => ['pageSize' => 50],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Numeric fields
        $query->andFilterWhere([
            'id' => $this->id,
            'budget_2018_19' => $this->budget_2018_19,
            'contract_sum' => $this->contract_sum,
            'percent_completion' => $this->percent_completion,
        ]);

        // County (already filtered by marginalized counties, but allow further filtering)
        if (!empty($this->county)) {
            $query->andFilterWhere(['IN', 'county', (array)$this->county]);
        }

        // Constituency
        if (!empty($this->constituency)) {
            $query->andFilterWhere(['IN', 'constituency', (array)$this->constituency]);
        }

        // Sector
        if (!empty($this->sector)) {
            $query->andFilterWhere(['IN', 'sector', (array)$this->sector]);
        }

        // Funding source
        if (!empty($this->funding_source)) {
            $query->andFilterWhere(['IN', 'funding_source', (array)$this->funding_source]);
        }

        // Text search: Project name
        $query->andFilterWhere(['like', 'project_name', $this->project_name]);
        
        // Filter by completion percentage range
        if ($this->min_completion !== null && $this->min_completion !== '') {
            $query->andWhere(['>=', 'percent_completion', $this->min_completion]);
        }
        
        if ($this->max_completion !== null && $this->max_completion !== '') {
            $query->andWhere(['<=', 'percent_completion', $this->max_completion]);
        }

        return $dataProvider;
    }
}