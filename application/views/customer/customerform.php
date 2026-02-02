<?php
    if($form=='add'){
        $button='<input type="submit" class="btn btn-sm btn-success" name="savecustomer" value="Save Customer">&nbsp;';
        //$button.='<a href="'.admin_url('biography/celebrities/').'" class="btn btn-sm btn-danger">Cancel</a>';
    }
    elseif($form=='update'){
        $button='<input type="submit" class="btn btn-sm btn-success" name="updatecustomer" value="Update Customer">&nbsp;';
        $button.='<a href="'.base_url('customers/').'" class="btn btn-sm btn-danger">Cancel</a>';
    }
?>
                            <div class="card">
                                <div class="card-body">
                                    <?= form_open_multipart('customers/savecustomer/'); ?>
                                        <div class="row mb-4">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        $name=!empty($customer['name'])?$customer['name']:'';
                                                        $attributes=array("id"=>"name","Placeholder"=>"Customer Name","autocomplete"=>"off");
                                                        echo create_form_input("text","name","Customer Name",true,$name,$attributes); 
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        $mobile=!empty($customer['mobile'])?$customer['mobile']:'';
                                                        $attributes=array("id"=>"mobile","Placeholder"=>"Mobile","autocomplete"=>"off","pattern"=>"[0-9]{10}","title"=>"Enter Valid Mobile No.","maxlength"=>"10");
                                                        echo create_form_input("text","mobile","Mobile",true,$mobile,$attributes); 
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        $email=!empty($customer['email'])?$customer['email']:'';
                                                        $attributes=array("id"=>"email","Placeholder"=>"Email","autocomplete"=>"off");
                                                        echo create_form_input("email","email","Email",false,$email,$attributes); 
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        $address=!empty($customer['address'])?$customer['address']:'';
                                                        $attributes=array("id"=>"address","Placeholder"=>"Address",
                                                                          "autocomplete"=>"off",'rows'=>3);
                                                        echo create_form_input("textarea","address","Address",true,$address,$attributes);  
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        $parent_id=!empty($customer['parent_id'])?$customer['parent_id']:'';
                                                        $state=!empty($customer['state'])?$customer['state']:''; 
                                                        echo create_form_input('select','parent_id',"State",true,$parent_id,['id'=>'parent_id'],$states); 
                                                        echo create_form_input('hidden','state',"",true,$state,['id'=>'state']); 
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        $area_id=!empty($customer['area_id'])?$customer['area_id']:'';
                                                        $district=!empty($customer['district'])?$customer['district']:'';
                                                        echo create_form_input('select','area_id',"District",true,$area_id,['id'=>'area_id'],$districts); 
                                                        echo create_form_input('hidden','district',"",true,$district,['id'=>'district']); 
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        $pincode=!empty($customer['pincode'])?$customer['pincode']:'';
                                                        $attributes=array("id"=>"pincode","Placeholder"=>"Pincode","autocomplete"=>"off","maxlength"=>"6");
                                                        echo create_form_input("text","pincode","Pincode",true,$pincode,$attributes); 
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label">GST (18%) <span class="text-danger">*</span></label>
                                                    <div class="form-check form-switch">
                                                        <?php
                                                            $gst_enabled=!empty($customer['gst_enabled']) && $customer['gst_enabled']==1;
                                                        ?>
                                                        <input class="form-check-input" type="checkbox" name="gst_enabled" id="gst_enabled" value="1" <?= $gst_enabled?'checked':''; ?>>
                                                        <label class="form-check-label" for="gst_enabled">
                                                            Enable 18% GST for this customer
                                                        </label>
                                                    </div>
                                                    <small class="text-muted">When enabled, 18% GST will be added to all service purchases</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <?php
                                                    $id=!empty($customer['id'])?$customer['id']:'';
                                                ?>
                                                <input type="hidden" name="id" id="id" value="<?= $id; ?>">
                                                <?= $button; ?>
                                            </div>
                                        </div>
                                    <?= form_close(); ?>
                                </div>

                    </div>
            <script>
                $(document).ready(function(e) {
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
                    $('form').on('change','#same',function(){
                        if($(this).is(':checked')){
                            $('#shipping_address').val($('#address').val());
                        }
                    });
                    $('form').on('keyup','#opening_balance',function(){
                        var balance=Number($(this).val());
                        if(balance>0){
                            $('.radio-options').removeClass('d-none');
                            $('#opening_date').attr('required',true);
                        }
                        else{
                            $('.radio-options').addClass('d-none');
                            $('#opening_date').removeAttr('required');
                        }
                    });
                });
            function getPhoto(input){

            }
            </script>
            </div>