            
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
                                            <th>Firm Name</th>
                                            <th>GSTIN</th>
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
                                            <td><?= $single['customer_name']; ?></td>
                                            <td><?= $single['mobile']; ?></td>
                                            <td><?= $single['email']; ?></td>
                                            <td><?= $single['name']; ?></td>
                                            <td><?= $single['gstin']; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-success approve" value="<?= md5('firm-id-'.$single['id']) ?>">Approve</button>
                                                <button type="button" class="btn btn-sm btn-danger reject" value="<?= md5('firm-id-'.$single['id']) ?>">Reject</button>
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
                    $('body').on('click','.approve',function(){
                        var id=$(this).val();
                        if(confirm("Confirm Approve Firm Delete Request?")){
                            updaterequest(id,1);
                        }
                    });
                    $('body').on('click','.reject',function(){
                        var id=$(this).val();
                        if(confirm("Confirm Reject Firm Delete Request?")){
                            updaterequest(id,0);
                        }
                    });
                    $('#table').dataTable();
                });
                
                function updaterequest(id,status){
                    $.ajax({
                        type:'post',
                        url:'<?= base_url('customers/updatefirmstatus'); ?>',
                        data:{id:id,status:status},
                        success:function(data){
                            window.location.reload();
                        }
                    });
                }
            function getPhoto(input){

            }
            </script>
            </div>