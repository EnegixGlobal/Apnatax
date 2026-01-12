<?php
$button='';
$status='<span class="text-danger">Pending</span>';
if($order['status']==2){
    $status='<span class="text-warning">Documents Uploaded!</span>';
}
elseif($order['status']==3){
    $status='<span class="text-info">Accepted for Assessment!</span>';
}
elseif($order['status']==4){
    $status='<span class="text-success">Assessment Done and Report Uploaded!</span>';
}
?>
                                <div class="card-body">
                                    <?= form_open_multipart('customers/savecustomer/'); ?>
                                        <div class="row mb-4">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        echo create_form_input('text','',"Service Name",true,$order['service_name'],['readonly'=>'true']); 
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        echo create_form_input('text','',"Date",true,date('d-m-Y',strtotime($order['added_on'])),['readonly'=>'true']); 
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        echo create_form_input('text','',"Firm Name",true,$documents[0]['firm_name'],['readonly'=>'true']); 
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        echo create_form_input('text','name',"Name",true,$order['name'],['readonly'=>'true']); 
                                                    ?>
                                                </div>
                                            </div>
                                            <?php
                                                //print_pre($documents);
                                                if(!empty($documents)){
                                                    $prev='';
                                                    $count=0;
                                                    foreach($documents as $single){
                                                        $value=$single['formvalue'];
                                                        $filetype='';
                                                        if($single['value']==1 && $prev==''){
                                                            $type='text';
                                                            $name=$single['slug'];
                                                            $label=$single['display_name'];
                                                            if(($single['file']==1 || $single['file']==2)){
                                                                $prev='value';
                                                            }
                                                            $count=0;
                                                        }
                                                        elseif(($single['file']==1 || $single['file']==2)){
                                                            $type='file';
                                                            $name=$single['slug'];
                                                            $label=$single['display_name'].' File';
                                                            $prev='file';
                                                            $count++;
                                                            if($count==$single['file']){
                                                                $prev='';
                                                            }
                                                            if(!empty($value)){
                                                                $extension=substr($value, -4);
                                                                $extension=trim($extension,'.');
                                                                $extension=strtolower($extension);
                                                                if($extension=='png' || $extension=='jpg'|| $extension=='jpeg'|| $extension=='webp'){
                                                                    $filetype='image';
                                                                }
                                                                elseif($extension=='pdf'){
                                                                    $filetype='pdf';
                                                                }
                                                                elseif($extension=='csv' || $extension=='xlsx'){
                                                                    $filetype='excel';
                                                                }
                                                            }
                                                        }
                                                        if($single['document_id']==0){
                                                            $label=$single['display_name'];
                                                            $value=$value;
                                                            $type="text";
                                                        }
                                            ?>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php
                                                        if($type=='text'){
                                                            echo create_form_input($type,$name,$label,true,$value,['readonly'=>'true']); 
                                                        }
                                                        else{
                                                    ?>
                                                    <label for=""><?= $label?></label><br>
                                                    <?php if($filetype=='image'){ ?>
                                                    <img src="<?= $value ?>" alt="<?= $label?>">
                                                    <?php 
                                                        }
                                                        else{ 
                                                    ?>
                                                    <a href="<?= $value ?>" target="_blank" download class="btn btn-sm btn-info"><i class="fa fa-download"></i> Download File</a>
                                                    <?php } ?>
                                                    <?php
                                                        }
                                                    ?>
                                                </div>
                                            </div>
                                            <?php
                                                    }
                                                }
                                            ?>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <?= $status ?>
                                                <?php
                                                    if($order['status']==4){
                                                        $extension=substr($assessment['file'], -4);
                                                        $extension=trim($extension,'.');
                                                        if($extension=='png' || $extension=='jpg'|| $extension=='jpeg'){
                                                            $filetype='image';
                                                        }
                                                        elseif($extension=='pdf'){
                                                            $filetype='pdf';
                                                        }
                                                        elseif($extension=='csv' || $extension=='xlsx'){
                                                            $filetype='excel';
                                                        }
                                                    ?>
                                                    <a href="<?= file_url($assessment['file']) ?>" target="_blank" download class="btn btn-sm btn-info"><i class="fa fa-download"></i> Download Assessment File</a>
                                                <?php
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <a href="<?= $order['type']=='Yearly'?base_url('services/purchasedservices/'):base_url('services/monthlyservices/'.$order['service_slug']) ?>" class="btn btn-sm btn-danger">Close</a>
                                            </div>
                                        </div>
                                    <?= form_close(); ?>
                                </div>

<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Assign Employee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php if($this->session->role=='admin' && $order['status']==2){ ?>
                        <?= form_open('orders/assignemployee','onSubmit="" class="form-horizontal" id="assign-form"'); ?>
                            <div class=" row mx-4 mb-4">
                                <?= create_form_input('text','',"Customer Name",true,$order['name'],['readonly'=>'true']);  ?>
                            </div>
                            <div class=" row m-4">
                                <?= create_form_input('text','',"Service Name",true,$order['service_name'],['readonly'=>'true']);  ?>
                            </div>
                            <div class=" row m-4">
                                <label class="col-md-12 form-label">Employees <span class="text-danger">*</span></label>
                                <div class="col-md-12">
                                    <?= form_dropdown('user_id',$employees,'',array('class'=>'form-control radius-0','id'=>'modal-user_id','required'=>'true')); ?>
                                </div>
                            </div>
                            <div class=" row m-4">
                                <div class="col-md-12">
                                    <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-primary" value="save" name="assignemployee">Assign Employee
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        <?= form_close(); ?>
                        <?php } ?>
                        <?= form_open_multipart('orders/uploadassessment','onSubmit="" class="form-horizontal" id="assessment-form"'); ?>
                            <div class=" row mx-4 mb-4">
                                <?= create_form_input('text','',"Customer Name",true,$order['name'],['readonly'=>'true']);  ?>
                            </div>
                            <div class=" row m-4">
                                <?= create_form_input('text','',"Firm Name",true,$documents[0]['firm_name'],['readonly'=>'true']);  ?>
                            </div>
                            <div class=" row m-4">
                                <?= create_form_input('text','',"Service Name",true,$order['service_name'],['readonly'=>'true']);  ?>
                            </div>
                            <div class=" row m-4">
                                <label class="col-md-12 form-label">Select File <span class="text-danger">*</span></label>
                                <div class="col-md-12">
                                    <input type="file" name="file" id="file" required>
                                </div>
                            </div>
                            <div class=" row m-4">
                                <label class="col-md-12 form-label">Remarks</label>
                                <div class="col-md-12">
                                    <textarea name="remarks" id="remarks" rows="3" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class=" row m-4">
                                <div class="col-md-12">
                                    <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-primary" value="save" name="uploadassessment">Upload Assessment
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
            <script>
                $(document).ready(function(e) {
                });
                function getPhoto(input){

                }
            </script>