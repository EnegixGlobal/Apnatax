<?php
    if(!empty($package)){
        $name=$package['package_id']==1?'Accountancy Prime':'Accountancy Premium';
        $packages=$this->master->getpackages(['name'=>$name]);
    }
?>
            <div class="card">
                
                <div class="card-body">
                    <?php
                    if(!empty($package)){
                    ?>
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="lead"><?= $name ?></h3>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="package-table">
                                    <thead>
                                        <tr>
                                            <th>Turnover</th>
                                            <th>Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            if(!empty($packages)){
                                                foreach($packages as $package){
                                        ?>
                                        <tr class="<?= generate_slug($package['name']); ?> package">
                                            <td><?= $package['remarks']; ?></td>
                                            <td><?= $package['rate']; ?></td>
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
                    <?php
                    }
                    ?>
                    <div class="row">
                        <div class="col-12">
                            <?= form_open('package/savepackage'); ?>
                            <table class="table table-bordered" id="service-table">
                                <thead>
                                    <tr>
                                        <th>Service</th>
                                        <?php /*?><th>Type</th><?php */?>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $required=true;
                                    if(!empty($service_package)){
                                        $service_ids=explode(',',$service_package['service_ids']);
                                        foreach($service_ids as $service_id){
                                    ?>
                                    <tr>
                                        <td>
                                            <?= create_form_input('select','service_id[]','',$required,$service_id,['class'=>'service_id'],service_dropdown(['id>'=>1])); ?>
                                        </td>
                                        <?php /*?><td>
                                            <?= create_form_input('select','type[]','',true,'',['class'=>'type'],array(''=>'Select Type')); ?>
                                        </td><?php */?>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger del-btn d-none"><i class="fa fa-trash"></i> Delete</button>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                        $required=false;
                                    }
                                    else{
                                    ?>
                                    <tr>
                                        <td>
                                            <?= create_form_input('select','service_id[]','',$required,'',['class'=>'service_id'],service_dropdown(['id>'=>1])); ?>
                                        </td>
                                        <?php /*?><td>
                                            <?= create_form_input('select','type[]','',true,'',['class'=>'type'],array(''=>'Select Type')); ?>
                                        </td><?php */?>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info add-btn"><i class="fa fa-plus"></i> Add</button>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <?php
                            if(empty($service_package)){
                            ?>
                            <button type="submit" name="savepackage" class="btn btn-sm btn-success">Save Package</button>
                            <?php
                            }
                            ?>
                            <?= form_close(); ?>
                        </div>
                    </div>
                </div>
                
            <script>
                $(document).ready(function(e) {
                    $('body').on('click','.add-btn',function(){
                        var row=$(this).closest('tr').clone();
                        $(row).find('.add-btn').html('<i class="fa fa-trash"></i> Remove');
                        $(row).find('.add-btn').removeClass('add-btn btn-info').addClass('remove-btn btn-danger');
                        $(row).find('.form-control').val('');
                        $('#service-table tbody').append(row);
                    });
                    $('body').on('click','.remove-btn',function(){
                        $(this).closest('tr').remove();
                    });
                    $('body').on('click','.del-btn',function(){
                        var service_id=$(this).closest('tr').find('.service_id').val();
                        $(this).closest('tr').find('.form-control').attr('readonly',true);
                        $(this).closest('tr').find('.service_id').attr('disabled',true);
                        $(this).closest('tr').find('.service_id').removeAttr('name');
                        $(this).closest('div').append('<input type="hidden" name="service_id[]" class="temp_service_id" value="'+service_id+'">');
                        $(this).closest('div').find('.status').val(0);
                        $(this).html('<i class="fa fa-undo" ></i> Undo');
                        $(this).removeClass('del-btn').addClass('undo-btn');
                    });
                    $('body').on('click','.undo-btn',function(){
                        $(this).closest('tr').find('.form-control').removeAttr('readonly');
                        $(this).closest('tr').find('.service_id').removeAttr('disabled');
                        $(this).closest('tr').find('.service_id').attr('name','service_id[]');
                        $(this).closest('div').find('.temp_service_id').remove();
                        $(this).closest('div').find('.status').val(1);
                        $(this).html('<i class="fa fa-trash" ></i> Delete');
                        $(this).removeClass('undo-btn').addClass('del-btn');
                    });
                });
            </script>
            </div>