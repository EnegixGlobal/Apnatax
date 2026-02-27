<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0"><?php echo $title; ?></h4>
    </div>
    <div class="card-body">
        <?php $this->load->view('includes/alerts'); ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Invoice No</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Firm</th>
                        <th>Service</th>
                        <th>Total Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($invoices)) : ?>
                        <?php foreach ($invoices as $key => $invoice) : ?>
                            <tr>
                                <td><?php echo $key + 1; ?></td>
                                <td><?php echo htmlspecialchars($invoice['invoice_no']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($invoice['invoice_date'])); ?></td>
                                <td><?php echo htmlspecialchars($invoice['billing_name']); ?></td>
                                <td><?php echo htmlspecialchars($invoice['firm_name']); ?></td>
                                <td><?php echo htmlspecialchars($invoice['service_name']); ?></td>
                                <td><?php echo number_format($invoice['total_amount'], 2); ?></td>
                                <td>
                                    <a href="<?php echo base_url('invoices/view/' . md5($invoice['id'])); ?>" target="_blank" class="btn btn-sm btn-primary mb-1">
                                        View
                                    </a>
                                    <a href="<?php echo base_url('invoices/download/' . md5($invoice['id'])); ?>" class="btn btn-sm btn-success">
                                        Download PDF
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="8" class="text-center">No invoices found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


