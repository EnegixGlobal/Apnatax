<style>
    .cell-right{
        text-align: right;
    }
</style>
<?php
$user_id=$year='';
if($this->session->flashdata('user_id')!==NULL){
    $user_id=$this->session->flashdata('user_id');
}
if($this->session->flashdata('year')!==NULL){
    $year=$this->session->flashdata('year');
}
?>
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <?php
                                                    echo create_form_input('text','',"Package",true,$package['name'],['id'=>'package','readonly'=>'true']); 
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row my-4">
                                        <div class="col-md-12">
                                            <div id="result">
                                                <table class="table table-bordered" id="acc_table">
                                                    <thead>
                                                        <tr>
                                                            <th>Month</th>
                                                            <th>Outstanding</th>
                                                            <th>GTO</th>
                                                            <th>Accounts Fee</th>
                                                            <th>Other Fee</th>
                                                            <th>Paid</th>
                                                            <th>Balance</th>
                                                            <th>Due date</th>
                                                            <th>Penalty</th>
                                                            <th>Total</th>
                                                            <th>Delay in Days</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                            //print_pre($accountancy);
                                                            $date=date('Y-m-d');
                                                            $percent=2/100;
                                                            $result=array();
                                                            $prev=array();
                                                            if(!empty($accountancy)){
                                                                $total_fees=$total_other=$total_paid=$total_penalty=0;
                                                                $total_days=0;
                                                                $outstanding=$total=0;
                                                                $fees=$total_turnover/$package['turnover'];
                                                                $fees*=$package['rate'];
                                                                $count=count($accountancy);
                                                                $last=end($accountancy);
                                                                if($last['date']==''){
                                                                    $count--;
                                                                }
                                                                $acc_fees=$fees/$count;
                                                                foreach($accountancy as $single){
                                                                    $days=$paid=$penalty=0;
                                                                    $paid=!empty($single['paid'])?$single['paid']:0;
                                                                    $outstanding=$total;
                                                                    if($single['date']!=''){
                                                                        $acc_fees=$fees/$count;
                                                                    }
                                                                    else{
                                                                        $acc_fees=0;
                                                                    }
                                                                    $other_fee=$single['other_fee'];
                                                                    $total_other+=$other_fee;
                                                                    $balance=$outstanding+$acc_fees+$other_fee;
                                                                    if($single['due_date']<$date && $paid<$balance){
                                                                        $balance-=$paid;
                                                                        $date1 = new DateTime($single['due_date']);
                                                                        $date2 = new DateTime($date);

                                                                        // Calculate the difference
                                                                        $interval = $date1->diff($date2);

                                                                        // Get the difference in days
                                                                        $days = $interval->days;
                                                                        $penalty=($percent*$balance);
                                                                        if($days<30){
                                                                            $penalty/=30;
                                                                            $penalty*=$days;
                                                                        }
                                                                        $penalty=round($penalty);
                                                                        $total_penalty+=$penalty;
                                                                        $total_days+=$days;
                                                                    }
                                                                    else{
                                                                        $balance-=$paid;
                                                                    }
                                                                    $total=$balance+$penalty;
                                                                    $total_fees+=$acc_fees;
                                                                    $total_paid+=$paid;
                                                                    $month=$single['date']!=''?date("Ym",strtotime($single['date'])):'';
                                                                    if($month!=''){
                                                                        if($paid>0 && !empty($prev)){
                                                                            foreach($prev as $m){
                                                                                $result[$m]['paid']=1;
                                                                            }
                                                                            $prev=array();
                                                                        }
                                                                        $result[$month]=array('turnover'=>$single['turnover'],
                                                                                             'due_date'=>$single['due_date'],
                                                                                              'paid'=>$paid);
                                                                        $prev[]=$month;
                                                                    }
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <?= $single['date']!=''?date('F-y',strtotime($single['date'])):'--'; ?>
                                                            </td>
                                                            <td class="cell-right">
                                                                <?= $this->amount->toDecimal($outstanding,false); ?>
                                                            </td>
                                                            <td class="cell-right">
                                                                <?= $this->amount->toDecimal($single['turnover'],false); ?>
                                                            </td>
                                                            <td class="cell-right">
                                                                <?= $this->amount->toDecimal($acc_fees,false); ?>
                                                            </td>
                                                            <td class="cell-right">
                                                                <?= $this->amount->toDecimal($other_fee,false); ?>
                                                            </td>
                                                            <td class="cell-right">
                                                                <?= $this->amount->toDecimal($paid,false); ?>
                                                            </td>
                                                            <td class="cell-right">
                                                                <?= $this->amount->toDecimal($balance,false); ?>
                                                            </td>
                                                            <td>
                                                                <?= $single['due_date']!=''?date('d-m-Y',strtotime($single['due_date'])):'--'; ?>
                                                            </td>
                                                            <td class="cell-right">
                                                                <?= $this->amount->toDecimal($penalty,false); ?>
                                                            </td>
                                                            <td class="cell-right">
                                                                <?= $this->amount->toDecimal($total,false); ?>
                                                            </td>
                                                            <td><?= $days; ?></td>
                                                            <?php if($paid==0){ ?>
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-info edit-btn" value="<?= $single['id']; ?>"><i class="fa fa-edit"></i></button>
                                                                <button type="button" class="btn btn-sm btn-danger delete-btn" value="<?= $single['id']; ?>"><i class="fa fa-trash"></i></button>
                                                            </td>
                                                            <?php }
                                                                else{
                                                            ?>
                                                            <td class="done">
                                                            </td>
                                                            <?php
                                                                }
                                                            ?>
                                                        </tr>
                                                        <?php
                                                                }
                                                            }
                                                            $rows=count($report);
                                                            if(!empty($report)){
                                                                foreach($report as $key=>$row){
                                                                    if($key==$rows-1){
                                                                        $footer=$row;
                                                                        break;
                                                                    }
                                                        ?>
                                                        <tr>
                                                            <?php
                                                                foreach($row as $value){
                                                            ?>
                                                            <td><?= $value; ?></td>
                                                            <?php
                                                                }
                                                            ?>
                                                        </tr>
                                                        <?php
                                                                }
                                                            }
                                                        ?>
                                                    </tbody>
                                                    <?php if(!empty($accountancy)){ ?>
                                                    <tfoot>
                                                        <tr>
                                                            <th></th>
                                                            <th></th>
                                                            <th class="cell-right">
                                                                <?= $this->amount->toDecimal($total_turnover,false); ?>
                                                            </th>
                                                            <th class="cell-right">
                                                                <?= $this->amount->toDecimal($total_fees,false); ?>
                                                            </th>
                                                            <th class="cell-right">
                                                                <?= $this->amount->toDecimal($total_other,false); ?>
                                                            </th>
                                                            <th class="cell-right">
                                                                <?= $this->amount->toDecimal($total_paid,false); ?>
                                                            </th>
                                                            <th></th>
                                                            <th></th>
                                                            <th class="cell-right">
                                                                <?= $this->amount->toDecimal($total_penalty,false); ?>
                                                            </th>
                                                            <th class="cell-right">
                                                                <?= $this->amount->toDecimal($total_fees+$total_penalty-$total_paid,false); ?>
                                                            </th>
                                                            <th><?= $total_days; ?></th>
                                                            <th>
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                    <?php } ?>
                                                    <?php if(!empty($footer)){ ?>
                                                    <tfoot>
                                                        <tr>
                                                            <?php
                                                                foreach($footer as $value){
                                                            ?>
                                                            <td><?= $value; ?></td>
                                                            <?php
                                                                }
                                                            ?>
                                                        </tr>
                                                    </tfoot>
                                                    <?php } ?>
                                                </table>
                                                <div id="acc_json" class="d-none"><?= json_encode($result); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

            <script>
                $(document).ready(function(e) {
                    $('body').on('change','#user_id',function(){
                        var user_id=$(this).val();
                        $.ajax({
                            type:"post",
                            url:"<?= base_url('orders/getpackage/'); ?>",
                            data:{user_id:user_id},
                            success:function(data){
                                $('#package').val(data);
                                getfirm(user_id);
                            }
                        });
                    });
                    $('body').on('change','#fyear',function(){
                        getreport();
                    });
                    <?php
                    if($year!=''){
                    ?>
                    $('#fyear').trigger('change');
                    <?php
                    }
                    ?>
                });
                
                function getfirm(user_id){
                    $.ajax({
                        type:"post",
                        url:"<?= base_url('orders/getfirms/'); ?>",
                        data:{user_id:user_id},
                        success:function(data){
                            $('#firm_id').html(data);
                            getreport();
                        }
                    });     
                }
                
                function getreport(){
                    resetFields();
                    var user_id=$('#user_id').val();
                    var firm_id=$('#firm_id').val();
                    var year=$('#fyear').val();
                    $.ajax({
                        type:"post",
                        url:"<?= base_url('orders/getyearlyreport/'); ?>",
                        data:{user_id:user_id,firm_id:firm_id,year:year},
                        success:function(data){
                            $('#result').html(data);
                            $('#result table tr').each(function(){
                                $(this).children().last().remove();
                            });
                            //var rows = $('tr:has(td.done)').first().prevAll();
                            //console.log(rows); // Logs the 
                            //rows.addClass('bg-danger');
                            //rows.each(function(){
                                //$(this).children().last().html('');
                            //});
                        }
                    });
                }
                function resetFields(){
                    $('#turnover,#due_date').val('');
                }
                function reloadAjax(){
                    getreport()
                }
            </script>