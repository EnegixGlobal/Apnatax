            
            <div class="card">
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-condensed" id="table">
                                    <thead>
                                        <tr>
                                            <th>Sl.No.</th>
                                            <th>Name</th>
                                            <th>Mobile</th>
                                            <th>E-Mail</th>
                                            <th>State</th>
                                            <th>District</th>
                                            <th>Added By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if(!empty($customers)){ $i=0;
                                            foreach($customers as $single){
                                                $i++;
                                        ?>
                                        <tr>
                                            <td><?= $i; ?></td>
                                            <td><?= $single['name']; ?></td>
                                            <td><?= $single['mobile']; ?></td>
                                            <td><?= $single['email']; ?></td>
                                            <td><?= $single['state']; ?></td>
                                            <td><?= $single['district']; ?></td>
                                            <td><?= empty($single['user_name'])?'-':$single['user_name']; ?></td>
                                            <td>
                                                <a href="<?= base_url('customers/editcustomer/'.md5($single['id'])); ?>" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                                                <a href="<?= base_url('customers/kycdetails/'.md5($single['id'])); ?>" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> View KYC</a>
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
                    $('#name').keyup(function(){
                        //if($('#id').val()==''){
                            var name=$(this).val();
                            $.ajax({
                                type:"POST",
                                url:"<?= base_url('masterkey/getslug/'); ?>",
                                data:{name:name},
                                success: function(data){
                                    $('#slug').val(data);
                                }
                            });
                        //}
                    });
                    $('#table').dataTable();
                });
            function getPhoto(input){

            }
            </script>
            </div>