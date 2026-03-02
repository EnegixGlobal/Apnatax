<?php
$button='';
?>
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">KYC Details - <?= !empty($customer['name']) ? $customer['name'] : 'Customer'; ?></h3>
                                    <div class="card-options">
                                        <a href="<?= base_url('customers/'); ?>" class="btn btn-sm btn-danger">
                                            <i class="fa fa-times"></i> Close
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Customer Information Section -->
                                    <div class="mb-4">
                                        <h5 class="mb-3"><i class="fa fa-user me-2"></i>Customer Information</h5>
                                        <div class="row">
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
                                    </div>
                                    
                                    <hr class="my-4">
                                    <!-- KYC Documents Section -->
                                    <div class="mb-4">
                                        <h5 class="mb-3"><i class="fa fa-id-card me-2"></i>KYC Documents</h5>
                                        
                                        <?php if(!empty($all_kyc) && count($all_kyc) > 0){ ?>
                                            <!-- Tabs for different KYC records -->
                                            <ul class="nav nav-tabs mb-3" id="kycTabs" role="tablist">
                                                <?php $first = true; foreach($all_kyc as $key => $kyc_record): ?>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link <?= $first ? 'active' : ''; ?>" 
                                                                id="kyc-tab-<?= $key; ?>" 
                                                                data-bs-toggle="tab" 
                                                                data-bs-target="#kyc-<?= $key; ?>" 
                                                                type="button" 
                                                                role="tab">
                                                            <i class="fa fa-building me-1"></i><?= htmlspecialchars($kyc_record['firm_name']); ?>
                                                        </button>
                                                    </li>
                                                <?php $first = false; endforeach; ?>
                                            </ul>
                                            
                                            <!-- Tab Content -->
                                            <div class="tab-content" id="kycTabContent">
                                                <?php $first = true; foreach($all_kyc as $key => $kyc_record): ?>
                                                    <div class="tab-pane fade <?= $first ? 'show active' : ''; ?>" 
                                                         id="kyc-<?= $key; ?>" 
                                                         role="tabpanel">
                                                        <?php 
                                                            $kyc = $kyc_record; // Use current KYC record for display
                                                            $current_firm_id = !empty($kyc_record['firm_id']) ? $kyc_record['firm_id'] : null;
                                                        ?>
                                                        
                                                        <!-- PAN Card Section -->
                                                        <div class="card mb-3">
                                                            <div class="card-body">
                                                                <h6 class="card-title mb-3"><i class="fa fa-credit-card me-2"></i>PAN Card</h6>
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <?php
                                                                                $pan=!empty($kyc['pan'])?$kyc['pan']: '';
                                                                                $attributes=array("id"=>"pan-".$key,"readonly"=>"true");
                                                                                echo create_form_input("text","pan","PAN Number",true,$pan,$attributes);  
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <div class="form-group">
                                                                            <?php if(!empty($kyc['pan_image'])){ ?>
                                                                                <label>PAN Card Image</label>
                                                                                <div class="mb-2">
                                                                                    <img src="<?= $kyc['pan_image'] ?>" alt="PAN Image" class="img-thumbnail" style="max-height: 200px; max-width: 300px;">
                                                                                </div>
                                                                                <div>
                                                                                    <a href="<?= $kyc['pan_image'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                                                        <i class="fa fa-eye"></i> View
                                                                                    </a>
                                                                                    <a href="<?= base_url('customers/download_kyc_document/'.md5($customer['id']).'/pan_image' . (!empty($current_firm_id) ? '?firm_id='.$current_firm_id : '')) ?>" class="btn btn-sm btn-success">
                                                                                        <i class="fa fa-download"></i> Download
                                                                                    </a>
                                                                                </div>
                                                                            <?php } else { ?>
                                                                                <p class="text-muted"><i class="fa fa-exclamation-circle me-1"></i>PAN Image not uploaded</p>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Aadhar Card Section -->
                                                        <div class="card mb-3">
                                                            <div class="card-body">
                                                                <h6 class="card-title mb-3"><i class="fa fa-id-badge me-2"></i>Aadhar Card</h6>
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <?php
                                                                                $aadhar=!empty($kyc['aadhar'])?$kyc['aadhar']: '';
                                                                                $attributes=array("id"=>"aadhar-".$key,"readonly"=>"true");
                                                                                echo create_form_input("text","aadhar","Aadhar Number (Optional)",false,$aadhar,$attributes);  
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Aadhar Front</label>
                                                                            <?php if(!empty($kyc['aadhar_image'])){ ?>
                                                                                <div class="mb-2">
                                                                                    <img src="<?= $kyc['aadhar_image'] ?>" alt="Aadhar Image" class="img-thumbnail" style="max-height: 200px; max-width: 300px;">
                                                                                </div>
                                                                                <div>
                                                                                    <a href="<?= $kyc['aadhar_image'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                                                        <i class="fa fa-eye"></i> View
                                                                                    </a>
                                                                                    <a href="<?= base_url('customers/download_kyc_document/'.md5($customer['id']).'/aadhar_image' . (!empty($current_firm_id) ? '?firm_id='.$current_firm_id : '')) ?>" class="btn btn-sm btn-success">
                                                                                        <i class="fa fa-download"></i> Download
                                                                                    </a>
                                                                                </div>
                                                                            <?php } else { ?>
                                                                                <p class="text-muted"><i class="fa fa-exclamation-circle me-1"></i>Aadhar Front not uploaded</p>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Aadhar Back</label>
                                                                            <?php if(!empty($kyc['aadhar_back'])){ ?>
                                                                                <div class="mb-2">
                                                                                    <img src="<?= $kyc['aadhar_back'] ?>" alt="Aadhar Back Image" class="img-thumbnail" style="max-height: 200px; max-width: 300px;">
                                                                                </div>
                                                                                <div>
                                                                                    <a href="<?= $kyc['aadhar_back'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                                                        <i class="fa fa-eye"></i> View
                                                                                    </a>
                                                                                    <a href="<?= base_url('customers/download_kyc_document/'.md5($customer['id']).'/aadhar_back' . (!empty($current_firm_id) ? '?firm_id='.$current_firm_id : '')) ?>" class="btn btn-sm btn-success">
                                                                                        <i class="fa fa-download"></i> Download
                                                                                    </a>
                                                                                </div>
                                                                            <?php } else { ?>
                                                                                <p class="text-muted"><i class="fa fa-exclamation-circle me-1"></i>Aadhar Back not uploaded</p>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php $first = false; endforeach; ?>
                                            </div>
                                        <?php } elseif(!empty($kyc)){ ?>
                                            <!-- Single KYC Display (backward compatibility) -->
                                            <!-- PAN Card Section -->
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title mb-3"><i class="fa fa-credit-card me-2"></i>PAN Card</h6>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <?php
                                                                    $pan=!empty($kyc['pan'])?$kyc['pan']: '';
                                                                    $attributes=array("id"=>"pan","readonly"=>"true");
                                                                    echo create_form_input("text","pan","PAN Number",true,$pan,$attributes);  
                                                                ?>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                                <?php if(!empty($kyc['pan_image'])){ ?>
                                                                    <label>PAN Card Image</label>
                                                                    <div class="mb-2">
                                                                        <img src="<?= $kyc['pan_image'] ?>" alt="PAN Image" class="img-thumbnail" style="max-height: 200px; max-width: 300px;">
                                                                    </div>
                                                                    <div>
                                                                        <a href="<?= $kyc['pan_image'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                                            <i class="fa fa-eye"></i> View
                                                                        </a>
                                                                        <a href="<?= base_url('customers/download_kyc_document/'.md5($customer['id']).'/pan_image') ?>" class="btn btn-sm btn-success">
                                                                            <i class="fa fa-download"></i> Download
                                                                        </a>
                                                                    </div>
                                                                <?php } else { ?>
                                                                    <p class="text-muted"><i class="fa fa-exclamation-circle me-1"></i>PAN Image not uploaded</p>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Aadhar Card Section -->
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title mb-3"><i class="fa fa-id-badge me-2"></i>Aadhar Card</h6>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <?php
                                                                    $aadhar=!empty($kyc['aadhar'])?$kyc['aadhar']: '';
                                                                    $attributes=array("id"=>"aadhar","readonly"=>"true");
                                                                    echo create_form_input("text","aadhar","Aadhar Number (Optional)",false,$aadhar,$attributes);  
                                                                ?>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Aadhar Front</label>
                                                                <?php if(!empty($kyc['aadhar_image'])){ ?>
                                                                    <div class="mb-2">
                                                                        <img src="<?= $kyc['aadhar_image'] ?>" alt="Aadhar Image" class="img-thumbnail" style="max-height: 200px; max-width: 300px;">
                                                                    </div>
                                                                    <div>
                                                                        <a href="<?= $kyc['aadhar_image'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                                            <i class="fa fa-eye"></i> View
                                                                        </a>
                                                                        <a href="<?= base_url('customers/download_kyc_document/'.md5($customer['id']).'/aadhar_image') ?>" class="btn btn-sm btn-success">
                                                                            <i class="fa fa-download"></i> Download
                                                                        </a>
                                                                    </div>
                                                                <?php } else { ?>
                                                                    <p class="text-muted"><i class="fa fa-exclamation-circle me-1"></i>Aadhar Front not uploaded</p>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Aadhar Back</label>
                                                                <?php if(!empty($kyc['aadhar_back'])){ ?>
                                                                    <div class="mb-2">
                                                                        <img src="<?= $kyc['aadhar_back'] ?>" alt="Aadhar Back Image" class="img-thumbnail" style="max-height: 200px; max-width: 300px;">
                                                                    </div>
                                                                    <div>
                                                                        <a href="<?= $kyc['aadhar_back'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                                            <i class="fa fa-eye"></i> View
                                                                        </a>
                                                                        <a href="<?= base_url('customers/download_kyc_document/'.md5($customer['id']).'/aadhar_back') ?>" class="btn btn-sm btn-success">
                                                                            <i class="fa fa-download"></i> Download
                                                                        </a>
                                                                    </div>
                                                                <?php } else { ?>
                                                                    <p class="text-muted"><i class="fa fa-exclamation-circle me-1"></i>Aadhar Back not uploaded</p>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="alert alert-warning">
                                                <i class="fa fa-exclamation-triangle me-2"></i><strong>KYC Details Not Uploaded!</strong> Customer has not uploaded their KYC documents yet.
                                            </div>
                                        <?php } ?>
                                    </div>
                                    
                                    <hr class="my-4">
                                    <!-- Certificates Section -->
                                    <div class="mb-4">
                                        <h5 class="mb-3"><i class="fa fa-certificate me-2"></i>Certificates</h5>
                                        <?= form_open_multipart('customers/uploadcertificates/'.md5($customer['id'])); ?>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6 class="card-title mb-3">TDS Certificate</h6>
                                                        <?php if(!empty($kyc) && !empty($kyc['tds_certificate'])){ ?>
                                                            <div class="mb-3">
                                                                <a href="<?= $kyc['tds_certificate'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                                    <i class="fa fa-eye"></i> View
                                                                </a>
                                                                <a href="<?= base_url('customers/download_certificate/'.md5($customer['id']).'/tds_certificate') ?>" class="btn btn-sm btn-success">
                                                                    <i class="fa fa-download"></i> Download
                                                                </a>
                                                                <a href="<?= base_url('customers/delete_certificate/'.md5($customer['id']).'/tds_certificate') ?>" 
                                                                   class="btn btn-sm btn-danger" 
                                                                   onclick="return confirm('Are you sure you want to delete this certificate?');">
                                                                    <i class="fa fa-trash"></i> Delete
                                                                </a>
                                                            </div>
                                                        <?php } else { ?>
                                                            <p class="text-muted mb-2"><i class="fa fa-exclamation-circle me-1"></i>Not uploaded</p>
                                                        <?php } ?>
                                                        <?php 
                                                            $attributes=array("id"=>"tds_certificate","accept"=>"image/*|application/pdf");
                                                            echo create_form_input("file","tds_certificate","Upload TDS Certificate",false,'',$attributes); 
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6 class="card-title mb-3">GST Certificate</h6>
                                                        <?php if(!empty($kyc) && !empty($kyc['gst_certificate'])){ ?>
                                                            <div class="mb-3">
                                                                <a href="<?= $kyc['gst_certificate'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                                    <i class="fa fa-eye"></i> View
                                                                </a>
                                                                <a href="<?= base_url('customers/download_certificate/'.md5($customer['id']).'/gst_certificate') ?>" class="btn btn-sm btn-success">
                                                                    <i class="fa fa-download"></i> Download
                                                                </a>
                                                                <a href="<?= base_url('customers/delete_certificate/'.md5($customer['id']).'/gst_certificate') ?>" 
                                                                   class="btn btn-sm btn-danger" 
                                                                   onclick="return confirm('Are you sure you want to delete this certificate?');">
                                                                    <i class="fa fa-trash"></i> Delete
                                                                </a>
                                                            </div>
                                                        <?php } else { ?>
                                                            <p class="text-muted mb-2"><i class="fa fa-exclamation-circle me-1"></i>Not uploaded</p>
                                                        <?php } ?>
                                                        <?php 
                                                            $attributes=array("id"=>"gst_certificate","accept"=>"image/*|application/pdf");
                                                            echo create_form_input("file","gst_certificate","Upload GST Certificate",false,'',$attributes); 
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6 class="card-title mb-3">Audit Report</h6>
                                                        <?php if(!empty($kyc) && !empty($kyc['audit_report'])){ ?>
                                                            <div class="mb-3">
                                                                <a href="<?= $kyc['audit_report'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                                    <i class="fa fa-eye"></i> View
                                                                </a>
                                                                <a href="<?= base_url('customers/download_certificate/'.md5($customer['id']).'/audit_report') ?>" class="btn btn-sm btn-success">
                                                                    <i class="fa fa-download"></i> Download
                                                                </a>
                                                                <a href="<?= base_url('customers/delete_certificate/'.md5($customer['id']).'/audit_report') ?>" 
                                                                   class="btn btn-sm btn-danger" 
                                                                   onclick="return confirm('Are you sure you want to delete this certificate?');">
                                                                    <i class="fa fa-trash"></i> Delete
                                                                </a>
                                                            </div>
                                                        <?php } else { ?>
                                                            <p class="text-muted mb-2"><i class="fa fa-exclamation-circle me-1"></i>Not uploaded</p>
                                                        <?php } ?>
                                                        <?php 
                                                            $attributes=array("id"=>"audit_report","accept"=>"image/*|application/pdf");
                                                            echo create_form_input("file","audit_report","Upload Audit Report",false,'',$attributes); 
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6 class="card-title mb-3">Income Tax Certificate</h6>
                                                        <?php if(!empty($kyc) && !empty($kyc['income_tax_certificate'])){ ?>
                                                            <div class="mb-3">
                                                                <a href="<?= $kyc['income_tax_certificate'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                                    <i class="fa fa-eye"></i> View
                                                                </a>
                                                                <a href="<?= base_url('customers/download_certificate/'.md5($customer['id']).'/income_tax_certificate') ?>" class="btn btn-sm btn-success">
                                                                    <i class="fa fa-download"></i> Download
                                                                </a>
                                                                <a href="<?= base_url('customers/delete_certificate/'.md5($customer['id']).'/income_tax_certificate') ?>" 
                                                                   class="btn btn-sm btn-danger" 
                                                                   onclick="return confirm('Are you sure you want to delete this certificate?');">
                                                                    <i class="fa fa-trash"></i> Delete
                                                                </a>
                                                            </div>
                                                        <?php } else { ?>
                                                            <p class="text-muted mb-2"><i class="fa fa-exclamation-circle me-1"></i>Not uploaded</p>
                                                        <?php } ?>
                                                        <?php 
                                                            $attributes=array("id"=>"income_tax_certificate","accept"=>"image/*|application/pdf");
                                                            echo create_form_input("file","income_tax_certificate","Upload Income Tax Certificate",false,'',$attributes); 
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-primary" name="uploadcertificates">
                                                    <i class="fa fa-upload me-2"></i>Upload Certificates
                                                </button>
                                            </div>
                                        </div>
                                        <?= form_close(); ?>
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