 <div class="col-xs-12">
  <table class="table table-bordered table-striped table-hover" style="margin-top:5%;">
                <thead>
                  <tr class="warning">
                    <th>FY</th>
                    <th>Development Expenditure(A)</th>
                     <th>Reccurent Expenditure(B) </th>
                     <th>Total (A+B) </th>
                     <th>Ratio (A:B)</th>
                    
                  </tr>
                  
                </thead>
                <tbody>
                  <?php 

                  $devTotal=0;
                  $recTotal=0;
                  $grandTotal=0;


                  foreach($data['models'] as $model){
                    $model= (object) $model;
                    $devTotal=$devTotal+$model->development_expenditure;
                    $recTotal=$recTotal+$model->recurrent_expenditure;
                    $grandTotal=$recTotal+$devTotal;



                    ?>
                    <tr>
                      <td><?=$model->fy?></td>
                       <td style="text-align:right"><?=number_format($model->development_expenditure,2)?> &nbsp;&nbsp;&nbsp;</td>
                       <td style="text-align:right"><?=number_format($model->recurrent_expenditure,2)?> &nbsp;&nbsp;&nbsp;</td>
                      

                       <td style="text-align:right"><?=number_format($model->total,2)?> &nbsp;&nbsp;&nbsp;</td>
                       <td><?=$model->DevRation?> :  <?=$model->RecRation?></td>
                        
                    </tr>


                  <?php } ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th>Total</th>
                    <td style="text-align:right;font-weight: bold;"><?=number_format($devTotal,2)?> &nbsp;&nbsp;&nbsp;</td>
                    <td style="text-align:right;font-weight: bold;"><?=number_format($recTotal,2)?> &nbsp;&nbsp;&nbsp;</td>
                     <td style="text-align:right;font-weight: bold;"><?=number_format($grandTotal,2)?> &nbsp;&nbsp;&nbsp;</td>

                    
                    


                  </tr>
                </tfoot>
                
              </table>
   
 </div>
 