<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Payment Status', ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6fb;
            color: #1f2937;
        }
        .wrapper {
            max-width: 760px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        .header {
            padding: 24px 28px;
            color: #fff;
        }
        .header.success { background: #16a34a; }
        .header.failure { background: #dc2626; }
        .content {
            padding: 24px 28px;
        }
        .message {
            margin: 0 0 18px;
            line-height: 1.5;
        }
        .details {
            margin-top: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }
        .row {
            display: flex;
            border-bottom: 1px solid #e5e7eb;
        }
        .row:last-child {
            border-bottom: none;
        }
        .label, .value {
            padding: 10px 12px;
            font-size: 14px;
        }
        .label {
            width: 220px;
            background: #f9fafb;
            font-weight: bold;
            color: #374151;
        }
        .value {
            flex: 1;
            word-break: break-word;
        }
        .redirect-note {
            margin-top: 18px;
            font-size: 14px;
            color: #4b5563;
        }
        .btn {
            display: inline-block;
            margin-top: 14px;
            background: #2563eb;
            color: #fff;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 14px;
        }
    </style>
</head>
<body>
<?php
$isSuccess = !empty($is_success);
$redirectUrl = !empty($redirect_url) ? $redirect_url : base_url('mywallet/');
$redirectSeconds = !empty($redirect_seconds) ? (int) $redirect_seconds : 6;
$details = !empty($details) && is_array($details) ? $details : [];
?>
    <div class="wrapper">
        <div class="header <?= $isSuccess ? 'success' : 'failure'; ?>">
            <h2 style="margin:0;"><?= htmlspecialchars($title ?? 'Payment Status', ENT_QUOTES, 'UTF-8'); ?></h2>
        </div>
        <div class="content">
            <p class="message"><?= htmlspecialchars($message ?? 'Payment status updated.', ENT_QUOTES, 'UTF-8'); ?></p>

            <?php if (!empty($details)): ?>
                <div class="details">
                    <?php foreach ($details as $item): ?>
                        <div class="row">
                            <div class="label"><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="value"><?= htmlspecialchars($item['value'], ENT_QUOTES, 'UTF-8'); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <p class="redirect-note">
                You will be redirected in <?= (int) $redirectSeconds; ?> seconds.
            </p>
            <a class="btn" href="<?= htmlspecialchars($redirectUrl, ENT_QUOTES, 'UTF-8'); ?>">Continue</a>
        </div>
    </div>

    <script>
        setTimeout(function () {
            window.location.href = <?= json_encode($redirectUrl); ?>;
        }, <?= ((int) $redirectSeconds * 1000); ?>);
    </script>
</body>
</html>
