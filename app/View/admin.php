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
          <a href="#inventory" class="nav-btn active"><i class="bi bi-box-seam"></i> Inventory</a>
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
      <!-- INVENTORY SECTION -->
      <section id="inventory" class="content-section active">
        <div class="section-header">
          <div>
            <div class="section-title"><i>Inventory Management</i></div>
            <div class="section-subtitle">Manage your product catalog</div>
          </div>
          <button class="btn" onclick="openProductModal()">
            <i class="bi bi-plus-circle"></i> Add Product
          </button>
        </div>

        <div class="filter-bar">
          <input type="text" id="productSearch" placeholder="Search products..." onkeyup="filterProducts()">
          <select id="categoryFilter" onchange="filterProducts()">
            <option value="">All Categories</option>
            <option value="Tops">Tops</option>
            <option value="Dresses">Dresses</option>
            <option value="Bottoms">Bottoms</option>
            <option value="Outerwear">Outerwear</option>
          </select>

          <select id="stockFilter" onchange="filterProducts()">
            <option value="">All Stock Levels</option>
            <option value="instock">In Stock</option>
            <option value="low">Low Stock</option>
            <option value="out">Out of Stock</option>
          </select>
        </div>

        <div class="data-table">
          <table>
            <thead>
              <tr>
                <th>Image</th>
                <th>Product Name</th>
                <th>Size</th>
                <th>Color</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="productTableBody">
              <!-- Populated by JS -->
            </tbody>
          </table>
        </div>
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

  <!-- PRODUCT MODAL -->
  <div id="productModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="productModalTitle">Add Product</h2>
        <button class="close-modal" onclick="closeProductModal()">&times;</button>
      </div>
      <form id="productForm">
        <input type="hidden" id="productId">
        <div class="form-group">
          <label>Product Name</label>
          <input type="text" id="productName" required>
        </div>
        <div class="form-group">
          <label>Size</label>
          <select id="sizeCategory" required>
            <option value="">Select Size</option>
            <option value="Small">Small</option>
            <option value="Medium">Medium</option>
            <option value="Large">Large</option>
          </select>
        </div>
        <div class="form-group">
          <label>Color</label>
          <div style="display: flex; gap: 0.5rem; align-items: center;">
            <select id="colorCategory" required style="flex: 1;">
              <option value="">Select Color</option>
            </select>
            <button type="button" class="btn" onclick="openAddColorModal()" title="Add New Color"
              style="padding: 0.75rem 1rem; white-space: nowrap; flex-shrink: 0;">
              <i class="bi bi-plus-circle" style="margin-right: 0.25rem;"></i> Add Color
            </button>
          </div>
        </div>
        <div class="form-group">
          <label>Category</label>
          <select id="productCategory" required>
            <option value="">Select Category</option>
            <option value="Dresses">Dresses</option>
            <option value="Tops">Tops</option>
            <option value="Bottoms">Bottoms</option>
            <option value="Outerwear">Outerwear</option>
          </select>
        </div>

        <div class="form-group">
          <label>Price ($)</label>
          <input type="number" id="productPrice" step="0.01" required>
        </div>
        <div class="form-group">
          <label>Stock Quantity</label>
          <input type="number" id="productStock" required>
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea id="productDescription"></textarea>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn btn-secondary" onclick="closeProductModal()">Cancel</button>
          <button type="submit" class="btn">Save Product</button>
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

  <!-- ADD COLOR MODAL -->
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
        <div class="modal-actions">
          <button type="button" class="btn btn-secondary" onclick="closeAddColorModal()">Cancel</button>
          <button type="submit" class="btn">Save Color</button>
        </div>
      </form>
    </div>
  </div>
</body>

</html>