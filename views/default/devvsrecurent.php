 <div class="col-xs-12">
  <table class="table table-bordered table-striped table-hover" style="margin-top:5%;">
                <thead>
                  <tr class="warning">
                    <th>FY</th>
                    <th>Development Budget(A)</th>
                     <th>Reccurent Budget(B) </th>
                     <th>Total (A+B) </th>
                     <th>Ratio (A:B)</th>
                    
                  </tr>
                  
                </thead>
                <tbody>
                  <?php foreach($data['models'] as $model){
                    $model= (object) $model; ?>
                    <tr>
                      <td><?=$model->fy?></td>
                       <td style="text-align:right"><?=number_format($model->development_budgement,2)?> &nbsp;&nbsp;&nbsp;</td>
                       <td style="text-align:right"><?=number_format($model->recurrent_budget,2)?> &nbsp;&nbsp;&nbsp;</td>

                       <td style="text-align:right"><?=number_format($model->total,2)?> &nbsp;&nbsp;&nbsp;</td>
                       <td><?=$model->DevRation?> :  <?=$model->RecRation?></td>
                        
                    </tr>


                  <?php } ?>
                </tbody>
                
              </table>
   
 </div>
 