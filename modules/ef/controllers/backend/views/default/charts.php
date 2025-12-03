<?php

/** @var yii\web\View $this */
/** @var app\modules\backend\models\AdditionalRevenueSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Charts';
$this->params['breadcrumbs'][] = $this->title;
$equitable_share_link = \yii\helpers\Url::to(['/backend/default/equitable-chart'], false);
$equitable_by_region_link = \yii\helpers\Url::to(['/backend/default/equitable-byregion-chart'], false);

?>
      <!-- /.row -->

        <div class="row">
        <div class="col-md-7">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Equitable Revenue  Share Analysis By Financial Year </h3>

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
                <div class="col-xs-12 col-12">
                  <div id="eq_chart_container" style="border:1px solid red"></div>
                  
                </div>
              </div>
            </div>
          </div>
        </div>
            
        <div class="col-md-5">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Equitable Revenue  Share Analysis By Region </h3>

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
        <div class="col-md-5"  style="background: white;">
          <div class="row">
            <div class="col-md-6">
              <input type="radio" name="top_param" checked value="1">Top 10 Counties Per Revenue share
            </div>
            <div class="col-md-6">
              <input type="radio" name="top_param" checked value="1">Bottom 10 Counties Per Revenue share
            </div>
            
          </div>
          <div class="row"  >
            <div class="table-resposive">
            <table class="table table-bordered table-hover" >
              <thead>
                <tr class="warning">
                  <th>#</th>
                  <th>County Name</th>
                  <th>Amount</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="TableContent">
                
              </tbody>
              
            </table>

        </div>

          </div>          
      </div>
    </div>
  </div>

<script type="text/javascript">
    
  /*var url="<?= $equitable_share_link ?>";
    $.get(url,function(data){
       $("#TableContent").html("");
       $("#TableContent").html(data);
    })*/
  

</script>

<?php
$js = <<<JS
var url="{$equitable_share_link}";
alert(url)
$.get(url,function(data){

      Highcharts.chart('eq_chart_container', {
    chart: {
        type: 'column'
    },
    title: {
        align: 'left',
        text: 'Revenue Distribution'
    },
    subtitle: {
        align: 'left',
        text: 'Click the columns to view versions. Source: <a href="#" target="_blank">IGFR System</a>'
    },
    accessibility: {
        announceNewData: {
            enabled: true
        }
    },
    xAxis: {
        type: 'category'
    },
    yAxis: {
        title: {
            text: 'Total Share'
        }

    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y}'
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}'
    },

    series: [
        {
            name: 'Financail Year',
            colorByPoint: true,
            data: JSON.parse(data)
        }
    ],
    
});
});
JS;
$this->registerJs(
    $js,
    \yii\web\View::POS_END,
    'chart_2'
);


$js2 = <<<JS
var url="{$equitable_by_region_link}";
    $.get(url,function(data){
		Highcharts.chart('RegionalAnalysis', {
		chart: {
			type: 'pie'
		},
		title: {
			text: 'Revenue share By Region',
			align: 'left'
		},
		subtitle: {
			text: 'Click the slices to view versions. Source: <a href="http://statcounter.com" target="_blank">statcounter.com</a>',
			align: 'left'
		},

		accessibility: {
			announceNewData: {
				enabled: true
			},
			point: {
				valueSuffix: ''
			}
		},

		plotOptions: {
			series: {
				borderRadius: 5,
				dataLabels: [{
					enabled: true,
					distance: 15,
					format: '{point.name}'
				}, {
					enabled: true,
					distance: '-30%',
					filter: {
						property: 'percentage',
						operator: '>',
						value: 5
					},
					format: '{point.y}',
					style: {
						fontSize: '0.9em',
						textOutline: 'none'
					}
				}]
			}
		},

		tooltip: {
			headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
			pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
		},

		series: [
			{
				name: 'Browsers',
				colorByPoint: true,
				data: JSON.parse(data)
			}
		],    
	});
});
JS;

$this->registerJs(
    $js2,
    \yii\web\View::POS_END,
    'chart_region'
);