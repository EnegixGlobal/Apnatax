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

                $('body').on('click', '.renew-btn', function() {
                    var id = $(this).data('id');
                    var amount = parseFloat($(this).data('amount')).toFixed(2);
                    var user_id = $(this).data('userid');
                    var firm_id = $(this).data('firmid');
                    var date = $(this).data('date');

                    if (confirm("Are you sure you want to deduct ₹" + amount + " from the wallet for this renewal?")) {
                        $.ajax({
                            type: "post",
                            url: "<?= base_url('customers/renewaccountancy/'); ?>",
                            data: { id: id, amount: amount, user_id: user_id, firm_id: firm_id, date: date },
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === true) {
                                    alert(response.message);
                                    reloadAjax();
                                } else {
                                    alert(response.message || "An error occurred.");
                                }
                            }
                        });
                    }
                });

                $('body').on('click', '.renew-monthly-btn', function() {
                    var btn = $(this);
                    var id = btn.data('id');
                    var amount = parseFloat(btn.data('amount')).toFixed(2);
                    var user_id = btn.data('userid');
                    var firm_id = btn.data('firmid');

                    if (confirm("Are you sure you want to deduct ₹" + amount + " from the customer's wallet for this monthly package?")) {
                        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');
                        $.ajax({
                            type: "post",
                            url: "<?= base_url('customers/renewmonthlypackage/'); ?>",
                            data: { id: id, amount: amount, user_id: user_id, firm_id: firm_id },
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === true) {
                                    alert(response.message);
                                    reloadAjax();
                                } else {
                                    alert(response.message || "An error occurred.");
                                    btn.prop('disabled', false).html('<i class="fe fe-credit-card me-1"></i> Pay');
                                }
                            },
                            error: function() {
                                alert("Server error occurred.");
                                btn.prop('disabled', false).html('<i class="fe fe-credit-card me-1"></i> Pay');
                            }
                        });
                    }
                });
            </script>
