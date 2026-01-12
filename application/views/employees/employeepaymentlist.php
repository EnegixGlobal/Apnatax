
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="datatable">
                                                <thead>
                                                    <tr>
                                                        <th>Sl.No.</th>
                                                        <th>Date</th>
                                                        <th>Name</th>
                                                        <th>Mobile</th>
                                                        <th>Email</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if(!empty($earnings)){ $i=0;
                                                        foreach($earnings as $single ){
                                                            
                                                    ?>
                                                    <tr>
                                                        <td><?= ++$i; ?></td>
                                                        <td><?= date('d-m-Y',strtotime($single['date'])); ?></td>
                                                        <td><?= $single['emp_name']; ?></td>
                                                        <td><?= $single['emp_mobile']; ?></td>
                                                        <td><?= $single['emp_email']; ?></td>
                                                        <td><?= $single['amount']; ?></td>
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