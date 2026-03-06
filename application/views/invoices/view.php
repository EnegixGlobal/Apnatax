<?php
// Simple printable invoice layout based on provided GST example
$invoice_no   = !empty($invoice['invoice_no']) ? $invoice['invoice_no'] : '';
$invoice_date = !empty($invoice['invoice_date']) ? date('d/m/Y', strtotime($invoice['invoice_date'])) : date('d/m/Y');
$billing_name = !empty($invoice['billing_name']) ? $invoice['billing_name'] : '';
$firm_name    = !empty($invoice['firm_name']) ? $invoice['firm_name'] : '';
$firm_gstin   = !empty($invoice['firm_gstin']) ? $invoice['firm_gstin'] : '';
$firm_pan     = !empty($invoice['firm_pan']) ? $invoice['firm_pan'] : '';

// If firm_name is missing, try to get it from order
if (empty($firm_name) && !empty($order['firm_name'])) {
    $firm_name = $order['firm_name'];
}
if (empty($firm_gstin) && !empty($order['firm_gstin'])) {
    $firm_gstin = $order['firm_gstin'];
}
if (empty($firm_pan) && !empty($order['firm_pan'])) {
    $firm_pan = $order['firm_pan'];
}
$subtotal     = isset($invoice['subtotal']) ? (float)$invoice['subtotal'] : 0;
$gst_rate     = isset($invoice['gst_rate']) ? (float)$invoice['gst_rate'] : 0;
$gst_amount   = isset($invoice['gst_amount']) ? (float)$invoice['gst_amount'] : 0;
$total        = isset($invoice['total_amount']) ? (float)$invoice['total_amount'] : $subtotal + $gst_amount;

// Calculate SGST/CGST or IGST based on state match
$customer_state_id = isset($customer_state_id) ? $customer_state_id : null;
$admin_state_id = isset($admin_state_id) ? $admin_state_id : null;

$sgst_amount = 0;
$cgst_amount = 0;
$igst_amount = 0;
$states_match = false;

if ($gst_amount > 0 && !empty($customer_state_id) && !empty($admin_state_id)) {
    $states_match = ($customer_state_id == $admin_state_id);

    if ($states_match) {
        // Same state: SGST 9% + CGST 9% = 18%
        $sgst_amount = round(($subtotal * 9) / 100, 2);
        $cgst_amount = round(($subtotal * 9) / 100, 2);
        // Adjust for rounding differences
        $total_gst = $sgst_amount + $cgst_amount;
        if (abs($total_gst - $gst_amount) > 0.01) {
            $diff = $gst_amount - $total_gst;
            $sgst_amount += round($diff / 2, 2);
            $cgst_amount = $gst_amount - $sgst_amount;
        }
    } else {
        // Different states: IGST 18%
        $igst_amount = $gst_amount;
    }
} else {
    // Fallback: if states not available, use IGST
    $igst_amount = $gst_amount;
}

// Package services (for package invoices)
$package_services = array();
if (!empty($package) && !empty($package['services']) && is_array($package['services'])) {
    $package_services = $package['services'];
}

function amount_in_words($number)
{
    // Very small helper for INR amount in words (rounded to integer)
    $no = round($number);
    $words = array(
        '0' => 'Zero',
        '1' => 'One',
        '2' => 'Two',
        '3' => 'Three',
        '4' => 'Four',
        '5' => 'Five',
        '6' => 'Six',
        '7' => 'Seven',
        '8' => 'Eight',
        '9' => 'Nine',
        '10' => 'Ten',
        '11' => 'Eleven',
        '12' => 'Twelve',
        '13' => 'Thirteen',
        '14' => 'Fourteen',
        '15' => 'Fifteen',
        '16' => 'Sixteen',
        '17' => 'Seventeen',
        '18' => 'Eighteen',
        '19' => 'Nineteen',
        '20' => 'Twenty',
        '30' => 'Thirty',
        '40' => 'Forty',
        '50' => 'Fifty',
        '60' => 'Sixty',
        '70' => 'Seventy',
        '80' => 'Eighty',
        '90' => 'Ninety'
    );
    $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
    $str = array();
    $i = 0;
    while ($no > 0) {
        $divider = ($i == 1) ? 10 : 100;
        $number = $no % $divider;
        $no = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number) {
            $plural = (count($str) && $number > 9) ? ' ' : null;
            $hundred = (count($str) == 1 && $str[0]) ? ' and ' : null;
            if ($number < 21) {
                $str[] = $words[$number] . ' ' . $digits[count($str)] . $plural . $hundred;
            } else {
                $str[] = $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[count($str)] . $plural . $hundred;
            }
        } else {
            $str[] = null;
        }
    }
    $str = array_reverse($str);
    $result = trim(implode('', $str));
    return $result . ' Only';
}

$amount_words = amount_in_words($total);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice <?php echo htmlspecialchars($invoice_no); ?></title>
    <style>
        /* Set page margins for Dompdf to avoid content clipping */
        @page {
            margin: 20px;
        }

        body {
            /* DejaVu Sans is bundled with Dompdf and supports the ₹ symbol */
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .invoice-container {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 16px;
            box-sizing: border-box;
        }

        h2 {
            text-align: center;
            margin: 0 0 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px 6px;
        }

        .no-border td,
        .no-border th {
            border: none;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mt-2 {
            margin-top: 8px;
        }

        .mt-3 {
            margin-top: 12px;
        }

        .invoice-logo {
            text-align: center;
            margin-bottom: 15px;
        }

        .invoice-logo img {
            max-width: 110px;
            height: auto;
            vertical-align: middle;
        }

        .company-name {
            font-size: 26px;
            font-weight: bold;
            vertical-align: middle;
            padding-left: 10px;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <div class="no-print" style="text-align:right;margin-bottom:10px;">
            <button onclick="window.print();">Print</button>
        </div>

        <div class="invoice-logo">
            <?php
            // Get absolute URL for logo (needed for PDF generation with Dompdf)
            $logo_path = FCPATH . 'assets/images/logo.png';
            if (file_exists($logo_path)) {
                // Use file_url helper which returns proper URL, then make it absolute for PDF
                $logo_url = file_url('assets/images/logo.png');
                // For PDF generation, ensure absolute URL
                if (strpos($logo_url, 'http') !== 0) {
                    // If relative URL, make it absolute
                    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
                    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
                    $logo_url = $protocol . $host . '/' . ltrim($logo_url, '/');
                }
                echo '<img src="' . htmlspecialchars($logo_url) . '" alt="ApnoTax Logo" style="display:inline-block;vertical-align:middle;" />';
            }
            ?>
            <span class="company-name" style="display:inline-block;vertical-align:middle;">ApnoTax Solutions</span>
        </div>

        <h2>TAX INVOICE</h2>

        <table class="no-border">
            <tr>
                <td><strong>Invoice No:</strong> <?php echo htmlspecialchars($invoice_no); ?></td>
                <td class="text-right">
                    <?php
                    // Show only admin GSTIN above date
                    $admin_gstin = isset($admin_gstin) ? $admin_gstin : '';
                    if (!empty($admin_gstin)) : ?>
                        <strong>GSTIN:</strong> <?php echo htmlspecialchars($admin_gstin); ?><br>
                    <?php endif; ?>
                    <strong>Date:</strong> <?php echo $invoice_date; ?>
                </td>
            </tr>
        </table>

        <table class="mt-2">
            <tr>
                <td>
                    <?php if (!empty($firm_name)) : ?>
                        <strong><?php echo htmlspecialchars($firm_name); ?></strong><br>
                        <?php if ($firm_gstin) : ?>
                            GSTIN: <?php echo htmlspecialchars($firm_gstin); ?><br>
                        <?php endif; ?>
                        <?php if ($firm_pan) : ?>
                            PAN: <?php echo htmlspecialchars($firm_pan); ?><br>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
                <td>
                    <strong>Bill To:</strong><br>
                    <?php echo htmlspecialchars($billing_name); ?><br>
                    <?php if (!empty($invoice['billing_mobile'])) : ?>
                        Mobile: <?php echo htmlspecialchars($invoice['billing_mobile']); ?><br>
                    <?php endif; ?>
                    <?php if (!empty($invoice['billing_email'])) : ?>
                        Email: <?php echo htmlspecialchars($invoice['billing_email']); ?>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <table class="mt-3">
            <thead>
                <tr>
                    <th>Sr No</th>
                    <th>Particulars (Service)</th>
                    <th>Type</th>
                    <th>Period</th>
                    <th class="text-right">Rate (&#8377;)</th>
                    <th class="text-right">GST %</th>
                    <th class="text-right">Amount (&#8377;)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($package_services)) : ?>
                    <?php
                    $sr = 0;
                    foreach ($package_services as $svc) :
                        $sr++;
                        $svc_name = !empty($svc['name']) ? $svc['name'] : (!empty($svc['service_name']) ? $svc['service_name'] : '');
                        $svc_rate = isset($svc['rate']) ? (float)$svc['rate'] : 0;
                    ?>
                        <tr>
                            <td class="text-center"><?php echo $sr; ?></td>
                            <td><?php echo htmlspecialchars($svc_name); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($invoice['type']); ?></td>
                            <td class="text-center">
                                <?php
                                if (!empty($invoice['period_value'])) {
                                    echo htmlspecialchars($invoice['period_value']);
                                } elseif (!empty($invoice['year'])) {
                                    echo htmlspecialchars($invoice['year']);
                                }
                                ?>
                            </td>
                            <td class="text-right"><?php echo number_format($svc_rate, 2); ?></td>
                            <td class="text-right"><?php echo $gst_rate > 0 ? $gst_rate . '%' : '0%'; ?></td>
                            <td class="text-right"><?php echo number_format($svc_rate, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td class="text-center">1</td>
                        <td><?php echo htmlspecialchars($invoice['service_name']); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($invoice['type']); ?></td>
                        <td class="text-center">
                            <?php
                            if (!empty($invoice['period_value'])) {
                                echo htmlspecialchars($invoice['period_value']);
                            } elseif (!empty($order['year'])) {
                                echo htmlspecialchars($order['year']);
                            }
                            ?>
                        </td>
                        <td class="text-right"><?php echo number_format($subtotal, 2); ?></td>
                        <td class="text-right"><?php echo $gst_rate > 0 ? $gst_rate . '%' : '0%'; ?></td>
                        <td class="text-right"><?php echo number_format($total, 2); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <table class="mt-2">
            <tr>
                <td class="text-right"><strong>Subtotal:</strong></td>
                <td class="text-right" style="width:120px;"><?php echo number_format($subtotal, 2); ?></td>
            </tr>
            <?php if ($states_match && ($sgst_amount > 0 || $cgst_amount > 0)) : ?>
                <tr>
                    <td class="text-right"><strong>SGST @ 9%:</strong></td>
                    <td class="text-right"><?php echo number_format($sgst_amount, 2); ?></td>
                </tr>
                <tr>
                    <td class="text-right"><strong>CGST @ 9%:</strong></td>
                    <td class="text-right"><?php echo number_format($cgst_amount, 2); ?></td>
                </tr>
            <?php elseif ($igst_amount > 0) : ?>
                <tr>
                    <td class="text-right"><strong>IGST @ <?php echo $gst_rate; ?>%:</strong></td>
                    <td class="text-right"><?php echo number_format($igst_amount, 2); ?></td>
                </tr>
            <?php else : ?>
                <tr>
                    <td class="text-right"><strong>GST @ <?php echo $gst_rate; ?>%:</strong></td>
                    <td class="text-right"><?php echo number_format($gst_amount, 2); ?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td class="text-right"><strong>Grand Total:</strong></td>
                <td class="text-right"><strong><?php echo number_format($total, 2); ?></strong></td>
            </tr>
        </table>

        <div class="mt-3">
            <strong>Total Amount (in words):</strong>
            Rupees <?php echo $amount_words; ?>
        </div>

        <table class="mt-3 no-border">
            <tr>
                <td style="width:50%;">
                    <strong>Terms &amp; Conditions:</strong>
                    <ul style="margin:4px 0 0 16px;padding:0;">
                        <li>Goods/Services once sold will not be taken back.</li>
                        <li>Interest may be charged on delayed payments.</li>
                        <li>Subject to firm jurisdiction.</li>
                    </ul>
                </td>
                <td class="text-right" style="vertical-align:bottom;">
                    For ApnoTax<br><br><br>
                    ___________________________<br>
                    Authorised Signatory
                </td>
            </tr>
        </table>
    </div>
</body>

</html>