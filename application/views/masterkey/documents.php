<?php
$service_id='';
if(!empty($service)){
    $service_id=$service['id'];
}
if($form=='add'){
    $button='<input type="submit" class="btn btn-success waves-effect waves-light btn-sm" name="addreqdocuments" value="Save Required Documents">';
    $req=true;
}
elseif($form=='update'){
    $button='<input type="submit" class="btn btn-success waves-effect waves-light btn-sm" name="updatereqdocuments" value="Update Required Documents">&nbsp;';
    $button.='<a href="'.base_url('masterkey/documentlist/').'" class="btn btn-sm btn-danger">Cancel</a>';
    $req=false;
}
?>

            <div class="card">
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <?= form_open_multipart('masterkey/addreqdocuments/'); ?>
                                <div class="row my-2">
                                    <div class="col-12">
                                        <?= create_form_input('select','service_id','Service',true,$service_id,['id'=>'service_id'],$services); ?>
                                    </div>
                                </div>
                                <?php
                                if(!empty($servicedocuments)){
                                    foreach($servicedocuments as $single){
                                ?>
                                <div class="row my-2">
                                    <div class="col-5">
                                        <?= create_form_input('select','document_id[]','Document',true,$single['document_id'],['class'=>'document_id'],$documents); ?>
                                    </div>
                                    <div class="col-5">
                                        <?= create_form_input('input','display_name[]','Field Name',true,$single['display_name'],['class'=>'display_name']); ?>
                                    </div>
                                    <div class="col-2"><br>
                                        <input type="hidden" name="id[]" value="<?= $single['id']; ?>">
                                        <input type="hidden" name="status[]" value="1" class="status">
                                        <button type="button" class="btn btn-sm btn-danger del-btn"><i class="fa fa-trash"></i></button>
                                    </div>
                                </div>
                                <?php
                                    }
                                }
                                ?>
                                <div class="row my-2">
                                    <div class="col-5">
                                        <?= create_form_input('select','document_id[]','Document',$req,'',['class'=>'document_id'],$documents); ?>
                                    </div>
                                    <div class="col-5">
                                        <?= create_form_input('input','display_name[]','Field Name',$req,'',['class'=>'display_name']); ?>
                                    </div>
                                    <div class="col-2"><br>
                                        <button type="button" class="btn btn-sm btn-info add-btn mt-2"><i class="fa fa-plus"></i> Add</button>
                                    </div>
                                </div>
                                <div class="row my-2" id="btn-row">
                                    <label class="col-12">
                                        <?= $button; ?>
                                    </div>
                                </div>
                            <?= form_close(); ?>
                        </div>
                    </div>
                </div>
            <script>
                $(document).ready(function(e) {
                    $('body').on('click','.add-btn',function(){
                        var row=$(this).closest('.row').clone();
                        $(row).find('.add-btn').html('<i class="fa fa-trash"></i> Remove');
                        $(row).find('.add-btn').removeClass('add-btn btn-info').addClass('remove-btn btn-danger');
                        $(row).find('.form-control').val('');
                        $(row).insertBefore(('#btn-row'));
                    });
                    $('body').on('click','.remove-btn',function(){
                        $(this).closest('.row').remove();
                    });
                    $('body').on('change','.document_id',function(){
                        var document_id=$(this).val();
                        var document=$(this).find('option[value="'+document_id+'"]').text();
                        $(this).closest('.row').find('.display_name').val(document);
                    });
                    $('body').on('click','.del-btn',function(){
                        var document_id=$(this).closest('.row').find('.document_id').val();
                        $(this).closest('.row').find('.form-control').attr('readonly',true);
                        $(this).closest('.row').find('.document_id').attr('disabled',true);
                        $(this).closest('.row').find('.document_id').removeAttr('name');
                        $(this).closest('div').append('<input type="text" name="document_id[]" class="temp_document_id" value="'+document_id+'">');
                        $(this).closest('div').find('.status').val(0);
                        $(this).html('<i class="fa fa-undo" ></i>');
                        $(this).removeClass('del-btn').addClass('undo-btn');
                    });
                    $('body').on('click','.undo-btn',function(){
                        $(this).closest('.row').find('.form-control').removeAttr('readonly');
                        $(this).closest('.row').find('.document_id').removeAttr('disabled');
                        $(this).closest('.row').find('.document_id').attr('name','document_id[]');
                        $(this).closest('div').find('.temp_document_id').remove();
                        $(this).closest('div').find('.status').val(1);
                        $(this).html('<i class="fa fa-trash" ></i>');
                        $(this).removeClass('undo-btn').addClass('del-btn');
                    });
                });
            function getPhoto(input){

            }
            </script>
            </div>