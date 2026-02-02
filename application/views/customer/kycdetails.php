<?php
$button='';
?>
                            <div class="card">
                                <div class="card-body">
                                        <div class="row mb-4">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        $name=!empty($customer['name'])?$customer['name']:'';
                                                        $attributes=array("id"=>"name","Placeholder"=>"Customer Name","autocomplete"=>"off","readonly"=>"true");
                                                        echo create_form_input("text","name","Customer Name",true,$name,$attributes); 
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        $mobile=!empty($customer['mobile'])?$customer['mobile']:'';
                                                        $attributes=array("id"=>"mobile","Placeholder"=>"Mobile","autocomplete"=>"off","pattern"=>"[0-9]{10}","title"=>"Enter Valid Mobile No.","maxlength"=>"10","readonly"=>"true");
                                                        echo create_form_input("text","mobile","Mobile",true,$mobile,$attributes); 
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        $email=!empty($customer['email'])?$customer['email']:'';
                                                        $attributes=array("id"=>"email","Placeholder"=>"Email","autocomplete"=>"off","readonly"=>"true");
                                                        echo create_form_input("email","email","Email",false,$email,$attributes); 
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if(!empty($kyc)){ ?>
                                        <div class="row mb-4">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        $pan=!empty($kyc['pan'])?$kyc['pan']:'';
                                                        $attributes=array("id"=>"pan","readonly"=>"true");
                                                        echo create_form_input("text","pan","PAN",true,$pan,$attributes);  
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <img src="<?= $kyc['pan_image'] ?>" alt="PAN Image">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        $aadhar=!empty($kyc['aadhar'])?$kyc['aadhar']:'';
                                                        $attributes=array("id"=>"aadhar","readonly"=>"true");
                                                        echo create_form_input("text","aadhar","Aadhar",true,$aadhar,$attributes);  
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <img src="<?= $kyc['aadhar_image'] ?>" alt="Aadhar Image">
                                                </div>
                                            </div>
                                        </div>
                                        <?php 
                                            }
                                        ?>
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <h4>Certificates</h4>
                                            </div>
                                        </div>
                                        <?= form_open_multipart('customers/uploadcertificates/'.md5($customer['id'])); ?>
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>TDS Certificate</label>
                                                    <?php if(!empty($kyc) && !empty($kyc['tds_certificate'])){ ?>
                                                        <div class="mb-2">
                                                            <a href="<?= $kyc['tds_certificate'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                                <i class="fa fa-eye"></i> View TDS Certificate
                                                            </a>
                                                            <a href="<?= base_url('customers/delete_certificate/'.md5($customer['id']).'/tds_certificate') ?>" 
                                                               class="btn btn-sm btn-danger" 
                                                               onclick="return confirm('Are you sure you want to delete this certificate?');">
                                                                <i class="fa fa-trash"></i> Delete TDS Certificate
                                                            </a>
                                                        </div>
                                                    <?php } ?>
                                                    <?php 
                                                        $attributes=array("id"=>"tds_certificate","accept"=>"image/*|application/pdf");
                                                        echo create_form_input("file","tds_certificate","Upload TDS Certificate",false,'',$attributes); 
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>GST Certificate</label>
                                                    <?php if(!empty($kyc) && !empty($kyc['gst_certificate'])){ ?>
                                                        <div class="mb-2">
                                                            <a href="<?= $kyc['gst_certificate'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                                <i class="fa fa-eye"></i> View GST Certificate
                                                            </a>
                                                            <a href="<?= base_url('customers/delete_certificate/'.md5($customer['id']).'/gst_certificate') ?>" 
                                                               class="btn btn-sm btn-danger" 
                                                               onclick="return confirm('Are you sure you want to delete this certificate?');">
                                                                <i class="fa fa-trash"></i> Delete GST Certificate
                                                            </a>
                                                        </div>
                                                    <?php } ?>
                                                    <?php 
                                                        $attributes=array("id"=>"gst_certificate","accept"=>"image/*|application/pdf");
                                                        echo create_form_input("file","gst_certificate","Upload GST Certificate",false,'',$attributes); 
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Audit Report</label>
                                                    <?php if(!empty($kyc) && !empty($kyc['audit_report'])){ ?>
                                                        <div class="mb-2">
                                                            <a href="<?= $kyc['audit_report'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                                <i class="fa fa-eye"></i> View Audit Report
                                                            </a>
                                                            <a href="<?= base_url('customers/delete_certificate/'.md5($customer['id']).'/audit_report') ?>" 
                                                               class="btn btn-sm btn-danger" 
                                                               onclick="return confirm('Are you sure you want to delete this certificate?');">
                                                                <i class="fa fa-trash"></i> Delete Audit Report
                                                            </a>
                                                        </div>
                                                    <?php } ?>
                                                    <?php 
                                                        $attributes=array("id"=>"audit_report","accept"=>"image/*|application/pdf");
                                                        echo create_form_input("file","audit_report","Upload Audit Report",false,'',$attributes); 
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Income Tax Certificate</label>
                                                    <?php if(!empty($kyc) && !empty($kyc['income_tax_certificate'])){ ?>
                                                        <div class="mb-2">
                                                            <a href="<?= $kyc['income_tax_certificate'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                                <i class="fa fa-eye"></i> View Income Tax Certificate
                                                            </a>
                                                            <a href="<?= base_url('customers/delete_certificate/'.md5($customer['id']).'/income_tax_certificate') ?>" 
                                                               class="btn btn-sm btn-danger" 
                                                               onclick="return confirm('Are you sure you want to delete this certificate?');">
                                                                <i class="fa fa-trash"></i> Delete Income Tax Certificate
                                                            </a>
                                                        </div>
                                                    <?php } ?>
                                                    <?php 
                                                        $attributes=array("id"=>"income_tax_certificate","accept"=>"image/*|application/pdf");
                                                        echo create_form_input("file","income_tax_certificate","Upload Income Tax Certificate",false,'',$attributes); 
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-sm btn-success" name="uploadcertificates">
                                                    <i class="fa fa-upload"></i> Upload Certificates
                                                </button>
                                            </div>
                                        </div>
                                        <?= form_close(); ?>
                                        <?php if(empty($kyc)){ ?>
                                        <div class="row">
                                            <div class="col-12">
                                                <h3 class="text-danger">KYC Details Not Uploaded!</h3>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="row">
                                            <div class="col-12">
                                                <a href="<?= base_url('customers/'); ?>" class="btn btn-sm btn-danger">Close</a>
                                            </div>
                                        </div>
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