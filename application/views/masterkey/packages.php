            
            <div class="card">
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?= form_open_multipart('masterkey/addpackage/','onSubmit="return validate()"'); ?>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label">Package Name</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" name="name" id="name" required>
                                    </div>
                                </div>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label">Turnover</label>
                                    <div class="col-sm-12">
                                        <input type="number" class="form-control" name="turnover" id="turnover" required>
                                    </div>
                                </div>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label">Remarks</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" name="remarks" id="remarks" required>
                                    </div>
                                </div>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label">Rate</label>
                                    <div class="col-sm-12">
                                        <input type="number" class="form-control" name="rate" id="rate" required>
                                    </div>
                                </div>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label">Status</label>
                                    <div class="col-sm-12">
                                        <div class="form-group m-0">
                                            <div class="custom-controls-stacked">
                                                <label class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" name="status" value="1" checked>
                                                    <span class="custom-control-label">Active</span>
                                                </label>
                                                <label class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" name="status" value="0">
                                                    <span class="custom-control-label">Inactive</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label"></label>
                                    <div class="col-sm-12">
                                        <input type="hidden" name="id" id="id">
                                        <input type="submit" class="btn btn-success waves-effect waves-light" name="addpackage" value="Save Package">
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
                                            <th>Name</th>
                                            <th>Turnover</th>
                                            <th>Remarks</th>
                                            <th>Rate</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if(!empty($packages)){ $i=0;
                                            foreach($packages as $single){
                                                $i++;
                                        ?>
                                        <tr>
                                            <td><?= $i; ?></td>
                                            <td><?= $single['name']; ?></td>
                                            <td><?= number_format($single['turnover']); ?></td>
                                            <td><?= $single['remarks']; ?></td>
                                            <td><?= number_format($single['rate']); ?></td>
                                            <td>
                                                <?php if($single['status']==1){ ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php } else { ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary edit-btn" value="<?= $single['id'] ?>"><i class="fa fa-edit"></i></button>
                                                <button type="button" class="btn btn-sm btn-danger delete-btn" value="<?= $single['id'] ?>" data-name="<?= $single['name'] ?>"><i class="fa fa-trash"></i></button>
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
                            url:"<?= base_url('masterkey/getpackage/'); ?>",
                            data:{id:$(this).val()},
                            success:function(data){
                                data=JSON.parse(data);
                                $('#name').val(data['name']);
                                $('#turnover').val(data['turnover']);
                                $('#remarks').val(data['remarks']);
                                $('#rate').val(data['rate']);
                                $('input[name="status"][value="'+data['status']+'"]').prop('checked',true);
                                $('#id').val(data['id']);
                                $('.cancel-btn').removeClass('hidden');
                                $('input[name="addpackage"]').attr('name','updatepackage').val('Update Package');
                            }
                        });
                    });
                    $('.cancel-btn').click(function(){
                        $('#name,#turnover,#remarks,#rate,#id').val('');
                        $('input[name="status"][value="1"]').prop('checked',true);
                        $('.cancel-btn').addClass('hidden');
                        $('input[name="updatepackage"]').attr('name','addpackage').val('Save Package');
                    });
                    $('table').on('click','.delete-btn',function(){
                        if(confirm('Are you sure you want to delete package: '+$(this).data('name')+'?')){
                            window.location.href = "<?= base_url('masterkey/deletepackage/'); ?>"+$(this).val();
                        }
                    });
                    $('#table').dataTable();
                });
            function validate(){
                return true;
            }
            </script>
            </div>

