
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6" id="form-div">
                        <?= form_open_multipart('users/addrole/','onsubmit="return validate();"'); ?>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Role</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="name" id="name" required maxlength="30">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Sections</label>
                                    <div class="col-sm-10">
                                        <div class="custom-controls-stacked">
                                            <label class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" name="sections[]" value="Section 1" >
                                                <span class="custom-control-label">Section 1</span>
                                            </label>
                                            <label class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" name="sections[]" value="Section 2" >
                                                <span class="custom-control-label">Section 2</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label"></label>
                                    <div class="col-sm-10">
                                        <input type="hidden" name="id" id="id">
                                        <input type="submit" class="btn btn-sm btn-success waves-effect waves-light" name="addrole" value="Save Role">
                                        <button type="button" class="btn btn-sm btn-danger waves-effect waves-light cancel-btn d-none">Cancel</button>
                                    </div>
                                </div>
                        <?= form_close(); ?>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="datatable">
                                <thead>
                                    <tr>
                                        <th>Sl.No.</th>
                                        <th>Role</th>
                                        <th>Sections</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(!empty($roles)){ $i=0;
                                        foreach($roles as $role ){
                                    ?>
                                    <tr>
                                        <td><?= ++$i; ?></td>
                                        <td><?= $role['name']; ?></td>
                                        <td><?= $role['sections']; ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info edit-btn p-1" value="<?php echo $role['id']; ?>"><i class="fa fa-edit"></i></button>
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
                            $('table').on('click','.edit-btn',function(){
                                $('#parent option').show();
                                $('input[name="sections[]"]').prop('checked',false);
                                $.ajax({
                                    type:"post",
                                    url:"<?= base_url('users/getrole/'); ?>",
                                    data:{id:$(this).val()},
                                    success:function(data){
                                        data=JSON.parse(data);
                                        $('#name').val(data['name']);
                                        for(i=0;i<data['sections'].length;i++){
                                            $('input[name="sections[]"][value="'+data['sections'][i]+'"]').prop('checked',true);
                                        }

                                        $('#id').val(data['id']);
                                        $('.cancel-btn,#form-div').removeClass('d-none');
                                        $('input[name="addrole"]').attr('name','updaterole').val('Update Role');
                                    }
                                });
                            });
                            $('.cancel-btn').click(function(){
                                $('#name,#id').val('');
                                $('input[name="sections[]"]').prop('checked',false);
                                $('.cancel-btn').addClass('d-none');
                                $('input[name="updaterole"]').attr('name','addrole').val('Save Role');
                            });
                        });

                        function validate(){
                            if($('input[name="sections[]"]:checked').length<1){
                                alert("Please select atleast 1 Section!");
                                return false;
                            }
                        }
                    </script>
