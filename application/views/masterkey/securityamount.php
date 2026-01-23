            
            <div class="card">
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?= form_open('masterkey/addsecuritydeposit/','onSubmit="return validate()"'); ?>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label">Customer <span class="text-danger">*</span></label>
                                    <div class="col-sm-12">
                                        <?= form_dropdown('user_id',$customers,'',array('class'=>'form-control','id'=>'user_id','required'=>'true')); ?>
                                    </div>
                                </div>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label">Amount <span class="text-danger">*</span></label>
                                    <div class="col-sm-12">
                                        <input type="number" step="0.01" class="form-control" name="amount" id="amount" required>
                                    </div>
                                </div>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label">Date <span class="text-danger">*</span></label>
                                    <div class="col-sm-12">
                                        <input type="date" class="form-control" name="date" id="date" value="<?= date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label">Remarks</label>
                                    <div class="col-sm-12">
                                        <textarea class="form-control" name="remarks" id="remarks" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label"></label>
                                    <div class="col-sm-12">
                                        <input type="hidden" name="id" id="id">
                                        <input type="submit" class="btn btn-success waves-effect waves-light" name="addsecuritydeposit" value="Save Security Deposit">
                                        <button type="button" class="btn btn-danger waves-effect waves-light cancel-btn hidden">Cancel</button>
                                    </div>
                                </div>
                            <?= form_close(); ?>
                        </div>
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-condensed" id="table">
                                    <thead>
                                        <tr>
                                            <th>Sl.No.</th>
                                            <th>Customer</th>
                                            <th>Mobile</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Remarks</th>
                                            <th>Added On</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if(!empty($security_deposits)){ $i=0;
                                            foreach($security_deposits as $single){
                                                $i++;
                                        ?>
                                        <tr>
                                            <td><?= $i; ?></td>
                                            <td><?= !empty($single['customer_name'])?$single['customer_name']:'N/A'; ?></td>
                                            <td><?= !empty($single['mobile'])?$single['mobile']:'N/A'; ?></td>
                                            <td><?= number_format($single['amount'],2); ?></td>
                                            <td><?= date('d-m-Y',strtotime($single['date'])); ?></td>
                                            <td><?= !empty($single['remarks'])?$single['remarks']:'-'; ?></td>
                                            <td><?= date('d-m-Y H:i',strtotime($single['added_on'])); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary edit-btn" value="<?= $single['id'] ?>"><i class="fa fa-edit"></i></button>
                                                <button type="button" class="btn btn-sm btn-danger delete-btn" value="<?= $single['id'] ?>" data-amount="<?= $single['amount'] ?>"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <script>
                $(document).ready(function(e) {
                    $('table').on('click','.edit-btn',function(){
                        $.ajax({
                            type:"post",
                            url:"<?= base_url('masterkey/getsecuritydeposit/'); ?>",
                            data:{id:$(this).val()},
                            success:function(data){
                                data=JSON.parse(data);
                                $('#user_id').val(data['user_id']);
                                $('#amount').val(data['amount']);
                                $('#date').val(data['date']);
                                $('#remarks').val(data['remarks']);
                                $('#id').val(data['id']);
                                $('.cancel-btn').removeClass('hidden');
                                $('input[name="addsecuritydeposit"]').attr('name','updatesecuritydeposit').val('Update Security Deposit');
                            }
                        });
                    });
                    $('.cancel-btn').click(function(){
                        $('#user_id,#amount,#date,#remarks,#id').val('');
                        $('#date').val('<?= date('Y-m-d'); ?>');
                        $('.cancel-btn').addClass('hidden');
                        $('input[name="updatesecuritydeposit"]').attr('name','addsecuritydeposit').val('Save Security Deposit');
                    });
                    $('table').on('click','.delete-btn',function(){
                        if(confirm('Are you sure you want to delete security deposit of amount: '+$(this).data('amount')+'?')){
                            window.location.href = "<?= base_url('masterkey/deletesecuritydeposit/'); ?>"+$(this).val();
                        }
                    });
                    $('#table').dataTable();
                });
            function validate(){
                return true;
            }
            </script>
            </div>

