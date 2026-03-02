<?php
?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Firm Edit Requests</h3>
                </div>
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
                                            <th>Current Firm Name</th>
                                            <th>Current GSTIN</th>
                                            <th>Proposed Firm Name</th>
                                            <th>Proposed GSTIN</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if(!empty($customers)){ $i=0;
                                            foreach($customers as $single){
                                                $i++;
                                                // Parse edit request data
                                                $edit_data = !empty($single['edit_request_data']) ? json_decode($single['edit_request_data'], true) : array();
                                                $current_name = !empty($edit_data['current_name']) ? $edit_data['current_name'] : $single['name'];
                                                $current_gstin = !empty($edit_data['current_gstin']) ? $edit_data['current_gstin'] : $single['gstin'];
                                                $proposed_name = !empty($edit_data['name']) ? $edit_data['name'] : '';
                                                $proposed_gstin = !empty($edit_data['gstin']) ? $edit_data['gstin'] : '';
                                        ?>
                                        <tr>
                                            <td><?= $i; ?></td>
                                            <td><?= $single['customer_name']; ?></td>
                                            <td><?= $single['mobile']; ?></td>
                                            <td><?= $single['email']; ?></td>
                                            <td><?= htmlspecialchars($current_name); ?></td>
                                            <td><?= htmlspecialchars($current_gstin); ?></td>
                                            <td><strong><?= htmlspecialchars($proposed_name); ?></strong></td>
                                            <td><strong><?= htmlspecialchars($proposed_gstin); ?></strong></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-success approve-edit" value="<?= md5('firm-id-'.$single['id']) ?>">Approve</button>
                                                <button type="button" class="btn btn-sm btn-danger reject-edit" value="<?= md5('firm-id-'.$single['id']) ?>">Reject</button>
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
                    $('body').on('click','.approve-edit',function(){
                        var id=$(this).val();
                        if(confirm("Confirm Approve Firm Edit Request?")){
                            updateeditrequest(id,1);
                        }
                    });
                    $('body').on('click','.reject-edit',function(){
                        var id=$(this).val();
                        if(confirm("Confirm Reject Firm Edit Request?")){
                            updateeditrequest(id,0);
                        }
                    });
                    $('#table').dataTable();
                });
                
                function updateeditrequest(id,status){
                    $.ajax({
                        type:'post',
                        url:'<?= base_url('customers/updatefirmeditstatus'); ?>',
                        data:{id:id,status:status},
                        success:function(data){
                            window.location.reload();
                        }
                    });
                }
            </script>
            </div>

