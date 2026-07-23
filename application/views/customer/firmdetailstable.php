<?php
$package_name = "None";
if (!empty($cpackage)) {
    if (strtolower($cpackage['package_type']) == 'turnover') {
        $package_name = "Turnover (" . ($cpackage['package_id'] == 1 ? 'Accountancy Prime' : 'Accountancy Premium') . ")";
    } else {
        $package_name = $cpackage['package_type'];
    }
}
?>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title text-white">Package Type</h5>
                <h3><?= $package_name ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title text-white">Credit Limit</h5>
                <h3>₹ <?= number_format($credit_limit, 2) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title text-white">Wallet Balance</h5>
                <h3>₹ <?= number_format($wallet_balance, 2) ?></h3>
            </div>
        </div>
    </div>
</div>

<?php
if (!empty($cpackage)) {
    $this->load->view('orders/acc_table', $data ?? []);
} else {
    echo '<h3 class="text-danger text-center mt-4">No Active Package Found For This Firm!</h3>';
}
?>
