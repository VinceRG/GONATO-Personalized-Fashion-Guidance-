
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" />
  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="public/css/admin.css" />
</head>

<body>
  <div class="admin-container">
    <div class="sidebar">
      <div>
        <div class="profile">
          <div class="profile-pic"><i class="bi bi-shield-check"></i></div>
          <div class="profile-info">
            <h4>Admin Panel</h4>
            <p><i>@administrator</i></p>
          </div>
        </div>

        <nav class="nav-links">
  <a href="#products" class="nav-btn active"><i class="bi bi-tags"></i> Products</a>
  <a href="#inventory" class="nav-btn"><i class="bi bi-box-seam"></i> Inventory</a>
  <a href="#users" class="nav-btn"><i class="bi bi-people"></i> Users</a>
  <a href="#orders" class="nav-btn"><i class="bi bi-cart-check"></i> Orders</a>
</nav>
      </div>

      <div class="sidebar-footer">
        <div class="logo-section">
          <div class="logo-img">
            <img src="public/image/amarelle.png">
          </div>
          <div class="logo-text-content">
            <div class="logo-text">Amarelle</div>
            <div class="logo-tagline">Admin Portal</div>
          </div>
        </div>
        <button class="logout-btn" onclick="logout()" title="Logout">
          <i class="bi bi-box-arrow-right"></i>
        </button>
      </div>
    </div>

    <div class="main-content">
      <!-- PRODUCTS SECTION -->
<section id="products" class="content-section active">
  <div class="section-header">
    <div>
      <div class="section-title"><i>Product Management</i></div>
      <div class="section-subtitle">Manage base product details</div>
    </div>
    <button class="btn" onclick="openProductModal()">
      <i class="bi bi-plus-circle"></i> Add Product
    </button>
  </div>

  <div class="data-table">
    <table>
      <thead>
        <tr>
          <th>Image</th>
          <th>Product Name</th>
          <th>Description</th>
          <th>Body Shape</th>
          <th>Price</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="productListBody">
        <!-- Populated by JS -->
      </tbody>
    </table>

  </div>
      <div id="productPagination" class="pagination-controls"></div>

</section>
<!-- INVENTORY SECTION -->
<section id="inventory" class="content-section">
  <div class="section-header">
    <div>
      <div class="section-title" id="inventoryProductTitle">Inventory Management</div>
      <div class="section-subtitle" id="inventorySubtitle">Select a product to view its stock variants</div>
    </div>
    <button class="btn" onclick="openInventoryModal()">
      <i class="bi bi-plus-circle"></i> Add Variant
    </button>
  </div>

  <div class="data-table">
    <table>
      <thead>
        <tr>
          <th>Size</th>
          <th>Color</th>
          <th>Quantity</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="inventoryTableBody">
        <!-- Populated by JS -->
      </tbody>
    </table>

  </div>
      <div id="inventoryPagination" class="pagination-controls"></div>

</section>

      <!-- USERS SECTION -->
<section id="users" class="content-section">
  <div class="section-header">
    <div>
      <div class="section-title"><i>User Management</i></div>
      <div class="section-subtitle">View and manage user accounts</div>
    </div>
  </div>

  <div class="filter-bar">
    <input type="text" id="userSearch" placeholder="Search users..." onkeyup="filterUsers()">
    <select id="statusFilter" onchange="filterUsers()">
      <option value="">All Status</option>
      <option value="active">Active</option>
      <option value="locked">Locked</option>
    </select>
  </div>

  <div class="data-table">
    <table>
      <thead>
        <tr>
          <th>Username</th>
          <th>Email</th>
          <th>Joined Date</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="userTableBody">
        <!-- Populated by JS -->
      </tbody>
    </table>
  </div>
</section>

<!-- <script>
let allUsers = [];

// Fetch users from the admin API
async function loadUsers() {
  try {
    const res = await fetch('index.php?api=admin&action=users'); // ensure your API route
    if (!res.ok) throw new Error('Failed to fetch users');
    allUsers = await res.json();
    renderUsers(allUsers);
  } catch (err) {
    console.error(err);
    alert('Error loading users. Make sure you are logged in as admin.');
  }
}

// Render users into table
function renderUsers(users) {
  const tbody = document.getElementById('userTableBody');
  tbody.innerHTML = '';

  users.forEach(user => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${user.USERNAME}</td>
      <td>${user.EMAIL}</td>
      <td>${new Date(user.CREATED_AT).toLocaleDateString()}</td>
      <td>${user.IS_LOCKED ? 'Locked' : 'Active'}</td>
      <td>
        <button class="btn btn-sm" onclick="toggleUserStatus(${user.USER_ID}, ${user.IS_LOCKED})">
          ${user.IS_LOCKED ? 'Unlock' : 'Lock'}
        </button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

// Filter users by search & status
function filterUsers() {
  const searchTerm = document.getElementById('userSearch').value.toLowerCase();
  const statusFilter = document.getElementById('statusFilter').value;

  const filtered = allUsers.filter(user => {
    const matchesSearch = user.USERNAME.toLowerCase().includes(searchTerm) ||
                          user.EMAIL.toLowerCase().includes(searchTerm);
    const matchesStatus = !statusFilter || 
                          (statusFilter === 'active' && !user.IS_LOCKED) ||
                          (statusFilter === 'locked' && user.IS_LOCKED);
    return matchesSearch && matchesStatus;
  });

  renderUsers(filtered);
}

// Toggle user lock status
async function toggleUserStatus(userId, isLocked) {
  try {
    const res = await fetch(`index.php?api=admin&action=toggleUserLock&userId=${userId}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ isLocked: !isLocked })
    });

    const result = await res.json();
    if (result.success) {
      await loadUsers();
    } else {
      alert('Failed to update user status');
    }
  } catch (err) {
    console.error(err);
    alert('Error updating user status');
  }
}

// Load users on page load
window.addEventListener('DOMContentLoaded', loadUsers);
</script> -->


      <!-- ORDERS SECTION -->
      <section id="orders" class="content-section">
        <div class="section-header">
          <div>
            <div class="section-title"><i>Order Management</i></div>
            <div class="section-subtitle">Track and manage customer orders</div>
          </div>
        </div>

        <div class="filter-bar">
          <input type="text" id="orderSearch" placeholder="Search orders..." onkeyup="filterOrders()">
          <select id="orderStatusFilter" onchange="filterOrders()">
            <option value="">All Status</option>
            <option value="confirmed">Confirmed</option>
            <option value="cancelled">Cancelled</option>
            <option value="pending">Pending</option>
            <option value="shipped">Shipped</option>
            <option value="delivered">Delivered</option>
          </select>
        </div>

        <div class="data-table">
          <table>
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
                <th>Shipping Required</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="orderTableBody">
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </div>
<div id="productModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2 class="modal-title" id="productModalTitle">Add Product</h2>
      <button class="close-modal" onclick="closeProductModal()">&times;</button>
    </div>

    <form 
      id="productForm" 
      method="POST"
      enctype="multipart/form-data"
    >
      <div class="form-group">
        <label>Product Name *</label>
        <input type="text" id="productName" name="productName" required>
      </div>
      
      <div class="form-group">
        <label>Description</label>
        <textarea id="productDescription" name="productDescription" rows="3"></textarea>
      </div>
      
      <div class="form-group">
        <label>Body Shape *</label>
        <select id="bodyShapeSelect" name="bodyShapeSelect" required>
          <option value="">Select Body Shape</option>
          <!-- filled by JS -->
        </select>
      </div>

      <!-- ❌ Season removed from product form -->

      <div class="form-group">
        <label>Price ($) *</label>
        <input type="text" id="productPrice" name="productPrice" step="0.01" min="0" required>
      </div>

      <div class="form-group" id="currentImageContainer" style="display:none;">
        <label>Current Image</label>
        <img id="currentProductImage" src="" alt="Current Product Image"
             style="max-width: 150px; height: auto; margin-bottom: 10px;">
      </div>

      <div class="form-group">
        <label>Product Image *</label>
        <!-- IMPORTANT: name must be product_image -->
        <input type="file" id="productImage" name="product_image" accept="image/*" required>
        <small class="form-text text-muted">
          Max file size: 2MB. Accepted formats: JPG, PNG, GIF.
        </small>
      </div>

      <div class="modal-actions">
        <button type="button" class="btn btn-secondary" onclick="closeProductModal()">Cancel</button>
        <button type="submit" class="btn">Save Product</button>
      </div>
    </form>
  </div>
</div>


<!-- INVENTORY MODAL (Replace your existing one) -->
<div id="inventoryModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2 class="modal-title" id="inventoryModalTitle">Add Variant</h2>
      <button class="close-modal" onclick="closeInventoryModal()">&times;</button>
    </div>

    <form id="inventoryForm">
      <div class="form-group">
        <label>Size</label>
        <select id="inventorySize" name="inventorySize" required>
          <option value="">Select Size</option>
          <option value="S">Small (S)</option>
          <option value="M">Medium (M)</option>
          <option value="L">Large (L)</option>
        </select>
      </div>


      <div class="form-group">
        <label>Season</label>
        <select id="inventorySeasonFilter">
          <option value="">Select Season</option>
        </select>
      </div>

      <div class="form-group">
        <label>Color</label>
        <div style="display:flex; gap:8px;">
          <select id="inventoryColor" name="inventoryColor" required>
            <!-- populated by JS -->
          </select>
          <button type="button" class="btn btn-secondary" onclick="openAddColorModalFromInventory()">
            Add Color
          </button>
        </div>
      </div>

      <div class="form-group">
        <label>Quantity</label>
        <input type="number" id="inventoryQuantity" name="inventoryQuantity" min="0" required>
      </div>

      <div class="modal-actions">
        <button type="button" class="btn btn-secondary" onclick="closeInventoryModal()">Cancel</button>
        <button type="submit" class="btn">Save Variant</button>
      </div>
    </form>
  </div>
</div>


  <!-- ORDER MODAL -->
  <div id="orderModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Order Details</h2>
        <button class="close-modal" onclick="closeOrderModal()">&times;</button>
      </div>
      <div id="orderDetails"></div>
      <div class="modal-actions">
        <button type="button" class="btn btn-secondary" onclick="closeOrderModal()">Close</button>
      </div>
    </div>
  </div>

  <!-- DELETE PRODUCT MODAL -->
<div id="deleteProductModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2 class="modal-title">Confirm Delete</h2>
      <button class="close-modal" onclick="closeDeleteModal()">&times;</button>
    </div>
    <div class="modal-body">
      <p id="deleteProductMessage">Are you sure you want to delete this product?</p>
    </div>
    <div class="modal-actions">
      <button class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
      <button class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
    </div>
  </div>
</div>

<div id="addColorModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2 class="modal-title">Add New Color</h2>
      <button class="close-modal" onclick="closeAddColorModal()">&times;</button>
    </div>
    <form id="addColorForm">
      <div class="form-group">
        <label>Color Name</label>
        <input type="text" id="newColorName" placeholder="e.g., Red, Navy Blue, Forest Green" required>
      </div>

      <div class="form-group">
        <label>Season *</label>
        <select id="newColorSeason" required>
          <option value="">Select Season</option>
          <!-- filled by JS from seasons[] -->
        </select>
      </div>

      <div class="modal-actions">
        <button type="button" class="btn btn-secondary" onclick="closeAddColorModal()">Cancel</button>
        <button type="submit" class="btn">Save Color</button>
      </div>
    </form>
  </div>
</div>

  <script src="public/js/admin.js"></script>
  <script>
  window.openInventoryManager = async function(productId, productName) {
    selectedProductId = productId;
    selectedProductName = productName;
document.getElementById('inventoryProductTitle').textContent = `Inventory - ${decodeURIComponent(productName)}`;
  document.getElementById('inventorySubtitle').textContent = `Managing stock variants for ${decodeURIComponent(productName)}`;

    switchSection('inventory');
    await loadInventory(productId);
  };

  window.editProduct = editProduct;
  window.deleteProduct = deleteProduct;
  window.toggleUserStatus = toggleUserStatus;
  window.viewOrder = viewOrder;
  window.updateOrderStatus = updateOrderStatus;
  window.openProductModal = openProductModal;
  window.closeProductModal = closeProductModal;
  window.openAddColorModal = openAddColorModal;
  window.closeAddColorModal = closeAddColorModal;
</script>
</body>

</html>