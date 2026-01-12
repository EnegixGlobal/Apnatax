            
            <div class="card">
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table table-condensed" id="table">
                                    <thead>
                                        <tr>
                                            <th>Sl.No.</th>
                                            <th>Percent</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if(!empty($salarypercents)){ $i=0;
                                            foreach($salarypercents as $single){
                                                $i++;
                                                if($single['status']==0){
                                                    $date=date('d-m-Y',strtotime($single['updated_on']));
                                                }
                                        ?>
                                        <tr>
                                            <td><?= $i; ?></td>
                                            <td><?= $single['percent']; ?></td>
                                            <td>
                                                <?php if($single['status']==1){ ?>
                                                <button type="button" class="btn btn-sm btn-primary edit-btn" value="<?= $single['id'] ?>"><i class="fa fa-edit"></i></button>
                                                <?php 
                                                    }
                                                    else{
                                                        echo 'Valid Upto '.$date;
                                                    }
                                                ?>
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
                        <div class="col-md-6 <?= !empty($salarypercents)?'d-none':''; ?>" id="form-div">
                            <?= form_open_multipart('masterkey/addsalarypercent/'); ?>
                                <div class="form-group row my-2">
                                    <label class="col-sm-2 col-form-label">Percent</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="percent" id="percent" required>
                                    </div>
                                </div>
                                <div class="form-group row my-2">
                                    <label class="col-sm-2 col-form-label"></label>
                                    <div class="col-sm-10">
                                        <input type="hidden" name="id" id="id">
                                        <input type="submit" class="btn btn-success waves-effect waves-light" name="addsalarypercent" value="Save Salary Percent">
                                        <button type="button" class="btn btn-danger waves-effect waves-light cancel-btn hidden">Cancel</button>
                                    </div>
                                </div>
                            <?= form_close(); ?>
                        </div>
                    </div>
                </div>
            <script>
                $(document).ready(function(e) {
                    $('table').on('click','.edit-btn',function(){
                        $('#rate').closest('.form-group').removeClass('d-none');
                        $.ajax({
                            type:"post",
                            url:"<?= base_url('masterkey/getsalarypercent/'); ?>",
                            data:{id:$(this).val()},
                            success:function(data){
                                data=JSON.parse(data);
                                $('#percent').val(data['percent']);
                                $('.cancel-btn').removeClass('hidden');
                                $('input[name="addsalarypercent"]').val('Update Salary Percent');
                                $('#form-div').removeClass('d-none');
                            }
                        });
                    });
                    $('.cancel-btn').click(function(){
                        $('#percent,#id').val('');
                        $('.cancel-btn').addClass('hidden');
                        $('input[name="addsalarypercent"]').val('Save Salary Percent');
                        $('#parent_id option').show();
                        $('#form-div').addClass('d-none');
                    });
                    $('#table').dataTable();
                });
            function getPhoto(input){

            }
            </script>
            </div>