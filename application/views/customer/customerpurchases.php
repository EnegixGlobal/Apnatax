<style>
    .cell-right{
        text-align: right;
    }
    .table{
        min-width: 1800px;
    }
    .table th,
    .table td{
        width: 140px;
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
                                                    echo create_form_input('select','year',"Financial Year",true,$year,['id'=>'fyear'],$years); 
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
                        getpurchases();
                    });
                    $('body').on('change','#fyear',function(){
                        getpurchases();
                    });
                    <?php
                    if($year!=''){
                    ?>
                    $('#fyear').trigger('change');
                    <?php
                    }
                    ?>
                });
                function getpurchases(){
                    var user_id=$('#user_id').val();
                    var year=$('#fyear').val();
                    $.ajax({
                        type:"post",
                        url:"<?= base_url('customers/getpurchases/'); ?>",
                        data:{user_id:user_id,year:year},
                        success:function(data){
                            $('#result').html(data);
                        }
                    });
                }
                function reloadAjax(){
                    getpurchases()
                }
            </script>