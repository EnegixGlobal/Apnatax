<?php
    if($form=='add'){
        $button='<input type="submit" class="btn btn-sm btn-success" name="saveemployee" value="Save Employee">&nbsp;';
    }
    elseif($form=='update'){
        $button='<input type="submit" class="btn btn-sm btn-success" name="updateemployee" value="Update Employee">&nbsp;';
        $button.='<a href="'.base_url('employees/').'" class="btn btn-sm btn-danger">Cancel</a>';
    }
?>

                                <div class="card">
                                    <div class="card-body">
                                        <?= form_open_multipart('employees/saveemployee/'); ?>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <?php 
                                                            $name=empty($employee['name'])?'':$employee['name'];
                                                            echo create_form_input('text','name',"Employee Name",true,$name,['id'=>'name']); 
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <?php 
                                                            $mobile=empty($employee['mobile'])?'':$employee['mobile'];
                                                            echo create_form_input('text','mobile',"Mobile",true,$mobile,['id'=>'mobile',"pattern"=>"[0-9]{10}", "title"=>"Enter Valid 10-Digit Mobile No.", "maxlength"=>"10"]); 
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <?php 
                                                            $email=empty($employee['email'])?'':$employee['email'];
                                                            echo create_form_input('email','email',"Email",true,$email,['id'=>'email']); 
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <?php 
                                                            $dob=empty($employee['dob'])?'':$employee['dob'];
                                                            echo create_form_input('date','dob',"Date of Birth",true,$dob,['id'=>'dob']); 
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <?php 
                                                            $address=empty($employee['address'])?'':$employee['address'];
                                                            echo create_form_input('textarea','address',"Employee Address",true,$address,['id'=>'address','rows'=>3]); 
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <?php
                                                            $parent_id=!empty($employee['parent_id'])?$employee['parent_id']:'';
                                                            $state=!empty($employee['state'])?$employee['state']:''; 
                                                            echo create_form_input('select','parent_id',"State",true,$parent_id,['id'=>'parent_id'],$states); 
                                                            echo create_form_input('hidden','state',"",true,$state,['id'=>'state']); 
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <?php
                                                            $area_id=!empty($employee['area_id'])?$employee['area_id']:'';
                                                            $district=!empty($employee['district'])?$employee['district']:'';
                                                            echo create_form_input('select','area_id',"District",true,$area_id,['id'=>'area_id'],$districts); 
                                                            echo create_form_input('hidden','district',"",true,$district,['id'=>'district']); 
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <?php 
                                                            $pan=empty($employee['pan'])?'':$employee['pan'];
                                                            echo create_form_input('text','pan',"PAN No",true,$pan,['id'=>'pan', "title"=>"Enter Valid PAN No.", "maxlength"=>"10","pattern"=>"^[A-Z]{5}\d{4}[A-Z]$"]); 
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <?php 
                                                            $aadhar=empty($employee['aadhar'])?'':$employee['aadhar'];
                                                            echo create_form_input('text','aadhar',"Aadhar No",true,$aadhar,['id'=>'aadhar', "title"=>"Enter Valid Aadhar No.", "maxlength"=>"12","pattern"=>"[0-9]{12}"]); 
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <?php 
                                                            $percent=empty($employee['percent'])?$salarypercent['percent']??0:$employee['percent'];
                                                            echo create_form_input('text','percent',"Salary Percent",true,$percent,['id'=>'percent']); 
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php /*?><div class="row">
                                                <div class="lead">Account Details</div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="account_no" class="form-label">Account Number</label>
                                                        <input type="text" class="form-control" name="account_no" id="account_no"  placeholder="Account Number">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="ifsc" class="form-label">IFSC Code</label>
                                                        <input type="text" class="form-control" name="ifsc" id="ifsc"  placeholder="IFSC Code">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="bank" class="form-label">Bank Name</label>
                                                        <input type="text" class="form-control" name="bank" id="bank"  placeholder="Bank Name">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="bank_branch" class="form-label">Branch Name</label>
                                                        <input type="text" class="form-control" name="bank_branch" id="bank_branch"  placeholder="Branch Name">
                                                    </div>
                                                </div>
                                            </div><?php */?>
                                            <?php
                                                if($form=='add'){
                                            ?>
                                            <div class="row hidden">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <?= create_form_input('checkbox','',"Create Employee Login",true,'',['id'=>'create_user','checked'=>'checked']); 
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <h4>Login Details</h4>
                                                </div>
                                            </div>
                                            <div class="row d-none" id="user-div">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <?= create_form_input('text','username',"Username",true,'',['id'=>'username']); 
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <?= create_form_input('password','password',"Password",true,$aadhar,['id'=>'password']); 
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                       <label for="role" class="form-label">Role</label>
                                                        <?= form_dropdown('role',$roles,'',array('class'=>'form-control','id'=>'role')); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                                }
                                            ?>
                                            <div class="row">
                                                <div class="col-12">
                                                        <?php 
                                                            $id=empty($employee['id'])?'':$employee['id'];
                                                            echo create_form_input('hidden','id',"id",false,$id,['id'=>'id']); 
                                                        ?>
                                                    <?= $button; ?>
                                                </div>
                                            </div>
                                        <?= form_close(); ?>
                                    </div>
                                </div>

            <script>
                $(document).ready(function(){
                    $('body').on('change','#parent_id',function(){
                        var parent_id=$(this).val();
                        var area_id=$('#area_id').data('value');
                        var state=$(this).find('option:selected').text();
                        $('#state').val(state);
                        $.ajax({
                            type:"post",
                            url:"<?= base_url('masterkey/getdistricts/'); ?>",
                            data:{parent_id:parent_id,area_id:area_id},
                            success:function(data){
                                $('#area_id').replaceWith(data);
                                if($('#area_id').val()=='')
                                    $('#district').val('');
                                //setarea_id();
                            }
                        });
                    });
                    $('form').on('change','#area_id',function(){
                        var district=$(this).find('option:selected').text();
                        $('#district').val(district);
                    });
                    $('form').on('change','#create_user',function(){
                        $('#user-div .form-control').val('');
                        $('#user-div .form-control').removeAttr('required');
                        if($(this).is(':checked')){
                            $('#user-div .form-control').attr('required',true);
                            $('#user-div').removeClass('d-none');   
                        }
                        else{
                            $('#user-div').addClass('d-none');   
                        }
                    });
                    $('#create_user').trigger('change');
                });
            </script>