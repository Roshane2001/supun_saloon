<?php
session_start();
$base_path = '../';
include $base_path . 'include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Log page access for auditing
$user_id = $_SESSION['staff_id'];
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
$details = "Staff member " . ($_SESSION['staff_name'] ?? 'Unknown') . " accessed the POS terminal.";
$log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
              VALUES ('$user_id', 'Accessed POS Page', '$ip_address', '$user_agent', '$details')";
mysqli_query($con, $log_query);

// Start session to get staff_id (assuming staff is logged in)
//session_start();
$current_cashier_id = $_SESSION['staff_id'] ?? 1; // Default to 1 if not set
$current_cashier_name = $_SESSION['staff_name'] ?? 'Cashier'; // Default to 'Cashier'

// Fetch active parent categories for filters
$parents_query = mysqli_query($con, "SELECT id, parent_category_name FROM tblparent_category ORDER BY parent_category_name ASC");
$parent_categories = [];
while ($row = mysqli_fetch_assoc($parents_query)) {
    $parent_categories[] = $row;
}

// Fetch active customers for selection
$customers_query = mysqli_query($con, "SELECT id, customer_name FROM tblcustomer WHERE is_active = 1 ORDER BY customer_name ASC");
$customers = [];
while ($row = mysqli_fetch_assoc($customers_query)) {
    $customers[] = $row;
}

// Generate a simple invoice number (e.g., POS-YYYYMMDD-XXXX)
$invoice_prefix = "POS-" . date("Ymd");
$last_invoice_query = mysqli_query($con, "SELECT invoice_no FROM tblsale WHERE invoice_no LIKE '$invoice_prefix%' ORDER BY id DESC LIMIT 1");
$last_invoice_no = mysqli_fetch_assoc($last_invoice_query)['invoice_no'] ?? '';

$next_sequence = 1;
if ($last_invoice_no) {
    $parts = explode('-', $last_invoice_no);
    if (count($parts) == 3) {
        $next_sequence = (int)$parts[2] + 1;
    }
}
$invoice_no = $invoice_prefix . '-' . str_pad($next_sequence, 4, '0', STR_PAD_LEFT);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../include/header.php'; ?>
    <title>POS Cart</title>
    <!-- Select2 CSS -->
    <link href="<?php echo $base_path; ?>assets/js/plugin/select2/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
    <style>
        body {
            background-color: #f5f7fd;
            overflow: hidden; /* Prevent body scroll */
        }
        .wrapper {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .main-panel {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            margin-left: 0; /* Override sidebar margin */
        }
        .main-header {
            position: relative; /* Not sticky to allow custom sticky header */
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            background-color: #fff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .pos-header-info {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .pos-header-info div {
            font-size: 0.9rem;
            color: #555;
        }
        .pos-header-info .invoice-no {
            font-weight: bold;
            color: #1572E8;
        }
        .pos-container {
            display: flex;
            flex: 1;
            overflow: hidden; /* Hide scrollbars on main container */
        }
        .left-column, .right-column {
            height: 100%;
            overflow-y: auto; /* Enable scrolling for columns */
            padding: 15px;
            background-color: #f5f7fd;
        }
        .left-column {
            flex: 0 0 65%;
            max-width: 65%;
            border-right: 1px solid #e9ecef;
        }
        .right-column {
            flex: 0 0 35%;
            max-width: 35%;
            display: flex;
            flex-direction: column;
        }
        .card {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 15px;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
            padding-top: 15px;
        }
        .product-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            text-align: center;
            background-color: #fff;
            transition: transform 0.2s ease-in-out;
            cursor: pointer;
        }
        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .product-card-img {
            height: 120px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #ccc;
            border-bottom: 1px solid #e9ecef;
        }
        .product-card-body {
            padding: 10px;
        }
        .product-card-name {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .product-card-code {
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .product-card-price {
            font-weight: bold;
            font-size: 1.1rem;
            color: #1572E8;
            margin-bottom: 10px;
        }
        .stock-badge {
            font-size: 0.75rem;
            padding: 3px 8px;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 5px;
        }
        .stock-badge.in-stock { background-color: #28a745; color: #fff; }
        .stock-badge.low-stock { background-color: #ffc107; color: #333; }
        .stock-badge.out-of-stock { background-color: #dc3545; color: #fff; }
        .add-to-cart-btn {
            width: 100%;
            padding: 8px;
            font-size: 0.9rem;
            border-radius: 5px;
        }

        /* Cart styles */
        .cart-table-container {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 15px;
        }
        .cart-table th, .cart-table td {
            vertical-align: middle;
            font-size: 0.9rem;
        }
        .cart-qty-control {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .cart-qty-control input {
            width: 50px;
            text-align: center;
            padding: 0.25rem;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        .cart-qty-control .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
        .cart-remove-btn {
            color: #dc3545;
            font-size: 1.1rem;
        }
        .order-summary .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1.25rem;
        }
        .order-summary .list-group-item strong {
            font-size: 1.2rem;
        }
        .order-summary .grand-total {
            background-color: #1572E8;
            color: #fff;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .payment-card .form-group {
            margin-bottom: 10px;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        .action-buttons .btn {
            flex: 1;
            margin-bottom: 0;
        }
        .footer-shortcut-bar {
            background-color: #343a40;
            color: #fff;
            padding: 8px 20px;
            font-size: 0.85rem;
            display: flex;
            justify-content: space-around;
            align-items: center;
            position: sticky;
            bottom: 0;
            width: 100%;
            z-index: 1000;
        }
        .footer-shortcut-bar span {
            margin-right: 15px;
        }
        .footer-shortcut-bar kbd {
            background-color: #6c757d;
            color: #fff;
            padding: 2px 5px;
            border-radius: 3px;
        }

        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            .pos-container {
                flex-direction: column;
            }
            .left-column, .right-column {
                flex: 1 1 100%;
                max-width: 100%;
                border-right: none;
                border-bottom: 1px solid #e9ecef;
            }
            .right-column {
                order: 1; /* Cart on top for smaller screens */
            }
            .left-column {
                order: 2;
            }
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .pos-header-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
            .pos-header-info div {
                font-size: 0.8rem;
            }
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }
            .product-card-img {
                height: 80px;
            }
            .product-card-name {
                font-size: 0.85rem;
            }
            .product-card-price {
                font-size: 1rem;
            }
            .add-to-cart-btn {
                font-size: 0.8rem;
                padding: 6px;
            }
            .cart-table th, .cart-table td {
                font-size: 0.8rem;
            }
            .cart-qty-control input {
                width: 40px;
            }
            .order-summary .list-group-item {
                padding: 0.5rem 1rem;
            }
            .order-summary .list-group-item strong {
                font-size: 1rem;
            }
            .order-summary .grand-total {
                font-size: 1.2rem;
            }
            .footer-shortcut-bar {
                flex-wrap: wrap;
                justify-content: center;
                gap: 10px;
                padding: 5px 10px;
            }
            .footer-shortcut-bar span {
                margin-right: 5px;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Main Header (Custom for POS) -->
        <div class="main-header">
            <div class="pos-header-info">
                <div class="invoice-no">Invoice: <?php echo htmlspecialchars($invoice_no); ?></div>
                <div>Date: <span id="currentDate"><?php echo date("Y-m-d"); ?></span></div>
                <div>Time: <span id="currentTime"><?php echo date("H:i:s"); ?></span></div>
                <div>Cashier: <?php echo htmlspecialchars($current_cashier_name); ?></div>
                <!-- <div>Branch: Main Branch</div> -->
            </div>
            <div class="pos-header-actions">
                <button class="btn btn-sm btn-icon btn-light me-2" title="Fullscreen" onclick="toggleFullscreen()">
                    <i class="fas fa-expand"></i>
                </button>
                <a href="<?php echo $base_path; ?>../auth/logout.php" class="btn btn-sm btn-icon btn-danger" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>

        <div class="pos-container">
            <!-- Left Column: Product Selection -->
            <div class="left-column">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-6">
                                <div class="form-group form-group-default">
                                    <label for="customer_id">Customer</label>
                                    <select class="form-select select2" id="customer_id" name="customer_id" style="width: 100%;">
                                        <option value="1" selected>Walk-in Customer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                                    <i class="fas fa-user-plus"></i> Add New Customer
                                </button>
                                <button class="btn btn-secondary btn-sm" id="viewHoldSalesBtn">
                                    <i class="fas fa-list"></i> Held Sales
                                </button>
                            </div>
                        </div>
                        <div class="form-group form-group-default">
                            <label for="product_search">Search Product</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                <input type="text" class="form-control" id="product_search" placeholder="Scan barcode or search product..." autofocus>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group form-group-default">
                                    <label for="parent_category_filter">Parent Category</label>
                                    <select class="form-select" id="parent_category_filter">
                                        <option value="">All Parent Categories</option>
                                        <?php foreach ($parent_categories as $parent) : ?>
                                            <option value="<?php echo $parent['id']; ?>"><?php echo htmlspecialchars($parent['parent_category_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-group-default">
                                    <label for="sub_category_filter">Sub Category</label>
                                    <select class="form-select" id="sub_category_filter">
                                        <option value="">All Sub Categories</option>
                                        <!-- Populated via AJAX -->
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="product-grid" id="productGrid">
                    <!-- Product cards will be loaded here via JavaScript -->
                    <div class="text-center text-muted p-5" id="loadingSpinner" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading products...</p>
                    </div>
                    <div class="text-center text-muted p-5" id="noProductsFound" style="display: none;">
                        <p>No products found matching your criteria.</p>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <button class="btn btn-light btn-sm" id="loadMoreProducts" style="display: none;">Load More</button>
                </div>
            </div>

            <!-- Right Column: Cart and Payment -->
            <div class="right-column">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Shopping Cart (<span id="cartItemCount">0</span> items)</h5>
                        <button class="btn btn-danger btn-sm" id="clearCartBtn"><i class="fas fa-trash"></i> Clear Cart</button>
                    </div>
                    <div class="card-body p-0 cart-table-container">
                        <table class="table table-striped table-hover cart-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cartTableBody">
                                <!-- Cart items will be loaded here -->
                                <tr>
                                    <td colspan="5" class="text-center text-muted" id="emptyCartMessage">Cart is empty. Add products!</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card order-summary">
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Total Items <span id="summaryTotalItems">0</span></li>
                            <li class="list-group-item">Total Quantity <span id="summaryTotalQty">0.00</span></li>
                            <li class="list-group-item">Subtotal <span id="summarySubtotal">0.00</span></li>
                            <li class="list-group-item">Discount <span id="summaryDiscount">0.00</span></li>
                            <li class="list-group-item">Tax <span id="summaryTax">0.00</span></li>
                            <li class="list-group-item grand-total">Grand Total <strong id="summaryGrandTotal">0.00</strong></li>
                        </ul>
                    </div>
                </div>

                <div class="card payment-card">
                    <div class="card-body">
                        <div class="form-group form-group-default">
                            <label for="payment_method">Payment Method</label>
                            <select class="form-select" id="payment_method">
                                <option value="Cash">Cash</option>
                                <option value="Card">Card</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Credit">Credit</option>
                            </select>
                        </div>
                        <div class="form-group form-group-default">
                            <label for="amount_received">Amount Received</label>
                            <input type="number" step="0.01" class="form-control" id="amount_received" value="0.00">
                        </div>
                        <div class="form-group form-group-default">
                            <label for="change_amount">Change Amount</label>
                            <input type="text" class="form-control" id="change_amount" value="0.00" readonly>
                        </div>
                        <div class="form-group form-group-default">
                            <label for="due_amount">Due Amount</label>
                            <input type="text" class="form-control" id="due_amount" value="0.00" readonly>
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="btn btn-warning" id="holdSaleBtn"><i class="fas fa-pause"></i> Hold Sale</button>
                    <button class="btn btn-info" id="printReceiptBtn"><i class="fas fa-print"></i> Print Receipt</button>
                    <button class="btn btn-success" id="completeSaleBtn"><i class="fas fa-check-circle"></i> Complete Sale</button>
                    <button class="btn btn-danger" id="cancelSaleBtn"><i class="fas fa-times-circle"></i> Cancel Sale</button>
                </div>
            </div>
        </div>

        <!-- Footer Shortcut Bar -->
        <div class="footer-shortcut-bar">
            <span><kbd>F2</kbd> Customer Search</span>
            <span><kbd>F3</kbd> Product Search</span>
            <span><kbd>F4</kbd> Complete Sale</span>
            <span><kbd>ESC</kbd> Clear Cart</span>
        </div>
    </div>

    <!-- Held Sales Modal -->
    <div class="modal fade" id="heldSalesModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Held Transactions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead><tr><th>Reference</th><th>Customer</th><th>Total</th><th>Action</th></tr></thead>
                        <tbody id="heldSalesTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <?php include 'add_customer_modal.php'; ?>

    <?php include "../include/script.php"; ?>
    <script src="<?php echo $base_path; ?>assets/js/plugin/select2/select2.full.min.js"></script>
    <script>
        let cart = [];
        let currentPage = 1;
        const productsPerPage = 20; // Number of products to load per page

        // Function to update current time in header
        function updateTime() {
            const now = new Date();
            document.getElementById('currentDate').innerText = now.toISOString().slice(0, 10);
            document.getElementById('currentTime').innerText = now.toLocaleTimeString();
        }
        setInterval(updateTime, 1000);
        updateTime(); // Initial call

        // Fullscreen toggle
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        // --- Product Loading and Filtering ---
        // Initialize Select2 for customer dropdown
        $('#customer_id').select2({
            theme: "bootstrap", // Assuming you are using Bootstrap theme for Select2
            placeholder: 'Search customer by name or phone',
            allowClear: true,
            ajax: {
                url: 'get_customers.php', // New file to fetch customers
                dataType: 'json',
                delay: 250, // milliseconds
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 10) < data.total_count // Assuming 10 items per page
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 1, // Minimum characters to type before searching
            templateResult: function (customer) { return customer.text + (customer.contact_no ? ' (' + customer.contact_no + ')' : ''); },
            templateSelection: function (customer) { return customer.text; }
        });
        function loadProducts(page = 1, append = false) {
            $('#loadingSpinner').show();
            $('#noProductsFound').hide();
            $('#loadMoreProducts').hide();

            const searchTerm = $('#product_search').val();
            const parentCatId = $('#parent_category_filter').val();
            const subCatId = $('#sub_category_filter').val();

            $.ajax({
                url: 'get_products.php',
                method: 'GET',
                data: {
                    page: page,
                    limit: productsPerPage,
                    search: searchTerm,
                    parent_category_id: parentCatId,
                    sub_category_id: subCatId
                },
                dataType: 'json',
                success: function(response) {
                $('#loadingSpinner').hide();
                    if (!append) {
                        $('#productGrid').empty();
                    }

                    if (response.products.length > 0) {
                        response.products.forEach(product => {
                            const stockStatusClass = product.stock_status ? product.stock_status.toLowerCase().replace(/\s/g, '-') : 'not-set';
                            const stockBadgeText = product.stock_status || 'Not Set';
                            const stockQty = parseFloat(product.quantity_on_hand || 0);
                            const isOutOfStock = stockQty <= 0;

                            const productCard = `
                                <div class="product-card ${isOutOfStock ? 'disabled' : ''}" data-product='${JSON.stringify(product)}'>
                                    <div class="product-card-img">
                                        ${product.image ? 
                                            `<img src="../assets/img/products/${product.image}" alt="${product.product_name}" style="width: 100%; height: 100%; object-fit: cover;">` : 
                                            '<i class="fas fa-image"></i>'}
                                    </div>
                                    <div class="product-card-body">
                                        <div class="product-card-name" title="${product.product_name}">${product.product_name}</div>
                                        <div class="product-card-code">${product.product_code}</div>
                                        <div class="stock-badge ${stockStatusClass}">${stockBadgeText}: ${stockQty}</div>
                                        <div class="product-card-price">LKR ${parseFloat(product.selling_price).toFixed(2)}</div>
                                        <button class="btn btn-primary btn-sm add-to-cart-btn" ${isOutOfStock ? 'disabled' : ''}>
                                            <i class="fas fa-cart-plus"></i> Add to Cart
                                        </button>
                                    </div>
                                </div>
                            `;
                            $('#productGrid').append(productCard);
                        });

                        if (response.products.length === productsPerPage) {
                            $('#loadMoreProducts').show();
                            currentPage++;
                        } else {
                            $('#loadMoreProducts').hide();
                        }
                    } else if (!append) {
                        $('#noProductsFound').show();
                    }
                },
                error: function(xhr, status, error) {
                    //$('#loadingSpinner').hide();
                    $('#productGrid').empty();
                    $('#noProductsFound').show().text('Error loading products: ' + xhr.responseText);
                    console.error("Error loading products:", error);
                }
            });
        }

        // Load initial products
        $(document).ready(function() {
            loadProducts();
        });

        // Event listeners for product search and filters
        $('#product_search').on('keyup', function() {
            currentPage = 1;
            loadProducts(currentPage);
        });

        $('#parent_category_filter').on('change', function() {
            const parentId = $(this).val();
            $('#sub_category_filter').empty().append('<option value="">All Sub Categories</option>');
            if (parentId) {
                $.ajax({
                    url: '../product/get_sub_categories.php', // Use existing API
                    method: 'POST',
                    data: { parent_id: parentId },
                    success: function(html) {
                        $('#sub_category_filter').append(html);
                    }
                });
            }
            currentPage = 1;
            loadProducts(currentPage);
        });

        $('#sub_category_filter').on('change', function() {
            currentPage = 1;
            loadProducts(currentPage);
        });

        $('#loadMoreProducts').on('click', function() {
            loadProducts(currentPage, true);
        });

        // --- Cart Management ---
        $('#productGrid').on('click', '.add-to-cart-btn', function() {
            const productData = $(this).closest('.product-card').data('product');
            const productId = productData.id;
            const productName = productData.product_name;
            const productCode = productData.product_code;
            const unitPrice = parseFloat(productData.selling_price);
            const purchasePrice = parseFloat(productData.purchase_price || 0); // Get purchase price from stock
            const currentStock = parseFloat(productData.quantity_on_hand || 0);

            if (currentStock <= 0) {
                swal("Out of Stock!", `${productName} is currently out of stock.`, "warning");
                return;
            }

            const existingItemIndex = cart.findIndex(item => item.product_id == productId);

            if (existingItemIndex > -1) {
                // Item already in cart, increment quantity
                if (cart[existingItemIndex].quantity + 1 > currentStock) {
                    swal("Limit Reached!", `Cannot add more than available stock (${currentStock}) for ${productName}.`, "warning");
                    return;
                }
                cart[existingItemIndex].quantity++;
            } else {
                // Add new item to cart
                cart.push({
                    product_id: productId,
                    product_name: productName,
                    product_code: productCode,
                    unit_price: unitPrice,
                    purchase_price: purchasePrice, // Store purchase price for sale_items
                    quantity: 1,
                    line_total: unitPrice,
                    max_stock: currentStock // Store max stock for validation
                });
            }
            updateCartUI();
        });

        // Decrease quantity when '-' is clicked
        $('#cartTableBody').on('click', '.qty-minus', function() {
            const productId = $(this).data('id');
            const itemIndex = cart.findIndex(item => item.product_id == productId);
            if (itemIndex > -1) {
                if (cart[itemIndex].quantity > 1) {
                    cart[itemIndex].quantity--;
                } else {
                    cart.splice(itemIndex, 1); // Remove the item if quantity reaches 0
                }
                updateCartUI();
            }
        });

        // Increase quantity when '+' is clicked
        $('#cartTableBody').on('click', '.qty-plus', function() {
            const productId = $(this).data('id');
            const itemIndex = cart.findIndex(item => item.product_id == productId);
            if (itemIndex > -1) {
                if (cart[itemIndex].quantity + 1 > cart[itemIndex].max_stock) {
                    swal("Limit Reached!", `Cannot add more than available stock (${cart[itemIndex].max_stock}) for ${cart[itemIndex].product_name}.`, "warning");
                    return;
                }
                cart[itemIndex].quantity++;
                updateCartUI();
            }
        });

        // Update quantity when numeric input is changed manually
        $('#cartTableBody').on('change', '.qty-input', function() {
            const productId = $(this).data('id');
            const newQty = parseInt($(this).val());
            const itemIndex = cart.findIndex(item => item.product_id == productId);

            if (itemIndex > -1 && !isNaN(newQty) && newQty >= 0) {
                if (newQty > cart[itemIndex].max_stock) {
                    swal("Limit Reached!", `Cannot add more than available stock (${cart[itemIndex].max_stock}) for ${cart[itemIndex].product_name}.`, "warning");
                    $(this).val(cart[itemIndex].quantity); // Revert to old quantity
                    return;
                }
                cart[itemIndex].quantity = newQty;
                if (newQty === 0) {
                    cart.splice(itemIndex, 1);
                }
                updateCartUI();
            } else {
                $(this).val(cart[itemIndex].quantity); // Revert to old quantity if invalid
            }
        });

        $('#cartTableBody').on('click', '.remove-item', function() {
            const productId = $(this).data('id');
            swal({
                title: "Are you sure?",
                text: "Do you want to remove this item from the cart?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    cart = cart.filter(item => item.product_id != productId);
                    updateCartUI();
                    swal("Item removed!", {
                        icon: "success",
                    });
                }
            });
        });

        $('#clearCartBtn').on('click', function() {
            if (cart.length === 0) {
                swal("Cart is already empty!", "", "info");
                return;
            }
            swal({
                title: "Clear Cart?",
                text: "All items will be removed from the cart.",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willClear) => {
                if (willClear) {
                    cart = [];
                    updateCartUI();
                    swal("Cart Cleared!", {
                        icon: "success",
                    });
                }
            });
        });

        function updateCartUI() {
            const cartTableBody = $('#cartTableBody');
            cartTableBody.empty();

            let totalItems = 0;
            let totalQty = 0;
            let subtotal = 0;
            const discount = 0; // Placeholder for now, if you add discount functionality later
            let grandTotal = 0;

            if (cart.length === 0) {
                // If cart is empty, display a message
                cartTableBody.append('<tr><td colspan="5" class="text-center text-muted" id="emptyCartMessage">Cart is empty. Add products!</td></tr>');
            } else {
                cart.forEach(item => {
                    item.line_total = item.quantity * item.unit_price;
                    totalItems++;
                    totalQty += item.quantity;
                    subtotal += item.line_total;

                    const row = `
                        <tr>
                            <td>${item.product_name}</td>
                            <td>
                                <div class="cart-qty-control">
                                    <button class="btn btn-sm btn-outline-secondary qty-minus" data-id="${item.product_id}"><i class="fas fa-minus"></i></button>
                                    <input type="number" class="qty-input" value="${item.quantity}" data-id="${item.product_id}" min="1" max="${item.max_stock}">
                                    <button class="btn btn-sm btn-outline-secondary qty-plus" data-id="${item.product_id}"><i class="fas fa-plus"></i></button>
                                </div>
                            </td>
                            <td>${item.unit_price.toFixed(2)}</td>
                            <td>${item.line_total.toFixed(2)}</td>
                            <td>
                                <button class="btn btn-link btn-danger remove-item" data-id="${item.product_id}"><i class="fas fa-times"></i></button>
                            </td>
                        </tr>
                    `;
                    cartTableBody.append(row);
                });
            }

            grandTotal = subtotal - discount;

            $('#cartItemCount').text(totalItems);
            $('#summaryTotalItems').text(totalItems);
            $('#summaryTotalQty').text(totalQty.toFixed(2));
            $('#summarySubtotal').text(subtotal.toFixed(2));
            $('#summaryDiscount').text(discount.toFixed(2));
            $('#summaryGrandTotal').text(grandTotal.toFixed(2));

            // Update payment calculations
            updatePaymentCalculations();
        }

        // --- Payment Calculations ---
        $('#amount_received').on('keyup change', updatePaymentCalculations);

        function updatePaymentCalculations() {
            const grandTotal = parseFloat($('#summaryGrandTotal').text());
            const amountReceived = parseFloat($('#amount_received').val()) || 0;
            let changeAmount = 0;
            let dueAmount = 0;

            if (amountReceived >= grandTotal) {
                changeAmount = amountReceived - grandTotal;
            } else {
                dueAmount = grandTotal - amountReceived;
            }

            $('#change_amount').val(changeAmount.toFixed(2));
            $('#due_amount').val(dueAmount.toFixed(2));
        }

        // --- Hold Sale ---
        $('#holdSaleBtn').on('click', function() {
            if (cart.length === 0) return swal("Empty Cart", "No items to hold.", "warning");

            swal({
                title: "Hold Sale?",
                text: "Enter a reference name/number:",
                content: "input",
                buttons: true,
            }).then((value) => {
                if (value === null) return;
                
                const holdData = {
                    customer_id: $('#customer_id').val(),
                    hold_reference: value || 'Held-' + Date.now(),
                    cart_items: cart,
                    total_items: parseInt($('#summaryTotalItems').text()),
                    total_qty: parseFloat($('#summaryTotalQty').text()),
                    subtotal: parseFloat($('#summarySubtotal').text()),
                    grand_total: parseFloat($('#summaryGrandTotal').text())
                };

                $.ajax({
                    url: 'hold_sale.php',
                    method: 'POST',
                    data: JSON.stringify(holdData),
                    contentType: 'application/json',
                    success: function(response) {
                        if (response.status === 'success') {
                            swal("Success", "Sale Suspended", "success");
                            resetPOS();
                        }
                    }
                });
            });
        });

        // --- View and Resume Held Sales ---
        $('#viewHoldSalesBtn').on('click', function() {
            $.get('get_held_sales.php', function(response) {
                let rows = '';
                response.sales.forEach(sale => {
                    rows += `<tr>
                        <td>${sale.hold_reference}</td>
                        <td>${sale.customer_name || 'Walk-in'}</td>
                        <td>${parseFloat(sale.grand_total).toFixed(2)}</td>
                        <td><button class="btn btn-xs btn-success resume-sale" data-id="${sale.id}"><i class="fas fa-play"></i> Resume</button></td>
                    </tr>`;
                });
                $('#heldSalesTableBody').html(rows || '<tr><td colspan="4" class="text-center">No held sales found.</td></tr>');
                $('#heldSalesModal').modal('show');
            });
        });

        $(document).on('click', '.resume-sale', function() {
            const id = $(this).data('id');
            $.get('get_held_sales.php', { id: id }, function(response) {
                if (response.status === 'success') {
                    cart = response.items.map(item => {
                        item.quantity = parseFloat(item.quantity);
                        item.unit_price = parseFloat(item.unit_price);
                        item.purchase_price = parseFloat(item.purchase_price);
                        item.max_stock = parseFloat(item.max_stock);
                        return item;
                    });
                    $('#customer_id').val(response.customer_id).trigger('change');
                    updateCartUI();
                    $('#heldSalesModal').modal('hide');
                }
            });
        });

        // --- Print Receipt ---
        let lastPrintedInvoiceNo = null; // Variable to store the invoice number of the last completed sale

        $('#printReceiptBtn').on('click', function() {
            if (lastPrintedInvoiceNo) {
                window.open(`print_receipt.php?invoice_no=${lastPrintedInvoiceNo}`, '_blank');
            } else {
                swal("No Sale to Print", "Please complete a sale first or enter an invoice number manually.", "info");
            }
        });

        // --- Sale Actions ---
        $('#completeSaleBtn').on('click', function() {
            if (cart.length === 0) {
                swal("Cart is Empty!", "Please add products to the cart before completing the sale.", "warning");
                return;
            }

            const grandTotal = parseFloat($('#summaryGrandTotal').text());
            const dueAmount = parseFloat($('#due_amount').val());

            if (dueAmount > 0) {
                swal("Insufficient Payment!", `Customer still owes LKR ${dueAmount.toFixed(2)}.`, "warning");
                return;
            }

            swal({
                title: "Complete Sale?",
                text: `Grand Total: LKR ${grandTotal.toFixed(2)}`,
                icon: "info",
                buttons: true,
                dangerMode: false,
            }).then((willComplete) => {
                if (willComplete) {
                    const saleData = {
                        customer_id: $('#customer_id').val(),
                        cart_items: cart,
                        total_items: parseInt($('#summaryTotalItems').text()),
                        total_qty: parseFloat($('#summaryTotalQty').text()),
                        subtotal: parseFloat($('#summarySubtotal').text()),
                        discount_amount: parseFloat($('#summaryDiscount').text()),
                        grand_total: grandTotal,
                        amount_received: parseFloat($('#amount_received').val()),
                        change_amount: parseFloat($('#change_amount').val()),
                        due_amount: dueAmount,
                        payment_method: $('#payment_method').val(),
                        created_by: <?php echo $current_cashier_id; ?> // Pass current cashier ID
                    };

                    $.ajax({
                        url: 'save_sale.php',
                        method: 'POST',
                        data: JSON.stringify(saleData),
                        contentType: 'application/json',
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                lastPrintedInvoiceNo = response.invoice_no; // Store the invoice number
                                
                                // Open receipt automatically in a new tab
                                window.open(`print_receipt.php?invoice_no=${response.invoice_no}`, '_blank');

                                swal("Sale Completed!", `Invoice No: ${response.invoice_no}`, "success").then(() => {
                                    resetPOS();
                                });
                            } else {
                                swal("Error!", response.message, "error");
                            }
                        },
                        error: function(xhr, status, error) {
                            swal("Error!", "Failed to complete sale: " + xhr.responseText, "error");
                            console.error("Sale completion error:", error, xhr.responseText);
                        }
                    });
                }
            });
        });

        $('#cancelSaleBtn').on('click', function() {
            if (cart.length === 0) {
                swal("Cart is Empty!", "", "info");
                return;
            }
            swal({
                title: "Cancel Sale?",
                text: "All cart items will be lost.",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willCancel) => {
                if (willCancel) {
                    resetPOS();
                    swal("Sale Canceled!", {
                        icon: "success",
                    });
                }
            });
        });
        function resetPOS() {
            cart = [];
            updateCartUI();
            $('#customer_id').val(1).trigger('change'); // Reset to Walk-in Customer
            $('#product_search').val('');
            $('#parent_category_filter').val('');
            $('#sub_category_filter').empty().append('<option value="">All Sub Categories</option>');
            $('#payment_method').val('Cash');
            $('#amount_received').val('0.00');
            $('#change_amount').val('0.00');
            $('#due_amount').val('0.00');
            currentPage = 1;
            loadProducts(); // Reload products
            // Regenerate invoice number
            $.ajax({
                url: 'generate_invoice_no.php', // A new API to generate next invoice no
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.invoice_no) {
                        $('.invoice-no').text('Invoice: ' + response.invoice_no);
                    }
                }
            });
        }

        // --- Customer Management (Modal) ---
        $('#addCustomerForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'save_customer.php',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        swal("Success!", response.message, "success").then(() => {
                            $('#addCustomerModal').modal('hide');
                            // Add new customer to dropdown and select it
                            $('#customer_id').append(new Option(response.customer_name, response.customer_id, true, true)).trigger('change');
                            $('#addCustomerForm')[0].reset(); // Clear form
                        });
                    } else {
                        swal("Error!", response.message, "error");
                    }
                },
                error: function(xhr, status, error) {
                    swal("Error!", "Failed to add customer: " + xhr.responseText, "error");
                }
            });
        });

        // --- Keyboard Shortcuts ---
        $(document).on('keydown', function(e) {
            if (e.key === 'F2') {
                e.preventDefault();
                $('#customer_id').focus(); // Focus on customer dropdown
            } else if (e.key === 'F3') {
                e.preventDefault();
                $('#product_search').focus(); // Focus on product search
            } else if (e.key === 'F4') {
                e.preventDefault();
                $('#completeSaleBtn').click(); // Trigger complete sale
            } else if (e.key === 'Escape') {
                e.preventDefault();
                $('#clearCartBtn').click(); // Trigger clear cart
            }
        });

        // Initial UI update
        updateCartUI();
    </script>
</body>

</html>