<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Restaurant POS System</title>
    <link rel="stylesheet" href="./pos_main.styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            /* Professional Color Scheme */
            --primary-color: #1a5f7a;     /* Deep Teal - Professional, Trustworthy */
            --secondary-color: #2c7da0;   /* Muted Blue - Calm, Efficient */
            --accent-color: #e76f51;      /* Warm Terra Cotta - Inviting, Energetic */
            --background-light: #f8f9fa;  /* Soft Off-White - Clean, Neutral */
            --text-primary: #2d3748;      /* Dark Charcoal - Crisp, Readable */
            --text-secondary: #4a5568;    /* Softer Charcoal - Supporting Text */
            --border-color: #e2e8f0;      /* Light Gray - Subtle Divisions */
            
            /* Subtle Gradient for Depth */
            --gradient-primary: linear-gradient(135deg, #1a5f7a 0%, #2c7da0 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-light);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .container {
            display: flex;
            max-width: 97%;
            height: 90vh;
            margin: 20px auto;
            background-color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            overflow: hidden;
        }

        .left-section {
            width: 50%;
            padding: 20px;
            background-color: white;
            border-right: 1px solid var(--border-color);
        }

        .right-section {
            width: 50%;
            padding: 20px;
            background-color: var(--background-light);
        }

        /* Bill ID Display */
        .bill-id-display {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .item-id-section {
            background-color: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .item-id-row {
            display: grid;
            grid-template-columns: 1fr 1fr 0.5fr 1fr auto;
            gap: 10px;
            align-items: center;
            width: 100%;
        }

        .item-id-row input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 14px;
            color: var(--text-primary);
        }

        .item-id-row input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(26, 95, 122, 0.1);
        }

        .item-id-row button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .item-id-row button:hover {
            background-color: var(--secondary-color);
        }

        .hotel-table-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .hotel-table-row select, 
        .hotel-type-section select, 
        .table-section select {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 14px;
            color: var(--text-primary);
            background-color: white;
        }

        .menu-grid-container {
            max-height: 80vh;
            overflow-y: auto;
            padding-right: 10px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .menu-item {
            background-color: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .menu-item h4 {
            margin-bottom: 10px;
            color: var(--primary-color);
        }

        .menu-item button {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .menu-item button:hover {
            background-color: #d6604a;
        }

        .purchase-items table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        .purchase-items table th {
            background-color: var(--primary-color);
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }

        .purchase-items table td {
            padding: 10px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-secondary);
        }





        .purchase-items {
    display: flex;
    flex-direction: column;
    height: 100%; /* Adjust based on your layout */
}

.purchase-items table {
    width: 100%;
    table-layout: fixed;
    border-collapse: collapse;
}

.purchase-items thead {
    display: table;
    width: 100%;
    table-layout: fixed;
}

.purchase-items tbody {
    display: block;
    max-height: calc(100vh - 200px); /* Adjust the value based on your layout */
    overflow-y: auto;
}

.purchase-items tbody tr {
    display: table;
    width: 100%;
    table-layout: fixed;
}

/* Ensure the columns align properly */
.purchase-items th, .purchase-items td {
    padding: 8px;
    text-align: left;
}



        .cart-buttons button {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: background-color 0.3s ease;
        }

        .cart-buttons .quantity-btn {
            background-color: var(--secondary-color);
            color: white;
        }

        .cart-buttons .remove-btn {
            background-color: var(--accent-color);
            color: white;
        }

        .total-section {
            display: flex;
            justify-content: space-between;
            background-color: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }

        .option-footer {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .footer-opt-btn {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s ease;
        }

        .footer-opt-btn:hover {
            background-color: var(--primary-color);
        }

        /* Checkout Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            overflow: auto;
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
            width: 80%;
            max-width: 800px;
            animation: modalFadeIn 0.3s ease;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 15px;
        }

        .modal-header h2 {
            color: var(--primary-color);
            margin: 0;
        }

        .close-modal {
            background: transparent;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--text-secondary);
        }

        .payment-section {
            margin-top: 20px;
            border-top: 1px solid var(--border-color);
            padding-top: 20px;
        }

        .payment-methods {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }

        .payment-method-btn {
            background-color: white;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .payment-method-btn:hover, .payment-method-btn.active {
            background-color: var(--primary-color);
            color: white;
        }

        .payment-details {
            margin-top: 15px;
        }

        .payment-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .payment-row input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 14px;
        }

        .payment-controls {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .action-btn {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .action-btn.primary {
            background-color: var(--primary-color);
            color: white;
        }

        .action-btn.secondary {
            background-color: var(--secondary-color);
            color: white;
        }

        .action-btn.danger {
            background-color: var(--accent-color);
            color: white;
        }

        .payments-list {
            margin-top: 15px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            overflow: hidden;
        }

        .payments-list table {
            width: 100%;
            border-collapse: collapse;
        }

        .payments-list th {
            background-color: var(--primary-color);
            color: white;
            padding: 10px;
            text-align: left;
        }

        .payments-list td {
            padding: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .payment-summary {
            margin-top: 20px;
            padding: 15px;
            background-color: var(--background-light);
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
        }

        /* Held Bills Modal */
        .held-bills-list {
            max-height: 300px;
            overflow-y: auto;
            margin-top: 15px;
        }

        .held-bill-item {
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .held-bill-item:hover {
            background-color: var(--background-light);
        }

        .held-bill-details {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
        }

        .bill-id {
            font-weight: bold;
            color: var(--primary-color);
        }

        .dish-info {
            display: flex;
            flex-direction: column;
        }

        .remark-toggler {
            cursor: pointer;
            color: var(--primary-color);
        }

        .kot_remarks {
            display: none;
            margin-top: 5px;
            padding: 5px;
            border: 1px solid var(--border-color);
            border-radius: 3px;
            font-size: 12px;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .container {
                flex-direction: column;
                height: auto;
            }
            .left-section, .right-section {
                width: 100%;
            }
            .modal-content {
                width: 95%;
            }
            .payment-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="container">
        <div class="left-section">
            <!-- Bill ID Display -->
            <div class="bill-id-display">
                <span id="bill-id-label">Next Bill :</span>
                <span id="bill-id">Loading...</span>
            </div>

            <div class="option-footer">
                <button class="footer-opt-btn" id="checkout-btn">
                    <i class="fa-solid fa-basket-shopping"></i>
                    Checkout Cart (F1)
                </button>
                <button class="footer-opt-btn">
                    <i class="fa-solid fa-receipt"></i>
                    Print KOT (F2)
                </button>
                <button class="footer-opt-btn">
                    <i class="fa-solid fa-money-check-dollar"></i>
                    Recent Transactions (F3)
                </button>
                <button class="footer-opt-btn" id="held-bills-btn">
                    <i class="fa-solid fa-wallet"></i>
                    Held Bills (F4)
                </button>
                <button class="footer-opt-btn" id="new-bill-btn">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    New Bill (F5)
                </button>
            </div>

            <!-- Item ID Input Section -->
            <div class="item-id-section">
                <div class="item-id-row">
                    <input type="text" id="item-id-input" placeholder="Item ID">
                    <input type="text" id="item-name-input" placeholder="Product Name" readonly>
                    <input type="number" id="quantity-input" value="1" min="1">
                    <input type="text" id="price-input" placeholder="Price" readonly>
                    <button id="add-manual-item">Add to Cart</button>
                </div>
            </div>

            <div class="purchase-items">
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="cart-items"></tbody>
                </table>
            </div>
            
            <div class="total-section">
                <div>
                    <strong>Total Price (LKR):</strong> <span id="total-price">0.00</span>
                </div>
                <div>
                    <strong>Total Discount (LKR):</strong> <span id="total-discount">0.00</span>
                </div>
            </div>
        </div>

        <div class="right-section">
            <div class="hotel-table-row">
                <select id="hotel-type">
                    <option value="">Hotel Type</option>
                </select>
                <select id="food-category-selector">
                    <option value="default">Food Category</option>
                </select>
                <select id="table">
                    <option value="">Table</option>
                </select>
            </div>

            <div class="menu-grid-container">
                <div id="menu-container" class="menu-grid"></div>
            </div>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div id="checkout-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Checkout</h2>
                <button class="close-modal">&times;</button>
            </div>
            
            <div class="cart-summary">
                <h3>Cart Items</h3>
                <table class="payments-list">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="checkout-items"></tbody>
                </table>
            </div>
            
            <div class="payment-section">
                <h3>Add Payment</h3>
                <div class="payment-methods">
                    <button class="payment-method-btn" data-method="cash">Cash</button>
                    <button class="payment-method-btn" data-method="card">Credit/Debit Card</button>
                    <button class="payment-method-btn" data-method="online">Online Payment</button>
                </div>
                
                <div class="payment-details">
                    <div class="payment-row">
                        <input type="text" id="payment-method" placeholder="Payment Method" readonly>
                        <input type="number" id="payment-amount" placeholder="Amount">
                    </div>
                    <div class="payment-row" id="card-details" style="display: none;">
                        <input type="text" id="card-id" placeholder="Card Number">
                    </div>
                </div>
                
                <button class="action-btn secondary" id="add-payment-btn">Add Payment</button>
                
                <div class="payments-list" id="payment-entries-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Payment Method</th>
                                <th>Amount</th>
                                <th>Card Details</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="payment-entries"></tbody>
                    </table>
                </div>
                
                <div class="payment-summary">
                    <div>
                        <strong>Total Bill:</strong> <span id="checkout-total">0.00</span>
                    </div>
                    <div>
                        <strong>Total Paid:</strong> <span id="total-paid">0.00</span>
                    </div>
                    <div>
                        <strong>Balance:</strong> <span id="balance-amount">0.00</span>
                    </div>
                </div>
            </div>
            
            <div class="payment-controls">
                <button class="action-btn danger" id="cancel-checkout">Cancel</button>
                <button class="action-btn primary" id="complete-bill">Complete Bill</button>
            </div>
        </div>
    </div>

    <!-- Held Bills Modal -->
    <div id="held-bills-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Held Bills</h2>
                <button class="close-modal">&times;</button>
            </div>
            
            <div class="held-bills-list" id="held-bills-container">
                <!-- Held bills will be loaded here -->
            </div>
            
            <div class="payment-controls">
                <button class="action-btn danger" id="close-held-bills">Close</button>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let currentBillId = null;
        let isHeldBill = false;
        const cart = [];
        const payments = [];
        
        // Document ready function
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize the page
            fetchNextBillId();
            initializeEventListeners();
            populateHotelTypes();
        });

        // Initialize all event listeners
        function initializeEventListeners() {
            // Checkout button
            document.getElementById('checkout-btn').addEventListener('click', openCheckoutModal);
            
            // New bill button
            document.getElementById('new-bill-btn').addEventListener('click', handleNewBill);
            
            // Held bills button
            document.getElementById('held-bills-btn').addEventListener('click', openHeldBillsModal);
            
            // Modal close buttons
            document.querySelectorAll('.close-modal').forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('.modal').style.display = 'none';
                });
            });
            
            // Cancel checkout button
            document.getElementById('cancel-checkout').addEventListener('click', () => {
                document.getElementById('checkout-modal').style.display = 'none';
            });
            
            // Close held bills button
            document.getElementById('close-held-bills').addEventListener('click', () => {
                document.getElementById('held-bills-modal').style.display = 'none';
            });
            
            // Complete bill button
            document.getElementById('complete-bill').addEventListener('click', completeBill);
            
            // Payment method buttons
            document.querySelectorAll('.payment-method-btn').forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    document.querySelectorAll('.payment-method-btn').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Set payment method input
                    const method = this.getAttribute('data-method');
                    document.getElementById('payment-method').value = method;
                    
                    // Show/hide card details based on payment method
                    if (method === 'card') {
                        document.getElementById('card-details').style.display = 'block';
                    } else {
                        document.getElementById('card-details').style.display = 'none';
                    }
                });
            });
            
            // Add payment button
            document.getElementById('add-payment-btn').addEventListener('click', addPayment);
            
            // Hotel Type and Category Selectors
            document.getElementById('hotel-type').addEventListener('change', function() {
                const selectedHotelType = this.value;
                const selectedCategory = document.getElementById('food-category-selector').value;
                if (selectedHotelType) {
                    populateTables(selectedHotelType);
                    populateMenu(selectedHotelType, selectedCategory);
                }
            });

            document.getElementById('food-category-selector').addEventListener('change', function() {
                const selectedHotelType = document.getElementById('hotel-type').value;
                const selectedCategory = this.value;
                if (selectedHotelType) {
                    populateMenu(selectedHotelType, selectedCategory);
                }
            });

            // Item ID Input
            const itemIdInput = document.getElementById('item-id-input');
            itemIdInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    const hotelType = document.getElementById('hotel-type').value;
                    if (hotelType) {
                        fetchItemDetails(itemIdInput.value, hotelType);
                    } else {
                        alert('Please select a hotel type first');
                    }
                }
            });

            // Add manual item button
            document.getElementById('add-manual-item').addEventListener('click', addManualItem);
        }

        // Hotel Type and Menu Functions
        function populateHotelTypes() {
            // Simulating API call
            fetch('fetch_hotel_types.php')
                .then(response => response.json())
                .then(data => {
                    const hotelTypeSelect = document.getElementById('hotel-type');
                    data.forEach(type => {
                        const option = document.createElement('option');
                        option.value = type.id;
                        option.textContent = type.name;
                        hotelTypeSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching hotel types:', error);
                    // For demo purposes, add some sample data
                    const hotelTypeSelect = document.getElementById('hotel-type');
                    const sampleTypes = [
                        { id: 1, name: 'Restaurant' },
                        { id: 4, name: 'Uber/Pickme' },
                        { id: 6, name: 'Delivery' },
                        { id: 7, name: 'Takeaway' }
                    ];
                    sampleTypes.forEach(type => {
                        const option = document.createElement('option');
                        option.value = type.id;
                        option.textContent = type.name;
                        hotelTypeSelect.appendChild(option);
                    });
                });

            // Populate food categories
            const foodCategorySelector = document.getElementById('food-category-selector');
            foodCategorySelector.style.textTransform = 'capitalize';
            
            const food_categories = [
                "Main Dishes",
                "Chicken",
                "Sea Food",
                "Beef",
                "Vegetables",
                "Soups",
                "Salads",
                "Starters",
                "Main Course",
                "Chai",
                "Snacks",
                "Drinks",
            ];
            
            food_categories.forEach((category) => {
                const optionTag = document.createElement('option');
                optionTag.setAttribute('value', category);
                optionTag.innerText = category.replace('_', ' ');
                foodCategorySelector.appendChild(optionTag);
            });
            
            const bakeryOption = document.createElement('option');
            bakeryOption.setAttribute('value', 'bakery_and_beverages');
            bakeryOption.innerText = "Bakery & Beverages";
            foodCategorySelector.appendChild(bakeryOption);
        }

        function populateTables(hotelTypeId) {
            fetch(`fetch_tables.php?hotel_type_id=${hotelTypeId}`)
                .then(response => response.json())
                .then(data => {
                    const tableSelect = document.getElementById('table');
                    tableSelect.innerHTML = '<option value="">Select Table</option>';
                    data.forEach(table => {
                        const option = document.createElement('option');
                        option.value = table.table_id;
                        option.textContent = `Table ${table.table_id} (Capacity: ${table.capacity}, ${table.is_available ? 'Available' : 'Occupied'})`;
                        tableSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching tables:', error);
                    // For demo purposes, add some sample data
                    const tableSelect = document.getElementById('table');
                    tableSelect.innerHTML = '<option value="">Select Table</option>';
                    const sampleTables = [
                        { table_id: 1, capacity: 4, is_available: true },
                        { table_id: 2, capacity: 2, is_available: true },
                        { table_id: 3, capacity: 6, is_available: false }
                    ];
                    sampleTables.forEach(table => {
                        const option = document.createElement('option');
                        option.value = table.table_id;
                        option.textContent = `Table ${table.table_id} (Capacity: ${table.capacity}, ${table.is_available ? 'Available' : 'Occupied'})`;
                        tableSelect.appendChild(option);
                    });
                });
        }

        function populateMenu(hotelType, selectedCategory) {
            fetch(`fetch_menu_items.php?hotel_type=${hotelType}&category=${selectedCategory}`)
                .then(response => response.json())
                .then(data => {
                    const menuContainer = document.getElementById('menu-container');
                    menuContainer.innerHTML = '';
                    
                    if (!Array.isArray(data)) {
                        console.error("NOT THE EXPECTED OUTPUT:", data);
                        return;
                    }
                    
                    data.forEach(item => {
                        let dishPrice = 0;
                        switch(parseInt(hotelType)) {
                            case 4:
                                dishPrice = item.uber_pickme_price;
                                break;
                            case 6:
                                dishPrice = item.uber_pickme_price;
                                break;
                            case 7:
                                dishPrice = item.takeaway_price;
                                break;
                            default:
                                dishPrice = item.item_price;
                        }
                        
                        const menuItem = document.createElement('div');
                        menuItem.classList.add('menu-item');
                        menuItem.innerHTML = `
                            <h4>${item.item_name}</h4>
                            <p>Type: ${item.item_type}</p>
                            <p>Category: ${item.item_category}</p>
                            <p>Price: LKR ${!dishPrice ? 0.00 : dishPrice}</p>
                            <button onclick="addToCart('${item.item_id}', '${item.item_name}', ${dishPrice})">Add to Cart</button>
                        `;
                        menuContainer.appendChild(menuItem);
                    });
                })
                .catch(error => {
                    console.error('Error fetching menu items:', error);
                    });
                    }