<?php
session_start();
include '../inc/dashHeader.php';

$user_name = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Report Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="scripts.js"></script>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap");

        :root {
            /* Light Theme Colors */
            --light-bg: #f8f9fa;
            --light-card-bg: #ffffff;
            --light-text: #212529;
            --light-border: #dee2e6;
            --light-primary: #343a40;
            --light-primary-hover: #212529;
            --light-secondary: #6c757d;
            --light-accent: #007bff;

            /* Dark Theme Colors */
            --dark-bg: #212529;
            --dark-card-bg: #343a40;
            --dark-text: #f8f9fa;
            --dark-border: #495057;
            --dark-primary: #6c757d;
            --dark-primary-hover: #adb5bd;
            --dark-secondary: #ced4da;
            --dark-accent: #0d6efd;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background-color: var(--light-bg);
            color: var(--light-text);
            transition: background-color 0.3s, color 0.3s;
        }

        body.dark-theme {
            background-color: var(--dark-bg);
            color: var(--dark-text);
        }

        .wrapper {
            width: 100%;
            padding-left: 20px;
            padding-right: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .report-page-header {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--light-primary);
            margin: 20px 0;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--light-border);
        }

        .dark-theme .report-page-header {
            color: var(--dark-text);
            border-bottom-color: var(--dark-border);
        }

        .rep-generation-placeholders {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px 0;
        }

        .report-btn-container {
            background-color: var(--light-card-bg);
            border: 1px solid var(--light-border);
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 20px;
            display: flex;
            flex-direction: column;
            height: auto;
            min-height: 300px;
            transition: box-shadow 0.3s, transform 0.3s;
        }

        .report-btn-container:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .dark-theme .report-btn-container {
            background-color: var(--dark-card-bg);
            border-color: var(--dark-border);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .report-heading-cont {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid var(--light-border);
            padding-bottom: 10px;
        }

        .dark-theme .report-heading-cont {
            border-bottom-color: var(--dark-border);
        }

        .report-placeholder-icon {
            margin-right: 10px;
        }

        .report-placeholder-icon i {
            font-size: 1.5rem;
            color: var(--light-primary);
        }

        .dark-theme .report-placeholder-icon i {
            color: var(--dark-secondary);
        }

        .report-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--light-primary);
        }

        .dark-theme .report-name {
            color: var(--dark-text);
        }

        .report-content {
            flex-grow: 1;
            font-size: 0.9rem;
            color: var(--light-secondary);
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .dark-theme .report-content {
            color: var(--dark-secondary);
        }

        input[type="text"],
        input[type="date"],
        select {
            width: 100%;
            padding: 8px 12px;
            margin-bottom: 10px;
            border: 1px solid var(--light-border);
            border-radius: 4px;
            background-color: var(--light-card-bg);
            color: var(--light-text);
            font-size: 0.9rem;
        }

        .dark-theme input[type="text"],
        .dark-theme input[type="date"],
        .dark-theme select {
            background-color: var(--dark-bg);
            border-color: var(--dark-border);
            color: var(--dark-text);
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        select:focus {
            outline: none;
            border-color: var(--light-accent);
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }

        .dark-theme input[type="text"]:focus,
        .dark-theme input[type="date"]:focus,
        .dark-theme select:focus {
            border-color: var(--dark-accent);
            box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.25);
        }

        .rep_generate_btn {
            display: inline-block;
            font-weight: 500;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            margin-top: 2px;
            font-size: 0.9rem;
            line-height: 1.5;
            border-radius: 0.25rem;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            color: #fff;
            background-color: var(--light-primary);
            border-color: var(--light-primary);
            cursor: pointer;
            width: 100%;
        }

        .rep_generate_btn:hover {
            background-color: var(--light-primary-hover);
            border-color: var(--light-primary-hover);
        }

        .dark-theme .rep_generate_btn {
            background-color: var(--dark-primary);
            border-color: var(--dark-primary);
            color: var(--dark-bg);
        }

        .dark-theme .rep_generate_btn:hover {
            background-color: var(--dark-primary-hover);
            border-color: var(--dark-primary-hover);
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: var(--light-text);
            border-collapse: collapse;
        }

        .table-bordered {
            border: 1px solid var(--light-border);
        }

        .dark-theme .table {
            color: var(--dark-text);
        }

        .dark-theme .table-bordered {
            border-color: var(--dark-border);
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid var(--light-border);
        }

        .dark-theme .table th,
        .dark-theme .table td {
            border-top-color: var(--dark-border);
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid var(--light-border);
        }

        .dark-theme .table-bordered th,
        .dark-theme .table-bordered td {
            border-color: var(--dark-border);
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid var(--light-border);
            background-color: var(--light-primary);
            color: #fff;
        }

        .dark-theme .table thead th {
            border-bottom-color: var(--dark-border);
            background-color: var(--dark-primary);
            color: var(--dark-bg);
        }

        .modal-dialog {
            max-width: 90%;
            width: auto;
        }

        .modal-content {
            max-height: 80vh;
            overflow-y: auto;
        }

        @media (max-width: 768px) {
            .wrapper {
                padding-left: 15px;
                padding-right: 15px;
            }

            .rep-generation-placeholders {
                grid-template-columns: 1fr;
            }

            .report-btn-container {
                width: 100%;
            }
        }

        .day-end-rep-btn{
                padding: 5px 10px;
                background-color: #212529;
                color: white;
                box-shadow: 4px 4px 1px #6c757d;
            }
    </style>
</head>

<body>
    <div class="wrapper">
        <h1 class="report-page-header">Report Export Page</h1>

        <button class="day-end-rep-btn" onclick="window.location.href = '../newPOS/custom_day_end_report.php'">Day End Reports</button>

        <div class="rep-generation-placeholders">
            <!-- Understocked Items -->
            <!-- <div class="report-btn-container">
                <div class="report-heading-cont">
                    <span class="report-placeholder-icon"><i class="fa-solid fa-box-open"></i></span>
                    <span class="report-name">Understocked Items</span>
                </div>
                <p class="report-content">Generate a report about the items which are currently in negative stock.</p>
                <button class="rep_generate_btn" onclick="generateUnderstockedItemsReport()">Generate</button>
            </div> -->


            <!-- Total Inventory Report Section -->
            <div class="report-btn-container">
                <div class="report-heading-cont">
                    <span class="report-name">Total Stock Inventory Report</span>
                </div>

                <select id="sales-category">
                    <option value="">All Categories</option>
                </select>
                <input type="text" id="search" placeholder="Search by Product Name or Barcode">
                <input type="date" id="start-date">
                <input type="date" id="end-date">
                <p class="report-content">Generate a report of total Inventory.</p>
                <button class="rep_generate_btn" onclick="fetchInventoryReport()">Run Report</button>
                <button class="rep_generate_btn" onclick="printInventoryReport()">Print Report</button>
            </div>

            <!-- Total Stock Report -->
            <div class="report-btn-container">
                <div class="report-heading-cont">
                    <span class="report-name">Total Stock Report</span>
                </div>
                <select id="supplier">
                    <option value="">Select Supplier</option>
                </select>
                <input type="text" id="search" placeholder="Search by Product Name or Barcode">
                <input type="date" id="start-date">
                <input type="date" id="end-date">
                <p class="report-content">Generate a report of total stock.</p>
                <button class="rep_generate_btn" onclick="fetchTotalStockReport()">Run Report</button>
                <button class="rep_generate_btn" onclick="printTotalStockReport()">Print Report</button>
            </div>
            <!-- Damage Inventory Report Section -->
            <div class="report-btn-container">
                <div class="report-heading-cont">
                    <span class="report-name">Damage Inventory Report</span>
                </div>
                <select id="damage-category">
                    <option value="">All Categories</option>
                </select>
                <input type="text" id="damage-search" placeholder="Search by Product Name">
                <input type="date" id="damage-start-date">
                <input type="date" id="damage-end-date">
                <p class="report-content">Generate a report of damaged inventory items.</p>
                <button class="rep_generate_btn" onclick="fetchDamageInventoryReport()">Run Report</button>
                <button class="rep_generate_btn" onclick="printDamageInventoryReport()">Print Report</button>
            </div>
            <!-- Assets Report Section -->
            <div class="report-btn-container">
                <div class="report-heading-cont">
                    <span class="report-name">Assets Report</span>
                </div>
                <input type="text" id="asset-search" placeholder="Search by Asset Name">
                <input type="date" id="asset-start-date">
                <input type="date" id="asset-end-date">
                <p class="report-content">Generate a report of assets.</p>
                <button class="rep_generate_btn" onclick="fetchAssetsReport()">Run Report</button>
                <button class="rep_generate_btn" onclick="printAssetsReport()">Print Report</button>
            </div>

            <!-- Damage Assets Report Section -->
            <div class="report-btn-container">
                <div class="report-heading-cont">
                    <span class="report-name">Damage Assets Report</span>
                </div>
                <input type="text" id="damage-asset-search" placeholder="Search by Asset Name">
                <input type="date" id="damage-asset-start-date">
                <input type="date" id="damage-asset-end-date">
                <p class="report-content">Generate a report of damaged assets.</p>
                <button class="rep_generate_btn" onclick="fetchDamageAssetsReport()">Run Report</button>
                <button class="rep_generate_btn" onclick="printDamageAssetsReport()">Print Report</button>
            </div>

            <div class="modal fade" id="reportModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="reportModalLabel">Report</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h4 id="reportTitle" class="text-center"></h4>
                            <table class="table table-bordered mt-3">
                                <thead>
                                    <tr id="reportTableHead"></tr>
                                </thead>
                                <tbody id="reportTableBody"></tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="report-btn-container">
                <div class="report-heading-cont">
                    <span class="report-name">Sales Report (Invoice Wise)</span>
                </div>
                <select id="users-select-product">
                    <option value="">Select User</option>
                    <!-- Populate dynamically via PHP or JavaScript -->
                </select>
                <input type="date" id="sales-start-date">
                <input type="date" id="sales-end-date">
                <p class="report-content">Generate a report of sales items.</p>
                <button class="rep_generate_btn" onclick="fetchSalesReport()">Run Report</button>
                <button class="rep_generate_btn" onclick="printSalesReport()">Print Report</button>
            </div>
            
            <!-- Product-wise Sales Report -->
            <div class="report-btn-container">
                <div class="report-heading-cont">
                    <span class="report-name">Sales Report (Product Wise)</span>
                </div>
                <!-- <select id="product-category-select">
                    <option value="">All Categories</option>
                </select> -->
                <input type="text" id="product-search" placeholder="Search by Product Name">
                <input type="date" id="product-sales-start-date">
                <input type="date" id="product-sales-end-date">
                <p class="report-content">Generate a detailed report of individual product sales.</p>
                <button class="rep_generate_btn" onclick="fetchProductSalesReport()">Run Report</button>
                <button class="rep_generate_btn" onclick="printProductSalesReport()">Print Report</button>
            </div>
                   <!-- Product-wise bakery Sales Report -->
                   <div class="report-btn-container">
                <div class="report-heading-cont">
                    <span class="report-name">Sales Item Report (Product Wise)</span>
                </div>
                <!-- <select id="product-category-select">
                    <option value="">All Categories</option>
                </select> -->
                <input type="text" id="bakery-product-search" placeholder="Search by Product Name">
                <input type="date" id="bakery-sales-start-date">
                <input type="date" id="bakery-sales-end-date">
                <p class="report-content">Generate a detailed report of individual bakery product sales.</p>
                <button class="rep_generate_btn" onclick="fetchBakeryProductSalesReport()">Run Report</button>
                <button class="rep_generate_btn" onclick="printBakeryProductSalesReport()">Print Report</button>
            </div>

            <div class="report-btn-container">
                <div class="report-heading-cont">
                    <span class="report-name">Sales Payment Method</span>
                </div>
                <select id="payment-select">
                    <option value="">All Payment Methods</option>
                    <option value="card">Card</option>
                    <option value="cash">Cash</option>
                    <option value="bank">Bank</option>
                    <option value="cre">Credit</option>
                    <option value="credit">Credit Card</option>
                    <option value="debit">Debit Card</option>
                </select>
                <input type="date" id="payment-start-date">
                <input type="date" id="payment-end-date">
                <p class="report-content">Generate a detailed report of sales by payment method.</p>
                <button class="rep_generate_btn" onclick="fetchPaymentSalesReport()">Run Report</button>
                <button class="rep_generate_btn" onclick="printPaymentSalesReport()">Print Report</button>
            </div>

            <!-- Menu Sales Report Section -->
            <div class="report-btn-container">
                <div class="report-heading-cont">
                    <span class="report-name">Menu Sales Report</span>
                </div>
                <input type="text" id="Menu-product-search" placeholder="Search by Product Name">
                <input type="date" id="Menu-sales-start-date">
                <input type="date" id="Menu-sales-end-date">
                <p class="report-content">Generate a detailed report of menu product sales.</p>
                <button class="rep_generate_btn" onclick="fetchMenuProductSalesReport()">Run Report</button>
                <button class="rep_generate_btn" onclick="printMenuProductSalesReport()">Print Report</button>
            </div>

            <script>
                // JavaScript functions remain unchanged; they are included below for completeness.
                function fetchDamageAssetsReport() {
                    const search = document.getElementById("damage-asset-search").value;
                    const startDate = document.getElementById("damage-asset-start-date").value;
                    const endDate = document.getElementById("damage-asset-end-date").value;

                    const url = `../report/fetch_damage_assets_report.php?search=${encodeURIComponent(search)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(`HTTP error! Status: ${response.status}, Response: ${text}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Fetched damage assets data:', data);
                            $("#reportTitle, #reportModalLabel").text("Damage Assets Report");
                            let tableHeaders = `
                <th>Asset Name</th>
                <th>Damage Quantity</th>
                <th>Damage Description</th>
                <th>Damage Date</th>
                <th>Created At</th>
            `;
                            let tableBody = data.length === 0 ?
                                "<tr><td colspan='5'>No damaged assets found.</td></tr>" :
                                data.map(item => `
                    <tr>
                        <td>${item.asset_name}</td>
                        <td>${item.damage_qty}</td>
                        <td>${item.damage_description || 'N/A'}</td>
                        <td>${item.damage_date}</td>
                        <td>${item.created_at}</td>
                    </tr>
                `).join('');
                            $("#reportTableHead").html(tableHeaders);
                            $("#reportTableBody").html(tableBody);
                            $("#reportModal").modal("show");
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            $("#reportTableBody").html(`<tr><td colspan='5'>Error: ${error.message}</td></tr>`);
                            $("#reportModal").modal("show");
                        });
                }

                function printDamageAssetsReport() {
                    const search = document.getElementById("damage-asset-search").value;
                    const startDate = document.getElementById("damage-asset-start-date").value;
                    const endDate = document.getElementById("damage-asset-end-date").value;

                    const url = `../report/print_damage_assets_report.php?search=${encodeURIComponent(search)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

                    const printWindow = window.open(url, '_blank');
                    printWindow?.addEventListener('load', () => printWindow.print());
                }

                function fetchTotalStockReport() {
                    let supplier = document.getElementById("supplier").value.trim();
                    let searchQuery = document.getElementById("search").value.trim();
                    let startDate = document.getElementById("start-date").value;
                    let endDate = document.getElementById("end-date").value;

                    let url = `../report/fetch_total_stock.php?supplier=${encodeURIComponent(supplier)}&search=${encodeURIComponent(searchQuery)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(`HTTP error! Status: ${response.status}, Response: ${text}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            $("#reportTitle, #reportModalLabel").text("Total Stock Report");

                            let tableHeaders = `
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Cost Price</th>
                <th>Retail Price</th>
                <th>Category</th>
                <th>Supplier ID</th>
            `;

                            let tableBody = data.length === 0 ?
                                "<tr><td colspan='6'>No stock items found.</td></tr>" :
                                data.map(item => `
                    <tr>
                        <td>${item.item_name}</td>
                        <td>${item.quantity}</td>
                        <td>${item.cost_price}</td>
                        <td>${item.item_price}</td>
                        <td>${item.bakery_category}</td>
                        <td>${item.supplier_id}</td>
                    </tr>
                `).join('');

                            $("#reportTableHead").html(tableHeaders);
                            $("#reportTableBody").html(tableBody);
                            $("#reportModal").modal("show");
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            $("#reportTableBody").html(`<tr><td colspan='6'>Error: ${error.message}</td></tr>`);
                            $("#reportModal").modal("show");
                        });
                }

                function printTotalStockReport() {
                    let supplier = document.getElementById("supplier").value.trim();
                    let searchQuery = document.getElementById("search").value.trim();
                    let startDate = document.getElementById("start-date").value;
                    let endDate = document.getElementById("end-date").value;

                    let url = `../report/print_total_stock.php?supplier=${encodeURIComponent(supplier)}&search=${encodeURIComponent(searchQuery)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

                    let printWindow = window.open(url, '_blank');
                    printWindow?.addEventListener('load', () => printWindow.print());
                }

                function fetchSalesReport() {
                    const staffId = document.getElementById("users-select-product").value;
                    const startDate = document.getElementById("sales-start-date").value;
                    const endDate = document.getElementById("sales-end-date").value;

                    const url = `../report/fetch_sales_report.php?staff_id=${encodeURIComponent(staffId)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(`HTTP error! Status: ${response.status}, Response: ${text}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Fetched sales data:', data);
                            $("#reportTitle, #reportModalLabel").text("Sales Report (Invoice Wise)");
                            let tableHeaders = `
                <th>Bill ID</th>
          
                <th>Bill Time</th>
                <th>Payment Amount</th>
                <th>Paid Amount</th>
                <th>Balance Amount</th>
                <th>Total Before Discount</th>
                <th>Discount Amount</th>
            `;
                            let tableBody = data.length === 0 ?
                                "<tr><td colspan='9'>No sales invoices found.</td></tr>" :
                                data.map(item => `
                    <tr>
                        <td>${item.bill_id}</td>
                                      <td>${item.bill_time || 'N/A'}</td>
                        <td>${item.payment_amount || '0.00'}</td>
                        <td>${item.paid_amount || '0.00'}</td>
                        <td>${item.balance_amount || '0.00'}</td>
                        <td>${item.total_before_discount || '0.00'}</td>
                        <td>${item.discount_amount || '0.00'}</td>
                    </tr>
                `).join('');
                            $("#reportTableHead").html(tableHeaders);
                            $("#reportTableBody").html(tableBody);
                            $("#reportModal").modal("show");
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            $("#reportTableBody").html(`<tr><td colspan='9'>Error: ${error.message}</td></tr>`);
                            $("#reportModal").modal("show");
                        });
                }

                function printSalesReport() {
                    const staffId = document.getElementById("users-select-product").value;
                    const startDate = document.getElementById("sales-start-date").value;
                    const endDate = document.getElementById("sales-end-date").value;

                    const url = `../report/print_sales_report.php?staff_id=${encodeURIComponent(staffId)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

                    const printWindow = window.open(url, '_blank');
                    printWindow?.addEventListener('load', () => printWindow.print());
                }

                // Remove fetchCategories since it's no longer needed for Total Stock Report
                function fetchSuppliers() {
                    fetch("../report/get_suppliers.php")
                        .then(response => response.json())
                        .then(data => {
                            let supplierSelect = document.getElementById("supplier");
                            supplierSelect.innerHTML = '<option value="">All Supplier</option>';
                            data.forEach(supplier => {
                                supplierSelect.innerHTML += `<option value="${supplier.supplier_id}">${supplier.supplier_name}</option>`;
                            });
                        })
                        .catch(error => console.error("Error fetching suppliers:", error));
                }

                // document.addEventListener("DOMContentLoaded", function() {
                //     fetchSuppliers();
                // });

                function fetchInventoryReport() {
                    const category = document.getElementById("sales-category").value;
                    const search = document.getElementById("search").value;
                    const startDate = document.getElementById("start-date").value;
                    const endDate = document.getElementById("end-date").value;

                    const url = `../report/fetch_inventory_report.php?category=${encodeURIComponent(category)}&search=${encodeURIComponent(search)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(`HTTP error! Status: ${response.status}, Response: ${text}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Fetched data:', data);
                            $("#reportTitle, #reportModalLabel").text("Total Stock Inventory Report");
                            let tableHeaders = `
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Measure</th>
                <th>Value</th>
                <th>Manufacture Date</th>
                <th>Expire Date</th>
                <th>Wastage</th>
                <th>Category</th>
            `;
                            let tableBody = data.length === 0 ?
                                "<tr><td colspan='8'>No inventory items found.</td></tr>" :
                                data.map(item => `
                    <tr>
                        <td>${item.item_name}</td>
                        <td>${item.quantity}</td>
                        <td>${item.measure || 'N/A'}</td>
                        <td>${item.value || 'N/A'}</td>
                        <td>${item.manufacturedate || 'N/A'}</td>
                        <td>${item.expire_date || 'N/A'}</td>
                        <td>${item.wastage}</td>
                        <td>${item.category || 'N/A'}</td>
                    </tr>
                `).join('');
                            $("#reportTableHead").html(tableHeaders);
                            $("#reportTableBody").html(tableBody);
                            $("#reportModal").modal("show");
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            $("#reportTableBody").html(`<tr><td colspan='8'>Error: ${error.message}</td></tr>`);
                            $("#reportModal").modal("show");
                        });
                }

                function printInventoryReport() {
                    const category = document.getElementById("sales-category").value;
                    const search = document.getElementById("search").value;
                    const startDate = document.getElementById("start-date").value;
                    const endDate = document.getElementById("end-date").value;

                    const url = `../report/print_inventory_report.php?category=${encodeURIComponent(category)}&search=${encodeURIComponent(search)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

                    const printWindow = window.open(url, '_blank');
                    printWindow?.addEventListener('load', () => printWindow.print());
                }
                // Initialization
                document.addEventListener("DOMContentLoaded", function() {
                    // Fetch suppliers
                    fetch("../report/get_suppliers.php")
                        .then(response => response.json())
                        .then(data => {
                            const supplierSelect = document.getElementById("supplier");
                            supplierSelect.innerHTML = '<option value="">All Suppliers</option>';
                            data.forEach(supplier => {
                                supplierSelect.innerHTML += `<option value="${supplier.supplier_id}">${supplier.supplier_name}</option>`;
                            });
                        });

                    // Fetch categories
                    fetch("../panel/get_categories.php")
                        .then(response => response.json())
                        .then(data => {
                            const categorySelect = document.getElementById("sales-category");
                            categorySelect.innerHTML = '<option value="">All Categories</option>';
                            data.forEach(category => {
                                categorySelect.innerHTML += `<option value="${category}">${category}</option>`;
                            });
                        });
                });

                function fetchDamageInventoryReport() {
                    const category = document.getElementById("damage-category").value;
                    const search = document.getElementById("damage-search").value;
                    const startDate = document.getElementById("damage-start-date").value;
                    const endDate = document.getElementById("damage-end-date").value;

                    const url = `../report/fetch_damage_inventory_report.php?category=${encodeURIComponent(category)}&search=${encodeURIComponent(search)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(`HTTP error! Status: ${response.status}, Response: ${text}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Fetched damage data:', data);
                            $("#reportTitle, #reportModalLabel").text("Damage Inventory Report");
                            let tableHeaders = `
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Measure</th>
                <th>Value</th>
                <th>Manufacture Date</th>
                <th>Expire Date</th>
                <th>Damage Date</th>
                <th>Category</th>
            `;
                            let tableBody = data.length === 0 ?
                                "<tr><td colspan='8'>No damaged inventory items found.</td></tr>" :
                                data.map(item => `
                    <tr>
                        <td>${item.item_name}</td>
                        <td>${item.quantity}</td>
                        <td>${item.measure || 'N/A'}</td>
                        <td>${item.value || 'N/A'}</td>
                        <td>${item.manufacturedate || 'N/A'}</td>
                        <td>${item.expire_date || 'N/A'}</td>
                        <td>${item.damage_date}</td>
                        <td>${item.category || 'N/A'}</td>
                    </tr>
                `).join('');
                            $("#reportTableHead").html(tableHeaders);
                            $("#reportTableBody").html(tableBody);
                            $("#reportModal").modal("show");
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            $("#reportTableBody").html(`<tr><td colspan='8'>Error: ${error.message}</td></tr>`);
                            $("#reportModal").modal("show");
                        });
                }

                function printDamageInventoryReport() {
                    const category = document.getElementById("damage-category").value;
                    const search = document.getElementById("damage-search").value;
                    const startDate = document.getElementById("damage-start-date").value;
                    const endDate = document.getElementById("damage-end-date").value;

                    const url = `../report/print_damage_inventory_report.php?category=${encodeURIComponent(category)}&search=${encodeURIComponent(search)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

                    const printWindow = window.open(url, '_blank');
                    printWindow?.addEventListener('load', () => printWindow.print());
                }

                function fetchAssetsReport() {
                    const search = document.getElementById("asset-search").value;
                    const startDate = document.getElementById("asset-start-date").value;
                    const endDate = document.getElementById("asset-end-date").value;

                    const url = `../report/fetch_assets_report.php?search=${encodeURIComponent(search)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(`HTTP error! Status: ${response.status}, Response: ${text}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Fetched assets data:', data);
                            $("#reportTitle, #reportModalLabel").text("Assets Report");
                            let tableHeaders = `
                <th>Asset Name</th>
                <th>Quantity</th>
                <th>Description</th>
                <th>Enter Date</th>
                <th>Created At</th>
            `;
                            let tableBody = data.length === 0 ?
                                "<tr><td colspan='5'>No assets found.</td></tr>" :
                                data.map(item => `
                    <tr>
                        <td>${item.asset_name}</td>
                        <td>${item.quantity}</td>
                        <td>${item.description || 'N/A'}</td>
                        <td>${item.enter_date}</td>
                        <td>${item.created_at}</td>
                    </tr>
                `).join('');
                            $("#reportTableHead").html(tableHeaders);
                            $("#reportTableBody").html(tableBody);
                            $("#reportModal").modal("show");
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            $("#reportTableBody").html(`<tr><td colspan='5'>Error: ${error.message}</td></tr>`);
                            $("#reportModal").modal("show");
                        });
                }

                function printAssetsReport() {
                    const search = document.getElementById("asset-search").value;
                    const startDate = document.getElementById("asset-start-date").value;
                    const endDate = document.getElementById("asset-end-date").value;

                    const url = `../report/print_assets_report.php?search=${encodeURIComponent(search)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

                    const printWindow = window.open(url, '_blank');
                    printWindow?.addEventListener('load', () => printWindow.print());
                }

                function fetchProductSalesReport() {
                    const categoryElement = document.getElementById("product-category-select");
                    const category = categoryElement ? categoryElement.value : "";
                    const search = document.getElementById("product-search").value;
                    const startDate = document.getElementById("product-sales-start-date").value;
                    const endDate = document.getElementById("product-sales-end-date").value;

                    const url = `../report/fetch_product_sales_report.php?&search=${encodeURIComponent(search)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(`HTTP error! Status: ${response.status}, Response: ${text}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Fetched product sales data:', data);
                            $("#reportTitle, #reportModalLabel").text("Sales Report (Product Wise)");
                            let tableHeaders = `
                                <th>Item ID</th>
                                <th>Product Name</th>
                                <th>Quantity Sold</th>
                                <th>Unit Price</th>
                                <th>Total Sales</th>
                                <th>Portion Size</th>
                            `;
                            let tableBody = data.length === 0 ?
                                "<tr><td colspan='6'>No product sales found.</td></tr>" :
                                data.map(item => `
                                    <tr>
                                        <td>${item.item_id || 'N/A'}</td>
                                        <td>${item.product_name || 'N/A'}</td>
                                        <td>${item.total_quantity || '0'}</td>
                                        <td>Rs. ${parseFloat(item.price).toFixed(2) || '0.00'}</td>
                                        <td>Rs. ${parseFloat(item.total_sales).toFixed(2) || '0.00'}</td>
                                        <td>${item.portion_size || 'N/A'}</td>
                                    </tr>
                                `).join('');
                            $("#reportTableHead").html(tableHeaders);
                            $("#reportTableBody").html(tableBody);
                            $("#reportModal").modal("show");
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            $("#reportTableBody").html(`<tr><td colspan='6'>Error: ${error.message}</td></tr>`);
                            $("#reportModal").modal("show");
                        });
                }

                function printProductSalesReport() {
                    const categoryElement = document.getElementById("product-category-select");
                    const category = categoryElement ? categoryElement.value : "";
                    const search = document.getElementById("product-search").value;
                    const startDate = document.getElementById("product-sales-start-date").value;
                    const endDate = document.getElementById("product-sales-end-date").value;

                    const url = `../report/print_product_sales_report.php?category=${encodeURIComponent(category)}&search=${encodeURIComponent(search)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

                    const printWindow = window.open(url, '_blank');
                    printWindow?.addEventListener('load', () => printWindow.print());
                }

                function fetchBakeryProductSalesReport() {
                    const search = document.getElementById("bakery-product-search").value;
                    const startDate = document.getElementById("bakery-sales-start-date").value;
                    const endDate = document.getElementById("bakery-sales-end-date").value;

                    const url = `../report/fetch_bakery_sales_report.php?search=${encodeURIComponent(search)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

                    // Display loading message
                    $("#reportTitle, #reportModalLabel").text("Sales Bakery Report (Product Wise)");
                    $("#reportTableBody").html("<tr><td colspan='9'>Loading data...</td></tr>");
                    $("#reportModal").modal("show");

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    console.error('Server response:', text);
                                    throw new Error(`HTTP error! Status: ${response.status}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Fetched bakery sales data:', data);
                            
                            if (data.error) {
                                throw new Error(data.error);
                            }
                            
                            // Access the items array from the response
                            const items = data.items || [];
                            const summary = data.summary || {};
                            
                            let tableHeaders = `
                                <th>Item ID</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Unit Price</th>
                                <th>Cost Price</th>
                                <th>Quantity Sold</th>
                                <th>Total Sales</th>
                                <th>Total Cost</th>
                                <th>Profit</th>
                            `;
                            
                            let tableBody = items.length === 0 ?
                                "<tr><td colspan='9'>No bakery product sales found.</td></tr>" :
                                items.map(item => `
                                    <tr>
                                        <td>${item.item_id || 'N/A'}</td>
                                        <td>${item.item_name || 'N/A'}</td>
                                        <td>${item.bakery_category || 'N/A'}</td>
                                        <td>Rs. ${parseFloat(item.item_price).toFixed(2) || '0.00'}</td>
                                        <td>Rs. ${parseFloat(item.cost_price).toFixed(2) || '0.00'}</td>
                                        <td>${item.total_quantity || '0'}</td>
                                        <td>Rs. ${parseFloat(item.total_sales).toFixed(2) || '0.00'}</td>
                                        <td>Rs. ${parseFloat(item.total_cost).toFixed(2) || '0.00'}</td>
                                        <td class="${parseFloat(item.profit) >= 0 ? 'text-success' : 'text-danger'}">
                                            Rs. ${parseFloat(item.profit).toFixed(2) || '0.00'}
                                        </td>
                                    </tr>
                                `).join('');
                                
                            // Add summary row if items exist
                            if (items.length > 0) {
                                tableBody += `
                                    <tr class="table-active font-weight-bold">
                                        <td colspan="5" style="text-align: right;">Grand Totals:</td>
                                        <td>${summary.totalQuantity || 0}</td>
                                        <td>Rs. ${parseFloat(summary.totalSales).toFixed(2) || '0.00'}</td>
                                        <td>Rs. ${parseFloat(summary.totalCost).toFixed(2) || '0.00'}</td>
                                        <td class="${parseFloat(summary.totalProfit) >= 0 ? 'text-success' : 'text-danger'}">
                                            Rs. ${parseFloat(summary.totalProfit).toFixed(2) || '0.00'}
                                        </td>
                                    </tr>
                                `;
                            }
                            
                            $("#reportTableHead").html(tableHeaders);
                            $("#reportTableBody").html(tableBody);
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            $("#reportTableBody").html(`<tr><td colspan='9'>Error: ${error.message}</td></tr>`);
                        });
                }

                function printBakeryProductSalesReport() {
                    const search = document.getElementById("bakery-product-search").value;
                    const startDate = document.getElementById("bakery-sales-start-date").value;
                    const endDate = document.getElementById("bakery-sales-end-date").value;

                    const url = `../report/print_bakery_sales_report.php?search=${encodeURIComponent(search)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

                    const printWindow = window.open(url, '_blank');
                    printWindow?.addEventListener('load', () => printWindow.print());
                }

                document.addEventListener("DOMContentLoaded", function() {
                    fetchSuppliers();
                    fetchCategories();

                    // Fetch product categories for product-wise report
                    fetch("../panel/get_categories.php")
                        .then(response => response.json())
                        .then(data => {
                            const productCategorySelect = document.getElementById("product-category-select");
                            if (productCategorySelect) {
                                productCategorySelect.innerHTML = '<option value="">All Categories</option>';
                                data.forEach(category => {
                                    productCategorySelect.innerHTML += `<option value="${category}">${category}</option>`;
                                });
                            }
                        })
                        .catch(error => console.error("Error fetching product categories:", error));
                });

                function fetchPaymentSalesReport() {
                    const paymentMethod = document.getElementById("payment-select").value;
                    const startDate = document.getElementById("payment-start-date").value;
                    const endDate = document.getElementById("payment-end-date").value;

                    const url = `../report/fetch_payment_report.php?payment_method=${encodeURIComponent(paymentMethod)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

                    // Display loading message
                    $("#reportTitle, #reportModalLabel").text("Payment Method Sales Report");
                    $("#reportTableBody").html("<tr><td colspan='7'>Loading data...</td></tr>");
                    $("#reportModal").modal("show");

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(`HTTP error! Status: ${response.status}, Response: ${text}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Fetched payment data:', data);
                            
                            if (data.error) {
                                throw new Error(data.error);
                            }
                            
                            // Access the payments array from the response
                            const payments = data.payments || [];
                            const summary = data.summary || {};
                            
                            // Payment method display mapping
                            const methodLabels = {
                                'cash': 'Cash',
                                'card': 'Card',
                                'cre': 'Credit',
                                'credit': 'Credit Card',
                                'debit': 'Debit Card',
                                'bank': 'Bank Transfer'
                            };

                            let tableHeaders = `
                                <th>Bill ID</th>
                                <th>Customer</th>
                                <th>Payment Method</th>
                                <th>Amount</th>
                                <th>Card ID</th>
                                <th>Bill Time</th>
                                <th>Payment Time</th>
                            `;
                            
                            let tableBody = payments.length === 0 ?
                                "<tr><td colspan='7'>No payment records found.</td></tr>" :
                                payments.map(item => `
                                    <tr>
                                        <td>${item.bill_id || 'N/A'}</td>
                                        <td>${item.customer_name || 'N/A'}</td>
                                        <td>${methodLabels[item.payment_method] || item.payment_method}</td>
                                        <td>Rs. ${parseFloat(item.amount).toFixed(2)}</td>
                                        <td>${item.card_id !== 'N/A' ? item.card_id : 'N/A'}</td>
                                        <td>${new Date(item.bill_time).toLocaleString()}</td>
                                        <td>${new Date(item.created_at).toLocaleString()}</td>
                                    </tr>
                                `).join('');
                                
                            // Add summary row if payments exist
                            if (payments.length > 0) {
                                tableBody += `
                                    <tr class="table-active font-weight-bold">
                                        <td colspan="3" style="text-align: right;"><strong>Grand Total:</strong></td>
                                        <td colspan="4"><strong>Rs. ${parseFloat(summary.totalAmount).toFixed(2)}</strong></td>
                                    </tr>
                                `;
                            }
                            
                            $("#reportTableHead").html(tableHeaders);
                            $("#reportTableBody").html(tableBody);
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            $("#reportTableBody").html(`<tr><td colspan='7'>Error: ${error.message}</td></tr>`);
                        });
                }

                function printPaymentSalesReport() {
                    const paymentMethod = document.getElementById("payment-select").value;
                    const startDate = document.getElementById("payment-start-date").value;
                    const endDate = document.getElementById("payment-end-date").value;

                    const url = `../report/print_payment_report.php?payment_method=${encodeURIComponent(paymentMethod)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

                    const printWindow = window.open(url, '_blank');
                    printWindow?.addEventListener('load', () => printWindow.print());
                }

                function fetchMenuProductSalesReport() {
                    const search = document.getElementById("Menu-product-search").value;
                    const startDate = document.getElementById("Menu-sales-start-date").value;
                    const endDate = document.getElementById("Menu-sales-end-date").value;

                    const url = `../report/fetch_menu_sales_report.php?search=${encodeURIComponent(search)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

                    // Display loading message
                    $("#reportTitle, #reportModalLabel").text("Menu Sales Report");
                    $("#reportTableBody").html("<tr><td colspan='8'>Loading data...</td></tr>");
                    $("#reportModal").modal("show");

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    console.error('Server response:', text);
                                    throw new Error(`HTTP error! Status: ${response.status}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Fetched menu sales data:', data);

                            if (data.error) {
                                throw new Error(data.error);
                            }

                            // Access the items array from the response
                            const items = data.items || [];
                            const summary = data.summary || {};

                            let tableHeaders = `
                                <th>Item ID</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Type</th>
                                <th>Size/Service</th>
                                <th>Unit Price</th>
                                <th>Quantity Sold</th>
                                <th>Total Sales</th>
                            `;

                            let tableBody = items.length === 0 ?
                                "<tr><td colspan='8'>No menu product sales found.</td></tr>" :
                                items.map(item => `
                                    <tr>
                                        <td>${item.item_id || 'N/A'}</td>
                                        <td>${item.item_name || 'N/A'}</td>
                                        <td>${item.item_category || 'N/A'}</td>
                                        <td>${item.item_type || 'N/A'}</td>
                                        <td>${item.display_size || 'N/A'}</td>
                                        <td>Rs. ${parseFloat(item.actual_price).toFixed(2) || '0.00'}</td>
                                        <td>${item.total_quantity || '0'}</td>
                                        <td>Rs. ${parseFloat(item.total_sales).toFixed(2) || '0.00'}</td>
                                    </tr>
                                `).join('');

                            // Add summary row if items exist
                            if (items.length > 0) {
                                tableBody += `
                                    <tr class="table-active font-weight-bold">
                                        <td colspan="6" style="text-align: right;"><strong>Grand Totals:</strong></td>
                                        <td>${summary.totalQuantity || 0}</td>
                                        <td>Rs. ${parseFloat(summary.totalSales).toFixed(2) || '0.00'}</td>
                                    </tr>
                                `;
                            }

                            $("#reportTableHead").html(tableHeaders);
                            $("#reportTableBody").html(tableBody);
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            $("#reportTableBody").html(`<tr><td colspan='8'>Error: ${error.message}</td></tr>`);
                        });
                }

                function printMenuProductSalesReport() {
                    const search = document.getElementById("Menu-product-search").value;
                    const startDate = document.getElementById("Menu-sales-start-date").value;
                    const endDate = document.getElementById("Menu-sales-end-date").value;

                    const url = `../report/print_menu_sales_report.php?search=${encodeURIComponent(search)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

                    const printWindow = window.open(url, '_blank');
                    printWindow?.addEventListener('load', () => printWindow.print());
                }
            </script>
</body>

</html>