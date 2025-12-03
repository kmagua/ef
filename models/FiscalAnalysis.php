<?php

namespace app\models;

use app\modules\backend\models\Fiscal;

class FiscalAnalysis
{

public function getDevBudgetData()
{
    // Fetch data with counties
    $data = \app\modules\backend\models\Fiscal::find()
        ->select(['countyid', 'fy', 'development_budgement'])
        ->with('county')
        ->orderBy(['fy' => SORT_DESC, 'countyid' => SORT_ASC])
        ->asArray()
        ->all();

    // --- Prepare analytics ---
    $analytics = [];
    $totalsByYear = [];

    foreach ($data as $row) {
        $fy = $row['fy'];
        $amount = $row['development_budgement'] ?? 0;

        if (!isset($totalsByYear[$fy])) {
            $totalsByYear[$fy] = 0;
        }
        $totalsByYear[$fy] += $amount;
    }

    // Calculate YoY growth
    ksort($totalsByYear);
    $prevYear = null;
    foreach ($totalsByYear as $year => $total) {
        $growth = 0;
        if ($prevYear !== null && $totalsByYear[$prevYear] > 0) {
            $growth = (($total - $totalsByYear[$prevYear]) / $totalsByYear[$prevYear]) * 100;
        }
        $analytics[$year] = [
            'total'  => $total,
            'growth' => $growth
        ];
        $prevYear = $year;
    }

    return [
        'data'      => $data,       // raw rows with counties
        'analytics' => $analytics   // totals & YoY growth per year
    ];
}


    public function getRecBudgetData()
    {
        return Fiscal::find()
            ->select(['countyid', 'fy', 'recurrent_budget'])
            ->with('county')
            ->orderBy(['fy' => SORT_DESC])
            ->asArray()
            ->all();
    }

    public function getTotalRevenueData()
    {
        return Fiscal::find()
            ->select(['countyid', 'fy', 'total_revenue'])
            ->with('county')
            ->orderBy(['fy' => SORT_DESC])
            ->asArray()
            ->all();
    }

    public function getActualRevenueData()
    {
        return Fiscal::find()
            ->select(['countyid', 'fy', 'actual_revenue'])
            ->with('county')
            ->orderBy(['fy' => SORT_DESC])
            ->asArray()
            ->all();
    }

    public function getRecExpenditureData()
    {
        return Fiscal::find()
            ->select(['countyid', 'fy', 'recurrent_expenditure'])
            ->with('county')
            ->orderBy(['fy' => SORT_DESC])
            ->asArray()
            ->all();
    }

    public function getDevExpenditureData()
    {
        return Fiscal::find()
            ->select(['countyid', 'fy', 'development_expenditure'])
            ->with('county')
            ->orderBy(['fy' => SORT_DESC])
            ->asArray()
            ->all();
    }

    public function getOsrTargetData()
    {
        return Fiscal::find()
            ->select(['countyid', 'fy', 'target_osr'])
            ->with('county')
            ->orderBy(['fy' => SORT_DESC])
            ->asArray()
            ->all();
    }

    public function getOsrActualData()
    {
        return Fiscal::find()
            ->select(['countyid', 'fy', 'actual_osr'])
            ->with('county')
            ->orderBy(['fy' => SORT_DESC])
            ->asArray()
            ->all();
    }

    public function getWagesBudgetedData()
    {
        return Fiscal::find()
            ->select(['countyid', 'fy', 'wages_budgeted'])
            ->with('county')
            ->orderBy(['fy' => SORT_DESC])
            ->asArray()
            ->all();
    }

    public function getWagesActualData()
    {
        return Fiscal::find()
            ->select(['countyid', 'fy', 'wages_actual'])
            ->with('county')
            ->orderBy(['fy' => SORT_DESC])
            ->asArray()
            ->all();
    }
}
