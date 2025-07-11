<?php
session_start(); // Ensure session is started
require_once '../posBackend/checkIfLoggedIn.php';
?>
<?php include '../inc/dashHeader.php' ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Type Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            background-color: #f0f2f5;
            padding: 25px;
            color: #344054;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #1e293b;
            font-weight: 600;
            font-size: 28px;
        }

        h2 {
            color: #1e293b;
            font-weight: 500;
            font-size: 20px;
            margin-bottom: 15px;
        }

        .form-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 25px;
            border-bottom: 1px solid #e2e8f0;
        }

        .add-form, .search-form {
            width: 48%;
            padding: 20px;
            background-color: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #4b5563;
            font-size: 15px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-group input:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
        }

        button {
            padding: 12px 20px;
            font-size: 15px;
            font-weight: 500;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        button[type="submit"] {
            background-color: #0f4c81;
            color: white;
        }

        button[type="submit"]:hover {
            background-color: #0d3e69;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        button[type="button"] {
            background-color: #cbd5e1;
            color: #1e293b;
        }

        button[type="button"]:hover {
            background-color: #94a3b8;
        }

        .hotel-types {
            margin-top: 25px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        th, td {
            padding: 15px 20px;
            text-align: left;
        }

        th {
            background-color: #0f4c81;
            color: white;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        
        tr:nth-child(odd) {
            background-color: white;
        }

        tr:hover {
            background-color: #e6f2ff;
        }

        td {
            border-bottom: 1px solid #e2e8f0;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .edit-btn {
            background-color: #3b82f6;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 13px;
            min-width: 36px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
        }

        .edit-btn:hover {
            background-color: #2563eb;
        }

        .delete-btn {
            background-color: #ef4444;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 13px;
            min-width: 36px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
        }

        .delete-btn:hover {
            background-color: #dc2626;
        }

        .no-data {
            text-align: center;
            padding: 25px;
            color: #64748b;
            font-style: italic;
            background-color: #f8fafc;
        }

        .success-message, .error-message {
            padding: 16px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: 500;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            animation: fadeIn 0.3s ease-in-out;
        }

        .success-message {
            background-color: #ecfdf5;
            color: #047857;
            border-left: 4px solid #10b981;
        }

        .error-message {
            background-color: #fef2f2;
            color: #b91c1c;
            border-left: 4px solid #ef4444;
        }

        .hidden {
            display: none;
        }

        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(-10px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .form-container {
                flex-direction: column;
            }
            
            .add-form, .search-form {
                width: 100%;
                margin-bottom: 20px;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    
    <div class="container">
        <h1 style="margin-top:10px"><i class="fas fa-hotel"></i> Hotel Type Management</h1>
        
        <div id="message" class="hidden"></div>
        
        <div class="form-container">
            <div class="add-form">
                <h2><i class="fas fa-plus-circle"></i> Add New Hotel Type</h2>
                <form id="addHotelTypeForm">
                    <div class="form-group">
                        <label for="hotelTypeName">Hotel Type Name</label>
                        <input type="text" id="hotelTypeName" name="hotelTypeName" placeholder="Enter hotel type name" required>
                    </div>
                    <button type="submit"><i class="fas fa-save"></i> Add Hotel Type</button>
                </form>
            </div>
            
            <div class="search-form">
                <h2><i class="fas fa-search"></i> Search Hotel Types</h2>
                <form id="searchForm">
                    <div class="form-group">
                        <label for="searchTerm">Search Term</label>
                        <input type="text" id="searchTerm" name="searchTerm" placeholder="Search by name...">
                    </div>
                    <button type="submit"><i class="fas fa-search"></i> Search</button>
                </form>
            </div>
        </div>
        
        <div class="hotel-types">
            <h2><i class="fas fa-list"></i> Hotel Types List</h2>
            <table id="hotelTypesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="hotelTypesBody">
                    <!-- Hotel types will be loaded here -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); z-index: 1000; backdrop-filter: blur(3px);">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: white; padding: 25px; border-radius: 8px; width: 450px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
            <h2 style="margin-bottom: 20px; color: #1e293b; display: flex; align-items: center;">
                <i class="fas fa-edit" style="margin-right: 10px; color: #3b82f6;"></i> Edit Hotel Type
            </h2>
            <form id="editHotelTypeForm">
                <input type="hidden" id="editHotelTypeId">
                <div class="form-group">
                    <label for="editHotelTypeName">Hotel Type Name</label>
                    <input type="text" id="editHotelTypeName" name="editHotelTypeName" required>
                </div>
                <div style="display: flex; justify-content: flex-end; margin-top: 20px; gap: 10px;">
                    <button type="button" onclick="document.getElementById('editModal').style.display='none'" style="background-color: #cbd5e1;">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" style="background-color: #3b82f6;">
                        <i class="fas fa-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load all hotel types on page load
            loadHotelTypes();
            
            // Add hotel type form submission
            document.getElementById('addHotelTypeForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const hotelTypeName = document.getElementById('hotelTypeName').value.trim();
                
                if (hotelTypeName === '') {
                    showMessage('Please enter a hotel type name', 'error');
                    return;
                }
                
                // Send AJAX request to add hotel type
                const formData = new FormData();
                formData.append('action', 'add');
                formData.append('name', hotelTypeName);
                
                fetch('hotel_type_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('hotelTypeName').value = '';
                        showMessage(data.message, 'success');
                        loadHotelTypes(); // Reload the list
                    } else {
                        showMessage(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('An error occurred while adding the hotel type', 'error');
                });
            });
            
            // Search form submission
            document.getElementById('searchForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const searchTerm = document.getElementById('searchTerm').value.trim();
                loadHotelTypes(searchTerm);
            });
            
            // Edit hotel type form submission
            document.getElementById('editHotelTypeForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const hotelTypeId = document.getElementById('editHotelTypeId').value;
                const hotelTypeName = document.getElementById('editHotelTypeName').value.trim();
                
                if (hotelTypeName === '') {
                    showMessage('Please enter a hotel type name', 'error');
                    return;
                }
                
                // Send AJAX request to update hotel type
                const formData = new FormData();
                formData.append('action', 'update');
                formData.append('id', hotelTypeId);
                formData.append('name', hotelTypeName);
                
                fetch('hotel_type_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('editModal').style.display = 'none';
                        showMessage(data.message, 'success');
                        loadHotelTypes(); // Reload the list
                    } else {
                        showMessage(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('An error occurred while updating the hotel type', 'error');
                });
            });
        });
        
        // Function to load hotel types
        function loadHotelTypes(searchTerm = '') {
            const formData = new FormData();
            formData.append('action', 'get');
            
            if (searchTerm) {
                formData.append('search', searchTerm);
            }
            
            fetch('hotel_type_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const hotelTypesBody = document.getElementById('hotelTypesBody');
                hotelTypesBody.innerHTML = '';
                
                if (data.success && data.hotel_types.length > 0) {
                    // Define protected hotel types that cannot be edited or deleted
                    const protectedTypes = ['Dine station', 'Uber', 'Pick Me', 'Takeaway', 'Delivery Service'];
                    
                    data.hotel_types.forEach(hotelType => {
                        const row = document.createElement('tr');
                        const isProtected = protectedTypes.includes(hotelType.name);
                        
                        // Only show actions column for non-protected types
                        if (isProtected) {
                            row.innerHTML = `
                                <td>${hotelType.id}</td>
                                <td>${hotelType.name}</td>
                                <td></td>
                            `;
                        } else {
                            row.innerHTML = `
                                <td>${hotelType.id}</td>
                                <td>${hotelType.name}</td>
                                <td class="actions">
                                    <button class="edit-btn" onclick="openEditModal(${hotelType.id}, '${hotelType.name.replace(/'/g, "\\'")}')" title="Edit"><i class="fas fa-pencil-alt"></i></button>
                                    <button class="delete-btn" onclick="deleteHotelType(${hotelType.id})" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                </td>
                            `;
                        }
                        
                        hotelTypesBody.appendChild(row);
                    });
                } else {
                    hotelTypesBody.innerHTML = `<tr><td colspan="3" class="no-data">No hotel types found</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred while loading hotel types', 'error');
            });
        }
        
        // Function to open edit modal
        function openEditModal(id, name) {
            // Define protected hotel types
            const protectedTypes = ['Dine station', 'Uber', 'Pick Me', 'Takeaway', 'Delivery Service'];
            
            // Check if this hotel type is protected
            if (protectedTypes.includes(name)) {
                showMessage(`Cannot edit '${name}'. This is a protected hotel type.`, 'error');
                return;
            }
            
            // If not protected, open the edit modal
            document.getElementById('editHotelTypeId').value = id;
            document.getElementById('editHotelTypeName').value = name;
            document.getElementById('editModal').style.display = 'block';
        }
        
        // Function to delete hotel type
        function deleteHotelType(id) {
            if (confirm('Are you sure you want to delete this hotel type?')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);
                
                fetch('hotel_type_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(data.message, 'success');
                        loadHotelTypes(); // Reload the list
                    } else {
                        showMessage(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('An error occurred while deleting the hotel type', 'error');
                });
            }
        }
        
        // Function to show messages
        function showMessage(message, type) {
            const messageElement = document.getElementById('message');
            messageElement.textContent = message;
            messageElement.className = type === 'success' ? 'success-message' : 'error-message';
            
            // Auto-hide after 3 seconds
            setTimeout(() => {
                messageElement.className = 'hidden';
            }, 3000);
        }
    </script>
</body>
</html>