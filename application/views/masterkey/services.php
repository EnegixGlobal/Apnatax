            
            <div class="card">
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?= form_open_multipart('masterkey/addservice/','onSubmit="return validate()"'); ?>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label">Service</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" name="name" id="name" required>
                                    </div>
                                </div>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label">Rate</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" name="rate" id="rate" required>
                                    </div>
                                </div>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label">Auto Debit Date</label>
                                    <div class="col-sm-12">
                                        <input type="date" class="form-control" name="debit_date" id="debit_date">
                                    </div>
                                </div>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label">Service For</label>
                                    <div class="col-sm-12">
                                        <div class="form-group m-0">
                                            <div class="custom-controls-stacked">
                                                <label class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input service_for" name="service_for" value="Individual" required >
                                                    <span class="custom-control-label">Individual</span>
                                                </label>
                                                <label class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input service_for" name="service_for" value="Firm">
                                                    <span class="custom-control-label">Firm</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label">Type</label>
                                    <div class="col-sm-12">
                                        <div class="form-group m-0">
                                            <div class="custom-controls-stacked">
                                                <label class="custom-control custom-checkbox d-none">
                                                    <input type="checkbox" class="custom-control-input type" name="type[]" value="Turnover" >
                                                    <span class="custom-control-label">Turnover</span>
                                                </label>
                                                <label class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input type" name="type[]" value="Yearly" >
                                                    <span class="custom-control-label">Yearly</span>
                                                </label>
                                                <label class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input type" name="type[]" value="Quarterly">
                                                    <span class="custom-control-label">Quarterly</span>
                                                </label>
                                                <label class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input type" name="type[]" value="Monthly">
                                                    <span class="custom-control-label">Monthly</span>
                                                </label>
                                                <label class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input type" name="type[]" value="Once">
                                                    <span class="custom-control-label">Once</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label"></label>
                                    <div class="col-sm-12">
                                        <input type="hidden" name="id" id="id">
                                        <input type="submit" class="btn btn-success waves-effect waves-light" name="addservice" value="Save Service">
                                        <button type="button" class="btn btn-danger waves-effect waves-light cancel-btn hidden">Cancel</button>
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
                                            <th>Service</th>
                                            <th>Rate</th>
                                            <th>Service For</th>
                                            <th>Type</th>
                                            <th>Auto Debit Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if(!empty($services)){ $i=0;
                                            foreach($services as $single){
                                                $i++;
                                        ?>
                                        <tr>
                                            <td><?= $i; ?></td>
                                            <td><?= $single['name']; ?></td>
                                            <td><?= $single['fixed']==1 || !empty($single['rate'])?$single['rate']:'--'; ?></td>
                                            <td><?= $single['service_for']; ?></td>
                                            <td><?= $single['type']; ?></td>
                                            <td><?= !empty($single['debit_date'])?date('d-m-Y',strtotime($single['debit_date'])):'-' ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary edit-btn" value="<?= $single['id'] ?>"><i class="fa fa-edit"></i></button>
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
                    $('body').on('change','.type',function(){
                        if($(this).is(':checked') && ($(this).val()=='Once')){
                           $('.type').not(this).prop('checked',false);
                        }
                        if($(this).is(':checked') && $(this).val()!='Once'){
                           $('.type[value="Once"]').prop('checked',false);
                        }
                    });
                    $('table').on('click','.edit-btn',function(){
                        $('#rate').closest('.form-group').removeClass('d-none');
                        $('.type,.service_for').prop('checked',false);
                        $.ajax({
                            type:"post",
                            url:"<?= base_url('masterkey/getservice/'); ?>",
                            data:{id:$(this).val()},
                            success:function(data){
                                data=JSON.parse(data);
                                $('#name').val(data['name']);
                                $('#rate').val(data['rate']);
                                if(data['fixed']==0){
                                   //$('#rate').closest('.form-group').addClass('d-none');
                                }
                                if(data['id']==1){
                                   $('.type').eq(0).closest('.custom-control').removeClass('d-none');
                                }
                                else{
                                   $('.type').eq(0).closest('.custom-control').addClass('d-none');
                                }
                                for(var i in data['types']){
                                    console.log(data['types'][i])
                                    $('.type[value="'+data['types'][i]+'"]').prop('checked',true);
                                }
                                for(var i in data['services_for']){
                                    console.log(data['services_for'][i])
                                    $('.service_for[value="'+data['services_for'][i]+'"]').prop('checked',true);
                                }
                                $('#id').val(data['id']);
                                $('.cancel-btn').removeClass('hidden');
                                $('input[name="addservice"]').attr('name','updateservice').val('Update Service');
                            }
                        });
                    });
                    $('.cancel-btn').click(function(){
                        $('#name,#rate,#id').val('');
                        $('#rate').closest('.form-group').removeClass('d-none');
                        $('.cancel-btn').addClass('hidden');
                        $('input[name="updateservice"]').attr('name','addservice').val('Save Service');
                        $('#parent_id option').show();
                    });
                    $('#table').dataTable();
                });
            function validate(input){
              const typeCheckboxes = document.querySelectorAll('input[name="type[]"]');
              atLeastOneChecked = false;

              typeCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                  atLeastOneChecked = true;
                }
              });

              if (!atLeastOneChecked) {
                alert("Please select at least one option for 'Type'.");
                return false; // Prevent form submission
              }

              return true; // Allow form submiss

            }
            </script>
            </div>