            
            <div class="card">
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?= form_open_multipart('firms/addfirm/','onSubmit="return validate()"'); ?>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label">Firm Name</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" name="name" id="name" required>
                                    </div>
                                </div>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label">GSTIN</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" name="gstin" id="gstin">
                                    </div>
                                </div>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label"></label>
                                    <div class="col-sm-12">
                                        <input type="hidden" name="id" id="id">
                                        <div class="d-flex gap-2">
                                            <input type="submit" class="btn btn-success waves-effect waves-light" name="addfirm" value="Save Firm" id="save-btn">
                                            <button type="button" class="btn btn-danger waves-effect waves-light cancel-btn hidden">Cancel</button>
                                        </div>
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
                                            <th>Firm</th>
                                            <th>GSTIN</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $user=getuser();
                                        if(!empty($firms)){ $i=0;
                                            foreach($firms as $single){
                                                $i++;
                                        ?>
                                        <tr>
                                            <td><?= $i; ?></td>
                                            <td><?= $single['name']; ?></td>
                                            <td><?= $single['gstin']; ?></td>
                                            <td>
                                                <div class="d-flex gap-1 align-items-center">
                                                    <?php if($single['edit_request']==0 || $single['edit_request']==2){ ?>
                                                        <button type="button" class="btn btn-sm btn-primary edit-btn" value="<?= $single['id'] ?>" data-name="<?= htmlspecialchars($single['name']) ?>" data-gstin="<?= htmlspecialchars($single['gstin']) ?>">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                    <?php } else { ?>
                                                        <span class="badge bg-warning">Edit Request Pending</span>
                                                    <?php } ?>
                                                    <?php if(!checkfirmservice($user,$single['id'])){ ?>
                                                        <button type="button" class="btn btn-sm btn-danger delete-btn" value="<?= $single['id'] ?>">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                    <?php } ?>
                                                </div>
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
                    var editMode = false;
                    
                    $('table').on('click','.edit-btn',function(){
                        var firm_id=$(this).val();
                        var firm_name=$(this).data('name');
                        var firm_gstin=$(this).data('gstin');
                        
                        // Populate form with current values
                        $('#name').val(firm_name);
                        $('#gstin').val(firm_gstin);
                        $('#id').val(firm_id);
                        $('.cancel-btn').removeClass('hidden');
                        editMode = true;
                        
                        // Hide add button and show edit request button
                        $('#save-btn').hide();
                        $('.edit-request-btn').remove();
                        $('.form-group:last .d-flex').append('<button type="button" class="btn btn-warning waves-effect waves-light edit-request-btn">Request Edit</button>');
                    });
                    
                    // Handle edit request submission
                    $('body').on('click','.edit-request-btn',function(){
                        var name=$('#name').val();
                        var gstin=$('#gstin').val();
                        var id=$('#id').val();
                        
                        if(!name){
                            alert('Firm Name is required!');
                            return;
                        }
                        
                        if(confirm("Do you want to request edit for this firm? Admin will review and approve your changes.")){
                            $.ajax({
                                type:"post",
                                url:"<?= base_url('firms/requestedit/'); ?>",
                                data:{id:id,name:name,gstin:gstin},
                                success:function(data){
                                    window.location.reload();
                                }
                            });
                        }
                    });
                    
                    $('table').on('click','.delete-btn',function(){
                        if(confirm("Confirm Firm Delete?")){
                            $.ajax({
                                type:"post",
                                url:"<?= base_url('firms/requestdelete/'); ?>",
                                data:{id:$(this).val()},
                                success:function(data){
                                    window.location.reload();
                                }
                            });
                        }
                    });
                    
                    $('.cancel-btn').click(function(){
                        $('#name,#gstin,#id').val('');
                        $('.cancel-btn').addClass('hidden');
                        $('.edit-request-btn').remove();
                        $('#save-btn').show().val('Save Firm');
                        editMode = false;
                    });
                    
                    $('#table').dataTable();
                });
            function validate(){
              return true; // Allow form submission
            }
            </script>
            </div>