<?php

namespace app\modules\ef\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\ef\models\Disbursement;

class DisbursementSearch extends Disbursement
{
    public $chartType;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id'], 'integer'],

            // Allow multi-select + normal strings
            [['county', 'sector', 'fiscal_year'], 'safe'],

            [['date_disbursed'], 'safe'],
            [['amount_disbursed'], 'number'],
            [['chartType'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Main search function (supports Select2 multiple selection)
     */
    public function search($params, $formName = null)
    {
        $query = Disbursement::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,

            // Latest first
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],

            // Pagination
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        // Load request params
        $this->load($params, $formName);

        // Stop if validation fails
        if (!$this->validate()) {
            return $dataProvider;
        }

        // Basic filters
        $query->andFilterWhere([
            'id' => $this->id,
            'amount_disbursed' => $this->amount_disbursed,
            'user_id' => $this->user_id,
            'date_disbursed' => $this->date_disbursed,
        ]);

        /* -------------------------------------------------
         * MULTIPLE SELECTION FILTERING (ARRAY OR STRING)
         * -------------------------------------------------*/

        // COUNTY filter
        if (!empty($this->county)) {
            $query->andFilterWhere(['IN', 'county', (array)$this->county]);
        }

        // SECTOR filter
        if (!empty($this->sector)) {
            $query->andFilterWhere(['IN', 'sector', (array)$this->sector]);
        }

        // FISCAL YEAR filter
        if (!empty($this->fiscal_year)) {
            $query->andFilterWhere(['IN', 'fiscal_year', (array)$this->fiscal_year]);
        }

        return $dataProvider;
    }
}
