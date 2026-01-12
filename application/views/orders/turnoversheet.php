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
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <?php
                                                    echo create_form_input('text','',"Package",true,'',['id'=>'package','readonly'=>'true']); 
                                                ?>
                                            </div>
                                        </div>
                                    </div>
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
                                getfirm(user_id);
                            }
                        });
                    });
                    $('body').on('change','#firm_id,#fyear',function(){
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