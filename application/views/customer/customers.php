            <div class="card">

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Customers List</h5>
                                <?php
                                // Only show GST toggle for admin
                                $CI = &get_instance();
                                if ($CI->session->role == 'admin' || $CI->session->role == 'superadmin') {
                                ?>
                                    <div>
                                        <label class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="bulk_gst_toggle"
                                                <?php
                                                // Check if all customers have GST enabled
                                                $all_gst_enabled = true;
                                                if (!empty($customers)) {
                                                    foreach ($customers as $customer) {
                                                        if (empty($customer['gst_enabled']) || $customer['gst_enabled'] != 1) {
                                                            $all_gst_enabled = false;
                                                            break;
                                                        }
                                                    }
                                                } else {
                                                    $all_gst_enabled = false;
                                                }
                                                echo $all_gst_enabled ? 'checked' : '';
                                                ?>>
                                            <span class="form-check-label" for="bulk_gst_toggle">
                                                <strong>Enable GST (18%) for All Customers</strong>
                                            </span>
                                        </label>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-condensed" id="table">
                                    <thead>
                                        <tr>
                                            <th width="50">Sl.No.</th>
                                            <th>Name</th>
                                            <th>Mobile</th>
                                            <th>E-Mail</th>
                                            <th>State</th>
                                            <th>District</th>
                                            <th>Added By</th>
                                            <th width="140" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($customers)) {
                                            $i = 0;
                                            foreach ($customers as $single) {
                                                $i++;
                                        ?>
                                                <tr>
                                                    <td><?= $i; ?></td>
                                                    <td><?= $single['name']; ?></td>
                                                    <td><?= $single['mobile']; ?></td>
                                                    <td><?= $single['email']; ?></td>
                                                    <td><?= $single['state']; ?></td>
                                                    <td><?= $single['district']; ?></td>
                                                    <td><?= empty($single['user_name']) ? '-' : $single['user_name']; ?></td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a href="<?= base_url('customers/editcustomer/' . md5($single['id'])); ?>"
                                                                class="btn btn-xs btn-primary"
                                                                data-bs-toggle="tooltip"
                                                                title="Edit Customer">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                            <a href="<?= base_url('customers/kycdetails/' . md5($single['id'])); ?>"
                                                                class="btn btn-xs btn-info"
                                                                data-bs-toggle="tooltip"
                                                                title="View KYC">
                                                                <i class="fa fa-id-card"></i>
                                                            </a>
                                                            <a href="<?= base_url('customers/uploadolddata/' . md5($single['id'])); ?>"
                                                                class="btn btn-xs btn-success"
                                                                data-bs-toggle="tooltip"
                                                                title="Upload Old Data">
                                                                <i class="fa fa-upload"></i>
                                                            </a>
                                                            <?php
                                                            // Only show delete button for admin
                                                            $CI = &get_instance();
                                                            if ($CI->session->role == 'admin' || $CI->session->role == 'superadmin') {
                                                            ?>
                                                                <a href="javascript:void(0)"
                                                                    onclick="showDeleteModal('<?= md5($single['id']); ?>', '<?= htmlspecialchars(addslashes($single['name'])); ?>')"
                                                                    class="btn btn-xs btn-danger"
                                                                    data-bs-toggle="tooltip"
                                                                    title="Delete Customer">
                                                                    <i class="fa fa-trash"></i>
                                                                </a>
                                                            <?php } ?>
                                                        </div>
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
                <style>
                    .btn-xs {
                        padding: 0.25rem 0.5rem;
                        font-size: 0.75rem;
                        line-height: 1.2;
                        border-radius: 0.25rem;
                        min-width: 28px;
                        height: 28px;
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        transition: all 0.2s ease;
                        border: 1px solid transparent;
                    }

                    .btn-xs:hover {
                        transform: translateY(-1px);
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
                        opacity: 0.9;
                    }

                    .btn-xs i {
                        font-size: 0.875rem;
                        line-height: 1;
                    }

                    .gap-1 {
                        gap: 0.25rem;
                    }

                    .table td {
                        vertical-align: middle;
                    }

                    .table .text-center {
                        white-space: nowrap;
                    }

                    .btn-xs.btn-primary {
                        background-color: #007bff;
                        border-color: #007bff;
                    }

                    .btn-xs.btn-info {
                        background-color: #17a2b8;
                        border-color: #17a2b8;
                    }

                    .btn-xs.btn-success {
                        background-color: #28a745;
                        border-color: #28a745;
                    }

                    .btn-xs.btn-danger {
                        background-color: #dc3545;
                        border-color: #dc3545;
                    }

                    .btn-xs.btn-danger:hover {
                        background-color: #c82333;
                        border-color: #bd2130;
                    }
                </style>

                <!-- Delete Customer Confirmation Modal -->
                <div class="modal fade" id="deleteCustomerModal" tabindex="-1" aria-labelledby="deleteCustomerModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title" id="deleteCustomerModalLabel">
                                    <i class="fa fa-exclamation-triangle me-2"></i>Confirm Deletion
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-danger mb-3" role="alert">
                                    <h6 class="alert-heading"><i class="fa fa-warning me-2"></i>Warning: This action cannot be undone!</h6>
                                    <hr>
                                    <p class="mb-0">You are about to permanently delete customer: <strong id="deleteCustomerName"></strong></p>
                                </div>
                                <p class="mb-2"><strong>This will permanently delete:</strong></p>
                                <ul class="list-unstyled ms-3">
                                    <li><i class="fa fa-check text-danger me-2"></i>Customer account</li>
                                    <li><i class="fa fa-check text-danger me-2"></i>All firms</li>
                                    <li><i class="fa fa-check text-danger me-2"></i>All purchases/orders</li>
                                    <li><i class="fa fa-check text-danger me-2"></i>Wallet transactions</li>
                                    <li><i class="fa fa-check text-danger me-2"></i>KYC documents</li>
                                    <li><i class="fa fa-check text-danger me-2"></i>Bank statements</li>
                                    <li><i class="fa fa-check text-danger me-2"></i>All related data</li>
                                </ul>
                                <div class="alert alert-warning mt-3 mb-0" role="alert">
                                    <i class="fa fa-info-circle me-2"></i>
                                    <strong>Please confirm:</strong> Are you absolutely sure you want to proceed with this deletion?
                                </div>
                                <input type="hidden" id="deleteCustomerId" value="">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="deleteCancelBtn">
                                    <i class="fa fa-times me-1"></i>Cancel
                                </button>
                                <button type="button" class="btn btn-danger" onclick="deleteCustomer()" id="deleteConfirmBtn">
                                    <i class="fa fa-trash me-1"></i>Yes, Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    $(document).ready(function(e) {
                        $('#table').dataTable();

                        // Initialize tooltips
                        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl);
                        });

                        // Bulk GST Toggle Handler - Only for admin
                        <?php
                        // Only load GST toggle handler for admin
                        $CI = &get_instance();
                        if ($CI->session->role == 'admin' || $CI->session->role == 'superadmin') {
                        ?>
                            $('#bulk_gst_toggle').change(function() {
                                var isEnabled = $(this).is(':checked');
                                var action = isEnabled ? 'enable' : 'disable';

                                if (confirm('Are you sure you want to ' + action + ' GST (18%) for ALL customers? This will affect all customers in the system.')) {
                                    $.ajax({
                                        type: "POST",
                                        url: "<?= base_url('customers/bulkgsttoggle/'); ?>",
                                        data: {
                                            enable: isEnabled ? 1 : 0
                                        },
                                        dataType: 'json',
                                        success: function(response) {
                                            if (response.status === true) {
                                                alertify.success(response.message);
                                                // Reload page after 1 second to reflect changes
                                                setTimeout(function() {
                                                    location.reload();
                                                }, 1000);
                                            } else {
                                                alertify.error(response.message || 'An error occurred');
                                                // Revert checkbox state
                                                $('#bulk_gst_toggle').prop('checked', !isEnabled);
                                            }
                                        },
                                        error: function() {
                                            alertify.error('An error occurred while updating GST settings');
                                            // Revert checkbox state
                                            $('#bulk_gst_toggle').prop('checked', !isEnabled);
                                        }
                                    });
                                } else {
                                    // Revert checkbox state if user cancels
                                    $(this).prop('checked', !isEnabled);
                                }
                            });
                        <?php
                        }
                        ?>
                    });

                    // Helper function to show notifications (with fallback)
                    function showNotification(message, type) {
                        type = type || 'success';
                        if (typeof alertify !== 'undefined') {
                            if (type === 'success') {
                                alertify.success(message);
                            } else {
                                alertify.error(message);
                            }
                        } else if (typeof notifIt !== 'undefined') {
                            notifIt({
                                type: type,
                                msg: message,
                                position: 'right',
                                timeout: 3000
                            });
                        } else {
                            // Fallback to alert
                            alert(message);
                            console.log(type.toUpperCase() + ':', message);
                        }
                    }

                    // Show delete confirmation modal
                    function showDeleteModal(id, name) {
                        $('#deleteCustomerId').val(id);
                        $('#deleteCustomerName').text(name);
                        $('#deleteCustomerModal').modal('show');
                    }

                    // Delete customer function
                    function deleteCustomer() {
                        var id = $('#deleteCustomerId').val();
                        var name = $('#deleteCustomerName').text();

                        if (!id) {
                            showNotification('Customer ID is missing!', 'error');
                            return;
                        }

                        // Disable buttons and show loading
                        $('#deleteConfirmBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Deleting...');
                        $('#deleteCancelBtn').prop('disabled', true);

                        $.ajax({
                            type: "POST",
                            url: "<?= base_url('customers/deletecustomer/'); ?>",
                            data: {
                                id: id
                            },
                            dataType: 'json',
                            timeout: 30000, // 30 second timeout
                            success: function(response) {
                                // Re-enable buttons first
                                $('#deleteConfirmBtn').prop('disabled', false).html('<i class="fa fa-trash"></i> Yes, Delete');
                                $('#deleteCancelBtn').prop('disabled', false);

                                // Check if response is valid
                                if (!response) {
                                    showNotification('Invalid response from server', 'error');
                                    return;
                                }

                                if (response.status === true) {
                                    // Close modal
                                    $('#deleteCustomerModal').modal('hide');
                                    showNotification(response.message || 'Customer deleted successfully!', 'success');
                                    // Reload page after 1 second
                                    setTimeout(function() {
                                        location.reload();
                                    }, 1000);
                                } else {
                                    var errorMsg = response.message || 'Failed to delete customer';
                                    showNotification(errorMsg, 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                var errorMsg = 'An error occurred while deleting customer';

                                // Try to parse error response
                                if (xhr.responseText) {
                                    try {
                                        var errorResponse = JSON.parse(xhr.responseText);
                                        if (errorResponse && errorResponse.message) {
                                            errorMsg = errorResponse.message;
                                        }
                                    } catch (e) {
                                        // If response is HTML (like error page), show generic message
                                        if (xhr.status === 403) {
                                            errorMsg = 'Access denied. You do not have permission to delete customers.';
                                        } else if (xhr.status === 404) {
                                            errorMsg = 'Delete endpoint not found. Please check the URL.';
                                        } else if (xhr.status === 500) {
                                            errorMsg = 'Server error occurred. Please try again later.';
                                        } else if (status === 'timeout') {
                                            errorMsg = 'Request timed out. Please try again.';
                                        } else if (status === 'error') {
                                            errorMsg = 'Network error. Please check your connection.';
                                        }
                                    }
                                } else {
                                    if (status === 'timeout') {
                                        errorMsg = 'Request timed out. Please try again.';
                                    } else if (status === 'error') {
                                        errorMsg = 'Network error. Please check your connection.';
                                    }
                                }

                                // Always re-enable buttons and show error
                                $('#deleteConfirmBtn').prop('disabled', false).html('<i class="fa fa-trash"></i> Yes, Delete');
                                $('#deleteCancelBtn').prop('disabled', false);

                                showNotification(errorMsg, 'error');
                            },
                            complete: function() {
                                // Always ensure buttons are enabled when request completes (success or error)
                                $('#deleteConfirmBtn').prop('disabled', false);
                                $('#deleteCancelBtn').prop('disabled', false);
                            }
                        });
                    }

                    // Reset modal when closed
                    $('#deleteCustomerModal').on('hidden.bs.modal', function() {
                        $('#deleteConfirmBtn').prop('disabled', false).html('<i class="fa fa-trash"></i> Yes, Delete');
                        $('#deleteCancelBtn').prop('disabled', false);
                        $('#deleteCustomerId').val('');
                        $('#deleteCustomerName').text('');
                    });

                    // Also reset on show to ensure clean state
                    $('#deleteCustomerModal').on('show.bs.modal', function() {
                        $('#deleteConfirmBtn').prop('disabled', false).html('<i class="fa fa-trash"></i> Yes, Delete');
                        $('#deleteCancelBtn').prop('disabled', false);
                    });
                </script>
            </div>