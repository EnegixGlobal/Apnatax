
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
                                                        <th>Address</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if(!empty($employees)){ $i=0;
                                                        foreach($employees as $employee ){
                                                    ?>
                                                    <tr>
                                                        <td><?= ++$i; ?></td>
                                                        <td><?= $employee['name']; ?></td>
                                                        <td><?= $employee['mobile']; ?></td>
                                                        <td><?= $employee['email']; ?></td>
                                                        <td><?= $employee['address']; ?></td>
                                                        <td>
                                                            <a href="<?= base_url("employees/edit/".md5($employee['id'])); ?>" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
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