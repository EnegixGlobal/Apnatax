<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Wallet Top-up - Razorpay</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f7fb;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 32px 28px;
            max-width: 420px;
            width: 100%;
            text-align: center;
        }

        h1 {
            margin-top: 0;
            margin-bottom: 8px;
            font-size: 22px;
            color: #1f2933;
        }

        p {
            margin-top: 0;
            margin-bottom: 6px;
            color: #6b7380;
            font-size: 14px;
        }

        .amount {
            font-size: 28px;
            font-weight: 600;
            margin: 16px 0 8px;
            color: #111827;
        }

        .meta {
            font-size: 12px;
            color: #9ca3af;
            margin-bottom: 24px;
        }

        .btn-pay {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 999px;
            border: none;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.35);
            transition: background 0.15s ease, transform 0.1s ease, box-shadow 0.1s ease;
        }

        .btn-pay:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.45);
        }

        .btn-pay:active {
            transform: translateY(0);
            box-shadow: 0 6px 14px rgba(37, 99, 235, 0.35);
        }

        .note {
            margin-top: 18px;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Complete Wallet Top-up</h1>
        <p>Secure payment powered by Razorpay</p>

        <div class="amount">
            ₹<?= htmlspecialchars(number_format((float)$display_amount, 2), ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <div class="meta">
            Order: <?= htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8'); ?>
        </div>

        <button id="rzp-button1" class="btn-pay">Pay with Razorpay</button>

        <div class="note">
            You will be redirected after payment is completed.
        </div>
    </div>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        (function() {
            // Project logo used in Razorpay Checkout
            // Make sure this URL is accessible in the browser (open it directly to confirm)
            var logoUrl = "<?= htmlspecialchars(base_url('images/logo2.png'), ENT_QUOTES, 'UTF-8'); ?>";

            var options = {
                "key": "<?= htmlspecialchars($razorpay_key_id, ENT_QUOTES, 'UTF-8'); ?>",
                "amount": "<?= (int)$amount_paise; ?>",
                "currency": "<?= htmlspecialchars($currency, ENT_QUOTES, 'UTF-8'); ?>",
                "name": "<?= htmlspecialchars(PROJECT_NAME, ENT_QUOTES, 'UTF-8'); ?>",
                "description": "Wallet Top-up",
                "image": logoUrl,
                "order_id": "<?= htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8'); ?>",
                "callback_url": "<?= htmlspecialchars($callback_url, ENT_QUOTES, 'UTF-8'); ?>",
                "prefill": {
                    "name": "<?= htmlspecialchars(!empty($user['name']) ? $user['name'] : '', ENT_QUOTES, 'UTF-8'); ?>",
                    "email": "<?= htmlspecialchars(!empty($user['email']) ? $user['email'] : '', ENT_QUOTES, 'UTF-8'); ?>",
                    "contact": "<?= htmlspecialchars(!empty($user['mobile']) ? $user['mobile'] : '', ENT_QUOTES, 'UTF-8'); ?>"
                },
                "notes": {
                    "wallet_txn_id": "<?= htmlspecialchars($merchant_txn_id, ENT_QUOTES, 'UTF-8'); ?>"
                },
                "theme": {
                    "color": "#2563eb"
                }
            };

            var rzp1 = new Razorpay(options);

            rzp1.on('payment.failed', function(response) {
                alert('Payment failed: ' + (response.error && response.error.description ? response.error.description : 'Please try again.'));
            });

            var btn = document.getElementById('rzp-button1');
            if (btn) {
                btn.addEventListener('click', function(e) {
                    rzp1.open();
                    e.preventDefault();
                });
            }

            // Auto-open for smoother UX
            window.onload = function() {
                rzp1.open();
            };
        })();
    </script>
</body>

</html>