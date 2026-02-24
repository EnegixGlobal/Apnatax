            
            <div class="card">
                
                <div class="card-body">
                    <?php if(!empty($migration_needed)){ ?>
                    <div class="alert alert-warning">
                        <strong>Migration Required!</strong> The 'request' column does not exist in the service_packages table. 
                        Please run the migration file: <code>database_migration_package_delete_request.sql</code>
                    </div>
                    <?php } else { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-condensed" id="table">
                                    <thead>
                                        <tr>
                                            <th>Sl.No.</th>
                                            <th>Customer Name</th>
                                            <th>Firm Name</th>
                                            <th>Year</th>
                                            <th>Services</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if(!empty($packages)){ $i=0;
                                            foreach($packages as $single){
                                                $i++;
                                                $service_names = array();
                                                if(!empty($single['services'])){
                                                    foreach($single['services'] as $service){
                                                        $service_names[] = $service['name'];
                                                    }
                                                }
                                                $services_display = !empty($service_names) ? implode(', ', $service_names) : 'N/A';
                                                
                                                // Format year: 20252026 -> 2025-2026
                                                $year_display = $single['year'];
                                                if(strlen($year_display) == 8 && is_numeric($year_display)){
                                                    $year1 = substr($year_display, 0, 4);
                                                    $year2 = substr($year_display, 4, 4);
                                                    $year_display = $year1 . '-' . $year2;
                                                }
                                        ?>
                                        <tr>
                                            <td><?= $i; ?></td>
                                            <td><?= $single['customer_name']; ?></td>
                                            <td><?= !empty($single['firm_name']) ? $single['firm_name'] : 'N/A (ID: '.$single['firm_id'].')'; ?></td>
                                            <td><?= $year_display; ?></td>
                                            <td><?= $services_display; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-success approve" value="<?= md5('package-id-'.$single['id']) ?>">Approve</button>
                                                <button type="button" class="btn btn-sm btn-danger reject" value="<?= md5('package-id-'.$single['id']) ?>">Reject</button>
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
                    <?php } ?>
                </div>
            <script>
                $(document).ready(function(e) {
                    $('body').on('click','.approve',function(){
                        var id=$(this).val();
                        if(confirm("Confirm Approve Package Delete Request?")){
                            updatepackagerequest(id,1);
                        }
                    });
                    $('body').on('click','.reject',function(){
                        var id=$(this).val();
                        if(confirm("Confirm Reject Package Delete Request?")){
                            updatepackagerequest(id,0);
                        }
                    });
                    $('#table').dataTable();
                });
                
                function updatepackagerequest(id,status){
                    $.ajax({
                        type:'post',
                        url:'<?= base_url('customers/updatepackagestatus'); ?>',
                        data:{id:id,status:status},
                        success:function(data){
                            window.location.reload();
                        }
                    });
                }
            </script>
            </div>

