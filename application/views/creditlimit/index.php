<div class="row">
    <div class="col-md-6 col-lg-4 col-xl-4">
        <div class="card bg-primary img-card box-primary-shadow">
            <div class="card-body">
                <div class="d-flex">
                    <div class="text-white">
                        <h2 class="mb-0 number-font">₹ <?= number_format($credit_limit, 2) ?></h2>
                        <p class="text-white mb-0">Available Credit Limit</p>
                    </div>
                    <div class="ms-auto"> <i class="fa fa-credit-card text-white fs-30 me-2 mt-2"></i> </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-12 col-lg-8 col-xl-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Credit Information</h3>
            </div>
            <div class="card-body">
                <p>Welcome to your Credit Limit dashboard. Your admin has assigned you a credit limit which allows you to purchase services seamlessly up to the available amount.</p>
                <ul>
                    <li><strong>Total Credit Limit:</strong> ₹ <?= number_format($credit_limit, 2) ?></li>
                </ul>
                <?php if ($credit_limit > 0): ?>
                    <div class="alert alert-success mt-4">
                        <i class="fa fa-check-circle-o"></i> Your credit line is active!
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mt-4">
                        <i class="fa fa-warning"></i> Your credit limit is currently zero. Please contact the administrator to assign a limit.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
