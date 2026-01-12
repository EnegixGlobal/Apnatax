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
                                    <?= form_open_multipart('orders/saveturnover/'); ?>
                                        <div class="row mb-4">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        echo create_form_input('select','user_id',"Customers",true,$user_id,['id'=>'user_id'],$customers); 
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        echo create_form_input('select','firm_id',"Firm",true,'',['id'=>'firm_id'],array(''=>'Select Firm')); 
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        echo create_form_input('select','year',"Financial Year",true,$year,['id'=>'fyear'],$years); 
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        echo create_form_input('text','',"Package",true,'',['id'=>'package','readonly'=>'true']); 
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row d-none" id="yearly">
                                            <div class="col-md-12">
                                                <table class="table table-condensed">
                                                    <thead>
                                                        <tr>
                                                            <th>Month</th>
                                                            <th>Turnover</th>
                                                            <th>Due Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $start=date('Y-04-01');
                                                        for($i=0;$i<12;$i++){
                                                        ?>
                                                        <tr>
                                                            <td><?= date('F-Y',strtotime($start." +$i month")); ?></td>
                                                            <td>
                                                                <?= create_form_input('select','month[]',"",true,'',['class'=>'month d-none'],$months);  ?>
                                                                <?= create_form_input('text','turnover[]',"",false,'',['class'=>'turnover form-control-sm']); ?>
                                                            </td>
                                                            <td>
                                                                <?= create_form_input('date','due_date[]',"",false,'',['class'=>'due_date form-control-sm']); ?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <input type="hidden" name="id" id="id">
                                                <button type="submit" name="saveyearlyturnover" class="btn btn-sm btn-success" id="save-btn">Save Turnover</button>
                                                <button type="button" class="btn btn-sm btn-danger btn-cancel d-none">Cancel</button>
                                            </div>
                                        </div>
                                    <?= form_close(); ?>
                                    <div class="row my-4">
                                        <div class="col-md-12">
                                            <div id="result">
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
                                getfirm(user_id)
                            }
                        });
                    });
                    $('body').on('change','#fyear',function(){
                        var year=$(this).val();
                        $('#yearly').addClass('d-none');
                        $.ajax({
                            type:"post",
                            url:"<?= base_url('orders/getmonths/'); ?>",
                            data:{year:year},
                            success:function(data){
                                data=data.replace('name="month"','name="month[]"');
                                data=data.replace('id="month"',"");
                                data=data.replace('form-control','form-control month d-none');
                                $('.month').replaceWith(data);
                                $('.month').each(function(index,ele){
                                    $(ele).find('option').eq(index+1).prop('selected','selected');
                                    $(ele).trigger('change');
                                });
                                $('#yearly').removeClass('d-none');
                                getreport();
                            }
                        });
                    });
                    $('body').on('change','.month',function(){
                        var text='';
                        if($(this).val()!=''){
                           var text=$(this).find('option:selected').text();
                        }
                        $(this).closest('tr').children().eq(0).text(text);
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
                    var year=$('#fyear').val();
                    $('.turnover,.due_date').val('');
                    $('.turnover,.due_date').prop('readonly',false);;
                    $.ajax({
                        type:"post",
                        url:"<?= base_url('orders/getyearlyreport/'); ?>",
                        data:{user_id:user_id,year:year},
                        success:function(data){
                            $('#result').html(data);
                            $('#result table tr').each(function() {
                                // Remove the last cell in each row
                                $(this).find('td:last, th:last').remove();
                                var result=$('#acc_json').text();
                                if(result!='[]'){
                                    result=JSON.parse(result); 
                                    var i=0;
                                    $('.month').each(function(){
                                        if(result.hasOwnProperty($(this).val())){
                                            var turnover=result[$(this).val()].turnover;
                                            var due_date=result[$(this).val()].due_date;
                                            var paid=result[$(this).val()].paid;
                                            $(this).closest('tr').find('.turnover').val(turnover);
                                            $(this).closest('tr').find('.due_date').val(due_date);
                                            if(paid>0){
                                                $(this).closest('tr').find('.turnover').prop('readonly',true);
                                                $(this).closest('tr').find('.due_date').prop('readonly',true);
                                            }
                                        }
                                    });
                                }
                            });
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