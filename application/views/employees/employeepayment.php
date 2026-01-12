<?php
    $button='<input type="submit" class="btn btn-success waves-effect waves-light btn-sm" name="makepayment" value="Make Employee Payment">';
    $req=true;
?>

            <div class="card">
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= form_open_multipart('employees/makepayment/','onSubmit="return validate();"'); ?>
                                <div class="row my-2">
                                    <div class="col-12">
                                        <?= create_form_input('date','date','Date',true,date('Y-m-d'),['id'=>'date']); ?>
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <div class="col-12">
                                        <?= create_form_input('select','emp_id','Service',true,'',['id'=>'emp_id'],$employees); ?>
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <div class="col-12">
                                        <?= create_form_input('text','','Balance',false,'',['id'=>'balance','readonly'=>'true']); ?>
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <div class="col-12">
                                        <?= create_form_input('text','amount','Pay Amount',true,'',['id'=>'amount']); ?>
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <div class="col-12">
                                        <?= create_form_input('textarea','remarks','Remarks',false,'',['id'=>'remarks','rows'=>3]); ?>
                                    </div>
                                </div>
                                <div class="row my-2" id="btn-row">
                                    <label class="col-12">
                                        <?= $button; ?>
                                    </div>
                                </div>
                            <?= form_close(); ?>
                        </div>
                    </div>
                </div>
            <script>
                $(document).ready(function(e) {
                    $('body').on('change','#emp_id',function(){
                        var emp_id=$(this).val();
                        $.ajax({
                            type:'post',
                            url:'<?= base_url('employees/getemployeebalance'); ?>',
                            data:{emp_id:emp_id},
                            success:function(data){
                                data=JSON.parse(data);
                                $('#balance').val(data['balance']);
                            }
                        });
                    });
                    $('body').on('keyup','#amount',function(){
                        var balance=Number($('#balance').val());
                        balance=isNaN(balance)?0:balance;
                        
                        var amount=Number($('#amount').val());
                        amount=isNaN(amount)?0:amount;
                        
                        if(amount>balance){
                            alert('Pay Amount cannot be Greater than Balance!');
                            $('#amount').val('');
                        }
                    });
                });
                function validate(){
                    var balance=Number($('#balance').val());
                    balance=isNaN(balance)?0:balance;

                    var amount=Number($('#amount').val());
                    amount=isNaN(amount)?0:amount;

                    if(amount>balance){
                        alert('Pay Amount cannot be Greater than Balance!');
                        return false
                    }
                }
            </script>
            </div>