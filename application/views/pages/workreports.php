            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="datatable">
                                    <thead>
                                        <tr>
                                            <th>Sl.No.</th>
                                            <th>Service Name</th>
                                            <th>Purchase Type</th>
                                            <th>Date</th>
                                            <th>Assessment Date</th>
                                            <th>Remarks</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if(!empty($workreports)){ 
                                            $i=0;
                                            foreach($workreports as $report){
                                                $i++;
                                                $assessment_file = !empty($report['assessment_file']) ? $report['assessment_file'] : '';
                                                $assessment_date = !empty($report['assessment_date']) ? date('d-m-Y', strtotime($report['assessment_date'])) : 'N/A';
                                                $order_date = !empty($report['date']) ? date('d-m-Y', strtotime($report['date'])) : 'N/A';
                                        ?>
                                        <tr>
                                            <td><?= $i; ?></td>
                                            <td><?= !empty($report['service_name']) ? $report['service_name'] : 'N/A'; ?></td>
                                            <td><?= !empty($report['purchased_type']) ? $report['purchased_type'] : 'N/A'; ?></td>
                                            <td><?= $order_date; ?></td>
                                            <td><?= $assessment_date; ?></td>
                                            <td><?= !empty($report['assessment_remarks']) ? $report['assessment_remarks'] : '-'; ?></td>
                                            <td>
                                                <?php if(!empty($assessment_file)){ ?>
                                                    <a href="<?= file_url($assessment_file) ?>" target="_blank" download class="btn btn-sm btn-primary">
                                                        <i class="fa fa-download"></i> Download Report
                                                    </a>
                                                <?php } else { ?>
                                                    <span class="text-muted">No report available</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                            }
                                        } else {
                                        ?>
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <p class="text-muted">No work reports available yet. Reports will appear here once assessments are completed.</p>
                                            </td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready(function(e) {
                    $('#datatable').dataTable({
                        "order": [[ 0, "desc" ]],
                        "pageLength": 25
                    });
                });
            </script>

