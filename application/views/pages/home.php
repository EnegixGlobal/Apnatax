
<?php
    $homedata=gethomedata();
?>

                                <?php
                                    if($this->session->role=='admin'){
                                ?>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="card bg-primary img-card box-primary-shadow">
                                            <div class="card-body">
                                                <div class="d-flex">
                                                    <div class="text-white">
                                                        <h2><?= countcustomers(); ?></h2>
                                                        <p class="text-white mb-0">Total Customers </p>
                                                    </div>
                                                    <div class="ms-auto card-icon">
                                                        <img src="<?= file_url('assets/images/user.png'); ?>" alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-secondary img-card box-primary-shadow">
                                            <div class="card-body">
                                                <div class="d-flex">
                                                    <div class="text-white">
                                                        <h2><?= countemployees(); ?></h2>
                                                        <p class="text-white mb-0">Total Employees </p>
                                                    </div>
                                                    <div class="ms-auto card-icon">
                                                        <img src="<?= file_url('assets/images/user.png'); ?>" alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                    }
                                    elseif($this->session->role=='customer'){
                                ?>
                                <div class="row">
                                    <div class="col-md-3">
                                        <a href="<?= base_url('services/'); ?>">
                                            <div class="card bg-primary img-card box-primary-shadow">
                                                <div class="card-body">
                                                    <div class="d-flex">
                                                        <div class="text-white">
                                                            <h2><?= countservices(); ?></h2>
                                                            <p class="text-white mb-0">Services </p>
                                                        </div>
                                                        <div class="ms-auto card-icon">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="<?= base_url('mywallet/'); ?>">
                                            <div class="card bg-blue img-card box-primary-shadow">
                                                <div class="card-body">
                                                    <div class="d-flex">
                                                        <div class="text-white">
                                                            <h2><?= getwalletbalance(); ?></h2>
                                                            <p class="text-white mb-0">My Wallet </p>
                                                        </div>
                                                        <div class="ms-auto card-icon">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="<?= base_url('profile/kyc/'); ?>">
                                            <div class="card bg-success img-card box-primary-shadow">
                                                <div class="card-body">
                                                    <div class="d-flex">
                                                        <div class="text-white">
                                                            <h2>&nbsp;</h2>
                                                            <p class="text-white mb-0">KYC </p>
                                                        </div>
                                                        <div class="ms-auto card-icon">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="<?= base_url('firms/'); ?>">
                                            <div class="card bg-secondary img-card box-primary-shadow">
                                                <div class="card-body">
                                                    <div class="d-flex">
                                                        <div class="text-white">
                                                            <h2><?= countfirms(); ?></h2>
                                                            <p class="text-white mb-0">Firms </p>
                                                        </div>
                                                        <div class="ms-auto card-icon">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="<?= base_url('package/'); ?>">
                                            <div class="card bg-purple img-card box-primary-shadow">
                                                <div class="card-body">
                                                    <div class="d-flex">
                                                        <div class="text-white">
                                                            <h2>&nbsp;</h2>
                                                            <p class="text-white mb-0">Package</p>
                                                        </div>
                                                        <div class="ms-auto card-icon">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="<?= base_url('services/purchasedservices/'); ?>">
                                            <div class="card bg-maroon img-card box-primary-shadow">
                                                <div class="card-body">
                                                    <div class="d-flex">
                                                        <div class="text-white">
                                                            <h2><?= countpurchasedservices(); ?></h2>
                                                            <p class="text-white mb-0">Purchased Services </p>
                                                        </div>
                                                        <div class="ms-auto card-icon">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="<?= base_url('reports/feereport/'); ?>">
                                            <div class="card bg-red img-card box-primary-shadow">
                                                <div class="card-body">
                                                    <div class="d-flex">
                                                        <div class="text-white">
                                                            <h2>&nbsp;</h2>
                                                            <p class="text-white mb-0">Fee Report </p>
                                                        </div>
                                                        <div class="ms-auto card-icon">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="<?= base_url('services/pendingservices/'); ?>">
                                            <div class="card bg-red img-card box-primary-shadow">
                                                <div class="card-body">
                                                    <div class="d-flex">
                                                        <div class="text-white">
                                                            <h2><?= countpendingservices(); ?></h2>
                                                            <p class="text-white mb-0">Pending Services </p>
                                                        </div>
                                                        <div class="ms-auto card-icon">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="<?= base_url('chat/'); ?>">
                                            <div class="card bg-teal img-card box-primary-shadow">
                                                <div class="card-body">
                                                    <div class="d-flex">
                                                        <div class="text-white">
                                                            <h2><?= countmessages(); ?></h2>
                                                            <p class="text-white mb-0">Chat </p>
                                                        </div>
                                                        <div class="ms-auto card-icon">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <?php
                                    }
                                    else{
                                ?>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="card bg-primary img-card box-primary-shadow">
                                            <div class="card-body">
                                                <div class="d-flex">
                                                    <div class="text-white">
                                                        <h2><?= countcustomers(); ?></h2>
                                                        <p class="text-white mb-0">Total Customers </p>
                                                    </div>
                                                    <div class="ms-auto card-icon">
                                                        <img src="<?= file_url('assets/images/user.png'); ?>" alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-secondary img-card box-primary-shadow">
                                            <div class="card-body">
                                                <div class="d-flex">
                                                    <div class="text-white">
                                                        <h2><?= $this->amount->toDecimal($balances['earnings'],false); ?></h2>
                                                        <p class="text-white mb-0">Total Earning </p>
                                                    </div>
                                                    <div class="ms-auto card-icon">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-warning img-card box-primary-shadow">
                                            <div class="card-body">
                                                <div class="d-flex">
                                                    <div class="text-white">
                                                        <h2><?= $this->amount->toDecimal($balances['payments'],false); ?></h2>
                                                        <p class="text-white mb-0">Total Payment </p>
                                                    </div>
                                                    <div class="ms-auto card-icon">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-success img-card box-primary-shadow">
                                            <div class="card-body">
                                                <div class="d-flex">
                                                    <div class="text-white">
                                                        <h2><?= $this->amount->toDecimal($balances['balance'],false); ?></h2>
                                                        <p class="text-white mb-0">Balance </p>
                                                    </div>
                                                    <div class="ms-auto card-icon">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                    }
                                ?>

    <script>
        $(document).ready(function(){
        });
        
    </script>
