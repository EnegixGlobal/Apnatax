
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
                                                        <th>Income</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if(!empty($earnings)){ $i=0;
                                                        foreach($earnings as $single ){
                                                            
                                                    ?>
                                                    <tr>
                                                        <td><?= ++$i; ?></td>
                                                        <td><?= $single['name']; ?></td>
                                                        <td><?= $single['mobile']; ?></td>
                                                        <td><?= $single['email']; ?></td>
                                                        <td><?= $single['service_name']; ?></td>
                                                        <td><?= $single['order_amount']; ?></td>
                                                        <td><?= $single['amount']; ?></td>
                                                    </tr>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php if($title=='View Earnings'){ ?>
                                        <a href="<?= base_url('wallet/employeeearnings/'); ?>" class="btn btn-sm btn-danger">Close</a>
                                        <?php } ?>
                                    </div>
                                </div>
            <script>
                $(document).ready(function(e) {
                    $('#datatable').dataTable();
                });
            </script>