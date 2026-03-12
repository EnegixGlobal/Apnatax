<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayU Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }
        .payment-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        .logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 20px;
        }
        .loading {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        h2 {
            color: #333;
            margin-bottom: 10px;
        }
        p {
            color: #666;
            margin-bottom: 20px;
        }
        .amount-display {
            font-size: 24px;
            font-weight: bold;
            color: #3498db;
            margin: 20px 0;
        }
        .btn-pay {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
        .btn-pay:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <?php 
        $logo_path = file_url('assets/images/logo.png');
        if (file_exists(FCPATH . 'assets/images/logo.png')): 
        ?>
            <img src="<?= $logo_path; ?>" alt="Logo" class="logo">
        <?php endif; ?>
        <div class="loading"></div>
        <h2>Redirecting to PayU...</h2>
        <p>Please wait while we redirect you to the secure payment gateway.</p>
        <div class="amount-display">Amount: ₹<?= htmlspecialchars(number_format($amount, 2), ENT_QUOTES, 'UTF-8'); ?></div>
        
        <form id="payu-form" method="post" action="<?= htmlspecialchars($payment_url, ENT_QUOTES, 'UTF-8'); ?>">
            <?php foreach ($payment_params as $key => $value): ?>
                <input type="hidden" name="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>" value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>">
            <?php endforeach; ?>
            <button type="submit" class="btn-pay">Proceed to Payment</button>
        </form>
        
        <script>
            // Auto-submit form after 2 seconds
            setTimeout(function() {
                document.getElementById('payu-form').submit();
            }, 2000);
        </script>
    </div>
</body>
</html>

