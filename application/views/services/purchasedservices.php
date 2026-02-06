            <div class="card">

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-condensed" id="table">
                                    <thead>
                                        <tr>
                                            <th>Sl.No.</th>
                                            <th>Services</th>
                                            <th>Purchase Type</th>
                                            <?php
                                            // Check if any service has an option type
                                            $has_options = false;
                                            if (!empty($services)) {
                                                foreach ($services as $s) {
                                                    if (!empty($s['service_option_display'])) {
                                                        $has_options = true;
                                                        break;
                                                    }
                                                }
                                            }
                                            if ($has_options) { ?>
                                                <th>Option Type</th>
                                            <?php } ?>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($services)) {
                                            $i = 0;
                                            foreach ($services as $single) {
                                                $i++;
                                        ?>
                                                <tr>
                                                    <td><?= $i; ?></td>
                                                    <td><?= $single['name']; ?></td>
                                                    <td><?= $single['purchased_type']; ?></td>
                                                    <?php if ($has_options) { ?>
                                                        <td>
                                                            <?php if (!empty($single['service_option_display'])) { ?>
                                                                <span class="badge bg-info"><?= htmlspecialchars($single['service_option_display']); ?></span>
                                                            <?php } else { ?>
                                                                <span class="text-muted">-</span>
                                                            <?php } ?>
                                                        </td>
                                                    <?php } ?>
                                                    <td>
                                                        <a href="<?= base_url($single['link']) ?>" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i> View</a>
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

                <input type="hidden" id="id">
                <script>
                    $(document).ready(function(e) {
                        $('#table').dataTable();
                    });


                    function validate() {

                        return true; // Allow form submiss

                    }
                </script>
            </div>