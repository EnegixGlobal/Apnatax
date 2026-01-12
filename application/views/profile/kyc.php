<?php
$button='';
if(empty($kyc) || $kyc['status']==0){
    $button='<input type="submit" class="btn btn-sm btn-success" name="updatekyc" value="Update KYC">&nbsp;';
}
?>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?= form_open_multipart('profile/updatekyc/'); ?>
                                                <div class="form-group">
                                                    <?php 
                                                        $attributes=array("Placeholder"=>"Aadhar",'pattern'=>'[0-9]{12}$','title'=>"Enter Valid 12-digit Aadhar No");
                                                        echo create_form_input("text","aadhar","Aadhar",true,$kyc['aadhar']??'',$attributes); 
                                                    ?>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <?php 
                                                                $attributes=array("id"=>"aadhar_image","onChange"=>"getPhoto(this,'aadhar_image')","accept"=>"image/*");
                                                                echo create_form_input("file","aadhar_image","Upload Aadhar Card Front :",true,'',$attributes); 
                                                                $aadhar_image="";
                                                                if(!empty($kyc['aadhar_image'])){
                                                                    $aadhar_image="src='".str_replace('//assets/','/assets/',$kyc['aadhar_image'])."'";
                                                                }
                                                            ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <img <?php echo $aadhar_image; ?> id="aadhar_imagepreview" style="height:150px; width:250px;" >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <?php 
                                                                $attributes=array("id"=>"aadhar_back","onChange"=>"getPhoto(this,'aadhar_back')","accept"=>"image/*");
                                                                echo create_form_input("file","aadhar_back","Upload Aadhar Card Back:",true,'',$attributes); 
                                                                $aadhar_back="";
                                                                if(!empty($kyc['aadhar_back'])){
                                                                    $aadhar_back="src='".str_replace('//assets/','/assets/',$kyc['aadhar_back'])."'";
                                                                }
                                                            ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <img <?php echo $aadhar_back; ?> id="aadhar_backpreview" style="height:150px; width:250px;" >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <?php 
                                                        $attributes=array("Placeholder"=>"PAN",'pattern'=>'^[A-Z]{5}\d{4}[A-Z]$','title'=>"Enter Valid PAN");
                                                        echo create_form_input("text","pan","PAN",true,$kyc['pan']??'',$attributes); 
                                                    ?>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <?php 
                                                                $attributes=array("id"=>"pan_image","onChange"=>"getPhoto(this,'pan_image')","accept"=>"image/*");
                                                                echo create_form_input("file","pan_image","Upload PAN Card :",true,'',$attributes); 
                                                                $pan_image="";
                                                                if(!empty($kyc['pan_image'])){
                                                                    $pan_image="src='".str_replace('//assets/','/assets/',$kyc['pan_image'])."'";
                                                                }
                                                            ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <img <?php echo $pan_image; ?> id="pan_imagepreview" style="height:150px; width:250px;" >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-4">
                                                    <div class="col-md-12">
                                                        <?= $button; ?>
                                                    </div>
                                                </div>
                                            <?= form_close(); ?>
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
                function getPhoto(input,field){
                    var id="#"+field;
                    var preview="#"+field+"preview";
                    $(preview).replaceWith('<img id="'+field+'preview" style="height:150px; width:250px;" >');
                    if (input.files && input.files[0]) {
                        var filename=input.files[0].name;
                        var re = /(?:\.([^.]+))?$/;
                        var ext = re.exec(filename)[1]; 
                        ext=ext.toLowerCase();
                        if(ext=='jpg' || ext=='jpeg' || ext=='png'){
                            var size=input.files[0].size;
                            if(size<=10485760 && size>=10240){
                                var reader = new FileReader();

                                reader.onload = function (e) {
                                    $(preview).attr('src',e.target.result);
                                }
                                reader.readAsDataURL(input.files[0]);
                            }
                            else if(size>=10485760){
                                alert("Image size is greater than 10MB");	
                                document.getElementById(field).value= null;
                            }
                            else if(size<=10240){
                                alert("Image size is less than 10KB");	
                                document.getElementById(field).value= null; 
                            }
                        }
                        else{
                            alert("Select 'jpeg' or 'jpg' or 'png' image file!!");	
                            document.getElementById(field).value= null;
                        }
                    }
                }
            </script>
            </div>