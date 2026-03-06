
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">

                        <?php if (!empty($expired_packages)) : ?>
                            <div class="alert alert-danger d-flex align-items-center mb-4" style="font-size:.9rem;">
                                <i class="fe fe-alert-circle me-2" style="font-size:1.1rem"></i>
                                <strong><?= count($expired_packages) ?> package(s) expired &amp; not renewed.</strong>&nbsp;
                                Renew by paying from your wallet, or <a href="<?= base_url('mywallet/') ?>">recharge wallet</a> first.
                            </div>

                            <?php foreach ($expired_packages as $epkg) :
                                $pkg_type  = !empty($epkg['package_type']) ? $epkg['package_type'] : 'Yearly';
                                $bill      = (float)($epkg['bill_amount'] ?? 0);
                                $exp_date  = !empty($epkg['expiry_date']) ? date('d-m-Y', strtotime($epkg['expiry_date'])) : '—';
                            ?>
                            <div class="card mb-3 border-danger" style="border-left:4px solid #dc3545;">
                                <div class="card-body py-3 px-3">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div>
                                            <span class="badge bg-danger me-1"><?= htmlspecialchars($pkg_type) ?></span>
                                            <span class="badge bg-light text-dark border">Expired <?= $exp_date ?></span>
                                            <?php if (!empty($epkg['year'])) : ?>
                                                <span class="badge bg-secondary ms-1">Year: <?= htmlspecialchars($epkg['year']) ?></span>
                                            <?php endif; ?>
                                            <div class="mt-1" style="font-size:.85rem;color:#333;">
                                                <strong>Services:</strong>
                                                <?php
                                                // Use pre-resolved service names from controller
                                                if (!empty($epkg['service_names'])) {
                                                    echo htmlspecialchars(implode(', ', $epkg['service_names']));
                                                } else {
                                                    echo '<em>No services</em>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div style="font-size:1.15rem;font-weight:700;color:#dc3545;">
                                                ₹<?= number_format($bill, 2) ?>
                                            </div>
                                            <button class="btn btn-danger btn-sm mt-1 renew-pkg-btn"
                                                    data-pkg-id="<?= $epkg['id'] ?>"
                                                    data-amount="<?= $bill ?>">
                                                <i class="fe fe-credit-card me-1"></i>Renew &amp; Pay
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>

                        <?php else : ?>
                            <div class="alert alert-success d-flex align-items-center" style="font-size:.9rem;">
                                <i class="fe fe-check-circle me-2" style="font-size:1.1rem"></i>
                                No expired packages pending renewal. All packages are up to date!
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
                    
                    <script>
                        $(document).ready(function(e) {

                            // ── Handle package renewal button click ──────────
                            $('body').on('click', '.renew-pkg-btn', function() {
                                var btn   = $(this);
                                var pkgId = btn.data('pkg-id');
                                var amt   = btn.data('amount');

                                if (!pkgId) { alert('Invalid package.'); return; }
                                if (!confirm('Renew this package for ₹' + parseFloat(amt).toFixed(2) + ' from your wallet?')) return;

                                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Processing…');

                                $.ajax({
                                    type: 'POST',
                                    url: '<?= base_url("services/renewpackage"); ?>',
                                    data: { package_id: pkgId },
                                    dataType: 'json',
                                    success: function(resp) {
                                        if (resp.status) {
                                            alert(resp.message);
                                            window.location.reload();
                                        } else {
                                            alert(resp.message || 'Unable to renew package.');
                                            if (resp.redirect) window.location = resp.redirect;
                                            btn.prop('disabled', false).html('<i class="fe fe-credit-card me-1"></i>Renew & Pay');
                                        }
                                    },
                                    error: function() {
                                        alert('Server error. Please try again.');
                                        btn.prop('disabled', false).html('<i class="fe fe-credit-card me-1"></i>Renew & Pay');
                                    }
                                });
                            });
                        });
                    </script>
