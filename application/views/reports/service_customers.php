<style>
    .cell-right{
        text-align: right;
    }
    .summary-card {
        border-left: 4px solid;
    }
    .summary-card.primary {
        border-left-color: #467fcf;
    }
    .summary-card.success {
        border-left-color: #5eba00;
    }
</style>
                                <div class="card-body">
                                    <!-- Filter Form -->
                                    <form method="get" action="<?= base_url('reports/servicecustomers/'); ?>" class="mb-4">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Service <span class="text-danger">*</span></label>
                                                    <?= form_dropdown('service_id', $services, $selected_service, 'class="form-control" id="service_id" required'); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Start Date</label>
                                                    <input type="date" name="start_date" class="form-control" value="<?= $start_date; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>End Date</label>
                                                    <input type="date" name="end_date" class="form-control" value="<?= $end_date; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>&nbsp;</label><br>
                                                    <button type="submit" class="btn btn-primary">Filter</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    
                                    <?php if(!empty($selected_service)){ ?>
                                    <!-- Summary Cards -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="card summary-card primary">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <h3 class="mb-0"><?= number_format($total_amount, 2); ?></h3>
                                                            <p class="text-muted mb-0">Total Amount</p>
                                                        </div>
                                                        <div class="ms-auto">
                                                            <i class="fa fa-rupee fa-2x text-primary"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card summary-card success">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <h3 class="mb-0"><?= $total_count; ?></h3>
                                                            <p class="text-muted mb-0">Total Purchases</p>
                                                        </div>
                                                        <div class="ms-auto">
                                                            <i class="fa fa-shopping-cart fa-2x text-success"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Customers Table -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="customers_table">
                                            <thead>
                                                <tr>
                                                    <th>Customer Name</th>
                                                    <th>Mobile</th>
                                                    <th>Email</th>
                                                    <th>Purchase Date</th>
                                                    <th class="cell-right">Amount</th>
                                                    <th>GST</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    if(!empty($customers_data)){
                                                        foreach($customers_data as $row){
                                                ?>
                                                <tr>
                                                    <td><?= $row['customer_name']; ?></td>
                                                    <td><?= $row['mobile']; ?></td>
                                                    <td><?= $row['email']; ?></td>
                                                    <td><?= date('d-m-Y', strtotime($row['date'])); ?></td>
                                                    <td class="cell-right"><?= number_format($row['amount'], 2); ?></td>
                                                    <td><?= !empty($row['gst_enabled']) && $row['gst_enabled']==1 ? 'Yes' : 'No'; ?></td>
                                                </tr>
                                                <?php
                                                        }
                                                    }
                                                    else{
                                                ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">No customers found for selected service</td>
                                                </tr>
                                                <?php
                                                    }
                                                ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="font-weight-bold">
                                                    <td colspan="4">Total</td>
                                                    <td class="cell-right"><?= number_format($total_amount, 2); ?></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <?php } else { ?>
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> Please select a service to view customers.
                                    </div>
                                    <?php } ?>
                                </div>
                                
<script>
    $(document).ready(function(){
        <?php if($datatable && !empty($selected_service)){ ?>
        $('#customers_table').DataTable({
            "order": [[ 3, "desc" ]],
            "pageLength": 25
        });
        <?php } ?>
    });
</script>

