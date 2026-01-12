            
            <div class="card">
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?= form_open_multipart('firms/addfirm/','onSubmit="return validate()"'); ?>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label">Firm Name</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" name="name" id="name" required>
                                    </div>
                                </div>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label">GSTIN</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" name="gstin" id="gstin">
                                    </div>
                                </div>
                                <div class="form-group row my-2">
                                    <label class="col-sm-12 col-form-label"></label>
                                    <div class="col-sm-12">
                                        <input type="hidden" name="id" id="id">
                                        <input type="submit" class="btn btn-success waves-effect waves-light" name="addfirm" value="Save Firm">
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
                                            <th>Firm</th>
                                            <th>GSTIN</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $user=getuser();
                                        if(!empty($firms)){ $i=0;
                                            foreach($firms as $single){
                                                $i++;
                                        ?>
                                        <tr>
                                            <td><?= $i; ?></td>
                                            <td><?= $single['name']; ?></td>
                                            <td><?= $single['gstin']; ?></td>
                                            <td>
                                                <?php /*?><button type="button" class="btn btn-sm btn-primary edit-btn" value="<?= $single['id'] ?>"><i class="fa fa-edit"></i></button><?php */?>
                                                <?php if(!checkfirmservice($user,$single['id'])){ ?>
                                                <button type="button" class="btn btn-sm btn-danger delete-btn" value="<?= $single['id'] ?>"><i class="fa fa-trash"></i></button>
                                                <?php } ?>
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
                        $.ajax({
                            type:"post",
                            url:"<?= base_url('firms/getfirm/'); ?>",
                            data:{id:$(this).val()},
                            success:function(data){
                                data=JSON.parse(data);
                                $('#name').val(data['name']);
                                $('#gstin').val(data['gstin']);
                                $('#id').val(data['id']);
                                $('.cancel-btn').removeClass('hidden');
                                $('input[name="addfirm"]').attr('name','updatefirm').val('Update Firm');
                            }
                        });
                    });
                    $('table').on('click','.delete-btn',function(){
                        if(confirm("Confirm Firm Delete?")){
                            $.ajax({
                                type:"post",
                                url:"<?= base_url('firms/requestdelete/'); ?>",
                                data:{id:$(this).val()},
                                success:function(data){
                                    window.location.reload();
                                }
                            });
                        }
                    });
                    $('.cancel-btn').click(function(){
                        $('#name,#gstin,#id').val('');
                        $('.cancel-btn').addClass('hidden');
                        $('input[name="updatefirm"]').attr('name','addfirm').val('Save Firm');
                    });
                    $('#table').dataTable();
                });
            function validate(){

              return true; // Allow form submiss

            }
            </script>
            </div>