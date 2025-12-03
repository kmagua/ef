<?php
$this->title = 'Backend';

$this->params['breadcrumbs'][] = $this->title;

$web = Yii::getAlias('@web');
$main_chart = yii\helpers\Url::to(['/backend/default/main-chart'], false);
$rev_comp = yii\helpers\Url::to(['/backend/default/total-revenue-vs-actual-revenue'], false);
$osr_comp = yii\helpers\Url::to(['/backend/default/target-osr-vs-actual'], false);
$budgetary_analysis = yii\helpers\Url::to(['/backend/default/budgetary-analysis'], false);
$pending_bills_url = yii\helpers\Url::to(['/backend/default/pending-bills'], false);
$counties = yii\helpers\ArrayHelper::map(app\modules\backend\models\County::find()->all(), 'CountyId', 'CountyName');
$counties[0] = 'All Counties';
$fys = yii\helpers\ArrayHelper::map(app\modules\backend\models\FinancialYear::find()->all(), 'financial_year', 'financial_year');
$fys[0] = 'All Financial Years';

?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
    .table th a {
  color: #fff !important;
}
    .table th a:hover {
  color: #000 !important;
}
    .bg-primary h1 {
        font-size: 1em;
        font-weight: bold;
        text-shadow: 2px 2px 4px #000;
        margin-bottom: 0;
        font-family: 'Poppins', sans-serif;
    }

    .county-budget-index {
        background-color: #f9f9f9;
        padding: 2px;
        border-radius: 10px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        margin-top: 2px;
    }

    .county-budget-index .grid-view {
        margin-top: 20px;
    }

    .county-budget-index .grid-view th {
        background-color: #7C4102;
        color: white !important;
        text-align: center;
        font-family: 'Poppins', sans-serif;
        font-size: 1em;
        padding: 10px;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        border-bottom: 2px solid #5A2E01;
    }

    .county-budget-index .grid-view td {
        text-align: center;
        font-family: 'Poppins', sans-serif;
        font-size: 1em;
        padding: 15px;
        border-bottom: 1px solid #ddd;
        transition: background-color 0.3s ease;
    }

    .county-budget-index .grid-view td:hover {
        background-color: #f1f1f1;
    }

    .county-budget-index .grid-view td:nth-child(even) {
        background-color: #f7f7f7;
    }

    .county-budget-index .action-column {
        width: 70px;
    }

    .county-budget-index .btn-view {
        color: #fff;
        background-color: #005baa;
        border-color: #005baa;
        padding: 5px 10px;
        border-radius: 4px;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    .county-budget-index .btn-view:hover {
        background-color: #003f8a;
        border-color: #003f8a;
    }

    .bg-gold {
        background-color: #7C4102;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    .bg-gold h1 {
        font-family: 'Poppins', sans-serif;
        font-size: 1.5em;
        font-weight: bold;
        text-shadow: 3px 3px 6px #000;
        margin: 0;
        color: white;
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .pagination li {
        margin: 0 5px;
        list-style: none;
    }

    .pagination a,
    .pagination span {
        display: block;
        padding: 10px;
        border: 1px solid #007bff;
        color: #007bff;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .pagination .active a,
    .pagination .active span {
        background-color: #007bff;
        color: white;
    }

    .pagination a:hover {
        background-color: #0056b3;
        color: white;
    }

    .table-responsive {
        overflow-x: auto;
    }

    @media (max-width: 768px) {
        .county-budget-index {
            padding: 10px;
        }

        .county-budget-index .grid-view th,
        .county-budget-index .grid-view td {
            font-size: 0.9em;
            padding: 10px;
        }

        .pagination a,
        .pagination span {
            padding: 8px;
            font-size: 0.9em;
        }
    }

    @media (max-width: 576px) {
        .bg-gold h1 {
            font-size: 1.8em;
        }

        .county-budget-index .grid-view th,
        .county-budget-index .grid-view td {
            font-size: 0.8em;
            padding: 8px;
        }

        .pagination a,
        .pagination span {
            padding: 6px;
            font-size: 0.8em;
        }
    }

    .table th,
    .table td {
        padding: 10px;
        text-align: left;
        border: 1px solid #1e73be;
    }
</style>


<div class="backend-default-index">
    <div class="row">
      <div class="col-12 grid-margin stretch-card">
        <div class="card corona-gradient-card">
          <div class="card-body py-0 px-0 px-sm-3">

          </div>
        </div>
      </div>
    </div>
           
<div class="row" style="margin-bottom: 2%;">
    <div class="col-xs-2 col-md-2 col-2">
      <!--<select class="form-control" id="Entity">
        <option value="0">All Counties</option>
        <option value="1">Mombasa</option>
        <option value="47">Nairobi</option>
        <option value="36">Bomet</option>
      </select>-->
        <?= yii\helpers\Html::dropDownList('Entity', 0, $counties, [
            'id' => 'Entity',
            'class' =>'form-control',
            'style' => 'border:1px solid #E3FAED']) ?>
    </div>
    <div class="col-xs-2 col-md-2 col-2">
      <!--<select class="form-control" id="FY">
        <option value="0">All Financial Years</option>
        <option value="2014/2015">2014/2015</option>
        <option value="2019/2020">2019/2020</option>
      </select>-->
        <?= yii\helpers\Html::dropDownList('FY', 0, $fys, [
            'id' => 'FY',
            'class' =>'form-control',
            'style' => 'border:1px solid #E3FAED']) ?>
    </div>

    <div class="col-xs-2 col-md-2 col-2">
      <button class="btn btn-sm btn-primary"  id="Visualize" style="height: 35px; width: 100%">Visualize</button>    
    </div>
</div>

  <!--Start of Incomes/--->
 <div class="row">
     
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-9">
                    <div class="d-flex align-items-center align-self-start">
                        <span class="info-box-text">Total Budget:</span>&nbsp;
                        <span class="info-box-number" id="TotalBudget"> - </span>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="icon icon-box-success ">
                      <span class="mdi mdi-arrow-top-right icon-item"></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
     
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-9">
                    <div class="d-flex align-items-center align-self-start">
                        <span class="info-box-text">Development Budget:</span>&nbsp;
                        <span class="info-box-number" id="DevelopmentBudget">-</span>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="icon icon-box-success ">
                      <span class="mdi mdi-arrow-top-right icon-item"></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
        
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-9">
                    <div class="d-flex align-items-center align-self-start">
                        <span class="info-box-text">Recurrent Budget:</span>&nbsp;
                        <span class="info-box-number" id="RecurrentBudget">-</span>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="icon icon-box-success ">
                      <span class="mdi mdi-arrow-top-right icon-item"></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-9">
                    <div class="d-flex align-items-center align-self-start">
                        <span class="info-box-text" >Actual Own Source Revenue</span>
                        <span class="info-box-number" id="TotalOwnSource">-</span>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="icon icon-box-success ">
                      <span class="mdi mdi-arrow-top-right icon-item"></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
        <!-- /.col -->
        
        <!-- /.col -->
      </div><!---End of income-->
      <!-- /.row -->

      <!-- start of Expenditure --->

      <div class="row">
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-9">
                    <div class="d-flex align-items-center align-self-start">
                        <span class="info-box-text">Total Expenditure:</span>&nbsp;
                        <span class="info-box-number" id="TotalExpenditure"> - </span>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="icon icon-box-success ">
                      <span class="mdi mdi-arrow-top-right icon-item"></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
          
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-9">
                    <div class="d-flex align-items-center align-self-start">
                        <span class="info-box-text">Development Expenditure:</span>&nbsp;
                        <span class="info-box-number" id="DevelopmentExpenditure">-</span>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="icon icon-box-success ">
                      <span class="mdi mdi-arrow-top-right icon-item"></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>      
        
        <!-- /.col -->
        
        <!-- /.col -->
        
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-9">
                    <div class="d-flex align-items-center align-self-start">
                        <span class="info-box-text">Recurrent  Expenditure</span>
                        <span class="info-box-number" id="RecurrentExpenditure">-</span>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="icon icon-box-success ">
                      <span class="mdi mdi-arrow-top-right icon-item"></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-9">
                    <div class="d-flex align-items-center align-self-start">
                        <span class="info-box-text">Personal Emoluments:</span>&nbsp;
                        <span class="info-box-number" id="PersonalEmoluments">-</span>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="icon icon-box-success ">
                      <span class="mdi mdi-arrow-top-right icon-item"></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-9">
                    <div class="d-flex align-items-center align-self-start">
                        <span class="info-box-text" >Total Pending Bills:</span>&nbsp;
                        <span class="info-box-number" id="TotalPendingBills">-</span>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="icon icon-box-success ">
                      <span class="mdi mdi-arrow-top-right icon-item"></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>

        <!-- /.col -->
        
        <!-- /.col -->
      </div><!---End of income-->
      <!-- /.row -->
        <div class="row">
        <div class="col-md-7">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Total Revenue against Actual Revenue</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <div class="btn-group">
                  <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-wrench"></i></button>
                  <ul class="dropdown-menu" role="menu">
                    
                    <li class="divider"></li>
                    <li><a href="#">Separated link</a></li>
                  </ul>
                </div>
               
              </div>
            </div>
             <!-- /.box-header -->
            <div class="box-body">
              <div class="row">
                <div class="col-xs-12">
                  <div id="old_container"></div>
                  
                </div>
              </div>

            </div>
          </div>
        </div>
        <div class="col-md-5">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Target OSR vs Actual OSR</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <div class="btn-group">
                  <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-wrench"></i></button>
                  <ul class="dropdown-menu" role="menu">
                    
                    <li class="divider"></li>
                    <li><a href="#">Separated link</a></li>
                  </ul>
                </div>
               
              </div>
            </div>
             <!-- /.box-header -->
            <div class="box-body">
              <div class="row">
                <div class="col-xs-12">
                  <div id="RegionalAnalysis"></div>
                  
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
      
      
      
    <div class="row">
        <div class="col-xs-12">
        <div class="col-md-12"  style="background: white;">
          <div class="row">
            <div class="col-md-8">
             <select  id="BudgetAnalysis">
              <option value="1">Development Budget Vs Recurrent Budget</option>
              <option value="2">Development Expenditure Vs Recurrent Expenditure</option>
              <option value="3">Development Budget Vs Development Expenditure</option>
              <option value="4">Recurrent Budget Vs Recurrent Expenditure</option>

             </select>
           </div>


          </div>
          <div class="row"  >
              <div class="col-md-12" >
                <div class="table-resposive" id="BudgetaryUnitComparisons" >
                </div>
              </div>
          </div>
      </div>

      <div class="col-md-12" >
          <div class="row">
            <div id="PendingBillsDiv"></div>
          </div>
      </div>
    </div>
    </div>

      
<?php
$js = <<<JS
   $("#Visualize").on("click",function(e){       
    e.preventDefault();
        DrawMain();        
        DrawRevenueComparisons();
        DrawRegionalAnalysis();        
        DrawBudgetComparisons();
        DrawPendingBills();
   });


  function DrawMain()
  {    
    var CountyId=$("#Entity").val();
    var FY=$("#FY").val();
     var url="$main_chart";
       $.get(url,{'fy':FY,'cnt_id':CountyId},function(data){
        data = JSON.parse(data)
         $("#DevelopmentBudget").html(data.DevelopmentBudget);
         $("#RecurrentBudget").html(data.RecurrentBudget);
         $("#TotalOwnSource").html(data.TotalOwnSource);
         $("#TotalBudget").html(data.TotalBudget);
         $("#TotalExpenditure").html(data.TotalExpenditure);
         $("#DevelopmentExpenditure").html(data.DevelopmentExpenditure);
         $("#RecurrentExpenditure").html(data.RecurrentExpenditure);
         $("#TotalPendingBills").html(data.TotalPendingBills);         
         $("#PersonalEmoluments").html(data.PersonalEmoluments);

       });     
  }
        
        
        
    DrawRevenueComparisons(); //total revenue action
    DrawRegionalAnalysis();


    function DrawRevenueComparisons(){
         var CountyId=$("#Entity").val();
         var FY=$("#FY").val();
     
        var url="$rev_comp";

         $.get(url,{'FY':FY,'CountyId':CountyId},function(data){
        data = JSON.parse(data);

      Highcharts.chart('old_container', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Total vs Actual Revenue',
        align: 'left'
    },
    subtitle: {
        text:
            'Source: <a target="_blank" ' +
            'href="3">IGFR Data</a>',
        align: 'left'
    },
    xAxis: {
        categories: data.categories,
        crosshair: true,
        accessibility: {
            description: 'Financial Year'
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Amount in KES'
        }
    },
    tooltip: {
        valueSuffix: ' (KES)'
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: data.dataseries
});


    });

      }




      function DrawRegionalAnalysis(){
         var CountyId=$("#Entity").val();
         var FY=$("#FY").val();
     
        var url="{$osr_comp}";

         $.get(url,{'FY':FY,'CountyId':CountyId},function(data){
            data = JSON.parse(data);

      Highcharts.chart('RegionalAnalysis', {
    chart: {
        type: 'bar'
    },
    title: {
        text: 'Total vs Actual OSR',
        align: 'left'
    },
    subtitle: {
        text:
            'Source: <a target="_blank" ' +
            'href="3">IGFR Data</a>',
        align: 'left'
    },
    xAxis: {
        categories: data.categories,
        crosshair: true,
        accessibility: {
            description: 'Financial Year'
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Amount in KES'
        }
    },
    tooltip: {
        valueSuffix: ' (KES)'
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: data.dataseries
});


    });

      }
        
        
        


  function DrawBudgetComparisons()
    {
        var CountyId=$("#Entity").val();
       var FY=$("#FY").val();
       var Type=$("#BudgetAnalysis").val();
        //alert('HERE')

     var url="$budgetary_analysis";

      $.get(url,{'fy':FY,'cnt_id':CountyId,'type':Type},function(data){
         $("#BudgetaryUnitComparisons").html(data);
      });
    }
                
    DrawBudgetComparisons();          
                
                
function DrawPendingBills(){
    var CountyId=$("#Entity").val();
    var FY=$("#FY").val();
    var url="$pending_bills_url";
    $.get(url,{'fy':FY,'cnt_id':CountyId},function(data){
        //data = JSON.parse(data);
        //console.log(data)
        //console.log(JSON.stringify(data))
        Highcharts.chart('PendingBillsDiv', {
        chart: {
             type: 'packedbubble',
             height: '100%'
         },
         title: {
             text: 'Pending Bills Analysis By Counties and Regions',
             align: 'center'
         },
         tooltip: {
             useHTML: true,
             pointFormat: '<b>{point.name}:</b> {point.value}KES'
         },
         plotOptions: {
             packedbubble: {
                 minSize: '5%',
                 maxSize: '55%',
                 zMin: 0,
                 zMax: 1000,
                 layoutAlgorithm: {
                     gravitationalConstant: 0.05,
                     splitSeries: true,
                     seriesInteraction: false,
                     dragBetweenSeries: true,
                     parentNodeLimit: true
                 },
                 dataLabels: {
                     enabled: true,
                     format: '{point.name}',
                     filter: {
                         property: 'y',
                         operator: '>',
                         value: 2500
                     },
                     style: {
                         color: 'black',
                         textOutline: 'none',
                         fontWeight: 'normal'
                     }
                 }
             }
         },
         series: JSON.parse(data)
     });
    });
}
DrawPendingBills();
JS;
$this->registerJs(
    $js,
    \yii\web\View::POS_END,
    'chart_main'
);
?>
</div>