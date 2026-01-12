
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= form_open_multipart('users/adduser/'); ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-sm-12 col-form-label">Username <span class="text-danger">*</span></label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" name="username" id="username" maxlength="60">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-sm-12 col-form-label">Email<span class="text-danger">*</span></label>
                                        <div class="col-sm-12">
                                            <input type="email" class="form-control" name="email" id="email" maxlength="60" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-sm-12 col-form-label">Password <span class="text-danger">*</span></label>
                                        <div class="col-sm-12">
                                            <input type="password" class="form-control" name="password" id="password" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-sm-12 col-form-label">Name <span class="text-danger">*</span></label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" name="name" id="name" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-sm-12 col-form-label">Mobile</label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" name="mobile" id="mobile"   pattern="[0-9]{10}" title="Enter Valid Mobile No." maxlength="10">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-sm-12 col-form-label">Role <span class="text-danger">*</span></label>
                                        <div class="col-sm-12">
                                            <?= form_dropdown('role',$roles,'',array('class'=>'form-control','required'=>"true","id"=>"role")); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-10">
                                    <input type="hidden" name="id" id="id">
                                    <input type="submit" class="btn btn-sm btn-success waves-effect waves-light" name="adduser" value="Save User">
                                    <button type="button" class="btn btn-sm btn-danger waves-effect waves-light cancel-btn d-none">Cancel</button>
                                </div>
                            </div>
                        <?= form_close(); ?>
                    </div>
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="datatable">
                                <thead>
                                    <tr>
                                        <th>Sl.No.</th>
                                        <th>Username</th>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Email</th>
                                        <th>Password</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(!empty($users)){ $i=0;
                                        foreach($users as $user ){
                                    ?>
                                    <tr>
                                        <td><?= ++$i; ?></td>
                                        <td><?= $user['username']; ?></td>
                                        <td><?= $user['name']; ?></td>
                                        <td><?= $user['mobile']; ?></td>
                                        <td><?= $user['email']; ?></td>
                                        <td><?= $user['vp']; ?></td>
                                        <td><?= $user['role_name']; ?></td>
                                        <td>
                                            <?= $user['status']==1?'<span class="text-success">Active</span>':'<span class="text-danger">Blocked</span>' ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info edit-btn" value="<?php echo $user['id']; ?>"><i class="fa fa-edit"></i></button>
                                            <?php if($user['status']==1){ ?>
                                            <a href="<?= base_url('users/blockuser/'.md5($user['username'])); ?>" class="btn btn-sm btn-danger" onClick="return validate('Block');">Block</a>
                                            <?php }else{ ?>
                                            <a href="<?= base_url('users/unblockuser/'.md5($user['username'])); ?>" class="btn btn-sm btn-success" onClick="return validate('Un-Block');">Un-Block</a>
                                            <?php } ?>

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
                    $('body').on('change','#role',function(){
                        if($(this).val()=='agent'){
                            $('#parent_id').closest('.col-md-4').removeClass('d-none');;
                        }
                        else{
                            $('#parent_id').closest('.col-md-4').addClass('d-none');;
                            $('#parent_id').val('');
                        }
                    });
                    $('table').on('click','.edit-btn',function(){
                        $.ajax({
                            type:"post",
                            url:"<?= base_url('users/getuser/'); ?>",
                            data:{id:$(this).val()},
                            success:function(data){
                                data=JSON.parse(data);
                                $('#id').val(data['id']);
                                $('#username').val(data['username']);
                                $('#name').val(data['name']);
                                $('#mobile').val(data['mobile']);
                                $('#email').val(data['email']);
                                $('#role').val(data['role']).trigger('change');
                                $('#parent_id').val(data['parent_id']);
                                $('#password').val('');
                                $('#password').removeAttr('required');

                                $('.cancel-btn').removeClass('d-none');
                                $('input[name="adduser"]').attr('name','updateuser').val('Update User');
                            }
                        });
                    });
                    $('.cancel-btn').click(function(){
                        $('#id,#username,#name,#mobile,#email,#role,#parent_id,#password').val('');
                        $('#role').trigger('change');
                        $('#password').attr('required',true);
                        $('.cancel-btn').addClass('d-none');
                        $('input[name="updateuser"]').attr('name','adduser').val('Save User');
                    });
                });
                
                function validate(text){
                    if(!confirm("Confirm "+text+" this user?")){
                        return false;
                    }
                }
                
            </script>
