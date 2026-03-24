<?php
$button = '';
$status = '<span class="text-danger">Pending</span>';
$order = !empty($order) && is_array($order) ? $order : [];
$firm = !empty($firm) && is_array($firm) ? $firm : [];
$order_status = isset($order['status']) ? (int)$order['status'] : 0;
if ($order_status === 2) {
    $status = '<span class="text-warning">Documents Uploaded!</span>';
} elseif ($order_status === 3) {
    $status = '<span class="text-info">Accepted for Assessment!</span>';
} elseif ($order_status === 4) {
    $status = '<span class="text-success">Assessment Done and Report Uploaded!</span>';
}
$service_name = isset($order['service_name']) ? $order['service_name'] : '';
$added_on = isset($order['added_on']) ? $order['added_on'] : '';
$display_date = !empty($added_on) ? date('d-m-Y', strtotime($added_on)) : '';
$firm_name = isset($firm['name']) ? $firm['name'] : '';
$purchased_type = isset($order['purchased_type']) ? $order['purchased_type'] : '';
$service_slug = isset($order['service_slug']) ? $order['service_slug'] : '';
$order_id = isset($order['id']) ? $order['id'] : '';
?>
<div class="card-body">
    <?= form_open_multipart('services/saveformdata/'); ?>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="form-group">
                <?php
                echo create_form_input('text', '', "Service Name", true, $service_name, ['readonly' => 'true']);
                ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <?php
                echo create_form_input('text', '', "Date", true, $display_date, ['readonly' => 'true']);
                ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <?php
                echo create_form_input('text', '', "Firm Name", true, $firm_name, ['readonly' => 'true']);
                ?>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <?php
        if ($purchased_type == 'Monthly') {
            $selected_month = !empty($selected_period) ? $selected_period : '';
        ?>
            <div class="col-md-4">
                <div class="form-group">
                    <?php
                    $attr = [];
                    echo create_form_input('select', 'month', "Month", true, $selected_month, $attr, month_dropdown($this->session->year));

                    ?>
                </div>
            </div>
        <?php
        } elseif ($purchased_type == 'Quarterly') {
            $selected_quarter = !empty($selected_period) ? $selected_period : '';
        ?>
            <div class="col-md-4">
                <div class="form-group">
                    <?php
                    $attr = [];
                    echo create_form_input('select', 'month', "Quarter", true, $selected_quarter, $attr, quarter_dropdown($this->session->year));

                    ?>
                </div>
            </div>
        <?php
        }
        ?>
        <?php
        //print_pre($documents);
        if (!empty($finaldocuments)) {
            $prev = '';
            $count = 0;
            foreach ($finaldocuments as $single) {
                $value = $single['field_value'];
                if ($single['value'] == 1 && $prev == '') {
                    $type = 'text';
                    $name = 'formdata[' . $single['slug'] . ']';
                    $label = $single['display_name'];
                    if (($single['file'] == 1 || $single['file'] == 2)) {
                        $prev = 'value';
                    }
                    $count = 0;
                } elseif (($single['file'] == 1 || $single['file'] == 2)) {
                    $type = 'file';
                    $name = $single['slug'] . '-file';
                    $label = $single['display_name'] . ' File';
                    $prev = 'file';
                    $count++;
                    if ($count == $single['file']) {
                        $prev = '';
                    }
                    if (!empty($value)) {
                        $extension = substr($value, -4);
                        $extension = trim($extension, '.');
                        if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') {
                            $filetype = 'image';
                        } elseif ($extension == 'pdf') {
                            $filetype = 'pdf';
                        } elseif ($extension == 'csv' || $extension == 'xlsx') {
                            $filetype = 'excel';
                        }
                    }
                }
                if ($single['document_id'] == 0) {
                    $label = $single['display_name'];
                    $value = $value;
                    $type = "text";
                }
        ?>
                <div class="col-md-4">
                    <div class="form-group">
                        <?php
                        $attr = [];
                        if (!$single['editable']) {
                            $attr['readonly'] = 'true';
                        }
                        // Add accept attribute for file inputs to restrict allowed formats
                        if ($type == 'file') {
                            $attr['accept'] = '.pdf,.jpg,.jpeg,.png,.csv,.xlsx,application/pdf,image/jpeg,image/jpg,image/png,text/csv,application/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                        }
                        echo create_form_input($type, $name, $label, true, $value, $attr);
                        // Add help text for file inputs
                        if ($type == 'file') {
                            echo '<small class="text-muted d-block mt-1"><i class="fa fa-info-circle"></i> Allowed formats: PDF, JPG, JPEG, PNG, CSV, XLSX</small>';
                        }
                        ?>
                    </div>
                </div>
        <?php
            }
        }
        ?>
    </div>
    <div class="row mb-4">
        <div class="col-12">
            <input type="hidden" name="slug" value="<?= $service_slug ?>">
            <input type="hidden" name="order_id" value="<?= $order_id ?>">
            <input type="submit" name="saveformdata" class="btn btn-sm btn-success">
        </div>
    </div>
    <?= form_close(); ?>
</div>
<script>
    $(document).ready(function(e) {});

    function getPhoto(input) {

    }
</script>