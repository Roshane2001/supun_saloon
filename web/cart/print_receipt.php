<?php
session_start(); // Start the session
$base_path = '../';
include '../include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

if (!isset($_GET['invoice_no'])) {
    die("Invoice number is required.");
}

$invoice_no = mysqli_real_escape_string($con, $_GET['invoice_no']);

// 1. Fetch Branding Info
$branding_query = mysqli_query($con, "SELECT * FROM branding LIMIT 1");
$brand = mysqli_fetch_assoc($branding_query);

// 2. Fetch Sale Info (Join with customer and staff)
$sale_query = mysqli_query($con, "SELECT s.*, c.customer_name, c.contact_no as c_tel, st.staff_name 
                                  FROM tblsale s 
                                  JOIN tblcustomer c ON s.customer_id = c.id 
                                  LEFT JOIN tblstaff st ON s.created_by = st.id 
                                  WHERE s.invoice_no = '$invoice_no'");
$sale = mysqli_fetch_assoc($sale_query);

if (!$sale) {
    die("Sale record not found.");
}

// 3. Fetch Sale Items
$items_query = mysqli_query($con, "SELECT * FROM tblsale_items WHERE sale_id = '{$sale['id']}'");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - <?php echo $invoice_no; ?></title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #fff;
            -webkit-print-color-adjust: exact;
        }
        .receipt-main {
            width: 72mm; /* Standard safe printable width for 80mm paper */
            background: #fff;
            padding: 2mm;
            margin: 0 auto;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .header { margin-bottom: 15px; }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 18px; }
        .header p { margin: 2px 0; font-size: 13px; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        .info-table, .items-table, .totals-table { width: 100%; border-collapse: collapse; }
        .info-table td { font-size: 13px; padding: 1px 0; }
        .items-table th { border-bottom: 1px solid #000; text-align: left; font-size: 13px; padding-bottom: 2px; }
        .items-table td { padding: 3px 0; vertical-align: top; font-size: 13px; }
        .items-table td small { font-size: 11px; }
        .totals-table td { padding: 1px 0; font-size: 13px; }
        .grand-total { font-size: 16px; border-top: 1px solid #000; padding-top: 5px; }
        
        @media print {
            body { background: none; padding: 0; }
            .receipt-main { box-shadow: none; margin: 0 auto; width: 72mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <script>
        window.onload = function() {
            window.print();
        };
        // Automatically close the tab/window after the print dialog is handled
        window.onafterprint = function() {
            window.close();
        };
    </script>

    <div class="receipt-main">
        <!-- Header -->
        <div class="header text-center">
            <h2><?php echo htmlspecialchars($brand['company_name']); ?></h2>
            <p><?php echo htmlspecialchars($brand['address']); ?></p>
            <p>Tel: <?php echo htmlspecialchars($brand['telephone']); ?></p>
            <p><?php echo htmlspecialchars($brand['company_email']); ?></p>
        </div>

        <div class="divider"></div>

        <!-- Sale Info -->
        <table class="info-table">
            <tr>
                <td>Invoice No:</td>
                <td class="text-right bold"><?php echo $sale['invoice_no']; ?></td>
            </tr>
            <tr>
                <td>Date:</td>
                <td class="text-right"><?php echo date('Y-m-d H:i', strtotime($sale['sale_date'])); ?></td>
            </tr>
            <tr>
                <td>Cashier:</td>
                <td class="text-right"><?php echo htmlspecialchars($sale['staff_name'] ?? 'System'); ?></td>
            </tr>
            <tr>
                <td>Customer:</td>
                <td class="text-right"><?php echo htmlspecialchars($sale['customer_name']); ?></td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while($item = mysqli_fetch_assoc($items_query)): ?>
                <tr>
                    <td>
                        <?php echo htmlspecialchars($item['product_name']); ?><br>
                        <small><?php echo number_format($item['quantity'], 2); ?> x <?php echo number_format($item['unit_price'], 2); ?></small>
                    </td>
                    <td class="text-right"><?php echo number_format($item['line_total'], 2); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- Totals -->
        <table class="totals-table">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right"><?php echo number_format($sale['subtotal'], 2); ?></td>
            </tr>
            <?php if($sale['discount_amount'] > 0): ?>
            <tr>
                <td>Discount:</td>
                <td class="text-right">-<?php echo number_format($sale['discount_amount'], 2); ?></td>
            </tr>
            <?php endif; ?>
            <tr class="grand-total bold">
                <td>Grand Total:</td>
                <td class="text-right">LKR <?php echo number_format($sale['grand_total'], 2); ?></td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top:10px;"></td>
            </tr>
            <tr>
                <td>Paid via <?php echo $sale['payment_method']; ?>:</td>
                <td class="text-right"><?php echo number_format($sale['amount_received'], 2); ?></td>
            </tr>
            <tr>
                <td>Change:</td>
                <td class="text-right"><?php echo number_format($sale['change_amount'], 2); ?></td>
            </tr>
        </table>

        <div class="divider"></div>

        <div class="header text-center" style="margin-top: 20px;">
            <p class="bold">THANK YOU FOR YOUR BUSINESS!</p>
            <p>Please come again.</p>
            <p>FEIT Solutions</p>
        </div>
    </div>

</body>
</html>