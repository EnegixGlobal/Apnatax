
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
                                        <td>
                                            <?php if (!empty($single['is_renewal'])) { ?>
                                                <button type="button"
                                                        class="btn btn-sm btn-primary renew-btn"
                                                        data-service-id="<?= $single['service_id']; ?>">
                                                    Renew
                                                </button>
                                            <?php } else { ?>
                                                <a href="<?= base_url('services/openform/' . $single['service_slug'] . '/' . $single['id']); ?>"
                                                   target="_blank"
                                                   class="btn btn-sm btn-primary">
                                                    Pay Now
                                                </a>
                                            <?php } ?>
                                        </td>
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

                            // Handle renewal button click
                            $('table').on('click', '.renew-btn', function() {
                                var serviceId = $(this).data('service-id');
                                if (!serviceId) {
                                    alert('Invalid service selected for renewal.');
                                    return false;
                                }
                                if (!confirm('Are you sure you want to renew this service for the current year?')) {
                                    return false;
                                }

                                $.ajax({
                                    type: "post",
                                    url: "<?= base_url('services/renewservice'); ?>",
                                    data: { service_id: serviceId },
                                    success: function(response) {
                                        try {
                                            var data = JSON.parse(response);
                                            if (data.status) {
                                                alert(data.message);
                                                window.location.reload();
                                            } else {
                                                alert(data.message || 'Unable to renew service.');
                                            }
                                        } catch (e) {
                                            alert('Unexpected response from server while renewing service.');
                                        }
                                    },
                                    error: function() {
                                        alert('Failed to renew service. Please try again.');
                                    }
                                });
                            });
                        });

                        function validate(){
                            if($('input[name="sections[]"]:checked').length<1){
                                alert("Please select atleast 1 Section!");
                                return false;
                            }
                        }
                    </script>
