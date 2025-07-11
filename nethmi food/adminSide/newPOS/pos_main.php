<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Restaurant POS System</title>
    <script src="../js/pos_handler.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS if you're using Bootstrap modals -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="./pos_main.styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../customerSide/menu/notifier/style.css">
    <script src="../../customerSide/menu/notifier/index.var.js"></script>
    <style>
        :root {
            /* Professional Color Scheme */
            --primary-color: #1a5f7a;
            /* Deep Teal - Professional, Trustworthy */
            --secondary-color: #2c7da0;
            /* Muted Blue - Calm, Efficient */
            --accent-color: #e76f51;
            /* Warm Terra Cotta - Inviting, Energetic */
            --background-light: #f8f9fa;
            /* Soft Off-White - Clean, Neutral */
            --text-primary: #2d3748;
            /* Dark Charcoal - Crisp, Readable */
            --text-secondary: #4a5568;
            /* Softer Charcoal - Supporting Text */
            --border-color: #e2e8f0;
            /* Light Gray - Subtle Divisions */

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
            gap: 3px;
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
            grid-template-columns: 1fr 1fr 1.5fr;
            gap: 15px;
            margin-bottom: 15px;
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
            max-height: 76vh;
            overflow-y: auto;
            padding-right: 10px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        /* .menu-item {
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
        } */

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
            max-height: calc(100vh - 260px);
            /* Adjust this value based on your layout */
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .purchase-items table {
            width: 100%;
            border-collapse: collapse;
        }

        .purchase-items thead {
            position: sticky;
            top: 0;
            background-color: white;
            /* Match your background color */
            z-index: 1;
        }

        .purchase-items tbody {
            /* This allows the body to grow/scroll while headers stay put */
        }

        /* Optional styling for better appearance */
        .purchase-items th,
        .purchase-items td {
            padding: 8px;
            text-align: left;
        }

        .total-section {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
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

        /* Responsive Design */
        @media (max-width: 1200px) {
            .container {
                flex-direction: column;
            }

            .left-section,
            .right-section {
                width: 100%;
            }
        }

        /* Add to your existing CSS */
        .bill-info-header {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 15px;
            text-align: center;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            font-weight: bold;
            font-size: 18px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Flexbox centering */
            justify-content: center;
            align-items: center;
        }

        /* When modal is active */
        /* .modal.show {
    display: flex;
} */

        .modal-content {
            background: white;
            position: relative;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            /* animation: modal-slide-in 0.3s ease-out; */
            overflow: auto;
            width: 70%;
            max-height: 85%;
            max-width: 80%;
            /* Ensure no margins or positioning interfere */
            margin: 0;
            /* Optionally, add these for absolute centering as a fallback */
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            scrollbar-width: thin;
            /* For Firefox */
            scrollbar-color: var(--primary-color) #f1f1f1;
            /* For Firefox */
        }

        @keyframes modal-slide-in {
            from {
                transform: translateY(-30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            background: var(--gradient-primary);
            padding: 15px 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            position: sticky;
            top: 0;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
        }

        .close {
            color: white;
            cursor: pointer;
            font-size: 18px;
        }


        .close:hover {
            transform: scale(1.1);
            color: white;
        }

        .checkout-items,
        .payment-section {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .section-title {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            color: var(--primary-color);
        }

        .section-title i {
            margin-right: 10px;
            font-size: 18px;
        }

        .section-title h3 {
            margin: 0;
            font-weight: 600;
        }

        #checkout-items-table,
        #payments-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 15px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        #checkout-items-table thead tr,
        #payments-table thead tr {
            background: var(--secondary-color);
            color: white;
        }

        #checkout-items-table th,
        #payments-table th {
            padding: 12px 15px;
            text-align: left;
            font-weight: 500;
        }

        #checkout-items-table td,
        #payments-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border-color);
        }

        #checkout-items-table tbody tr:nth-child(even),
        #payments-table tbody tr:nth-child(even) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        #checkout-items-table tfoot tr,
        #payments-table tfoot tr {
            background-color: #f8fafb;
            font-weight: 600;
        }

        #checkout-items-table tfoot td,
        #payments-table tfoot td {
            padding: 12px 15px;
        }

        .payment-form {
            background-color: #f8fafb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            display: flex;
        }

        .payment-form select,
        .payment-form input {
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .payment-form select:focus,
        .payment-form input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(26, 95, 122, 0.1);
        }

        #add-payment-btn {
            background: var(--secondary-color);
            color: white;
            border: none;
            padding: 12px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.2s;
        }

        #add-payment-btn:hover {
            background-color: #236b87;
        }

        .remove-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
            font-size: 12px;
        }

        .remove-btn:hover {
            background-color: #c0392b;
        }

        .checkout-actions {
            display: flex;
            justify-content: flex-end;
            padding: 20px;
            gap: 15px;
        }

        #complete-bill-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
            display: flex;
            align-items: center;
        }

        #complete-bill-btn:hover {
            background-color: #154960;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        #complete-bill-btn:active {
            transform: translateY(0);
        }

        #complete-bill-btn i {
            margin-right: 8px;
        }

        #cancel-checkout-btn {
            background-color: #718096;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.2s;
        }

        #cancel-checkout-btn:hover {
            background-color: #5a6677;
        }

        /* Balance highlight */
        #balance-amount.negative {
            color: #e74c3c;
            font-weight: bold;
        }

        #balance-amount.positive {
            color: #2ecc71;
        }


        /* */
        /* Held Bills Dropdown Styles */
        .dropdown {
            position: fixed;
            /* Changed from absolute to fixed for better positioning */
            z-index: 999;
            display: none;
            /* Hide by default */
            width: 300px;
            transition: opacity 0.3s, transform 0.3s;
            opacity: 0;
            pointer-events: none;
        }

        .dropdown.show {
            display: block;
            /* Show when .show class is added */
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .dropdown-content {
            background-color: white;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            overflow: hidden;
            max-height: 500px;
            display: flex;
            flex-direction: column;
        }

        .dropdown-header {
            background: var(--gradient-primary);
            padding: 12px 15px;
            color: white;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dropdown-header h3 {
            margin: 0;
            font-size: 16px;
        }

        .dropdown-close {
            color: white;
            cursor: pointer;
            font-size: 18px;
        }

        .dropdown-body {
            overflow-y: auto;
            max-height: 500px;
            /* Custom scrollbar styles */
            scrollbar-width: thin;
            /* For Firefox */
            scrollbar-color: var(--primary-color) #f1f1f1;
            /* For Firefox */
        }

        /* WebKit browsers (Chrome, Safari, Edge) custom scrollbar */
        .dropdown-body::-webkit-scrollbar {
            width: 6px;
        }

        .dropdown-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .dropdown-body::-webkit-scrollbar-thumb {
            background: var(--primary-color, #4299e1);
            border-radius: 10px;
        }

        .dropdown-body::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark, #3182ce);
        }

        #held-bills-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        #held-bills-list li {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: background-color 0.2s;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #held-bills-list li:hover {
            background-color: #f1f5f9;
        }

        #held-bills-list li .bill-info {
            display: flex;
            flex-direction: column;
        }

        #held-bills-list li .bill-id {
            font-weight: 600;
            color: var(--primary-color);
        }

        #held-bills-list li .bill-details {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 3px;
        }

        #held-bills-list li .bill-amount {
            font-weight: 600;
        }

        .empty-state {
            padding: 20px;
            text-align: center;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 24px;
            margin-bottom: 10px;
            opacity: 0.5;
        }

        /* Add active state for Held Bills button */
        .footer-opt-btn.active-held {
            background-color: var(--primary-color);
            color: white;
        }


        /* Highlight held bill button when active */
        .footer-opt-btn.active-held {
            background-color: var(--accent-color);
            color: white;
        }

        /* Payment method badges */
        .payment-method {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-right: 5px;
        }

        .payment-method.cash {
            background-color: #2ecc71;
            color: white;
        }

        .payment-method.credit,
        .payment-method.debit {
            background-color: #3498db;
            color: white;
        }

        .payment-method.bank {
            background-color: #9b59b6;
            color: white;
        }



        /* Advanced Alert Styles */
        .alert-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .alert-overlay.show {
            opacity: 1;
            pointer-events: auto;
        }

        .alert-box {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transform: translateY(20px);
            opacity: 0;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .alert-overlay.show .alert-box {
            transform: translateY(0);
            opacity: 1;
        }

        .alert-header {
            padding: 15px 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #eee;
        }

        .alert-header .alert-icon {
            margin-right: 15px;
            font-size: 24px;
        }

        .alert-success .alert-header {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-error .alert-header {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-warning .alert-header {
            background-color: #fff3cd;
            color: #856404;
        }

        .alert-info .alert-header {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .alert-title {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        .alert-body {
            padding: 20px;
            font-size: 16px;
            line-height: 1.5;
        }

        .alert-footer {
            padding: 15px 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-top: 1px solid #eee;
        }

        .alert-btn {
            padding: 8px 16px;
            border-radius: 5px;
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .alert-btn-primary {
            background-color: var(--primary-color, #4299e1);
            color: white;
        }

        .alert-btn-primary:hover {
            background-color: var(--primary-dark, #3182ce);
        }

        .alert-btn-secondary {
            background-color: #e2e8f0;
            color: #4a5568;
        }

        .alert-btn-secondary:hover {
            background-color: #cbd5e0;
        }

        #discount-input {
            width: 120px;
            /* Fixed width for consistency */
            padding: 8px 10px;
            /* Comfortable padding */
            border: 1px solid var(--border-color);
            /* Matches other inputs */
            border-radius: 5px;
            /* Consistent with other form elements */
            font-size: 14px;
            color: var(--text-primary);
            background-color: white;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            /* Smooth transitions */
        }

        /* Focus state for discount input */
        #discount-input:focus {
            outline: none;
            border-color: var(--primary-color);
            /* Highlights with primary color */
            box-shadow: 0 0 0 2px rgba(26, 95, 122, 0.1);
            /* Subtle glow effect */
        }

        /* Hover state for discount input */
        #discount-input:hover:not(:focus) {
            border-color: var(--secondary-color);
            /* Slight change on hover */
        }

        /* Ensure input doesn't allow negative values visually */
        #discount-input::-webkit-outer-spin-button,
        #discount-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            /* Removes default spinner in Webkit browsers */
            margin: 0;
            /* Ensures no extra spacing */
        }

        /* Firefox number input styling */
        #discount-input[type="number"] {
            -moz-appearance: textfield;
            /* Removes spinner in Firefox */
        }

        /* Placeholder styling */
        #discount-input::placeholder {
            color: var(--text-secondary);
            opacity: 0.7;
        }

        /* Optional: Error state styling (if you add validation later) */
        #discount-input.error {
            border-color: #e74c3c;
            /* Red border for errors */
            background-color: #ffebee;
            /* Light red background */
        }




        /*transactions */
        /* .modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    position: relative;
    width: 80%;
    max-width: 1200px;
    max-height: 85vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
} */

        .transactions-modal-content {
            background: white;
            position: relative;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            /* animation: modal-slide-in 0.3s ease-out; */
            overflow: auto;
            width: 80%;
            height: 92%;
            max-height: 92%;
            max-width: 90%;
            /* Ensure no margins or positioning interfere */
            margin: 0;
            /* Optionally, add these for absolute centering as a fallback */
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            scrollbar-width: thin;
            /* For Firefox */
            scrollbar-color: var(--primary-color) #f1f1f1;
            /* For Firefox */
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        /* .modal-header h2 {
    margin: 0;
    color: #333;
    display: flex;
    align-items: center;
} */

        /* .modal-header h2 i {
    margin-right: 10px;
    color: #4285f4;
} */

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s;
        }

        .close:hover {
            color: #333;
        }

        /* Transactions Modal Specific Styles */
        .transactions-modal-content {
            height: 90vh;
        }

        .transactions-filters {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            border: 1px solid #e0e0e0;
        }

        .filter-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-group input,
        .filter-group select {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .filter-group button {
            background-color: #4285f4;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background-color 0.3s;
        }

        .filter-group button:hover {
            background-color: #3367d6;
        }

        .transactions-container {
            flex: 1;
            overflow-y: auto;
            padding-right: 5px;
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .transactions-table th,
        .transactions-table td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .transactions-table th {
            background-color: #f1f3f4;
            position: sticky;
            top: 0;
            /* z-index: 10; */
        }

        .transactions-table tr:hover {
            background-color: #f8f9fa;
        }

        /* Add these CSS styles */
        .transactions-container {
            position: relative;
            height: calc(100vh - 240px);
            /* Adjust this value based on the height of other elements */
            display: flex;
            flex-direction: column;
        }


        .transactions-table thead tr {
            position: sticky;
            top: 0;

            z-index: 1;
        }

        /* Ensure the loading spinner appears at the bottom */
        #loading-spinner {
            margin-top: 10px;
            text-align: center;
        }


        .transaction-actions {
            display: flex;
            gap: 8px;
        }

        .transaction-actions button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            padding: 5px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .transaction-actions button:hover {
            background-color: #e0e0e0;
        }

        .view-btn {
            color: #4285f4;
        }

        .print-btn {
            color: #34a853;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-active {
            background-color: #ffedd5;
            color: #d97706;
        }

        .status-completed {
            background-color: #d1fae5;
            color: #059669;
        }

        .status-cancelled {
            background-color: #e0e0e0;
            color: #616161;
        }

        /* Bill Details Modal Specific Styles */
        /* .bill-details-modal-content {
    width: 75%;
    max-width: 90%;
} */

        .bill-details-modal-content {
            background: white;
            position: relative;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            /* animation: modal-slide-in 0.3s ease-out; */
            overflow: auto;
            width: 75%;
            height: 90%;
            max-height: 92%;
            max-width: 90%;
            /* Ensure no margins or positioning interfere */
            margin: 0;
            /* Optionally, add these for absolute centering as a fallback */
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            scrollbar-width: thin;
            /* For Firefox */
            scrollbar-color: var(--primary-color) #f1f1f1;
            /* For Firefox */
        }

        .bill-summary {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            background-color: #f1f3f4;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .summary-item {
            display: flex;
            flex-direction: column;
        }

        .bill-sections {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        @media (min-width: 768px) {
            .bill-sections {
                grid-template-columns: 1fr 1fr;
            }
        }

        .bill-section {
            background-color: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            overflow: hidden;
        }

        .bill-section h3 {
            margin: 0;
            padding: 12px 15px;
            background-color: #f1f3f4;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .bill-items-table,
        .bill-payments-table {
            width: 100%;
            border-collapse: collapse;
        }

        .bill-items-table th,
        .bill-items-table td,
        .bill-payments-table th,
        .bill-payments-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .bill-items-table th,
        .bill-payments-table th {
            background-color: #f8f9fa;
        }

        .bill-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        #print-bill-btn {
            background-color: #34a853;
            color: white;
        }

        #reopen-bill-btn {
            background-color: #fbbc05;
            color: white;
        }

        #add-payment-to-bill-btn {
            background-color: #4285f4;
            color: white;
        }

        #close-bill-details-btn {
            background-color: #ea4335;
            color: white;
        }

        .action-btn:hover {
            opacity: 0.9;
        }

        .text-right {
            text-align: right;
        }

        /* Loading and Empty State Styles */
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            color: #666;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 10px;
            color: #ccc;
        }


        /* Cancel Bill Modal Styles */
        #cancel-bill-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: none;
        }

        #cancel-bill-modal .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 500px;
            width: 90%;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        #cancel-bill-modal .modal-header {
            background-color: #dc3545;
            color: white;
            padding: 15px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #cancel-bill-modal .modal-header h2 {
            margin: 0;
            font-size: 18px;
        }

        #cancel-bill-modal .modal-body {
            padding: 20px;
        }

        #cancel-bill-modal .modal-footer {
            padding: 15px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-top: 1px solid #e9ecef;
        }

        #cancel-bill-modal .form-group {
            margin-top: 20px;
        }

        #cancel-bill-modal label {
            display: block;
            margin-bottom: 5px;
        }

        #cancel-bill-modal textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            resize: vertical;
        }

        #cancel-bill-modal textarea.error {
            border-color: #dc3545;
            background-color: #fff8f8;
        }

        #cancel-bill-modal .btn-danger {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        #cancel-bill-modal .btn-secondary {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        #cancel-bill-modal .btn-danger:hover {
            background-color: #c82333;
        }

        #cancel-bill-modal .btn-secondary:hover {
            background-color: #5a6268;
        }

        #cancel-bill-modal .close {
            color: white;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        /* Remove Item Modal Styles */
        #remove-item-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: none;
        }

        #remove-item-modal .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 500px;
            width: 90%;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        #remove-item-modal .modal-header {
            background-color: #e74c3c;
            color: white;
            padding: 15px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #remove-item-modal .modal-header h2 {
            margin: 0;
            font-size: 18px;
        }

        #remove-item-modal .modal-body {
            padding: 20px;
        }

        #remove-item-modal .modal-footer {
            padding: 15px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-top: 1px solid #e9ecef;
        }

        #remove-item-modal .form-group {
            margin-top: 20px;
        }

        #remove-item-modal label {
            display: block;
            margin-bottom: 5px;
        }

        #remove-item-modal textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            resize: vertical;
        }

        #remove-item-modal textarea.error {
            border-color: #dc3545;
            background-color: #fff8f8;
        }

        #remove-item-modal .btn-danger {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        #remove-item-modal .btn-secondary {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        #remove-item-modal .btn-danger:hover {
            background-color: #c82333;
        }

        #remove-item-modal .btn-secondary:hover {
            background-color: #5a6268;
        }

        #remove-item-modal .close {
            color: white;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        /* Reference Number Container Styles */
        .reference-number-container {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
            margin: 6px 0;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            gap: 6px;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .reference-number-container label {
            font-weight: bold;
            margin-right: 10px;
            min-width: 160px;
        }

        .reference-number-container input {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
        }

        .reference-number-container input:focus {
            outline: none;
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .reference-number-container input.error {
            border-color: #dc3545;
            background-color: #fff8f8;
        }

        /* Add responsive styling */
        @media (max-width: 768px) {
            .reference-number-container {
                flex-direction: column;
                align-items: flex-start;
            }

            .reference-number-container label {
                margin-bottom: 5px;
            }

            .reference-number-container input {
                width: 100%;
            }
        }

        .reference-number-container {
            background-color: var(--background-light);
            border-radius: 8px;
            border: 1px solid var(--border-color);
            border-left: 4px solid var(--primary-color);
            display: flex;
            flex-direction: row;
            gap: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            animation: slideDown 0.3s ease-in-out;
            transition: all 0.2s ease;
        }

        .reference-number-container:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
            border-left-color: var(--accent-color);
        }

        .reference-number-container label {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 0.95rem;
            display: block;
            margin-bottom: 4px;
        }

        .reference-number-container input {
            padding: 10px 12px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            width: 100%;
            font-size: 0.95rem;
            color: var(--text-secondary);
            background-color: white;
            transition: all 0.2s ease;
        }

        .reference-number-container input:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(44, 125, 160, 0.2);
        }

        .reference-number-container input::placeholder {
            color: #a0aec0;
        }

        /* Replace fadeIn animation with slideDown */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }



        /* Enhanced Table Dropdown Styles */

        /* Base Dropdown Styling */
        #table {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px 12px;
            width: 100%;
            font-size: 14px;
            line-height: 1.5;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        #table:hover {
            border-color: #aaa;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
        }

        #table:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.25);
        }

        /* Custom Dropdown Arrow */
        .table-selection-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
            max-width: 300px;
        }

        .table-selection-wrapper::after {
            font-size: 18px;
            color: #555;
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }

        /* Option Styling */
        #table option {
            padding: 12px;
            font-size: 14px;
            border-bottom: 1px solid #f0f0f0;
        }

        /* Status Color Styles */
        .table-occupied {
            color: #ff0000;
            /* Red color for occupied tables */
            font-weight: bold;
        }

        .table-available {
            color: #008000;
            /* Green color for available tables */
        }

        .table-dirty {
            color: #ff9800;
            /* Orange color for dirty tables */
            font-weight: bold;
        }

        /* Background colors for options */
        #table option.table-occupied {
            background-color: #ffeeee;
            /* Light red background */
            border-left: 4px solid #ff0000;
            padding-left: 8px;
        }

        #table option.table-available {
            background-color: #eeffee;
            /* Light green background */
            border-left: 4px solid #008000;
            padding-left: 8px;
        }

        #table option.table-dirty {
            background-color: #fff5e6;
            /* Light orange background */
            border-left: 4px solid #ff9800;
            padding-left: 8px;
            position: relative;
        }

        /* Reset Icon Styling */
        #table option.table-dirty::after {
            content: "🔄";
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.8;
        }

        /* Styling for disabled options */
        #table option:disabled {
            opacity: 0.6;
            font-style: italic;
        }

        /* Style for when dropdown is open */
        #table:focus+.dropdown-open-indicator {
            transform: rotate(180deg);
        }

        /* Custom scrollbar for dropdown (works in modern browsers) */
        #table::-webkit-scrollbar {
            width: 8px;
        }

        #table::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        #table::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        #table::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Reset Button Animation */
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .reset-icon {
            display: inline-block;
            margin-left: 5px;
            transition: transform 0.3s ease;
        }

        .reset-icon:hover {
            animation: spin 1s infinite linear;
        }

        /* Dropdown Container - for better positioning */
        .dropdown-container {
            position: relative;
            display: inline-block;
        }

        /* Table Label for better UI */
        .table-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #333;
            font-size: 14px;
        }

        /* State indicators next to the table number */
        .state-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .state-indicator.occupied {
            background-color: #ff0000;
        }

        .state-indicator.available {
            background-color: #008000;
        }

        .state-indicator.dirty {
            background-color: #ff9800;
        }

        /* For browsers that support :has and advanced selectors */
        @supports selector(:has(.foo)) {

            /* Style the dropdown when it contains a selected dirty table */
            #table:has(option.table-dirty:checked) {
                border-color: #ff9800;
                box-shadow: 0 0 0 2px rgba(255, 152, 0, 0.25);
            }

            /* Style the dropdown when it contains a selected available table */
            #table:has(option.table-available:checked) {
                border-color: #008000;
                box-shadow: 0 0 0 2px rgba(0, 128, 0, 0.25);
            }
        }

        /* Media query for smaller screens */
        @media (max-width: 768px) {
            .table-selection-wrapper {
                max-width: 100%;
            }

            #table {
                padding: 12px;
                font-size: 16px;
                /* Larger text for better touch targets */
            }
        }


        .not-available {
            color: #dc3545;
            font-style: italic;
            margin: 5px 0;
            font-size: 14px;
        }

        /* Additional menu container styles for better layout */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        /* Make menu consistent height */
        .menu-item {
            display: flex;
            flex-direction: column;
            height: 250px;
            width: 300px;
        }

        /* Push the portion container to the bottom */
        .portion-container {
            margin-top: auto;
        }

        /* Style for table and hotel type selectors */
        /* .hotel-table-row {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
    padding: 15px;
    background-color: var(--background-light);
    border-radius: 8px;
    border: 1px solid var(--border-color);
} */

        .hotel-table-row select {
            padding: 10px 15px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            background-color: white;
            color: var(--text-primary);
            font-size: 14px;
            min-width: 150px;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 1em;
        }

        /* Color variables */
        :root {
            /* Professional Color Scheme */
            --primary-color: #1a5f7a;
            /* Deep Teal - Professional, Trustworthy */
            --secondary-color: #2c7da0;
            /* Muted Blue - Calm, Efficient */
            --accent-color: #e76f51;
            /* Warm Terra Cotta - Inviting, Energetic */
            --background-light: #f8f9fa;
            /* Soft Off-White - Clean, Neutral */
            --text-primary: #2d3748;
            /* Dark Charcoal - Crisp, Readable */
            --text-secondary: #4a5568;
            /* Softer Charcoal - Supporting Text */
            --border-color: #e2e8f0;
            /* Light Gray - Subtle Divisions */
            /* Subtle Gradient for Depth */
            --gradient-primary: linear-gradient(135deg, #1a5f7a 0%, #2c7da0 100%);
        }

        .menu-item {
            border: 1px solid var(--border-color);
            padding: 18px;
            border-radius: 8px;
            background-color: #fff;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            overflow: visible;
            /* Changed from overflow: hidden to fix button overflow */
        }

        .menu-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: var(--gradient-primary);
        }

        .item-name {
            margin: 0 0 10px 0;
            font-size: 18px;
            color: var(--text-primary);
            font-weight: 600;
        }

        .item-description {
            font-size: 14px;
            color: var(--text-secondary);
            font-style: italic;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .item-details {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .item-details p {
            margin: 0;
            color: var(--text-secondary);
        }

        .item-details span {
            font-weight: 500;
            color: var(--text-primary);
        }

        .portion-container {
            display: flex;
            align-items: center;
            margin-top: 15px;
            gap: 12px;
            flex-wrap: wrap;
            /* Added to fix button overflow */
        }

        .portion-select {
            padding: 8px 12px;
            min-width: 180px;
            max-width: 100%;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background-color: var(--background-light);
            font-size: 14px;
            color: var(--text-primary);
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 1em;
            cursor: pointer;
        }

        .portion-select:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 2px rgba(44, 125, 160, 0.2);
        }

        button {
            padding: 8px 16px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            transition: background-color 0.2s, transform 0.1s;
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        button:hover {
            background-color: var(--secondary-color);
        }

        button:active {
            transform: scale(0.98);
        }

        /* Add to Cart button specific styling */
        .portion-container button {
            background-color: var(--accent-color);
        }

        .portion-container button:hover {
            background-color: #d66347;
            /* Darker shade of accent color */
        }


        /* Add styling for the portion selector */
        #portion-selector-container {
            margin: 0 3px;
            width: auto;
            min-width: 120px;
            flex: 0 1 auto;
        }

        .portion-select {
            padding: 8px 12px;
            width: 100%;
            height: 38px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background-color: var(--background-light);
            font-size: 14px;
            color: var(--text-primary);
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 1em;
            cursor: pointer;
        }

        .portion-select:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 2px rgba(44, 125, 160, 0.2);
        }

        /* Order of fields (for better visibility and understanding) */
        /* The order is:
1. Item ID input
2. Item Name input
3. Portion selector (when visible)
4. Quantity input
5. Price input
6. Add button
*/

        .item-id-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: nowrap;
            width: 100%;
        }

        .item-id-row>* {
            margin: 0 3px;
        }

        /* Input fields styling */
        .item-id-row input[type="text"],
        .item-id-row input[type="number"] {
            padding: 8px 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 14px;
            height: 38px;
            min-width: 0;
            /* Allows inputs to shrink below min-content */
        }

        #item-id-input {
            width: 100px;
            flex: 0 0 auto;
        }

        #item-name-input {
            flex: 1 1 150px;
            min-width: 0;
        }

        #quantity-input {
            width: 60px;
            flex: 0 0 auto;
        }

        #price-input {
            width: 80px;
            flex: 0 0 auto;
        }

        .item-id-row input[type="text"]:focus,
        .item-id-row input[type="number"]:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(26, 95, 122, 0.2);
        }

        /* Add to cart button styling */
        #add-manual-item {
            padding: 8px 10px;
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
            height: 38px;
            white-space: nowrap;
            flex: 0 0 auto;
            min-width: 42px;
        }

        #add-manual-item:hover {
            background-color: #d66347;
        }

        #add-manual-item:before {
            content: '';
            display: inline-block;
            width: 16px;
            height: 16px;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3ccircle cx='9' cy='21' r='1'%3e%3c/circle%3e%3ccircle cx='20' cy='21' r='1'%3e%3c/circle%3e%3cpath d='M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6'%3e%3c/path%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
        }

        /* For smaller screens, allow wrapping but keep it as single line on desktop */
        @media (max-width: 992px) {
            .item-id-row {
                flex-wrap: wrap;
            }

            .item-id-row>* {
                margin-bottom: 8px;
            }

            #item-id-input,
            #item-name-input {
                flex-basis: 48%;
            }

            #quantity-input,
            #price-input {
                flex-basis: 48%;
            }

            #portion-selector-container {
                flex-basis: 100%;
                margin: 5px 0;
            }

            #add-manual-item {
                flex-basis: 100%;
            }
        }


        /* Modal Styles */
        .tableMap-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
        /* Flexbox for perfect centering */
        display: none; /* Will be changed to flex when active */
        justify-content: center;
        align-items: center;
        }

        .tableMap-modal-content {
        background-color: #fefefe;
        padding: 20px;
        border: 1px solid #888;
        width: 90%;
        max-width: 1000px;
        max-height: 90vh; /* Prevent modal from being taller than viewport */
        overflow-y: auto; /* Add scrolling for tall content */
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .tableMap-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #ddd;
        padding-bottom: 10px;
        margin-bottom: 20px;
        }

        .tableMap-header h2 {
        margin: 0;
        font-size: 24px;
        font-weight: bold;
        }

        .tableMap-close {
        color: #ff5b5b;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        }

        .tableMap-close:hover {
        color: #f00;
        }

        .tableMap-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
        }

        /* Table Box Styles */
        .tableMap-box {
        width: 140px;
        height: 110px;
        border-radius: 8px;
        position: relative;
        cursor: pointer;
        transition: transform 0.2s;
        }

        .tableMap-box:hover {
        transform: scale(1.05);
        }

        /* Status Colors */
        .tableMap-available {
        background-color: #4caf50; /* Green for available */
        }

        .tableMap-occupied {
        background-color: #f44336; /* Red for occupied */
        }

        .tableMap-dirty {
        background-color: #ff9800; /* Orange for dirty */
        }

        /* Table Styles */
        .tableMap-table {
        width: 90px;
        height: 60px;
        background-color: #8B4513; /* Brown color for tables */
        border-radius: 5px;
        margin: 15px auto 5px auto;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: white;
        font-weight: bold;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .tableMap-capacity {
        margin-top: 10px;
        display: flex;
        justify-content: center;
        gap: 5px;
        }

        .tableMap-capacity-dot {
        width: 12px;
        height: 12px;
        background-color: rgba(255, 255, 255, 0.7);
        border-radius: 50%;
        display: inline-block;
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 768px) {
        .tableMap-modal-content {
            width: 95%;
            padding: 15px;
        }
        
        .tableMap-container {
            gap: 15px;
        }
        
        .tableMap-box {
            width: 140px;
            height: 110px;
        }
        
        .tableMap-table {
            width: 85px;
            height: 55px;
            font-size: 14px;
        }
        }

        @media (max-width: 576px) {
        .tableMap-modal-content {
            width: 98%;
            padding: 10px;
        }
        
        .tableMap-header h2 {
            font-size: 20px;
        }
        
        .tableMap-container {
            gap: 10px;
        }
        
        .tableMap-box {
            width: 95px;
            height: 95px;
        }
        
        .tableMap-table {
            width: 75px;
            height: 45px;
            font-size: 12px;
            margin: 10px auto 5px auto;
        }
        
        .tableMap-capacity-dot {
            width: 10px;
            height: 10px;
        }
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- food catalogue modal -->
        <div class="food-catalogue" id="food-catalogue">

            <select id="hotel-selector">
                <option value="0">Select Hotel Type</option>
            </select>

            <div class="fc-close-btn">
                <i class="fa-solid fa-xmark"></i>
            </div>

            <span class="fc-headline">Food Categories (Main)</span>

            <!-- main categories container -->
            <div class="fc-main-cat-cont" id="fc-main-cat-cont"></div>

            <span class="fc-headline-mini">Sub Categories</span>

            <!-- sub categories container -->
            <div class="fc-sub-cat-cont" id="fc-sub-cat-cont"></div>

            <span class="fc-headline-mini">Food Items</span>

            <!-- food items displayer -->
            <div class="food-items-displayer" id="food-items-displayer">

            </div>
        </div>

        <!-- dine in orders slider window -->
        <div class="slider-window-orders" id="slider-window-orders">
            <button class="slider-closer-btn" onclick="closeSliderWindow()">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <button class="slider-refresh-btn" onclick="fetchPendingOrders()">
                <i class="fa-solid fa-arrows-rotate"></i>
            </button>

            <!-- headline -->
            <div class="headliner-orders">Dine-In Orders</div>

            <div class="dine-in-order-container">

            </div>
        </div>


        <div class="new-orders-indicator">
            <div id="new-order-counter">00</div>
            <i class="fa-solid fa-utensils"></i>
        </div>

        <div class="right-section">
            <div class="hotel-table-row">
                <select id="hotel-type">
                    <option value="0">Select Hotel Type</option>
                </select>
                <select id="table">
                    <option value="">Table</option>
                </select>
                <select id="food-category-selector">
                    <option value="default">Food Category</option>
                </select>
            </div>

            <div class="menu-grid-container">
                <div id="menu-container" class="menu-grid"></div>
            </div>
            <button type="button" id="viewTableMapBtn" class="btn btn-info">🗺️ View Map</button>
        </div>

        <div class="left-section">
            <div class="bill-info-header">
                <div id="bill-id-display">Next Bill: <span id="next-bill-id">Loading...</span></div>
            </div>
            <div class="option-footer">
                <button class="footer-opt-btn">
                    <i class="fa-solid fa-basket-shopping"></i>
                    Checkout Cart (+)
                </button>
                <button class="footer-opt-btn" id="kot-print-btn">
                    <i class="fa-solid fa-receipt"></i>
                    Print KOT (-)
                </button>
                <button class="footer-opt-btn">
                    <i class="fa-solid fa-money-check-dollar"></i>
                    Recent Transactions (F3)
                </button>
                <button class="footer-opt-btn">
                    <i class="fa-solid fa-wallet"></i>
                    Held Bills (F4)
                </button>
                <button class="footer-opt-btn">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    New Invoice (F5)
                </button>
                <button class="footer-opt-btn" id="temp-print-btn">
                    <i class="fa-solid fa-receipt"></i>
                    Print Temporary bill (F6)
                </button>
                <button class="footer-opt-btn" id="cancel-bill-btn">
                    <i class="fa fa-times-circle"></i>
                    Cancel Bill
                </button>
                <button class="footer-opt-btn" id="dyaend" onclick="window.location.href='dayend_balance.php'">
                    <i class="fa-solid fa-receipt"></i> day_end_balance
                </button>
                <!-- <button class="footer-opt-btn" id="cusadvance">
                    <i class="fa-solid fa-receipt"></i> Advanced payment
                </button>
                <button class="footer-opt-btn" id="adv-pay-slider">
                    <i class="fa-solid fa-receipt"></i> Toggle Advance Payment
                </button> -->
                <button class="footer-opt-btn" id="dine-orders-slider">
                    <i class="fa-solid fa-utensils"></i> Toggle Dine In Orders
                </button>
                <button class="footer-opt-btn" id="food-catalog-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dice-5-fill" viewBox="0 0 16 16">
                        <path d="M3 0a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V3a3 3 0 0 0-3-3zm2.5 4a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m8 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0M12 13.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3M5.5 12a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0M8 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3" />
                    </svg> <span style="margin-left: 10px;">Open Food Catlogue</span>
                </button>
            </div>
            <!-- Modal HTML for Customer Payments -->
            <div class="modal" id="customerPaymentModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2><i class="fa-solid fa-receipt"></i> Add Customer Payment</h2>
                        <span class="close" id="customerPaymentClose">&times;</span>
                    </div>
                    <div class="modal-body" style="padding: 20px;">
                        <form id="customerPaymentForm">
                            <div class="form-group">
                                <label for="phone_number">Phone Number</label>
                                <input type="tel" class="form-control" id="phone_number" name="phone_number" placeholder="Enter phone number" required>
                                <div id="phone-suggestions" class="customer-suggestions" style="display: none; position: absolute; z-index: 1000; background: white; border: 1px solid var(--border-color); border-radius: 5px; max-height: 200px; overflow-y: auto; width: 100%; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"></div>
                            </div>
                            <div class="form-group">
                                <label for="customer_id">Customer ID</label>
                                <input type="number" class="form-control" id="customer_id" name="customer_id" required>
                            </div>
                            <div class="form-group">
                                <label for="payment_amount">Payment Amount</label>
                                <input type="number" class="form-control" id="payment_amount" name="payment_amount" required>
                            </div>
                            <div class="form-group">
                                <label for="payment_date">Payment Date</label>
                                <input type="datetime-local" class="form-control" id="payment_date" name="payment_date" required>
                            </div>
                            <div class="form-group">
                                <label for="payment_type">Payment Type</label>
                                <select class="form-control" id="payment_type" name="payment_type" required>
                                    <option value="advance">Advance</option>
                                    <option value="partial">Partial</option>
                                    <option value="full">Full</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea class="form-control" id="notes" name="notes"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer" style="padding: 15px 20px;">
                        <button type="button" class="alert-btn alert-btn-secondary" id="customerPaymentCloseBtn">Close</button>
                        <button type="button" class="alert-btn alert-btn-primary" id="saveCustomerPayment">Save Payment</button>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready(function() {
                    // Existing code for opening/closing modal remains the same
                    $('#cusadvance').on('click', function() {
                        $('#customerPaymentModal').css('display', 'block');
                    });

                    $('#customerPaymentClose').on('click', function() {
                        $('#customerPaymentModal').css('display', 'none');
                    });

                    $('#customerPaymentCloseBtn').on('click', function() {
                        $('#customerPaymentModal').css('display', 'none');
                    });

                    // Phone number suggestion functionality
                    let selectedSuggestionIndex = -1;

                    $('#phone_number').on('input', function() {
                        const phoneNumber = $(this).val().trim();
                        const suggestionsContainer = $('#phone-suggestions');

                        if (phoneNumber.length >= 3) { // Start suggesting after 3 digits
                            $.ajax({
                                url: 'fetch_customers.php',
                                type: 'GET',
                                data: {
                                    query: phoneNumber
                                },
                                success: function(response) {
                                    try {
                                        const customers = JSON.parse(response);
                                        suggestionsContainer.empty().show();

                                        if (customers.length > 0) {
                                            customers.forEach((customer, index) => {
                                                const suggestion = $('<div>')
                                                    .addClass('suggestion-item')
                                                    .text(`${customer.phone_number} - ${customer.name}`)
                                                    .css({
                                                        padding: '8px 12px',
                                                        cursor: 'pointer',
                                                        borderBottom: '1px solid var(--border-color)'
                                                    })
                                                    .on('mouseover', function() {
                                                        $('.suggestion-item').removeClass('highlighted');
                                                        $(this).addClass('highlighted');
                                                        selectedSuggestionIndex = index;
                                                    })
                                                    .on('click', function() {
                                                        $('#phone_number').val(customer.phone_number);
                                                        $('#customer_id').val(customer.customer_id);
                                                        suggestionsContainer.hide();
                                                        selectedSuggestionIndex = -1;
                                                    });
                                                suggestionsContainer.append(suggestion);
                                            });
                                        } else {
                                            suggestionsContainer.html('<div style="padding: 8px 12px; color: var(--text-secondary);">No customers found</div>');
                                        }
                                    } catch (e) {
                                        suggestionsContainer.hide();
                                        showAlert({
                                            type: 'error',
                                            title: 'Error',
                                            message: 'Error processing customer data',
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                },
                                error: function() {
                                    suggestionsContainer.hide();
                                    showAlert({
                                        type: 'error',
                                        title: 'Connection Error',
                                        message: 'Failed to fetch customer data',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            });
                        } else {
                            suggestionsContainer.hide();
                        }
                    });

                    // Keyboard navigation for suggestions
                    $('#phone_number').on('keydown', function(e) {
                        const suggestions = $('#phone-suggestions .suggestion-item');
                        const suggestionsContainer = $('#phone-suggestions');

                        if (suggestions.length === 0 || !suggestionsContainer.is(':visible')) return;

                        switch (e.key) {
                            case 'ArrowDown':
                                e.preventDefault();
                                selectedSuggestionIndex = Math.min(selectedSuggestionIndex + 1, suggestions.length - 1);
                                updateHighlight(suggestions);
                                break;
                            case 'ArrowUp':
                                e.preventDefault();
                                selectedSuggestionIndex = Math.max(selectedSuggestionIndex - 1, -1);
                                updateHighlight(suggestions);
                                break;
                            case 'Enter':
                                e.preventDefault();
                                if (selectedSuggestionIndex >= 0) {
                                    suggestions.eq(selectedSuggestionIndex).trigger('click');
                                }
                                break;
                            case 'Escape':
                                suggestionsContainer.hide();
                                selectedSuggestionIndex = -1;
                                break;
                        }
                    });

                    // Function to update highlighted suggestion
                    function updateHighlight(suggestions) {
                        suggestions.removeClass('highlighted');
                        if (selectedSuggestionIndex >= 0) {
                            suggestions.eq(selectedSuggestionIndex).addClass('highlighted');
                        }
                    }

                    // Hide suggestions when clicking outside
                    $(document).on('click', function(e) {
                        if (!$(e.target).closest('#phone_number, #phone-suggestions').length) {
                            $('#phone-suggestions').hide();
                            selectedSuggestionIndex = -1;
                        }
                    });

                    // Existing save payment code with slight modification
                    // $('#saveCustomerPayment').on('click', function() {
                    //     var formData = $('#customerPaymentForm').serialize();

                    //     $.ajax({
                    //         url: 'add_advance_payment.php',
                    //         type: 'POST',
                    //         data: formData,
                    //         success: function(response) {
                    //             try {
                    //                 var result = JSON.parse(response);
                    //                 if (result.status === 'success') {
                    //                     showAlert({
                    //                         type: 'success',
                    //                         title: 'Success',
                    //                         message: result.message,
                    //                         confirmButtonText: 'OK',
                    //                         onConfirm: function() {
                    //                             $('#customerPaymentModal').css('display', 'none');
                    //                             location.reload();
                    //                         }
                    //                     });
                    //                 } else {
                    //                     showAlert({
                    //                         type: 'error',
                    //                         title: 'Error',
                    //                         message: result.message,
                    //                         confirmButtonText: 'OK'
                    //                     });
                    //                 }
                    //             } catch (e) {
                    //                 showAlert({
                    //                     type: 'error',
                    //                     title: 'Error',
                    //                     message: 'Invalid response from server',
                    //                     confirmButtonText: 'OK'
                    //                 });
                    //             }
                    //         },
                    //         error: function() {
                    //             showAlert({
                    //                 type: 'error',
                    //                 title: 'Connection Error',
                    //                 message: 'Failed to save payment. Please try again.',
                    //                 confirmButtonText: 'OK'
                    //             });
                    //         }
                    //     });
                    // });


                    $('#saveCustomerPayment').on('click', function() {
                        var formData = $('#customerPaymentForm').serialize();

                        $.ajax({
                            url: 'add_advance_payment.php',
                            type: 'POST',
                            data: formData,
                            success: function(response) {
                                try {
                                    var result = JSON.parse(response);
                                    if (result.status === 'success') {
                                        // Use values returned from server
                                        var payment_id = result.payment_id || 'ADV/001';
                                        var customer_id = result.customer_id;
                                        var customer_name = result.customer_name;
                                        var phone_number = result.phone_number;
                                        var payment_amount = result.payment_amount;
                                        var payment_date = result.payment_date;
                                        var payment_type = result.payment_type;
                                        var notes = result.notes;

                                        // Build URL with parameters properly separated
                                        var receiptUrl = 'customer_advance_payment_bill.php';
                                        receiptUrl += '?payment_id=' + encodeURIComponent(payment_id);
                                        receiptUrl += '&customer_id=' + encodeURIComponent(customer_id);
                                        receiptUrl += '&customer_name=' + encodeURIComponent(customer_name);
                                        receiptUrl += '&phone_number=' + encodeURIComponent(phone_number);
                                        receiptUrl += '&payment_amount=' + encodeURIComponent(payment_amount);
                                        receiptUrl += '&payment_date=' + encodeURIComponent(payment_date);
                                        receiptUrl += '&payment_type=' + encodeURIComponent(payment_type);
                                        receiptUrl += '&notes=' + encodeURIComponent(notes);

                                        // Open receipt in a new tab
                                        var receiptWindow = window.open(receiptUrl, '_blank');

                                        showAlert({
                                            type: 'success',
                                            title: 'Success',
                                            message: result.message,
                                            confirmButtonText: 'OK',
                                            onConfirm: function() {
                                                $('#customerPaymentModal').css('display', 'none');
                                                location.reload();
                                            }
                                        });
                                    } else {
                                        showAlert({
                                            type: 'error',
                                            title: 'Error',
                                            message: result.message,
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                } catch (e) {
                                    showAlert({
                                        type: 'error',
                                        title: 'Error',
                                        message: 'Invalid response from server',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            },
                            error: function() {
                                showAlert({
                                    type: 'error',
                                    title: 'Connection Error',
                                    message: 'Failed to save payment. Please try again.',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    });

                    // Existing close modal on outside click
                    $(window).on('click', function(event) {
                        var modal = $('#customerPaymentModal');
                        if (event.target === modal[0]) {
                            modal.css('display', 'none');
                        }
                    });
                });
            </script>

            <style>
                /* Additional styles for customer payment modal */
                #customerPaymentModal .modal-content {
                    width: 500px;
                    max-width: 90%;
                }

                #customerPaymentModal .form-group {
                    margin-bottom: 15px;
                }

                #customerPaymentModal .form-group label {
                    display: block;
                    margin-bottom: 5px;
                    color: var(--text-primary);
                    font-weight: 500;
                }

                #customerPaymentModal .form-control {
                    width: 100%;
                    padding: 10px;
                    border: 1px solid var(--border-color);
                    border-radius: 5px;
                    font-size: 14px;
                    color: var(--text-primary);
                    background-color: white;
                    transition: border-color 0.2s, box-shadow 0.2s;
                }

                #customerPaymentModal .form-control:focus {
                    outline: none;
                    border-color: var(--primary-color);
                    box-shadow: 0 0 0 2px rgba(26, 95, 122, 0.1);
                }

                #customerPaymentModal textarea.form-control {
                    min-height: 80px;
                    resize: vertical;
                }

                #customerPaymentModal .modal-footer {
                    display: flex;
                    justify-content: flex-end;
                    gap: 10px;
                }

                #customerPaymentModal .customer-suggestions .suggestion-item:hover,
                #customerPaymentModal .customer-suggestions .suggestion-item.highlighted {
                    background-color: var(--secondary-color);
                    color: white;
                }

                #customerPaymentModal .customer-suggestions .suggestion-item:last-child {
                    border-bottom: none;
                }
            </style>

            <!-- customer selector -->
            <div class="customer-selector hide-customer-selector">
                <div class="cus-toggler" onclick="toggleCustomerSelector()">
                    <i class="fa-regular fa-id-badge"></i>
                </div>
                <label class="customer-telephone-label">Customer Mobile Number:</label>
                <input type="text" id="customer-telephone" placeholder="Select Customer">
                <input type="text" id="customer-id-holder" hidden>
                <div class="customer-suggestions" id="customer-suggestions"></div>

                <!-- Add Customer Button -->
                <button id="new-customer-add-btn" class="new-customer-add-btn" onclick="openAddCustomerModal()">
                    <i class="fa-solid fa-user-plus"></i> Add New Customer
                </button>
            </div>

            <!-- Add Customer Modal -->
            <div id="new-customer-modal" class="new-customer-modal">
                <div class="new-customer-modal-content">
                    <div class="new-customer-modal-header">
                        <h3>Add New Customer</h3>
                        <span class="new-customer-modal-close" onclick="closeAddCustomerModal()">&times;</span>
                    </div>
                    <div class="new-customer-modal-body">
                        <form id="new-customer-form">
                            <div class="new-customer-form-group">
                                <label for="new-customer-name">Name <span class="new-customer-required">*</span></label>
                                <input type="text" id="new-customer-name" name="customer_name" required>
                                <div class="new-customer-error-message" id="new-customer-name-error"></div>
                            </div>

                            <div class="new-customer-form-group">
                                <label for="new-customer-phone">Phone Number <span class="new-customer-required">*</span></label>
                                <input type="text" id="new-customer-phone" name="customer_phone" required>
                                <div class="new-customer-error-message" id="new-customer-phone-error"></div>
                            </div>

                            <div class="new-customer-form-group">
                                <label for="new-customer-address">Address</label>
                                <textarea id="new-customer-address" name="customer_address" rows="2"></textarea>
                            </div>

                            <div class="new-customer-form-actions">
                                <button type="button" class="new-customer-cancel-btn" onclick="closeAddCustomerModal()">Cancel</button>
                                <button type="submit" class="new-customer-save-btn">Save Customer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- advance payment indicator -->
            <div class="adv-paym-indicator">
                <div class="adv-pay-icon">
                    <i class="fa-solid fa-coins"></i>
                </div>
                <!-- payment info cont -->
                <div class="adv-payment-info">
                    <h4 class="adv-paym-head">Advance Payment Found!</h4>
                    <p>Advance payment of LKR <span id="adv-payment-amount">00.00</span> found!</p>
                    <div style="display: flex; font-size: 0.8em;">
                        <label style="margin-right: 15px;">Deduct the advance payment: </label>
                        <input style="accent-color: black;" type="checkbox" id="adv-payment-check" onchange="handleCheckboxChange()">
                    </div>
                </div>
            </div>

            <!-- Item ID Input Section -->
            <!-- <div class="item-id-section">
                <div class="item-id-row">
                    <input type="text" id="item-id-input" placeholder="Item ID">
                    <input type="text" id="item-name-input" placeholder="Product Name" readonly>
                    <input type="number" id="quantity-input" value="1" min="1">
                    <input type="text" id="price-input" placeholder="Price" readonly>
                    <button id="add-manual-item">Add to Cart</button>
                </div>
            </div> -->

            <div class="item-id-section">
                <div class="item-id-row">
                    <input type="text" id="item-id-input" placeholder="Item ID">
                    <input type="text" id="item-name-input" placeholder="Product Name" readonly>
                    <select id="portion-size-input" style="display: none;">
                        <option value="regular">Family</option>
                        <option value="medium">Medium</option>
                        <option value="large">Large</option>
                    </select>
                    <input type="number" id="quantity-input" value="1" min="1">
                    <input type="text" id="price-input" placeholder="Price" readonly>
                    <button id="add-manual-item"></button>
                </div>
            </div>

            <div class="purchase-items">
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Free</th>
                            <th>Discount</th>
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
                    <strong>Discount (LKR):</strong> <input type="number" id="discount-input" value="0" min="0" step="0.01">
                </div>
                <!-- <div>
                    <strong>Total After Discount (LKR):</strong> <span id="total-after-discount">0.00</span>
                </div> -->
            </div>
        </div>

        
    </div>

    <!-- Table Map Modal -->
    <div id="tableMapModal" class="tableMap-modal">
    <div class="tableMap-modal-content">
        <div class="tableMap-header">
        <h2>Dining Stations</h2>
        <span class="tableMap-close">&times;</span>
        </div>
        <div class="tableMap-container" id="tableMapContainer">
        <!-- Tables will be dynamically added here using JavaScript -->
        </div>
    </div>
    </div>

    <!-- Enhanced Checkout Modal -->
    <div id="checkout-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fa-solid fa-shopping-cart"></i> Checkout</h2>
                <span class="close" id="checkout-modal-close">&times;</span>
            </div>

            <div class="checkout-items">
                <div class="section-title">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <h3>Cart Items</h3>
                </div>
                <table id="checkout-items-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Free Items</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="checkout-items-body"></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Total Before Discount:</strong></td>
                            <td id="total-before-discount">0.00</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Discount:</strong></td>
                            <td id="discount-amount">0.00</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Total Amount:</strong></td>
                            <td id="checkout-total-amount">0.00</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Service Charge:</strong></td>
                            <td id="service_charge">0.00</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Sub Total:</strong></td>
                            <td id="checkout-sub-total">0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="payment-section">
                <div class="section-title">
                    <i class="fa-solid fa-credit-card"></i>
                    <h3>Payments</h3>
                </div>
                <div class="payment-form">
                    <select id="payment-method">
                        <option value="cash">Cash</option>
                        <option value="bank">Bank Transfer</option>
                        <!-- <option value="card">Card</option> -->
                        <option value="credit">Credit Card</option>
                        <option value="debit">Debit Card</option>
                        <option value="cre">Credit</option>
                        <option value="uber">Uber</option>
                        <option value="pickme">PickMe</option>

                    </select>
                    <input type="text" id="card-id" placeholder="Card ID (if applicable)">
                    <input type="number" id="payment-amount" placeholder="Amount" step="0.01">
                    <button id="add-payment-btn"><i class="fa-solid fa-plus"></i> Add Payment</button>
                </div>

                <table id="payments-table">
                    <thead>
                        <tr>
                            <th>Method</th>
                            <th>Card ID</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="payments-body"></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-right"><strong>Total Paid:</strong></td>
                            <td id="total-paid-amount">0.00</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right"><strong>Balance:</strong></td>
                            <td id="balance-amount">0.00</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="checkout-actions">
                <button id="complete-bill-btn"><i class="fa-solid fa-check-circle"></i> Complete Bill</button>
                <button id="cancel-checkout-btn"><i class="fa-solid fa-times-circle"></i> Cancel</button>
            </div>
        </div>
    </div>

    <!-- Enhanced Held Bills Dropdown -->
    <div id="held-bills-dropdown" class="dropdown">
        <div class="dropdown-content">
            <div class="dropdown-header">
                <h3><i class="fa-solid fa-wallet"></i> Held Bills</h3>
                <span class="dropdown-close">&times;</span>
            </div>
            <div class="dropdown-body">
                <ul id="held-bills-list">
                    <!-- Will be populated dynamically -->
                </ul>
                <div id="empty-held-bills" class="empty-state" style="display: none;">
                    <i class="fa-solid fa-info-circle"></i>
                    <p>No held bills found</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bill Transactions Modal -->
    <div id="transactions-modal" class="modal">
        <div class="transactions-modal-content">
            <div class="modal-header">
                <h2 style="color:white;"><i class="fa-solid fa-money-check-dollar"></i> Bill Transactions</h2>
                <span class="close" id="close-transactions">&times;</span>
            </div>

            <div class="transactions-filters">
                <div class="filter-group">
                    <input type="date" id="date-from" placeholder="Date From">
                    <input type="date" id="date-to" placeholder="Date To">
                    <select id="status-filter">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <input type="text" id="search-bill" placeholder="Search by Bill ID or Customer">
                    <button id="apply-filters"><i class="fa-solid fa-filter"></i> Apply Filters</button>
                </div>
            </div>

            <div class="transactions-container">
                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th>Bill ID</th>
                            <th>Date & Time</th>
                            <th>Table</th>
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="transactions-list">
                        <!-- Will be populated dynamically -->
                    </tbody>
                </table>
                <div id="loading-spinner" class="loading-spinner">
                    <i class="fa-solid fa-spinner fa-spin"></i> Loading more transactions...
                </div>
                <div id="no-transactions" class="empty-state" style="display: none;">
                    <i class="fa-solid fa-receipt"></i>
                    <p>No transactions found</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bill Details Modal (shown when clicking on a transaction) -->
    <div id="bill-details-modal" class="modal">
        <div class="bill-details-modal-content">
            <div class="modal-header">
                <h2 style="color:white;"><i class="fa-solid fa-file-invoice"></i> Bill Details <span id="bill-id-header"></span></h2>
                <span class="close" id="close-bill-details">&times;</span>
            </div>

            <div class="bill-details-container">
                <div class="bill-summary">
                    <div class="summary-item"><strong>Bill Date:</strong> <span id="bill-date"></span></div>
                    <div class="summary-item"><strong>Table:</strong> <span id="bill-table"></span></div>
                    <div class="summary-item"><strong>Customer:</strong> <span id="bill-customer"></span></div>
                    <div class="summary-item"><strong>Status:</strong> <span id="bill-status"></span></div>
                    <div class="summary-item"><strong>Hotel Type:</strong> <span id="bill-hotel-type"></span></div>
                </div>

                <div class="bill-sections">
                    <div class="bill-section">
                        <h3><i class="fa-solid fa-utensils"></i> Items</h3>
                        <table class="bill-items-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="bill-items-list">
                                <!-- Will be populated dynamically -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                                    <td id="bill-subtotal">0.00</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Discount:</strong></td>
                                    <td id="bill-discount">0.00</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                    <td id="bill-total">0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="bill-section">
                        <h3><i class="fa-solid fa-credit-card"></i> Payments</h3>
                        <table class="bill-payments-table">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Method</th>
                                    <th>Card ID</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody id="bill-payments-list">
                                <!-- Will be populated dynamically -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Total Paid:</strong></td>
                                    <td id="bill-paid-total">0.00</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Balance:</strong></td>
                                    <td id="bill-balance">0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="bill-actions" style="display:none">
                    <button id="print-bill-btn" class="action-btn" hidden><i class="fa-solid fa-print"></i> Print</button>
                    <button id="reopen-bill-btn" class="action-btn" hidden><i class="fa-solid fa-unlock"></i> Reopen</button>
                    <button id="add-payment-to-bill-btn" class="action-btn" hidden><i class="fa-solid fa-plus"></i> Add Payment</button>
                    <button id="close-bill-details-btn" class="action-btn" hidden><i class="fa-solid fa-times"></i> Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="alert-container"></div>

    <div class="today-bill-count">
        <i class="fa-solid fa-flag"></i> Today Bills: <span id="daily-bill-counter">00</span>
    </div>

    <script>

        let checkoutWindowState = false

        document.addEventListener("DOMContentLoaded", function() {
            const numberInput = document.getElementById("quantity-input");
            numberInput.addEventListener("focus", function() {
                this.select();
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('item-id-input').focus();
            populateInitialTables();
        });
        document.addEventListener('keydown', (e) => {
            const numberInput = document.getElementById("quantity-input");
            const addBtn = document.getElementById('add-manual-item')
            if (e.key == "Enter" && document.activeElement == numberInput) {
                e.preventDefault()
                addBtn.click()
            }
        })
        // Hotel Type Dropdown Population
        // function populateHotelTypes() {
        //     fetch('fetch_hotel_types.php')
        //         .then(response => response.json())
        //         .then(data => {
        //             const hotelTypeSelect = document.getElementById('hotel-type');
        //             data.forEach(type => {
        //                 const option = document.createElement('option');
        //                 option.value = type.id;
        //                 option.textContent = type.name;
        //                 hotelTypeSelect.appendChild(option);
        //             });
        //         });
        // }

        // function populateHotelTypesForSelector() {
        //     fetch('fetch_hotel_types.php')
        //         .then(response => response.json())
        //         .then(data => {
        //             const hotelTypeSelect = document.getElementById('hotel-selector');
        //             data.forEach(type => {
        //                 const option = document.createElement('option');
        //                 option.value = type.id;
        //                 option.textContent = type.name;
        //                 hotelTypeSelect.appendChild(option);
        //             });
        //         });
        // }


        // Table Dropdown Population
        // function populateTables(hotelTypeId, callback) {
        //     fetch(`fetch_tables.php`)
        //         .then(response => response.json())
        //         .then(data => {
        //             const tableSelect = document.getElementById('table');
        //             tableSelect.innerHTML = '<option value="">Select Table</option>';
        //             data.forEach(table => {
        //                 const option = document.createElement('option');
        //                 option.value = table.table_id;
        //                 option.textContent = `Table ${table.table_id} (Capacity: ${table.capacity}, ${table.is_available ? 'Available' : 'Occupied'})`;
        //                 tableSelect.appendChild(option);
        //             });
        //             if (typeof callback === 'function') {
        //                 callback();
        //             }
        //         })
        //         .catch(error => {
        //             console.error('Error fetching tables:', error);
        //             // Still call the callback even if there's an error
        //             if (typeof callback === 'function') {
        //                 callback();
        //             }
        //         });
        // }



// localStorage keys
const STORAGE_KEYS = {
    HOTEL_TYPE: 'selectedHotelType',
    TABLE: 'selectedTable',
    HELD_BILL_ID: 'heldBillId',
    IS_HELD_BILL: 'isHeldBill'
};

// Save values to localStorage
function saveToLocalStorage(key, value) {
    try {
        localStorage.setItem(key, value);
    } catch (error) {
        console.error('Error saving to localStorage:', error);
    }
}

// Get values from localStorage
function getFromLocalStorage(key) {
    try {
        return localStorage.getItem(key);
    } catch (error) {
        console.error('Error reading from localStorage:', error);
        return null;
    }
}

// Restore saved selections from localStorage
function restoreSelections() {
    // Restore hotel type selection
    const savedHotelType = getFromLocalStorage(STORAGE_KEYS.HOTEL_TYPE);
    if (savedHotelType) {
        const hotelTypeSelect = document.getElementById('hotel-type');
        const hotelSelectorSelect = document.getElementById('hotel-selector');
        
        if (hotelTypeSelect) {
            hotelTypeSelect.value = savedHotelType;
        }
        if (hotelSelectorSelect) {
            hotelSelectorSelect.value = savedHotelType;
        }
    }

    // Restore table selection
    const savedTable = getFromLocalStorage(STORAGE_KEYS.TABLE);
    if (savedTable) {
        const tableSelect = document.getElementById('table');
        if (tableSelect) {
            tableSelect.value = savedTable;
        }
    }
}

// Hotel Type Dropdown Population with localStorage restore
function populateHotelTypes() {
    fetch('fetch_hotel_types.php')
        .then(response => response.json())
        .then(data => {
            const hotelTypeSelect = document.getElementById('hotel-type');
            
            // Clear existing options except the first one
            hotelTypeSelect.innerHTML = '<option value="0">Select Hotel Type</option>';
            
            data.forEach(type => {
                const option = document.createElement('option');
                option.value = type.id;
                option.textContent = type.name;
                hotelTypeSelect.appendChild(option);
            });

            // Restore saved selection
            const savedHotelType = getFromLocalStorage(STORAGE_KEYS.HOTEL_TYPE);
            if (savedHotelType && savedHotelType !== '0') {
                hotelTypeSelect.value = savedHotelType;
            }

            // Add event listener for changes
            hotelTypeSelect.addEventListener('change', function() {
                const selectedValue = this.value;
                saveToLocalStorage(STORAGE_KEYS.HOTEL_TYPE, selectedValue);
                
                // Sync with hotel-selector
                const hotelSelectorSelect = document.getElementById('hotel-selector');
                if (hotelSelectorSelect) {
                    hotelSelectorSelect.value = selectedValue;
                }
            });
        })
        .catch(error => {
            console.error('Error fetching hotel types:', error);
        });
}

// Hotel Selector Dropdown Population (synchronized with hotel-type)
function populateHotelTypesForSelector() {
    fetch('fetch_hotel_types.php')
        .then(response => response.json())
        .then(data => {
            const hotelSelectorSelect = document.getElementById('hotel-selector');
            
            // Clear existing options except the first one
            hotelSelectorSelect.innerHTML = '<option value="0">Select Hotel Type</option>';
            
            data.forEach(type => {
                const option = document.createElement('option');
                option.value = type.id;
                option.textContent = type.name;
                hotelSelectorSelect.appendChild(option);
            });

            // Restore saved selection
            const savedHotelType = getFromLocalStorage(STORAGE_KEYS.HOTEL_TYPE);
            if (savedHotelType && savedHotelType !== '0') {
                hotelSelectorSelect.value = savedHotelType;
            }

            // Add event listener for changes
            hotelSelectorSelect.addEventListener('change', function() {
                const selectedValue = this.value;
                saveToLocalStorage(STORAGE_KEYS.HOTEL_TYPE, selectedValue);
                
                // Sync with hotel-type
                const hotelTypeSelect = document.getElementById('hotel-type');
                if (hotelTypeSelect) {
                    hotelTypeSelect.value = selectedValue;
                }
            });
        })
        .catch(error => {
            console.error('Error fetching hotel types for selector:', error);
        });
}

// Restore held bill status and display
function restoreHeldBillStatus() {
    const savedHeldBillId = getFromLocalStorage(STORAGE_KEYS.HELD_BILL_ID);
    const savedIsHeldBill = getFromLocalStorage(STORAGE_KEYS.IS_HELD_BILL);
    
    if (savedHeldBillId && savedIsHeldBill === 'true') {
        // Restore global variables
        if (typeof currentBillId !== 'undefined') {
            currentBillId = savedHeldBillId;
        }
        if (typeof isHeldBill !== 'undefined') {
            isHeldBill = true;
        }
        
        // Update display and protect it from being overwritten
        const billDisplayElement = document.getElementById('next-bill-id');
        if (billDisplayElement) {
            billDisplayElement.textContent = `Held Bill #${savedHeldBillId}`;
            // Mark the element to prevent fetchNextBillId from overwriting it
            billDisplayElement.dataset.isHeldBill = 'true';
        }
        
        console.log(`Restored held bill status: Bill #${savedHeldBillId}`);
        return true;
    }
    return false;
}

// Wrapper function to protect held bill display from being overwritten
function protectedFetchNextBillId() {
    const billDisplayElement = document.getElementById('next-bill-id');
    
    // Only fetch next bill ID if we're not in held bill mode
    if (!billDisplayElement?.dataset.isHeldBill && 
        (!isHeldBill || isHeldBill === false)) {
        
        if (typeof fetchNextBillId === 'function') {
            fetchNextBillId();
        }
    } else {
        console.log('Skipping fetchNextBillId - held bill is loaded');
    }
}
function forceRestoreTableSelection() {
    const savedTable = getFromLocalStorage(STORAGE_KEYS.TABLE);
    if (savedTable) {
        const tableSelect = document.getElementById('table');
        if (tableSelect) {
            const savedOption = tableSelect.querySelector(`option[value="${savedTable}"]`);
            if (savedOption) {
                // Enable the option if it's disabled and set the value
                if (savedOption.disabled) {
                    savedOption.disabled = false;
                }
                tableSelect.value = savedTable;
                console.log(`Force restored table ${savedTable} from localStorage`);
                return true;
            }
        }
    }
    return false;
}

// Enhanced populateInitialTables with better restoration logic
function populateInitialTables(callback) {
    fetch(`fetch_tables.php`)
        .then(response => response.json())
        .then(data => {
            const tableSelect = document.getElementById('table');

            // Create a wrapper if it doesn't exist
            if (!tableSelect.closest('.table-selection-wrapper')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'table-selection-wrapper';
                tableSelect.parentNode.insertBefore(wrapper, tableSelect);
                wrapper.appendChild(tableSelect);
            }

            tableSelect.innerHTML = '<option value="">Select Table</option>';

            data.forEach(table => {
                const option = document.createElement('option');
                option.value = table.table_id;

                // Determine status and styling
                let statusText = '';
                let statusClass = '';
                let isDisabled = false;

                if (table.status === 'occupied') {
                    statusText = ' [Occupied]';
                    statusClass = 'table-occupied';
                    isDisabled = true;
                } else if (table.status === 'dirty') {
                    statusText = ' [Dirty]';
                    statusClass = 'table-dirty';
                    option.dataset.isDirty = 'true';
                } else {
                    statusText = ' [Available]';
                    statusClass = 'table-available';
                }

                option.textContent = `Table ${table.table_id} (Capacity: ${table.capacity})${statusText}`;
                option.className = statusClass;
                option.disabled = isDisabled;

                tableSelect.appendChild(option);
            });

            // Restore saved table selection
            const savedTable = getFromLocalStorage(STORAGE_KEYS.TABLE);
            if (savedTable) {
                // Check if the saved table still exists
                const savedOption = tableSelect.querySelector(`option[value="${savedTable}"]`);
                if (savedOption) {
                    // For held bills, restore the table even if it shows as occupied/dirty
                    // since we're continuing an existing order
                    tableSelect.value = savedTable;
                    
                    // If table is disabled but we have a saved selection, enable it temporarily
                    // This handles the case where we're loading a held bill for a table that's marked as occupied
                    if (savedOption.disabled) {
                        console.log(`Restoring previously selected table ${savedTable} from localStorage (was disabled)`);
                        savedOption.disabled = false;
                        tableSelect.value = savedTable;
                    }
                } else {
                    // Clear saved selection only if table completely doesn't exist
                    console.log(`Saved table ${savedTable} no longer exists, clearing localStorage`);
                    saveToLocalStorage(STORAGE_KEYS.TABLE, '');
                }
            }

            // Ensure we only attach the event listener once
            if (!tableSelect.dataset.hasListener) {
                tableSelect.dataset.hasListener = 'true';

                // Remove any existing listener
                tableSelect.removeEventListener('change', handleTableSelect);
                tableSelect.removeEventListener('change', handleTableSelectWithStorage);

                // Add the enhanced listener with localStorage
                tableSelect.addEventListener('change', handleTableSelectWithStorage);
            }

            if (typeof callback === 'function') {
                callback();
            }
        })
        .catch(error => {
            console.error('Error fetching tables:', error);
            if (typeof callback === 'function') {
                callback();
            }
        });
}

// Enhanced table select handler with localStorage
function handleTableSelectWithStorage(event) {
    const selectedValue = event.target.value;
    
    // Save to localStorage
    saveToLocalStorage(STORAGE_KEYS.TABLE, selectedValue);
    
    // Call original handler if it exists
    if (typeof handleTableSelect === 'function') {
        handleTableSelect(event);
    }
}

// Enhanced loadHeldBill function with localStorage saving
function loadHeldBill(billId) {
    fetch(`fetch_held_bill.php?bill_id=${billId}`)
        .then(response => response.json())
        .then(data => {
            console.log("Loaded bill data:", data);
            
            // Set current bill ID and status
            currentBillId = billId;
            isHeldBill = true;
            const billDisplayElement = document.getElementById('next-bill-id');
            billDisplayElement.textContent = `Held Bill #${billId}`;
            
            // Mark element to prevent fetchNextBillId from overwriting it
            billDisplayElement.dataset.isHeldBill = 'true';
            
            // Save held bill status to localStorage
            saveToLocalStorage(STORAGE_KEYS.HELD_BILL_ID, billId);
            saveToLocalStorage(STORAGE_KEYS.IS_HELD_BILL, 'true');
            
            // Update hotel type and table
            const hotelTypeId = data.hotel_type;
            
            console.log("Hotel Type ID:", hotelTypeId);
            
            // Update dropdowns
            document.getElementById('hotel-type').value = hotelTypeId;
            document.getElementById('hotel-selector').value = hotelTypeId;
            
            // Save hotel type to localStorage
            saveToLocalStorage(STORAGE_KEYS.HOTEL_TYPE, hotelTypeId);
            
            // IMPORTANT: Handle Uber/Pickme reference number field AFTER setting the hotel selector value
            setTimeout(() => {
                // Remove any existing reference input first
                const existingRefInput = document.getElementById('reference-input-container');
                if (existingRefInput) {
                    existingRefInput.remove();
                }
                
                // Check if Uber (id: 4) or Pickme (id: 6) is selected - use string comparison since values might be strings
                if (hotelTypeId == 4 || hotelTypeId == 6) {
                    console.log("Creating reference input for Uber/Pickme");
                    
                    const serviceName = hotelTypeId == 4 ? 'Uber' : 'Pickme';
                    const hotelSelector = document.getElementById('hotel-selector');
                    
                    if (!hotelSelector) {
                        console.error("Hotel selector element not found!");
                        return;
                    }
                    
                    // Create reference number input
                    const refContainer = document.createElement('div');
                    refContainer.id = 'reference-input-container';
                    refContainer.style.marginTop = '10px';
                    refContainer.style.marginBottom = '15px';
                    
                    // Use the saved reference number from the database if available
                    const savedRefNumber = data.reference_number || '';
                    console.log("Saved reference number:", savedRefNumber);
                    
                    refContainer.innerHTML = `
                        <label for="ref-number">${serviceName} Reference Number:</label>
                        <input type="text" id="ref-number" 
                            style="display:block; width:100%; padding:8px; margin-top:5px; border:1px solid #ccc; border-radius:4px;" 
                            placeholder="Enter ${serviceName} reference number" 
                            value="${savedRefNumber}" required>
                    `;
                    
                    // Insert after hotel selector
                    if (hotelSelector.nextSibling) {
                        hotelSelector.parentNode.insertBefore(refContainer, hotelSelector.nextSibling);
                    } else {
                        hotelSelector.parentNode.appendChild(refContainer);
                    }
                    
                    console.log("Reference input created successfully");
                } else {
                    console.log("Not Uber/Pickme, no reference input needed");
                }
                
                // Continue with the rest of the function
                if (data.table_id) {
                    document.getElementById('table').value = data.table_id;
                    // Save table selection to localStorage
                    saveToLocalStorage(STORAGE_KEYS.TABLE, data.table_id);
                    
                    // Ensure table selection is properly set even if table appears occupied
                    setTimeout(() => {
                        const tableSelect = document.getElementById('table');
                        if (tableSelect && tableSelect.value !== data.table_id) {
                            // Force restore the table selection
                            forceRestoreTableSelection();
                        }
                    }, 50);
                }
                
                // Clear cart and add held items
                cart.length = 0;
                data.items.forEach(item => {
                    // Create display name with portion size if available
                    const portionSize = item.portion_size;
                    cart.push({
                        id: item.item_id,
                        uniqueId: `${item.item_id}-${portionSize}`,
                        name: item.product_name,
                        price: parseFloat(item.price),
                        quantity: parseInt(item.quantity),
                        portionSize: portionSize,
                        fc: parseInt(item.free_count),
                    });
                });
                localStorage.setItem('restaurant_cart', JSON.stringify(cart));
                updateCart();
                
                // Clear payments and add held payments
                payments.length = 0;
                data.payments.forEach(payment => {
                    payments.push({
                        method: payment.payment_method,
                        cardId: payment.card_id,
                        amount: parseFloat(payment.amount)
                    });
                });
                
                // Close dropdown using classList
                document.getElementById('held-bills-dropdown').classList.remove('show');
                document.querySelector('.footer-opt-btn:nth-child(4)').classList.remove('active-held');
            }, 100); // Short timeout to ensure hotel selector value is set
        })
        .catch(error => {
            console.error('Error loading held bill:', error);
            alert('Error loading held bill. Please try again.');
        });
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // First, restore held bill status if it exists
    restoreHeldBillStatus();
    
    // Populate dropdowns
    populateHotelTypes();
    populateHotelTypesForSelector();
    
    // Populate tables with enhanced restoration
    populateInitialTables(() => {
        // After tables are populated, force restore if needed
        // This is especially important for held bills after page refresh
        setTimeout(() => {
            forceRestoreTableSelection();
            
            // Use protected fetchNextBillId only if not in held bill mode
            protectedFetchNextBillId();
        }, 100);
    });
});


// Clear saved selections function
function clearSavedSelections() {
    localStorage.removeItem(STORAGE_KEYS.HOTEL_TYPE);
    localStorage.removeItem(STORAGE_KEYS.TABLE);
    localStorage.removeItem(STORAGE_KEYS.HELD_BILL_ID);
    localStorage.removeItem(STORAGE_KEYS.IS_HELD_BILL);
    
    // Reset dropdowns to default values
    const hotelTypeSelect = document.getElementById('hotel-type');
    const hotelSelectorSelect = document.getElementById('hotel-selector');
    const tableSelect = document.getElementById('table');
    
    if (hotelTypeSelect) hotelTypeSelect.value = '0';
    if (hotelSelectorSelect) hotelSelectorSelect.value = '0';
    if (tableSelect) tableSelect.value = '';
    
    // Reset bill display to normal
    const billDisplayElement = document.getElementById('next-bill-id');
    if (billDisplayElement) {
        // Remove the held bill protection
        delete billDisplayElement.dataset.isHeldBill;
        // Reset global variables
        if (typeof isHeldBill !== 'undefined') {
            isHeldBill = false;
        }
        // Now fetch next bill ID
        if (typeof fetchNextBillId === 'function') {
            fetchNextBillId();
        }
    }
}

// Enhanced Create new bill function with localStorage clearing
function createNewBill() {
    const selectedHotelType = document.getElementById('hotel-selector').value;
    const selectedTable = document.getElementById('table').value;
    
    // Check if cart is empty
    if (!Array.isArray(cart) || cart.length === 0) {
        showAlert({
            type: 'warning',
            title: 'Empty Cart',
            message: 'Cannot hold a bill with an empty cart.',
            confirmButtonText: 'OK'
        });
        
        // Add Enter key listener to close alert
        setTimeout(() => {
            const handleEnter = function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    
                    // Look for visible buttons with OK text
                    const okButton = Array.from(document.querySelectorAll('button')).find(btn => {
                        const isVisible = btn.offsetParent !== null;
                        const hasOkText = btn.textContent.trim().toUpperCase().includes('OK');
                        return isVisible && hasOkText;
                    });
                    
                    if (okButton) {
                        okButton.click();
                    }
                    
                    document.removeEventListener('keydown', handleEnter);
                }
            };
            document.addEventListener('keydown', handleEnter);
        }, 100);
        
        fetchNextBillId();
        return; // Exit the function early
    }
    else if (selectedHotelType == "0") {
        showAlert({
            type: 'warning',
            title: 'Select Hotel Type',
            message: 'Please select a hotel type!',
            confirmButtonText: 'OK'
        });
        
        // Add Enter key listener to close alert
        setTimeout(() => {
            const handleEnter = function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    
                    // Look for visible buttons with OK text
                    const okButton = Array.from(document.querySelectorAll('button')).find(btn => {
                        const isVisible = btn.offsetParent !== null;
                        const hasOkText = btn.textContent.trim().toUpperCase().includes('OK');
                        return isVisible && hasOkText;
                    });
                    
                    if (okButton) {
                        okButton.click();
                    }
                    
                    document.removeEventListener('keydown', handleEnter);
                }
            };
            document.addEventListener('keydown', handleEnter);
        }, 100);
        
        return; // Exit the function early
    }
    else if (selectedHotelType == "1" && selectedTable == "") {
        showAlert({
            type: 'warning',
            title: 'Select Table',
            message: 'Please select a table!',
            confirmButtonText: 'OK'
        });
        
        // Add Enter key listener to close alert
        setTimeout(() => {
            const handleEnter = function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    
                    // Look for visible buttons with OK text
                    const okButton = Array.from(document.querySelectorAll('button')).find(btn => {
                        const isVisible = btn.offsetParent !== null;
                        const hasOkText = btn.textContent.trim().toUpperCase().includes('OK');
                        return isVisible && hasOkText;
                    });
                    
                    if (okButton) {
                        okButton.click();
                    }
                    
                    document.removeEventListener('keydown', handleEnter);
                }
            };
            document.addEventListener('keydown', handleEnter);
        }, 100);
        return; // Exit the function early
    }

    // Hold current bill
    const hotelTypeId = document.getElementById('hotel-selector').value || document.getElementById('hotel-type').value;
    const billData = {
        bill_id: currentBillId,
        hotel_type: document.getElementById('hotel-selector').value || document.getElementById('hotel-type').value,
        table_id: document.getElementById('table').value,
        items: cart.map(item => ({
            item_id: item.id,
            quantity: item.quantity,
            portion_size: item.portionSize,
            name: item.name,
            price: item.price,
            fc: item.fc,
        })),
        payments: payments
    };
    
    if (hotelTypeId === '4' || hotelTypeId === '6') {
        const referenceInput = document.getElementById('ref-number') || document.getElementById('reference-number');
        billData.reference_number = referenceInput.value.trim();
    }
    
    fetch('hold_bill.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(billData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear cart and payments
            cart.length = 0;
            payments.length = 0;
            clearPersistantCart();
            updateCart();
            
            // Clear localStorage and reset dropdowns to default values
            clearSavedSelections();
            
            // Fetch new bill ID
            fetchNextBillId();
            populateInitialTables();

            setTimeout(() => {
                window.location.reload();
            }, 100);

        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error holding bill:', error);
        alert('Error holding bill. Please try again.');
    });
}

        // Enhanced table selection handler with better UX
        function handleTableSelect(e) {
            const tableSelect = e.target;
            const selectedOption = tableSelect.options[tableSelect.selectedIndex];

            // If this is a dirty table, trigger the reset action
            if (selectedOption && selectedOption.dataset.isDirty === 'true') {
                const tableId = selectedOption.value;

                // Save the selected index
                const selectedIndex = tableSelect.selectedIndex;

                // Reset the dropdown to the default option
                tableSelect.selectedIndex = 0;

                // Show confirmation with custom styling
                showTableResetConfirmation(tableId, selectedIndex);
            }
        }

        // Custom confirmation dialog for resetting table
        function showTableResetConfirmation(tableId, selectedIndex) {
            // Use the showAlert function if available, or create a better-styled confirmation
            if (typeof showAlert === 'function') {
                showAlert({
                    type: 'warning',
                    title: 'Reset Table Status',
                    message: `Are you sure you want to mark Table ${tableId} as clean and available?`,
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Reset Table',
                    cancelButtonText: 'Cancel',
                    onConfirm: () => {
                        resetTableWithAnimation(tableId);
                    }
                });
                
                // Add Enter key listener to click "Yes, Reset Table" button
                setTimeout(() => {
                    const handleEnter = function(event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            
                            // Look for visible buttons with "Yes, Reset Table" text
                            const confirmButton = Array.from(document.querySelectorAll('button')).find(btn => {
                                const isVisible = btn.offsetParent !== null;
                                const buttonText = btn.textContent.trim().toUpperCase();
                                return isVisible && (
                                    buttonText.includes('YES, RESET TABLE') ||
                                    buttonText.includes('YES') ||
                                    buttonText.includes('RESET TABLE') ||
                                    buttonText.includes('CONFIRM')
                                );
                            });
                            
                            if (confirmButton) {
                                confirmButton.click();
                            }
                            
                            document.removeEventListener('keydown', handleEnter);
                        }
                    };
                    document.addEventListener('keydown', handleEnter);
                }, 100);
            } else {
                // Fallback to standard confirm
                if (confirm(`Are you sure you want to reset Table ${tableId} to Available?`)) {
                    resetTableWithAnimation(tableId);
                }
            }
        }

        // Reset table with animation
        function resetTableWithAnimation(tableId) {
            // Find the table option
            const tableSelect = document.getElementById('table');
            let dirtyOption = null;

            // Find the option for this table
            for (let i = 0; i < tableSelect.options.length; i++) {
                if (tableSelect.options[i].value === tableId.toString()) {
                    dirtyOption = tableSelect.options[i];
                    break;
                }
            }

            // If found, add animation
            if (dirtyOption) {
                // Create a visual indicator for the reset operation
                const indicator = document.createElement('div');
                indicator.className = 'reset-indicator';
                indicator.textContent = `Resetting Table ${tableId}...`;
                indicator.style.position = 'fixed';
                indicator.style.top = '20px';
                indicator.style.right = '20px';
                indicator.style.padding = '10px 15px';
                indicator.style.backgroundColor = '#ff9800';
                indicator.style.color = 'white';
                indicator.style.borderRadius = '4px';
                indicator.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';
                indicator.style.zIndex = '9999';
                indicator.style.transition = 'opacity 0.3s ease';

                document.body.appendChild(indicator);

                // Make the actual reset call
                resetTableStatus(tableId)
                    .then(success => {
                        if (success) {
                            // Update indicator
                            indicator.textContent = `Table ${tableId} is now Available`;
                            indicator.style.backgroundColor = '#4CAF50';

                            // Fade out after showing success
                            setTimeout(() => {
                                indicator.style.opacity = '0';
                                setTimeout(() => {
                                    document.body.removeChild(indicator);
                                }, 300);
                            }, 2000);
                        } else {
                            // Show error
                            indicator.textContent = `Failed to reset Table ${tableId}`;
                            indicator.style.backgroundColor = '#f44336';

                            // Fade out after showing error
                            setTimeout(() => {
                                indicator.style.opacity = '0';
                                setTimeout(() => {
                                    document.body.removeChild(indicator);
                                }, 300);
                            }, 2000);
                        }
                    });
            } else {
                // If option not found, just call reset without animation
                resetTableStatus(tableId);
            }
        }

        // Reset table status with Promise
        function resetTableStatus(tableId) {
            return fetch('reset_table_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        table_id: tableId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Refresh the tables dropdown
                        populateInitialTables();
                        return true;
                    } else {
                        console.error(`Error: ${data.message || 'Failed to reset table status'}`);
                        return false;
                    }
                })
                .catch(error => {
                    console.error('Error resetting table status:', error);
                    return false;
                });
        }


        // Menu Items Population
        // function populateMenu(hotelType, selectedCategory) {
        //     if (selectedCategory == "bakery_and_beverages") {
        //         const menuContainer = document.getElementById('menu-container');
        //         menuContainer.innerHTML = '';
        //         bakeryItems.forEach(item => {
        //             let dishPrice = 0
        //             switch (parseInt(hotelType)) {
        //                 case 1:
        //                     dishPrice = item.dining_price
        //                     break;
        //                 case 4:
        //                     dishPrice = item.uber_pickme_price
        //                     break;
        //                 case 6:
        //                     dishPrice = item.uber_pickme_price
        //                     break;
        //                 case 7:
        //                     dishPrice = item.takeaway_price
        //                     break;
        //                 case 11:
        //                     dishPrice = item.delivery_service_item_price
        //                     break;

        //                 default:
        //                     dishPrice = item.item_price
        //             }
        //             const menuItem = document.createElement('div');
        //             menuItem.classList.add('menu-item');
        //             menuItem.innerHTML = `
        //                     <h4>${item.item_name}</h4>
        //                     <p>Type: ${item.item_type}</p>
        //                     <p>Category: ${item.bakery_category}</p>
        //                     <p>Price: LKR ${!dishPrice ? 0.00 : dishPrice}</p>
        //                     <button onclick="addToCart('${item.item_id}', '${item.item_name}', ${dishPrice}, '${item.item_category || item.bakery_category}')">Add to Cart</button>
        //                 `;
        //             menuContainer.appendChild(menuItem);
        //         });
        //         return
        //     }

        //     fetch(`fetch_menu_items.php?hotel_type=${hotelType}&category=${selectedCategory}`)
        //         .then(response => response.json())
        //         .then(data => {
        //             const menuContainer = document.getElementById('menu-container');
        //             menuContainer.innerHTML = '';
        //             if (!Array.isArray(data)) {
        //                 console.error("NOT THE EXPECTED OUTPUT:", data);
        //                 return;
        //             }
        //             data.forEach(item => {
        //                 let dishPrice = 0
        //                 switch (parseInt(hotelType)) {
        //                     case 1:
        //                         dishPrice = item.dining_price
        //                         break;
        //                     case 4:
        //                         dishPrice = item.uber_pickme_price
        //                         break;
        //                     case 6:
        //                         dishPrice = item.uber_pickme_price
        //                         break;
        //                     case 7:
        //                         dishPrice = item.takeaway_price
        //                         break;
        //                     case 11:
        //                         dishPrice = item.delivery_service_item_price
        //                         break;

        //                     default:
        //                         dishPrice = item.item_price
        //                 }
        //                 const menuItem = document.createElement('div');
        //                 menuItem.classList.add('menu-item');
        //                 menuItem.innerHTML = `
        //                     <h4>${item.item_name}</h4>
        //                     <p>Type: ${item.item_type}</p>
        //                     <p>Category: ${item.item_category}</p>
        //                     <p>Price: LKR ${!dishPrice ? 0.00 : dishPrice}</p>
        //                     <button onclick="addToCart('${item.item_id}', '${item.item_name}', ${dishPrice}, '${item.item_category || item.bakery_category}')">Add to Cart</button>
        //                 `;
        //                 menuContainer.appendChild(menuItem);
        //             });
        //         });
        // }

        // // Cart Management
        let cart = [];

        // function addToCart(itemId, itemName, price, itemCategory) {
        //     const existingItem = cart.find(item => item.id === itemId);
        //     if (existingItem) {
        //         existingItem.quantity++;
        //     } else {
        //         cart.push({
        //             id: itemId,
        //             name: itemName,
        //             price,
        //             quantity: 1,
        //             itemCategory
        //         });
        //     }
        //     updateCart();
        // }


        function populateMenu(hotelType, selectedCategory) {
            if (selectedCategory == "bakery_and_beverages") {
                const menuContainer = document.getElementById('menu-container');
                menuContainer.innerHTML = '';
                bakeryItems.forEach(item => {
                    let dishPrice = 0;
                    // For bakery items, you might need to adjust this based on how you handle portion sizes
                    switch (parseInt(hotelType)) {
                        case 1:
                            dishPrice = item.dining_price;
                            break;
                        case 4:
                            dishPrice = item.uber_pickme_price;
                            break;
                        case 6:
                            dishPrice = item.uber_pickme_price;
                            break;
                        case 7:
                            dishPrice = item.takeaway_price;
                            break;
                        case 11:
                            dishPrice = item.delivery_service_item_price;
                            break;
                        default:
                            dishPrice = item.item_price;
                    }

                    const menuItem = document.createElement('div');
                    menuItem.classList.add('menu-item');

                    // Check if price exists
                    const hasPrice = dishPrice !== null && dishPrice !== undefined && dishPrice !== 0;

                    menuItem.innerHTML = `
                <h4>${item.item_name}</h4>
                <p>Type: ${item.item_type}</p>
                <p>Category: ${item.bakery_category}</p>
                <p>Price: LKR ${!hasPrice ? '0.00 (Not Available)' : dishPrice}</p>
                ${hasPrice ? 
                    `<button onclick="addToCart('${item.item_id}', '${item.item_name}', ${dishPrice}, '${item.item_category || item.bakery_category}', '')">Add to Cart</button>` : 
                    '<p class="not-available">Not available for this service type</p>'}
            `;
                    menuContainer.appendChild(menuItem);
                });
                return;
            }

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
                        const menuItem = document.createElement('div');
                        menuItem.classList.add('menu-item');

                        // Get all prices for this hotel type
                        let regularPrice = 0,
                            mediumPrice = 0,
                            largePrice = 0;

                        switch (parseInt(hotelType)) {
                            case 1: // Dining
                                regularPrice = item.regular_price;
                                mediumPrice = item.medium_price;
                                largePrice = item.large_price;
                                break;
                            case 4: // Uber/PickMe
                            case 6:
                                regularPrice = item.uber_pickme_regular;
                                mediumPrice = item.uber_pickme_medium;
                                largePrice = item.uber_pickme_large;
                                break;
                            case 7: // Takeaway
                                regularPrice = item.takeaway_regular;
                                mediumPrice = item.takeaway_medium;
                                largePrice = item.takeaway_large;
                                break;
                            case 11: // Delivery Service
                                regularPrice = item.delivery_service_regular;
                                mediumPrice = item.delivery_service_medium;
                                largePrice = item.delivery_service_large;
                                break;
                            default:
                                regularPrice = item.regular_price;
                                mediumPrice = item.medium_price;
                                largePrice = item.large_price;
                        }

                        // Check if the price exists for each portion size
                        const hasRegularPrice = regularPrice !== null && regularPrice !== undefined && regularPrice > 0;
                        const hasMediumPrice = mediumPrice !== null && mediumPrice !== undefined && mediumPrice > 0;
                        const hasLargePrice = largePrice !== null && largePrice !== undefined && largePrice > 0;

                        // Create a unique ID for this menu item's portion selector
                        const portionSelectId = `portion-${item.item_id}`;

                        // Check if any portion size is available
                        const isAvailable = hasRegularPrice || hasMediumPrice || hasLargePrice;

                        let portionOptionsHTML = '';

                        if (isAvailable) {
                            portionOptionsHTML = `
                        <div class="portion-container">
                            <select id="${portionSelectId}" class="portion-select" data-item-id="${item.item_id}">
                                ${hasRegularPrice ? `<option value="regular" data-price="${regularPrice}">Family - LKR ${regularPrice}</option>` : ''}
                                ${hasMediumPrice ? `<option value="medium" data-price="${mediumPrice}">Medium - LKR ${mediumPrice}</option>` : ''}
                                ${hasLargePrice ? `<option value="large" data-price="${largePrice}">Large - LKR ${largePrice}</option>` : ''}
                            </select>
                            <button onclick="addToCartWithPortion('${item.item_id}', '${item.item_name}', '${item.item_category}', '${portionSelectId}')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cart-icon">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                Add to Cart
                            </button>
                        </div>
                    `;
                        } else {
                            portionOptionsHTML = '<p class="not-available">Not available for this service type</p>';
                        }

                        menuItem.innerHTML = `
                    <h4 class="item-name">${item.item_name}</h4>
                    <p class="item-description">${item.item_description ? item.item_description.substr(0,75) + "..." : 'No description available'}</p>
                    <div class="item-details">
                        <p>Type: <span>${item.item_type}</span></p>
                        <p>Category: <span>${item.item_category}</span></p>
                    </div>
                    ${portionOptionsHTML}
                `;
                
                menuContainer.appendChild(menuItem);
            });
        });
}

function loadPersistedCart() {
    const storedCart = localStorage.getItem('restaurant_cart');
    if (storedCart) {
        cart = JSON.parse(storedCart);
        updateCart();
        console.log("cart found " + cart.length);
        
    }
}

function savePersistedCart() {
    localStorage.setItem('restaurant_cart', JSON.stringify(cart));
}

window.addEventListener('load', loadPersistedCart);

// Updated version of the addToCartWithPortion function with icon
function addToCartWithPortion(itemId, itemName, itemCategory, portionSelectId) {
    const portionSelect = document.getElementById(portionSelectId);
    
    // If no portion is available or selected, don't add to cart
    if (!portionSelect || portionSelect.options.length === 0) {
        alert("No portion available for this item.");
        return;
    }
    
    const portionSize = portionSelect.value;
    const selectedOption = portionSelect.options[portionSelect.selectedIndex];
    const price = parseFloat(selectedOption.getAttribute('data-price'));
    
    // Call the existing addToCart function with the portion info
    savePersistedCart()
    addToCart(itemId, itemName, price, itemCategory, portionSize);
}

        // Updated addToCart function to include portion size
        function addToCart(itemId, itemName, price, itemCategory, portionSize) {
            const qtyValue = parseInt(document.getElementById('quantity-input').value) || 1
            // Create a unique identifier combining itemId and portionSize
            const cartItemId = `${itemId}-${portionSize}`;

            let displayName = itemName;

            if (portionSize !== '') {
                displayName = `${itemName} (${portionSize.charAt(0).toUpperCase() + portionSize.slice(1)})`;
            } else {
                displayName = itemName;
            }

    
    const existingItem = cart.find(item => item.uniqueId === cartItemId);
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({
            id: itemId,
            uniqueId: cartItemId,
            name: displayName, // Include portion size in the name
            price: price,
            quantity: qtyValue,
            fc: 0,
            itemCategory,
            portionSize
        });
    }
    savePersistedCart()
    updateCart();
}



        const dishRemarksDisplayer = (id) => {
            const targetInput = document.getElementById(id);
            if (!targetInput) {
                console.error(`Element with ID "${id}" not found`);
                return;
            }
            if (targetInput.style.visibility == "hidden") {
                targetInput.style.visibility = "visible"
            } else {
                targetInput.style.visibility = "hidden"
            }
        }

        function updateCart() {
            const storedCart = localStorage.getItem('restaurant_cart');
            if (storedCart) {
                cart = JSON.parse(storedCart);
            } else {
                cart = [];
            }

            const cartItemsBody = document.getElementById('cart-items');
            cartItemsBody.innerHTML = '';
            let totalPrice = 0;

            cart.forEach((item, index) => {
                const total = item.price * item.quantity;
                totalPrice += total;

                // Modify the display name to replace "Regular" with "Family"
                let displayName = item.name;
                if (displayName.includes('(Regular)')) {
                    displayName = displayName.replace('(Regular)', '(Family)');
                } else if (displayName.includes('(regular)')) {
                    displayName = displayName.replace('(regular)', '(Family)');
                }

                const row = document.createElement('tr');
                const rowId = `${item.name}_${item.price}`
                row.dataset.itemCategory = item.itemCategory
                row.innerHTML = `
                    <td>
                        <div class="dish-info">
                            <div class="remark-toggler" onclick="dishRemarksDisplayer('dish_remark_${rowId}')">
                               <i class="fa-solid fa-utensils"></i>
                            </div>
                            <span>${item.name}</span>
                            <input type="text" placeholder="Remarks" class="kot_remarks" id="dish_remark_${rowId}"/>
                        </div>
                    </td>
                    <td>${item.price.toFixed(2)}</td>
                    <td>
                        <div class="cart-buttons">
                            <button class="quantity-btn" onclick="changeQuantity(${index}, -1)">-</button>
                            ${item.quantity}
                            <button class="quantity-btn" onclick="changeQuantity(${index}, 1)">+</button>
                        </div>
                    </td>
                    <td>
                        <div class="cart-buttons">
                            <button class="quantity-btn" onclick="changeFc(${index}, -1)">-</button>
                            ${item.fc}
                            <button class="quantity-btn" onclick="changeFc(${index}, 1)">+</button>
                        </div>
                    </td>
                    <td>
                        <input type="number" class="dish_discount" id="dish_discount_${rowId}" value="${item.discount || 0}" min="0" onchange="applyDiscount(${index}, this.value)"/>
                    </td>
                    <td id="item_total_${index}">${total.toFixed(2)}</td>
                    <td>
                        <div class="cart-buttons">
                            <button class="remove-btn" onclick="removeFromCart(${index})">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                cartItemsBody.appendChild(row);
            });
            updateTotalBillDiscount()
            document.getElementById('total-price').textContent = totalPrice.toFixed(2);
            let serviceCharge = 0

            const totalDiscount = parseFloat(document.getElementById('discount-input').value)

            // if(parseInt(document.getElementById('hotel-type').value) == 1){
            //     serviceCharge = ((totalPrice - totalDiscount) * 10) / 100
            // }else{
            //     serviceCharge = 0
            // }

            document.getElementById("service_charge").textContent = serviceCharge.toFixed(2);
            document.getElementById("checkout-sub-total").textContent = ((totalPrice - totalDiscount) + serviceCharge).toFixed(2);
        }

        function changeQuantity(index, change) {
            cart[index].quantity = Math.max(0, cart[index].quantity + change);
            savePersistedCart()
            updateCart();
        }

        function changeFc(index, change) {
            cart[index].fc = Math.max(0, cart[index].fc + change);
            savePersistedCart()
            updateCart();
        }

        function removeFromCart(index) {
            // Get the item to be removed
            const item = cart[index];

            // Create the modal HTML
            const modalHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h2><i class="fa-solid fa-trash"></i> Remove Item</h2>
                        <span class="close">&times;</span>
                    </div>
                    <div class="modal-body">
                        <p>You are about to remove the following item:</p>
                        <p><strong>Item:</strong> ${item.name}</p>
                        <p><strong>Price:</strong> Rs. ${item.price.toFixed(2)}</p>
                        <p><strong>Quantity:</strong> ${item.quantity}</p>
                        <p><strong>Total:</strong> Rs. ${(item.price * item.quantity).toFixed(2)}</p>
                        
                        <div class="form-group">
                            <label for="remove-reason"><strong>Reason for Removal:</strong></label>
                            <textarea id="remove-reason" rows="3" placeholder="Please provide a reason for removing this item" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="confirm-remove-item" class="btn-danger"><i class="fa-solid fa-check"></i> Confirm Removal</button>
                        <button id="cancel-remove-item" class="btn-secondary"><i class="fa-solid fa-times"></i> Cancel</button>
                    </div>
                </div>
            `;

            // Create and show modal
            const modalContainer = document.createElement('div');
            modalContainer.className = 'modal';
            modalContainer.id = 'remove-item-modal';
            modalContainer.innerHTML = modalHTML;
            document.body.appendChild(modalContainer);

            // Show the modal
            modalContainer.style.display = 'block';

            // Add event listeners
            const closeBtn = modalContainer.querySelector('.close');
            const cancelBtn = document.getElementById('cancel-remove-item');
            const confirmBtn = document.getElementById('confirm-remove-item');

            closeBtn.addEventListener('click', () => {
                document.body.removeChild(modalContainer);
            });

            cancelBtn.addEventListener('click', () => {
                document.body.removeChild(modalContainer);
            });

            confirmBtn.addEventListener('click', () => {
                const reason = document.getElementById('remove-reason').value.trim();

                if (!reason) {
                    // Show error if no reason provided
                    const reasonTextarea = document.getElementById('remove-reason');
                    reasonTextarea.classList.add('error');
                    reasonTextarea.placeholder = 'Reason is required';
                    return;
                }

                // Process the item removal
                processRemoveItem(index, item, reason);
                document.body.removeChild(modalContainer);
            });
        }

        // Function to process item removal
        function processRemoveItem(index, item, reason) {
            // Create data for removed item
            const removedItemData = {
                bill_id: currentBillId,
                item_id: item.id,
                item_name: item.name,
                unit_price: item.price,
                quantity: item.quantity,
                total_price: item.price * item.quantity,
                reason: reason
            };

            // Send request to server to save removed item details
            fetch('save_removed_item.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(removedItemData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove item from cart
                        cart.splice(index, 1);
                        savePersistedCart()
                        updateCart();

                        // Show success message
                        showAlert({
                            type: 'success',
                            title: 'Success',
                            message: 'Item removed successfully!',
                            autoClose: true,
                            autoCloseTime: 2000
                        });
                    } else {
                        // Show error message but still remove from cart
                        cart.splice(index, 1);
                        savePersistedCart()
                        updateCart();

                        showAlert({
                            type: 'warning',
                            title: 'Warning',
                            message: 'Item removed from cart, but there was an error saving the removal record.',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error saving removed item:', error);

                    // Still remove the item from cart even if there's an error
                    cart.splice(index, 1);
                    savePersistedCart()
                    updateCart();

                    showAlert({
                        type: 'warning',
                        title: 'Warning',
                        message: 'Item removed from cart, but there was an error saving the removal record.',
                        confirmButtonText: 'OK'
                    });
                });
        }

        // Event Listeners
        document.getElementById('hotel-type').addEventListener('change', function() {
            const selectedHotelType = document.getElementById('hotel-type').value;
            document.getElementById('hotel-selector').value = document.getElementById('hotel-type').value;
            const selectedCategory = document.getElementById('food-category-selector').value;
            if (selectedHotelType) {
                //populateTables(this.options[this.selectedIndex].value);
                populateMenu(selectedHotelType, selectedCategory);
            }
            updateCart()
        });

        document.getElementById('hotel-selector').addEventListener('change', function() {
            document.getElementById('hotel-type').value = document.getElementById('hotel-selector').value;
            foodFilterRecursive();
        });

        document.getElementById('food-category-selector').addEventListener('change', function() {
            const selectedHotelType = document.getElementById('hotel-type').value;
            const selectedCategory = document.getElementById('food-category-selector').value;
            if (selectedHotelType) {
                //populateTables(this.options[this.selectedIndex].value);
                populateMenu(selectedHotelType, selectedCategory);
            }
        });

        // Initialize
        populateHotelTypes();

        // Updated JavaScript for handling portion sizes
        document.addEventListener('DOMContentLoaded', () => {
            const itemIdInput = document.getElementById('item-id-input');
            const itemNameInput = document.getElementById('item-name-input');
            const quantityInput = document.getElementById('quantity-input');
            const priceInput = document.getElementById('price-input');
            const addManualItemBtn = document.getElementById('add-manual-item');
            const hotelTypeSelect = document.getElementById('hotel-type');

            // Create a container for the portion selector (to be added dynamically)
            const itemIdRow = document.querySelector('.item-id-row');
            const portionSelectorContainer = document.createElement('div');
            portionSelectorContainer.id = 'portion-selector-container';
            portionSelectorContainer.style.display = 'none';
            itemIdRow.insertBefore(portionSelectorContainer, quantityInput);

            let currentItemData = null; // Will store the complete item data with portion info

            // Modify the item ID input's Enter key handler
            itemIdInput.addEventListener('keypress', async (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();

                    if (hotelTypeSelect.value == 0 || hotelTypeSelect.value == '') {
                        showAlert({
                            type: 'warning',
                            title: 'Missing Selection',
                            message: 'Please select a hotel type first.',
                            confirmButtonText: 'OK'
                        });
                                setTimeout(() => {
                                        const handleEnter = function(event) {
                                            if (event.key === 'Enter') {
                                                event.preventDefault();
                                                
                                                // Look for visible buttons with OK text
                                                const okButton = Array.from(document.querySelectorAll('button')).find(btn => {
                                                    const isVisible = btn.offsetParent !== null;
                                                    const hasOkText = btn.textContent.trim().toUpperCase().includes('OK');
                                                    return isVisible && hasOkText;
                                                });
                                                
                                                if (okButton) {
                                                    okButton.click();
                                                }
                                                
                                                document.removeEventListener('keydown', handleEnter);
                                            }
                                        };
                                        document.addEventListener('keydown', handleEnter);
                                }, 100);

                        clearAllItemFields();
                        return;
                    }

                    try {
                        // Fetch item details
                        currentItemData = await fetchItemDetails(itemIdInput.value);

                        // For debugging - log the item data
                        console.log("Item data received:", currentItemData);

                        // If valid details, check for portion sizes
                        if (currentItemData && currentItemData.item_name) {
                            // Explicitly check that has_portions is true (not just truthy)
                            if (currentItemData.has_portions === true) {
                                console.log("Item has portions, showing selector");
                                createPortionSelector(currentItemData);
                                portionSelectorContainer.style.display = 'block';

                                // Focus on the portion selector
                                const portionSelect = document.getElementById('portion-select');
                                if (portionSelect) {
                                    portionSelect.focus();
                                }
                            } else {
                                // No portions, hide the selector
                                console.log("Item doesn't have portions, hiding selector");
                                portionSelectorContainer.style.display = 'none';

                                // Set the price from the item data - make sure it's set correctly
                                if (currentItemData.display_price>0) {
                                    priceInput.value = currentItemData.display_price;
                                    console.log("Setting price to:", currentItemData.display_price);
                                } else {
                                    showAlert({
                                        type: 'warning',
                                        title: 'Prices Not Found',
                                        message: 'No Prices found for the selected hotel type',
                                        confirmButtonText: 'OK'
                                    });                                   

                                    // Add Enter key listener to close alert
                                    setTimeout(() => {
                                        const handleEnter = function(event) {
                                            if (event.key === 'Enter') {
                                                event.preventDefault();
                                                
                                                // Look for visible buttons with OK text
                                                const okButton = Array.from(document.querySelectorAll('button')).find(btn => {
                                                    const isVisible = btn.offsetParent !== null;
                                                    const hasOkText = btn.textContent.trim().toUpperCase().includes('OK');
                                                    return isVisible && hasOkText;
                                                });
                                                
                                                if (okButton) {
                                                    okButton.click();
                                                }
                                                
                                                document.removeEventListener('keydown', handleEnter);
                                            }
                                        };
                                        document.addEventListener('keydown', handleEnter);
                                    }, 100);
                                    reject('Prices not found');
                                    return;
                                    
                                    // console.warn("No display_price found in item data");
                                    // priceInput.value = "0.00";
                                }

                                // Move focus to quantity
                                quantityInput.focus();
                                quantityInput.select();
                            }
                        }
                    } catch (error) {
                        console.error('Error processing item:', error);
                        clearAllItemFields();
                        itemIdInput.focus();
                    }
                }
            });

            // Function to create portion selector dropdown
            function createPortionSelector(itemData) {
                portionSelectorContainer.innerHTML = '';

                const select = document.createElement('select');
                select.id = 'portion-select';
                select.className = 'portion-select';

                // Add available portion options
                if (itemData.regular_price > 0) {
                    const option = document.createElement('option');
                    option.value = 'regular';
                    option.dataset.price = itemData.regular_price;
                    option.textContent = `Family - LKR ${itemData.regular_price}`;
                    select.appendChild(option);
                }

                if (itemData.medium_price > 0) {
                    const option = document.createElement('option');
                    option.value = 'medium';
                    option.dataset.price = itemData.medium_price;
                    option.textContent = `Medium - LKR ${itemData.medium_price}`;
                    select.appendChild(option);
                }

                if (itemData.large_price > 0) {
                    const option = document.createElement('option');
                    option.value = 'large';
                    option.dataset.price = itemData.large_price;
                    option.textContent = `Large - LKR ${itemData.large_price}`;
                    select.appendChild(option);
                }

                // Update price when portion changes
                select.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption && selectedOption.dataset.price) {
                        priceInput.value = selectedOption.dataset.price;
                    }
                });

                // Add keypress event for Enter key
                select.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        // Move to quantity input on Enter
                        quantityInput.focus();
                        quantityInput.select();
                    }
                });

                // Support arrow up/down navigation within dropdown
                select.addEventListener('keydown', function(e) {
                    if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                        // Let the default browser behavior handle the arrow keys
                        // This will move between options in the dropdown
                        // We don't need to do anything extra here

                        // After a brief delay to let the option change, update the price
                        setTimeout(() => {
                            const selectedOption = this.options[this.selectedIndex];
                            if (selectedOption && selectedOption.dataset.price) {
                                priceInput.value = selectedOption.dataset.price;
                            }
                        }, 50);
                    }
                });

                portionSelectorContainer.appendChild(select);

                // Set initial price to first option
                if (select.options.length > 0) {
                    priceInput.value = select.options[0].dataset.price;
                }
            }

            // Add Enter key handler for quantity input
            quantityInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    // Trigger the add button click
                    document.getElementById('add-manual-item').click();
                }
            });

            // Update Add button handler to include portion size and item category
            addManualItemBtn.addEventListener('click', () => {
                try {
                    // Check if price and item name are valid
                    if (!itemNameInput.value || !priceInput.value) {
                        console.warn("Missing required fields for adding to cart");
                        return;
                    }

                    const portionSelect = document.getElementById('portion-select');
                    let itemName = itemNameInput.value;
                    let portionSize = '';
                    let uniqueId = itemIdInput.value;
                    let itemCategory = currentItemData && currentItemData.item_category ? currentItemData.item_category : '';

                    // If portion selector exists and is visible, get portion size
                    if (portionSelect && portionSelectorContainer.style.display !== 'none') {
                        portionSize = portionSelect.value;
                        // Add portion size to name if available
                        if (portionSize) {
                            itemName = `${itemName} (${portionSize.charAt(0).toUpperCase() + portionSize.slice(1)})`;
                            uniqueId = `${itemIdInput.value}-${portionSize}`;
                        }
                    }
                    addToCart(
                        itemIdInput.value,
                        itemName,
                        parseFloat(priceInput.value),
                        parseInt(quantityInput.value) || 1,
                        uniqueId,
                        portionSize,
                        itemCategory
                    );
                    clearAllItemFields();
                    itemIdInput.focus();
                } catch (error) {
                    console.error('Error adding item:', error);
                    clearAllItemFields();
                    itemIdInput.focus();
                }
            });

            // New helper function to clear fields
            function clearAllItemFields() {
                itemIdInput.value = '';
                itemNameInput.value = '';
                priceInput.value = '';
                quantityInput.value = '1';
                portionSelectorContainer.style.display = 'none';
                portionSelectorContainer.innerHTML = '';
                currentItemData = null;
            }

            // Modified fetch function to return Promise with portion data
            function fetchItemDetails(itemId) {
                return new Promise((resolve, reject) => {
                    const hotelType = hotelTypeSelect.value;

                    fetch(`fetch_item_details.php?item_id=${itemId}&hotel_type=${hotelType}`)
                        .then(response => response.json())
                        .then(item => {
                            if (item) {
                                console.log("Raw response from backend:", item);
                                itemNameInput.value = item.item_name;

                                // Only set price directly if no portions
                                if (item.has_portions !== true) {
                                    priceInput.value = item.display_price;

                                }

                                resolve(item);
                            } else {
                                showAlert({
                                    type: 'warning',
                                    title: 'Item Not Found',
                                    message: 'No item found with that ID',
                                    confirmButtonText: 'OK'
                                });
                                setTimeout(() => {
                                        const handleEnter = function(event) {
                                            if (event.key === 'Enter') {
                                                event.preventDefault();
                                                
                                                // Look for visible buttons with OK text
                                                const okButton = Array.from(document.querySelectorAll('button')).find(btn => {
                                                    const isVisible = btn.offsetParent !== null;
                                                    const hasOkText = btn.textContent.trim().toUpperCase().includes('OK');
                                                    return isVisible && hasOkText;
                                                });
                                                
                                                if (okButton) {
                                                    okButton.click();
                                                }
                                                
                                                document.removeEventListener('keydown', handleEnter);
                                            }
                                        };
                                        document.addEventListener('keydown', handleEnter);
                                }, 100);
                                reject('Item not found');
                                return;
                                
                            }
                        })
                        .catch(error => {
                            showAlert({
                                type: 'error',
                                title: 'Connection Error',
                                message: 'Failed to fetch item details',
                                confirmButtonText: 'OK'
                            });
                                setTimeout(() => {
                                        const handleEnter = function(event) {
                                            if (event.key === 'Enter') {
                                                event.preventDefault();
                                                
                                                // Look for visible buttons with OK text
                                                const okButton = Array.from(document.querySelectorAll('button')).find(btn => {
                                                    const isVisible = btn.offsetParent !== null;
                                                    const hasOkText = btn.textContent.trim().toUpperCase().includes('OK');
                                                    return isVisible && hasOkText;
                                                });
                                                
                                                if (okButton) {
                                                    okButton.click();
                                                }
                                                
                                                document.removeEventListener('keydown', handleEnter);
                                            }
                                        };
                                        document.addEventListener('keydown', handleEnter);
                                }, 100);
                                reject(error);
                                return;
                            
                        });
                });
            }

            // Modified addToCart function to accept uniqueId, portionSize, and itemCategory
            // function addToCart(itemId, itemName, price, quantity = 1, uniqueId = itemId, portionSize = '', itemCategory = '') {
            //     console.log("this worked");
                
            //     const existingItem = cart.find(item => item.uniqueId === uniqueId);
            //     if (existingItem) {
            //         existingItem.quantity += quantity;
            //     } else {
            //         cart.push({
            //             id: itemId,
            //             uniqueId: uniqueId,
            //             name: itemName,
            //             price,
            //             quantity,
            //             portionSize,
            //             itemCategory
            //         });
            //     }
            //     updateCart();
            // }

            document.getElementById('kot-print-btn').addEventListener('click', () => {
                let shop_bucket = collectCartData()
                navigateToPrintKOT(shop_bucket)
            })

            // Add this new function to calculate totals separately
            function calculateTotals() {
                let totalPrice = 0;
                cart.forEach(item => {
                    totalPrice += item.price * item.quantity;
                });
                const discount = parseFloat(document.getElementById('discount-input').value) || 0;
                const totalAfterDiscount = totalPrice - discount;

                document.getElementById('total-price').textContent = totalPrice.toFixed(2);
                document.getElementById('total-after-discount').textContent = totalAfterDiscount.toFixed(2);
            }



            document.getElementById('discount-input').addEventListener('input', calculateTotals);


            if (hotelTypeSelect) {
                hotelTypeSelect.addEventListener('change', function() {
                    handleHotelTypeChange(this.value);
                });
            }

        });

        // food category selector related functions
        let food_categories = []; // Will be populated from the database

        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', () => {
            const foodCategorySelector = document.getElementById('food-category-selector');
            foodCategorySelector.style.textTransform = 'capitalize';

            // Show loading indicator in dropdown while fetching
            const loadingOption = document.createElement('option');
            loadingOption.innerText = 'Loading categories...';
            foodCategorySelector.appendChild(loadingOption);

            // Fetch the categories from the database
            fetch('fetch_menu_types.php')
                .then(response => response.json())
                .then(data => {
                    // Assign the fetched categories to food_categories
                    food_categories = data;

                    // Clear the loading option
                    foodCategorySelector.innerHTML = '';

                    // Add "Select category" as first option
                    const defaultOption = document.createElement('option');
                    defaultOption.setAttribute('value', '');
                    defaultOption.innerText = 'Select category';
                    foodCategorySelector.appendChild(defaultOption);

                    // Add all categories from the database
                    food_categories.forEach((category) => {
                        const optionTag = document.createElement('option');
                        optionTag.setAttribute('value', category);
                        optionTag.innerText = category.replace('_', ' ');
                        foodCategorySelector.appendChild(optionTag);
                    });

                    // Add the "Bakery & Beverages" option manually if it's not in the database
                    if (!food_categories.includes('bakery_and_beverages')) {
                        const optionTag = document.createElement('option');
                        optionTag.setAttribute('value', 'bakery_and_beverages');
                        optionTag.innerText = "Bakery & Beverages";
                        foodCategorySelector.appendChild(optionTag);
                    }
                })
                .catch(error => {
                    console.error('Error fetching food categories:', error);

                    // Fallback to hard-coded values if database fetch fails
                    food_categories = [
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
                        "Soup",
                        "Salad",
                        "Grilled",
                        "Pasta",
                        "Fried Rice and Noodles",
                        "SeaFood",
                        "Mutton",
                        "Lamb",
                        "Omelette",
                        "Dessert",
                        "Iced Beverage",
                        "Shake",
                        "Fresh Juice",
                        "Mojitho",
                        "Mocktails"
                    ];

                    // Clear loading message
                    foodCategorySelector.innerHTML = '';

                    // Add "Select category" as first option
                    const defaultOption = document.createElement('option');
                    defaultOption.setAttribute('value', '');
                    defaultOption.innerText = 'Select category';
                    foodCategorySelector.appendChild(defaultOption);

                    // Populate with fallback values
                    food_categories.forEach((category) => {
                        const optionTag = document.createElement('option');
                        optionTag.setAttribute('value', category);
                        optionTag.innerText = category.replace('_', ' ');
                        foodCategorySelector.appendChild(optionTag);
                    });

                    // Add Bakery & Beverages
                    const optionTag = document.createElement('option');
                    optionTag.setAttribute('value', 'bakery_and_beverages');
                    optionTag.innerText = "Bakery & Beverages";
                    foodCategorySelector.appendChild(optionTag);
                });
        });


        // Payments array for checkout
        const payments = [];
        let currentBillId = null;
        let isHeldBill = false;

        // Fetch next bill ID on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Existing code...

            // Fetch the next bill ID
            //fetchNextBillId();

            // Add event listeners for new buttons
            document.querySelector('.footer-opt-btn:nth-child(1)').addEventListener('click', openCheckoutModal);
            document.querySelector('.footer-opt-btn:nth-child(4)').addEventListener('click', toggleHeldBillsDropdown);
            document.querySelector('.footer-opt-btn:nth-child(5)').addEventListener('click', createNewBill);

            function isCheckoutModalVisible() {
                const checkoutModal = document.getElementById('checkout-modal');
                return checkoutModal && checkoutModal.style.display === 'block';
            }

            document.addEventListener('keydown',(e)=>{
                if(e.key === '+'){
                    if (isCheckoutModalVisible()) {
                        document.getElementById('complete-bill-btn').click();
                    } else {
                        openCheckoutModal();
                        document.getElementById('payment-amount').focus();
                        document.getElementById('payment-amount').value = ''
                    }
                }
            })

            document.addEventListener('keydown', (e) => {
                if(document.activeElement === document.getElementById('payment-amount') && e.key === 'Enter'){
                    document.getElementById('add-payment-btn').click()
                }
            })

            document.addEventListener('keydown',(e)=>{
                if(e.key === '-'){
                    let shop_bucket = collectCartData()
                    navigateToPrintKOT(shop_bucket)
                }
            })

            let dropdownFocused = false;
            document.addEventListener('keydown',(e)=>{
                if(e.key === 'PageUp'){
                    e.preventDefault();
                    const hotelSelectorDropdown = document.getElementById('hotel-type');
                    
                    if (!dropdownFocused || document.activeElement !== hotelSelectorDropdown) {
                        hotelSelectorDropdown.focus();
                        dropdownFocused = true;
                        return;
                    }

                     if (hotelSelectorDropdown.selectedIndex > 0) {
                        hotelSelectorDropdown.selectedIndex -= 1;
                    } else {
                        hotelSelectorDropdown.selectedIndex = hotelSelectorDropdown.options.length - 1;
                    }
                    hotelSelectorDropdown.dispatchEvent(new Event('change'));
                }
            })

             document.addEventListener('keydown',(e)=>{
                if(e.key === 'PageDown'){
                    e.preventDefault()
                    const targetInput = document.getElementById('item-id-input')
                    targetInput.focus()
                }
            })

            document.addEventListener('keydown',(e)=>{
                if(e.key === "Enter"){
                    if(document.activeElement === document.getElementById('item-name-input')){
                        document.getElementById('quantity-input').focus()
                        return
                    }
                    if(document.activeElement === document.getElementById('quantity-input')){
                        document.getElementById('price-input').focus()
                        return
                    }
                    if(document.activeElement === document.getElementById('price-input')){
                        document.getElementById('add-manual-item').click()
                        return
                    }
                }
            })
            
            // Checkout modal event listeners
            document.querySelector('.close').addEventListener('click', closeCheckoutModal);
            document.getElementById('cancel-checkout-btn').addEventListener('click', closeCheckoutModal);
            document.getElementById('add-payment-btn').addEventListener('click', addPayment);
            document.getElementById('complete-bill-btn').addEventListener('click', completeBill);


            // Add event listener to close dropdown
            document.querySelector('.dropdown-close').addEventListener('click', function() {
                document.getElementById('held-bills-dropdown').classList.remove('show');
                document.querySelector('.footer-opt-btn:nth-child(4)').classList.remove('active-held');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const dropdown = document.getElementById('held-bills-dropdown');
                const heldBillBtn = document.querySelector('.footer-opt-btn:nth-child(4)');

                if (dropdown.classList.contains('show') &&
                    !dropdown.contains(event.target) &&
                    !heldBillBtn.contains(event.target)) {
                    dropdown.classList.remove('show');
                    heldBillBtn.classList.remove('active-held');
                }
            });
        });

        // Function to fetch the next bill ID
        function fetchNextBillId() {
            fetch('fetch_next_bill_id.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('next-bill-id').textContent = `#${data.next_bill_id}`;
                    currentBillId = data.next_bill_id;
                    isHeldBill = false;
                })
                .catch(error => {
                    console.error('Error fetching next bill ID:', error);
                    document.getElementById('next-bill-id').textContent = 'Error';
                });
        }

        function fetchHeldBills() {
            const heldBillsList = document.getElementById('held-bills-list');
            const emptyState = document.getElementById('empty-held-bills');

            // Show loading state
            heldBillsList.innerHTML = '<li class="loading"><div class="bill-info">Loading...</div></li>';
            emptyState.style.display = 'none';

            fetch('fetch_held_bills.php')
                .then(response => response.json())
                .then(data => {
                    heldBillsList.innerHTML = '';

                    if (data.length === 0) {
                        emptyState.style.display = 'block';
                        return;
                    }

                    data.forEach(bill => {
                        const li = document.createElement('li');

                        // Format date if available
                        const date = bill.bill_time ? new Date(bill.bill_time) : new Date();
                        const formattedDate = `${date.toLocaleDateString()} ${date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}`;

                        li.innerHTML = `
                            <div class="bill-info">
                                <span class="bill-id">Bill #${bill.bill_id}</span>
                                <span class="bill-details">Table: ${bill.table_id || 'N/A'} | ${formattedDate}</span>
                            </div>
                            <span class="bill-amount">LKR ${parseFloat(bill.payment_amount).toFixed(2)}</span>
                        `;

                        li.addEventListener('click', () => loadHeldBill(bill.bill_id));
                        heldBillsList.appendChild(li);
                    });
                })
                .catch(error => {
                    console.error('Error fetching held bills:', error);
                    heldBillsList.innerHTML = '';
                    emptyState.innerHTML = `
                        <i class="fa-solid fa-exclamation-circle"></i>
                        <p>Error loading held bills</p>
                    `;
                    emptyState.style.display = 'block';
                });
        }

        // open checkout modal
        function openCheckoutModal() {
            const selectedHotelType = document.getElementById('hotel-selector').value;
            const selectedTable = document.getElementById('table').value;
            if (cart.length === 0) {
                showAlert({
                    type: 'warning',
                    title: 'Empty Cart',
                    message: 'Cannot open Checkout with an empty cart.',
                    confirmButtonText: 'OK'
                });
                
                // Add Enter key listener to close alert
                setTimeout(() => {
                    const handleEnter = function(event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            
                            // Look for visible buttons with OK text
                            const okButton = Array.from(document.querySelectorAll('button')).find(btn => {
                                const isVisible = btn.offsetParent !== null;
                                const hasOkText = btn.textContent.trim().toUpperCase().includes('OK');
                                return isVisible && hasOkText;
                            });
                            
                            if (okButton) {
                                okButton.click();
                            }
                            
                            document.removeEventListener('keydown', handleEnter);
                        }
                    };
                    document.addEventListener('keydown', handleEnter);
                }, 100);
                return; // Exit the function early
            }
            else if (selectedHotelType == "0") {
                showAlert({
                    type: 'warning',
                    title: 'Select Hotel Type',
                    message: 'Please select a hotel type!',
                    confirmButtonText: 'OK'
                });
                
                // Add Enter key listener to close alert
                setTimeout(() => {
                    const handleEnter = function(event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            
                            // Look for visible buttons with OK text
                            const okButton = Array.from(document.querySelectorAll('button')).find(btn => {
                                const isVisible = btn.offsetParent !== null;
                                const hasOkText = btn.textContent.trim().toUpperCase().includes('OK');
                                return isVisible && hasOkText;
                            });
                            
                            if (okButton) {
                                okButton.click();
                            }
                            
                            document.removeEventListener('keydown', handleEnter);
                        }
                    };
                    document.addEventListener('keydown', handleEnter);
                }, 100);
                return; // Exit the function early
            }
            else if (selectedHotelType == "1" && selectedTable == "") {
                showAlert({
                    type: 'warning',
                    title: 'Select Table',
                    message: 'Please select a table!',
                    confirmButtonText: 'OK'
                });
                
                // Add Enter key listener to close alert
                setTimeout(() => {
                    const handleEnter = function(event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            
                            // Look for visible buttons with OK text
                            const okButton = Array.from(document.querySelectorAll('button')).find(btn => {
                                const isVisible = btn.offsetParent !== null;
                                const hasOkText = btn.textContent.trim().toUpperCase().includes('OK');
                                return isVisible && hasOkText;
                            });
                            
                            if (okButton) {
                                okButton.click();
                            }
                            
                            document.removeEventListener('keydown', handleEnter);
                        }
                    };
                    document.addEventListener('keydown', handleEnter);
                }, 100);
                return; // Exit the function early
            }

            const checkoutItemsBody = document.getElementById('checkout-items-body');
            checkoutItemsBody.innerHTML = '';
            let totalBeforeDiscount = 0;
            if (cart.length === 0) {
                checkoutItemsBody.innerHTML = `
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px;">
                        <i class="fa-solid fa-shopping-basket" style="font-size: 24px; color: #ccc; display: block; margin-bottom: 10px;"></i>
                        Your cart is empty
                    </td>
                </tr>
            `;
            } else {
                cart.forEach(item => {
                    const total = item.price * item.quantity;
                    totalBeforeDiscount += total;
                    // Modify the display name for checkout as well
                    let displayName = item.name;
                    if (displayName.includes('(Regular)')) {
                        displayName = displayName.replace('(Regular)', '(Family)');
                    } else if (displayName.includes('(regular)')) {
                        displayName = displayName.replace('(regular)', '(Family)');
                    }
                    const row = document.createElement('tr');
                    row.innerHTML = `
                    <td>${displayName}</td>
                    <td>${item.price.toFixed(2)}</td>
                    <td>${item.quantity}</td>
                    <td>${item.fc}</td>
                    <td>${total.toFixed(2)}</td>
                `;
                    checkoutItemsBody.appendChild(row);
                });
            }
            const discount = parseFloat(document.getElementById('discount-input').value) || 0;
            const totalAfterDiscount = totalBeforeDiscount - discount;
            const advPaymentDeduction = isAdvPaymentChecked()
            const advPaymentAmount = parseFloat(document.getElementById('adv-payment-amount').textContent).toFixed(2)
            if (advPaymentDeduction) {
                document.getElementById('total-before-discount').textContent = (totalBeforeDiscount - advPaymentAmount).toFixed(2);
                document.getElementById('discount-amount').textContent = discount.toFixed(2);
                document.getElementById('checkout-total-amount').textContent = (totalAfterDiscount - advPaymentAmount).toFixed(2);
            } else {
                document.getElementById('total-before-discount').textContent = totalBeforeDiscount.toFixed(2);
                document.getElementById('discount-amount').textContent = discount.toFixed(2);
                document.getElementById('checkout-total-amount').textContent = totalAfterDiscount.toFixed(2);
            }
            updatePaymentSummary();
            document.getElementById('checkout-modal').style.display = 'block';
            checkoutWindowState = true
        }
        
        // Close checkout modal
        function closeCheckoutModal() {
            document.getElementById('checkout-modal').style.display = 'none';
            checkoutWindowState = false
        }
        document.getElementById('checkout-modal-close').addEventListener('click', function() {
            document.getElementById('checkout-modal').style.display = 'none';
        });

        function addPayment() {
            const method = document.getElementById('payment-method').value;
            const cardId = document.getElementById('card-id').value || 'N/A';
            const amountInput = document.getElementById('payment-amount');
            const amount = parseFloat(amountInput.value);

            if (isNaN(amount) || amount <= 0) {
                // Apply error styling
                amountInput.style.borderColor = '#e74c3c';
                amountInput.style.backgroundColor = '#ffebee';
                setTimeout(() => {
                    amountInput.style.borderColor = '';
                    amountInput.style.backgroundColor = '';
                }, 2000);
                return;
            }

            // For card payments, validate card ID
            if ((method === 'credit' || method === 'debit') && cardId === 'N/A') {
                const cardIdInput = document.getElementById('card-id');
                cardIdInput.style.borderColor = '#e74c3c';
                cardIdInput.style.backgroundColor = '#ffebee';
                setTimeout(() => {
                    cardIdInput.style.borderColor = '';
                    cardIdInput.style.backgroundColor = '';
                }, 2000);
                return;
            }

            payments.push({
                method,
                cardId,
                amount
            });

            // Update payments table
            updatePaymentsTable();

            // Clear inputs
            document.getElementById('card-id').value = '';
            document.getElementById('payment-amount').value = '';
        }

        // Enhanced update payments table with styling
        function updatePaymentsTable() {
            const paymentsBody = document.getElementById('payments-body');
            paymentsBody.innerHTML = '';

            if (payments.length === 0) {
                paymentsBody.innerHTML = `
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 15px;">
                            No payments added
                        </td>
                    </tr>
                `;
            } else {
                payments.forEach((payment, index) => {
                    const row = document.createElement('tr');

                    // Create method badge
                    const methodBadge = `<span class="payment-method ${payment.method}">${payment.method.charAt(0).toUpperCase() + payment.method.slice(1)}</span>`;

                    row.innerHTML = `
                        <td>${methodBadge}</td>
                        <td>${payment.cardId}</td>
                        <td>${payment.amount.toFixed(2)}</td>
                        <td><button class="remove-btn" onclick="removePayment(${index})"><i class="fa-solid fa-trash"></i></button></td>
                    `;
                    paymentsBody.appendChild(row);
                });
            }

            updatePaymentSummary();
        }

        // Remove payment
        function removePayment(index) {
            payments.splice(index, 1);
            updatePaymentsTable();
        }

        function updatePaymentSummary() {
            const totalAmount = parseFloat(document.getElementById('checkout-sub-total').textContent);
            const totalPaid = payments.reduce((sum, payment) => sum + payment.amount, 0);
            const balance = totalAmount - totalPaid;

            document.getElementById('total-paid-amount').textContent = totalPaid.toFixed(2);

            const balanceElement = document.getElementById('balance-amount');
            balanceElement.textContent = Math.abs(balance).toFixed(2);

            // Add color indicator for balance
            balanceElement.className = ''; // Reset classes
            if (balance > 0) {
                balanceElement.classList.add('negative');
                balanceElement.textContent = "-" + balanceElement.textContent; // Add minus sign
            } else if (balance < 0) {
                balanceElement.classList.add('positive');
                balanceElement.textContent = "+" + balanceElement.textContent; // Add plus sign
            }

            // Enable/disable complete button based on payment
            const completeBtn = document.getElementById('complete-bill-btn');
            if (totalPaid < totalAmount && cart.length > 0) {
                completeBtn.style.opacity = '0.7';
                completeBtn.title = 'Payment is less than total amount';
            } else {
                completeBtn.style.opacity = '1';
                completeBtn.title = '';
            }
        }


        // Complete bill
        function completeBill() {
            const selectedHotelType = document.getElementById('hotel-selector').value;

            if (cart.length === 0) {
                showAlert({
                    type: 'warning',
                    title: 'Empty Cart',
                    message: 'Cannot complete bill with an empty cart.',
                    confirmButtonText: 'OK'
                });
                return; // Exit the function early
            }
            else if (selectedHotelType == "0") {
                showAlert({
                    type: 'warning',
                    title: 'Select Hotel Type',
                    message: 'Please select a hotel type!',
                    confirmButtonText: 'OK'
                });
                return; // Exit the function early
            }

            const deductAdvPayment = isAdvPaymentChecked()
            const totalAmount = parseFloat(document.getElementById('checkout-total-amount').textContent);
            const totalPaid = payments.reduce((sum, payment) => sum + payment.amount, 0);

            if (totalPaid < totalAmount) {
                showConfirm({
                    title: 'Payment Incomplete',
                    message: 'Payment is less than total amount. Do you want to proceed with the bill?',
                    confirmButtonText: 'Yes, Proceed',
                    cancelButtonText: 'Cancel',
                    onConfirm: () => processCompleteBill(totalAmount, totalPaid),
                    onCancel: () => {
                        // Do nothing, dialog will close
                    }
                });
            } else {
                processCompleteBill(totalAmount, totalPaid);
            }
        }

        // function processCompleteBill(totalAmount, totalPaid) {
        //     const totalBeforeDiscount = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
        //     const discount = parseFloat(document.getElementById('discount-input').value) || 0;
        //     const hotelTypeId = document.getElementById('hotel-type').value;
        //     const customerName = document.getElementById('customer-telephone').value;
        //     const customerId = document.getElementById('customer-id-holder').value || null;

        //     const billData = {
        //         bill_id: currentBillId,
        //         hotel_type: document.getElementById('hotel-type').value,
        //         table_id: document.getElementById('table').value,
        //         total_before_discount: totalBeforeDiscount, // Added for potential future use
        //         discount_amount: discount, // Added for potential future use
        //         payment_amount: totalAmount, // This is totalAfterDiscount
        //         paid_amount: totalPaid,
        //         balance_amount: totalPaid - totalAmount,
        //         status: 'completed',
        //         items: cart.map(item => ({
        //             item_id: item.id,
        //             quantity: item.quantity,
        //             portion_size: item.portionSize,
        //             name:item.name,
        //             price:item.price,
        //         })),
        //         payments: payments.map(payment => ({
        //             payment_method: payment.method,
        //             amount: payment.amount,
        //             card_id: payment.cardId
        //         })),
        //         customer_name: customerName, 
        //         customer_id: customerId 
        //     };


        //     // Check if hotel type is Uber (4) or Pick Me (6)
        //     if (hotelTypeId === '4' || hotelTypeId === '6') {
        //         const referenceInput = document.getElementById('reference-number');

        //         // Validate reference number
        //         if (!referenceInput || !referenceInput.value.trim()) {
        //             showAlert({
        //                 type: 'error',
        //                 title: 'Missing Reference',
        //                 message: 'Please enter a reference number.',
        //                 confirmButtonText: 'OK'
        //             });
        //             return; // Exit function if reference is missing
        //         }

        //         // Add reference number to bill data
        //         billData.reference_number = referenceInput.value.trim();
        //     }

        //     // Show loading message
        //     const loadingAlert = showAlert({
        //         type: 'info',
        //         title: 'Processing',
        //         message: 'Saving bill, please wait...',
        //         showCancelButton: false,
        //         confirmButtonText: 'OK',
        //         autoClose: false
        //     });

        //     fetch('save_bill.php', {
        //             method: 'POST',
        //             headers: {
        //                 'Content-Type': 'application/json'
        //             },
        //             body: JSON.stringify(billData)
        //         })
        //         .then(response => response.json())
        //         .then(data => {
        //             closeAlert(loadingAlert);
        //             const advPaymentHolder = document.getElementById('adv-payment-amount')
        //             const tableID = document.getElementById('table').value
        //             const date = new Date();
        //             const localDate = date.toLocaleDateString();
        //             const localTime = date.toLocaleTimeString();
        //             const newDate = `${localDate} ${localTime}`
        //             increaseBillCount()
        //             printPosBill(cart, currentBillId, tableID, newDate, totalPaid, totalAmount)
        //             const hasAdvancePayment = isAdvPaymentChecked()
        //             if (hasAdvancePayment) {
        //                 const cusId = document.getElementById('customer-id-holder').value
        //                 if (!cusId) {
        //                     return
        //                 } else {
        //                     deleteCustomerPayment(cusId)
        //                 }
        //             }
        //             advPaymentHolder.value = 0
        //             resetAdvancePaymentCheckbox()
        //             if (data.success) {
        //                 showAlert({
        //                     type: 'success',
        //                     title: 'Success',
        //                     message: 'Bill completed successfully!',
        //                     autoClose: true,
        //                     autoCloseTime: 3000,
        //                     onConfirm: () => {
        //                         cart.length = 0;
        //                         payments.length = 0;
        //                         updateCart();
        //                         closeCheckoutModal();
        //                         fetchNextBillId();
        //                     }
        //                 });
        //                 cart.length = 0;
        //                 payments.length = 0;
        //                 updateCart();
        //                 closeCheckoutModal();
        //                 fetchNextBillId();
        //                 populateInitialTables();
        //             } else {
        //                 showAlert({
        //                     type: 'error',
        //                     title: 'Error',
        //                     message: data.message || 'Failed to complete bill.',
        //                     confirmButtonText: 'Try Again'
        //                 });
        //             }
        //         })
        //         .catch(error => {
        //             closeAlert(loadingAlert);
        //             console.error('Error saving bill:', error);
        //             showAlert({
        //                 type: 'error',
        //                 title: 'Connection Error',
        //                 message: 'Unable to save bill. Please check your connection and try again.',
        //                 confirmButtonText: 'OK'
        //             });
        //         });
        // }

function processCompleteBill(totalAmount, totalPaid) {
    const totalBeforeDiscount = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
    const discount = parseFloat(document.getElementById('discount-input').value) || 0;
    const hotelTypeId = document.getElementById('hotel-selector').value || document.getElementById('hotel-type').value;
    const customerName = document.getElementById('customer-telephone').value;
    const customerId = document.getElementById('customer-id-holder').value || null;
    const serviceCharge = parseFloat(document.getElementById('service_charge').textContent);

            const ingredientsData = {
                items: cart.map(item => ({
                    item_id: item.id,
                    quantity: item.quantity,
                    portion_size: item.portionSize || 'default'
                }))
            };

    fetch('deductIngredients.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(ingredientsData)
    })
    .then(response => response.json())
    .then(ingredientsResult => {
        if (!ingredientsResult.success) {
            // showAlert({
            //     type: 'error',
            //     title: 'Inventory Error',
            //     message: ingredientsResult.message || 'Failed to update inventory.',
            //     confirmButtonText: 'OK'
            // });
        }

        const subTotalValue = parseFloat(totalAmount + serviceCharge)

        const billData = {
            bill_id: currentBillId,
            hotel_type: hotelTypeId,
            table_id: document.getElementById('table').value,
            total_before_discount: totalBeforeDiscount,
            discount_amount: discount,
            payment_amount: subTotalValue,
            paid_amount: totalPaid,
            balance_amount: totalPaid - subTotalValue,
            status: 'completed',
            items: cart.map(item => ({
                item_id: item.id,
                quantity: item.quantity,
                portion_size: item.portionSize,
                name: item.name,
                price: item.price,
                fc: item.fc,
            })),
            payments: payments.map(payment => ({
                payment_method: payment.method,
                amount: payment.amount,
                card_id: payment.cardId
            })),
            customer_name: customerName,
            customer_id: customerId,
            service_charge: serviceCharge
        };
        if (hotelTypeId === '4' || hotelTypeId === '6') {
            const referenceInput = document.getElementById('ref-number') || document.getElementById('reference-number');

            // Validate reference number
            if (!referenceInput || !referenceInput.value.trim()) {
                showAlert({
                    type: 'error',
                    title: 'Missing Reference',
                    message: 'Please enter the uber/pickme reference number.',
                    confirmButtonText: 'OK'
                });
                        setTimeout(() => {
                                const handleEnter = function(event) {
                                        if (event.key === 'Enter') {
                                            event.preventDefault();
                                                
                                            // Look for visible buttons with OK text
                                            const okButton = Array.from(document.querySelectorAll('button')).find(btn => {
                                                const isVisible = btn.offsetParent !== null;
                                                const hasOkText = btn.textContent.trim().toUpperCase().includes('OK');
                                                return isVisible && hasOkText;
                                            });
                                                
                                            if (okButton) {
                                                okButton.click();
                                            }
                                                
                                            document.removeEventListener('keydown', handleEnter);
                                        }
                                };
                            document.addEventListener('keydown', handleEnter);
                        }, 100);

                return;
            }
            billData.reference_number = referenceInput.value.trim();
        }

        // Show loading message
        const loadingAlert = showAlert({
            type: 'info',
            title: 'Processing',
            message: 'Saving bill, please wait...',
            showCancelButton: false,
            confirmButtonText: 'OK',
            autoClose: false
        });

        fetch('save_bill.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(billData)
        })
        .then(response => response.json())
        .then(data => {
            closeAlert(loadingAlert);
            const advPaymentHolder = document.getElementById('adv-payment-amount')
            const tableID = document.getElementById('table').value
            
            // Get Sri Lanka date and time
            const sriLankaTime = getSriLankaDateTime();
            const localDate = sriLankaTime.toLocaleDateString('en-GB', { timeZone: 'Asia/Colombo' });
            const localTime = sriLankaTime.toLocaleTimeString('en-GB', { 
                timeZone: 'Asia/Colombo',
                hour12: true,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            const newDate = `${localDate} ${localTime}`;
            
            increaseBillCount()
            printPosBill(cart, currentBillId, tableID, newDate, totalPaid, totalAmount, serviceCharge)
            document.getElementById('payments-body').innerHTML = ''
            clearPersistantCart();
            clearSavedSelections();
            const hasAdvancePayment = isAdvPaymentChecked()
            if (hasAdvancePayment) {
                const cusId = document.getElementById('customer-id-holder').value
                if (!cusId) {
                    return
                } else {
                    deleteCustomerPayment(cusId)
                }
            }
            advPaymentHolder.value = 0
            resetAdvancePaymentCheckbox()
            if (data.success) {
                showAlert({
                    type: 'success',
                    title: 'Success',
                    message: 'Bill completed successfully!',
                    autoClose: false,
                    autoCloseTime: 3000,
                    onConfirm: () => {
                        cart.length = 0;
                        payments.length = 0;
                        updateCart();
                        closeCheckoutModal();
                        fetchNextBillId();

                        setTimeout(() => {
                            window.location.reload();
                        }, 100);
                    }
                });
                        setTimeout(() => {
                            const handleEnter = function(event) {
                                if (event.key === 'Enter') {
                                    event.preventDefault();
                                    
                                    // Look for visible buttons with OK text
                                    const okButton = Array.from(document.querySelectorAll('button')).find(btn => {
                                        const isVisible = btn.offsetParent !== null;
                                        const hasOkText = btn.textContent.trim().toUpperCase().includes('OK');
                                        return isVisible && hasOkText;
                                    });
                                    
                                    if (okButton) {
                                        okButton.click();
                                    }
                                    
                                    document.removeEventListener('keydown', handleEnter);
                                }
                            };
                            document.addEventListener('keydown', handleEnter);
                        }, 100);

                cart.length = 0;
                payments.length = 0;
                //resetTableStatus(document.getElementById('table').value)
                updateCart();
                closeCheckoutModal();
                fetchNextBillId();
                populateInitialTables();
            } else {
                showAlert({
                    type: 'error',
                    title: 'Error',
                    message: data.message || 'Failed to complete bill.',
                    confirmButtonText: 'Try Again'
                });
            }
        })
        .catch(error => {
            closeAlert(loadingAlert);
            console.error('Error saving bill:', error);
            showAlert({
                type: 'error',
                title: 'Connection Error',
                message: 'Unable to save bill. Please check your connection and try again.',
                confirmButtonText: 'OK'
            });
        });
    })
    .catch(error => {
        console.error('Error updating inventory:', error);
        // showAlert({
        //     type: 'error',
        //     title: 'Inventory Error',
        //     message: 'Failed to update inventory. Please try again.',
        //     confirmButtonText: 'OK'
        // });
    });
}

// Helper function to get Sri Lanka date and time
function getSriLankaDateTime() {
    const now = new Date();
    // Create a new date object with Sri Lanka timezone
    const sriLankaTime = new Date(now.toLocaleString("en-US", {timeZone: "Asia/Colombo"}));
    return sriLankaTime;
}

// Alternative helper function that returns formatted Sri Lanka date and time
function getSriLankaDateTimeFormatted() {
    const now = new Date();
    
    // Get Sri Lanka date
    const sriLankaDate = now.toLocaleDateString('en-GB', { 
        timeZone: 'Asia/Colombo',
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
    
    // Get Sri Lanka time
    const sriLankaTime = now.toLocaleTimeString('en-GB', { 
        timeZone: 'Asia/Colombo',
        hour12: true,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    
    return {
        date: sriLankaDate,
        time: sriLankaTime,
        dateTime: `${sriLankaDate} ${sriLankaTime}`,
        timestamp: now.toLocaleString('sv-SE', { timeZone: 'Asia/Colombo' }) // ISO format in Sri Lanka time
    };
}

// Usage example for other parts of your code:
// const sriLankaDateTime = getSriLankaDateTimeFormatted();
// console.log('Sri Lanka Date:', sriLankaDateTime.date);
// console.log('Sri Lanka Time:', sriLankaDateTime.time);
// console.log('Combined:', sriLankaDateTime.dateTime);
// console.log('Timestamp:', sriLankaDateTime.timestamp);

        // Toggle held bills dropdown
        function toggleHeldBillsDropdown() {
            const dropdown = document.getElementById('held-bills-dropdown');
            const heldBillBtn = document.querySelector('.footer-opt-btn:nth-child(4)');

            // Toggle dropdown visibility
            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
                heldBillBtn.classList.remove('active-held');
            } else {
                // Fetch held bills
                fetchHeldBills();
                dropdown.classList.add('show');
                heldBillBtn.classList.add('active-held');

                // Position dropdown above the button
                const buttonRect = heldBillBtn.getBoundingClientRect();
                dropdown.style.bottom = (window.innerHeight - buttonRect.top) + 'px';
                dropdown.style.left = buttonRect.left + 'px';
            }
        }

        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('held-bills-dropdown');
            const heldBillBtn = document.querySelector('.footer-opt-btn:nth-child(4)');

            if (dropdown.classList.contains('show') &&
                !dropdown.contains(event.target) &&
                event.target !== heldBillBtn) {
                dropdown.classList.remove('show');
                heldBillBtn.classList.remove('active-held');
            }
        });


        // Fetch held bills


        // function loadHeldBill(billId) {
        //     fetch(`fetch_held_bill.php?bill_id=${billId}`)
        //         .then(response => response.json())
        //         .then(data => {
        //             console.log("Loaded bill data:", data);
                    
        //             // Set current bill ID and status
        //             currentBillId = billId;
        //             isHeldBill = true;
        //             document.getElementById('next-bill-id').textContent = `Held Bill #${billId}`;
                    
        //             // Update hotel type and table
        //             const hotelTypeId = data.hotel_type;
                    
        //             console.log("Hotel Type ID:", hotelTypeId);
                    
        //             // Update dropdowns
        //             document.getElementById('hotel-type').value = hotelTypeId;
        //             document.getElementById('hotel-selector').value = hotelTypeId;
                    
        //             // IMPORTANT: Handle Uber/Pickme reference number field AFTER setting the hotel selector value
        //             setTimeout(() => {
        //                 // Remove any existing reference input first
        //                 const existingRefInput = document.getElementById('reference-input-container');
        //                 if (existingRefInput) {
        //                     existingRefInput.remove();
        //                 }
                        
        //                 // Check if Uber (id: 4) or Pickme (id: 6) is selected - use string comparison since values might be strings
        //                 if (hotelTypeId == 4 || hotelTypeId == 6) {
        //                     console.log("Creating reference input for Uber/Pickme");
                            
        //                     const serviceName = hotelTypeId == 4 ? 'Uber' : 'Pickme';
        //                     const hotelSelector = document.getElementById('hotel-selector');
                            
        //                     if (!hotelSelector) {
        //                         console.error("Hotel selector element not found!");
        //                         return;
        //                     }
                            
        //                     // Create reference number input
        //                     const refContainer = document.createElement('div');
        //                     refContainer.id = 'reference-input-container';
        //                     refContainer.style.marginTop = '10px';
        //                     refContainer.style.marginBottom = '15px';
                            
        //                     // Use the saved reference number from the database if available
        //                     const savedRefNumber = data.reference_number || '';
        //                     console.log("Saved reference number:", savedRefNumber);
                            
        //                     refContainer.innerHTML = `
        //                         <label for="ref-number">${serviceName} Reference Number:</label>
        //                         <input type="text" id="ref-number" 
        //                             style="display:block; width:100%; padding:8px; margin-top:5px; border:1px solid #ccc; border-radius:4px;" 
        //                             placeholder="Enter ${serviceName} reference number" 
        //                             value="${savedRefNumber}" required>
        //                     `;
                            
        //                     // Insert after hotel selector
        //                     if (hotelSelector.nextSibling) {
        //                         hotelSelector.parentNode.insertBefore(refContainer, hotelSelector.nextSibling);
        //                     } else {
        //                         hotelSelector.parentNode.appendChild(refContainer);
        //                     }
                            
        //                     console.log("Reference input created successfully");
        //                 } else {
        //                     console.log("Not Uber/Pickme, no reference input needed");
        //                 }
                        
        //                 // Continue with the rest of the function
        //                 if (data.table_id) {
        //                     document.getElementById('table').value = data.table_id;
        //                 }
                        
        //                 // Clear cart and add held items
        //                 cart.length = 0;
        //                 data.items.forEach(item => {
        //                     // Create display name with portion size if available
        //                     const portionSize = item.portion_size;
        //                     cart.push({
        //                         id: item.item_id,
        //                         uniqueId: `${item.item_id}-${portionSize}`,
        //                         name: item.product_name,
        //                         price: parseFloat(item.price),
        //                         quantity: parseInt(item.quantity),
        //                         portionSize: portionSize,
        //                         fc: parseInt(item.free_count),
        //                     });
        //                 });
        //                 localStorage.setItem('restaurant_cart', JSON.stringify(cart));
        //                 updateCart();
                        
        //                 // Clear payments and add held payments
        //                 payments.length = 0;
        //                 data.payments.forEach(payment => {
        //                     payments.push({
        //                         method: payment.payment_method,
        //                         cardId: payment.card_id,
        //                         amount: parseFloat(payment.amount)
        //                     });
        //                 });
                        
        //                 // Close dropdown using classList
        //                 document.getElementById('held-bills-dropdown').classList.remove('show');
        //                 document.querySelector('.footer-opt-btn:nth-child(4)').classList.remove('active-held');
        //             }, 100); // Short timeout to ensure hotel selector value is set
        //         })
        //         .catch(error => {
        //             console.error('Error loading held bill:', error);
        //             alert('Error loading held bill. Please try again.');
        //         });
        // }


        // Helper function to manually trigger Uber/Pickme reference field creation
        function checkAndCreateReferenceField() {
            const hotelSelector = document.getElementById('hotel-selector');
            if (!hotelSelector) return;
            
            const hotelTypeId = hotelSelector.value;
            
            // Remove any existing reference input first
            const existingRefInput = document.getElementById('reference-input-container');
            if (existingRefInput) {
                existingRefInput.remove();
            }
            
            // Check if Uber (id: 4) or Pickme (id: 6) is selected
            if (hotelTypeId == 4 || hotelTypeId == 6) {
                const serviceName = hotelTypeId == 4 ? 'Uber' : 'Pickme';
                
                // Create reference number input
                const refContainer = document.createElement('div');
                refContainer.id = 'reference-input-container';
                refContainer.style.marginTop = '10px';
                refContainer.style.marginBottom = '15px';
                
                refContainer.innerHTML = `
                    <label for="ref-number">${serviceName} Reference Number:</label>
                    <input type="text" id="ref-number" 
                        style="display:block; width:100%; padding:8px; margin-top:5px; border:1px solid #ccc; border-radius:4px;" 
                        placeholder="Enter ${serviceName} reference number" required>
                `;
                
                // Insert after hotel selector
                if (hotelSelector.nextSibling) {
                    hotelSelector.parentNode.insertBefore(refContainer, hotelSelector.nextSibling);
                } else {
                    hotelSelector.parentNode.appendChild(refContainer);
                }
                
                console.log("Reference input created by manual check");
                return true;
            }
            
            return false;
        }

        // Function to get reference number
        function getReferenceNumber() {
            const refInput = document.getElementById('ref-number');
            return refInput ? refInput.value.trim() : null;
        }

        // To use at the end of loadHeldBill as a backup
        function forceReferenceFieldCheck() {
            setTimeout(checkAndCreateReferenceField, 200);
        }



        // Add event listeners for payment method
        document.addEventListener('DOMContentLoaded', function() {
            // Update card ID field visibility based on payment method
            const paymentMethod = document.getElementById('payment-method');
            const cardIdField = document.getElementById('card-id');

            paymentMethod.addEventListener('change', function() {
                if (this.value === 'credit' || this.value === 'debit') {
                    cardIdField.style.display = 'flex';
                    cardIdField.placeholder = this.value === 'credit' ? 'Credit Card Number' : 'Debit Card Number';
                    cardIdField.required = true;
                } else if (this.value === 'bank') {
                    cardIdField.style.display = 'flex';
                    cardIdField.placeholder = 'Reference Number';
                    cardIdField.required = true;
                } else {
                    cardIdField.style.display = 'none';
                    cardIdField.required = false;
                }
            });

            // Trigger change event initially
            paymentMethod.dispatchEvent(new Event('change'));
        });


        function createAlertContainer() {
            if (!document.getElementById('alert-container')) {
                const alertContainer = document.createElement('div');
                alertContainer.id = 'alert-container';
                document.body.appendChild(alertContainer);
            }
        }

        // Show alert message
        function showAlert(options) {
            createAlertContainer();

            const defaults = {
                type: 'info', // success, error, warning, info
                title: 'Notification',
                message: '',
                icon: null,
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
                showCancelButton: false,
                onConfirm: null,
                onCancel: null,
                autoClose: false,
                autoCloseTime: 3000
            };

            const settings = {
                ...defaults,
                ...options
            };

            // Set icon based on type if not provided
            if (!settings.icon) {
                switch (settings.type) {
                    case 'success':
                        settings.icon = 'fa-check-circle';
                        break;
                    case 'error':
                        settings.icon = 'fa-times-circle';
                        break;
                    case 'warning':
                        settings.icon = 'fa-exclamation-triangle';
                        break;
                    case 'info':
                    default:
                        settings.icon = 'fa-info-circle';
                        break;
                }
            }

            // Create alert overlay
            const alertOverlay = document.createElement('div');
            alertOverlay.className = 'alert-overlay';

            // Create alert box
            const alertBox = document.createElement('div');
            alertBox.className = `alert-box alert-${settings.type}`;

            // Create alert header
            const alertHeader = document.createElement('div');
            alertHeader.className = 'alert-header';

            const alertIcon = document.createElement('i');
            alertIcon.className = `alert-icon fa-solid ${settings.icon}`;

            const alertTitle = document.createElement('h3');
            alertTitle.className = 'alert-title';
            alertTitle.textContent = settings.title;

            alertHeader.appendChild(alertIcon);
            alertHeader.appendChild(alertTitle);

            // Create alert body
            const alertBody = document.createElement('div');
            alertBody.className = 'alert-body';
            alertBody.textContent = settings.message;

            // Create alert footer
            const alertFooter = document.createElement('div');
            alertFooter.className = 'alert-footer';

            // Create confirm button
            const confirmButton = document.createElement('button');
            confirmButton.className = 'alert-btn alert-btn-primary';
            confirmButton.textContent = settings.confirmButtonText;

            // Add event listener to confirm button
            confirmButton.addEventListener('click', () => {
                closeAlert(alertOverlay);
                if (typeof settings.onConfirm === 'function') {
                    settings.onConfirm();
                }
            });

            alertFooter.appendChild(confirmButton);

            // Create cancel button if needed
            if (settings.showCancelButton) {
                const cancelButton = document.createElement('button');
                cancelButton.className = 'alert-btn alert-btn-secondary';
                cancelButton.textContent = settings.cancelButtonText;

                // Add event listener to cancel button
                cancelButton.addEventListener('click', () => {
                    closeAlert(alertOverlay);
                    if (typeof settings.onCancel === 'function') {
                        settings.onCancel();
                    }
                });

                // Insert cancel button before confirm button
                alertFooter.insertBefore(cancelButton, confirmButton);
            }

            // Assemble alert box
            alertBox.appendChild(alertHeader);
            alertBox.appendChild(alertBody);
            alertBox.appendChild(alertFooter);

            // Add alert box to overlay
            alertOverlay.appendChild(alertBox);

            // Add overlay to container
            document.getElementById('alert-container').appendChild(alertOverlay);

            // Show alert
            setTimeout(() => {
                alertOverlay.classList.add('show');
            }, 10);

            // Auto close if enabled
            if (settings.autoClose) {
                setTimeout(() => {
                    closeAlert(alertOverlay);
                }, settings.autoCloseTime);
            }

            return alertOverlay;
        }

        // Close alert
        function closeAlert(alertOverlay) {
            alertOverlay.classList.remove('show');

            // Remove alert after animation
            setTimeout(() => {
                if (alertOverlay.parentNode) {
                    alertOverlay.parentNode.removeChild(alertOverlay);
                }
            }, 300);
        }

        // Show confirm dialog
        function showConfirm(options) {
            return showAlert({
                type: 'warning',
                showCancelButton: true,
                ...options
            });
        }


        document.addEventListener('DOMContentLoaded', function() {
            // Global variables
            let currentPage = 1;
            const recordsPerPage = 50;
            let isLoading = false;
            let hasMoreRecords = true;
            let activeFilters = {
                dateFrom: '',
                dateTo: '',
                status: '',
                search: ''
            };

            // DOM Elements
            const transactionsBtn = document.querySelector('.footer-opt-btn:nth-child(3)');
            const transactionsModal = document.getElementById('transactions-modal');
            const closeTransactionsBtn = document.getElementById('close-transactions');
            const transactionsList = document.getElementById('transactions-list');
            const loadingSpinner = document.getElementById('loading-spinner');
            const noTransactions = document.getElementById('no-transactions');

            const billDetailsModal = document.getElementById('bill-details-modal');
            const closeBillDetailsBtn = document.getElementById('close-bill-details');
            const billIdHeader = document.getElementById('bill-id-header');
            const billItemsList = document.getElementById('bill-items-list');
            const billPaymentsList = document.getElementById('bill-payments-list');

            // Filter elements
            const dateFrom = document.getElementById('date-from');
            const dateTo = document.getElementById('date-to');
            const statusFilter = document.getElementById('status-filter');
            const searchBill = document.getElementById('search-bill');
            const applyFiltersBtn = document.getElementById('apply-filters');

            // Bill details elements customer-telephone
            const billDate = document.getElementById('bill-date');
            const billTable = document.getElementById('bill-table');
            const billCustomer = document.getElementById('bill-customer');
            const billStatus = document.getElementById('bill-status');
            const billHotelType = document.getElementById('bill-hotel-type');
            const billSubtotal = document.getElementById('bill-subtotal');
            const billDiscount = document.getElementById('bill-discount');
            const billTotal = document.getElementById('bill-total');
            const billPaidTotal = document.getElementById('bill-paid-total');
            const billBalance = document.getElementById('bill-balance');

            const printBillBtn = document.getElementById('print-bill-btn');
            const reopenBillBtn = document.getElementById('reopen-bill-btn');
            const addPaymentToBillBtn = document.getElementById('add-payment-to-bill-btn');
            const closeBillDetailsBtn2 = document.getElementById('close-bill-details-btn');

            // Event listeners for opening and closing modals
            transactionsBtn.addEventListener('click', function() {
                openTransactionsModal();
            });

            // You can also listen for F3 key press
            document.addEventListener('keydown', function(e) {
                if (e.key === 'F3') {
                    e.preventDefault();
                    openTransactionsModal();
                }
            });

            closeTransactionsBtn.addEventListener('click', function() {
                transactionsModal.style.display = 'none';
            });

            closeBillDetailsBtn.addEventListener('click', function() {
                billDetailsModal.style.display = 'none';
            });

            closeBillDetailsBtn2.addEventListener('click', function() {
                billDetailsModal.style.display = 'none';
            });

            // Filter application
            applyFiltersBtn.addEventListener('click', function() {
                activeFilters = {
                    dateFrom: dateFrom.value,
                    dateTo: dateTo.value,
                    status: statusFilter.value,
                    search: searchBill.value
                };

                // Reset and reload transactions
                currentPage = 1;
                hasMoreRecords = true;
                transactionsList.innerHTML = '';
                loadTransactions();
            });

            // Infinite scroll implementation
            document.querySelector('.transactions-container').addEventListener('scroll', function(e) {
                const {
                    scrollTop,
                    scrollHeight,
                    clientHeight
                } = e.target;

                // Check if user has scrolled to the bottom
                if (scrollHeight - scrollTop <= clientHeight + 50) {
                    if (!isLoading && hasMoreRecords) {
                        loadNextPage();
                    }
                }
            });

            function openTransactionsModal() {
                // Reset state
                currentPage = 1;
                hasMoreRecords = true;
                transactionsList.innerHTML = '';
                activeFilters = {
                    dateFrom: '',
                    dateTo: '',
                    status: '',
                    search: ''
                };

                // Reset filter inputs
                dateFrom.value = '';
                dateTo.value = '';
                statusFilter.value = '';
                searchBill.value = '';

                // Display modal and load initial transactions
                transactionsModal.style.display = 'block';
                loadTransactions();
            }

            function loadNextPage() {
                currentPage++;
                loadTransactions();
            }

            function loadTransactions() {
                if (isLoading || !hasMoreRecords) return;

                isLoading = true;
                loadingSpinner.style.display = 'block';
                noTransactions.style.display = 'none';

                // Prepare query parameters
                const params = new URLSearchParams({
                    page: currentPage,
                    limit: recordsPerPage,
                    dateFrom: activeFilters.dateFrom,
                    dateTo: activeFilters.dateTo,
                    status: activeFilters.status,
                    search: activeFilters.search
                });

                // Fetch transactions from server
                fetch(`get_transactions.php?${params.toString()}`)
                    .then(response => response.json())
                    .then(data => {
                        loadingSpinner.style.display = 'none';
                        isLoading = false;

                        if (data.transactions && data.transactions.length > 0) {
                            renderTransactions(data.transactions);
                            hasMoreRecords = data.transactions.length === recordsPerPage;
                        } else {
                            hasMoreRecords = false;
                            if (currentPage === 1) {
                                noTransactions.style.display = 'block';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching transactions:', error);
                        loadingSpinner.style.display = 'none';
                        isLoading = false;

                        // Show error notification
                        showAlert('Failed to load transactions. Please try again.', 'error');
                    });
            }

            function renderTransactions(transactions) {
                const fragment = document.createDocumentFragment();

                transactions.forEach(transaction => {
                    const row = document.createElement('tr');

                    // Format date
                    const billDate = new Date(transaction.bill_time);
                    const formattedDate = billDate.toLocaleString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    // Set status class
                    const statusClass = `status-${transaction.status}`;

                    row.innerHTML = `
                        <td>${transaction.bill_id}</td>
                        <td>${formattedDate}</td>
                        <td>${transaction.table_id || 'N/A'}</td>
                        <td>${transaction.customer_name || 'Walk-in'}</td>
                        <td>${parseFloat(transaction.payment_amount).toFixed(2)}</td>
                        <td>${parseFloat(transaction.paid_amount).toFixed(2)}</td>
                        <td>${parseFloat(transaction.balance_amount).toFixed(2)}</td>
                        <td><span class="status-badge ${statusClass}">${transaction.status}</span></td>
                        <td class="transaction-actions">
                            <button class="view-btn" data-bill-id="${transaction.bill_id}">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <button class="print-btn" data-bill-id="${transaction.bill_id}">
                                <i class="fa-solid fa-print"></i>
                            </button>
                        </td>
                    `;

                    // Add event listeners for view and print buttons
                    const viewBtn = row.querySelector('.view-btn');
                    const printBtn = row.querySelector('.print-btn');

                    viewBtn.addEventListener('click', function() {
                        const billId = this.getAttribute('data-bill-id');
                        viewBillDetails(billId);
                    });

                    printBtn.addEventListener('click', function() {
                        const billId = this.getAttribute('data-bill-id');
                        printBill(billId);
                    });

                    fragment.appendChild(row);
                });

                transactionsList.appendChild(fragment);
            }

            function viewBillDetails(billId) {
                // Reset bill details
                billItemsList.innerHTML = '';
                billPaymentsList.innerHTML = '';

                // Show loading
                billIdHeader.textContent = ` #${billId} (Loading...)`;
                billDetailsModal.style.display = 'block';

                // Fetch bill details from server
                fetch(`get_bill_details.php?bill_id=${billId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            showAlert(data.error, 'error');
                            billDetailsModal.style.display = 'none';
                            return;
                        }

                        // Update bill header and summary
                        billIdHeader.textContent = ` #${billId}`;

                        const billDateTime = new Date(data.bill.bill_time);
                        billDate.textContent = billDateTime.toLocaleString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        billTable.textContent = data.bill.table_id || 'N/A';
                        billCustomer.textContent = data.bill.customer_name || 'Walk-in';
                        billStatus.textContent = data.bill.status;
                        billHotelType.textContent = data.bill.hotel_type;
                        billDiscount.textContent = parseFloat(data.bill.discount_amount).toFixed(2);
                        billTotal.textContent = parseFloat(data.bill.payment_amount).toFixed(2);
                        billPaidTotal.textContent = parseFloat(data.bill.paid_amount).toFixed(2);
                        billBalance.textContent = parseFloat(data.bill.balance_amount).toFixed(2);

                        // Add status class
                        billStatus.className = '';
                        billStatus.classList.add(`status-${data.bill.status}`);

                        // Render bill items
                        let subtotal = 0;
                        data.items.forEach(item => {
                            const row = document.createElement('tr');
                            const itemTotal = parseFloat(item.price) * parseFloat(item.quantity);
                            subtotal += itemTotal;

                            const portionSize = item.portion_size;
                            // let displayName = item.item_name;

                            // if (portionSize !== '') {
                            //     displayName = `${item.item_name} (${portionSize.charAt(0).toUpperCase() + portionSize.slice(1)})`;
                            // } else {
                            //     displayName = item.item_name;
                            // }

                            row.innerHTML = `
                                <td>${item.item_name}</td>
                                <td>${parseFloat(item.price).toFixed(2)}</td>
                                <td>${item.quantity}</td>
                                <td>${itemTotal.toFixed(2)}</td>
                            `;

                            billItemsList.appendChild(row);
                        });

                        billSubtotal.textContent = subtotal.toFixed(2);

                        // Render bill payments
                        data.payments.forEach(payment => {
                            const row = document.createElement('tr');
                            const paymentDate = new Date(payment.created_at);

                            row.innerHTML = `
                                <td>${paymentDate.toLocaleString('en-US', {
                                    month: 'short',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })}</td>
                                <td>${payment.payment_method}</td>
                                <td>${payment.card_id || 'N/A'}</td>
                                <td>${parseFloat(payment.amount).toFixed(2)}</td>
                            `;

                            billPaymentsList.appendChild(row);
                        });

                        // Show/hide action buttons based on bill status
                        if (data.bill.status === 'completed') {
                            reopenBillBtn.style.display = 'flex';
                        } else {
                            reopenBillBtn.style.display = 'none';
                        }

                        if (data.bill.status === 'active') {
                            addPaymentToBillBtn.style.display = 'flex';
                        } else {
                            addPaymentToBillBtn.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching bill details:', error);
                        showAlert('Failed to load bill details. Please try again.', 'error');
                        billDetailsModal.style.display = 'none';
                    });
            }

// This function handles printing old completed bills from the transactions list
function printBill(billId) {
    // Show loading indicator
    showLoading("Preparing old bill for printing...");
    
    // Fetch the completed bill data
    fetch(`print_old_bill.php?bill_id=${billId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Server error');
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                throw new Error(data.message || 'Failed to get bill data');
            }
            
            // Open the print window with the bill content
            const printWindow = window.open('', '_blank');
            printWindow.document.write(data.html);
            printWindow.document.close();
            
            // After the content is loaded, trigger print
            printWindow.onload = function() {
                printWindow.focus();
                printWindow.print();
            };
            
            hideLoading();
        })
        .catch(error => {
            console.error('Error printing bill:', error);
            hideLoading();
            alert('Failed to print bill: ' + error.message);
        });
}

// Helper functions for loading indicator
function showLoading(message) {
    // Create loading overlay if it doesn't exist
    if (!document.getElementById('loading-overlay')) {
        const overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.style.position = 'fixed';
        overlay.style.top = '0';
        overlay.style.left = '0';
        overlay.style.width = '100%';
        overlay.style.height = '100%';
        overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
        overlay.style.display = 'flex';
        overlay.style.justifyContent = 'center';
        overlay.style.alignItems = 'center';
        overlay.style.zIndex = '9999';
        
        const spinner = document.createElement('div');
        spinner.className = 'loading-content';
        spinner.innerHTML = `
            <i class="fa-solid fa-spinner fa-spin fa-3x" style="color: white;"></i>
            <p id="loading-message" style="color: white; margin-top: 10px; font-size: 16px;">${message}</p>
        `;
        
        overlay.appendChild(spinner);
        document.body.appendChild(overlay);
    } else {
        document.getElementById('loading-message').textContent = message;
        document.getElementById('loading-overlay').style.display = 'flex';
    }
}

function hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

            // Event listeners for bill detail actions
            printBillBtn.addEventListener('click', function() {
                const billId = billIdHeader.textContent.trim().replace('#', '');
                printBill(billId);
            });

            reopenBillBtn.addEventListener('click', function() {
                const billId = billIdHeader.textContent.trim().replace('#', '');

                if (confirm('Are you sure you want to reopen this bill?')) {
                    fetch('reopen_bill.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                bill_id: billId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showAlert('Bill reopened successfully!', 'success');
                                billDetailsModal.style.display = 'none';

                                // Reload transactions to reflect changes
                                currentPage = 1;
                                transactionsList.innerHTML = '';
                                loadTransactions();
                            } else {
                                showAlert(data.error || 'Failed to reopen bill.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error reopening bill:', error);
                            showAlert('Failed to reopen bill. Please try again.', 'error');
                        });
                }
            });

            addPaymentToBillBtn.addEventListener('click', function() {
                const billId = billIdHeader.textContent.trim().replace('#', '');

                // For simplicity, we'll use the checkout modal for adding payment
                // You can create a dedicated modal or adapt this to your needs
                const checkoutModal = document.getElementById('checkout-modal');

                // Set up the checkout modal for adding payment to existing bill
                // This assumes your checkout modal has this functionality
                // You might need to customize this based on your actual implementation
                if (window.setupCheckoutForExistingBill) {
                    window.setupCheckoutForExistingBill(billId);
                    billDetailsModal.style.display = 'none';
                    checkoutModal.style.display = 'block';
                } else {
                    showAlert('Payment feature not available. Please implement this functionality.', 'warning');
                }
            });

            closeBillDetailsBtn.addEventListener('click', function() {
                billDetailsModal.style.display = 'none';
            });

            // Utility function to show alerts
            function showAlert(message, type = 'info') {
                const alertContainer = document.getElementById('alert-container');
                const alert = document.createElement('div');
                alert.className = `alert alert-${type}`;
                alert.innerHTML = `
                    <span class="alert-icon">
                        <i class="fa-solid ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                    </span>
                    <span class="alert-message">${message}</span>
                `;

                alertContainer.appendChild(alert);

                // Auto remove after a few seconds
                setTimeout(() => {
                    alert.classList.add('fade-out');
                    setTimeout(() => {
                        alertContainer.removeChild(alert);
                    }, 300);
                }, 3000);
            }
        });


        document.getElementById('temp-print-btn').addEventListener('click', () => {
            let shop_bucket = collectCartData();
            navigateToPrintTempBill(shop_bucket);
        });

        // function navigateToPrintTempBill(cartData) {
        //     const cartJSON = encodeURIComponent(JSON.stringify(cartData));
        //     const hotelType = encodeURIComponent(document.getElementById('hotel-type').value);
        //     const billId = encodeURIComponent(document.getElementById('next-bill-id').textContent.replace('Next Bill: #', '').replace('Held Bill #', ''));
        //     window.open(`pos_temp_bill.php?cart=${cartJSON}&hotel_type=${hotelType}&bill_id=${billId}`, '_blank');
        // }

        function navigateToPrintTempBill(cartData) {
            const selectedHotelType = document.getElementById('hotel-selector').value;
            const selectedTable = document.getElementById('table').value;
            if (!Array.isArray(cart) || cart.length === 0) {
                showAlert({
                    type: 'warning',
                    title: 'Empty Cart',
                    message: 'Cannot print bill with an empty cart.',
                    confirmButtonText: 'OK'
                });
                
                // Add Enter key listener to close alert
                setTimeout(() => {
                    const handleEnter = function(event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            
                            // Look for visible buttons with OK text
                            const okButton = Array.from(document.querySelectorAll('button')).find(btn => {
                                const isVisible = btn.offsetParent !== null;
                                const hasOkText = btn.textContent.trim().toUpperCase().includes('OK');
                                return isVisible && hasOkText;
                            });
                            
                            if (okButton) {
                                okButton.click();
                            }
                            
                            document.removeEventListener('keydown', handleEnter);
                        }
                    };
                    document.addEventListener('keydown', handleEnter);
                }, 100);
                return; // Exit the function early
            }
            else if (selectedHotelType == "0") {
                showAlert({
                    type: 'warning',
                    title: 'Select Hotel Type',
                    message: 'Please select a hotel type!',
                    confirmButtonText: 'OK'
                });
                
                // Add Enter key listener to close alert
                setTimeout(() => {
                    const handleEnter = function(event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            
                            // Look for visible buttons with OK text
                            const okButton = Array.from(document.querySelectorAll('button')).find(btn => {
                                const isVisible = btn.offsetParent !== null;
                                const hasOkText = btn.textContent.trim().toUpperCase().includes('OK');
                                return isVisible && hasOkText;
                            });
                            
                            if (okButton) {
                                okButton.click();
                            }
                            
                            document.removeEventListener('keydown', handleEnter);
                        }
                    };
                    document.addEventListener('keydown', handleEnter);
                }, 100);
                return; // Exit the function early
            }
            else if (selectedHotelType == "1" && selectedTable == "") {
                showAlert({
                    type: 'warning',
                    title: 'Select Table',
                    message: 'Please select a table!',
                    confirmButtonText: 'OK'
                });
                
                // Add Enter key listener to close alert
                setTimeout(() => {
                    const handleEnter = function(event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            
                            // Look for visible buttons with OK text
                            const okButton = Array.from(document.querySelectorAll('button')).find(btn => {
                                const isVisible = btn.offsetParent !== null;
                                const hasOkText = btn.textContent.trim().toUpperCase().includes('OK');
                                return isVisible && hasOkText;
                            });
                            
                            if (okButton) {
                                okButton.click();
                            }
                            
                            document.removeEventListener('keydown', handleEnter);
                        }
                    };
                    document.addEventListener('keydown', handleEnter);
                }, 100);
                return; // Exit the function early
            }

            const cartJSON = encodeURIComponent(JSON.stringify(cartData));
            const hotelTypeId = document.getElementById('hotel-selector').value || document.getElementById('hotel-type').value;
            const hotelTypeSelect = document.getElementById('hotel-type');
            const hotelTypeName = hotelTypeSelect.options[hotelTypeSelect.selectedIndex].text;
            const billId = encodeURIComponent(document.getElementById('next-bill-id').textContent.replace('Next Bill: #', '').replace('Held Bill #', ''));
            const tableNumber = document.getElementById('table').value;
            // Get reference number if it's Uber or Pick Me
            let referenceNumber = '';
            if (hotelTypeId === '4' || hotelTypeId === '6') {
                const referenceInput = document.getElementById('ref-number') || document.getElementById('reference-number');
                if (referenceInput) {
                    referenceNumber = referenceInput.value.trim();
                }
            }
            window.open(`pos_temp_bill2.php?cart=${cartJSON}&hotel_type_id=${hotelTypeId}&hotel_type_name=${encodeURIComponent(hotelTypeName)}&bill_id=${billId}&reference_number=${encodeURIComponent(referenceNumber)}&table_number=${encodeURIComponent(tableNumber)}`, '_blank');
        }

        // Bind F6 key to print temporary bill
        document.addEventListener('keydown', function(event) {
            if (event.key === 'F6') {
                let shop_bucket = collectCartData();
                navigateToPrintTempBill(shop_bucket);
            }
        });


        function collectCartData() {
            return cart.map(item => ({
                id: item.id,
                name: item.name,
                price: item.price,
                quantity: item.quantity
            }));
        }
        document.addEventListener("keydown", function(event) {
            if (event.code === "Home") {
                window.location.href = "../panel/pos-panel.php";
            }
        });





        //cancel bill
        // Add event listener for the cancel bill button
        // document.addEventListener('DOMContentLoaded', function() {
        //     const cancelBillBtn = document.getElementById('cancel-bill-btn');

        //     if (cancelBillBtn) {
        //         cancelBillBtn.addEventListener('click', function() {
        //             showCancelBillDialog();
        //         });
        //     }
        // });

        // // Function to show cancel bill dialog
        // function showCancelBillDialog() {
        //     // Check if cart is empty
        //     if (cart.length === 0) {
        //         showAlert({
        //             type: 'warning',
        //             title: 'Empty Cart',
        //             message: 'There are no items in the cart to cancel.',
        //             confirmButtonText: 'OK'
        //         });
        //         return;
        //     }

        //     // Calculate total amount
        //     const totalAmount = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        //     const discount = parseFloat(document.getElementById('discount-input').value) || 0;
        //     const totalAfterDiscount = totalAmount - discount;

        //     // Create the modal HTML
        //     const modalHTML = `
        //         <div class="modal-content">
        //             <div class="modal-header">
        //                 <h2><i class="fa-solid fa-ban"></i> Cancel Bill</h2>
        //                 <span class="close">&times;</span>
        //             </div>
        //             <div class="modal-body">
        //                 <p>You are about to cancel bill #${currentBillId}.</p>
        //                 <p><strong>Total Amount:</strong> Rs. ${totalAfterDiscount.toFixed(2)}</p>
        //                 <p><strong>Items:</strong> ${cart.length}</p>

        //                 <div class="form-group">
        //                     <label for="cancel-reason"><strong>Reason for Cancellation:</strong></label>
        //                     <textarea id="cancel-reason" rows="3" placeholder="Please provide a reason for cancellation" required></textarea>
        //                 </div>
        //             </div>
        //             <div class="modal-footer">
        //                 <button id="confirm-cancel-bill" class="btn-danger"><i class="fa-solid fa-check"></i> Confirm Cancellation</button>
        //                 <button id="cancel-cancel-bill" class="btn-secondary"><i class="fa-solid fa-times"></i> Go Back</button>
        //             </div>
        //         </div>
        //     `;

        //     // Create and show modal
        //     const modalContainer = document.createElement('div');
        //     modalContainer.className = 'modal';
        //     modalContainer.id = 'cancel-bill-modal';
        //     modalContainer.innerHTML = modalHTML;
        //     document.body.appendChild(modalContainer);

        //     // Show the modal
        //     //modalContainer.style.display = 'block';

        //     // Add event listeners
        //     const closeBtn = modalContainer.querySelector('.close');
        //     const cancelBtn = document.getElementById('cancel-cancel-bill');
        //     const confirmBtn = document.getElementById('confirm-cancel-bill');

        //     closeBtn.addEventListener('click', () => {
        //         document.body.removeChild(modalContainer);
        //     });

        //     cancelBtn.addEventListener('click', () => {
        //         document.body.removeChild(modalContainer);
        //     });

        //     confirmBtn.addEventListener('click', () => {
        //         const reason = document.getElementById('cancel-reason').value.trim();

        //         if (!reason) {
        //             // Show error if no reason provided
        //             const reasonTextarea = document.getElementById('cancel-reason');
        //             reasonTextarea.classList.add('error');
        //             reasonTextarea.placeholder = 'Reason is required';
        //             return;
        //         }

        //         // Process the cancellation
        //         processCancelBill(reason, totalAfterDiscount);
        //         document.body.removeChild(modalContainer);
        //     });
        // }

        // // Function to process bill cancellation
        // function processCancelBill(reason, totalAmount) {
        //     // Show loading message
        //     const loadingAlert = showAlert({
        //         type: 'info',
        //         title: 'Processing',
        //         message: 'Cancelling bill, please wait...',
        //         showCancelButton: false,
        //         confirmButtonText: 'OK',
        //         autoClose: false
        //     });

        //     // Create cancel bill data
        //     const cancelData = {
        //         bill_id: currentBillId,
        //         hotel_type: document.getElementById('hotel-type').value,
        //         table_id: document.getElementById('table').value,
        //         total_before_discount: cart.reduce((sum, item) => sum + (item.price * item.quantity), 0),
        //         discount_amount: parseFloat(document.getElementById('discount-input').value) || 0,
        //         payment_amount: totalAmount,
        //         reason: reason,
        //         items: cart.map(item => ({
        //             item_id: item.id,
        //             quantity: item.quantity
        //         })),
        //         customer_name: document.getElementById('customer-telephone').value
        //     };

        //     // Send request to server
        //     fetch('cancel_bill.php', {
        //         method: 'POST',
        //         headers: {
        //             'Content-Type': 'application/json'
        //         },
        //         body: JSON.stringify(cancelData)
        //     })
        //     .then(response => response.json())
        //     .then(data => {
        //         closeAlert(loadingAlert);

        //         if (data.success) {
        //             showAlert({
        //                 type: 'success',
        //                 title: 'Success',
        //                 message: 'Bill cancelled successfully!',
        //                 autoClose: true,
        //                 autoCloseTime: 3000,
        //                 onConfirm: () => {
        //                     // Clear cart
        //                     cart.length = 0;
        //                     updateCart();
        //                     fetchNextBillId();
        //                 }
        //             });

        //             // Clear cart and fetch next bill ID
        //             cart.length = 0;
        //             updateCart();
        //             fetchNextBillId();
        //         } else {
        //             showAlert({
        //                 type: 'error',
        //                 title: 'Error',
        //                 message: data.message || 'Failed to cancel bill.',
        //                 confirmButtonText: 'Try Again'
        //             });
        //         }
        //     })
        //     .catch(error => {
        //         closeAlert(loadingAlert);
        //         console.error('Error cancelling bill:', error);
        //         showAlert({
        //             type: 'error',
        //             title: 'Connection Error',
        //             message: 'Unable to cancel bill. Please check your connection and try again.',
        //             confirmButtonText: 'OK'
        //         });
        //     });
        // }


        // Add event listener for the cancel bill button
        document.addEventListener('DOMContentLoaded', function() {
            const cancelBillBtn = document.getElementById('cancel-bill-btn');

            if (cancelBillBtn) {
                cancelBillBtn.addEventListener('click', function() {
                    showCancelBillDialog();
                });
            }
        });

        // Function to show cancel bill dialog
        function showCancelBillDialog() {
            // Check if cart is empty
            if (cart.length === 0) {
                showAlert({
                    type: 'warning',
                    title: 'Empty Cart',
                    message: 'There are no items in the cart to cancel.',
                    confirmButtonText: 'OK'
                });
                            setTimeout(() => {
                                        const handleEnter = function(event) {
                                            if (event.key === 'Enter') {
                                                event.preventDefault();
                                                
                                                // Look for visible buttons with OK text
                                                const okButton = Array.from(document.querySelectorAll('button')).find(btn => {
                                                    const isVisible = btn.offsetParent !== null;
                                                    const hasOkText = btn.textContent.trim().toUpperCase().includes('OK');
                                                    return isVisible && hasOkText;
                                                });
                                                
                                                if (okButton) {
                                                    okButton.click();
                                                }
                                                
                                                document.removeEventListener('keydown', handleEnter);
                                            }
                                        };
                                        document.addEventListener('keydown', handleEnter);
                            }, 100);
                return;
            }

            // Calculate total amount
            const totalAmount = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const discount = parseFloat(document.getElementById('discount-input').value) || 0;
            const totalAfterDiscount = totalAmount - discount;

            // Create the modal HTML
            const modalHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h2><i class="fa-solid fa-ban"></i> Cancel Bill</h2>
                        <span class="close">&times;</span>
                    </div>
                    <div class="modal-body">
                        <p>You are about to cancel bill #${currentBillId}.</p>
                        <p><strong>Total Amount:</strong> Rs. ${totalAfterDiscount.toFixed(2)}</p>
                        <p><strong>Items:</strong> ${cart.length}</p>
                        
                        <div class="form-group">
                            <label for="cancel-reason"><strong>Reason for Cancellation:</strong></label>
                            <textarea id="cancel-reason" rows="3" placeholder="Please provide a reason for cancellation" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="confirm-cancel-bill" class="btn-danger"><i class="fa-solid fa-check"></i> Confirm Cancellation</button>
                        <button id="cancel-cancel-bill" class="btn-secondary"><i class="fa-solid fa-times"></i> Go Back</button>
                    </div>
                </div>
            `;

            // Create and show modal
            const modalContainer = document.createElement('div');
            modalContainer.className = 'modal';
            modalContainer.id = 'cancel-bill-modal';
            modalContainer.innerHTML = modalHTML;
            document.body.appendChild(modalContainer);

            // Show the modal
            modalContainer.style.display = 'block';

            // Add event listeners
            const closeBtn = modalContainer.querySelector('.close');
            const cancelBtn = document.getElementById('cancel-cancel-bill');
            const confirmBtn = document.getElementById('confirm-cancel-bill');

            closeBtn.addEventListener('click', () => {
                document.body.removeChild(modalContainer);
            });

            cancelBtn.addEventListener('click', () => {
                document.body.removeChild(modalContainer);
            });

            confirmBtn.addEventListener('click', () => {
                const reason = document.getElementById('cancel-reason').value.trim();

                if (!reason) {
                    // Show error if no reason provided
                    const reasonTextarea = document.getElementById('cancel-reason');
                    reasonTextarea.classList.add('error');
                    reasonTextarea.placeholder = 'Reason is required';
                    return;
                }

                // Process the cancellation
                processCancelBill(reason, totalAfterDiscount);
                document.body.removeChild(modalContainer);
            });
        }

        // Function to process bill cancellation
        function processCancelBill(reason, totalAmount) {
            // Show loading message
            const loadingAlert = showAlert({
                type: 'info',
                title: 'Processing',
                message: 'Cancelling bill, please wait...',
                showCancelButton: false,
                confirmButtonText: 'OK',
                autoClose: false
            });

            // Create cancel bill data
            const cancelData = {
                bill_id: currentBillId,
                hotel_type: document.getElementById('hotel-type').value,
                table_id: document.getElementById('table').value,
                total_before_discount: cart.reduce((sum, item) => sum + (item.price * item.quantity), 0),
                discount_amount: parseFloat(document.getElementById('discount-input').value) || 0,
                payment_amount: totalAmount,
                reason: reason,
                items: cart.map(item => ({
                    item_id: item.id,
                    quantity: item.quantity,
                    name: item.name,
                    price: item.price,

                })),
                customer_name: document.getElementById('customer-telephone').value
            };

            // Send request to server
            fetch('cancel_bill.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(cancelData)
                })
                .then(response => response.json())
                .then(data => {
                    closeAlert(loadingAlert);

                    if (data.success) {
                        showAlert({
                            type: 'success',
                            title: 'Success',
                            message: 'Bill cancelled successfully!',
                            autoClose: false,
                            autoCloseTime: 2000,
                            onConfirm: () => {
                                // Clear cart
                                cart.length = 0;
                                updateCart();
                                clearSavedSelections();
                                clearPersistantCart();
                                fetchNextBillId();

                                // Refresh tables list to reflect updated status
                                populateInitialTables();

                                // Also show a notification about table status
                                //showResetNotification(`Table ${cancelData.table_id} is now Available`, 'success');
                                setTimeout(() => {
                                    window.location.reload();
                                }, 100);
                            }
                        });
                        setTimeout(() => {
                            const handleEnter = function(event) {
                                if (event.key === 'Enter') {
                                    event.preventDefault();
                                    
                                    // Look for visible buttons with OK text
                                    const okButton = Array.from(document.querySelectorAll('button')).find(btn => {
                                        const isVisible = btn.offsetParent !== null;
                                        const hasOkText = btn.textContent.trim().toUpperCase().includes('OK');
                                        return isVisible && hasOkText;
                                    });
                                    
                                    if (okButton) {
                                        okButton.click();
                                    }
                                    
                                    document.removeEventListener('keydown', handleEnter);
                                }
                            };
                            document.addEventListener('keydown', handleEnter);
                        }, 100);

                        // Clear cart and fetch next bill ID
                        cart.length = 0;
                        updateCart();
                        fetchNextBillId();

                        // Refresh tables list to reflect updated status
                        populateInitialTables();
                    } else {
                        showAlert({
                            type: 'error',
                            title: 'Error',
                            message: data.message || 'Failed to cancel bill.',
                            confirmButtonText: 'Try Again'
                        });
                    }
                })
                .catch(error => {
                    closeAlert(loadingAlert);
                    console.error('Error cancelling bill:', error);
                    showAlert({
                        type: 'error',
                        title: 'Connection Error',
                        message: 'Unable to cancel bill. Please check your connection and try again.',
                        confirmButtonText: 'OK'
                    });
                });
        }

        // Function to handle hotel type change
        function handleHotelTypeChange(hotelTypeId) {
            // Remove any existing reference number container
            const existingContainer = document.getElementById('reference-number-container');
            if (existingContainer) {
                existingContainer.remove();

                // Reset menu-grid-container height when reference container is removed
                const menuGridContainer = document.querySelector('.menu-grid-container');
                if (menuGridContainer) {
                    menuGridContainer.style.maxHeight = '76vh';
                }
            }

            // Check if hotel type is Uber (4) or Pick Me (6)
            if (hotelTypeId === '4' || hotelTypeId === '6') {
                // Create reference number input container
                const container = document.createElement('div');
                container.id = 'reference-number-container';
                container.className = 'reference-number-container';
                const serviceName = hotelTypeId === '4' ? 'Uber' : 'Pick Me';
                container.innerHTML = `
                    <label for="reference-number">${serviceName} Reference Number:</label>
                    <input type="text" id="reference-number" placeholder="Enter ${serviceName} reference" required>
                `;
                // Insert after hotel-table-row
                const hotelTableRow = document.querySelector('.hotel-table-row');
                hotelTableRow.parentNode.insertBefore(container, hotelTableRow.nextSibling);

                // Adjust menu-grid-container height when reference container is added
                const menuGridContainer = document.querySelector('.menu-grid-container');
                if (menuGridContainer) {
                    menuGridContainer.style.maxHeight = '67vh';
                }
            }
        }

        // Update processCompleteBill function to include reference number
        function updateBillDataWithReference(billData) {
            // Check if hotel type is Uber or Pick Me
            const hotelTypeId = document.getElementById('hotel-type').value;

            if (hotelTypeId === '4' || hotelTypeId === '6') {
                const referenceNumber = document.getElementById('ref-number').value.trim() || document.getElementById('reference-number').value.trim();

                // Validate reference number
                if (!referenceNumber) {
                    showAlert({
                        type: 'error',
                        title: 'Missing Reference',
                        message: 'Please enter a reference number for the delivery service.',
                        confirmButtonText: 'OK'
                    });
                    return null; // Return null to indicate validation failed
                }

                // Add reference number to bill data
                billData.reference_number = referenceNumber;
            }

            return billData; // Return updated bill data
        }




        // New Customer Modal functions
        function openAddCustomerModal() {
            document.getElementById('new-customer-modal').style.display = 'block';
            document.getElementById('new-customer-name').focus();
            // Reset form
            document.getElementById('new-customer-form').reset();
            clearNewCustomerErrors();
        }

        function closeAddCustomerModal() {
            document.getElementById('new-customer-modal').style.display = 'none';
        }

        window.addEventListener('click', function(event) {
            const modal = document.getElementById('new-customer-modal');
            if (event.target === modal) {
                closeAddCustomerModal();
            }
        });

        document.getElementById('new-customer-form').addEventListener('submit', function(e) {
            e.preventDefault();

            if (validateNewCustomerForm()) {
                submitNewCustomerForm();
            }
        });

        function validateNewCustomerForm() {
            let isValid = true;
            clearNewCustomerErrors();

            // Validate name
            const name = document.getElementById('new-customer-name').value.trim();
            if (name === '') {
                displayNewCustomerError('new-customer-name-error', 'Name is required');
                document.getElementById('new-customer-name').classList.add('new-customer-shake');
                isValid = false;
            }

            // Validate phone
            const phone = document.getElementById('new-customer-phone').value.trim();
            if (phone === '') {
                displayNewCustomerError('new-customer-phone-error', 'Phone number is required');
                document.getElementById('new-customer-phone').classList.add('new-customer-shake');
                isValid = false;
            } else if (!isValidNewCustomerPhone(phone)) {
                displayNewCustomerError('new-customer-phone-error', 'Please enter a valid phone number');
                document.getElementById('new-customer-phone').classList.add('new-customer-shake');
                isValid = false;
            }

            return isValid;
        }

        function isValidNewCustomerPhone(phone) {
            return /^\+?[0-9]{10,15}$/.test(phone);
        }

        function displayNewCustomerError(elementId, message) {
            document.getElementById(elementId).textContent = message;
        }

        function clearNewCustomerErrors() {
            const errorElements = document.querySelectorAll('.new-customer-error-message');
            errorElements.forEach(element => {
                element.textContent = '';
            });


            document.querySelectorAll('.new-customer-shake').forEach(element => {
                element.classList.remove('new-customer-shake');
            });
        }

        document.querySelectorAll('#new-customer-form input').forEach(input => {
            input.addEventListener('animationend', function() {
                this.classList.remove('new-customer-shake');
            });
        });

        function submitNewCustomerForm() {
            const formData = new FormData(document.getElementById('new-customer-form'));

            // Show loading state on button
            const saveBtn = document.querySelector('.new-customer-save-btn');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
            saveBtn.disabled = true;

            fetch('add_customer.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    saveBtn.innerHTML = originalText;
                    saveBtn.disabled = false;

                    if (data.success) {
                        // Handle success
                        showNewCustomerSuccessMessage(data.message);


                        // if (data.customer) {
                        //     selectNewCustomer(data.customer);
                        // }


                        setTimeout(() => {
                            closeAddCustomerModal();
                        }, 1500);
                    } else {
                        // Handle error
                        if (data.errors) {
                            // Display specific errors
                            Object.keys(data.errors).forEach(field => {
                                displayNewCustomerError(`new-customer-${field}-error`, data.errors[field]);
                                document.getElementById(`new-customer-${field}`).classList.add('new-customer-shake');
                            });
                        } else {
                            // General error
                            alert(data.message || 'Something went wrong. Please try again.');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    saveBtn.innerHTML = originalText;
                    saveBtn.disabled = false;
                    alert('An error occurred. Please try again.');
                });
        }

        function showNewCustomerSuccessMessage(message) {
            let successMsg = document.querySelector('.new-customer-success-message');
            if (!successMsg) {
                successMsg = document.createElement('div');
                successMsg.className = 'new-customer-success-message';
                const form = document.getElementById('new-customer-form');
                form.parentNode.insertBefore(successMsg, form);
            }

            successMsg.textContent = message || 'Customer added successfully!';
            successMsg.style.display = 'block';

            // Hide after 3 seconds
            setTimeout(() => {
                successMsg.style.display = 'none';
            }, 3000);
        }

        function selectNewCustomer(customer) {
            // Update the customer-telephone input with the new customer's phone
            document.getElementById('customer-telephone').value = customer.phone_number;

            // Update the hidden customer ID holder
            document.getElementById('customer-id-holder').value = customer.customer_id;

            // Clear any existing suggestions
            document.getElementById('customer-suggestions').innerHTML = '';
        }



        document.addEventListener('DOMContentLoaded', function() {
            // Populate hotel types
            // populateHotelTypesForSelector();
            
            // Get hotel selector element
            const hotelSelector = document.getElementById('hotel-selector');
            
            // Add change event listener to handle Uber/Pickme selection
            hotelSelector.addEventListener('change', function() {
                const selectedValue = this.value;
                
                // Remove any existing reference input
                const existingRefInput = document.getElementById('reference-input-container');
                if (existingRefInput) {
                    existingRefInput.remove();
                }
                
                // Check if Uber (id: 4) or Pickme (id: 6) is selected
                if (selectedValue === '4' || selectedValue === '6') {
                    const serviceName = selectedValue === '4' ? 'Uber' : 'Pickme';
                    
                    // Create reference number input
                    const refContainer = document.createElement('div');
                    refContainer.id = 'reference-input-container';
                    refContainer.style.marginTop = '10px';
                    refContainer.style.marginBottom = '15px';
                    
                    refContainer.innerHTML = `
                        <label for="ref-number">${serviceName} Reference Number:</label>
                        <input type="text" id="ref-number" style="display:block; width:100%; padding:8px; margin-top:5px; border:1px solid #ccc; border-radius:4px;" placeholder="Enter ${serviceName} reference number" required>
                    `;
                    
                    // Insert after hotel selector
                    hotelSelector.parentNode.insertBefore(refContainer, hotelSelector.nextSibling);
                }
            });
        });



        document.addEventListener('DOMContentLoaded', function() {
        // Get the modal
        const tableMapModal = document.getElementById('tableMapModal');
        
        // Get the button that opens the modal
        const tableMapBtn = document.getElementById('viewTableMapBtn');
        
        // Get the <span> element that closes the modal
        const tableMapCloseBtn = document.querySelector('.tableMap-close');
        
        // Get the container where tables will be displayed
        const tableMapContainer = document.getElementById('tableMapContainer');
        
        // When the user clicks the button, open the modal and load tables
        tableMapBtn.onclick = function() {
            tableMapModal.style.display = "flex"; // Use flex to center the modal
            tableMapLoadTables();
            
            // Add class to body to prevent scrolling of background content
            document.body.style.overflow = "hidden";
        }
        
        // When the user clicks on <span> (x), close the modal
        tableMapCloseBtn.onclick = function() {
            tableMapModal.style.display = "none";
            
            // Allow scrolling again
            document.body.style.overflow = "";
        }
        
        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == tableMapModal) {
            tableMapModal.style.display = "none";
            
            // Allow scrolling again
            document.body.style.overflow = "";
            }
        }
        
        // Function to load tables via AJAX
        function tableMapLoadTables() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_tables.php', true);
            
            xhr.onload = function() {
            if (this.status === 200) {
                try {
                const tables = JSON.parse(this.responseText);
                tableMapRenderTables(tables);
                } catch (e) {
                console.error('Error parsing JSON:', e);
                tableMapContainer.innerHTML = '<p>Error loading tables. Please try again.</p>';
                }
            } else {
                tableMapContainer.innerHTML = '<p>Error loading tables. Please try again.</p>';
            }
            };
            
            xhr.onerror = function() {
            tableMapContainer.innerHTML = '<p>Network error occurred. Please try again.</p>';
            };
            
            xhr.send();
        }
        
        // Function to render tables in the modal
        function tableMapRenderTables(tables) {
            tableMapContainer.innerHTML = '';
            
            tables.forEach(function(table) {
            // Create table box
            const tableBox = document.createElement('div');
            tableBox.className = 'tableMap-box';
            
            // Set status class
            if (table.status === 'available') {
                tableBox.classList.add('tableMap-available');
            } else if (table.status === 'occupied') {
                tableBox.classList.add('tableMap-occupied');
            } else {
                tableBox.classList.add('tableMap-dirty');
            }
            
            // Create table element
            const tableElement = document.createElement('div');
            tableElement.className = 'tableMap-table';
            tableElement.innerHTML = `
                Table ${table.table_id}<br>
                ${table.capacity} Pax
            `;
            
            // Create capacity dots container
            const capacityContainer = document.createElement('div');
            capacityContainer.className = 'tableMap-capacity';
            
            // Add dots based on capacity
            for (let i = 0; i < table.capacity; i++) {
                const dot = document.createElement('span');
                dot.className = 'tableMap-capacity-dot';
                capacityContainer.appendChild(dot);
            }
            
            // Add table and capacity to the box
            tableBox.appendChild(tableElement);
            tableBox.appendChild(capacityContainer);
            
            // Add click event to select table
            tableBox.addEventListener('click', function() {
                tableMapSelectTable(table.table_id);
            });
            
            // Add to container
            tableMapContainer.appendChild(tableBox);
            });
        }
        
        // Function to handle table selection
        function tableMapSelectTable(tableId) {
            // You can implement what happens when a table is selected
            console.log('Table ' + tableId + ' selected');
            // For example, you could close the modal and set the selected table
            // tableMapModal.style.display = "none";
            // document.getElementById('selectedTable').value = tableId;
        }
        });
    </script>
</body>

</html>