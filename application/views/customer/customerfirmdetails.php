<style>
    .cell-right{
        text-align: right;
    }
    .table th,
    .table td{
        white-space: nowrap;
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
                                                <label for="firm_id">Customers firm <span class="text-danger">*</span></label>
                                                <select name="firm_id" id="firm_id" class="form-control select2" required>
                                                    <option value="">Select Firm</option>
                                                </select>
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
                                    <div class="row my-4">
                                        <div class="col-md-12">
                                            <div id="result" class="table-responsive">
                                            </div>
                                        </div>
                                    </div>
                                </div>

            <script>
                $(document).ready(function(e) {
                    $('body').on('change','#user_id',function(){
                        getuserfirms();
                    });
                    $('body').on('change','#firm_id',function(){
                        getfirmdetails();
                    });
                    $('body').on('change','#fyear',function(){
                        getfirmdetails();
                    });
                    <?php
                    if($year!=''){
                    ?>
                    $('#fyear').trigger('change');
                    <?php
                    }
                    ?>
                });
                function getuserfirms(){
                    var user_id=$('#user_id').val();
                    $('#result').html('');
                    $.ajax({
                        type:"post",
                        url:"<?= base_url('customers/getuserfirms/'); ?>",
                        data:{user_id:user_id},
                        success:function(data){
                            $('#firm_id').html(data);
                            $('#firm_id').trigger('change');
                        }
                    });
                }
                function getfirmdetails(){
                    var user_id=$('#user_id').val();
                    var firm_id=$('#firm_id').val();
                    var year=$('#fyear').val();
                    
                    if(!firm_id) {
                        $('#result').html('');
                        return;
                    }

                    $.ajax({
                        type:"post",
                        url:"<?= base_url('customers/getfirmdetails/'); ?>",
                        data:{user_id:user_id, firm_id:firm_id, year:year},
                        success:function(data){
                            $('#result').html(data);
                        }
                    });
                }
                function reloadAjax(){
                    getfirmdetails();
                }
            </script>
