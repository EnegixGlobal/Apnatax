
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
                                                        <th>Income</th>
                                                        <th>Paid</th>
                                                        <th>Balance</th>
                                                        <th>Action</th>
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
                                                        <td><?= $single['amount']; ?></td>
                                                        <td><?= $single['paid']; ?></td>
                                                        <td><?= $single['balance']; ?></td>
                                                        <td>
                                                            <a href="<?= base_url('wallet/viewearnings/'.md5($single['id'])); ?>" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> View</a>
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