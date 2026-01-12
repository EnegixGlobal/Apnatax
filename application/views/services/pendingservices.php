
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="datatable">
                                <thead>
                                    <tr>
                                        <th>Sl.No.</th>
                                        <th>Service</th>
                                        <th>Month</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(!empty($services)){ $i=0;
                                        foreach($services as $single ){
                                    ?>
                                    <tr>
                                        <td><?= ++$i; ?></td>
                                        <td><?= $single['service_name']; ?></td>
                                        <td><?= $single['month']; ?></td>
                                        <td><?= $single['amount']; ?></td>
                                        <td><a href="#" target="_blank" class="btn btn-sm btn-primary">Pay Now</a></td>
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
