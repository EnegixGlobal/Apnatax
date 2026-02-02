                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Old Data</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <p class="text-muted">View and download old data uploaded by admin/employee for your account.</p>
                                        </div>
                                    </div>
                                    
                                    <?php if(!empty($old_data)){ ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="oldDataTable">
                                            <thead>
                                                <tr>
                                                    <th>Service</th>
                                                    <th>File Name</th>
                                                    <th>Year</th>
                                                    <th>Description</th>
                                                    <th>Uploaded On</th>
                                                    <th>File Size</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($old_data as $data){ 
                                                    $file_size = !empty($data['file_size']) ? $data['file_size'] : 0;
                                                    $file_size_formatted = '';
                                                    if($file_size > 0){
                                                        if($file_size < 1024){
                                                            $file_size_formatted = $file_size . ' B';
                                                        } elseif($file_size < 1048576){
                                                            $file_size_formatted = round($file_size / 1024, 2) . ' KB';
                                                        } else {
                                                            $file_size_formatted = round($file_size / 1048576, 2) . ' MB';
                                                        }
                                                    }
                                                ?>
                                                <tr>
                                                    <td><?= !empty($data['service_name'])?$data['service_name']:'-'; ?></td>
                                                    <td><?= !empty($data['file_name'])?$data['file_name']:'-'; ?></td>
                                                    <td><?= !empty($data['year'])?$data['year']:'-'; ?></td>
                                                    <td><?= !empty($data['description'])?$data['description']:'-'; ?></td>
                                                    <td><?= !empty($data['added_on'])?date('d-m-Y H:i',strtotime($data['added_on'])):'-'; ?></td>
                                                    <td><?= $file_size_formatted; ?></td>
                                                    <td>
                                                        <a href="<?= base_url('profile/downloadolddata/'.md5($data['id'])); ?>" class="btn btn-sm btn-success" title="Download">
                                                            <i class="fa fa-download"></i> Download
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php } else { ?>
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> No old data available. Old data uploaded by admin/employee will be displayed here.
                                    </div>
                                    <?php } ?>
                                    
                                </div>
                            </div>
                            
                            <?php if(!empty($datatable)){ ?>
                            <script>
                                $(document).ready(function() {
                                    $('#oldDataTable').DataTable({
                                        "order": [[ 4, "desc" ]],
                                        "pageLength": 25,
                                        "language": {
                                            "search": "Search:",
                                            "lengthMenu": "Show _MENU_ entries",
                                            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                                            "infoEmpty": "No entries found",
                                            "infoFiltered": "(filtered from _MAX_ total entries)",
                                            "paginate": {
                                                "first": "First",
                                                "last": "Last",
                                                "next": "Next",
                                                "previous": "Previous"
                                            }
                                        }
                                    });
                                });
                            </script>
                            <?php } ?>

