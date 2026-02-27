
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="datatable">
                                                <thead>
                                                    <tr>
                                                        <th>Sl.No.</th>
                                                        <th>Name</th>
                                                        <th>Mobile</th>
                                                        <th>Email</th>
                                                        <th>Service Purchased</th>
                                                        <th>Amount</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if(!empty($orders)){ $i=0;
                                                        foreach($orders as $single ){
                                                            $status='<span class="text-danger">Pending</span>';
                                                            if($single['status']==2){
                                                                $status='<span class="text-warning">Documents Uploaded!</span>';
                                                            }
                                                            elseif($single['status']==3){
                                                                $status='<span class="text-info">Accepted for Assessment!</span>';
                                                            }
                                                            elseif($single['status']==4){
                                                                $status='<span class="text-success">Assessment Done and Report Uploaded!</span>';
                                                            }
                                                            
                                                    ?>
                                                    <tr>
                                                        <td><?= ++$i; ?></td>
                                                        <td><?= $single['name']; ?></td>
                                                        <td><?= $single['mobile']; ?></td>
                                                        <td><?= $single['email']; ?></td>
                                                        <td><?= $single['service_name']; ?></td>
                                                        <td><?= $single['amount']; ?></td>
                                                        <td><?= $status; ?></td>
                                                        <td>
                                                            <?php
                                                                if($single['status']!=0){
                                                            ?>
                                                            <a href="<?= base_url("orders/viewdocuments/".md5($single['id'])); ?>" class="btn btn-sm btn-info mb-1"><i class="fa fa-eye"></i></a>
                                                            <?php
                                                                // Determine main order id used for invoice (parent row if exists)
                                                                $order_id = !empty($single['parent_id']) ? $single['parent_id'] : $single['id'];
                                                            ?>
                                                            <a href="<?= base_url("invoices/viewbyorder/".$order_id); ?>" class="btn btn-sm btn-primary mb-1">
                                                                <i class="fa fa-file-invoice"></i> Invoice
                                                            </a>
                                                            <a href="<?= base_url("invoices/downloadbyorder/".$order_id); ?>" class="btn btn-sm btn-success">
                                                                <i class="fa fa-download"></i>
                                                            </a>
                                                            <?php 
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
                                </div>
            <script>
                $(document).ready(function(e) {
                    $('#datatable').dataTable();
                });
            </script>