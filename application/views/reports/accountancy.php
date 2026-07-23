<style>
    .cell-right {
        text-align: right;
    }
</style>
<?php
$user_id = $year = '';
if ($this->session->flashdata('user_id') !== NULL) {
    $user_id = $this->session->flashdata('user_id');
}
if ($this->session->flashdata('year') !== NULL) {
    $year = $this->session->flashdata('year');
}
?>
<div class="card-body">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="form-group">
                <?php
                echo create_form_input('text', '', "Package", true, $package['name'], ['id' => 'package', 'readonly' => 'true']);
                ?>
            </div>
        </div>
    </div>
    <div class="row my-4">
        <div class="col-md-12">
            <div id="result">
                <table class="table table-bordered" id="acc_table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Outstanding</th>
                            <th>GTO</th>
                            <th>Accounts Fee</th>
                            <th>Late Fee</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Due date</th>
                            <th>Delay in Days</th>
                            <th>Auto Debit Status</th>
                            <th>Action / Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        //print_pre($accountancy);
                        $date = date('Y-m-d');
                        $percent = 2 / 100;
                        $result = array();
                        $prev = array();
                        if (!empty($accountancy)) {
                            $total_fees = $total_other = $total_paid = $total_penalty = 0;
                            $total_days = 0;
                            $outstanding = $total = $balance = 0;
                            $total_balance_sum = 0;
                            $total_sum = 0;
                            $fees = $total_turnover / $package['turnover'];
                            $fees *= $package['rate'];
                            $count = count($accountancy);
                            $last = end($accountancy);
                            if ($last['date'] == '') {
                                $count--;
                            }
                            $acc_fees = $fees / $count;
                            foreach ($accountancy as $single) {
                                $days = $paid = $penalty = 0;
                                $paid = !empty($single['paid']) ? $single['paid'] : 0;
                                // Outstanding should be the previous month's balance (unpaid amount)
                                $outstanding = $balance;
                                if ($single['date'] != '') {
                                    $acc_fees = $fees / $count;
                                } else {
                                    $acc_fees = 0;
                                }
                                $other_fee = $single['other_fee'];
                                $total_other += $other_fee;
                                $balance = $outstanding + $acc_fees + $other_fee;
                                if ($single['due_date'] < $date && $paid < $balance) {
                                    $balance -= $paid;
                                    $date1 = new DateTime($single['due_date']);
                                    $date2 = new DateTime($date);

                                    // Calculate the difference
                                    $interval = $date1->diff($date2);

                                    // Get the difference in days
                                    $days = $interval->days;
                                    $penalty = ($percent * $balance);
                                    if ($days < 30) {
                                        $penalty /= 30;
                                        $penalty *= $days;
                                    }
                                    $penalty = round($penalty);
                                    $total_penalty += $penalty;
                                    $total_days += $days;
                                } else {
                                    $balance -= $paid;
                                }
                                // Ensure balance doesn't go negative
                                if ($balance < 0) {
                                    $balance = 0;
                                }
                                $total = $balance + $penalty;
                                $total_balance_sum += $balance;
                                $total_sum += $total;
                                $total_fees += $acc_fees;
                                $total_paid += $paid;
                                $month = $single['date'] != '' ? date("Ym", strtotime($single['date'])) : '';
                                if ($month != '') {
                                    if ($paid > 0 && !empty($prev)) {
                                        foreach ($prev as $m) {
                                            $result[$m]['paid'] = 1;
                                        }
                                        $prev = array();
                                    }
                                    $result[$month] = array(
                                        'turnover' => $single['turnover'],
                                        'due_date' => $single['due_date'],
                                        'paid' => $paid
                                    );
                                    $prev[] = $month;
                                }
                        ?>
                                <tr>
                                    <td>
                                        <?= $single['date'] != '' ? date('F-y', strtotime($single['date'])) : '--'; ?>
                                    </td>
                                    <td class="cell-right">
                                        <?= $this->amount->toDecimal($outstanding, false); ?>
                                    </td>
                                    <td class="cell-right">
                                        <?= $this->amount->toDecimal($single['turnover'], false); ?>
                                    </td>
                                    <td class="cell-right">
                                        <?= $this->amount->toDecimal($acc_fees, false); ?>
                                    </td>
                                    <td class="cell-right">
                                        <?= $this->amount->toDecimal($penalty, false); ?>
                                    </td>
                                    <td class="cell-right">
                                        <?= $this->amount->toDecimal($total, false); ?>
                                    </td>
                                    <td class="cell-right">
                                        <?php if ($paid > 0) { ?>
                                            <span class="badge bg-success"><?= $this->amount->toDecimal($paid, false); ?></span>
                                        <?php } else { ?>
                                            <?= $this->amount->toDecimal($paid, false); ?>
                                        <?php } ?>
                                    </td>
                                    <td class="cell-right">
                                        <?= $this->amount->toDecimal($balance, false); ?>
                                    </td>
                                    <td>
                                        <?= $single['due_date'] != '' ? date('d-m-Y F', strtotime($single['due_date'])) : '--'; ?>
                                    </td>
                                    <td><?= $days; ?></td>
                                    <td class="text-center">
                                        <?php if (($single['auto_debit_status'] ?? 'Pending') == 'Confirmed') { ?>
                                            <span class="badge bg-success">Confirmed</span>
                                        <?php } else { ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php } ?>
                                    </td>
                                    <?php if ($paid == 0) { ?>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info edit-btn" value="<?= $single['id']; ?>"><i class="fa fa-edit"></i></button>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" value="<?= $single['id']; ?>"><i class="fa fa-trash"></i></button>
                                        </td>
                                    <?php } else {
                                    ?>
                                        <td class="text-center font-weight-bold text-success">
                                            <?= !empty($single['payment_date']) ? date('d-m-Y', strtotime($single['payment_date'])) : '<i class="fa fa-check-circle" style="font-size: 1.5em;"></i>' ?>
                                        </td>
                                    <?php
                                    }
                                    ?>
                                </tr>
                            <?php
                            }
                        }
                        $rows = count($report);
                        if (!empty($report)) {
                            foreach ($report as $key => $row) {
                                if ($key == $rows - 1) {
                                    $footer = $row;
                                    break;
                                }
                            ?>
                                <tr>
                                    <?php
                                    foreach ($row as $value) {
                                    ?>
                                        <td><?= $value; ?></td>
                                    <?php
                                    }
                                    ?>
                                </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                    <?php if (!empty($accountancy)) {
                        // Use the sum of all individual row balances instead of calculation
                        $total_balance = isset($total_balance_sum) ? $total_balance_sum : 0;
                        // Ensure total balance doesn't go negative
                        if ($total_balance < 0) {
                            $total_balance = 0;
                        }
                    ?>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th class="cell-right">
                                    <?= $this->amount->toDecimal($total_turnover, false); ?>
                                </th>
                                <th class="cell-right">
                                    <?= $this->amount->toDecimal($total_fees, false); ?>
                                </th>
                                <th class="cell-right">
                                    <?= $this->amount->toDecimal($total_penalty, false); ?>
                                </th>
                                <th class="cell-right">
                                    <?php
                                    // Total should be sum of all row totals (balance + penalty for each row)
                                    // Since all balances are 0 after payment, total should be 0
                                    $footer_total = 0;
                                    if (isset($total_sum)) {
                                        $footer_total = $total_sum;
                                    }
                                    // Ensure total doesn't go negative
                                    if ($footer_total < 0) {
                                        $footer_total = 0;
                                    }
                                    echo $this->amount->toDecimal($footer_total, false);
                                    ?>
                                </th>
                                <th class="cell-right">
                                    <?php if ($total_paid > 0) { ?>
                                        <span class="badge bg-success"><?= $this->amount->toDecimal($total_paid, false); ?></span>
                                    <?php } else { ?>
                                        <?= $this->amount->toDecimal($total_paid, false); ?>
                                    <?php } ?>
                                </th>
                                <th></th>
                                <th></th>
                                <th><?= $total_days; ?></th>
                                <th></th>
                                <th>
                                    <?php if ($total_balance > 0) { ?>
                                        <a href="<?= base_url('reports/payment/'); ?>" class="btn btn-sm btn-success">
                                            <i class="fa fa-money"></i> Pay ₹<?= $this->amount->toDecimal($total_balance, false); ?>
                                        </a>
                                    <?php } ?>
                                </th>
                            </tr>
                        </tfoot>
                    <?php } ?>
                    <?php if (!empty($footer)) { ?>
                        <tfoot>
                            <tr>
                                <?php
                                foreach ($footer as $value) {
                                ?>
                                    <td><?= $value; ?></td>
                                <?php
                                }
                                ?>
                            </tr>
                        </tfoot>
                    <?php } ?>
                </table>
                <div id="acc_json" class="d-none"><?= json_encode($result); ?></div>
            </div>

            <?php
            // Show payment button after totals are calculated - only for last month's balance
            if (!empty($accountancy) && isset($total_fees) && isset($total_penalty) && isset($total_paid)) {
                $total_balance_display = $total_fees + $total_penalty - $total_paid;

                // Get the last month's balance and first month for range
                $last_month_balance = 0;
                $first_month_name = '';
                $last_month_name = '';
                $payment_month_range = '';
                if ($total_balance_display > 0) {
                    // Find the first and last unpaid month
                    $date_check = date('Y-m-d');
                    $percent_check = 2 / 100;
                    $outstanding_check = $total_check = 0;
                    $first_found = false;
                    foreach ($accountancy as $single_check) {
                        $paid_check = !empty($single_check['paid']) ? $single_check['paid'] : 0;
                        $outstanding_check = $total_check;
                        if ($single_check['date'] != '') {
                            $acc_fees_check = $fees / $count;
                        } else {
                            $acc_fees_check = 0;
                        }
                        $other_fee_check = $single_check['other_fee'] ?? 0;
                        $balance_check = $outstanding_check + $acc_fees_check + $other_fee_check;
                        if ($single_check['due_date'] < $date_check && $paid_check < $balance_check) {
                            $balance_check -= $paid_check;
                            $date1_check = new DateTime($single_check['due_date']);
                            $date2_check = new DateTime($date_check);
                            $interval_check = $date1_check->diff($date2_check);
                            $days_check = $interval_check->days;
                            $penalty_check = ($percent_check * $balance_check);
                            if ($days_check < 30) {
                                $penalty_check /= 30;
                                $penalty_check *= $days_check;
                            }
                            $penalty_check = round($penalty_check);
                        } else {
                            $balance_check -= $paid_check;
                        }
                        $total_check = $balance_check + ($penalty_check ?? 0);
                        if ($balance_check > 0) {
                            if (!$first_found) {
                                $first_month_name = $single_check['date'] != '' ? date('F-y', strtotime($single_check['date'])) : '';
                                $first_found = true;
                            }
                            $last_month_balance = $balance_check;
                            $last_month_name = $single_check['date'] != '' ? date('F-y', strtotime($single_check['date'])) : '';
                        }
                    }
                    // Create payment month range
                    if (!empty($first_month_name) && !empty($last_month_name)) {
                        if ($first_month_name != $last_month_name) {
                            $payment_month_range = $first_month_name . '-' . $last_month_name;
                        } else {
                            $payment_month_range = $last_month_name;
                        }
                    }
                }

                if ($last_month_balance > 0) {
            ?>
                    <div class="alert alert-warning mt-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5><i class="fa fa-exclamation-triangle"></i> Outstanding Balance</h5>
                                <p class="mb-0">Amount to Pay (<?= !empty($payment_month_range) ? $payment_month_range : $last_month_name; ?>): <strong class="text-danger" style="font-size: 1.3em;">₹<?= $this->amount->toDecimal($last_month_balance, false); ?></strong></p>
                                <small class="text-muted">Click the button below to make payment for <?= !empty($payment_month_range) ? $payment_month_range : $last_month_name; ?></small>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?= base_url('reports/payment/'); ?>" class="btn btn-success btn-lg">
                                    <i class="fa fa-money"></i> Pay Now ₹<?= $this->amount->toDecimal($last_month_balance, false); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php
                } else {
                ?>
                    <div class="alert alert-success mt-4">
                        <i class="fa fa-check-circle"></i> All payments are up to date! No outstanding balance.
                    </div>
            <?php
                }
            }
            ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(e) {
        $('body').on('change', '#user_id', function() {
            var user_id = $(this).val();
            $.ajax({
                type: "post",
                url: "<?= base_url('orders/getpackage/'); ?>",
                data: {
                    user_id: user_id
                },
                success: function(data) {
                    $('#package').val(data);
                    getfirm(user_id);
                }
            });
        });
        $('body').on('change', '#fyear', function() {
            getreport();
        });
        <?php
        if ($year != '') {
        ?>
            $('#fyear').trigger('change');
        <?php
        }
        ?>
    });

    function getfirm(user_id) {
        $.ajax({
            type: "post",
            url: "<?= base_url('orders/getfirms/'); ?>",
            data: {
                user_id: user_id
            },
            success: function(data) {
                $('#firm_id').html(data);
                getreport();
            }
        });
    }

    function getreport() {
        resetFields();
        var user_id = $('#user_id').val();
        var firm_id = $('#firm_id').val();
        var year = $('#fyear').val();
        $.ajax({
            type: "post",
            url: "<?= base_url('orders/getyearlyreport/'); ?>",
            data: {
                user_id: user_id,
                firm_id: firm_id,
                year: year
            },
            success: function(data) {
                $('#result').html(data);
                $('#result table tr').each(function() {
                    $(this).children().last().remove();
                });
                //var rows = $('tr:has(td.done)').first().prevAll();
                //console.log(rows); // Logs the 
                //rows.addClass('bg-danger');
                //rows.each(function(){
                //$(this).children().last().html('');
                //});
            }
        });
    }

    function resetFields() {
        $('#turnover,#due_date').val('');
    }

    function reloadAjax() {
        getreport()
    }
</script>