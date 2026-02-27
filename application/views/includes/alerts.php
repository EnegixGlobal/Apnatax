<?php
// Simple reusable alerts partial for flash messages
$msg     = $this->session->flashdata('msg');
$err_msg = $this->session->flashdata('err_msg');
?>

<?php if (!empty($msg)) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($err_msg)) : ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $err_msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>


