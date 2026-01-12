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
                                                        echo create_form_input('select','year',"Financial Year",true,$year,['id'=>'fyear'],$years); 
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        echo create_form_input('select','month',"Month",true,'',['id'=>'month'],$months); 
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
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        echo create_form_input('text','turnover',"Turnover",true,'',['id'=>'turnover']); 
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        echo create_form_input('date','due_date',"Due Date",true,'',['id'=>'due_date']); 
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <input type="hidden" name="id" id="id">
                                                <button type="submit" name="saveturnover" class="btn btn-sm btn-success" id="save-btn">Save Turnover</button>
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
                                getreport();
                            }
                        });
                    });
                    $('body').on('change','#fyear',function(){
                        var year=$(this).val();
                        $.ajax({
                            type:"post",
                            url:"<?= base_url('orders/getmonths/'); ?>",
                            data:{year:year},
                            success:function(data){
                                $('#month').replaceWith(data);
                                getreport();
                            }
                        });
                    });
                    $('body').on('click','.edit-btn',function(){
                        var id=$(this).val();
                        $.ajax({
                            type:"post",
                            url:"<?= base_url('orders/getturnover/'); ?>",
                            data:{id:id},
                            success:function(data){
                                if(data!='null'){
                                    data=JSON.parse(data);
                                    $('#id').val(data['id']);
                                    $('#month').val(data['month']);
                                    $('#turnover').val(data['turnover']);
                                    $('#due_date').val(data['due_date']);
                                    $('#save-btn').html('Update Turnover').attr('name','updateturnover');
                                    $('.btn-cancel').removeClass('d-none');
                                }
                            }
                        });
                    });
                    $('body').on('click','.delete-btn',function(){
                        if(confirm("Are you sure you want to Delete this Entry?")){
                            var id=$(this).val();
                            $.ajax({
                                type:"post",
                                url:"<?= base_url('orders/deleteturnover/'); ?>",
                                data:{id:id},
                                success:function(data){
                                    window.location.reload();
                                }
                            });
                        }
                    });
                    $('body').on('click','.btn-cancel',function(){
                        $('#save-btn').html('Save Turnover').attr('name','saveturnover');
                        $('.btn-cancel').addClass('d-none');
                        $('#id').val('');
                        resetFields();
                    });
                    <?php
                    if($year!=''){
                    ?>
                    $('#fyear').trigger('change');
                    <?php
                    }
                    ?>
                });
                function getreport(){
                    resetFields();
                    var user_id=$('#user_id').val();
                    var year=$('#fyear').val();
                    $.ajax({
                        type:"post",
                        url:"<?= base_url('orders/getyearlyreport/'); ?>",
                        data:{user_id:user_id,year:year},
                        success:function(data){
                            $('#result').html(data);
                            var rows = $('tr:has(td.done)').first().prevAll();
                            console.log(rows); // Logs the 
                            //rows.addClass('bg-danger');
                            rows.each(function(){
                                $(this).children().last().html('');
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