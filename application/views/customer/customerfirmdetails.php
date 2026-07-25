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

                var pendingRenewData = {};
                var pendingRenewAction = '';
                var pendingRenewBtn = null;

                $('body').on('click', '.renew-btn, .renew-monthly-btn', function() {
                    var btn = $(this);
                    pendingRenewBtn = btn;
                    var amount = parseFloat(btn.data('amount')).toFixed(2);
                    
                    pendingRenewData = {
                        id: btn.data('id'),
                        amount: amount,
                        user_id: btn.data('userid'),
                        firm_id: btn.data('firmid'),
                        date: btn.data('date') || ''
                    };
                    
                    pendingRenewAction = btn.hasClass('renew-btn') ? 'renewaccountancy' : 'renewmonthlypackage';
                    
                    $('#pm_amount').text(amount);
                    $('#paymentMethodModal').modal('show');
                });

                function processRenewal(payment_method) {
                    $('#paymentMethodModal').modal('hide');
                    var url = "<?= base_url('customers/'); ?>" + pendingRenewAction;
                    pendingRenewData.payment_method = payment_method;
                    
                    if (pendingRenewAction == 'renewmonthlypackage') {
                        pendingRenewBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');
                    }
                    
                    $.ajax({
                        type: "post",
                        url: url,
                        data: pendingRenewData,
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === true) {
                                alert(response.message);
                                reloadAjax();
                            } else {
                                alert(response.message || "An error occurred.");
                                if (pendingRenewAction == 'renewmonthlypackage') {
                                    pendingRenewBtn.prop('disabled', false).html('<i class="fe fe-credit-card me-1"></i> Pay');
                                }
                            }
                        },
                        error: function() {
                            alert("Server error occurred.");
                            if (pendingRenewAction == 'renewmonthlypackage') {
                                pendingRenewBtn.prop('disabled', false).html('<i class="fe fe-credit-card me-1"></i> Pay');
                            }
                        }
                    });
                }

                $('#btn-pay-wallet').click(function() { processRenewal('Wallet'); });
                $('#btn-pay-credit').click(function() { processRenewal('Credit Limit'); });
            </script>

            <!-- Modal for Payment Method -->
            <div class="modal fade" id="paymentMethodModal" tabindex="-1" role="dialog" aria-hidden="true">
              <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Payment Method</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body text-center">
                    <p>Choose a method to pay ₹<span id="pm_amount" style="font-weight:bold;"></span></p>
                    <button type="button" class="btn btn-primary btn-block mb-2" id="btn-pay-wallet" style="width:100%;">Wallet</button>
                    <button type="button" class="btn btn-info btn-block text-white" id="btn-pay-credit" style="width:100%;">Credit Limit</button>
                  </div>
                </div>
              </div>
            </div>
