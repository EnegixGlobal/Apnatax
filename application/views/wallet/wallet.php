
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-primary img-card box-primary-shadow">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="text-white">
                                        <h2 class="mb-0 number-font"><?= $this->amount->toDecimal($available_balance); ?></h2>
                                        <p class="text-white mb-0">Available Wallet Balance </p>
                                    </div>
                                    <div class="ms-auto card-icon">
                                        <img src="<?= file_url('assets/images/money.svg'); ?>" alt="" style="filter:grayscale(1)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-4">
                        <div class="card bg-warning img-card box-primary-shadow">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="text-white">
                                        <h2 class="mb-0 number-font"><?= $this->amount->toDecimal($security_deposit); ?></h2>
                                        <p class="text-white mb-0">Security Deposit </p>
                                    </div>
                                    <div class="ms-auto card-icon">
                                        <img src="<?= file_url('assets/images/money.svg'); ?>" alt="" style="filter:grayscale(1)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info img-card box-primary-shadow">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="text-white">
                                        <h2 class="mb-0 number-font"><?= $this->amount->toDecimal($total_balance); ?></h2>
                                        <p class="text-white mb-0">Total Wallet Balance </p>
                                    </div>
                                    <div class="ms-auto card-icon">
                                        <img src="<?= file_url('assets/images/money.svg'); ?>" alt="" style="filter:grayscale(1)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div><hr>
                <div class="row <?= $this->session->flashdata('remaining')===NULL?'':'bg-danger py-2'; ?>">
                    <div class="col-md-12">
                        <div class="lead">Add To wallet</div>
                        <?= form_open('wallet/addtowallet'); ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-sm-12 col-form-label">Amount <span class="text-danger">*</span></label>
                                        <div class="col-sm-12">
                                            <input type="number" class="form-control" name="amount" id="amount" required value="<?= $this->session->flashdata('remaining')??'' ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8"><br>
                                    <button type="submit" name="addtowallet" class="btn btn-success">Add To Wallet</button>
                                </div>
                        </div>
                        <?= form_close(); ?>
                    </div>
                </div><hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="lead">Transactions</div>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="datatable">
                                <thead>
                                    <tr>
                                        <th>Sl.No.</th>
                                        <th>Service Name</th>
                                        <th>Type</th>
                                        <th>Transaction Type</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Remarks</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(!empty($transactions)){ $i=0;
                                        foreach($transactions as $single ){
                                            $status='<span class="text-success">Success</span>';
                                            $type='Wallet Top Up';
                                            $color='text-success';
                                            $trans_type='Credit';
                                            if($single['type']=='service_purchase'){
                                                $type='Service Purchase';
                                                $color='text-danger';
                                                $trans_type='Debit';
                                            }
                                            elseif($single['type']=='acc_payment'){
                                                $type='Accountancy Payment';
                                                $color='text-danger';
                                                $trans_type='Debit';
                                            }
                                            elseif($single['type']=='security_deposit'){
                                                $type='Security Deposit';
                                                $color='text-danger';
                                                $trans_type='Debit';
                                            }
                                            $remarks=!empty($single['remarks'])?$single['remarks'].'<br>':'';
                                            $remarks.="Transaction Id: ".$single['transaction_id'];
                                    ?>
                                    <tr>
                                        <td><?= ++$i; ?></td>
                                        <td><?= !empty($single['service_name'])?$single['service_name']:'-'; ?></td>
                                        <td><?= $type; ?></td>
                                        <td class="<?= $color ?>"><?= $trans_type; ?></td>
                                        <td><?= date('d-m-Y',strtotime($single['date'])); ?></td>
                                        <td class="<?= $color ?>"><?= $single['amount']; ?></td>
                                        <td><?= $remarks; ?></td>
                                        <td><?= $status; ?></td>
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
            </div>
                    
                    <script>
                        $(document).ready(function(e) {
                            $('#datatable').dataTable();
                            $.get( "<?= base_url('franchise/franchisestatus'); ?>", function( data ) {
                            });
                        });

                        function validate(){
                            if($('input[name="sections[]"]:checked').length<1){
                                alert("Please select atleast 1 Section!");
                                return false;
                            }
                        }
                    </script>
