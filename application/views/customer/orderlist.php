
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="datatable">
                                                <thead>
                                                    <tr>
                                                        <th>Sl.No.</th>
                                                        <th>Name</th>
                                                        <th>Mobile</th>
                                                        <th>Email</th>
                                                        <th>Service Purchased</th>
                                                        <th>Subtotal</th>
                                                        <th>GST (18%)</th>
                                                        <th>Total Amount</th>
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
                                                            
                                                            // Get GST information
                                                            $subtotal = !empty($single['subtotal']) ? $single['subtotal'] : $single['amount'];
                                                            $gst_amount = !empty($single['gst_amount']) ? $single['gst_amount'] : 0;
                                                            $gst_enabled = !empty($single['gst_enabled']) && $single['gst_enabled'] == 1;
                                                            $total_amount = !empty($single['amount']) ? $single['amount'] : $subtotal;
                                                            
                                                    ?>
                                                    <tr>
                                                        <td><?= ++$i; ?></td>
                                                        <td><?= $single['name']; ?></td>
                                                        <td><?= $single['mobile']; ?></td>
                                                        <td><?= $single['email']; ?></td>
                                                        <td><?= $single['service_name']; ?></td>
                                                        <td><?= number_format($subtotal, 2); ?></td>
                                                        <td><?= $gst_enabled ? number_format($gst_amount, 2) : '-'; ?></td>
                                                        <td><strong><?= number_format($total_amount, 2); ?></strong></td>
                                                        <td><?= $status; ?></td>
                                                        <td>
                                                            <?php
                                                                if($single['status']!=0){
                                                            ?>
                                                            <a href="<?= base_url("orders/viewdocuments/".md5($single['id'])); ?>" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
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
            <script>
                $(document).ready(function(e) {
                    
                });
            </script>