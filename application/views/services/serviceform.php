<?php
$button = '';
$status = '<span class="text-danger">Pending</span>';
if ($order['status'] == 2) {
    $status = '<span class="text-warning">Documents Uploaded!</span>';
} elseif ($order['status'] == 3) {
    $status = '<span class="text-info">Accepted for Assessment!</span>';
} elseif ($order['status'] == 4) {
    $status = '<span class="text-success">Assessment Done and Report Uploaded!</span>';
}
?>
<div class="card-body">
    <?= form_open_multipart('services/saveformdata/'); ?>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="form-group">
                <?php
                echo create_form_input('text', '', "Service Name", true, $order['service_name'], ['readonly' => 'true']);
                ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <?php
                echo create_form_input('text', '', "Date", true, date('d-m-Y', strtotime($order['added_on'])), ['readonly' => 'true']);
                ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <?php
                echo create_form_input('text', '', "Firm Name", true, $firm['name'], ['readonly' => 'true']);
                ?>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <?php
        if ($order['purchased_type'] == 'Monthly') {
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
        } elseif ($order['purchased_type'] == 'Quarterly') {
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
                        echo create_form_input($type, $name, $label, true, $value, $attr);

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
            <input type="hidden" name="slug" value="<?= $order['service_slug'] ?>">
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
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