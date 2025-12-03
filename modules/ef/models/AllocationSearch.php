<?php

namespace app\modules\ef\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\ef\models\Allocation;

class AllocationSearch extends Allocation
{
    public $financial_year;
    public $base_year;

    public function rules()
    {
        return [
            [['id'], 'integer'],

            // Allow arrays for Select2 multi-select
            [['financial_year', 'base_year', 'date_added'], 'safe'],

            [['audited_revenues', 'ef_allocation', 'ef_entitlement', 'amount_reflected_dora'], 'number'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Multi-selection search support for financial_year and base_year
     */
    public function search($params, $formName = null)
    {
        $query = Allocation::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => ['pageSize' => 50],
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            return $dataProvider;
        }

        /* ------------------------------------------------------------
           FIX NUMERIC + DATE FIELDS
        ------------------------------------------------------------ */
        $query->andFilterWhere([
            'id' => $this->id,
            'audited_revenues' => $this->audited_revenues,
            'ef_allocation' => $this->ef_allocation,
            'ef_entitlement' => $this->ef_entitlement,
            'amount_reflected_dora' => $this->amount_reflected_dora,
            'date_added' => $this->date_added,
        ]);

        /* ------------------------------------------------------------
           MULTIPLE SELECTION FILTERING
           Select2 passes arrays → use IN()
        ------------------------------------------------------------ */

        // FINANCIAL YEAR
        if (!empty($this->financial_year)) {
            $query->andFilterWhere(['IN', 'financial_year', (array) $this->financial_year]);
        }

        // BASE YEAR
        if (!empty($this->base_year)) {
            $query->andFilterWhere(['IN', 'base_year', (array) $this->base_year]);
        }

        return $dataProvider;
    }
}
