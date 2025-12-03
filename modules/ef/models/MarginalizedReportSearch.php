<?php

namespace app\modules\ef\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * MarginalizedReportSearch represents the model behind the search form for marginalized reports.
 */
class MarginalizedReportSearch extends Model
{
    public $county;
    public $sector;
    public $min_completion;
    public $max_completion;
    public $start_date;
    public $end_date;
    public $project_type;
    public $funding_source;
    public $constituency;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['county', 'sector', 'constituency', 'project_type', 'funding_source'], 'string'],
            [['min_completion', 'max_completion'], 'integer', 'min' => 0, 'max' => 100],
            [['start_date', 'end_date'], 'safe'],
            ['min_completion', 'compare', 'compareAttribute' => 'max_completion', 'operator' => '<=', 'type' => 'number', 'message' => 'Min completion must be less than or equal to max completion.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'county' => 'County',
            'sector' => 'Sector',
            'constituency' => 'Constituency',
            'min_completion' => 'Min Completion (%)',
            'max_completion' => 'Max Completion (%)',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'project_type' => 'Project Type',
            'funding_source' => 'Funding Source',
        ];
    }

    /**
     * Search for marginalized projects with applied filters
     * @param array $params
     * @param array $marginalizedCounties
     * @return ActiveDataProvider
     */
    public function search($params, $marginalizedCounties)
    {
        $query = EqualizationFundProject::find()->where(['IN', 'county', $marginalizedCounties]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => ['pageSize' => 50],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Filter by county
        if (!empty($this->county)) {
            $query->andFilterWhere(['IN', 'county', (array)$this->county]);
        }

        // Filter by sector
        if (!empty($this->sector)) {
            $query->andFilterWhere(['IN', 'sector', (array)$this->sector]);
        }

        // Filter by constituency
        if (!empty($this->constituency)) {
            $query->andFilterWhere(['IN', 'constituency', (array)$this->constituency]);
        }

        // Filter by completion range
        if ($this->min_completion !== null && $this->min_completion !== '') {
            $query->andWhere(['>=', 'percent_completion', $this->min_completion]);
        }
        
        if ($this->max_completion !== null && $this->max_completion !== '') {
            $query->andWhere(['<=', 'percent_completion', $this->max_completion]);
        }

        // Filter by funding source
        if (!empty($this->funding_source)) {
            $query->andFilterWhere(['IN', 'funding_source', (array)$this->funding_source]);
        }

        // Filter by project type (if applicable)
        if (!empty($this->project_type)) {
            $query->andWhere(['like', 'project_name', $this->project_type]);
        }

        // Date range filtering (if you have date fields in your table)
        if (!empty($this->start_date)) {
            $query->andWhere(['>=', 'created_at', date('Y-m-d', strtotime($this->start_date))]);
        }
        
        if (!empty($this->end_date)) {
            $query->andWhere(['<=', 'created_at', date('Y-m-d', strtotime($this->end_date . ' +1 day'))]);
        }

        return $dataProvider;
    }

    /**
     * Get analytics data for marginalized areas
     * @param array $marginalizedCounties
     * @return array
     */
    public function getAnalyticsData($marginalizedCounties)
    {
        // County analytics
        $countyAnalytics = (new Query())
            ->select([
                'county', 
                'COUNT(*) as project_count', 
                'SUM(budget_2018_19) as total_budget',
                'AVG(percent_completion) as avg_completion',
                'SUM(contract_sum) as total_contract_sum'
            ])
            ->from('equalization_fund_project')
            ->where(['IN', 'county', $marginalizedCounties])
            ->groupBy('county')
            ->orderBy(['project_count' => SORT_DESC])
            ->all();

        // Sector analytics
        $sectorAnalytics = (new Query())
            ->select([
                'sector', 
                'COUNT(*) as project_count', 
                'SUM(budget_2018_19) as total_budget',
                'AVG(percent_completion) as avg_completion'
            ])
            ->from('equalization_fund_project')
            ->where(['IN', 'county', $marginalizedCounties])
            ->groupBy('sector')
            ->orderBy(['project_count' => SORT_DESC])
            ->all();

        // Constituency analytics
        $constituencyAnalytics = (new Query())
            ->select([
                'county',
                'constituency', 
                'COUNT(*) as project_count', 
                'SUM(budget_2018_19) as total_budget',
                'AVG(percent_completion) as avg_completion'
            ])
            ->from('equalization_fund_project')
            ->where(['IN', 'county', $marginalizedCounties])
            ->andWhere(['not', ['constituency' => '']])
            ->groupBy(['county', 'constituency'])
            ->orderBy(['county' => SORT_ASC, 'project_count' => SORT_DESC])
            ->all();

        // Performance analytics
        $performanceAnalytics = [];
        $completionRanges = [
            ['min' => 0, 'max' => 25, 'label' => '0-25%'],
            ['min' => 26, 'max' => 50, 'label' => '26-50%'],
            ['min' => 51, 'max' => 75, 'label' => '51-75%'],
            ['min' => 76, 'max' => 100, 'label' => '76-100%'],
        ];

        foreach ($completionRanges as $range) {
            $count = EqualizationFundProject::find()
                ->where(['IN', 'county', $marginalizedCounties])
                ->andWhere(['>=', 'percent_completion', $range['min']])
                ->andWhere(['<=', 'percent_completion', $range['max']])
                ->count();
            
            $performanceAnalytics[] = [
                'range' => $range['label'],
                'count' => $count,
            ];
        }

        // Financial analytics
        $financialAnalytics = (new Query())
            ->select([
                'county',
                'SUM(budget_2018_19) as total_budget',
                'SUM(contract_sum) as total_contract_sum',
                '(SUM(contract_sum) / SUM(budget_2018_19)) * 100 as utilization_rate'
            ])
            ->from('equalization_fund_project')
            ->where(['IN', 'county', $marginalizedCounties])
            ->groupBy('county')
            ->orderBy(['utilization_rate' => SORT_DESC])
            ->all();

        // Trend analytics (by month if you have date fields)
        $trendAnalytics = [];
        // This would require date fields in your table to work properly
        // For now, we'll return an empty array as a placeholder

        return [
            'countyAnalytics' => $countyAnalytics,
            'sectorAnalytics' => $sectorAnalytics,
            'constituencyAnalytics' => $constituencyAnalytics,
            'performanceAnalytics' => $performanceAnalytics,
            'financialAnalytics' => $financialAnalytics,
            'trendAnalytics' => $trendAnalytics,
        ];
    }

    /**
     * Get filter options for dropdowns
     * @param array $marginalizedCounties
     * @return array
     */
    public function getFilterOptions($marginalizedCounties)
    {
        // County options
        $countyOptions = EqualizationFundProject::find()
            ->select('county')
            ->where(['IN', 'county', $marginalizedCounties])
            ->distinct()
            ->column();
        
        // Sector options
        $sectorOptions = EqualizationFundProject::find()
            ->select('sector')
            ->where(['IN', 'county', $marginalizedCounties])
            ->distinct()
            ->column();
        
        // Constituency options
        $constituencyOptions = EqualizationFundProject::find()
            ->select('constituency')
            ->where(['IN', 'county', $marginalizedCounties])
            ->andWhere(['not', ['constituency' => '']])
            ->distinct()
            ->column();
        
        // Funding source options
        $fundingSourceOptions = EqualizationFundProject::find()
            ->select('funding_source')
            ->where(['IN', 'county', $marginalizedCounties])
            ->distinct()
            ->column();

        return [
            'countyOptions' => array_combine($countyOptions, $countyOptions),
            'sectorOptions' => array_combine($sectorOptions, $sectorOptions),
            'constituencyOptions' => array_combine($constituencyOptions, $constituencyOptions),
            'fundingSourceOptions' => array_combine($fundingSourceOptions, $fundingSourceOptions),
        ];
    }
}